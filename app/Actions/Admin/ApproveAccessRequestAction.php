<?php

namespace App\Actions\Admin;

use App\Models\AccessRequest;
use App\Models\AuditLog;
use App\Enums\ApprovalType;
use App\Enums\AuditSeverity;
use App\Services\Core\AuditLogger;
use App\DTOs\AuditLogData;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ApproveAccessRequestAction
{
    public function execute(AccessRequest $request, $adminId): void
    {
        if (!$request->request_type) {
            throw new \Exception("Critical: Request type (request_type) not found in ID {$request->id}");
        }

        $metadata = $request->metadata ?? [];

        DB::transaction(function () use ($request, $adminId, &$metadata) {

            match ($request->request_type) {
                ApprovalType::ACCOUNT_REQUEST => $this->handleAccountProvisioning($request, $metadata),
                ApprovalType::RESET_PASSWORD  => $this->handleResetPassword($request, $metadata),
                ApprovalType::SERVER_ACCESS   => $this->handleServerAccess($request),
                default => throw new \Exception("The approval type is not recognized by the system."),
            };

            $request->update([
                'status'       => 'approved',
                'processed_at' => now(),
                'processed_by' => $adminId,
                'metadata'     => $metadata,
            ]);

            AuditLogger::log(new AuditLogData(
                action: 'access_authorized',
                category: 'security',
                user_id: $adminId,
                severity: AuditSeverity::CRITICAL,
                target_type: $request::class,
                target_id: $request->id,
                metadata: [
                    'request_type' => $request->request_type->value,
                    'target_user' => $request->user->email,
                    'provisioned' => $metadata['provisioned_at'] ?? null,
                ]
            ));
        });
    }

    protected function handleAccountProvisioning($request, &$metadata)
    {
        $request->user->update(['is_active' => true]);

        $metadata['provisioned_at'] = now()->toDateTimeString();
    }

    protected function handleResetPassword($request, &$metadata)
    {
        $temporaryPassword = Str::random(15);

        $request->user->update([
            'password' => Hash::make($temporaryPassword)
        ]);

        $metadata['temporary_password'] = $temporaryPassword;
    }

    protected function handleServerAccess($request)
    {
        if (!isset($request->metadata['public_key'])) {
            throw new \Exception("Public Key is missing. Failed to grant server access.");
        }
    }
}
