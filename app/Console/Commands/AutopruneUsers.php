<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\Core\AuditLogger;
use App\DTOs\AuditLogData;
use App\Enums\AuditSeverity;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class AutopruneUsers extends Command
{
    protected $signature = 'flux:autoprune-users';
    protected $description = 'Clean up users trashed for more than 30 days and archive entity';

    public function handle()
    {
        $threshold = now()->subDays(30);
        $users = User::onlyTrashed()->where('deleted_at', '<=', $threshold)->get();

        if ($users->isEmpty()) {
            $this->info('Identity Vault is up to date. No expired users found.');
            return;
        }

        foreach ($users as $user) {
            $this->info("Archiving User: {$user->email}");

            $userData = $user->toArray();

            unset(
                $userData['password'],
                $userData['remember_token'],
                $userData['two_factor_secret'],
                $userData['two_factor_recovery_codes']
            );

            $snapshot = [
                'identity' => $userData,
                'metadata' => [
                    'pruned_at'     => now()->toDateTimeString(),
                    'reason'        => 'Retention exceeded 30 days',
                    'category'      => 'identity'
                ],
                'audit_trail'       => $user->auditLogs()->orderBy('created_at', 'desc')->get()->toArray(),
            ];

            $filename = "identity/USER_ARCHIVE_{$user->id}_" . now()->format('Ymd_His') . ".json";

            if (!Storage::disk('local')->exists('archives/identity')) {
                Storage::disk('local')->makeDirectory('archives/identity');
            }

            Storage::disk('local')->put("archives/{$filename}", json_encode($snapshot, JSON_PRETTY_PRINT));

            AuditLogger::log(new AuditLogData(
                action: 'USER_ENTITY_PURGED',
                category: 'identity',
                severity: AuditSeverity::INFO,
                user_id: null,
                target_type: User::class,
                target_id: $user->id,
                metadata: [
                    'email'         => $user->email,
                    'archive_file'  => $filename
                ]
            ));

            $user->forceDelete();
        }

        $this->info('User autoprune completed successfully.');
    }
}
