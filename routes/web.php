<?php

declare(strict_types=1);

use App\Controllers\{LandingPageController, AuthController, HomeController};

use App\Middleware\{AuthMiddleware, CsrfMiddleware};

$auth = new AuthMiddleware();
$csrf = new CsrfMiddleware();


$router->get('/', [LandingPageController::class, 'landingPage']);
$router->get('/login', [AuthController::class, 'loginForm']);
$router->post('/login', [AuthController::class, 'login'], [$csrf]);
$router->post('/logout', [AuthController::class, 'logout'], [$auth, $csrf]);
$router->get('/home', [HomeController::class, 'index'], [$auth]);