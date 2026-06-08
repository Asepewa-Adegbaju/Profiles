<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;

class DashboardController
{
    public function index(): void
    {
        Auth::require();

        $db   = Database::getInstance();
        $user = Auth::user();

        // ── Stats ────────────────────────────────────────────────────────────
        $crmCount = (int) $db->query(
            "SELECT COUNT(*) FROM companies WHERE status != 'inactief'"
        )->fetchColumn();

        $month      = date('Y-m');
        $urenTotal  = (float) ($db->query(
            "SELECT COALESCE(SUM(hours),0) FROM time_entries
             WHERE user_id = ? AND DATE_FORMAT(entry_date,'%Y-%m') = ?",
            [Auth::id(), $month]
        )->fetchColumn() ?? 0);

        $projectCount = (int) $db->query(
            "SELECT COUNT(*) FROM projects WHERE status = 'actief'"
        )->fetchColumn();

        $openInvoiceAmount = (float) ($db->query(
            "SELECT COALESCE(SUM(ii.quantity * ii.unit_price * (1 + ii.vat_rate/100)),0)
             FROM invoices i
             JOIN invoice_items ii ON ii.invoice_id = i.id
             WHERE i.status IN ('verzonden','te-laat')"
        )->fetchColumn() ?? 0);

        $overdueCount = (int) $db->query(
            "SELECT COUNT(*) FROM invoices
             WHERE status = 'verzonden' AND due_date < CURDATE()"
        )->fetchColumn();

        // ── Komende afspraken (max 5) ─────────────────────────────────────
        $upcomingMeetings = $db->query(
            "SELECT m.*, c.name AS company_name, co.name AS contact_name
             FROM meetings m
             JOIN companies c ON c.id = m.company_id
             LEFT JOIN contacts co ON co.id = m.contact_id
             WHERE m.meeting_date >= NOW() AND m.status IN ('gepland','bevestigd')
             ORDER BY m.meeting_date ASC
             LIMIT 5"
        )->fetchAll();

        // ── Recente uren (huidige gebruiker, max 5) ───────────────────────
        $recentTime = $db->query(
            "SELECT t.*, p.name AS project_name, c.name AS company_name
             FROM time_entries t
             LEFT JOIN projects p ON p.id = t.project_id
             LEFT JOIN companies c ON c.id = t.company_id
             WHERE t.user_id = ?
             ORDER BY t.entry_date DESC, t.id DESC
             LIMIT 5",
            [Auth::id()]
        )->fetchAll();

        // ── Recente audit-log activiteit (max 8) ─────────────────────────
        $recentActivity = $db->query(
            "SELECT a.*, u.name AS user_name
             FROM audit_log a
             LEFT JOIN users u ON u.id = a.user_id
             ORDER BY a.created_at DESC
             LIMIT 8"
        )->fetchAll();

        $title = 'Dashboard';
        $view  = 'dashboard/index';

        require APP_ROOT . '/app/Views/layouts/main.php';
    }
}
