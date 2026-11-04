<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;

final class LandingPageController
{
    public function landingPage(): void
    {
        View::render(
            'landingpage',
            [
                'title' => 'Bem-vindo ao Help Desk',
            ],
            'index'
        );
    }
}