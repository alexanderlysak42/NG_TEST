<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Database;
use PDO;

class GameResultRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function save(int $registrationId, int $number, string $result, float $amount): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO game_results (registration_id, number, result, amount, created_at)
             VALUES (:registration_id, :number, :result, :amount, :created_at)'
        );
        $stmt->execute([
            'registration_id' => $registrationId,
            'number' => $number,
            'result' => $result,
            'amount' => $amount,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function lastThree(int $registrationId): array
    {
        $stmt = $this->db->prepare(
            'SELECT number, result, amount, created_at FROM game_results
             WHERE registration_id = :registration_id
             ORDER BY id DESC LIMIT 3'
        );
        $stmt->execute(['registration_id' => $registrationId]);

        return $stmt->fetchAll();
    }
}