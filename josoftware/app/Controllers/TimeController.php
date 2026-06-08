<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Session;
use App\Core\AuditLog;
use App\Models\TimeEntry;
use App\Models\KmEntry;

class TimeController
{
    // ─────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────

    private function resolveMonth(): string
    {
        $month = $_GET['month'] ?? date('Y-m');
        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            $month = date('Y-m');
        }
        return $month;
    }

    private function monthLabel(string $month): string
    {
        $maanden = [
            '', 'januari', 'februari', 'maart', 'april', 'mei', 'juni',
            'juli', 'augustus', 'september', 'oktober', 'november', 'december',
        ];
        $m = (int) substr($month, 5, 2);
        $y = substr($month, 0, 4);
        return $maanden[$m] . ' ' . $y;
    }

    private function prevMonth(string $month): string
    {
        return date('Y-m', strtotime($month . '-01 -1 month'));
    }

    private function nextMonth(string $month): string
    {
        return date('Y-m', strtotime($month . '-01 +1 month'));
    }

    // ─────────────────────────────────────────────
    // Uren: index
    // ─────────────────────────────────────────────

    public function index(): void
    {
        Auth::require();

        $userId  = Auth::id();
        $month   = $this->resolveMonth();
        $label   = $this->monthLabel($month);
        $prev    = $this->prevMonth($month);
        $next    = $this->nextMonth($month);
        $entries = TimeEntry::allByUser($userId, $month);
        $totals  = TimeEntry::monthlyTotal($userId, $month);

        $title = 'Uren & Kilometers';
        $view  = 'time/index';

        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    // ─────────────────────────────────────────────
    // Uren: create form
    // ─────────────────────────────────────────────

    public function create(): void
    {
        Auth::require();

        $projects  = TimeEntry::getProjects();
        $companies = TimeEntry::getCompanies();

        $title = 'Nieuwe urenregistratie';
        $view  = 'time/create';

        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    // ─────────────────────────────────────────────
    // Uren: store
    // ─────────────────────────────────────────────

    public function store(): void
    {
        Auth::require();
        CSRF::validateOrDie();

        $userId      = Auth::id();
        $entryDate   = trim($_POST['entry_date']  ?? '');
        $description = trim($_POST['description'] ?? '');
        $projectId   = (int) ($_POST['project_id']  ?? 0);
        $companyId   = (int) ($_POST['company_id']  ?? 0);
        $hours       = (float) str_replace(',', '.', $_POST['hours'] ?? '0');
        $hourlyRate  = (float) str_replace(',', '.', $_POST['hourly_rate'] ?? '0');
        $type        = $_POST['type'] ?? 'zakelijk';
        $billable    = isset($_POST['billable']) ? 1 : 0;

        // Validation
        if ($entryDate === '' || !strtotime($entryDate)) {
            Session::flash('error', 'Voer een geldige datum in.');
            header('Location: ' . APP_URL . '/uren/nieuw');
            exit;
        }

        if ($description === '') {
            Session::flash('error', 'Omschrijving is verplicht.');
            header('Location: ' . APP_URL . '/uren/nieuw');
            exit;
        }

        if (strlen($description) > 500) {
            Session::flash('error', 'Omschrijving mag maximaal 500 tekens bevatten.');
            header('Location: ' . APP_URL . '/uren/nieuw');
            exit;
        }

        if ($hours <= 0 || $hours > 24) {
            Session::flash('error', 'Uren moet tussen 0.25 en 24 liggen.');
            header('Location: ' . APP_URL . '/uren/nieuw');
            exit;
        }

        if (!in_array($type, ['zakelijk', 'prive'], true)) {
            $type = 'zakelijk';
        }

        $id = TimeEntry::create([
            'user_id'     => $userId,
            'project_id'  => $projectId ?: null,
            'company_id'  => $companyId ?: null,
            'entry_date'  => $entryDate,
            'description' => $description,
            'hours'       => $hours,
            'hourly_rate' => $hourlyRate,
            'type'        => $type,
            'billable'    => $billable,
        ]);

        AuditLog::log('create', 'time_entries', $id, 'Uren toegevoegd');

        Session::flash('success', 'Uren succesvol geregistreerd.');
        header('Location: ' . APP_URL . '/uren');
        exit;
    }

    // ─────────────────────────────────────────────
    // Uren: edit form
    // ─────────────────────────────────────────────

    public function edit(string $id): void
    {
        Auth::require();

        $entry = TimeEntry::find((int) $id);

        if ($entry === null) {
            Session::flash('error', 'Urenboeking niet gevonden.');
            header('Location: ' . APP_URL . '/uren');
            exit;
        }

        $projects  = TimeEntry::getProjects();
        $companies = TimeEntry::getCompanies();

        $title = 'Uren bewerken';
        $view  = 'time/edit';

        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    // ─────────────────────────────────────────────
    // Uren: update
    // ─────────────────────────────────────────────

    public function update(string $id): void
    {
        Auth::require();
        CSRF::validateOrDie();

        $intId = (int) $id;
        $entry = TimeEntry::find($intId);

        if ($entry === null) {
            Session::flash('error', 'Urenboeking niet gevonden.');
            header('Location: ' . APP_URL . '/uren');
            exit;
        }

        $entryDate   = trim($_POST['entry_date']  ?? '');
        $description = trim($_POST['description'] ?? '');
        $projectId   = (int) ($_POST['project_id']  ?? 0);
        $companyId   = (int) ($_POST['company_id']  ?? 0);
        $hours       = (float) str_replace(',', '.', $_POST['hours'] ?? '0');
        $hourlyRate  = (float) str_replace(',', '.', $_POST['hourly_rate'] ?? '0');
        $type        = $_POST['type'] ?? 'zakelijk';
        $billable    = isset($_POST['billable']) ? 1 : 0;

        // Validation
        if ($entryDate === '' || !strtotime($entryDate)) {
            Session::flash('error', 'Voer een geldige datum in.');
            header('Location: ' . APP_URL . '/uren/' . $intId . '/edit');
            exit;
        }

        if ($description === '') {
            Session::flash('error', 'Omschrijving is verplicht.');
            header('Location: ' . APP_URL . '/uren/' . $intId . '/edit');
            exit;
        }

        if (strlen($description) > 500) {
            Session::flash('error', 'Omschrijving mag maximaal 500 tekens bevatten.');
            header('Location: ' . APP_URL . '/uren/' . $intId . '/edit');
            exit;
        }

        if ($hours <= 0 || $hours > 24) {
            Session::flash('error', 'Uren moet tussen 0.25 en 24 liggen.');
            header('Location: ' . APP_URL . '/uren/' . $intId . '/edit');
            exit;
        }

        if (!in_array($type, ['zakelijk', 'prive'], true)) {
            $type = 'zakelijk';
        }

        TimeEntry::update($intId, [
            'project_id'  => $projectId ?: null,
            'company_id'  => $companyId ?: null,
            'entry_date'  => $entryDate,
            'description' => $description,
            'hours'       => $hours,
            'hourly_rate' => $hourlyRate,
            'type'        => $type,
            'billable'    => $billable,
        ]);

        AuditLog::log('update', 'time_entries', $intId, 'Uren bijgewerkt');

        Session::flash('success', 'Uren succesvol bijgewerkt.');
        header('Location: ' . APP_URL . '/uren');
        exit;
    }

    // ─────────────────────────────────────────────
    // Uren: delete
    // ─────────────────────────────────────────────

    public function delete(string $id): void
    {
        Auth::require();
        CSRF::validateOrDie();

        $intId = (int) $id;
        $entry = TimeEntry::find($intId);

        if ($entry === null) {
            Session::flash('error', 'Urenboeking niet gevonden.');
            header('Location: ' . APP_URL . '/uren');
            exit;
        }

        TimeEntry::delete($intId);
        AuditLog::log('delete', 'time_entries', $intId, 'Uren verwijderd');

        Session::flash('success', 'Urenboeking succesvol verwijderd.');
        header('Location: ' . APP_URL . '/uren');
        exit;
    }

    // ─────────────────────────────────────────────
    // Uren: export CSV
    // ─────────────────────────────────────────────

    public function exportCsv(): void
    {
        Auth::require();

        $userId  = Auth::id();
        $month   = $this->resolveMonth();
        $entries = TimeEntry::allByUser($userId, $month);

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="uren-' . $month . '.csv"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo "\xEF\xBB\xBF"; // UTF-8 BOM for Excel

        $out = fopen('php://output', 'w');
        fputcsv($out, ['Datum', 'Omschrijving', 'Project', 'Bedrijf', 'Uren', 'Uurtarief', 'Bedrag', 'Type', 'Factureerbaar'], ';');

        foreach ($entries as $e) {
            $amount = round((float) $e['hours'] * (float) $e['hourly_rate'], 2);
            fputcsv($out, [
                $e['entry_date'],
                $e['description'],
                $e['project_name'] ?? '',
                $e['company_name'] ?? '',
                number_format((float) $e['hours'], 2, '.', ''),
                number_format((float) $e['hourly_rate'], 2, '.', ''),
                number_format($amount, 2, '.', ''),
                $e['type'],
                $e['billable'] ? 'Ja' : 'Nee',
            ], ';');
        }

        fclose($out);
        exit;
    }

    // ─────────────────────────────────────────────
    // Kilometers: index
    // ─────────────────────────────────────────────

    public function kmIndex(): void
    {
        Auth::require();

        $userId  = Auth::id();
        $month   = $this->resolveMonth();
        $label   = $this->monthLabel($month);
        $prev    = $this->prevMonth($month);
        $next    = $this->nextMonth($month);
        $entries = KmEntry::allByUser($userId, $month);
        $totals  = KmEntry::monthlyTotal($userId, $month);

        $title = 'Uren & Kilometers';
        $view  = 'time/km';

        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    // ─────────────────────────────────────────────
    // Kilometers: create form
    // ─────────────────────────────────────────────

    public function kmCreate(): void
    {
        Auth::require();

        $title = 'Kilometers registreren';
        $view  = 'time/km_create';

        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    // ─────────────────────────────────────────────
    // Kilometers: store
    // ─────────────────────────────────────────────

    public function kmStore(): void
    {
        Auth::require();
        CSRF::validateOrDie();

        $userId       = Auth::id();
        $entryDate    = trim($_POST['entry_date']    ?? '');
        $fromLocation = trim($_POST['from_location'] ?? '');
        $toLocation   = trim($_POST['to_location']   ?? '');
        $km           = (float) str_replace(',', '.', $_POST['km']         ?? '0');
        $purpose      = trim($_POST['purpose']       ?? '');
        $type         = $_POST['type']               ?? 'zakelijk';
        $ratePerKm    = (float) str_replace(',', '.', $_POST['rate_per_km'] ?? '0.230');

        // Validation
        if ($entryDate === '' || !strtotime($entryDate)) {
            Session::flash('error', 'Voer een geldige datum in.');
            header('Location: ' . APP_URL . '/kilometers/nieuw');
            exit;
        }

        if ($fromLocation === '') {
            Session::flash('error', 'Vertreklocatie is verplicht.');
            header('Location: ' . APP_URL . '/kilometers/nieuw');
            exit;
        }

        if ($toLocation === '') {
            Session::flash('error', 'Aankomstlocatie is verplicht.');
            header('Location: ' . APP_URL . '/kilometers/nieuw');
            exit;
        }

        if ($km <= 0) {
            Session::flash('error', 'Kilometers moet groter zijn dan 0.');
            header('Location: ' . APP_URL . '/kilometers/nieuw');
            exit;
        }

        if ($purpose === '') {
            Session::flash('error', 'Doel is verplicht.');
            header('Location: ' . APP_URL . '/kilometers/nieuw');
            exit;
        }

        if (!in_array($type, ['zakelijk', 'prive'], true)) {
            $type = 'zakelijk';
        }

        if ($ratePerKm <= 0) {
            $ratePerKm = 0.230;
        }

        $id = KmEntry::create([
            'user_id'       => $userId,
            'entry_date'    => $entryDate,
            'from_location' => $fromLocation,
            'to_location'   => $toLocation,
            'km'            => $km,
            'purpose'       => $purpose,
            'type'          => $type,
            'rate_per_km'   => $ratePerKm,
        ]);

        AuditLog::log('create', 'km_entries', $id, 'Kilometers toegevoegd');

        Session::flash('success', 'Kilometers succesvol geregistreerd.');
        header('Location: ' . APP_URL . '/kilometers');
        exit;
    }

    // ─────────────────────────────────────────────
    // Kilometers: edit form
    // ─────────────────────────────────────────────

    public function kmEdit(string $id): void
    {
        Auth::require();

        $entry = KmEntry::find((int) $id);

        if ($entry === null) {
            Session::flash('error', 'Kilometerregistratie niet gevonden.');
            header('Location: ' . APP_URL . '/kilometers');
            exit;
        }

        $title = 'Kilometers bewerken';
        $view  = 'time/km_edit';

        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    // ─────────────────────────────────────────────
    // Kilometers: update
    // ─────────────────────────────────────────────

    public function kmUpdate(string $id): void
    {
        Auth::require();
        CSRF::validateOrDie();

        $intId = (int) $id;
        $entry = KmEntry::find($intId);

        if ($entry === null) {
            Session::flash('error', 'Kilometerregistratie niet gevonden.');
            header('Location: ' . APP_URL . '/kilometers');
            exit;
        }

        $entryDate    = trim($_POST['entry_date']    ?? '');
        $fromLocation = trim($_POST['from_location'] ?? '');
        $toLocation   = trim($_POST['to_location']   ?? '');
        $km           = (float) str_replace(',', '.', $_POST['km']         ?? '0');
        $purpose      = trim($_POST['purpose']       ?? '');
        $type         = $_POST['type']               ?? 'zakelijk';
        $ratePerKm    = (float) str_replace(',', '.', $_POST['rate_per_km'] ?? '0.230');

        // Validation
        if ($entryDate === '' || !strtotime($entryDate)) {
            Session::flash('error', 'Voer een geldige datum in.');
            header('Location: ' . APP_URL . '/kilometers/' . $intId . '/edit');
            exit;
        }

        if ($fromLocation === '') {
            Session::flash('error', 'Vertreklocatie is verplicht.');
            header('Location: ' . APP_URL . '/kilometers/' . $intId . '/edit');
            exit;
        }

        if ($toLocation === '') {
            Session::flash('error', 'Aankomstlocatie is verplicht.');
            header('Location: ' . APP_URL . '/kilometers/' . $intId . '/edit');
            exit;
        }

        if ($km <= 0) {
            Session::flash('error', 'Kilometers moet groter zijn dan 0.');
            header('Location: ' . APP_URL . '/kilometers/' . $intId . '/edit');
            exit;
        }

        if ($purpose === '') {
            Session::flash('error', 'Doel is verplicht.');
            header('Location: ' . APP_URL . '/kilometers/' . $intId . '/edit');
            exit;
        }

        if (!in_array($type, ['zakelijk', 'prive'], true)) {
            $type = 'zakelijk';
        }

        if ($ratePerKm <= 0) {
            $ratePerKm = 0.230;
        }

        KmEntry::update($intId, [
            'entry_date'    => $entryDate,
            'from_location' => $fromLocation,
            'to_location'   => $toLocation,
            'km'            => $km,
            'purpose'       => $purpose,
            'type'          => $type,
            'rate_per_km'   => $ratePerKm,
        ]);

        AuditLog::log('update', 'km_entries', $intId, 'Kilometers bijgewerkt');

        Session::flash('success', 'Kilometerregistratie succesvol bijgewerkt.');
        header('Location: ' . APP_URL . '/kilometers');
        exit;
    }

    // ─────────────────────────────────────────────
    // Kilometers: delete
    // ─────────────────────────────────────────────

    public function kmDelete(string $id): void
    {
        Auth::require();
        CSRF::validateOrDie();

        $intId = (int) $id;
        $entry = KmEntry::find($intId);

        if ($entry === null) {
            Session::flash('error', 'Kilometerregistratie niet gevonden.');
            header('Location: ' . APP_URL . '/kilometers');
            exit;
        }

        KmEntry::delete($intId);
        AuditLog::log('delete', 'km_entries', $intId, 'Kilometers verwijderd');

        Session::flash('success', 'Kilometerregistratie succesvol verwijderd.');
        header('Location: ' . APP_URL . '/kilometers');
        exit;
    }
}
