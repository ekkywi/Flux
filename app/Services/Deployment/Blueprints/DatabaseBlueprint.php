<?php

namespace App\Services\Deployment\Blueprints;

use Exception;

class DatabaseBlueprint
{
  public static function getCompose(string $type, string $dbName, string $dbUser, string $dbPass, ?string $version = null, ?int $port = null): string
  {
    $type = strtolower(trim($type));

    if ($type === 'mysql') {
      $ver = $version ?: '8.0';
      $extPort = $port ?: 3306;

      return "services:\n"
        . "  db:\n"
        . "    image: mysql:{$ver}\n"
        . "    restart: unless-stopped\n"
        . "    environment:\n"
        . "      - MYSQL_DATABASE={$dbName}\n"
        . "      - MYSQL_USER={$dbUser}\n"
        . "      - MYSQL_PASSWORD={$dbPass}\n"
        . "      - MYSQL_ROOT_PASSWORD={$dbPass}_root\n"
        . "    ports:\n"
        . "      - \"{$extPort}:3306\"\n"
        . "    volumes:\n"
        . "      - db_data:/var/lib/mysql\n"
        . "volumes:\n"
        . "  db_data:\n";
    }

    if ($type === 'pgsql' || $type === 'postgresql') {
      $ver = $version ?: '15-alpine';
      $extPort = $port ?: 5432;

      return "services:\n"
        . "  db:\n"
        . "    image: postgres:{$ver}\n"
        . "    restart: unless-stopped\n"
        . "    environment:\n"
        . "      - POSTGRES_DB={$dbName}\n"
        . "      - POSTGRES_USER={$dbUser}\n"
        . "      - POSTGRES_PASSWORD={$dbPass}\n"
        . "    ports:\n"
        . "      - \"{$extPort}:5432\"\n"
        . "    volumes:\n"
        . "      - db_data:/var/lib/postgresql/data\n"
        . "volumes:\n"
        . "  db_data:\n";
    }

    if ($type === 'mariadb') {
      $ver = $version ?: '10.11';
      $extPort = $port ?: 3306;

      return "services:\n"
        . "  db:\n"
        . "    image: mariadb:{$ver}\n"
        . "    restart: unless-stopped\n"
        . "    environment:\n"
        . "      - MARIADB_DATABASE={$dbName}\n"
        . "      - MARIADB_USER={$dbUser}\n"
        . "      - MARIADB_PASSWORD={$dbPass}\n"
        . "      - MARIADB_ROOT_PASSWORD={$dbPass}_root\n"
        . "    ports:\n"
        . "      - \"{$extPort}:3306\"\n"
        . "    volumes:\n"
        . "      - db_data:/var/lib/mysql\n"
        . "volumes:\n"
        . "  db_data:\n";
    }

    throw new Exception("FATAL: Unsupported database type: '{$type}'");
  }
}
