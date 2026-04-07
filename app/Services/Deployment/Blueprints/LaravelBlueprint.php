<?php

namespace App\Services\Deployment\Blueprints;

class LaravelBlueprint implements BlueprintInterface
{
  public function getDockerfile(array $options = []): string
  {
    $phpVersion = $options['php_version'] ?? '8.4';

    $installIoncube = !empty($options['install_ioncube']);

    $ioncubeScript = '';
    if ($installIoncube) {
      $ioncubeScript = <<<SCRIPT

RUN echo "Installing ionCube Loader for PHP {$phpVersion}..." \
    && curl -fSL "https://downloads.ioncube.com/loader_downloads/ioncube_loaders_lin_x86-64.tar.gz" -o ioncube.tar.gz \
    && tar -xzf ioncube.tar.gz \
    && EXT_DIR=\$(php-config --extension-dir) \
    && cp "ioncube/ioncube_loader_lin_{$phpVersion}.so" "\$EXT_DIR/" \
    && echo "zend_extension=\$EXT_DIR/ioncube_loader_lin_{$phpVersion}.so" > /usr/local/etc/php/conf.d/00-ioncube.ini \
    && rm -rf ioncube ioncube.tar.gz

SCRIPT;
    }

    return <<<EOF
FROM php:{$phpVersion}-cli

RUN apt-get update && apt-get install -y zip unzip git libpq-dev sqlite3 \
    && docker-php-ext-install pdo pdo_pgsql pdo_mysql
{$ioncubeScript}
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock* ./
RUN composer install --no-interaction --no-scripts --no-autoloader --prefer-dist

COPY . .

RUN composer dump-autoload --optimize --no-scripts
RUN chmod -R 777 storage bootstrap/cache database || true

EXPOSE 8000

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

volumes:
  app_storage:
  app_database:
EOF;
  }
}
