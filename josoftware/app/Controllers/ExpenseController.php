<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Session;
use App\Core\AuditLog;
use App\Models\Expense;

class ExpenseController
{
    private const RECEIPT_DIR     = APP_ROOT . '/storage/receipts/';
    private const ALLOWED_TYPES   = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
    private const MAX_FILE_SIZE   = 5 * 1024 * 1024; // 5 MB

    public function index(): void
    {
        Auth::require();

        $month = $_GET['month'] ?? date('Y-m');
        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            $month = date('Y-m');
        }

        $prevMonth   = date('Y-m', strtotime($month . '-01 -1 month'));
        $nextMonth   = date('Y-m', strtotime($month . '-01 +1 month'));
        $monthLabel  = $this->dutchMonth($month);

        $expenses    = Expense::all($month);
        $totals      = Expense::monthlyTotals($month);
        $perCategory = Expense::perCategory($month);

        $title = 'Uitgaven';
        $view  = 'expenses/index';
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    public function create(): void
    {
        Auth::require();
        $categories = Expense::getCategories();
        $companies  = Expense::getCompanies();
        $projects   = Expense::getProjects();
        $title      = 'Nieuwe uitgave';
        $view       = 'expenses/create';
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    public function store(): void
    {
        Auth::require();
        CSRF::validateOrDie();

        $amount = (float) str_replace(',', '.', $_POST['amount'] ?? '0');
        if ($amount <= 0) {
            Session::flash('error', 'Voer een geldig bedrag in.');
            header('Location: ' . APP_URL . '/uitgaven/nieuw'); exit;
        }

        $description = trim($_POST['description'] ?? '');
        if ($description === '') {
            Session::flash('error', 'Omschrijving is verplicht.');
            header('Location: ' . APP_URL . '/uitgaven/nieuw'); exit;
        }

        $data = [
            'user_id'     => Auth::id(),
            'category_id' => (int)($_POST['category_id'] ?? 0) ?: null,
            'company_id'  => (int)($_POST['company_id']  ?? 0) ?: null,
            'project_id'  => (int)($_POST['project_id']  ?? 0) ?: null,
            'entry_date'  => $_POST['entry_date']  ?? date('Y-m-d'),
            'amount'      => $amount,
            'vat_rate'    => (float)($_POST['vat_rate'] ?? 21),
            'description' => $description,
            'supplier'    => trim($_POST['supplier'] ?? ''),
            'type'        => in_array($_POST['type'] ?? '', ['zakelijk','prive']) ? $_POST['type'] : 'zakelijk',
            'status'      => 'ingediend',
            'notes'       => trim($_POST['notes'] ?? ''),
        ];

        $id = Expense::create($data);

        // Bonnetje uploaden
        if (!empty($_FILES['receipt']['name'])) {
            $filename = $this->handleUpload($_FILES['receipt'], $id);
            if ($filename) {
                Expense::updateReceiptFilename($id, $filename);
            }
        }

        AuditLog::log('create', 'expenses', $id, 'Uitgave toegevoegd: ' . $description);
        Session::flash('success', 'Uitgave opgeslagen.');
        header('Location: ' . APP_URL . '/uitgaven'); exit;
    }

    public function show(string $id): void
    {
        Auth::require();
        $expense = Expense::find((int)$id);
        if (!$expense) {
            Session::flash('error', 'Uitgave niet gevonden.');
            header('Location: ' . APP_URL . '/uitgaven'); exit;
        }
        $title = 'Uitgave details';
        $view  = 'expenses/show';
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    public function edit(string $id): void
    {
        Auth::require();
        $expense    = Expense::find((int)$id);
        if (!$expense) {
            Session::flash('error', 'Uitgave niet gevonden.');
            header('Location: ' . APP_URL . '/uitgaven'); exit;
        }
        $categories = Expense::getCategories();
        $companies  = Expense::getCompanies();
        $projects   = Expense::getProjects();
        $title      = 'Uitgave bewerken';
        $view       = 'expenses/edit';
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    public function update(string $id): void
    {
        Auth::require();
        CSRF::validateOrDie();

        $expense = Expense::find((int)$id);
        if (!$expense) {
            Session::flash('error', 'Uitgave niet gevonden.');
            header('Location: ' . APP_URL . '/uitgaven'); exit;
        }

        $amount = (float) str_replace(',', '.', $_POST['amount'] ?? '0');

        Expense::update((int)$id, [
            'category_id' => (int)($_POST['category_id'] ?? 0) ?: null,
            'company_id'  => (int)($_POST['company_id']  ?? 0) ?: null,
            'project_id'  => (int)($_POST['project_id']  ?? 0) ?: null,
            'entry_date'  => $_POST['entry_date']  ?? date('Y-m-d'),
            'amount'      => $amount,
            'vat_rate'    => (float)($_POST['vat_rate'] ?? 21),
            'description' => trim($_POST['description'] ?? ''),
            'supplier'    => trim($_POST['supplier']    ?? ''),
            'type'        => in_array($_POST['type'] ?? '', ['zakelijk','prive']) ? $_POST['type'] : 'zakelijk',
            'status'      => in_array($_POST['status'] ?? '', ['ingediend','goedgekeurd','afgewezen']) ? $_POST['status'] : 'ingediend',
            'notes'       => trim($_POST['notes'] ?? ''),
        ]);

        // Nieuw bonnetje uploaden
        if (!empty($_FILES['receipt']['name'])) {
            // Oud bonnetje verwijderen
            if ($expense['receipt_filename']) {
                @unlink(self::RECEIPT_DIR . $expense['receipt_filename']);
            }
            $filename = $this->handleUpload($_FILES['receipt'], (int)$id);
            if ($filename) {
                Expense::updateReceiptFilename((int)$id, $filename);
            }
        }

        AuditLog::log('update', 'expenses', (int)$id, 'Uitgave bijgewerkt');
        Session::flash('success', 'Uitgave bijgewerkt.');
        header('Location: ' . APP_URL . '/uitgaven/' . $id); exit;
    }

    public function delete(string $id): void
    {
        Auth::require();
        CSRF::validateOrDie();

        $filename = Expense::delete((int)$id);
        if ($filename) {
            @unlink(self::RECEIPT_DIR . $filename);
        }

        AuditLog::log('delete', 'expenses', (int)$id, 'Uitgave verwijderd');
        Session::flash('success', 'Uitgave verwijderd.');
        header('Location: ' . APP_URL . '/uitgaven'); exit;
    }

    public function receipt(string $id): void
    {
        Auth::require();
        $expense = Expense::find((int)$id);

        if (!$expense || !$expense['receipt_filename']) {
            http_response_code(404); die('Bonnetje niet gevonden.');
        }

        $path = self::RECEIPT_DIR . basename($expense['receipt_filename']);
        if (!file_exists($path)) {
            http_response_code(404); die('Bestand niet gevonden.');
        }

        $mime = mime_content_type($path);
        header('Content-Type: ' . $mime);
        header('Content-Disposition: inline; filename="' . basename($path) . '"');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;
    }

    public function exportCsv(): void
    {
        Auth::require();
        $month    = $_GET['month'] ?? date('Y-m');
        $expenses = Expense::all($month);

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="uitgaven-' . $month . '.csv"');
        echo "\xEF\xBB\xBF";
        $out = fopen('php://output', 'w');
        fputcsv($out, ['Datum','Leverancier','Omschrijving','Categorie','Bedrag excl.','BTW%','BTW','Bedrag incl.','Type','Status','Project','Bedrijf'], ';');
        foreach ($expenses as $e) {
            fputcsv($out, [
                $e['entry_date'],
                $e['supplier'] ?? '',
                $e['description'],
                $e['category_name'] ?? 'Overig',
                number_format((float)$e['amount'], 2, ',', '.'),
                $e['vat_rate'],
                number_format((float)$e['vat_amount'], 2, ',', '.'),
                number_format((float)$e['amount_incl'], 2, ',', '.'),
                $e['type'],
                $e['status'],
                $e['project_name'] ?? '',
                $e['company_name'] ?? '',
            ], ';');
        }
        fclose($out);
        exit;
    }

    // ── Privé hulpmethodes ────────────────────────────────────────────────────

    private function handleUpload(array $file, int $expenseId): ?string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) return null;
        if ($file['size'] > self::MAX_FILE_SIZE) return null;

        $mime = mime_content_type($file['tmp_name']);
        if (!in_array($mime, self::ALLOWED_TYPES, true)) return null;

        $ext      = $mime === 'application/pdf' ? 'pdf' : 'jpg';
        $filename = 'receipt_' . $expenseId . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $dest     = self::RECEIPT_DIR . $filename;

        if (!is_dir(self::RECEIPT_DIR)) {
            mkdir(self::RECEIPT_DIR, 0750, true);
        }

        if (move_uploaded_file($file['tmp_name'], $dest)) {
            return $filename;
        }
        return null;
    }

    private function dutchMonth(string $month): string
    {
        $m = ['','januari','februari','maart','april','mei','juni',
              'juli','augustus','september','oktober','november','december'];
        return $m[(int)substr($month, 5, 2)] . ' ' . substr($month, 0, 4);
    }
}
