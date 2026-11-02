<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Contracts\Middleware;
use App\Core\Http;

final class AuthMiddleware implements Middleware
{
    public function handle(callable $next): void
    {
        $user = $_SESSION['user'] ?? null;

        if (
            !is_array($user)
            || !isset($user['id'], $user['perfil'])
            || !in_array(
                $user['perfil'],
                ['admin', 'colaborador'],
                true
            )
        ) {
            Http::error(401);
            //Definir redirecionamento para /login
            return;
        }
        $next();
    }
}