<?php

namespace App\Actions\Auth;

use App\Models\User;
use App\Enums\ApprovalType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterUserAction
{
    public function execute(array $data): void
    {
        DB::transaction(function () use ($data) {
            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'department' => $data['department'],
                'role' => $data['role'],
                'is_active' => false,
            ]);

            $user->accessRequest()->create([
                'request_type' => ApprovalType::ACCOUNT_REQUEST,
                'requested_role' => $data['role'],
                'justification' => $data['justification'],
                'status' => 'pending'
            ]);
        });
    }
}
