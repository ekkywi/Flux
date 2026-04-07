<?php

namespace App\Enums;

enum ProjectStatus: string
{
    case ACTIVE = 'active';
    case ARCHIVED = 'archive';
    case MAINTENANCE = 'maintenance';
    case FAILED = 'failed';

    public function color(): string
    {
        return match ($this) {
            self::ACTIVE => 'text-cyan-600 bg-cyan-50 border-cyan-100',
            self::ARCHIVED => 'text-zinc-500 bg-zinc-100 border-zinc-200',
            self::MAINTENANCE => 'text-yellow-600 bg-yellow-50 border-yellow-100',
            self::FAILED => 'text-red-600 bg-red-50 border-red-100',
        };
    }
}
