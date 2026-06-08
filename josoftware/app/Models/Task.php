<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Task
{
    public static function allByProject(int $projectId): array
    {
        return Database::getInstance()->query(
            'SELECT t.*,
                    u.name AS assigned_name
               FROM tasks t
               LEFT JOIN users u ON u.id = t.assigned_to
              WHERE t.project_id = ?
              ORDER BY
                FIELD(t.priority, \'urgent\', \'hoog\', \'normaal\', \'laag\'),
                t.created_at ASC',
            [$projectId]
        )->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $row = Database::getInstance()->query(
            'SELECT t.*,
                    u.name AS assigned_name
               FROM tasks t
               LEFT JOIN users u ON u.id = t.assigned_to
              WHERE t.id = ?
              LIMIT 1',
            [$id]
        )->fetch();

        return $row ?: null;
    }

    public static function create(array $data): int
    {
        $db = Database::getInstance();

        $db->query(
            'INSERT INTO tasks
                (project_id, assigned_to, title, description, status, priority, due_date, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, NOW())',
            [
                $data['project_id'],
                $data['assigned_to']  ?: null,
                $data['title'],
                $data['description']  ?? null,
                $data['status']       ?? 'te-doen',
                $data['priority']     ?? 'normaal',
                $data['due_date']     ?: null,
            ]
        );

        return $db->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $completedAt = null;
        if (($data['status'] ?? '') === 'klaar') {
            // Behoud bestaande completed_at als die al gezet was, anders NOW()
            $existing = self::find($id);
            $completedAt = $existing['completed_at'] ?? null;
            if ($completedAt === null) {
                $completedAt = date('Y-m-d H:i:s');
            }
        }

        Database::getInstance()->query(
            'UPDATE tasks
                SET assigned_to  = ?,
                    title        = ?,
                    description  = ?,
                    status       = ?,
                    priority     = ?,
                    due_date     = ?,
                    completed_at = ?
              WHERE id = ?',
            [
                $data['assigned_to']  ?: null,
                $data['title'],
                $data['description']  ?? null,
                $data['status']       ?? 'te-doen',
                $data['priority']     ?? 'normaal',
                $data['due_date']     ?: null,
                $completedAt,
                $id,
            ]
        );
    }

    public static function updateStatus(int $id, string $status): void
    {
        if ($status === 'klaar') {
            Database::getInstance()->query(
                'UPDATE tasks
                    SET status = ?, completed_at = NOW()
                  WHERE id = ?',
                [$status, $id]
            );
        } else {
            Database::getInstance()->query(
                'UPDATE tasks
                    SET status = ?, completed_at = NULL
                  WHERE id = ?',
                [$status, $id]
            );
        }
    }

    public static function delete(int $id): void
    {
        Database::getInstance()->query(
            'DELETE FROM tasks WHERE id = ?',
            [$id]
        );
    }

    public static function countByProjectAndStatus(int $projectId): array
    {
        $rows = Database::getInstance()->query(
            'SELECT status, COUNT(*) AS cnt
               FROM tasks
              WHERE project_id = ?
              GROUP BY status',
            [$projectId]
        )->fetchAll();

        $counts = [
            'te-doen' => 0,
            'bezig'   => 0,
            'review'  => 0,
            'klaar'   => 0,
        ];

        foreach ($rows as $row) {
            $counts[$row['status']] = (int) $row['cnt'];
        }

        return $counts;
    }

    public static function getUsers(): array
    {
        return Database::getInstance()->query(
            'SELECT id, name FROM users ORDER BY name'
        )->fetchAll();
    }
}
