<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Meeting
{
    public static function upcoming(int $limit = 10): array
    {
        return Database::getInstance()->query(
            'SELECT m.*,
                    c.name  AS company_name,
                    ct.name AS contact_name,
                    u.name  AS user_name
               FROM meetings m
               LEFT JOIN companies c  ON c.id  = m.company_id
               LEFT JOIN contacts  ct ON ct.id = m.contact_id
               LEFT JOIN users     u  ON u.id  = m.user_id
              WHERE m.meeting_date >= NOW()
                AND m.status NOT IN (\'geweest\', \'afgeslagen\')
              ORDER BY m.meeting_date ASC
              LIMIT ' . $limit
        )->fetchAll();
    }

    public static function allByCompany(int $companyId): array
    {
        return Database::getInstance()->query(
            'SELECT m.*,
                    ct.name AS contact_name,
                    u.name  AS user_name
               FROM meetings m
               LEFT JOIN contacts ct ON ct.id = m.contact_id
               LEFT JOIN users    u  ON u.id  = m.user_id
              WHERE m.company_id = ?
              ORDER BY m.meeting_date DESC',
            [$companyId]
        )->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $row = Database::getInstance()
            ->query('SELECT * FROM meetings WHERE id = ? LIMIT 1', [$id])
            ->fetch();

        return $row ?: null;
    }

    public static function create(array $data): int
    {
        $db = Database::getInstance();

        $db->query(
            'INSERT INTO meetings
                (company_id, contact_id, user_id, title, meeting_date, location, status, notes, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())',
            [
                $data['company_id'],
                $data['contact_id']   ?? null,
                $data['user_id']      ?? null,
                $data['title'],
                $data['meeting_date'],
                $data['location']     ?? null,
                $data['status']       ?? 'gepland',
                $data['notes']        ?? null,
            ]
        );

        return $db->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        Database::getInstance()->query(
            'UPDATE meetings
                SET title        = ?,
                    meeting_date = ?,
                    location     = ?,
                    status       = ?,
                    notes        = ?,
                    contact_id   = ?,
                    user_id      = ?
              WHERE id = ?',
            [
                $data['title'],
                $data['meeting_date'],
                $data['location']   ?? null,
                $data['status']     ?? 'gepland',
                $data['notes']      ?? null,
                $data['contact_id'] ?? null,
                $data['user_id']    ?? null,
                $id,
            ]
        );
    }

    public static function updateStatus(int $id, string $status): void
    {
        Database::getInstance()->query(
            'UPDATE meetings SET status = ? WHERE id = ?',
            [$status, $id]
        );
    }
}
