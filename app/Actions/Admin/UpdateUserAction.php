<?php

namespace App\Actions\Admin;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;

class UpdateUserAction
{
    public function execute(User $user, array $data, $actorId)
    {
        return DB::transaction(function () use ($user, $data, $actorId) {
            $before = $user->getOriginal();
            $user->update($data);
            $after = $user->getChanges();

            unset($after['updated_at']);

            if (count($after) > 0) {
                AuditLog::create([
                    'user_id'     => $actorId,
                    'action'      => 'IDENTITY_UPDATED',
                    'category'    => 'Identity',
                    'severity'    => 'info',
                    'target_type' => 'User',
                    'target_id'   => $user->id,
                    'ip_address'  => request()->ip(),
                    'user_agent'  => request()->userAgent(),
                    'metadata'    => [
                        'before'   => array_intersect_key($before, $after),
                        'after'    => $after,
                        'username' => $user->username
                    ]
                ]);
            }
            return $user;
        });
    }
}
