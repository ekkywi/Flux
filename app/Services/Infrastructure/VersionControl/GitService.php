<?php

namespace App\Services\Infrastructure\VersionControl;

use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Log;
use Exception;

class GitService
{
    public function getRemoteBranches(string $repositoryUrl): array
    {
        $gitPath = 'git';

        $authUrl = $this->injectTokenIfGitea($repositoryUrl);

        $command = "{$gitPath} ls-remote --heads {$authUrl}";

        Log::info("Running Git Command: " . $this->maskUrl($command));

        $result = Process::timeout(60)->run($command);

        if ($result->failed()) {
            $errorMsg = $result->errorOutput();
            if (empty($errorMsg)) $errorMsg = $result->output();

            Log::error("Git Error: " . $this->maskUrl($errorMsg));

            throw new Exception("Git Error: " . $this->maskUrl($errorMsg));
        }

        $output = $result->output();

        Log::info("Raw Git Output:\n" . $output);

        $branches = [];

        if (preg_match_all('/refs\/heads\/(.+)/', $output, $matches)) {
            foreach ($matches[1] as $branch) {
                $branches[] = trim($branch);
            }
        }

        if (empty($branches) && !empty($output)) {
            foreach (explode("\n", $output) as $line) {
                $line = trim($line);
                if (empty($line)) continue;

                $pos = strpos($line, 'refs/heads/');
                if ($pos !== false) {
                    $branches[] = substr($line, $pos + 11);
                }
            }
        }

        return $branches;
    }

    private function injectTokenIfGitea(string $url): string
    {
        $giteaBase = env('GITEA_BASE_URL');
        $token = env('GITEA_TOKEN');

        if (empty($giteaBase) || empty($token)) {
            return $url;
        }

        $repoHost = parse_url($url, PHP_URL_HOST);
        $giteaHost = parse_url($giteaBase, PHP_URL_HOST);

        if ($repoHost === $giteaHost) {
            $parts = parse_url($url);

            $scheme = $parts['scheme'] ?? 'http';
            $host = $parts['host'];
            $port = isset($parts['port']) ? ':' . $parts['port'] : '';
            $path = $parts['path'] ?? '';

            return "{$scheme}://{$token}@{$host}{$port}{$path}";
        }

        return $url;
    }

    private function maskUrl(string $text): string
    {
        $token = env('GITEA_TOKEN');
        if (empty($token))
            return $text;

        return str_replace($token, '*****', $text);
    }
}
