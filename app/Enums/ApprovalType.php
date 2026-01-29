<?php

namespace App\Enums;

enum ApprovalType: string
{
    case ACCOUNT_REQUEST = 'account_request';
    case RESET_PASSWORD = 'reset_password';
    case SERVER_ACCESS = 'server_access';

    public function label(): string
    {
        return match ($this) {
            self::ACCOUNT_REQUEST => 'New Account Request',
            self::RESET_PASSWORD => 'Password Reset Request',
            self::SERVER_ACCESS => 'Server Access Provisioning',
        };
    }
}
