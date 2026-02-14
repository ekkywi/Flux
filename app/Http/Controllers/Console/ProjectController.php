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

        if ($user->role === 'system_admin') {
            $projects = Project::with('owner')->latest()->get();
        } else {
            $projects = $user->projects()->with('owner')->latest()->get();
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

    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        $project->delete();

        return redirect()->route('console.projects.index')
            ->with('success', 'Project was successfully deleted.');
    }
}
