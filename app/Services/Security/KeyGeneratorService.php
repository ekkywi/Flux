<?php

namespace App\Services\Security;

use App\Models\SystemSetting;
use App\Models\AuditLog;
use phpseclib3\Crypt\RSA;
use Illuminate\Support\Facades\Auth;

class KeyGeneratorService
{
    public function generateMasterKey(array $requestContext = []): SystemSetting
    {
        $private = RSA::createKey(4096);
        $public = $private->getPublicKey();

        $setting = SystemSetting::updateOrCreate(
            ['key_name' => 'master_ssh_key'],
            [
                'private_key' => $private->toString('PKCS8'),
                'public_key' => $public->toString('OpenSSH'),
                'last_rotated_at' => now(),
            ]
        );

        $this->writeAuditLog($setting, $requestContext);

        return $setting;
    }

    protected function writeAuditLog(SystemSetting $setting, array $context = []): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'ROTATED_MASTER_SSH_KEY',
            'category' => 'secuirity',
            'severity' => 'critical',
            'target_type' => get_class($setting),
            'target_id' => $setting->id,
            'ip_address' => $context['ip'] ?? null,
            'user_agent' => $context['user_agent'] ?? null,
            'metadata' => [
                'key_name'  => strtoupper($setting->key_name),
                'algorithm' => 'RSA-4096',
                'method' => isset($context['ip']) ? 'Web Interface' : 'Console/Tinker',
            ]
        ]);
    }
}
