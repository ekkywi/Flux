<?php

namespace App\Services\Stacks;

interface StackInterface
{
    public function validateRepository(string $repoUrl): bool;

    public function analyzeRisk(string $repoUrl, array $userChoises): array;
}
