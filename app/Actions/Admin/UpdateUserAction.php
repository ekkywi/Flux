<?php

namespace App\Actions\Admin;

use App\Models\User;
use App\Enums\AuditSeverity;
use App\Services\Core\AuditLogger;
use App\DTOs\AuditLogData;
use Illuminate\Support\Facades\DB;

class UpdateUserAction
{
    public function execute(User $user, array $data, $actorId): User
    {
        return DB::transaction(function () use ($user, $data, $actorId) {
            $before = $user->getOriginal();
            $user->update($data);
            $after = $user->getChanges();

            unset($after['updated_at'], $after['password'], $after['remember_token']);

            if (count($after) > 0) {
                $originalValues = array_intersect_key($before, $after);

                AuditLogger::log(new AuditLogData(
                    action: 'identity_updated',
                    category: 'identity',
                    severity: AuditSeverity::INFO,
                    user_id: $actorId,
                    target_type: $user::class,
                    target_id: $user->id,
                    metadata: [
                        'target_user_email' => $user->email,
                        'before' => $originalValues,
                        'after'  => $after,
                    ]
                ));
            }

            return $user;
        });
    }
}
