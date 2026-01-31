<?php

namespace App\Enums;

enum AuditSeverity: string
{
    case INFO = 'info';
    case WARNING = 'warning';
    case CRITICAL = 'critical';
    case SUCCESS = 'success';
}
