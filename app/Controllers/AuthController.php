<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;

final class AuthController
{
    public function form(): void
    {
        View::render(
            'auth/login',
            [
                'title' => 'Acessar conta'
            ],
            'auth'
        );
    }
}