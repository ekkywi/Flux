<?php

namespace App\Actions\Admin;

use App\Models\User;
use App\Enums\AuditSeverity;
use App\Services\Core\AuditLogger;
use App\DTOs\AuditLogData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProvisionUserAction
{
    public function execute(array $data, string $adminId): User
    {
        return DB::transaction(function () use ($data, $adminId) {
            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name'  => $data['last_name'],
                'username'   => $data['username'],
                'email'      => $data['email'],
                'department' => $data['department'],
                'password'   => Hash::make($data['temporary_password']),
                'role'       => $data['role'],
                'is_active'  => true,
            ]);

            AuditLogger::log(new AuditLogData(
                action: 'IDENTITY_PROVISIONED',
                category: 'identity',
                severity: AuditSeverity::CRITICAL,
                user_id: $adminId,
                target_type: $user::class,
                target_id: $user->id,
                metadata: [
                    'target_user_email' => $user->email,
                    'assigned_role'     => $user->role,
                    'note'              => 'Manual provisioning by Administrator',
                ]
            ));

            return $user;
        });
    }
}
