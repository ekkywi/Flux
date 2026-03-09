<?php

namespace App\Jobs;

use App\Models\Environment;
use App\Models\SystemSetting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use phpseclib3\Net\SSH2;
use phpseclib3\Crypt\RSA;
use Throwable;

class StartEnvironment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Environment $environment
    ) {}

    public function handle(): void
    {
        try {
            $project = $this->environment->project;
            $appServer = $this->environment->server;
            $dbServer = $this->environment->dbServer;
            $dbType = strtolower(trim($project->build_options['database_type'] ?? 'sqlite'));

            $masterKey = SystemSetting::where('key_name', 'master_ssh_key')->first();
            if (!$masterKey) throw new \Exception('Master SSH Key not found');
            $privateKey = RSA::load($masterKey->private_key);

            if (in_array($dbType, ['mysql', 'pgsql', 'mariadb']) && $dbServer) {
                $sshDb = new SSH2($dbServer->ip_address, $dbServer->ssh_port, 10);
                if ($sshDb->login($dbServer->ssh_user, $privateKey)) {
                    $dbWorkspace = "~/flux-databases/{$project->id}/{$this->environment->name}";
                    $sshDb->exec("cd {$dbWorkspace} && docker compose start");
                    $sshDb->disconnect();
                }
            }

            $sshApp = new SSH2($appServer->ip_address, $appServer->ssh_port, 10);
            if (!$sshApp->login($appServer->ssh_user, $privateKey)) {
                throw new \Exception("SSH Login failed to App Server.");
            }
            $workspace = "~/flux-projects/{$project->id}/{$this->environment->name}";
            $outputApp = $sshApp->exec("cd {$workspace} && docker compose start 2>&1");
            Log::info("[START APP SERVER] " . trim($outputApp));
            $sshApp->exec("cd {$workspace} && docker compose start");
            $status = ($sshApp->getExitStatus() === 0) ? 'running' : 'failed';
            $this->environment->update(['status' => $status]);
            $sshApp->disconnect();
        } catch (Throwable $e) {
            Log::error('START ENV ERROR: ' . $e->getMessage());
            $this->environment->update(['status' => 'failed']);
        }
    }
}
