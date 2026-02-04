<?php

namespace App\Services\Stacks;

use App\Services\Integrations\GiteaIntegrationService;

class LaravelStack implements StackInterface
{
    public function __construct(
        protected GiteaIntegrationService $gitea
    ) {}

    public function identify(string $repoUrl): bool
    {
        $composerJson = $this->gitea->getFileContent($repoUrl, 'compose.json');

        if (!$composerJson) {
            return false;
        }

        $data = json_decode($composerJson, true);

        return isset($data['require']['laravel/framework']);
    }

    public function analyzeRisk(string $repoUrl, array $userChoices): array
    {
        $analysis = [
            'has_docker' => false,
            'conflicts'  => [],
        ];

        $dockerfile = $this->gitea->getFileContent($repoUrl, 'Dockerfile');

        if ($dockerfile) {
            $analysis['has_docker'] = true;

            $selectedPhp = $userChoices['php_version'] ?? '8.3';
            if (!str_contains($dockerfile, "php:{$selectedPhp}")) {
                $analysis['conflicts'][] = "Mismatch Versi PHP: Dockerfile menggunakan versi berbeda dari pilihan UI ({$selectedPhp}).";
            }
        }

        return $analysis;
    }
}
