<?php

namespace App\Services\Orchestration;

use App\Jobs\DeployProjectJob;
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
        DeployProjectJob::dispatch($environment);

        Log::inf("Deployment queued for environment: {$environment->name}");
    }
}
