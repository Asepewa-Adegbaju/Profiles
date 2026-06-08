<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Session;
use App\Core\AuditLog;
use App\Models\Company;
use App\Models\Contact;
use App\Models\ContactLog;
use App\Models\Meeting;

class CrmController
{
    // ── Bedrijven ────────────────────────────────────────────────────────────

    public function index(): void
    {
        Auth::require();

        $status  = trim($_GET['status'] ?? '');
        $search  = trim($_GET['q'] ?? '');
        $counts  = Company::countByStatus();
        $total   = array_sum($counts);

        if ($search !== '') {
            $companies = Company::search($search);
        } else {
            $companies = Company::all($status);
        }

        $title = 'CRM — Bedrijven';
        $view  = 'crm/index';
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    public function createCompany(): void
    {
        Auth::require();

        $title = 'CRM — Nieuw bedrijf';
        $view  = 'crm/create';
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    public function storeCompany(): void
    {
        Auth::require();
        CSRF::validateOrDie();

        $name = trim($_POST['name'] ?? '');

        if ($name === '' || strlen($name) > 200) {
            Session::flash('error', 'Bedrijfsnaam is verplicht en mag maximaal 200 tekens bevatten.');
            header('Location: ' . APP_URL . '/crm/create'); exit;
        }

        $email = trim($_POST['email'] ?? '');
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::flash('error', 'Het opgegeven e-mailadres is ongeldig.');
            header('Location: ' . APP_URL . '/crm/create'); exit;
        }

        $allowedStatuses = ['lead', 'prospect', 'klant', 'inactief'];
        $status = in_array($_POST['status'] ?? '', $allowedStatuses, true) ? $_POST['status'] : 'lead';

        $id = Company::create([
            'name'        => $name,
            'sector'      => trim($_POST['sector'] ?? '') ?: null,
            'phone'       => trim($_POST['phone'] ?? '') ?: null,
            'email'       => $email ?: null,
            'website'     => trim($_POST['website'] ?? '') ?: null,
            'address'     => trim($_POST['address'] ?? '') ?: null,
            'city'        => trim($_POST['city'] ?? '') ?: null,
            'postal_code' => trim($_POST['postal_code'] ?? '') ?: null,
            'status'      => $status,
            'notes'       => trim($_POST['notes'] ?? '') ?: null,
            'created_by'  => Auth::id(),
        ]);

        AuditLog::log('create', 'companies', $id, 'Bedrijf aangemaakt: ' . $name);
        Session::flash('success', 'Bedrijf "' . $name . '" is aangemaakt.');
        header('Location: ' . APP_URL . '/crm/' . $id); exit;
    }

    public function showCompany(string $id): void
    {
        Auth::require();

        $company = Company::find((int) $id);
        if ($company === null) {
            Session::flash('error', 'Bedrijf niet gevonden.');
            header('Location: ' . APP_URL . '/crm'); exit;
        }

        $contacts = Contact::allByCompany((int) $id);
        $logs     = ContactLog::allByCompany((int) $id);
        $meetings = Meeting::allByCompany((int) $id);

        $title = 'CRM — ' . $company['name'];
        $view  = 'crm/show';
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    public function editCompany(string $id): void
    {
        Auth::require();

        $company = Company::find((int) $id);
        if ($company === null) {
            Session::flash('error', 'Bedrijf niet gevonden.');
            header('Location: ' . APP_URL . '/crm'); exit;
        }

        $title = 'CRM — Bedrijf bewerken';
        $view  = 'crm/edit';
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    public function updateCompany(string $id): void
    {
        Auth::require();
        CSRF::validateOrDie();

        $company = Company::find((int) $id);
        if ($company === null) {
            Session::flash('error', 'Bedrijf niet gevonden.');
            header('Location: ' . APP_URL . '/crm'); exit;
        }

        $name = trim($_POST['name'] ?? '');
        if ($name === '' || strlen($name) > 200) {
            Session::flash('error', 'Bedrijfsnaam is verplicht en mag maximaal 200 tekens bevatten.');
            header('Location: ' . APP_URL . '/crm/' . $id . '/edit'); exit;
        }

        $email = trim($_POST['email'] ?? '');
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::flash('error', 'Het opgegeven e-mailadres is ongeldig.');
            header('Location: ' . APP_URL . '/crm/' . $id . '/edit'); exit;
        }

        $allowedStatuses = ['lead', 'prospect', 'klant', 'inactief'];
        $status = in_array($_POST['status'] ?? '', $allowedStatuses, true) ? $_POST['status'] : 'lead';

        Company::update((int) $id, [
            'name'        => $name,
            'sector'      => trim($_POST['sector'] ?? '') ?: null,
            'phone'       => trim($_POST['phone'] ?? '') ?: null,
            'email'       => $email ?: null,
            'website'     => trim($_POST['website'] ?? '') ?: null,
            'address'     => trim($_POST['address'] ?? '') ?: null,
            'city'        => trim($_POST['city'] ?? '') ?: null,
            'postal_code' => trim($_POST['postal_code'] ?? '') ?: null,
            'status'      => $status,
            'notes'       => trim($_POST['notes'] ?? '') ?: null,
        ]);

        AuditLog::log('update', 'companies', (int) $id, 'Bedrijf bijgewerkt: ' . $name);
        Session::flash('success', 'Bedrijf "' . $name . '" is bijgewerkt.');
        header('Location: ' . APP_URL . '/crm/' . $id); exit;
    }

    public function deleteCompany(string $id): void
    {
        Auth::require();
        CSRF::validateOrDie();

        $company = Company::find((int) $id);
        if ($company === null) {
            Session::flash('error', 'Bedrijf niet gevonden.');
            header('Location: ' . APP_URL . '/crm'); exit;
        }

        Company::delete((int) $id);
        AuditLog::log('delete', 'companies', (int) $id, 'Bedrijf verwijderd: ' . $company['name']);
        Session::flash('success', 'Bedrijf "' . $company['name'] . '" is verwijderd.');
        header('Location: ' . APP_URL . '/crm'); exit;
    }

    // ── Contactpersonen ──────────────────────────────────────────────────────

    public function storeContact(string $companyId): void
    {
        Auth::require();
        CSRF::validateOrDie();

        $company = Company::find((int) $companyId);
        if ($company === null) {
            Session::flash('error', 'Bedrijf niet gevonden.');
            header('Location: ' . APP_URL . '/crm'); exit;
        }

        $name = trim($_POST['name'] ?? '');
        if ($name === '' || strlen($name) > 150) {
            Session::flash('error', 'Naam contactpersoon is verplicht en mag maximaal 150 tekens bevatten.');
            header('Location: ' . APP_URL . '/crm/' . $companyId); exit;
        }

        $email = trim($_POST['email'] ?? '');
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::flash('error', 'Het opgegeven e-mailadres van de contactpersoon is ongeldig.');
            header('Location: ' . APP_URL . '/crm/' . $companyId); exit;
        }

        $contactId = Contact::create([
            'company_id' => (int) $companyId,
            'name'       => $name,
            'function'   => trim($_POST['function'] ?? '') ?: null,
            'email'      => $email ?: null,
            'phone'      => trim($_POST['phone'] ?? '') ?: null,
            'notes'      => trim($_POST['notes'] ?? '') ?: null,
        ]);

        AuditLog::log('create', 'contacts', $contactId, 'Contactpersoon aangemaakt: ' . $name . ' bij ' . $company['name']);
        Session::flash('success', 'Contactpersoon "' . $name . '" is toegevoegd.');
        header('Location: ' . APP_URL . '/crm/' . $companyId); exit;
    }

    public function deleteContact(string $id): void
    {
        Auth::require();
        CSRF::validateOrDie();

        $contact = Contact::find((int) $id);
        if ($contact === null) {
            Session::flash('error', 'Contactpersoon niet gevonden.');
            header('Location: ' . APP_URL . '/crm'); exit;
        }

        $companyId = $contact['company_id'];
        Contact::delete((int) $id);
        AuditLog::log('delete', 'contacts', (int) $id, 'Contactpersoon verwijderd: ' . $contact['name']);
        Session::flash('success', 'Contactpersoon "' . $contact['name'] . '" is verwijderd.');
        header('Location: ' . APP_URL . '/crm/' . $companyId); exit;
    }

    // ── Contact log ──────────────────────────────────────────────────────────

    public function storeLog(string $companyId): void
    {
        Auth::require();
        CSRF::validateOrDie();

        $company = Company::find((int) $companyId);
        if ($company === null) {
            Session::flash('error', 'Bedrijf niet gevonden.');
            header('Location: ' . APP_URL . '/crm'); exit;
        }

        $notes = trim($_POST['notes'] ?? '');
        if ($notes === '') {
            Session::flash('error', 'Notities zijn verplicht voor een logboekregel.');
            header('Location: ' . APP_URL . '/crm/' . $companyId); exit;
        }

        $allowedTypes = ['telefoongesprek', 'email', 'meeting', 'overig'];
        $type = in_array($_POST['type'] ?? '', $allowedTypes, true) ? $_POST['type'] : 'overig';

        $loggedAt = trim($_POST['logged_at'] ?? '');
        if ($loggedAt === '') {
            $loggedAt = date('Y-m-d H:i:s');
        } else {
            $loggedAt = date('Y-m-d H:i:s', strtotime($loggedAt));
        }

        $contactId = !empty($_POST['contact_id']) ? (int) $_POST['contact_id'] : null;

        $logId = ContactLog::create([
            'company_id' => (int) $companyId,
            'contact_id' => $contactId,
            'user_id'    => Auth::id(),
            'type'       => $type,
            'notes'      => $notes,
            'logged_at'  => $loggedAt,
        ]);

        AuditLog::log('create', 'contact_log', $logId, 'Log toegevoegd voor bedrijf: ' . $company['name']);
        Session::flash('success', 'Logboekregel is toegevoegd.');
        header('Location: ' . APP_URL . '/crm/' . $companyId); exit;
    }

    // ── Afspraken ────────────────────────────────────────────────────────────

    public function meetings(): void
    {
        Auth::require();

        $upcomingMeetings = Meeting::upcoming(50);

        $title = 'CRM — Afspraken';
        $view  = 'crm/meetings';
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    public function createMeeting(string $companyId): void
    {
        Auth::require();

        $company = Company::find((int) $companyId);
        if ($company === null) {
            Session::flash('error', 'Bedrijf niet gevonden.');
            header('Location: ' . APP_URL . '/crm'); exit;
        }

        $contacts = Contact::allByCompany((int) $companyId);

        $title = 'CRM — Nieuwe afspraak';
        $view  = 'crm/meeting_create';
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    public function storeMeeting(string $companyId): void
    {
        Auth::require();
        CSRF::validateOrDie();

        $company = Company::find((int) $companyId);
        if ($company === null) {
            Session::flash('error', 'Bedrijf niet gevonden.');
            header('Location: ' . APP_URL . '/crm'); exit;
        }

        $title = trim($_POST['title'] ?? '');
        if ($title === '' || strlen($title) > 200) {
            Session::flash('error', 'Titel is verplicht en mag maximaal 200 tekens bevatten.');
            header('Location: ' . APP_URL . '/crm/' . $companyId . '/meeting'); exit;
        }

        $meetingDate = trim($_POST['meeting_date'] ?? '');
        if ($meetingDate === '') {
            Session::flash('error', 'Datum en tijd zijn verplicht.');
            header('Location: ' . APP_URL . '/crm/' . $companyId . '/meeting'); exit;
        }

        $meetingDate = date('Y-m-d H:i:s', strtotime($meetingDate));

        $allowedStatuses = ['gepland', 'bevestigd', 'geweest', 'afgeslagen'];
        $status = in_array($_POST['status'] ?? '', $allowedStatuses, true) ? $_POST['status'] : 'gepland';

        $contactId = !empty($_POST['contact_id']) ? (int) $_POST['contact_id'] : null;

        $meetingId = Meeting::create([
            'company_id'   => (int) $companyId,
            'contact_id'   => $contactId,
            'user_id'      => Auth::id(),
            'title'        => $title,
            'meeting_date' => $meetingDate,
            'location'     => trim($_POST['location'] ?? '') ?: null,
            'status'       => $status,
            'notes'        => trim($_POST['notes'] ?? '') ?: null,
        ]);

        AuditLog::log('create', 'meetings', $meetingId, 'Afspraak aangemaakt: ' . $title . ' voor ' . $company['name']);
        Session::flash('success', 'Afspraak "' . $title . '" is aangemaakt.');
        header('Location: ' . APP_URL . '/crm/' . $companyId); exit;
    }

    public function updateMeetingStatus(string $id): void
    {
        Auth::require();
        CSRF::validateOrDie();

        $meeting = Meeting::find((int) $id);
        if ($meeting === null) {
            Session::flash('error', 'Afspraak niet gevonden.');
            header('Location: ' . APP_URL . '/crm/meetings'); exit;
        }

        $allowedStatuses = ['gepland', 'bevestigd', 'geweest', 'afgeslagen'];
        $status = $_POST['status'] ?? '';

        if (!in_array($status, $allowedStatuses, true)) {
            Session::flash('error', 'Ongeldige status.');
            header('Location: ' . APP_URL . '/crm/meetings'); exit;
        }

        Meeting::updateStatus((int) $id, $status);
        AuditLog::log('update', 'meetings', (int) $id, 'Afspraatstatus bijgewerkt naar: ' . $status);
        Session::flash('success', 'Status van de afspraak is bijgewerkt.');
        header('Location: ' . APP_URL . '/crm/meetings'); exit;
    }

    // ── CSV Import ───────────────────────────────────────────────────────────

    public function showImport(): void
    {
        Auth::require();

        $title = 'CRM — CSV Importeren';
        $view  = 'crm/import';
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    public function processImport(): void
    {
        Auth::require();
        CSRF::validateOrDie();

        if (empty($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            Session::flash('error', 'Geen geldig CSV-bestand geüpload.');
            header('Location: ' . APP_URL . '/crm/import'); exit;
        }

        $file = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($file, 'r');
        if ($handle === false) {
            Session::flash('error', 'Kan het CSV-bestand niet openen.');
            header('Location: ' . APP_URL . '/crm/import'); exit;
        }

        // Sla de headerregel over
        fgetcsv($handle);

        $imported = 0;
        $skipped  = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (empty($row) || (count($row) === 1 && trim($row[0]) === '')) {
                continue;
            }

            $name = isset($row[0]) ? trim($row[0]) : '';
            if ($name === '') {
                $skipped++;
                continue;
            }

            $allowedStatuses = ['lead', 'prospect', 'klant', 'inactief'];
            $status = isset($row[7]) && in_array(trim($row[7]), $allowedStatuses, true)
                ? trim($row[7])
                : 'lead';

            $email = isset($row[3]) ? trim($row[3]) : '';
            if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $email = '';
            }

            $id = Company::create([
                'name'        => $name,
                'sector'      => isset($row[1]) ? (trim($row[1]) ?: null) : null,
                'phone'       => isset($row[2]) ? (trim($row[2]) ?: null) : null,
                'email'       => $email ?: null,
                'website'     => isset($row[4]) ? (trim($row[4]) ?: null) : null,
                'city'        => isset($row[5]) ? (trim($row[5]) ?: null) : null,
                'postal_code' => isset($row[6]) ? (trim($row[6]) ?: null) : null,
                'status'      => $status,
                'created_by'  => Auth::id(),
            ]);

            AuditLog::log('create', 'companies', $id, 'Bedrijf geïmporteerd via CSV: ' . $name);
            $imported++;
        }

        fclose($handle);

        Session::flash('success', $imported . ' bedrijven geïmporteerd' . ($skipped > 0 ? ', ' . $skipped . ' overgeslagen (lege naam).' : '.'));
        header('Location: ' . APP_URL . '/crm'); exit;
    }
}
