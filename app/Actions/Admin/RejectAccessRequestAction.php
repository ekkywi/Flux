<?php

namespace App\Actions\Admin;

use App\Models\AccessRequest;
use App\Enums\ApprovalType;
use App\Enums\AuditSeverity;
use App\Services\Core\AuditLogger;
use App\DTOs\AuditLogData;
use Illuminate\Support\Facades\DB;

use function Symfony\Component\Translation\t;

class RejectAccessRequestAction
{
    public function execute(AccessRequest $request, string $reason, $adminId): void
    {
        DB::transaction(function () use ($request, $reason, $adminId) {
            $targetUserEmail = $request->user->email ?? 'Unknown User';
            $targetUserName = $request->user->username ?? 'Unkown';

            $request->update([
                'status' => 'rejected',
                'rejection_reason' => $reason,
                'processed_at' => now(),
                'processed_by' => $adminId,
            ]);

            AuditLogger::log(new AuditLogData(
                action: 'access_rejected',
                category: 'security',
                severity: AuditSeverity::WARNING,
                user_id: $adminId,
                target_type: $request::class,
                target_id: $request->id,
                metadata: [
                    'request_type' => $request->request_type->value,
                    'reason' => $reason,
                    'target_user_email' => $targetUserEmail,
                    'target_user_name' => $targetUserName,
                    'action_taken' => $request->request_type === ApprovalType::ACCOUNT_REQUEST
                        ? 'account_deleted'
                        : 'request_denied',
                ]
            ));
        });
    }
}
