<?php

namespace App\Actions\Admin;

use App\Models\Server;
use App\Enums\AuditSeverity;
use App\Services\Core\AuditLogger;
use App\DTOs\AuditLogData;
use Illuminate\Support\Facades\DB;

class UpdateServerAction
{
    public function execute(Server $server, array $data, string $adminId): Server
    {
        return DB::transaction(function () use ($server, $data, $adminId) {
            $before = $server->getOriginal();

            $server->update($data);

            $after = $server->getChanges();
            unset($after['updated_at']);

            if (count($after) > 0) {
                AuditLogger::log(new AuditLogData(
                    action: 'SERVER_ENTITY_UPDATED',
                    category: 'infrastructure',
                    severity: AuditSeverity::WARNING,
                    user_id: $adminId,
                    target_type: $server::class,
                    target_id: $server->id,
                    metadata: [
                        'server_name' => $server->name,
                        'before'      => array_intersect_key($before, $after),
                        'after'       => $after,
                    ]
                ));
            }

            return $server;
        });
    }
}
