<?php

declare(strict_types=1);

namespace App\Core;

use Dotenv\Dotenv;

class Config{
    public static function loadEnv(string $projectRoot): void
    {
        if (!empty($_ENV['APP_ENV'])) {
            return;
        }
        $dotenv = Dotenv::createImmutable($projectRoot);
        $dotenv->safeLoad();
    }
    public static function env(string $key, ?string $default = null): ?string
    {
        return $_ENV[$key] ?? $default;
    }
    public static function db(): array
    {
        return [
            'host' => self::env('DB_HOST', '127.0.0.1'),
            'port' => (int) self::env('DB_PORT', '3306'),
            'dbname'  => self::env('DB_NAME', 'simple_store_db'),
            'user'    => self::env('DB_USER', 'root'),
            'pass'    => self::env('DB_PASS', ''),
            'charset' => self::env('DB_CHARSET', 'utf8mb4'),
        ];
    }
}
