<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(request $request)
    {
        $input = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginType = filter_var($input['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::attempt([$loginType => $input['login'], 'password' => $input['password']])) {

            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();

                return back()->withErrors([
                    'login' => 'Akun Anda sedang persetujuan dari Administrator.',
                ])->onlyInput('login');
            }

            $request->session()->regenerate();
            $user->update(['last_login_at' => now()]);
        }

        return back()->withErrors(['login' => 'Kredensial tidak valid']);
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
