<?php
declare(strict_types=1);
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\{Router, Http};

try {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        if(!session_start([
            'use_strict_mode' => true,
            'cookie_httponly' => true,
            'cookie_samesite' => 'Lax',
        ])) {
            throw new RuntimeException(
                'Não foi possível iniciar a sessão.'
            );
        }
    }

    if (
        !isset($_SESSION['csrf_token'])
        || !is_string($_SESSION['csrf_token'])
        || $_SESSION['csrf_token'] === ''
    ) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    $router = new Router();
    require __DIR__ . '/../routes/web.php';

    $path = parse_url(
        $_SERVER['REQUEST_URI'] ?? '/',
        PHP_URL_PATH
    );

    if (!is_string($path) || $path === '') {
        Http::error(400);
        return;
    }

    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    header('Content-Type: text/html; charset=UTF-8');

    $router->dispatch($method, $path);

} catch (Throwable $exception) {
    error_log(
        (string) $exception
    );
    Http::error(500);
}
