<?php

namespace App\Services\Deployment\Blueprints;

class LaravelBlueprint implements BlueprintInterface
{
    public function getDockerfile(array $options = []): string
    {
        $phpVersion = $options['php_version'] ?? '8.4';

        return <<<EOF
FROM php:{$phpVersion}-cli

# Install system dependencies
RUN apt-get update && apt-get install -y zip unzip git libpq-dev sqlite3 \
    && docker-php-ext-install pdo pdo_pgsql pdo_mysql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Cache dependencies (Layer caching for faster builds)
COPY composer.json composer.lock* ./
RUN composer install --no-interaction --no-scripts --no-autoloader --prefer-dist

# Copy full source code
COPY . .

# Finalize setup
RUN composer dump-autoload --optimize --no-scripts
RUN chmod -R 777 storage bootstrap/cache database || true

EXPOSE 8000

# CMD sangat bersih, tidak ada generate key yang berisiko menimpa data
CMD php artisan serve --host=0.0.0.0 --port=8000
EOF;
    }

    public function getDockerCompose(array $options = []): string
    {
        $port = $options['port'] ?? 8000;

        return <<<EOF
services:
  app:
    build: .
    restart: unless-stopped
    ports:
      - "{$port}:8000"
    volumes:
      - app_storage:/app/storage
      - app_database:/app/database

# Named Volumes agar file upload (storage) dan file SQLite (database) abadi
volumes:
  app_storage:
  app_database:
EOF;
    }
}
