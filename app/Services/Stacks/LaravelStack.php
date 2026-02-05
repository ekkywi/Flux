<?php

namespace App\Services\Stacks;

use App\Services\Integrations\GiteaIntegrationService;
use Illuminate\Support\Facades\Log;

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

    public function validateRepository(string $repoUrl): bool
    {
        try {
            $gitea = app(GiteaIntegrationService::class);

            $composerJson = $gitea->getFileContent($repoUrl, 'composer.json');

            if (!$composerJson) {
                return false;
            }

            $data = json_decode($composerJson, true);

            return isset($data['require']['laravel/framework']);
        } catch (\Exception $e) {
            Log::error("Validation failed for {$repoUrl}: " . $e->getMessage());
            return false;
        }
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
