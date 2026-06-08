<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Session;
use App\Core\AuditLog;
use App\Models\Quote;
use App\Models\Invoice;

class FinanceController
{
    // ── Dashboard ────────────────────────────────────────────────────────────

    public function index(): void
    {
        Auth::require();

        $openAmount   = Invoice::totalOpenAmount();
        $overdueCount = Invoice::overdueCount();
        $quoteCount   = Quote::count();
        $invoiceCount = Invoice::count();
        $recentQuotes   = Quote::recent(5);
        $recentInvoices = Invoice::recent(5);

        $title = 'Financiën';
        $view  = 'financien/index';
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    // ── Offertes ─────────────────────────────────────────────────────────────

    public function quotes(): void
    {
        Auth::require();

        $status = trim($_GET['status'] ?? '');
        $quotes = Quote::all($status);

        $title = 'Financiën — Offertes';
        $view  = 'financien/quotes/index';
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    public function createQuote(): void
    {
        Auth::require();

        $companies  = Quote::getCompanies();
        $nextNumber = Quote::nextNumber();

        $title = 'Financiën — Nieuwe offerte';
        $view  = 'financien/quotes/create';
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    public function storeQuote(): void
    {
        Auth::require();
        CSRF::validateOrDie();

        $companyId  = (int) ($_POST['company_id'] ?? 0);
        $issueDate  = trim($_POST['issue_date'] ?? '');
        $validUntil = trim($_POST['valid_until'] ?? '');

        if ($companyId === 0 || $issueDate === '' || $validUntil === '') {
            Session::flash('error', 'Bedrijf, offertedatum en geldigheidsdatum zijn verplicht.');
            header('Location: ' . APP_URL . '/financien/offertes/nieuw'); exit;
        }

        $quoteId = Quote::create([
            'quote_number' => Quote::nextNumber(),
            'company_id'   => $companyId,
            'created_by'   => Auth::id(),
            'issue_date'   => $issueDate,
            'valid_until'  => $validUntil,
            'status'       => 'concept',
            'notes'        => trim($_POST['notes'] ?? '') ?: null,
        ]);

        $items = $_POST['items'] ?? [];
        $sortOrder = 0;
        foreach ($items as $item) {
            if (empty(trim($item['description'] ?? ''))) {
                continue;
            }
            Quote::addItem($quoteId, [
                'description' => trim($item['description']),
                'quantity'    => (float) ($item['quantity'] ?? 1),
                'unit_price'  => (float) ($item['unit_price'] ?? 0),
                'vat_rate'    => (float) ($item['vat_rate'] ?? 21),
                'sort_order'  => $sortOrder++,
            ]);
        }

        AuditLog::log('create', 'quotes', $quoteId, 'Offerte aangemaakt: ' . (Quote::find($quoteId)['quote_number'] ?? (string) $quoteId));
        Session::flash('success', 'Offerte is aangemaakt.');
        header('Location: ' . APP_URL . '/financien/offertes/' . $quoteId); exit;
    }

    public function showQuote(string $id): void
    {
        Auth::require();

        $data = Quote::findWithItems((int) $id);
        if ($data['quote'] === null) {
            Session::flash('error', 'Offerte niet gevonden.');
            header('Location: ' . APP_URL . '/financien/offertes'); exit;
        }

        $quote  = $data['quote'];
        $items  = $data['items'];
        $totals = Quote::calculateTotals($items);

        $title = 'Financiën — Offerte ' . $quote['quote_number'];
        $view  = 'financien/quotes/show';
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    public function updateQuoteStatus(string $id): void
    {
        Auth::require();
        CSRF::validateOrDie();

        $quote = Quote::find((int) $id);
        if ($quote === null) {
            Session::flash('error', 'Offerte niet gevonden.');
            header('Location: ' . APP_URL . '/financien/offertes'); exit;
        }

        $allowed = ['concept', 'verzonden', 'geaccepteerd', 'afgewezen', 'verlopen'];
        $status  = $_POST['status'] ?? '';

        if (!in_array($status, $allowed, true)) {
            Session::flash('error', 'Ongeldige status.');
            header('Location: ' . APP_URL . '/financien/offertes/' . $id); exit;
        }

        Quote::updateStatus((int) $id, $status);
        AuditLog::log('update', 'quotes', (int) $id, 'Offertestatus bijgewerkt naar: ' . $status);
        Session::flash('success', 'Status van de offerte is bijgewerkt.');
        header('Location: ' . APP_URL . '/financien/offertes/' . $id); exit;
    }

    public function deleteQuote(string $id): void
    {
        Auth::require();
        CSRF::validateOrDie();

        $quote = Quote::find((int) $id);
        if ($quote === null) {
            Session::flash('error', 'Offerte niet gevonden.');
            header('Location: ' . APP_URL . '/financien/offertes'); exit;
        }

        Quote::delete((int) $id);
        AuditLog::log('delete', 'quotes', (int) $id, 'Offerte verwijderd: ' . $quote['quote_number']);
        Session::flash('success', 'Offerte "' . $quote['quote_number'] . '" is verwijderd.');
        header('Location: ' . APP_URL . '/financien/offertes'); exit;
    }

    public function printQuote(string $id): void
    {
        Auth::require();

        $data = Quote::findWithItems((int) $id);
        if ($data['quote'] === null) {
            Session::flash('error', 'Offerte niet gevonden.');
            header('Location: ' . APP_URL . '/financien/offertes'); exit;
        }

        $quote  = $data['quote'];
        $items  = $data['items'];
        $totals = Quote::calculateTotals($items);

        require APP_ROOT . '/app/Views/financien/quotes/print.php';
    }

    // ── Facturen ─────────────────────────────────────────────────────────────

    public function invoices(): void
    {
        Auth::require();

        $status   = trim($_GET['status'] ?? '');
        $invoices = Invoice::all($status);

        $title = 'Financiën — Facturen';
        $view  = 'financien/invoices/index';
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    public function createInvoice(): void
    {
        Auth::require();

        $companies      = Invoice::getCompanies();
        $nextNumber     = Invoice::nextNumber();
        $acceptedQuotes = Quote::acceptedQuotes();

        $title = 'Financiën — Nieuwe factuur';
        $view  = 'financien/invoices/create';
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    public function storeInvoice(): void
    {
        Auth::require();
        CSRF::validateOrDie();

        $companyId = (int) ($_POST['company_id'] ?? 0);
        $issueDate = trim($_POST['issue_date'] ?? '');
        $dueDate   = trim($_POST['due_date'] ?? '');

        if ($companyId === 0 || $issueDate === '' || $dueDate === '') {
            Session::flash('error', 'Bedrijf, factuurdatum en vervaldatum zijn verplicht.');
            header('Location: ' . APP_URL . '/financien/facturen/nieuw'); exit;
        }

        $quoteId = !empty($_POST['quote_id']) ? (int) $_POST['quote_id'] : null;

        $invoiceId = Invoice::create([
            'invoice_number' => Invoice::nextNumber(),
            'company_id'     => $companyId,
            'quote_id'       => $quoteId,
            'created_by'     => Auth::id(),
            'issue_date'     => $issueDate,
            'due_date'       => $dueDate,
            'status'         => 'concept',
            'notes'          => trim($_POST['notes'] ?? '') ?: null,
        ]);

        $items = $_POST['items'] ?? [];
        $sortOrder = 0;
        foreach ($items as $item) {
            if (empty(trim($item['description'] ?? ''))) {
                continue;
            }
            Invoice::addItem($invoiceId, [
                'description' => trim($item['description']),
                'quantity'    => (float) ($item['quantity'] ?? 1),
                'unit_price'  => (float) ($item['unit_price'] ?? 0),
                'vat_rate'    => (float) ($item['vat_rate'] ?? 21),
                'sort_order'  => $sortOrder++,
            ]);
        }

        AuditLog::log('create', 'invoices', $invoiceId, 'Factuur aangemaakt: ' . (Invoice::find($invoiceId)['invoice_number'] ?? (string) $invoiceId));
        Session::flash('success', 'Factuur is aangemaakt.');
        header('Location: ' . APP_URL . '/financien/facturen/' . $invoiceId); exit;
    }

    public function showInvoice(string $id): void
    {
        Auth::require();

        $data = Invoice::findWithItems((int) $id);
        if ($data['invoice'] === null) {
            Session::flash('error', 'Factuur niet gevonden.');
            header('Location: ' . APP_URL . '/financien/facturen'); exit;
        }

        $invoice = $data['invoice'];
        $items   = $data['items'];
        $totals  = Invoice::calculateTotals($items);

        $title = 'Financiën — Factuur ' . $invoice['invoice_number'];
        $view  = 'financien/invoices/show';
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    public function updateInvoiceStatus(string $id): void
    {
        Auth::require();
        CSRF::validateOrDie();

        $invoice = Invoice::find((int) $id);
        if ($invoice === null) {
            Session::flash('error', 'Factuur niet gevonden.');
            header('Location: ' . APP_URL . '/financien/facturen'); exit;
        }

        $allowed = ['concept', 'verzonden', 'betaald', 'te-laat', 'geannuleerd'];
        $status  = $_POST['status'] ?? '';

        if (!in_array($status, $allowed, true)) {
            Session::flash('error', 'Ongeldige status.');
            header('Location: ' . APP_URL . '/financien/facturen/' . $id); exit;
        }

        Invoice::updateStatus((int) $id, $status);
        AuditLog::log('update', 'invoices', (int) $id, 'Factuurstatus bijgewerkt naar: ' . $status);
        Session::flash('success', 'Status van de factuur is bijgewerkt.');
        header('Location: ' . APP_URL . '/financien/facturen/' . $id); exit;
    }

    public function deleteInvoice(string $id): void
    {
        Auth::require();
        CSRF::validateOrDie();

        $invoice = Invoice::find((int) $id);
        if ($invoice === null) {
            Session::flash('error', 'Factuur niet gevonden.');
            header('Location: ' . APP_URL . '/financien/facturen'); exit;
        }

        Invoice::delete((int) $id);
        AuditLog::log('delete', 'invoices', (int) $id, 'Factuur verwijderd: ' . $invoice['invoice_number']);
        Session::flash('success', 'Factuur "' . $invoice['invoice_number'] . '" is verwijderd.');
        header('Location: ' . APP_URL . '/financien/facturen'); exit;
    }

    public function printInvoice(string $id): void
    {
        Auth::require();

        $data = Invoice::findWithItems((int) $id);
        if ($data['invoice'] === null) {
            Session::flash('error', 'Factuur niet gevonden.');
            header('Location: ' . APP_URL . '/financien/facturen'); exit;
        }

        $invoice = $data['invoice'];
        $items   = $data['items'];
        $totals  = Invoice::calculateTotals($items);

        require APP_ROOT . '/app/Views/financien/invoices/print.php';
    }
}
