<?php

declare(strict_types=1);

namespace App;

use PDO;

class Database
{
    private static ?PDO $instance = null;

    public static function connection(): PDO
    {
        if (self::$instance === null) {
            $host = getenv('DB_HOST');
            $port = getenv('DB_PORT');
            $name = getenv('DB_NAME');
            $user = getenv('DB_USER');
            $password = getenv('DB_PASSWORD');

            $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";

            self::$instance = new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        }

        return self::$instance;
    }
}