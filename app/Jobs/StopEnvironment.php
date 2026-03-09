<?php

namespace App\Jobs;

use App\Models\Environment;
use App\Models\SystemSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use phpseclib3\Net\SSH2;
use phpseclib3\Crypt\RSA;
use Throwable;

class StopEnvironment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300;

    public function __construct(public Environment $environment) {}

    public function handle(): void
    {
        try {
            $project = $this->environment->project;
            $masterKey = SystemSetting::where('key_name', 'master_ssh_key')->first();
            $privateKey = RSA::load($masterKey->private_key);

            $appServer = $this->environment->server;
            $sshApp = new SSH2($appServer->ip_address, $appServer->ssh_port, 10);
            if ($sshApp->login($appServer->ssh_user, $privateKey)) {
                $sshApp->setTimeout(0);
                $workspace = "~/flux-projects/{$project->id}/{$this->environment->name}";
                $sshApp->exec("cd {$workspace} && docker compose stop 2>&1");
                $sshApp->disconnect();
            }

            $dbServer = $this->environment->dbServer;
            $dbType = strtolower(trim($project->build_options['database_type'] ?? 'sqlite'));
            if ($dbServer && in_array($dbType, ['mysql', 'pgsql'])) {
                $sshDb = new SSH2($dbServer->ip_address, $dbServer->ssh_port, 10);
                if ($sshDb->login($dbServer->ssh_user, $privateKey)) {
                    $sshDb->setTimeout(0);
                    $dbWorkspace = "~/flux-databases/{$project->id}/{$this->environment->name}";
                    $sshDb->exec("cd {$dbWorkspace} && docker compose stop 2>&1");
                    $sshDb->disconnect();
                }
            }

            $this->environment->update(['status' => 'stopped']);
        } catch (Throwable $e) {
            $this->environment->update(['status' => 'failed']);
            \Log::error("StopEnvironment Failed for {$this->environment->name}: " . $e->getMessage());
        }
    }
}
