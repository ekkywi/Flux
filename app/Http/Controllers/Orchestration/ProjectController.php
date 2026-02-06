<?php

namespace App\Http\Controllers\Orchestration;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Orchestration\StoreProjectRequest;
use App\Models\User;
use App\Models\ProjectEnvironment;
use App\Models\Project;
use App\Models\Server;
use App\Services\Orchestration\ProjectService;
use App\Jobs\DeployProjectJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Services\Orchestration\DeploymentService;

class ProjectController extends Controller
{
    public function __construct(
        protected DeploymentService $deploymentService
    ) {}

    public function index()
    {
        $projects = Auth::user()->projects()
            ->with('environments')
            ->latest()
            ->get();

        return view('console.projects.index', [
            'page_title'    => 'Project Inventory',
            'projects'      => $projects
        ]);
    }

    public function store(StoreProjectRequest $request)
    {
        try {
            $data = $request->validated();

            /** @var User $user */
            $user = Auth::user();

            $project = $this->projectService->onboardProject($data, $user);

            return redirect()->route('console.projects.index')
                ->with('success', 'Project successfully onboarded!');
        } catch (\Exception $e) {
            Log::error("Onboarding Failed: " . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Onboarding failed: ' . $e->getMessage());
        }
    }

    public function show(Project $project)
    {
        $isMember = $project->members()->where('user_id', Auth::id())->exists();

        if (!$isMember && Auth::user()->role !== 'System Administrator') {
            return redirect()->route('console.projects.index')
                ->with('error', 'Unauthorized access to project core.');
        }

        $project->load(['environments.appServer', 'members.user']);

        $availableServers = Server::orderBy('name')->get();

        return view('console.projects.show', [
            'page_title'    => $project->name . ' // Control Panel',
            'project'       => $project,
            'servers'       => $availableServers,
        ]);
    }

    public function assignServer(Request $request, ProjectEnvironment $environment)
    {
        $request->validate([
            'server_id' => 'required|exists:servers,id',
        ]);

        try {
            $this->projectService->assignServer(
                $environment->id,
                $request->server_id
            );

            return redirect()->route('console.projects.show', $environment->project_id)
                ->with('success', 'Infrastructure Node successfully linked!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to link node: ' . $e->getMessage());
        }
    }

    public function deploy(Request $request, ProjectEnvironment $environment)
    {
        $request->validate(['branch' => 'required|string']);

        $this->deploymentService->executeDeployment(
            $environment,
            $request->input('branch')
        );

        return response()->json(['message' => 'Deployment queued']);
    }
}
