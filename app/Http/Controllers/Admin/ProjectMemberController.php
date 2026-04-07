<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProjectMemberController extends Controller
{
    public function search(Request $request, Project $project)
    {
        $this->authorize('view', $project);

        $existingMemberIds = $project->members()->pluck('users.id')->toArray();

        $users = User::whereNotIn('id', $existingMemberIds)
            ->select('id', 'email', 'first_name', 'last_name')
            ->orderBy('first_name')
            ->limit(50)
            ->get();

        return response()->json($users);
    }

    private function getAllowedRoles(Project $project)
    {
        $user = Auth::user();

        if ($user->role === 'System Administrator') {
            return ['owner', 'manager', 'member'];
        }

        $myRole = $project->members()
            ->where('user_id', $user->id)
            ->value('project_members.role');

        if ($myRole === 'owner') {
            return ['owner', 'manager', 'member'];
        }

        if ($myRole === 'manager') {
            return ['member'];
        }

        return [];
    }

    public function store(Request $request, Project $project)
    {
        $this->authorize('addMember', $project);

        $allowedRoles = $this->getAllowedRoles($project);

        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'role'  => ['required', Rule::in($allowedRoles)],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if ($project->members()->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'User is already in the team.');
        }

        $project->members()->attach($user->id, [
            'role'  => $validated['role']
        ]);

        return back()->with('success', "{$user->first_name} added as {$validated['role']}.");
    }

    public function update(Request $request, Project $project, User $user)
    {
        $this->authorize('updateMember', [$project, $user]);

        $allowedRoles = $this->getAllowedRoles($project);

        $validated = $request->validate([
            'role'  => ['required', Rule::in($allowedRoles)],
        ]);

        $project->members()->updateExistingPivot($user->id, [
            'role'  => $validated['role']
        ]);

        return back()->with('success', "Role updated successfully.");
    }

    public function destroy(Project $project, User $user)
    {
        $this->authorize('removeMember', [$project, $user]);

        if ($user->id === Auth::id()) {
            return back()->with('error', "Cannot remove yourself. Leave project instead.");
        }

        $project->members()->detach($user->id);

        return back()->with('success', "Member removed from project.");
    }
}
