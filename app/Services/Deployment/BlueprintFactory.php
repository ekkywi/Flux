<?php

namespace App\Services\Deployment;

use App\Services\Deployment\Blueprint\BlueprintInterfance;
use App\Services\Deployment\Blueprints\BlueprintInterface;
use App\Services\Deployment\Blueprints\LaravelBlueprint;
use Exception;

class BlueprintFactory
{
    public static function make(string $stack): BlueprintInterface
    {
        return match (strtolower($stack)) {
            'laravel', 'php' => new LaravelBlueprint(),
            default => throw new Exception("Stack '{$stack}' is not yet supported by the Flux system."),
        };
    }
}
