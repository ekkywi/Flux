<?php

namespace App\Services\Infrastructure;

use App\Models\Server;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ServerService
{
    public function getInventory($perPage = 10)
    {
        return Server::latest()->paginate($perPage);
    }

    public function provisionServer(array $data, array $context): Server
    {
        return DB::transaction(function () use ($data, $context) {
            $server = Server::create($data);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'Provisioned New Server',
                'category' => 'infrastructure',
                'severity' => 'info',
                'target_type' => 'Server',
                'target_id' => $server->id,
                'ip_address' => $context['ip'] ?? null,
                'user_agent' => $context['user_agent'] ?? null,
                'metadata' => [
                    'server_name' => strtoupper($server->name),
                    'ip_target' => $server->ip_address,
                    'environment' => strtoupper($server->environment),
                ]
            ]);

            return $server;
        });
    }
}
