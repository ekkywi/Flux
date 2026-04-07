<?php

namespace App\Policies;

use App\Models\Environment;
use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\Response;

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
        return $project->member()->where('user_id', $user->id)->exists();
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Project $project): bool
    {
        return $project->members()
            ->where('user_id', $user->id)
            ->wherePivotIn('role', ['owner', 'manager'])
            ->exists();
    }

    public function delete(User $user, Project $project): bool
    {
        return $project->members()
            ->where('user_id', $user->id)
            ->wherePivot('role', 'owner')
            ->exists();
    }

    public function addMember(User $user, Project $project): bool
    {
        return $project->members()
            ->where('user_id', $user->id)
            ->wherePivotIn('role', ['owner', 'manager'])
            ->exists();
    }

    public function updateMember(User $user, Project $project, User $targetUser): Response|bool
    {
        $actorRole = $project->members()
            ->where('user_id', $user->id)
            ->value('project_members.role');

        if (!in_array($actorRole, ['owner', 'manager'])) {
            return false;
        }

        if ($actorRole === 'manager') {
            $targetRole = $project->members()
                ->where('user_id', $targetUser->id)
                ->value('project_members.role');

            if ($targetRole === 'owner') {
                return Response::deny('Managers cannot modify the Project Owner.');
            }
        }

        return true;
    }

    public function removeMember(User $user, Project $project, User $targetUser): Response|bool
    {
        return $this->updateMember($user, $project, $targetUser);
    }

    public function deploy(User $user, Project $project, Environment $environment): bool
    {
        $membership = $project->members->find($user->id);

        if (!$membership) {
            return false;
        }

        $role = $membership->pivot->role;

        if (in_array($role, ['owner', 'manager'])) {
            return true;
        }

        if ($role === 'member') {
            return $environment->type !== 'production';
        }

        return false;
    }
}
