<?php

namespace App\Http\Controllers\Console;

use App\Http\Controllers\Controller;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Actions\Project\CreateProjectAction;
use App\Http\Controllers\Admin\EnvironmentController;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Services\Infrastructure\VersionControl\GitService;
use Illuminate\Support\Facades\Auth;
use App\Models\SystemSetting;
use phpseclib3\Net\SSH2;
use phpseclib3\Crypt\RSA;

class ProjectController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'System Administrator') {
            $projects = Project::with(['environments', 'owner'])->latest()->get();
        } else {
            $projects = $user->projects()->with(['environments', 'owner'])->latest()->get();
        }

        return view('console.projects.index', compact('projects'));
    }

    public function create()
    {
        return view('console.projects.create');
    }

    public function fetchBranches(Request $request, GitService $gitService)
    {
        $request->validate(['repository_url' => 'required|url']);

        try {
            $branches = $gitService->getRemoteBranches($request->input('repository_url'));
            return response()->json(['branches' => $branches]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function store(StoreProjectRequest $request, CreateProjectAction $action)
    {
        $project = $action->execute(
            $request->validated(),
            $request->user()
        );

        return redirect()->route('console.projects.show', $project)
            ->with('success', "Project '{$project->name}' created successfully");
    }

    public function show(Project $project)
    {
        $this->authorize('view', $project);

        $project->load(['members', 'owner', 'environments']);

        return view('console.projects.show', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'repository_url'    => 'required|url',
            'branch'            => 'required|string|max:50',
            'status'            => 'required|in:active,maintenance,archived',
            'description'       => 'nullable|string|max:500',
            'stack'             => 'required|string|in:laravel,nodejs,html',
            'php_version'       => 'nullable|string|in:8.1,8.2,8.3,8.4,8.5',
            'database_type'     => 'required|string|in:sqlite,mysql,pgsql',
        ]);

        $buildOptions = $project->build_options ?? [];
        $stack = strtolower($validated['stack']);

        if ($stack === 'laravel' || $stack === 'php') {
            $buildOptions['php_version'] = $validated['php_version'] ?? '8.4';
        } else {
            unset($buildOptions['php_version']);
        }

        $buildOptions['database_type'] = $validated['database_type'];

        $project->update([
            'name'              => $validated['name'],
            'repository_url'    => $validated['repository_url'],
            'default_branch'    => $validated['branch'],
            'status'            => $validated['status'],
            'description'       => $validated['description'],
            'stack'             => $stack,
            'build_options'     => $buildOptions,

        ]);

        return back()->with('success', 'Project configuration updated');
    }

    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        $environments = $project->environments()->get();

        try {
            $masterKey = SystemSetting::where('key_name', 'master_ssh_key')->first();
            if ($masterKey && !empty($masterKey->private_key)) {
                $privateKey = RSA::load($masterKey->private_key);

                foreach ($environments as $environment) {

                    $appServer = $environment->server;
                    if ($appServer) {
                        $sshApp = new SSH2($appServer->ip_address, $appServer->ssh_port, 10);
                        if ($sshApp->login($appServer->ssh_user, $privateKey)) {
                            $workspace = "~/flux-projects/{$project->id}/{$environment->name}";

                            $sshApp->exec("cd {$workspace} && docker compose down -v 2>/dev/null || true");
                            $sshApp->exec("rm -rf {$workspace}");
                            $sshApp->exec("rmdir ~/flux-projects/{$project->id} 2>/dev/null || true");

                            $sshApp->disconnect();
                        }
                    }

                    $dbServer = $environment->dbServer;
                    $dbType = strtolower(trim($project->build_options['database_type'] ?? 'sqlite'));

                    if ($dbServer && in_array($dbType, ['mysql', 'pgsql', 'mariadb'])) {
                        $sshDb = new SSH2($dbServer->ip_address, $dbServer->ssh_port, 10);
                        if ($sshDb->login($dbServer->ssh_user, $privateKey)) {
                            $dbWorkspace = "~/flux-databases/{$project->id}/{$environment->name}";

                            $sshDb->exec("cd {$dbWorkspace} && docker compose down -v 2>/dev/null || true");
                            $sshDb->exec("rm -rf {$dbWorkspace}");
                            $sshDb->exec("rmdir ~/flux-databases/{$project->id} 2>/dev/null || true");

                            $sshDb->disconnect();
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            \Log::error("Teardown Failed for Project ID {$project->id}: " . $e->getMessage());
        }

        $project->delete();

        return redirect()->route('console.projects.index')
            ->with('success', 'Project and all associated infrastructure were successfully destroyed.');
    }
}
