<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Repositories\UserRepository;
use App\Database\Connection;

use ErrorException;
use RuntimeException;

final class AuthController
{
    public function loginForm(): void
    {
        View::render(
            'auth/login',
            [
                'title' => 'Acessar conta'
            ],
            'auth'
        );
    }

    public function login(): void {
        $email = $_POST['email'] ?? null;
        $password = $_POST['password'] ?? null;

        if (
            !is_string($email)
            || !is_string($password)
            || filter_var(trim($email), FILTER_VALIDATE_EMAIL) === false
            || $password === ''
        ) {
            $this->reject(
                'Informe um e-mail válido e sua senha.',
                422
            );
            return;
        }

        $repository = new UserRepository(
            Connection::connect()
        );

        $user = $repository->searchByEmail(trim($email));

        if ($user === null) {
            $this->reject('E-mail ou senha inválidos.');
            return;
        }

        $passwordIsValid = password_verify($password, $user['password']);

        $isActive = filter_var(
            $user['ativo'],
            FILTER_VALIDATE_BOOL
        );

        $profileIsValid = in_array(
            $user['perfil'],
            ['admin', 'colaborador'],
            true
        );

        if (!$passwordIsValid || !$isActive || !$profileIsValid) {
            $this->reject('E-mail ou senha inválidos.');
            return;
        }

        if (!session_regenerate_id(true)) {
            throw new RuntimeException('Não foi possível renovar a sessão');
        }

        $csrfToken = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $csrfToken;

        $_SESSION['user'] = [
            'id' => (string) $user['id'],
            'nome' => $user['nome'],
            'perfil' => $user['perfil']
        ];

        header('Location: /home', true, 303);
    }

    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();

            setcookie(session_name(), '', [
                'expires' => time() - 3600,
                'path' => $params['path'],
                'domain' => $params['domain'],
                'secure' => $params['secure'],
                'httponly' => $params['httponly'],
                'samesite' => $params['samesite'],
            ]);
        }

        if (!session_destroy()) {
            throw new RuntimeException('Não foi possível encerrar a sessão.');
        }

        header('Location: /login', true, 303);

    }

    private function reject(
        string $message,
        int $status = 401
    ): void {
        http_response_code($status);
        $email = $_POST['email'] ?? '';
        View::render(
            'auth/login',
            [
                'title' => 'Acessar conta',
                'error' => $message,
                'old' => [
                    'email' => is_string($email) ? $email : '',
                ],
            ],
            'auth'
        );
    }
}