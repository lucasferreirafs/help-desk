<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Contracts\Middleware;
use App\Core\Http;


final class CsrfMiddleware implements Middleware
{
    public function handle(callable $next): void
    {
        $expected = $_SESSION['csrf_token'] ?? null;
        $received = $_POST['_token'] ?? null;

        if (
            !is_string($expected)
            || $expected === ''
            || !is_string($received)
            || !hash_equals($expected, $received)
        ) {
            Http::error(403);
            return;
        }
        $next();
    }
}