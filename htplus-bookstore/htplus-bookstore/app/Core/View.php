<?php

declare(strict_types=1);

namespace App\Core;

class View
{
    //share layouts
    private static string $layoutPath = __DIR__ . '/../Views/layouts/';
    private static string $viewPath = __DIR__ . '/../Views/';

    public static function render(string $view, array $data = [], ?string $layout = 'main'): void
    {
        extract($data);
        
        //output buffering 
        ob_start();
        
        //about.index -> about/index.php
        $viewFile = self::$viewPath . str_replace('.', '/', $view) . '.php';
        
        if (!file_exists($viewFile)) {
            throw new \Exception("View file not found: {$viewFile}");
        }
        
        include $viewFile;
        
        $content = ob_get_clean();
    
        if ($layout !== null) {
            $layoutFile = self::$layoutPath . $layout . '.php';
            
            if (!file_exists($layoutFile)) {
                throw new \Exception("Layout file not found: {$layoutFile}");
            }
            
            include $layoutFile;
        } else {
            echo $content;
        }
    }
    
    //render navabar 
    public static function partial(string $view, array $data = []): void
    {
        self::render($view, $data, null);
    }
    
    public static function component(string $component, array $data = []): void
    {
        extract($data);
        $componentFile = self::$viewPath . 'components/' . $component . '.php';
        
        if (file_exists($componentFile)) {
            include $componentFile;
        }
    }
    
    //prevent XSS
    public static function e($value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
    
    //format money 
    public static function currency($amount): string
    {
        return number_format((float)$amount, 0, ',', '.') . '₫';
    }
    
    public static function date($date, $format = 'd/m/Y H:i'): string
    {
        if (is_string($date)) {
            $date = strtotime($date);
        }
        return date($format, $date);
    }
    
    public static function asset(string $path): string
    {
        return '/assets/' . ltrim($path, '/');
    }
    
    //url('book') => http://localhost/book
    public static function url(string $path = ''): string
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        return $protocol . '://' . $host . '/' . ltrim($path, '/');
    }
    

    public static function csrf(): string
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    public static function isActive(string $route): bool
    {
        return strpos($_SERVER['REQUEST_URI'], $route) !== false;
    }
}

