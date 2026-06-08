<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, string $controller, string $method): void
    {
        $this->routes['GET'][$path] = [$controller, $method];
    }

    public function post(string $path, string $controller, string $method): void
    {
        $this->routes['POST'][$path] = [$controller, $method];
    }

    public function dispatch(string $httpMethod, string $requestUri): void
    {
        $uri = parse_url($requestUri, PHP_URL_PATH);

        // Strip de subfolder-prefix zodat /josoftware/login → /login werkt
        $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        $uri  = '/' . ltrim(substr($uri, strlen($base)), '/');

        $routes = $this->routes[$httpMethod] ?? [];

        foreach ($routes as $pattern => [$controllerName, $action]) {
            // Vervang {param} door capture-groepen
            $regex = '#^' . preg_replace('/\{[^}]+\}/', '([^/]+)', $pattern) . '$#';

            if (preg_match($regex, $uri, $matches)) {
                array_shift($matches); // volledige match verwijderen

                $class = 'App\\Controllers\\' . $controllerName;
                if (!class_exists($class)) {
                    $this->abort(500, "Controller {$class} niet gevonden.");
                    return;
                }

                $controller = new $class();
                call_user_func_array([$controller, $action], $matches);
                return;
            }
        }

        $this->abort(404, 'Pagina niet gevonden.');
    }

    private function abort(int $code, string $message): void
    {
        http_response_code($code);
        echo '<h1>' . $code . '</h1><p>' . htmlspecialchars($message) . '</p>';
    }
}
