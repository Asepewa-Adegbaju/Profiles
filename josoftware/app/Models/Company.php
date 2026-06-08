<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Company
{
    public static function all(string $status = ''): array
    {
        $db = Database::getInstance();

        if ($status !== '') {
            return $db->query(
                'SELECT c.*,
                        COUNT(ct.id) AS contact_count
                   FROM companies c
                   LEFT JOIN contacts ct ON ct.company_id = c.id
                  WHERE c.status = ?
                  GROUP BY c.id
                  ORDER BY c.name',
                [$status]
            )->fetchAll();
        }

        return $db->query(
            'SELECT c.*,
                    COUNT(ct.id) AS contact_count
               FROM companies c
               LEFT JOIN contacts ct ON ct.company_id = c.id
              GROUP BY c.id
              ORDER BY c.name'
        )->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $row = Database::getInstance()
            ->query('SELECT * FROM companies WHERE id = ? LIMIT 1', [$id])
            ->fetch();

        return $row ?: null;
    }

    public static function create(array $data): int
    {
        $db = Database::getInstance();

        $db->query(
            'INSERT INTO companies
                (name, sector, phone, email, website, address, city, postal_code, status, notes, created_by, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())',
            [
                $data['name'],
                $data['sector']      ?? null,
                $data['phone']       ?? null,
                $data['email']       ?? null,
                $data['website']     ?? null,
                $data['address']     ?? null,
                $data['city']        ?? null,
                $data['postal_code'] ?? null,
                $data['status']      ?? 'lead',
                $data['notes']       ?? null,
                $data['created_by']  ?? null,
            ]
        );

        return $db->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        Database::getInstance()->query(
            'UPDATE companies
                SET name        = ?,
                    sector      = ?,
                    phone       = ?,
                    email       = ?,
                    website     = ?,
                    address     = ?,
                    city        = ?,
                    postal_code = ?,
                    status      = ?,
                    notes       = ?,
                    updated_at  = NOW()
              WHERE id = ?',
            [
                $data['name'],
                $data['sector']      ?? null,
                $data['phone']       ?? null,
                $data['email']       ?? null,
                $data['website']     ?? null,
                $data['address']     ?? null,
                $data['city']        ?? null,
                $data['postal_code'] ?? null,
                $data['status']      ?? 'lead',
                $data['notes']       ?? null,
                $id,
            ]
        );
    }

    public static function delete(int $id): void
    {
        Database::getInstance()->query(
            'DELETE FROM companies WHERE id = ?',
            [$id]
        );
    }

    public static function search(string $q): array
    {
        $like = '%' . $q . '%';

        return Database::getInstance()->query(
            'SELECT c.*,
                    COUNT(ct.id) AS contact_count
               FROM companies c
               LEFT JOIN contacts ct ON ct.company_id = c.id
              WHERE c.name LIKE ?
                 OR c.city LIKE ?
                 OR c.sector LIKE ?
              GROUP BY c.id
              ORDER BY c.name',
            [$like, $like, $like]
        )->fetchAll();
    }

    public static function countByStatus(): array
    {
        $rows = Database::getInstance()->query(
            'SELECT status, COUNT(*) AS cnt FROM companies GROUP BY status'
        )->fetchAll();

        $counts = [
            'lead'     => 0,
            'prospect' => 0,
            'klant'    => 0,
            'inactief' => 0,
        ];

        foreach ($rows as $row) {
            $counts[$row['status']] = (int) $row['cnt'];
        }

        return $counts;
    }
}
