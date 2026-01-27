<?php

namespace App\Actions\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginUserAction
{
    public function execute(array $credentials, bool $remember)
    {
        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                throw ValidationException::withMessages([
                    'login' => 'Your account is awaiting approval from the Administrator.'
                ]);
            }

            $user->update(['last_login_at' => now()]);
        }

        return false;
    }
}
