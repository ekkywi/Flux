<?php

namespace App\Actions\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Services\Core\AuditLogger;
use App\DTOs\AuditLogData;
use App\Enums\AuditSeverity;

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

        AuditLogger::Log(new AuditLogData(
            action: 'user_login_attempt',
            category: 'authentication',
            severity: Auth::check() ? AuditSeverity::INFO : AuditSeverity::WARNING,
            metadata: [
                'username' => $credentials['username'] ?? null,
                'email' => $credentials['email'] ?? null,
                'successful' => Auth::check(),
            ]
        ));

        return false;
    }
}
