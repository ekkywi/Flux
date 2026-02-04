<?php

namespace App\Services\Orchestration;

use App\Models\Project;
use App\Models\ProjectEnvironment;
use App\Services\Infrastructure\RemoteTaskService;
use Illuminate\Support\Facades\Log;

class DeploymentService
{
    public function handlePushEvent(array $payload): void
    {
        $repoUrl = $payload['repository']['html_url'];
        $branch = str_replace('refs/heads/', '', $payload['ref']);

        $project = Project::where('repository_url', $repoUrl)->first();

        if (!$project) {
            Log::warning("Deployment skipped: No project found for repo {$repoUrl}");
            return;
        }

        $envName = $this->mapBranchToEnv($branch);
        $environment = $project->environment()->where('name', $envName)->first();

        if (!$environment || $environment->server_app_id) {
            Log::warning("Deployment skipped: Environment [{$envName}] not configured for project {$project->name}");
            return;
        }

        $this->executeDeployment($environment);
    }

    protected function mapBranchToEnv(string $branch): string
    {
        return match ($branch) {
            'main', 'master'    => 'production',
            'staging'           => 'staging',
            default             => 'development'
        };
    }

    protected function executeDeployment(ProjectEnvironment $environment): void
    {
        $project = $environment->project;
        $server = $environment->appServer;
        $taskRunner = app(RemoteTaskService::class);

        $deployPath = "/home/{$server->ssh_user}/apps/{$project->slug}";

        $commands = [
            "cd {$deployPath}",
            "git pull origin {$environment->name}",
            "echo 'PORT={$environment->assigned_port}' > .env.flux",
            "echo 'CONTAINER_NAME=flux_{$project->slug}_{$environment->name}' >> .env.flux",
            "docker compose --env-file .env.flux up -d --build"
        ];

        try {
            $output = $taskRunner->run($server, $commands);
            Log::info("Deployment Output: " . $output);

            $environment->update(['last_deployment_at' => now()]);
        } catch (\Exception $e) {
            Log::error("Deployment Failed: " . $e->getMessage());
            throw $e;
        }
    }
}
