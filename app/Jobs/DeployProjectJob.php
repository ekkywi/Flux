<?php

namespace App\Jobs;

use App\Models\ProjectEnvironment;
use App\Models\DeploymentLog;
use App\Services\Infrastructure\RemoteTaskService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DeployProjectJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $timeout = 600;

    public function __construct(
        protected ProjectEnvironment $environment
    ) {}

    public function handle(RemoteTaskService $taskRunner): void
    {
        $startTime = microtime(true);
        $project = $this->environment->project;
        $server = $this->environment->appServer;
        $deploymentLog = DeploymentLog::create([
            'project_environment_id'    => $this->environment->id,
            'status'                    => 'starting',
            'output'                    => 'Deployment initiated via Webhook.',
        ]);

        try {
            $deployPath = "/home/{$server->ssh_user}/apps/{$project->slug}";
            $commands = [
                "cd {$deployPath}",
                "git pull origin {$this->environment->name}",
                "echo 'PORT={$this->environment->assigned_port}' > .env.flux",
                "docker compose --env-file .env.flux up -d --build --remove-orphans"
            ];

            $output = $taskRunner->run($server, $commands);
            $deploymentLog->update([
                'status'                => 'success',
                'output'                => $output,
                'duration_seconds'      => (int)(microtime(true) - $startTime),
            ]);

            $this->environment->update([
                'last_deployed_at'  => now()
            ]);
        } catch (\Exception $e) {
            $deploymentLog->update([
                'status'                => 'failed',
                'output'                => "ERROR: " . $e->getMessage() . "\n\nPartial Output:\n" . ($output ?? ''),
                'duration_seconds'      => (int)(microtime(true) - $startTime),
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Final Deployment Failure for Environment {$this->environment->id}: " . $exception->getMessage());
    }
}
