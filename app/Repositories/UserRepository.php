<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;
use PDOException;
use RuntimeException;

final class UserRepository
{
    public function __construct(
        private readonly PDO $connection
    ) {
    }

    public function searchByEmail(string $email): ?array
    {
        try {
            $statement = $this->connection->prepare(
                'SELECT id, nome, email, password, perfil, ativo FROM public.users WHERE lower(email) = lower(:email)'
            );

            $statement->execute([
                'email' => trim($email),
            ]);

            $user = $statement->fetch(PDO::FETCH_ASSOC);

            return $user === false ? null : $user;
        } catch (PDOException $exception) {
            throw new RuntimeException(
                'Não foi possível encontrar o usuário.',
                0,
                $exception
            );
        }
    }
}