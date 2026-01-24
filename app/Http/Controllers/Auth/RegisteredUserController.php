<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Actions\Auth\RegisterUserAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request, RegisterUserAction $action)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'requireq|string|max:50',
            'username' => 'required|string|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'department' => 'required|string',
            'role' => 'required|in: System Administratorm Quality Assurance, Developer',
        ]);

        $user = $action->execute($data);
        Auth::login($user);

        return redirect('/dashboard');
    }
}
