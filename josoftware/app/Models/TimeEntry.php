<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class TimeEntry
{
    /**
     * All entries for a specific user, optionally filtered by month (Y-m).
     */
    public static function allByUser(int $userId, ?string $month = null): array
    {
        $db = Database::getInstance();

        $sql = 'SELECT te.*,
                       p.name  AS project_name,
                       c.name  AS company_name
                  FROM time_entries te
                  LEFT JOIN projects  p ON p.id = te.project_id
                  LEFT JOIN companies c ON c.id = te.company_id
                 WHERE te.user_id = ?';

        $params = [$userId];

        if ($month !== null && preg_match('/^\d{4}-\d{2}$/', $month)) {
            $sql      .= ' AND DATE_FORMAT(te.entry_date, \'%Y-%m\') = ?';
            $params[]  = $month;
        }

        $sql .= ' ORDER BY te.entry_date DESC, te.id DESC';

        return $db->query($sql, $params)->fetchAll();
    }

    /**
     * All entries for all users, optionally filtered by month.
     */
    public static function all(?string $month = null): array
    {
        $db = Database::getInstance();

        $sql = 'SELECT te.*,
                       u.name  AS user_name,
                       p.name  AS project_name,
                       c.name  AS company_name
                  FROM time_entries te
                  LEFT JOIN users     u ON u.id = te.user_id
                  LEFT JOIN projects  p ON p.id = te.project_id
                  LEFT JOIN companies c ON c.id = te.company_id';

        $params = [];

        if ($month !== null && preg_match('/^\d{4}-\d{2}$/', $month)) {
            $sql      .= ' WHERE DATE_FORMAT(te.entry_date, \'%Y-%m\') = ?';
            $params[]  = $month;
        }

        $sql .= ' ORDER BY te.entry_date DESC, te.id DESC';

        return $db->query($sql, $params)->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $row = Database::getInstance()
            ->query(
                'SELECT te.*,
                        p.name AS project_name,
                        c.name AS company_name
                   FROM time_entries te
                   LEFT JOIN projects  p ON p.id = te.project_id
                   LEFT JOIN companies c ON c.id = te.company_id
                  WHERE te.id = ?
                  LIMIT 1',
                [$id]
            )
            ->fetch();

        return $row ?: null;
    }

    public static function create(array $data): int
    {
        $db = Database::getInstance();

        $db->query(
            'INSERT INTO time_entries
                (user_id, project_id, company_id, entry_date, description, hours, hourly_rate, type, billable, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())',
            [
                $data['user_id'],
                $data['project_id'] ?: null,
                $data['company_id'] ?: null,
                $data['entry_date'],
                $data['description'],
                $data['hours'],
                $data['hourly_rate'] ?? 0.00,
                $data['type']        ?? 'zakelijk',
                isset($data['billable']) ? (int) $data['billable'] : 1,
            ]
        );

        return $db->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        Database::getInstance()->query(
            'UPDATE time_entries
                SET project_id  = ?,
                    company_id  = ?,
                    entry_date  = ?,
                    description = ?,
                    hours       = ?,
                    hourly_rate = ?,
                    type        = ?,
                    billable    = ?
              WHERE id = ?',
            [
                $data['project_id'] ?: null,
                $data['company_id'] ?: null,
                $data['entry_date'],
                $data['description'],
                $data['hours'],
                $data['hourly_rate'] ?? 0.00,
                $data['type']        ?? 'zakelijk',
                isset($data['billable']) ? (int) $data['billable'] : 1,
                $id,
            ]
        );
    }

    public static function delete(int $id): void
    {
        Database::getInstance()->query(
            'DELETE FROM time_entries WHERE id = ?',
            [$id]
        );
    }

    /**
     * Monthly totals for a single user.
     * Returns ['hours' => float, 'zakelijk' => float, 'prive' => float, 'amount' => float]
     */
    public static function monthlyTotal(int $userId, string $month): array
    {
        $rows = Database::getInstance()
            ->query(
                'SELECT type,
                        SUM(hours)               AS total_hours,
                        SUM(hours * hourly_rate) AS total_amount
                   FROM time_entries
                  WHERE user_id = ?
                    AND DATE_FORMAT(entry_date, \'%Y-%m\') = ?
                  GROUP BY type',
                [$userId, $month]
            )
            ->fetchAll();

        $result = [
            'hours'    => 0.0,
            'zakelijk' => 0.0,
            'prive'    => 0.0,
            'amount'   => 0.0,
        ];

        foreach ($rows as $row) {
            $hours  = (float) $row['total_hours'];
            $amount = (float) $row['total_amount'];

            $result['hours']  += $hours;
            $result['amount'] += $amount;

            if ($row['type'] === 'zakelijk') {
                $result['zakelijk'] = $hours;
            } elseif ($row['type'] === 'prive') {
                $result['prive'] = $hours;
            }
        }

        return $result;
    }

    /**
     * Monthly totals per user for a given month.
     * Returns array of ['user_id', 'user_name', 'hours', 'amount']
     */
    public static function monthlyTotals(string $month): array
    {
        return Database::getInstance()
            ->query(
                'SELECT te.user_id,
                        u.name                       AS user_name,
                        SUM(te.hours)                AS hours,
                        SUM(te.hours * te.hourly_rate) AS amount
                   FROM time_entries te
                   LEFT JOIN users u ON u.id = te.user_id
                  WHERE DATE_FORMAT(te.entry_date, \'%Y-%m\') = ?
                  GROUP BY te.user_id, u.name
                  ORDER BY u.name',
                [$month]
            )
            ->fetchAll();
    }

    /**
     * Project list for dropdowns.
     */
    public static function getProjects(): array
    {
        try {
            return Database::getInstance()
                ->query('SELECT id, name FROM projects WHERE status = \'actief\' ORDER BY name')
                ->fetchAll();
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * Company list for dropdowns.
     */
    public static function getCompanies(): array
    {
        try {
            return Database::getInstance()
                ->query('SELECT id, name FROM companies ORDER BY name')
                ->fetchAll();
        } catch (\Throwable) {
            return [];
        }
    }
}
