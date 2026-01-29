<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Actions\Auth\RegisterUserAction;
use Illuminate\Validation\Rules\Password;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request, RegisterUserAction $registerAction)
    {
        $data = $request->validate([
            'first_name'    => ['required', 'string', 'max:50'],
            'last_name'     => ['required', 'string', 'max:50'],
            'username'      => ['required', 'string', 'min:4', 'unique:users,username'],
            'email'         => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'      => ['required', 'confirmed', Password::defaults()],
            'department'    => ['required', 'string', 'max:100'],
            'role'          => ['required', 'in:Developer,Quality Assurance,System Administrator'],
            'justification' => ['required', 'string', 'min:10', 'max:1000'],
        ]);

        $registerAction->execute($data);

        return redirect()->route('login')->with('success', 'Request access submitted. Our administrator will review your justification shortly.');
    }
}
