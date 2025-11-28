<?php

declare(strict_types=1);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
require __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Core\Config;
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
Config::loadEnv(__DIR__ . '/../env');
$router = new Router();
require __DIR__ . '/../routes/web.php';
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
