<?php

namespace App\Policies;

use App\Models\Environment;
use App\Models\User;
use Illuminate\Support\Env;

class EnvironmentPolicy
{
    public function before(User $user, $ability)
    {
        if ($user->role === 'System Administrator') {
            return true;
        }
    }

    public function deploy(User $user, Environment $environment): bool
    {
        $membership = $environment->project->members->find($user->id);

        if (!$membership) {
            return false;
        }

        $role = $membership->pivot->role;

        if (in_array($role, ['owner', 'manager'])) {
            return true;
        }

        if ($role === 'member') {
            return $environment->type === 'production';
        }

        return false;
    }

    public function delete(User $user, Environment $environment): bool
    {
        $membership = $environment->project->members->find($user->id);

        if (!$membership) {
            return false;
        }

        $role = $membership->pivot->role;

        if (!in_array($role, ['owner', 'manager'])) {
            return false;
        }

        if ($environment->type === 'production') {
            return $role === 'owner';
        }

        return true;
    }
}
