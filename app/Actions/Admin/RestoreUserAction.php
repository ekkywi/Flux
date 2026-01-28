<?php

namespace App\Actions\Admin;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;

class RestoreUserAction
{
    public function execute($userId, $adminId): void
    {
        DB::transaction(function () use ($userId, $adminId) {
            $user = User::withTrashed()->findOrFail($userId);

            $user->restore();

            AuditLog::create([
                'user_id'     => $adminId,
                'action'      => 'ACCESS_RESTORED',
                'category'    => 'SECURITY',
                'severity'    => 'warning',
                'target_type' => 'User',
                'target_id'   => $user->id,
                'metadata'    => [
                    'target_user' => $user->email,
                    'restored_at'   => now()->toDateTimeString(),
                ],
                'ip_address'  => request()->ip(),
                'user_agent'  => request()->userAgent(),
            ]);
        });
    }
}
