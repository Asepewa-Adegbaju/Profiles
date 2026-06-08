<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Invoice
{
    public static function all(string $status = ''): array
    {
        $db = Database::getInstance();

        if ($status !== '') {
            return $db->query(
                'SELECT i.*,
                        c.name AS company_name,
                        COALESCE(
                            (SELECT SUM(ii.quantity * ii.unit_price)
                               FROM invoice_items ii WHERE ii.invoice_id = i.id), 0
                        ) AS subtotal
                   FROM invoices i
                   LEFT JOIN companies c ON c.id = i.company_id
                  WHERE i.status = ?
                  ORDER BY i.created_at DESC',
                [$status]
            )->fetchAll();
        }

        return $db->query(
            'SELECT i.*,
                    c.name AS company_name,
                    COALESCE(
                        (SELECT SUM(ii.quantity * ii.unit_price)
                           FROM invoice_items ii WHERE ii.invoice_id = i.id), 0
                    ) AS subtotal
               FROM invoices i
               LEFT JOIN companies c ON c.id = i.company_id
              ORDER BY i.created_at DESC'
        )->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $row = Database::getInstance()->query(
            'SELECT i.*,
                    c.name        AS company_name,
                    c.address     AS company_address,
                    c.city        AS company_city,
                    c.postal_code AS company_postal_code,
                    u.name        AS created_by_name,
                    q.quote_number
               FROM invoices i
               LEFT JOIN companies c ON c.id = i.company_id
               LEFT JOIN users     u ON u.id = i.created_by
               LEFT JOIN quotes    q ON q.id = i.quote_id
              WHERE i.id = ?
              LIMIT 1',
            [$id]
        )->fetch();

        return $row ?: null;
    }

    public static function findWithItems(int $id): array
    {
        $invoice = self::find($id);
        if ($invoice === null) {
            return ['invoice' => null, 'items' => []];
        }

        $items = Database::getInstance()->query(
            'SELECT * FROM invoice_items WHERE invoice_id = ? ORDER BY sort_order ASC, id ASC',
            [$id]
        )->fetchAll();

        return ['invoice' => $invoice, 'items' => $items];
    }

    public static function create(array $data): int
    {
        $db = Database::getInstance();

        $db->query(
            'INSERT INTO invoices
                (invoice_number, company_id, quote_id, created_by, issue_date, due_date, status, notes, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())',
            [
                $data['invoice_number'],
                $data['company_id'],
                $data['quote_id']    ?? null,
                $data['created_by']  ?? null,
                $data['issue_date'],
                $data['due_date'],
                $data['status']      ?? 'concept',
                $data['notes']       ?? null,
            ]
        );

        return $db->lastInsertId();
    }

    public static function addItem(int $invoiceId, array $item): void
    {
        Database::getInstance()->query(
            'INSERT INTO invoice_items
                (invoice_id, description, quantity, unit_price, vat_rate, sort_order)
             VALUES (?, ?, ?, ?, ?, ?)',
            [
                $invoiceId,
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
            'UPDATE invoices SET status = ? WHERE id = ?',
            [$status, $id]
        );
    }

    public static function delete(int $id): void
    {
        // invoice_items worden verwijderd via ON DELETE CASCADE
        Database::getInstance()->query(
            'DELETE FROM invoices WHERE id = ?',
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
            'SELECT COUNT(*) AS cnt FROM invoices WHERE invoice_number LIKE ?',
            ['FACT-' . $year . '-%']
        )->fetch();
        $n = ($row['cnt'] ?? 0) + 1;
        return 'FACT-' . $year . '-' . str_pad((string)$n, 3, '0', STR_PAD_LEFT);
    }

    public static function totalOpenAmount(): float
    {
        // SUM of subtotaal + BTW for all non-paid, non-cancelled invoices
        $rows = Database::getInstance()->query(
            'SELECT ii.quantity, ii.unit_price, ii.vat_rate
               FROM invoice_items ii
               JOIN invoices i ON i.id = ii.invoice_id
              WHERE i.status NOT IN (\'betaald\', \'geannuleerd\')'
        )->fetchAll();

        $total = 0.0;
        foreach ($rows as $row) {
            $lineTotal = (float)$row['quantity'] * (float)$row['unit_price'];
            $vatAmount = $lineTotal * ((float)$row['vat_rate'] / 100);
            $total += $lineTotal + $vatAmount;
        }
        return $total;
    }

    public static function overdueCount(): int
    {
        $row = Database::getInstance()->query(
            "SELECT COUNT(*) AS cnt
               FROM invoices
              WHERE status = 'verzonden'
                AND due_date < CURDATE()"
        )->fetch();
        return (int)($row['cnt'] ?? 0);
    }

    public static function recent(int $limit = 5): array
    {
        return Database::getInstance()->query(
            'SELECT i.*,
                    c.name AS company_name,
                    COALESCE(
                        (SELECT SUM(ii.quantity * ii.unit_price)
                           FROM invoice_items ii WHERE ii.invoice_id = i.id), 0
                    ) AS subtotal
               FROM invoices i
               LEFT JOIN companies c ON c.id = i.company_id
              ORDER BY i.created_at DESC
              LIMIT ?',
            [$limit]
        )->fetchAll();
    }

    public static function count(): int
    {
        $row = Database::getInstance()->query(
            'SELECT COUNT(*) AS cnt FROM invoices'
        )->fetch();
        return (int)($row['cnt'] ?? 0);
    }
}
