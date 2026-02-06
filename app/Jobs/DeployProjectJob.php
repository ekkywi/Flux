<?php

namespace App\Jobs;

use App\Models\ProjectEnvironment;
use App\Models\DeploymentLog;
use App\Events\DeploymentLogEvent;
use App\Services\Infrastructure\RemoteTaskService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

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
        $env = $this->environment;
        $logKey = "deployment:logs:{$env->id}";

        Redis::del($logKey);

        $deploymentLog = DeploymentLog::create([
            'project_environment_id'    => $env->id,
            'status'                    => 'starting',
            'output'                    => 'Deployment initiated...',
        ]);

        try {
            $deployPath = "/home/{$env->appServer->ssh_user}/apps/{$env->project->slug}";
            $commands = [
                "cd {$deployPath}",
                "git pull origin {$env->name}",
                "echo 'PORT={$env->assigned_port}' > .env.flux",
                "docker compose --env-file .env.flux up -d --build --remove-orphans"
            ];

            $output = $taskRunner->run($env->appServer, $commands, function ($buffer) use ($env, $logKey) {
                $lines = explode("\n", $buffer);

                foreach ($lines as $line) {
                    if (empty(trim($line))) continue;

                    $data = [
                        'line' => trim($line),
                        'type' => 'info',
                        'time' => now()->format('H:i:s')
                    ];

                    Redis::rpush($logKey, json_encode($data));
                    Redis::expire($logKey, 3600);

                    broadcast(new DeploymentLogEvent($env->id, $data));
                }
            });

            $deploymentLog->update([
                'status'                => 'success',
                'output'                => $output,
                'duration_seconds'      => (int)(microtime(true) - $startTime),
            ]);

            $env->update(['last_deployed_at' => now()]);
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
