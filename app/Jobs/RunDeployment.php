<?php

namespace App\Jobs;

use App\Models\Deployment;
use App\Models\SystemSetting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use phpseclib3\Net\SSH2;
use phpseclib3\Crypt\RSA;
use Throwable;

class RunDeployment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600;

    public function __construct(
        public Deployment $deployment
    ) {}

    public function handle(): void
    {
        $this->deployment->update(['status' => 'running']);

        try {
            $environment = $this->deployment->environment;
            $project = $environment->project;
            $server = $environment->server;

            $masterKey = SystemSetting::where('key_name', 'master_ssh_key')->first();
            if (!$masterKey) {
                throw new \Exception('Master SSH Key not found in system settings.');
            }

            $privateKey = RSA::load($masterKey->private_key);
            $ssh = new SSH2($server->ip_address, $server->ssh_port, 10);

            if (!$ssh->login($server->ssh_user, $privateKey)) {
                throw new \Exception('SSH Handshake/Login failed.');
            }

            $ssh->setTimeout(0);

            $workspace = "~/flux-projects/{$project->id}/{$environment->name}";
            $branch = escapeshellarg($environment->branch);

            $rawRepoUrl = $project->repository_url;
            $gitToken = env('GITEA_TOKEN');

            if ($gitToken && str_starts_with($rawRepoUrl, 'http')) {
                $parsed = parse_url($rawRepoUrl);
                $scheme = $parsed['scheme'] ?? 'http';
                $host = $parsed['host'] ?? '';
                $port = isset($parsed['port']) ? ':' . $parsed['port'] : '';
                $path = $parsed['path'] ?? '';
                $authenticatedUrl = "{$scheme}://{$gitToken}@{$host}{$port}{$path}";
            } else {
                $authenticatedUrl = $rawRepoUrl;
            }

            $repoUrl = escapeshellarg($authenticatedUrl);
            $stack = strtolower($project->stack ?? 'laravel');

            if ($stack === 'laravel' || $stack === 'php') {
                $dockerfile = <<<EOF
FROM php:8.4-cli

RUN apt-get update && apt-get install -y zip unzip git libpq-dev \\
    && docker-php-ext-install pdo pdo_pgsql pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Step 1: Install dependencies (Layer Caching)
COPY composer.json composer.lock* ./
RUN composer install --no-interaction --no-scripts --no-autoloader --prefer-dist

# Step 2: Copy Source Code
COPY . .

# Step 3: Finalize Composer & Permissions
RUN composer dump-autoload --optimize --no-scripts
RUN chmod -R 777 storage bootstrap/cache || true

EXPOSE 8000

# Step 4: Bootstrapping Runtime
CMD if [ ! -f .env ]; then cp .env.example .env; fi && \\
    php artisan key:generate --force && \\
    php artisan serve --host=0.0.0.0 --port=8000
EOF;

                $compose = <<<EOF
services:
  app:
    build: .
    container_name: development-app-1
    restart: always
    ports:
      - "8000:8000"
EOF;
            } elseif ($stack === 'nodejs' || $stack === 'node') {
                $dockerfile = <<<EOF
FROM node:18-alpine
WORKDIR /app
COPY package*.json ./
RUN npm install || true
COPY . .
EXPOSE 3000
CMD ["npm", "start"]
EOF;

                $compose = <<<EOF
services:
  app:
    build: .
    restart: always
    ports:
      - "3000:3000"
EOF;
            } else {
                $dockerfile = "FROM nginx:alpine\nCOPY . /usr/share/nginx/html\nEXPOSE 80";
                $compose = "services:\n  web:\n    build: .\n    restart: always\n    ports:\n      - \"80:80\"";
            }

            $b64Dockerfile = base64_encode($dockerfile);
            $b64Compose = base64_encode($compose);

            $commands = [
                "mkdir -p {$workspace}",
                "cd {$workspace}",
                "if [ ! -d .git ]; then git clone {$repoUrl} . ; else git remote set-url origin {$repoUrl} && git fetch --all && git reset --hard origin/{$branch}; fi",
                "git checkout {$branch}",
                "git pull origin {$branch}",

                "echo 'Generating PaaS Docker Configuration (Stack: {$stack})...'",
                "echo '{$b64Dockerfile}' | base64 -d > Dockerfile",
                "echo '{$b64Compose}' | base64 -d > docker-compose.yml",

                "echo 'Starting Docker Build & Up...'",
                "docker compose up -d --build 2>&1"
            ];

            $fullCommand = implode(' && ', $commands);

            $ssh->exec($fullCommand, function ($outputString) {
                $lines = explode("\n", trim($outputString));
                foreach ($lines as $line) {
                    if (trim($line) !== '') {
                        $this->deployment->logs()->create([
                            'output'    => $line,
                            'type'      => 'info'
                        ]);
                    }
                }
            });

            $status = ($ssh->getExitStatus() === 0) ? 'completed' : 'failed';
            $error = ($status === 'failed') ? "Exit code: " . $ssh->getExitStatus() : null;

            $this->deployment->update([
                'status' => $status,
                'error_message' => $error
            ]);

            $ssh->disconnect();
        } catch (Throwable $e) {
            $this->deployment->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage()
            ]);

            $this->deployment->logs()->create([
                'output'    => 'CRITICAL SYSTEM ERROR: ' . $e->getMessage(),
                'type'      => 'error'
            ]);
        }
    }
}
