<?php

namespace App\Actions\Admin;

use App\Models\Server;
use App\Enums\AuditSeverity;
use App\Services\Core\AuditLogger;
use App\DTOs\AuditLogData;
use Illuminate\Support\Facades\DB;

class ProvisionServerAction
{
    public function execute(array $data, string $adminId): Server
    {
        return DB::transaction(function () use ($data, $adminId) {
            $server = Server::create([
                'name' => $data['name'],
                'ip_address' => $data['ip_address'],
                'ssh_port' => $data['ssh_port'],
                'environment' => $data['environment'],
                'description' => $data['description'] ?? null,
                'status' => 'active',
                'ssh_user'        => $data['ssh_user'],
                'ssh_private_key' => $data['ssh_private_key'] ?? null,
            ]);

            AuditLogger::log(new AuditLogData(
                action: 'SERVER_ENTITY_PROVISIONED',
                category: 'infrastructure',
                severity: AuditSeverity::CRITICAL,
                user_id: $adminId,
                target_type: $server::class,
                target_id: $server->id,
                metadata: [
                    'server_name' => strtoupper($server->name),
                    'server_ip' => $server->ip_address,
                    'environment' => strtoupper($server->environment),
                ]
            ));

            return $server;
        });
    }
}
