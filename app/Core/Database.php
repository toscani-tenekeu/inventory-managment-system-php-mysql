<?php

declare(strict_types=1);

namespace IMS\Core;

use PDO;
use PDOException;
use RuntimeException;

final class Database
{
    public static function connect(array $config): PDO
    {
        $host = (string) ($config['host'] ?? '127.0.0.1');
        $port = (int) ($config['port'] ?? 3306);
        $name = (string) ($config['name'] ?? '');
        $user = (string) ($config['user'] ?? '');
        $pass = (string) ($config['pass'] ?? '');

        if ($name === '' || $user === '') {
            throw new RuntimeException('Database configuration is incomplete.');
        }

        try {
            $pdo = new PDO(
                "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4",
                $user,
                $pass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_STRINGIFY_FETCHES => false,
                ]
            );
            $pdo->exec('SET time_zone = ' . $pdo->quote(date('P')));
            return $pdo;
        } catch (PDOException $exception) {
            throw new RuntimeException('Unable to connect to the database.', 0, $exception);
        }
    }
}
