<?php

namespace App\Actions\Admin;

use App\Models\User;
use App\Enums\AuditSeverity;
use App\Services\Core\AuditLogger;
use App\DTOs\AuditLogData;
use Illuminate\Support\Facades\DB;

class RevokeUserAccessAction
{
    public function execute(User $user, $adminId): void
    {
        DB::transaction(function () use ($user, $adminId) {

            $snapshot = [
                'target_user_email' => $user->email,
                'target_user_name'  => $user->username,
                'target_user_role'  => $user->role,
                'termination_date'  => now()->toDateTimeString(),
                'method'            => 'administrative_purge'
            ];

            AuditLogger::log(new AuditLogData(
                action: 'access_revoked',
                category: 'security',
                severity: AuditSeverity::CRITICAL,
                user_id: $adminId,
                target_type: $user::class,
                target_id: $user->id,
                metadata: $snapshot
            ));

            // 3. EXECUTION: Hapus user secara permanen
            $user->delete();
        });
    }
}
