<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function before(User $user, $ability)
    {
        if ($user->role === 'System Administrator') {
            return true;
        }
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Project $project): bool
    {
        return $project->members()->where('user_id', $user->id)->exists();
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Project $project): bool
    {
        $member = $project->members()->where('user_id', $user->id)->first();

        return $member && in_array($member->pivot->role, ['owner', 'manager']);
    }

    public function manageMembers(User $user, Project $project): bool
    {
        $member = $project->members()->where('user_id', $user->id)->first();
        return $member && in_array($member->pivot->role, ['owner', 'manager']);
    }
}
