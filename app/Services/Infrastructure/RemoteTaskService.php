<?php

namespace App\Services\Infrastructure;

use App\Models\Server;
use Illuminate\Support\Facades\Log;
use phpseclib3\Net\SSH2;
use phpseclib3\Crypt\PublicKeyLoader;

class RemoteTaskService
{
    public function run(Server $server, string|array $commands): string
    {
        $ssh = new SSH2($server->ip_address, $server->ssh_port ?? 22);

        $privateKey = PublicKeyLoader::load(decrypt($server->ssh_private_key));

        if (!$ssh->login($server->ssh_user, $privateKey)) {
            throw new \Exception("SSH Login Failed for server: {$server->ip_address}");
        }

        $commandString = is_array($commands) ? implode(' && ', $commands) : $commands;

        Log::info("Executing on {$server->name}: {$commandString}");

        $output = $ssh->exec($commandString);

        return $output;
    }
}
