<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class ContactLog
{
    public static function allByCompany(int $companyId, int $limit = 50): array
    {
        return Database::getInstance()->query(
            'SELECT cl.*,
                    u.name  AS user_name,
                    ct.name AS contact_name
               FROM contact_log cl
               LEFT JOIN users    u  ON u.id  = cl.user_id
               LEFT JOIN contacts ct ON ct.id = cl.contact_id
              WHERE cl.company_id = ?
              ORDER BY cl.logged_at DESC
              LIMIT ' . $limit,
            [$companyId]
        )->fetchAll();
    }

    public static function create(array $data): int
    {
        $db = Database::getInstance();

        $db->query(
            'INSERT INTO contact_log
                (company_id, contact_id, user_id, type, notes, logged_at, created_at)
             VALUES (?, ?, ?, ?, ?, ?, NOW())',
            [
                $data['company_id'],
                $data['contact_id'] ?? null,
                $data['user_id']    ?? null,
                $data['type'],
                $data['notes'],
                $data['logged_at'],
            ]
        );

        return $db->lastInsertId();
    }
}
