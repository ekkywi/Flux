<?php

namespace App\Services\Infrastructure;

use App\Models\Server;
use App\Models\SystemSetting; // <--- Import ini
use Illuminate\Support\Facades\Log;
use phpseclib3\Net\SSH2;
use phpseclib3\Crypt\PublicKeyLoader;

class RemoteTaskService
{
    public function run(Server $server, string|array $commands, callable $onOutput = null): string
    {
        // 1. VALIDASI IP
        if (empty($server->ip_address)) {
            throw new \Exception("Server IP Address tidak ditemukan untuk server: {$server->name}");
        }

        // 2. STRATEGI PENGAMBILAN KEY (Server Specific vs Master Key)
        $rawKey = $server->ssh_private_key;
        $usingMasterKey = false;

        // Jika Server tidak punya key spesifik, ambil Master Key
        if (empty($rawKey)) {
            $masterSetting = SystemSetting::where('key_name', 'master_ssh_key')->first();

            if (!$masterSetting || empty($masterSetting->private_key)) {
                throw new \Exception("CRITICAL: Server tidak memiliki Private Key, dan Master Key belum digenerate di System Settings.");
            }

            $rawKey = $masterSetting->private_key;
            $usingMasterKey = true;
        }

        // 3. SMART DECRYPT (Tangani Terenkripsi vs Plain)
        try {
            // Cek apakah key perlu didekripsi (fitur Laravel Casts 'encrypted')
            // Key RSA asli pasti diawali '-----BEGIN', jika tidak berarti hash terenkripsi
            if (!str_contains($rawKey, '-----BEGIN')) {
                try {
                    $rawKey = decrypt($rawKey);
                } catch (\Exception $e) {
                    throw new \Exception("Gagal mendekripsi Key (" . ($usingMasterKey ? "Master" : "Server") . "). Cek APP_KEY Anda.");
                }
            }

            // Load Key ke Library SSH
            $privateKey = PublicKeyLoader::load($rawKey);
        } catch (\Exception $e) {
            Log::error("SSH Key Error pada server {$server->name}: " . $e->getMessage());
            throw new \Exception("Format SSH Key Salah atau Corrupt. Detail: " . $e->getMessage());
        }

        // 4. KONEKSI SSH
        $ssh = new SSH2($server->ip_address, $server->ssh_port ?? 22);
        $ssh->setTimeout(0);

        if (!$ssh->login($server->ssh_user, $privateKey)) {
            throw new \Exception("SSH Login Failed! User [{$server->ssh_user}] ditolak oleh server {$server->ip_address}. Pastikan Public Key Master sudah didistribusikan ke server ini.");
        }

        // 5. EKSEKUSI COMMAND
        $commandString = is_array($commands) ? implode(' && ', $commands) : $commands;

        Log::info("Flux Executing on {$server->name} (" . ($usingMasterKey ? "via MasterKey" : "via CustomKey") . "): {$commandString}");

        if ($onOutput) {
            $onOutput("--- CONNECTED TO {$server->ip_address} using " . ($usingMasterKey ? "MASTER KEY" : "CUSTOM KEY") . " ---");
        }

        $output = $ssh->exec($commandString, function ($buffer) use ($onOutput) {
            if ($onOutput) {
                $onOutput($buffer);
            }
        });

        return (string) $output;
    }
}
