<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Database;
use PDO;
use Random\RandomException;

class RegistrationRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    /**
     * @throws RandomException
     */
    public function create(string $username, string $phoneNumber): array
    {
        $token = bin2hex(random_bytes(32));
        $createdAt = date('Y-m-d H:i:s');
        $expiresAt = date('Y-m-d H:i:s', strtotime('+7 days'));

        $stmt = $this->db->prepare(
            'INSERT INTO registrations (username, phone_number, token, is_active, created_at, expires_at)
             VALUES (:username, :phone_number, :token, 1, :created_at, :expires_at)'
        );
        $stmt->execute([
            'username' => $username,
            'phone_number' => $phoneNumber,
            'token' => $token,
            'created_at' => $createdAt,
            'expires_at' => $expiresAt,
        ]);

        return $this->findByToken($token);
    }

    public function findByToken(string $token): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM registrations WHERE token = :token');
        $stmt->execute(['token' => $token]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    /**
     * @throws RandomException
     */
    public function regenerateToken(int $id): string
    {
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+7 days'));

        $stmt = $this->db->prepare(
            'UPDATE registrations SET token = :token, expires_at = :expires_at WHERE id = :id'
        );
        $stmt->execute([
            'token' => $token,
            'expires_at' => $expiresAt,
            'id' => $id,
        ]);

        return $token;
    }

    public function deactivate(int $id): void
    {
        $stmt = $this->db->prepare('UPDATE registrations SET is_active = 0 WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function isValid(array $registration): bool
    {
        if (!(bool) $registration['is_active']) {
            return false;
        }

        return strtotime($registration['expires_at']) > time();
    }
}