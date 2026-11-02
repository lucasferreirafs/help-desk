<?php
declare(strict_types=1);

namespace App\Core;

use App\Contracts\Middleware;
use App\Core\Http;

use InvalidArgumentException;
use RuntimeException;

final class Router
{
    private array $routes = [];

    public function get(
        string $path,
        array $action,
        array $middlewares = []
    ): void {
        $this->add(
            'GET',
            $path,
            $action,
            $middlewares
        );
    }

    public function post(
        string $path,
        array $action,
        array $middlewares = []
    ): void {
        $this->add(
            'POST',
            $path,
            $action,
            $middlewares
        );
    }

    public function add(
        string $method,
        string $path,
        array $action,
        array $middlewares = []
    ): void {
        if ($path === '' || $path[0] !== '/') {
            throw new InvalidArgumentException(
                'A rota deve começar com uma barra.'
            );
        }

        if (
            !array_is_list($action)
            || count($action) !== 2
            || !is_string($action[0])
            || !is_string($action[1])
        ) {
            throw new InvalidArgumentException(
                'A ação deve conter a classe e o método do controller'
            );
        }

        foreach ($middlewares as $middleware) {
            if (!$middleware instanceof Middleware) {
                throw new InvalidArgumentException(
                    'Middleware incompatível com o contrato da aplicação.'
                );
            }
        }

        if (isset($this->routes[$path][$method])) {
            throw new RuntimeException(
                "A rota {$method} {$path} já foi cadastrada."
            );
        }

        $this->routes[$path][$method] = [
            'action' => $action,
            'middlewares' => $middlewares,
        ];
    }

    public function dispatch(string $method, string $path): void
    {
        if (!isset($this->routes[$path])) {
            Http::error(404);
            return;
        }

        if (!isset($this->routes[$path][$method])) {
            header(
                'Allow: ' . implode(', ', array_keys($this->routes[$path]))
            );
            Http::error(405);
            return;
        }

        $route = $this->routes[$path][$method];

        $next = static function () use ($route): void {
            [$controllerClass, $controllerMethod] = $route['action'];

            if (!class_exists($controllerClass)) {
                throw new  RuntimeException('O controller da rota não foi encontrado.');
            }

            $controller = new $controllerClass();

            if (!is_callable([$controller, $controllerMethod])) {
                throw new RuntimeException('O método do controller não pode ser executado.');
            }

            $controller->$controllerMethod();
        };

        foreach (array_reverse($route['middlewares']) as $middleware) {
            $following = $next;

            $next = static function () use (
                $middleware,
                $following
            ): void {
                $middleware->handle($following);
            };
        }

        $next();
    }
}