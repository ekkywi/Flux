<?php

namespace App\Jobs;

use App\Models\Deployment;
use App\Models\SystemSetting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use phpseclib3\Net\SSH2;
use phpseclib3\Crypt\RSA;
use Throwable;

class RunDeployment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600;

    public function __construct(
        public Deployment $deployment
    ) {}

    public function handle(): void
    {
        $this->deployment->update(['status' => 'running']);

        try {
            $environment = $this->deployment->environment;
            $project = $environment->project;
            $server = $environment->server;

            $masterKey = SystemSetting::where('key_name', 'master_ssh_key')->first();
            if (!$masterKey) {
                throw new \Exception('Master SSH Key not found in system settings.');
            }

            $privateKey = RSA::load($masterKey->private_key);
            $ssh = new SSH2($server->ip_address, $server->ssh_port, 10);

            if (!$ssh->login($server->ssh_user, $privateKey)) {
                throw new \Exception('SSH Handshake/Login failed during deployment.');
            }

            $ssh->setTimeout(0);

            $workspace = "/opt/flux/projects/{$project->id}/{$environment->name}";
            $repoUrl = escapeshellarg($project->repository_url);
            $branch = escapeshellarg($environment->branch);

            $commands = [
                "mkdir -p {$workspace}",
                "cd {$workspace}",
                "if [ ! -d .git ]; then git clone {$repoUrl} . ; else git fetch --all && git reset --hard origin/{$branch}; fi",
                "git checkout {$branch}",
                "git pull origin {$branch}",
                "echo 'Start the container build process...'",
                "docker compose up -d --build"
            ];

            $fullCommand = implode(' && ', $commands);

            $ssh->exec($fullCommand, function ($outputString) {
                $lines = explode("\n", trim($outputString));
                foreach ($lines as $line) {
                    if (trim($line) !== '') {
                        $this->deployment->logs()->create([
                            'output'    => $line,
                            'type'      => 'info'
                        ]);
                    }
                }
            });

            $ssh->disconnect();

            if ($ssh->getExitStatus() === 0) {
                $this->deployment->update(['status' => 'completed']);
            } else {
                $this->deployment->update([
                    'status'            => 'failed',
                    'error_message'     => "Deployment script returned non-zero exit code: " . $ssh->getExitStatus()
                ]);
            }
        } catch (Throwable $e) {
            $this->deployment->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage()
            ]);

            $this->deployment->logs()->create([
                'output'    => 'CRITICAL SYSTEM ERROR: ' . $e->getMessage(),
                'type'      => 'error'
            ]);
        }
    }
}
