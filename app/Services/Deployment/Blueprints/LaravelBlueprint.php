<?php

namespace App\Services\Deployment\Blueprints;

class LaravelBlueprint implements BlueprintInterface
{
    public function getDockerfile(array $options = []): string
    {
        $phpVersion = $options['php_version'] ?? '8.4';

        return <<<EOF
FROM php:{$phpVersion}-cli

RUN apt-get update && apt-get install -y zip unzip git libpq-dev \\
    && docker-php-ext-install pdo pdo_pgsql pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Cache dependencies
COPY composer.json composer.lock* ./
RUN composer install --no-interaction --no-scripts --no-autoloader --prefer-dist

# Copy source
COPY . .

# Finalize setup
RUN composer dump-autoload --optimize --no-scripts
RUN chmod -R 777 storage bootstrap/cache || true

EXPOSE 8000

CMD if [ ! -f .env ]; then cp .env.example .env; fi && \\
    php artisan key:generate --force && \\
    php artisan serve --host=0.0.0.0 --port=8000
EOF;
    }

    public function getDockerCompose(array $options = []): string
    {
        $port = $options['port'] ?? 8000;

        return <<<EOF
services:
  app:
    build: .
    restart: always
    ports:
      - "{$port}:8000"
EOF;
    }
}
