<?php

namespace App\Services\Deployment\Blueprints;

use Exception;

class DatabaseBlueprint
{
  public static function getCompose(string $type, string $dbName, string $dbUser, string $dbPass): string
  {
    $type = strtolower(trim($type));

    if ($type === 'mysql') {
      return "services:\n"
        . "  db:\n"
        . "    image: mysql:8.0\n"
        . "    restart: unless-stopped\n"
        . "    environment:\n"
        . "      MYSQL_DATABASE: '{$dbName}'\n"
        . "      MYSQL_USER: '{$dbUser}'\n"
        . "      MYSQL_PASSWORD: '{$dbPass}'\n"
        . "      MYSQL_ROOT_PASSWORD: '{$dbPass}_root'\n"
        . "    ports:\n"
        . "      - \"3306:3306\"\n"
        . "    volumes:\n"
        . "      - db_data:/var/lib/mysql\n"
        . "volumes:\n"
        . "  db_data:\n";
    }

    if ($type === 'pgsql') {
      return "services:\n"
        . "  db:\n"
        . "    image: postgres:15-alpine\n"
        . "    restart: unless-stopped\n"
        . "    environment:\n"
        . "      POSTGRES_DB: '{$dbName}'\n"
        . "      POSTGRES_USER: '{$dbUser}'\n"
        . "      POSTGRES_PASSWORD: '{$dbPass}'\n"
        . "    ports:\n"
        . "      - \"5432:5432\"\n"
        . "    volumes:\n"
        . "      - db_data:/var/lib/postgresql/data\n"
        . "volumes:\n"
        . "  db_data:\n";
    }

    throw new Exception("FATAL: Unsupported database type: '{$type}'");
  }
}
