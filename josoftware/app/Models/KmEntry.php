<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class KmEntry
{
    /**
     * All km entries for a specific user, optionally filtered by month (Y-m).
     */
    public static function allByUser(int $userId, ?string $month = null): array
    {
        $db = Database::getInstance();

        $sql    = 'SELECT * FROM km_entries WHERE user_id = ?';
        $params = [$userId];

        if ($month !== null && preg_match('/^\d{4}-\d{2}$/', $month)) {
            $sql      .= ' AND DATE_FORMAT(entry_date, \'%Y-%m\') = ?';
            $params[]  = $month;
        }

        $sql .= ' ORDER BY entry_date DESC, id DESC';

        return $db->query($sql, $params)->fetchAll();
    }

    /**
     * All km entries for all users, optionally filtered by month.
     */
    public static function all(?string $month = null): array
    {
        $db = Database::getInstance();

        $sql = 'SELECT ke.*, u.name AS user_name
                  FROM km_entries ke
                  LEFT JOIN users u ON u.id = ke.user_id';

        $params = [];

        if ($month !== null && preg_match('/^\d{4}-\d{2}$/', $month)) {
            $sql      .= ' WHERE DATE_FORMAT(ke.entry_date, \'%Y-%m\') = ?';
            $params[]  = $month;
        }

        $sql .= ' ORDER BY ke.entry_date DESC, ke.id DESC';

        return $db->query($sql, $params)->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $row = Database::getInstance()
            ->query('SELECT * FROM km_entries WHERE id = ? LIMIT 1', [$id])
            ->fetch();

        return $row ?: null;
    }

    public static function create(array $data): int
    {
        $db = Database::getInstance();

        $db->query(
            'INSERT INTO km_entries
                (user_id, entry_date, from_location, to_location, km, purpose, type, rate_per_km, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())',
            [
                $data['user_id'],
                $data['entry_date'],
                $data['from_location'],
                $data['to_location'],
                $data['km'],
                $data['purpose'],
                $data['type']        ?? 'zakelijk',
                $data['rate_per_km'] ?? 0.230,
            ]
        );

        return $db->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        Database::getInstance()->query(
            'UPDATE km_entries
                SET entry_date    = ?,
                    from_location = ?,
                    to_location   = ?,
                    km            = ?,
                    purpose       = ?,
                    type          = ?,
                    rate_per_km   = ?
              WHERE id = ?',
            [
                $data['entry_date'],
                $data['from_location'],
                $data['to_location'],
                $data['km'],
                $data['purpose'],
                $data['type']        ?? 'zakelijk',
                $data['rate_per_km'] ?? 0.230,
                $id,
            ]
        );
    }

    public static function delete(int $id): void
    {
        Database::getInstance()->query(
            'DELETE FROM km_entries WHERE id = ?',
            [$id]
        );
    }

    /**
     * Monthly totals for a single user.
     * Returns ['km' => float, 'zakelijk' => float, 'prive' => float, 'amount' => float]
     */
    public static function monthlyTotal(int $userId, string $month): array
    {
        $rows = Database::getInstance()
            ->query(
                'SELECT type,
                        SUM(km)             AS total_km,
                        SUM(km * rate_per_km) AS total_amount
                   FROM km_entries
                  WHERE user_id = ?
                    AND DATE_FORMAT(entry_date, \'%Y-%m\') = ?
                  GROUP BY type',
                [$userId, $month]
            )
            ->fetchAll();

        $result = [
            'km'       => 0.0,
            'zakelijk' => 0.0,
            'prive'    => 0.0,
            'amount'   => 0.0,
        ];

        foreach ($rows as $row) {
            $km     = (float) $row['total_km'];
            $amount = (float) $row['total_amount'];

            $result['km']     += $km;
            $result['amount'] += $amount;

            if ($row['type'] === 'zakelijk') {
                $result['zakelijk'] = $km;
            } elseif ($row['type'] === 'prive') {
                $result['prive'] = $km;
            }
        }

        return $result;
    }
}
