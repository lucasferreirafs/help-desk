<?php

declare(strict_types=1);

namespace App\Core;

use InvalidArgumentException;
use Throwable;

final class Http
{
    private const array ERRORS = [
        400 => [
            'title' => 'Requisição Inválida',
        ],
        401 => [
            'title' => 'Autenticação necessária',
        ],
        403 => [
            'title' => 'Acesso negado',
        ],
        404 => [
            'title' => 'Página não encontrada',
        ],
        405 => [
            'title' => 'Método não permitido',
        ],
        500 => [
            'title' => 'Erro interno do servidor',
        ],
    ];

    public static function error(int $status): void
    {
        if (!isset(self::ERRORS[$status])) {
            throw new InvalidArgumentException(
                "Não existe um página configurada para o status {$status}."
            );
        }

        http_response_code($status);

        header('Content-Type: text/html; charset=UTF-8');

        $data = self::ERRORS[$status];
        $data['status'] = $status;

        try {
            View::render(
                'errors/' . $status,
                $data,
                'error'
            );
        } catch (Throwable $exception) {
            error_log((string) $exception);
            http_response_code(500);
            echo 'Não foi possível apresentar a página solicitada.';
        }
    }
}