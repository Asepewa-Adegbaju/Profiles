<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Expense
{
    public static function all(?string $month = null, ?int $userId = null): array
    {
        $where  = ['1=1'];
        $params = [];

        if ($month && preg_match('/^\d{4}-\d{2}$/', $month)) {
            $where[]  = "DATE_FORMAT(e.entry_date,'%Y-%m') = ?";
            $params[] = $month;
        }
        if ($userId) {
            $where[]  = 'e.user_id = ?';
            $params[] = $userId;
        }

        $sql = "SELECT e.*,
                       u.name        AS user_name,
                       ec.name       AS category_name,
                       ec.color      AS category_color,
                       c.name        AS company_name,
                       p.name        AS project_name,
                       ROUND(e.amount * e.vat_rate / 100, 2) AS vat_amount,
                       ROUND(e.amount * (1 + e.vat_rate / 100), 2) AS amount_incl
                FROM expenses e
                LEFT JOIN users             u  ON u.id  = e.user_id
                LEFT JOIN expense_categories ec ON ec.id = e.category_id
                LEFT JOIN companies          c  ON c.id  = e.company_id
                LEFT JOIN projects           p  ON p.id  = e.project_id
                WHERE " . implode(' AND ', $where) . "
                ORDER BY e.entry_date DESC, e.id DESC";

        return Database::getInstance()->query($sql, $params)->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $row = Database::getInstance()->query(
            "SELECT e.*,
                    u.name        AS user_name,
                    ec.name       AS category_name,
                    ec.color      AS category_color,
                    c.name        AS company_name,
                    p.name        AS project_name
             FROM expenses e
             LEFT JOIN users              u  ON u.id  = e.user_id
             LEFT JOIN expense_categories ec ON ec.id = e.category_id
             LEFT JOIN companies          c  ON c.id  = e.company_id
             LEFT JOIN projects           p  ON p.id  = e.project_id
             WHERE e.id = ? LIMIT 1",
            [$id]
        )->fetch();

        return $row ?: null;
    }

    public static function create(array $data): int
    {
        $db = Database::getInstance();
        $db->query(
            "INSERT INTO expenses
                (user_id, category_id, company_id, project_id, entry_date,
                 amount, vat_rate, description, supplier, receipt_filename,
                 type, status, notes, created_at)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,NOW())",
            [
                $data['user_id'],
                $data['category_id']  ?: null,
                $data['company_id']   ?: null,
                $data['project_id']   ?: null,
                $data['entry_date'],
                $data['amount'],
                $data['vat_rate'],
                $data['description'],
                $data['supplier']     ?: null,
                $data['receipt_filename'] ?? null,
                $data['type'],
                $data['status'] ?? 'ingediend',
                $data['notes']        ?: null,
            ]
        );
        return $db->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        Database::getInstance()->query(
            "UPDATE expenses SET
                category_id = ?, company_id = ?, project_id = ?,
                entry_date = ?, amount = ?, vat_rate = ?,
                description = ?, supplier = ?, type = ?,
                status = ?, notes = ?
             WHERE id = ?",
            [
                $data['category_id'] ?: null,
                $data['company_id']  ?: null,
                $data['project_id']  ?: null,
                $data['entry_date'],
                $data['amount'],
                $data['vat_rate'],
                $data['description'],
                $data['supplier']    ?: null,
                $data['type'],
                $data['status'],
                $data['notes']       ?: null,
                $id,
            ]
        );
    }

    public static function updateReceiptFilename(int $id, string $filename): void
    {
        Database::getInstance()->query(
            'UPDATE expenses SET receipt_filename = ? WHERE id = ?',
            [$filename, $id]
        );
    }

    public static function delete(int $id): ?string
    {
        $row = self::find($id);
        Database::getInstance()->query('DELETE FROM expenses WHERE id = ?', [$id]);
        return $row['receipt_filename'] ?? null;
    }

    public static function monthlyTotals(string $month): array
    {
        return Database::getInstance()->query(
            "SELECT
                COALESCE(SUM(amount), 0)                              AS total_excl,
                COALESCE(SUM(amount * vat_rate / 100), 0)            AS total_vat,
                COALESCE(SUM(amount * (1 + vat_rate / 100)), 0)      AS total_incl,
                COALESCE(SUM(CASE WHEN type='zakelijk' THEN amount ELSE 0 END), 0) AS zakelijk,
                COALESCE(SUM(CASE WHEN type='prive'    THEN amount ELSE 0 END), 0) AS prive,
                COUNT(*)                                               AS count
             FROM expenses
             WHERE DATE_FORMAT(entry_date,'%Y-%m') = ?",
            [$month]
        )->fetch();
    }

    public static function perCategory(string $month): array
    {
        return Database::getInstance()->query(
            "SELECT
                ec.name  AS category,
                ec.color AS color,
                COALESCE(SUM(e.amount), 0) AS total,
                COUNT(e.id)                AS count
             FROM expenses e
             LEFT JOIN expense_categories ec ON ec.id = e.category_id
             WHERE DATE_FORMAT(e.entry_date,'%Y-%m') = ?
             GROUP BY ec.id, ec.name, ec.color
             ORDER BY total DESC",
            [$month]
        )->fetchAll();
    }

    public static function yearlyTotal(int $year): array
    {
        return Database::getInstance()->query(
            "SELECT
                DATE_FORMAT(entry_date,'%m') AS month_num,
                COALESCE(SUM(amount), 0)     AS total
             FROM expenses
             WHERE YEAR(entry_date) = ? AND type = 'zakelijk'
             GROUP BY month_num
             ORDER BY month_num",
            [$year]
        )->fetchAll();
    }

    public static function getCategories(): array
    {
        return Database::getInstance()->query(
            'SELECT * FROM expense_categories ORDER BY name'
        )->fetchAll();
    }

    public static function getCompanies(): array
    {
        return Database::getInstance()->query(
            'SELECT id, name FROM companies ORDER BY name'
        )->fetchAll();
    }

    public static function getProjects(): array
    {
        return Database::getInstance()->query(
            "SELECT id, name FROM projects WHERE status = 'actief' ORDER BY name"
        )->fetchAll();
    }
}
