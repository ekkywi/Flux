<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Actions\Auth\LoginUserAction;
use App\Services\Core\AuditLogger;
use App\DTOs\AuditLogData;
use App\Enums\AuditSeverity;
use Illuminate\Support\Facades\Auth;

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
        if (Auth::check()) {
            AuditLogger::log(new AuditLogData(
                action: 'user_logout',
                category: 'authentication',
                severity: AuditSeverity::INFO,
                metadata: [
                    'email' => Auth::user()->email,
                    'username' => Auth::user()->username,
                ]
            ));
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
