<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Actions\Admin\ProvisionUserAction;
use App\Actions\Admin\RevokeUserAccessAction;
use App\Actions\Admin\UpdateUserAction;
use App\Actions\Admin\RestoreUserAction;
use Illuminate\Support\Facades\Auth;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request, ProvisionUserAction $action)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'username'   => 'required|string|alpha_dash|unique:users,username|max:50',
            'email'      => 'required|email|unique:users,email',
            'department' => 'required|string|max:100',
            'role'       => 'required|string',
            'temporary_password' => 'required|string|min:8',
        ]);

        try {
            $action->execute($validated, Auth::id());

            $fullName = $validated['first_name'] . ' ' . $validated['last_name'];
            return back()->with('success', "New identity provisioned: {$fullName}");
        } catch (\Exception $e) {
            return back()->with('error', "Provisioning failed: " . $e->getMessage());
        }
    }

    public function update(Request $request, User $user, UpdateUserAction $action)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'username'   => 'required|string|unique:users,username,' . $user->id,
            'email'      => 'required|email|unique:users,email,' . $user->id,
            'department' => 'required|string',
            'role'       => 'required|string',
            'is_active'  => 'required|boolean',
        ]);

        try {
            $action->execute($user, $validated, Auth::id());

            return back()->with('success', "Identity updated for {$user->username}");
        } catch (\Exception $e) {
            return back()->with('error', "Update failed: " . $e->getMessage());
        }
    }

    public function archived()
    {
        $archivedUsers = User::onlyTrashed()->latest('deleted_at')->paginate(15);
        return view('admin.users.archived', compact('archivedUsers'));
    }

    public function restore($id, RestoreUserAction $action)
    {
        try {
            $user = User::withTrashed()->findOrFail($id);

            $action->execute($user->id, Auth::id());

            return redirect()->route('admin.users.index')
                ->with('success', "Access restored for {$user->username}. Personnel is back to operational status.");
        } catch (\Exception $e) {
            return back()->with('error', "Restoration failed: " . $e->getMessage());
        }
    }

    public function destroy(User $user, RevokeUserAccessAction $action)
    {
        try {
            $action->execute($user, Auth::id());
            return back()->with('success', "Security clearance for {$user->username} has been revoked.");
        } catch (\Exception $e) {
            return back()->with('error', "Revocation failed: " . $e->getMessage());
        }
    }
}
