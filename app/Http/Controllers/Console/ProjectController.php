<?php

namespace App\Http\Controllers\Console;

use App\Http\Controllers\Controller;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Actions\Project\CreateProjectAction;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Services\Infrastructure\VersionControl\GitService;
use Illuminate\Support\Facades\Auth;

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
            'php_version'       => 'nullable|string|in:8.1,8.2,8.3,8.4',
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

        $project->delete();

        return redirect()->route('console.projects.index')
            ->with('success', 'Project was successfully deleted.');
    }
}
