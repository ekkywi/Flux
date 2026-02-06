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

    /**
     * Constructor sekarang menerima branch spesifik.
     * Jika null, baru kita pakai logic default.
     */
    public function __construct(
        protected ProjectEnvironment $environment,
        protected ?string $branch = null // <--- TAMBAHAN: Parameter Branch
    ) {}

    public function handle(RemoteTaskService $taskRunner): void
    {
        $startTime = microtime(true);
        $env = $this->environment;
        $project = $env->project;

        // LOGIKA PRIORITAS BRANCH:
        // 1. Jika ada input dropdown ($this->branch), pakai itu.
        // 2. Jika tidak ada (null), baru pakai default mapping.
        $targetBranch = $this->branch ?? match ($env->name) {
            'production' => 'main',
            'staging'    => 'staging',
            default      => 'development', // Fallback default
        };

        $logKey = "deployment:logs:{$env->id}";
        Redis::del($logKey);

        $deploymentLog = DeploymentLog::create([
            'project_environment_id'    => $env->id,
            'status'                    => 'starting',
            'output'                    => "Deployment initiated for branch: {$targetBranch}...", // Info branch masuk log
        ]);

        try {
            // --- SETUP URL & TOKEN ---
            $token = config('services.gitea.token');
            $rawUrl = $project->repository_url;
            $authRepoUrl = str_replace(
                ['http://', 'https://'],
                ["http://oauth2:{$token}@", "https://oauth2:{$token}@"],
                $rawUrl
            );

            $deployPath = "/home/{$env->appServer->ssh_user}/apps/{$project->slug}";

            // --- SCRIPT DEPLOY (Updated variable $targetBranch) ---
            $script = <<<BASH
                set -e
                echo "🚀 Starting deployment for {$project->name} on branch [{$targetBranch}]..."
                
                mkdir -p {$deployPath}

                if [ -d "{$deployPath}/.git" ]; then
                    echo "📂 Repository detected. Updating..."
                    cd {$deployPath}
                    git remote set-url origin {$authRepoUrl}
                    git fetch origin
                    
                    # Checkout ke branch yang DIPILIH user (Dropdown)
                    echo "🔄 Checking out to {$targetBranch}..."
                    git checkout {$targetBranch} || git checkout -b {$targetBranch} origin/{$targetBranch}
                    
                    git reset --hard origin/{$targetBranch}
                else
                    echo "✨ Fresh cloning branch [{$targetBranch}]..."
                    rm -rf {$deployPath}
                    mkdir -p {$deployPath}
                    
                    # Clone langsung ke branch pilihan user
                    git clone -b {$targetBranch} {$authRepoUrl} {$deployPath}
                    cd {$deployPath}
                fi

                # ... (Sisa script sama: .env, docker compose, dll) ...
                echo "PORT={$env->assigned_port}" > .env.flux
                echo "APP_ENV={$env->name}" >> .env.flux

                echo "🐳 Rebuilding containers..."
                docker compose --env-file .env.flux up -d --build --remove-orphans
            BASH;

            // ... (Bagian eksekusi TaskRunner sama seperti sebelumnya) ...
            $output = $taskRunner->run($env->appServer, [$script], function ($buffer) use ($env, $logKey) {
                // ... (Logic logging Redis sama) ...
                $lines = explode("\n", $buffer);
                foreach ($lines as $line) {
                    if (empty(trim($line))) continue;
                    $data = ['line' => trim($line), 'type' => 'info', 'time' => now()->format('H:i:s')];
                    Redis::rpush($logKey, json_encode($data));
                    broadcast(new DeploymentLogEvent($env->id, $data));
                }
            });

            $deploymentLog->update([
                'status'           => 'success',
                'output'           => $output,
                'duration_seconds' => (int)(microtime(true) - $startTime),
            ]);
            $env->update(['last_deployed_at' => now()]);
        } catch (\Exception $e) {
            // ... (Error handling sama) ...
            $deploymentLog->update([
                'status' => 'failed',
                'output' => "ERROR: " . $e->getMessage(),
                'duration_seconds' => (int)(microtime(true) - $startTime)
            ]);
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Final Deployment Failure for Environment {$this->environment->id}: " . $exception->getMessage());
    }
}
