<?php

namespace App\Services\Deployment\Blueprints;

interface BlueprintInterface
{
    public function getDockerfile(array $options = []): string;

    public function getDockerCompose(array $options = []): string;
}
