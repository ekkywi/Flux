<?php

namespace App\Services\Stacks;

use App\Services\Stacks\StackInterface;
use App\Services\Stacks\LaravelStack;
use Exception;

class StackFactory
{
    public static function make(string $type): StackInterface
    {
        return match ($type) {
            'laravel' => app(LaravelStack::class),
            default => throw new Exception("Stack [{$type}] not yet supported by Flux."),
        };
    }
}
