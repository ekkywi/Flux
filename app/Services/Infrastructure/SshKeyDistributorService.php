<?php

namespace App\Services\Infrastructure;

use App\Models\Server;
use App\Models\SystemSetting;
use phpseclib3\Net\SSH2;

class SshKeyDistributorService
{
    public function deploy(Server $server, string $password): array
    {
        $masterKey = SystemSetting::where('key_name', 'master_ssh_key')->first();

        if (!$masterKey || !$masterKey->public_key) {
            return [
                'status' => 'error',
                'message' => 'Public key not found.'
            ];
        }

        try {
            $ssh = new SSH2($server->ip_address, $server->ssh_port, 10);

            if (!$ssh->login($server->ssh_user, $password)) {
                return [
                    'status' => 'error',
                    'message' => 'Invalid credentials provided.'
                ];
            }

            $publicKey = $masterKey->public_key;

            $script = "mkdir -p ~/.ssh && chmod 700 ~/.ssh && echo \"$publicKey\" >> ~/.ssh/authorized_keys && chmod 600 ~/.ssh/authorized_keys";

            $ssh->exec($script);
            $ssh->disconnect();

            return [
                'status' => 'success',
                'message' => 'Public key successfully deployed to ' . $server->name
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
}
