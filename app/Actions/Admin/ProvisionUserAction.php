<?php

namespace App\Actions\Admin;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProvisionUserAction
{
    public function execute(array $data, string $adminId): User
    {
        return DB::transaction(function () use ($data, $adminId) {
            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'username'   => $data['username'],
                'email' => $data['email'],
                'department' => $data['department'],
                'password' => Hash::make($data['temporary_password']),
                'role' => $data['role'],
                'is_active' => true,
            ]);

            AuditLog::create([
                'user_id' => $adminId,
                'action' => 'IDENTITY_PROVISIONED',
                'category' => 'identity',
                'severity' => 'critical',
                'target_type' => User::class,
                'target_id' => $user->id,
                'metadata' => [
                    'provisioned_email' => $user->email,
                    'assigned_role' => $user->role,
                    'note' => 'Manual provisioning by Administrator'
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return $user;
        });
    }
}
