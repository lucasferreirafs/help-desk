<?php

declare(strict_types=1);

namespace App\Database;

use PDO;
use PDOException;
use RuntimeException;

final class Connection
{
    private static ?PDO $connetion = null;

    public static function connect(): PDO
    {
        if (self::$connetion instanceof PDO) return self::$connetion;

        $host = self::checkVariable('DB_HOST');
        $port = self::checkVariable('DB_PORT');
        $database = self::checkVariable('DB_DATABASE');
        $username = self::checkVariable('DB_USERNAME');
        $password = self::checkVariable('DB_PASSWORD');
        $ssl = self::checkVariable("DB_SSLMODE");

        $validPort = filter_var(
            $port,
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => 1,
                    'max_range' => 65535,
                ],
            ]
        );

        if($validPort === false) {
            throw new RuntimeException(
                "DB_PORT deve ser uma porta válida."
            );
        }

        foreach([$host, $database] as $value) {
            if(preg_match('/[;\x00-\x1F]/', $value) === 1) {
                throw new RuntimeException(
                    "DB_HOST ou DB_DATABASE contém caracteres inválidos."
                );
            }
        }

        $dsn = sprintf(
            'pgsql:host=%s;port=%s;dbname=%s;sslmode=%s;connect_timeout=5',
            $host,
            $port,
            $database,
            $ssl
        );

        try {
            $pdo = new PDO(
                $dsn,
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_PERSISTENT => false,
                ]
            );

            $statament = $pdo->prepare(
                "SELECT set_config('TimeZone', :timezone, false)"
            );

            $statament->execute([
                'timezone' => 'America/Sao_Paulo',
            ]);

            self::$connetion = $pdo;

            return $pdo;

        } catch (PDOException $exception) {
            error_log(
                '[Database] Falha na conexão ou configuralção. Código:'
                . $exception->getCode()
            );

            throw new RuntimeException(
                'Não foi possível acessar o banco de dados.',
                0,
                $exception
            );
        }
    }

    private static function checkVariable(string $key): string
    {
        $value = getenv($key);

        if($value === false || trim($value) === '') {
            throw new RuntimeException(
                "A variável de ambiente {$key} não foi configurada."
            );
        }

        return $value;
    }
}