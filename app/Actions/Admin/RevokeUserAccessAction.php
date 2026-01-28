<?php

namespace App\Actions\Admin;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;

class RevokeUserAccessAction
{
    public function execute(User $user, $adminId): void
    {
        DB::transaction(function () use ($user, $adminId) {
            $username = $user->username;

            $user->delete();

            AuditLog::create([
                'user_id'     => $adminId,
                'action'      => 'ACCESS_REVOKED',
                'category'    => 'SECURITY',
                'severity'    => 'critical',
                'target_type' => 'User',
                'target_id'   => $user->id,
                'metadata'    => [
                    'target_user'      => $user->email,
                    'termination_date' => now()->toDateTimeString(),
                    'method'           => 'administrative_purge'
                ],
                'ip_address'  => request()->ip(),
                'user_agent'  => request()->userAgent(),
            ]);
        });
    }
}
