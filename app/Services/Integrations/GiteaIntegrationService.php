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
        // 1. Ambil URL dari config/env (misal: http://172.17.21.80:3000)
        $urlFromEnv = rtrim(\config('services.gitea.url'), '/');

        // 2. Otomatis tambahkan /api/v1 di belakangnya
        // Hasil akhir: http://172.17.21.80:3000/api/v1
        $this->baseUrl = $urlFromEnv . '/api/v1';

        // 3. Ambil Token
        $this->token = \config('services.gitea.token');
    }

    /**
     * Helper central untuk membuat request HTTP dengan Header yang BENAR untuk Gitea.
     */
    protected function request()
    {
        // PERBAIKAN UTAMA:
        // Jangan pakai Http::withToken() karena itu mengirim "Bearer <token>".
        // Gitea membutuhkan format "token <token>" (lowercase 'token' + spasi).

        return Http::withHeaders([
            'Authorization' => 'token ' . $this->token,
            'Accept'        => 'application/json',
            'User-Agent'    => 'Flux-Console-Orchestrator'
        ])->timeout(10); // Timeout 10 detik agar tidak hanging
    }

    /**
     * Parsing URL Git untuk mendapatkan Owner dan Repo Name.
     * Contoh: http://gitea.local/ekky/laravel-app.git -> ['owner' => 'ekky', 'repo' => 'laravel-app']
     */
    public function parseRepoUrl(string $url): array
    {
        $path = parse_url($url, PHP_URL_PATH);
        $path = trim($path, '/');
        $path = str_replace('.git', '', $path);

        $parts = explode('/', $path);

        // Ambil bagian terakhir sebagai repo, bagian sebelumnya sebagai owner
        $repoName = array_pop($parts);
        $owner = array_pop($parts);

        return [
            'owner'     => $owner,
            'repo'      => $repoName,
            'full_name' => "$owner/$repoName"
        ];
    }

    /**
     * Mengambil daftar branch dari repository.
     */
    public function getBranches(string $url): array
    {
        $info = $this->parseRepoUrl($url);

        try {
            $response = $this->request()->get("{$this->baseUrl}/repos/{$info['owner']}/{$info['repo']}/branches");

            if ($response->successful()) {
                // Return array nama branch saja: ['main', 'develop', 'staging']
                return collect($response->json())->pluck('name')->toArray();
            }

            Log::warning("Gitea API Error (Get Branches): " . $response->body());
            return [];
        } catch (\Exception $e) {
            Log::error("Gitea Connection Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Mengambil isi file tertentu dari repo (misal: composer.json).
     */
    public function getFileContent(string $url, string $path): ?string
    {
        $info = $this->parseRepoUrl($url);

        $response = $this->request()
            ->get("{$this->baseUrl}/repos/{$info['owner']}/{$info['repo']}/contents/{$path}");

        if ($response->successful()) {
            // Konten dari Gitea di-encode base64, kita decode dulu
            return base64_decode($response->json('content'));
        }

        return null;
    }

    /**
     * Validasi apakah token bisa mengakses repo tersebut.
     */
    public function validateAccess(string $url): bool
    {
        $info = $this->parseRepoUrl($url);

        // Cek endpoint repo detail
        $response = $this->request()->get("{$this->baseUrl}/repos/{$info['owner']}/{$info['repo']}");

        return $response->successful();
    }

    /**
     * Mendaftarkan Webhook agar Flux tahu kalau ada push baru.
     */
    public function registerWebhook(string $repoUrl): bool
    {
        $info = $this->parseRepoUrl($repoUrl);

        // URL Webhook Flux kita
        $webhookUrl = \config('app.url') . '/api/webhooks/gitea';

        // Secret key untuk validasi payload
        $secret = \config('services.gitea.webhook_secret');

        $response = $this->request()->post("{$this->baseUrl}/repos/{$info['owner']}/{$info['repo']}/hooks", [
            'type'      => 'gitea',
            'config'    => [
                'url'           => $webhookUrl,
                'content_type'  => 'json',
                'secret'        => $secret,
            ],
            'events'    => ['push'],
            'active'    => true,
        ]);

        return $response->successful();
    }
}
