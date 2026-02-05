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

class ProjectController extends Controller
{
    public function __construct(
        protected ProjectService $projectService
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

        return view('console.project.show', [
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
            $updatedEnv = $this->projectService->assignServer(
                $environment->id,
                $request->server_id
            );

            return response()->json([
                'status'    => 'success',
                'message'   => 'Server assigned and infrastructure initialized!',
                'data'      => $updatedEnv
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'    => 'error',
                'message'   => 'Failed to assign server: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deploy(ProjectEnvironment $environment)
    {
        if (!$environment->server_app_id) {
            return response()->json([
                'status'    => 'error',
                'message'   => 'Cannot deploy: No server assigned to this environment.'
            ], 422);
        }

        DeployProjectJob::dispatch($environment);

        return response()->json([
            'status'    => 'success',
            'message'   => 'Deployment dispatched! Flux is working in the background.',
            'log_url'   => "/api/environments/{$environment->id}/logs/latest"
        ]);
    }
}
