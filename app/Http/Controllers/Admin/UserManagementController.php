<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Actions\Admin\ProvisionUserAction;

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
            $action->execute($validated, auth()->id());

            $fullName = $validated['first_name'] . ' ' . $validated['last_name'];
            return back()->with('success', "New identity provisioned: {$fullName}");
        } catch (\Exception $e) {
            return back()->with('error', "Provisioning failed: " . $e->getMessage());
        }
    }
}
