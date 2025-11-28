<?php

namespace App\Core;

class Router
{
    private array $routes = [
        'GET' => [],
        'POST' => [],
    ];

    //sign up get route 
    public function get(string $path, array $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, array $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(string $uri, string $method): void
    {
        $path = parse_url($uri, PHP_URL_PATH);
        if (isset($this->routes[$method][$path])) {
            //get handler of route 
            [$class, $action] = $this->routes[$method][$path];
            $controller = new $class();
            call_user_func([$controller, $action]);
            return;
        }

        //dynamic route 
        foreach ($this->routes[$method] as $route => $handler) {

            $pattern = preg_replace('#\{[a-zA-Z_]+\}#', '([0-9a-zA-Z-_]+)', $route);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $path, $matches)) {

                //get parameter 
                array_shift($matches);

                [$class, $action] = $handler;
                $controller = new $class();

                call_user_func_array([$controller, $action], $matches);
                return;
            }
        }
        http_response_code(404);
        echo "404 Not Found";
    }
}
