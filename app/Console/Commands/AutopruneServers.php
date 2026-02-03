<?php

namespace App\Console\Commands;

use App\Models\Server;
use App\Models\AuditLog;
use App\Services\Core\AuditLogger;
use App\DTOs\AuditLogData;
use App\Enums\AuditSeverity;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;



class AutopruneServers extends Command
{
    protected $signature = 'flux:autoprune-servers';
    protected $description = 'Backup and permanently delete servers trashed for more than 30 days';

    public function handle()
    {
        $threshold = now()->subDays(30);
        $servers = Server::onlyTrashed()->where('deleted_at', '<=', $threshold)->get();

        if ($servers->isEmpty()) {
            $this->info('Grid is clean. No expired entities found.');
            return;
        }

        foreach ($servers as $server) {
            $this->info("Archiving: {$server->name}");

            $logs = AuditLog::where('target_type', Server::class)
                ->where('target_id', $server->id)
                ->orderBy('created_at', 'desc')
                ->get();

            $snapshot = [
                'identity' => $server->makeVisible([
                    'ssh_port',
                    'ssh_user',
                    'description'
                ])->toArray(),
                'metadata' => [
                    'prune_at' => now()->toDateTimeString(),
                    'retention_period' => '30-days',
                    'category' => 'infrastructure'
                ],
                'audit_trail' => $logs->toArray(),
            ];

            if (!Storage::disk('local')->exists('archives/infrastructure')) {
                Storage::disk('local')->makeDirectory('archives/infrastructure');
            }

            $filename = "infrastructure/PRUNED_{$server->id}_" . now()->format('Ymd_His') . ".json";
            Storage::disk('local')->put("archives/{$filename}", json_encode($snapshot, JSON_PRETTY_PRINT));

            AuditLogger::log(new AuditLogData(
                action: 'SERVER_ENTITY_PURGED',
                category: 'infrastructure',
                severity: AuditSeverity::INFO,
                user_id: null,
                target_type: Server::class,
                target_id: $server->id,
                metadata: [
                    'server_name' => $server->name,
                    'reason' => 'Retention period exceeded (30 days).',
                    'backup_file' => $filename
                ]
            ));

            $server->forceDelete();
        }

        $this->info('Autoprune and audit logging completed.');
    }
}
