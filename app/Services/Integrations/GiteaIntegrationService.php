<?php

namespace App\Services\Integrations;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;

class GiteaIntegrationService
{
    protected string $baseUrl;
    protected string $token;

    public function __construct()
    {
        $this->baseUrl = config('services.gitea.base_url');
        $this->token = config('services.gitea.token');
    }

    public function parseRepoUrl(string $url): array
    {
        $path = parse_url($url, PHP_URL_PATH);
        $parts = explode('/', trim($path, '/'));
        $repoName = str_replace('.git', '', end($parts));
        $owner = $parts[count($parts) - 2] ?? '';

        return [
            'owner'     => $owner,
            'repo'      => $repoName
        ];
    }

    public function getFileContent(string $url, string $path): ?string
    {
        $info = $this->parseRepoUrl($url);

        $response = Http::withToken($this->token)
            ->get("{$this->baseUrl}/repos/{$info['owner']}/{$info['repo']}/contents/{$path}");

        /** @var Response $response */
        if ($response->successful()) {
            return base64_decode($response->json('content'));
        }

        return null;
    }

    public function validateAccess(string $url): bool
    {
        $info = $this->parseRepoUrl($url);

        /** @var Response $response */
        $response = $this->request()->get("{$this->baseUrl}/repos/{$info['owner']}/{$info['repo']}");
        return $response->successful();
    }

    protected function request()
    {
        return Http::withToken($this->token)
            ->withHeaders([
                'Accept'        => 'application/json',
                'User-Agent'    => 'Flux-Console-Orchestrator'
            ]);
    }
}
