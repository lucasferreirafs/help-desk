<?php

declare(strict_types=1);

use App\Controllers\{LandingPageController, AuthController};

use App\Middleware\{AuthMiddleware, CsrfMiddleware};

$auth = new AuthMiddleware();
$csrf = new CsrfMiddleware();


$router->get('/', [LandingPageController::class, 'landingPage']);

// Autenticação
$router->get('/login',[AuthController::class, 'form']);

