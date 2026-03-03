<?php

namespace App\Jobs;

use App\Models\Deployment;
use App\Models\SystemSetting;
use App\Services\Deployment\BlueprintFactory;
use App\Services\Deployment\Blueprints\DatabaseBlueprint;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use phpseclib3\Net\SSH2;
use phpseclib3\Crypt\RSA;
use Throwable;

class RunDeployment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 900;

    public function __construct(
        public Deployment $deployment
    ) {}

    private function log(string $message, string $type = 'info')
    {
        $this->deployment->logs()->create([
            'output' => $message,
            'type'   => $type
        ]);
    }

    public function handle(): void
    {
        $this->deployment->update(['status' => 'running']);

        try {
            $environment = $this->deployment->environment;
            $project = $environment->project;
            $appServer = $environment->server;

            $masterKey = SystemSetting::where('key_name', 'master_ssh_key')->first();
            if (!$masterKey) {
                throw new \Exception('Master SSH Key not found in system settings.');
            }
            $privateKey = RSA::load($masterKey->private_key);

            $dbType = $project->build_options['database_type'] ?? 'sqlite';
            $dbName = Str::slug($project->name, '_') . '_' . $environment->name;
            $dbUser = 'flux_user';
            $dbPass = substr(hash('sha256', $environment->id . $project->id . env('APP_KEY')), 0, 16);
            $dbHost = '127.0.0.1';
            $dbPort = '3306';

            if ($dbType === 'mysql' || $dbType === 'pgsql') {
                $dbServer = $environment->dbServer;

                if (!$dbServer) {
                    throw new \Exception("Database Server is required for {$dbType} but not assigned in Environment.");
                }

                $this->log("Initializing Database Deployment on [{$dbServer->ip_address}]...");

                $sshDb = new SSH2($dbServer->ip_address, $dbServer->ssh_port, 10);
                if (!$sshDb->login($dbServer->ssh_user, $privateKey)) {
                    throw new \Exception("SSH Login failed to DB Server: {$dbServer->ip_address}");
                }
                $sshDb->setTimeout(0);

                $dbWorkspace = "~/flux-databases/{$project->id}/{$environment->name}";
                $dbCompose = DatabaseBlueprint::getCompose($dbType, $dbName, $dbUser, $dbPass);
                $b64DbCompose = base64_encode(trim($dbCompose));

                $dbCommands = [
                    "mkdir -p {$dbWorkspace}",
                    "cd {$dbWorkspace}",
                    "echo '{$b64DbCompose}' | base64 -d > docker-compose.yml",
                    "if [ ! -s docker-compose.yml ]; then echo 'FATAL: Compose file is completely empty!'; exit 1; fi",
                    "echo 'Starting {$dbType} container...'",
                    "docker compose down 2>/dev/null || true",
                    "docker compose up -d 2>&1"
                ];

                $sshDb->exec(implode(' && ', $dbCommands), function ($out) {
                    $this->log("[DB Node] " . trim($out));
                });

                if ($sshDb->getExitStatus() !== 0) {
                    throw new \Exception("Failed to deploy Database container on DB Server.");
                }

                $dbHost = $dbServer->ip_address;
                $dbPort = ($dbType === 'pgsql') ? '5432' : '3306';

                $sshDb->disconnect();
                $this->log("Database provisioning completed.");
            }

            $this->log("Initializing Application Deployment on [{$appServer->ip_address}]...");

            $sshApp = new SSH2($appServer->ip_address, $appServer->ssh_port, 10);
            if (!$sshApp->login($appServer->ssh_user, $privateKey)) {
                throw new \Exception("SSH Login failed to App Server: {$appServer->ip_address}");
            }
            $sshApp->setTimeout(0);

            $workspace = "~/flux-projects/{$project->id}/{$environment->name}";
            $branch = escapeshellarg($environment->branch);

            if ($dbType === 'sqlite') {
                $envTemplate = "APP_NAME=\"{$project->name}\"\nAPP_ENV={$environment->type}\nAPP_KEY=\nAPP_DEBUG=true\nAPP_URL=http://{$appServer->ip_address}:8000\nDB_CONNECTION=sqlite\n";
            } else {
                $envTemplate = "APP_NAME=\"{$project->name}\"\nAPP_ENV={$environment->type}\nAPP_KEY=\nAPP_DEBUG=true\nAPP_URL=http://{$appServer->ip_address}:8000\nDB_CONNECTION={$dbType}\nDB_HOST={$dbHost}\nDB_PORT={$dbPort}\nDB_DATABASE={$dbName}\nDB_USERNAME={$dbUser}\nDB_PASSWORD={$dbPass}\n";
            }

            $b64Env = base64_encode(trim($envTemplate));

            $rawRepoUrl = $project->repository_url;
            $gitToken = env('GITEA_TOKEN');
            $repoUrl = escapeshellarg($gitToken && str_starts_with($rawRepoUrl, 'http')
                ? str_replace('://', "://{$gitToken}@", $rawRepoUrl)
                : $rawRepoUrl);

            $stack = strtolower($project->stack ?? 'laravel');
            $buildOptions = $project->build_options ?? [];
            $blueprint = BlueprintFactory::make($stack);

            $b64Dockerfile = base64_encode($blueprint->getDockerfile($buildOptions));
            $b64Compose = base64_encode($blueprint->getDockerCompose($buildOptions));

            $appCommands = [
                "mkdir -p {$workspace}",
                "cd {$workspace}",
                "if [ ! -d .git ]; then git clone {$repoUrl} . ; else git remote set-url origin {$repoUrl} && git fetch --all && git reset --hard origin/{$branch}; fi",
                "git checkout {$branch}",
                "git pull origin {$branch}",
                "OLD_KEY=''",
                "if [ -f .env ]; then OLD_KEY=$(grep '^APP_KEY=' .env | cut -d '=' -f2); fi",
                "echo 'Injecting secure .env configuration...'",
                "echo '{$b64Env}' | base64 -d > .env",
                "if [ ! -z \"\$OLD_KEY\" ]; then sed -i \"s|^APP_KEY=.*|APP_KEY=\$OLD_KEY|\" .env; fi",
                "echo '{$b64Dockerfile}' | base64 -d > Dockerfile",
                "echo '{$b64Compose}' | base64 -d > docker-compose.yml",
                "echo 'Starting Application Build & Up...'",
                "docker compose up -d --build 2>&1",
                "echo 'Running Laravel Production Setup...'",
                "sleep 3",
                "docker compose exec -T app bash -c \"grep -q '^APP_KEY=$' .env && php artisan key:generate --force || echo 'APP_KEY is secured.'\"",
                "echo 'Waiting for Database to be ready...'",
                "for i in {1..15}; do docker compose exec -T app php artisan migrate --force 2>&1 && break || { echo 'Database not ready, retrying in 3s...'; sleep 3; }; done",
                "docker compose exec -T app php artisan optimize 2>&1"
            ];

            $sshApp->exec(implode(' && ', $appCommands), function ($out) {
                $lines = explode("\n", trim($out));
                foreach ($lines as $line) {
                    if (trim($line) !== '') $this->log("[App Node] " . $line);
                }
            });

            $status = ($sshApp->getExitStatus() === 0) ? 'completed' : 'failed';
            $error = ($status === 'failed') ? "Exit code: " . $sshApp->getExitStatus() : null;

            $this->deployment->update([
                'status' => $status,
                'error_message' => $error
            ]);

            $sshApp->disconnect();
        } catch (Throwable $e) {
            $this->deployment->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage()
            ]);

            $this->log('CRITICAL ERROR: ' . $e->getMessage(), 'error');
        }
    }
}
