<?php

namespace App\Actions\Admin;

use App\Models\AccessRequest;
use App\Enums\ApprovalType;

class RejectAccessRequestAction
{
    public function execute(AccessRequest $request, string $reason, $adminId): void
    {
        $request->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'porcessed_at' => now(),
            'processed_by' => $adminId,
        ]);

        if ($request->request_type === ApprovalType::ACCOUNT_REQUEST) {
            $request->user->delete();
        }
    }
}
