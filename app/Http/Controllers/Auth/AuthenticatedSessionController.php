<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Actions\Auth\LoginUserAction;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request, LoginUserAction $action)
    {
        $input = $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        $loginType = filter_var($input['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $success = $action->execute(
            [$loginType => $input['login'], 'password' => $input['password']],
            $request->boolean('remember')
        );

        if ($success) {
            $request->session()->regenerate();
            return redirect()->intended(route('console.dashboard'));
        }

        return back()->withErrors(['login' => 'Invalid credentials']);
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
