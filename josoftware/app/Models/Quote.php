<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Quote
{
    public static function all(string $status = ''): array
    {
        $db = Database::getInstance();

        if ($status !== '') {
            return $db->query(
                'SELECT q.*,
                        c.name AS company_name,
                        COALESCE(
                            (SELECT SUM(qi.quantity * qi.unit_price)
                               FROM quote_items qi WHERE qi.quote_id = q.id), 0
                        ) AS subtotal
                   FROM quotes q
                   LEFT JOIN companies c ON c.id = q.company_id
                  WHERE q.status = ?
                  ORDER BY q.created_at DESC',
                [$status]
            )->fetchAll();
        }

        return $db->query(
            'SELECT q.*,
                    c.name AS company_name,
                    COALESCE(
                        (SELECT SUM(qi.quantity * qi.unit_price)
                           FROM quote_items qi WHERE qi.quote_id = q.id), 0
                    ) AS subtotal
               FROM quotes q
               LEFT JOIN companies c ON c.id = q.company_id
              ORDER BY q.created_at DESC'
        )->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $row = Database::getInstance()->query(
            'SELECT q.*,
                    c.name    AS company_name,
                    c.address AS company_address,
                    c.city    AS company_city,
                    c.postal_code AS company_postal_code,
                    u.name    AS created_by_name
               FROM quotes q
               LEFT JOIN companies c ON c.id = q.company_id
               LEFT JOIN users     u ON u.id = q.created_by
              WHERE q.id = ?
              LIMIT 1',
            [$id]
        )->fetch();

        return $row ?: null;
    }

    public static function findWithItems(int $id): array
    {
        $quote = self::find($id);
        if ($quote === null) {
            return ['quote' => null, 'items' => []];
        }

        $items = Database::getInstance()->query(
            'SELECT * FROM quote_items WHERE quote_id = ? ORDER BY sort_order ASC, id ASC',
            [$id]
        )->fetchAll();

        return ['quote' => $quote, 'items' => $items];
    }

    public static function create(array $data): int
    {
        $db = Database::getInstance();

        $db->query(
            'INSERT INTO quotes
                (quote_number, company_id, created_by, issue_date, valid_until, status, notes, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, NOW())',
            [
                $data['quote_number'],
                $data['company_id'],
                $data['created_by'] ?? null,
                $data['issue_date'],
                $data['valid_until'],
                $data['status']     ?? 'concept',
                $data['notes']      ?? null,
            ]
        );

        return $db->lastInsertId();
    }

    public static function addItem(int $quoteId, array $item): void
    {
        Database::getInstance()->query(
            'INSERT INTO quote_items
                (quote_id, description, quantity, unit_price, vat_rate, sort_order)
             VALUES (?, ?, ?, ?, ?, ?)',
            [
                $quoteId,
                $item['description'],
                $item['quantity']   ?? 1.00,
                $item['unit_price'] ?? 0.00,
                $item['vat_rate']   ?? 21.00,
                $item['sort_order'] ?? 0,
            ]
        );
    }

    public static function updateStatus(int $id, string $status): void
    {
        Database::getInstance()->query(
            'UPDATE quotes SET status = ? WHERE id = ?',
            [$status, $id]
        );
    }

    public static function delete(int $id): void
    {
        // quote_items worden verwijderd via ON DELETE CASCADE
        Database::getInstance()->query(
            'DELETE FROM quotes WHERE id = ?',
            [$id]
        );
    }

    public static function calculateTotals(array $items): array
    {
        $subtotal = 0.0;
        $vat      = ['21%' => 0.0, '9%' => 0.0, '0%' => 0.0];

        foreach ($items as $item) {
            $lineTotal = (float)$item['quantity'] * (float)$item['unit_price'];
            $subtotal += $lineTotal;

            $rate = (float)$item['vat_rate'];
            $vatAmount = $lineTotal * ($rate / 100);

            if ($rate >= 21) {
                $vat['21%'] += $vatAmount;
            } elseif ($rate >= 9) {
                $vat['9%'] += $vatAmount;
            } else {
                $vat['0%'] += $vatAmount;
            }
        }

        $totalVat = $vat['21%'] + $vat['9%'] + $vat['0%'];
        $total    = $subtotal + $totalVat;

        return [
            'subtotal' => $subtotal,
            'vat'      => $vat,
            'total'    => $total,
        ];
    }

    public static function getCompanies(): array
    {
        return Database::getInstance()->query(
            'SELECT id, name FROM companies ORDER BY name'
        )->fetchAll();
    }

    public static function nextNumber(): string
    {
        $year = date('Y');
        $row  = Database::getInstance()->query(
            'SELECT COUNT(*) AS cnt FROM quotes WHERE quote_number LIKE ?',
            ['OFF-' . $year . '-%']
        )->fetch();
        $n = ($row['cnt'] ?? 0) + 1;
        return 'OFF-' . $year . '-' . str_pad((string)$n, 3, '0', STR_PAD_LEFT);
    }

    public static function recent(int $limit = 5): array
    {
        return Database::getInstance()->query(
            'SELECT q.*,
                    c.name AS company_name,
                    COALESCE(
                        (SELECT SUM(qi.quantity * qi.unit_price)
                           FROM quote_items qi WHERE qi.quote_id = q.id), 0
                    ) AS subtotal
               FROM quotes q
               LEFT JOIN companies c ON c.id = q.company_id
              ORDER BY q.created_at DESC
              LIMIT ?',
            [$limit]
        )->fetchAll();
    }

    public static function count(): int
    {
        $row = Database::getInstance()->query(
            'SELECT COUNT(*) AS cnt FROM quotes'
        )->fetch();
        return (int)($row['cnt'] ?? 0);
    }

    public static function acceptedQuotes(): array
    {
        return Database::getInstance()->query(
            'SELECT q.id, q.quote_number, c.name AS company_name
               FROM quotes q
               LEFT JOIN companies c ON c.id = q.company_id
              WHERE q.status = \'geaccepteerd\'
              ORDER BY q.issue_date DESC'
        )->fetchAll();
    }
}
