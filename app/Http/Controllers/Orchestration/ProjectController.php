<?php

namespace App\Http\Controllers\Orchestration;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Orchestration\StoreProjectRequest;
use App\Models\ProjectEnvironment;
use App\Services\Orchestration\ProjectService;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function __construct(
        protected ProjectService $projectService
    ) {}

    public function store(StoreProjectRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $data['stack_options'] = [
                'php_version'       => $request->php_version,
                'framework_version' => $request->framework_version,
            ];

            /** @var User $user */
            $user = Auth::user();

            $project = $this->projectService->onboardProject($data, $user);

            return response()->json([
                'status'    => 'success',
                'message'   => 'Project successfully onboarded!',
                'data'      => $project->load('environments')
            ], 201);
        } catch (\Exception $e) {
            Log::error("Onboarding Failed: " . $e->getMessage());

            return response()->json([
                'status'    => 'error',
                'message'   => 'Onboarding failed: ' . $e->getMessage(),
            ], 422);
        }
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
}
