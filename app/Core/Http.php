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
            'message' => 'Não foi possível interpretar a requisição.',
        ],
        401 => [
            'title' => 'Autenticação necessária',
            'message' => 'Entre no sistema com uma conta valida para acessar esta página.',
        ],
        403 => [
            'title' => 'Acesso não permitido',
            'message' => 'Você não tem permissão para realizar esta ação.',
        ],
        404 => [
            'title' => 'Página não encontrada',
            'message' => 'O endereço solicitado não foi encontrado.',
        ],
        405 => [
            'title' => 'Método não permitido',
            'message' => 'Este endereço não aceita o método utilizado.',
        ],
        500 => [
            'title' => 'Erro interno',
            'message' => 'Não foi possível concluir a solicitação. Tente novamente mais tarde.',
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