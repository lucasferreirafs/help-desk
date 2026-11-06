<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\{View, Http};

final class HomeController
{
    public function index(): void
    {
        $perfil = $_SESSION['user']['perfil'] ?? null;

        $page = match ($perfil) {
            'admin' => 'admin/home',
            'colaborador' => 'colaborador/home',
            default => null,
        };

        if ($page === null) {
            Http::error(403);
            return;
        }

        View::render(
            $page,
            [
                'title' => 'Página incial',
                'userInfo' => [
                    'username' => $_SESSION['user']['nome'] ?? 'Usuário',
                    'role' => $_SESSION['user']['perfil'] ?? 'Não definido'
                ]
            ],
            'main'
        );
    }
}