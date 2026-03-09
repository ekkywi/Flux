<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Project\StoreEnvironmentRequest;
use App\Models\Project;
use App\Models\Environment;
use App\Models\SystemSetting;
use App\Jobs\StopEnvironment;
use App\Jobs\StartEnvironment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use phpseclib3\Net\SSH2;
use phpseclib3\Crypt\RSA;

class EnvironmentController extends Controller
{
    public function store(StoreEnvironmentRequest $request, Project $project)
    {
        $data = $request->validated();

        $defaultScript = "php artisan migrate --force\nphp artisan optimize:clear\nphp artisan optimize";

        $data['deploy_script'] = $defaultScript;

        $project->environments()->create($data);

        return back()->with('success', 'New environment provisioned successfully.');
    }

    public function destroy(Project $project, Environment $environment)
    {
        $this->authorize('update', $project);

        if ($environment->project_id !== $project->id) {
            abort(404);
        }

        if ($project->environments()->count() <= 1) {
            return back()->with('error', 'Cannot delete the only environment available. Terminate the project instead.');
        }

        if ($environment->type === 'production') {
            $user = Auth::user();
            $isOwner = $project->members()
                ->where('user_id', $user->id)
                ->wherePivot('role', 'owner')
                ->exists();
            $isSysAdmin = $user->role === 'System Administrator';

            if (!$isOwner && !$isSysAdmin) {
                return back()->with('error', 'Access Denied: Only the Project Owner can delete the Production environment.');
            }
        }

        $environment->delete();

        return back()->with('success', 'Environment has been de-provisioned.');
    }

    public function start(Project $project, Environment $environment)
    {
        $this->authorize('deploy', [$project, $environment]);

        $environment->update(['status' => 'starting']);

        StartEnvironment::dispatch($environment);

        return back()->with('success', 'Environment is starting up...');
    }

    public function stop(Project $project, Environment $environment)
    {
        $environment->update(['status' => 'stopping']);

        StopEnvironment::dispatch($environment);

        return back()->with('success', 'Stop signal send to servers. Environment is shutting down.');
    }

    public function update(Request $request, Project $project, Environment $environment)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'deploy_script' => 'nullable|string',
        ]);

        $environment->update($validated);

        return back()->with('success', "Deployment script for {$environment->name} update successfully.");
    }

    public function runCommand(Request $request, Project $project, Environment $environment)
    {
        $this->authorize('update', $project);

        $request->validate([
            'command' => 'required|string|max:500',
        ]);

        if ($environment->status !== 'running') {
            return response()->json([
                'status' => 'error',
                'output' => 'Error: Container is no running. Please start the environment first.'
            ], 400);
        }

        try {
            $appServer = $environment->server;
            if (!$appServer) {
                throw new \Exception("Validation Error: No server associated with this environment");
            }
            if (empty($appServer->ip_address)) {
                throw new \Exception("Validation Error: Server IP Address is empty.");
            }
            if (empty($appServer->ssh_user)) {
                throw new \Exception("Validation Error: Server SSH Username is empty.");
            }

            $masterKey = SystemSetting::where('key_name', 'master_ssh_key')->first();
            if (!$masterKey || empty($masterKey->private_key)) {
                throw new \Exception("Validation Error: Master SSH Key not found in system settings or is empty.");
            }

            $privateKey = RSA::load($masterKey->private_key);
            $ssh = new SSH2($appServer->ip_address, $appServer->ssh_port, 10);

            if (!$ssh->login($appServer->ssh_user, $privateKey)) {
                throw new \Exception("SSH Login failed to server. Please check your server credentials");
            }

            $workspace = "~/flux-projects/{$project->id}/{$environment->name}";
            $userCommand = trim($request->command);
            $forbiddenPrefixes = ['nano ', 'vi ', 'vim ', 'top', 'htop'];

            foreach ($forbiddenPrefixes as $forbidden) {
                if (str_starts_with($userCommand, $forbidden)) {
                    return response()->json([
                        'status' => 'error',
                        'output' => "Command '{$userCommand}' is an interactive command and cannot be run from the web terminal."
                    ], 400);
                }
            }

            $fullCommand = "cd {$workspace} && docker compose exec -T app {$userCommand} 2>&1";
            $ssh->setTimeout(60);

            $output = $ssh->exec($fullCommand);

            $ssh->disconnect();

            return response()->json([
                'status' => 'success',
                'output' => trim($output) ?: 'Command executed successfully (no output).'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'output' => 'Critical Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
