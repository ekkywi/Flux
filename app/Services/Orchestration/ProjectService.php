<?php

namespace App\Services\Orchestration;

use App\Models\Project;
use App\Models\User;
use App\Models\ProjectEnvironment;
use App\Services\Infrastructure\RemoteTaskService;
use App\Services\Integrations\GiteaIntegrationService;
use App\Services\Stacks\StackFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Exception;

class ProjectService
{
    public function __construct(
        protected GiteaIntegrationService $gitea
    ) {}

    public function onboardProject(array $data, User $creator): Project
    {
        $stack = StackFactory::make($data['stack_type'] ?? 'laravel');

        if (!$stack->validateRepository($data['repository_url'])) {
            throw new Exception("Repository is invalid or not a project " . ucfirst($data['stack_type']));
        }

        return DB::transaction(function () use ($data, $creator, $stack) {
            $project = Project::create([
                'name'              => $data['name'],
                'slug'              => Str::slug($data['name']),
                'repository_url'    => $data['repository_url'],
                'stack_type'        => $data['stack_type'] ?? 'laravel',
                'stack_options'     => $data['stack_options'] ?? [],
                'description'       => $data['description'] ?? null,
            ]);

            $this->initializeEnvironment($project);

            try {
                $this->gitea->registerWebhook($project->repository_url);
            } catch (\Exception $e) {
                Log::error("Webhook Registeration Failure: " . $e->getMessage());
            }

            $devEnv = $project->environments()->where('name', 'development')->first();
            if ($devEnv && $devEnv->server_app_id) {
                $this->setupInitialInfrastructure($devEnv);
            }

            $analysis = $stack->analyzeRisk($project->repository_url, $project->stack_options);

            $project->update([
                'onboarding_status' => $analysis['has_docker'] ? 'ready' : 'needs_injection'
            ]);

            $project->members()->create([
                'user_id'   => $creator->id,
                'is_owner'  => true,
            ]);

            return $project;
        });
    }

    protected function initializeEnvironment(Project $project): void
    {
        $envs = ['development', 'staging', 'production'];

        foreach ($envs as $index => $env) {
            $lastPort = ProjectEnvironment::max('assigned_port') ?? 8999;
            $newPort = $lastPort + 1;

            $project->environments()->create([
                'name'          => $env,
                'assigned_port' => $newPort,
                'env_vars'      => [
                    'APP_NAME'          => $project->name,
                    'APP_ENV'           => $env,
                    'APP_KEY'           => 'base64:' . base64_encode(random_bytes(32)),
                    'CONTAINER_NAME'    => "flux_{$project->slug}_{$env}",
                ],
            ]);
        }
    }

    protected function setupInitialInfrastructure(ProjectEnvironment $environment): void
    {
        $project = $environment->project;
        $server = $environment->appServer;

        if (!$server) return;

        $taskRunner = app(RemoteTaskService::class);
        $deployPath = "/home/{$server->ssh_user}/apps/{$project->slug}";
        $commands = [
            "mkdir -p {$deployPath}",
            "cd {$deployPath}",
            "git clone {$project->repository_url} .",
            "docker network create flux_net_{$project->slug} || true"
        ];

        try {
            $taskRunner->run($server, $commands);
            Log::info("Initial infrastructure ready for {$project->name}");
        } catch (\Exception $e) {
            Log::error("Initial Setup Failed: " . $e->getMessage());
        }
    }

    public function assignServer(string $environmentId, string $serverId): ProjectEnvironment
    {
        return DB::transaction(function () use ($environmentId, $serverId) {
            $environment = ProjectEnvironment::findOrFail($environmentId);
            $environment->update([
                'server_app_id' => $serverId
            ]);

            $this->setupInitialInfrastructure($environment);

            return $environment->load('project', 'appServer');
        });
    }
}
