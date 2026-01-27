<?php

namespace App\Actions\Admin;

use App\Models\AccessRequest;
use App\Enums\ApprovalType;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class ApproveAccessRequestAction
{
    public function execute(AccessRequest $request, $adminId): void
    {
        // if (!$request->request_type) {
        //     throw new \Exception("Invalid Request: request_type is missing for ID {$request->id}");
        // }

        $metadata = $request->metadata ?? [];

        match ($request->request_type) {
            ApprovalType::ACCOUNT_REQUEST => $request->user->update(['is_active' => true]),
            ApprovalType::RESET_PASSWORD => $this->handleResetPassword($request, $metadata),
            ApprovalType::SERVER_ACCESS => $this->handleServerAccess($request),
        };

        $request->update([
            'status' => 'approved',
            'processed_at' => now(),
            'processed_by' => $adminId,
            'metadata' => $metadata,
        ]);
    }

    protected function handleResetPassword($request, &$metadata)
    {
        $temporaryPassword = Str::random(15);

        $request->user->update([
            'password' => Hash::make($temporaryPassword)
        ]);

        $metadata['temporary_password'] = $temporaryPassword;
    }

    public function handleServerAccess($request)
    {
        if (!isset($request->metadata['public_key'])) {
            throw new \Exception("Public Key is missing for server access request.");
        }
    }
}
