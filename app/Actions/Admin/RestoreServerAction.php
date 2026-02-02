<?php

namespace App\Actions\Admin;

use App\Models\Server;
use App\Enums\AuditSeverity;
use App\Services\Core\AuditLogger;
use App\DTOs\AuditLogData;
use Illuminate\Support\Facades\DB;

class RestoreServerAction
{
    public function execute(string $serverId, string $adminId): Server
    {
        return DB::transaction(function () use ($serverId, $adminId) {

            $server = Server::withTrashed()->findOrFail($serverId);

            $server->restore();

            AuditLogger::log(new AuditLogData(
                action: 'INFRA_ENTITY_RESTORED',
                category: 'infrastructure',
                severity: AuditSeverity::WARNING,
                user_id: $adminId,
                target_type: $server::class,
                target_id: $server->id,
                metadata: [
                    'server_name' => $server->name,
                    'server_ip'   => $server->ip_address,
                    'status_before' => 'decommissioned'
                ]
            ));

            return $server;
        });
    }
}
