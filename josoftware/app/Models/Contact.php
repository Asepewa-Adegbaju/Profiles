<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Contact
{
    public static function allByCompany(int $companyId): array
    {
        return Database::getInstance()->query(
            'SELECT * FROM contacts WHERE company_id = ? ORDER BY name',
            [$companyId]
        )->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $row = Database::getInstance()
            ->query('SELECT * FROM contacts WHERE id = ? LIMIT 1', [$id])
            ->fetch();

        return $row ?: null;
    }

    public static function create(array $data): int
    {
        $db = Database::getInstance();

        $db->query(
            'INSERT INTO contacts
                (company_id, name, function, email, phone, notes, created_at)
             VALUES (?, ?, ?, ?, ?, ?, NOW())',
            [
                $data['company_id'],
                $data['name'],
                $data['function'] ?? null,
                $data['email']    ?? null,
                $data['phone']    ?? null,
                $data['notes']    ?? null,
            ]
        );

        return $db->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        Database::getInstance()->query(
            'UPDATE contacts
                SET name     = ?,
                    function = ?,
                    email    = ?,
                    phone    = ?,
                    notes    = ?
              WHERE id = ?',
            [
                $data['name'],
                $data['function'] ?? null,
                $data['email']    ?? null,
                $data['phone']    ?? null,
                $data['notes']    ?? null,
                $id,
            ]
        );
    }

    public static function delete(int $id): void
    {
        Database::getInstance()->query(
            'DELETE FROM contacts WHERE id = ?',
            [$id]
        );
    }
}
