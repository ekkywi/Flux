<?php

namespace App\Services\Infrastructure;

use App\Models\Server;
use App\Models\SystemSetting;
use phpseclib3\Net\SSH2;
use phpseclib3\Crypt\RSA;
use Illuminate\Support\Facades\Log;

class SshConnectivityService
{
    public function verifyNode(Server $server): array
    {
        $masterKey = SystemSetting::where('key_name', 'master_ssh_key')->first();

        if (!$masterKey) {
            return ['status' => 'error', 'message' => 'Key not found.'];
        }

        try {
            $privateKey = RSA::load($masterKey->private_key);
            $ssh = new SSH2($server->ip_address, $server->ssh_port, 10);

            if (!$ssh->login($server->ssh_user, $privateKey)) {
                return ['status' => 'unauthorized', 'message' => 'Handshake failed.'];
            }

            $statsCommand = 'free -m | awk \'NR==2{printf "Memory: %s/%sMB (%.2f%%)", $3,$2,$3*100/$2 }\'; echo " | "; uptime | awk \'{print "Load: " $(NF-2) " " $(NF-1) " " $NF}\'';

            $output = $ssh->exec($statsCommand);
            $ssh->disconnect();

            return [
                'status' => 'success',
                'message' => 'Node Accessible',
                'detail' => trim($output)
            ];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
