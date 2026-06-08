<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Project
{
    public static function all(string $status = ''): array
    {
        $db = Database::getInstance();

        if ($status !== '') {
            return $db->query(
                'SELECT p.*,
                        c.name AS company_name,
                        COUNT(t.id) AS task_count,
                        SUM(CASE WHEN t.status = \'klaar\' THEN 1 ELSE 0 END) AS tasks_done
                   FROM projects p
                   LEFT JOIN companies c ON c.id = p.company_id
                   LEFT JOIN tasks t ON t.project_id = p.id
                  WHERE p.status = ?
                  GROUP BY p.id
                  ORDER BY p.created_at DESC',
                [$status]
            )->fetchAll();
        }

        return $db->query(
            'SELECT p.*,
                    c.name AS company_name,
                    COUNT(t.id) AS task_count,
                    SUM(CASE WHEN t.status = \'klaar\' THEN 1 ELSE 0 END) AS tasks_done
               FROM projects p
               LEFT JOIN companies c ON c.id = p.company_id
               LEFT JOIN tasks t ON t.project_id = p.id
              GROUP BY p.id
              ORDER BY p.created_at DESC'
        )->fetchAll();
    }

    public static function active(): array
    {
        return Database::getInstance()->query(
            'SELECT p.*,
                    c.name AS company_name,
                    COUNT(t.id) AS task_count,
                    SUM(CASE WHEN t.status = \'klaar\' THEN 1 ELSE 0 END) AS tasks_done
               FROM projects p
               LEFT JOIN companies c ON c.id = p.company_id
               LEFT JOIN tasks t ON t.project_id = p.id
              WHERE p.status = \'actief\'
              GROUP BY p.id
              ORDER BY p.created_at DESC'
        )->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $row = Database::getInstance()->query(
            'SELECT p.*,
                    c.name AS company_name,
                    u.name AS created_by_name
               FROM projects p
               LEFT JOIN companies c ON c.id = p.company_id
               LEFT JOIN users u ON u.id = p.created_by
              WHERE p.id = ?
              LIMIT 1',
            [$id]
        )->fetch();

        return $row ?: null;
    }

    public static function create(array $data): int
    {
        $db = Database::getInstance();

        $db->query(
            'INSERT INTO projects
                (company_id, name, description, status, start_date, end_date, budget, created_by, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())',
            [
                $data['company_id']  ?? null,
                $data['name'],
                $data['description'] ?? null,
                $data['status']      ?? 'actief',
                $data['start_date']  ?: null,
                $data['end_date']    ?: null,
                $data['budget']      ?: null,
                $data['created_by']  ?? null,
            ]
        );

        return $db->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        Database::getInstance()->query(
            'UPDATE projects
                SET company_id  = ?,
                    name        = ?,
                    description = ?,
                    status      = ?,
                    start_date  = ?,
                    end_date    = ?,
                    budget      = ?,
                    updated_at  = NOW()
              WHERE id = ?',
            [
                $data['company_id']  ?? null,
                $data['name'],
                $data['description'] ?? null,
                $data['status']      ?? 'actief',
                $data['start_date']  ?: null,
                $data['end_date']    ?: null,
                $data['budget']      ?: null,
                $id,
            ]
        );
    }

    public static function delete(int $id): void
    {
        // tasks worden verwijderd via ON DELETE CASCADE
        Database::getInstance()->query(
            'DELETE FROM projects WHERE id = ?',
            [$id]
        );
    }

    public static function countByStatus(): array
    {
        $rows = Database::getInstance()->query(
            'SELECT status, COUNT(*) AS cnt FROM projects GROUP BY status'
        )->fetchAll();

        $counts = [
            'actief'      => 0,
            'on-hold'     => 0,
            'afgerond'    => 0,
            'geannuleerd' => 0,
        ];

        foreach ($rows as $row) {
            $counts[$row['status']] = (int) $row['cnt'];
        }

        return $counts;
    }

    public static function getCompanies(): array
    {
        return Database::getInstance()->query(
            'SELECT id, name FROM companies ORDER BY name'
        )->fetchAll();
    }
}
