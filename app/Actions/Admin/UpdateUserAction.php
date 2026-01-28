<?php

namespace App\Actions\Admin;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;

class UpdateUserAction
{
    public function execute(User $user, array $data, $adminId): User
    {
        return DB::transaction(function () use ($user, $data, $adminId) {
            $oldData = $user->only(['first_name', 'last-name', 'username', 'department', 'role', 'is_active']);

            $user->update($data);

            AuditLog::create([
                'user_id'           => $adminId,
                'action'            => 'IDENTITY_UPDATED',
                'category'          => 'IDENTITY',
                'severity'          => 'info',
                'target_type'       => 'User',
                'target_id'         => $user->id,
                'metadata'          => [
                    'before'        => $oldData,
                    'after'         => $user->only(['first_name', 'last_name', 'username', 'department', 'role', 'is_active']),
                    'target_user'   => $user->email
                ],
                'ip_address'        => request()->ip(),
                'user_agent'        => request()->userAgent(),
            ]);

            return $user;
        });
    }
}
