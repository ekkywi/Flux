<?php

namespace App\Actions\Admin;

use App\Models\User;
use App\Enums\AuditSeverity;
use App\Services\Core\AuditLogger;
use App\DTOs\AuditLogData;
use Illuminate\Support\Facades\DB;

class RestoreUserAction
{
    public function execute($userId, $adminId): void
    {
        DB::transaction(function () use ($userId, $adminId) {
            $user = User::withTrashed()->findOrFail($userId);

            $user->restore();

            AuditLogger::log(new AuditLogData(
                action: 'ACCESS_RESTORED',
                category: 'SECURITY',
                severity: AuditSeverity::WARNING,
                user_id: $adminId,
                target_type: $user::class,
                target_id: $user->id,
                metadata: [
                    'target_user_email' => $user->email,
                    'restored_at'       => now()->toDateTimeString(),
                    'previous_status'   => 'soft_deleted'
                ]
            ));
        });
    }
}
