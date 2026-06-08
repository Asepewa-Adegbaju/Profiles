<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class User
{
    public static function findByEmail(string $email): ?array
    {
        $row = Database::getInstance()
            ->query('SELECT * FROM users WHERE email = ? LIMIT 1', [$email])
            ->fetch();

        return $row ?: null;
    }

    public static function findById(int $id): ?array
    {
        $row = Database::getInstance()
            ->query('SELECT id, name, email, created_at FROM users WHERE id = ? LIMIT 1', [$id])
            ->fetch();

        return $row ?: null;
    }

    public static function all(): array
    {
        return Database::getInstance()
            ->query('SELECT id, name, email, created_at FROM users ORDER BY name')
            ->fetchAll();
    }

    public static function create(string $name, string $email, string $password): int
    {
        $db   = Database::getInstance();
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);

        $db->query(
            'INSERT INTO users (name, email, password, created_at) VALUES (?, ?, ?, NOW())',
            [$name, $email, $hash]
        );

        return $db->lastInsertId();
    }

    public static function updatePassword(int $id, string $newPassword): void
    {
        $hash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);
        Database::getInstance()->query(
            'UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?',
            [$hash, $id]
        );
    }
}
