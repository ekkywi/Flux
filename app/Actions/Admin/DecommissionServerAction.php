<?php

namespace App\Actions\Admin;

use App\Models\Server;
use App\Enums\AuditSeverity;
use App\Services\Core\AuditLogger;
use App\DTOs\AuditLogData;
use Illuminate\Support\Facades\DB;

class DecommissionServerAction
{
    public function execute(Server $server, string $adminId): void
    {
        DB::transaction(function () use ($server, $adminId) {

            $snapshot = [
                'server_name' => $server->name,
                'server_ip'   => $server->ip_address,
                'environment' => strtoupper($server->environment),
                'decommissioned_at' => now()->toDateTimeString(),
            ];

            $server->delete();

            AuditLogger::log(new AuditLogData(
                action: 'INFRA_ENTITY_DECOMMISSIONED',
                category: 'infrastructure',
                severity: AuditSeverity::CRITICAL,
                user_id: $adminId,
                target_type: $server::class,
                target_id: $server->id,
                metadata: $snapshot
            ));
        });
    }
}
