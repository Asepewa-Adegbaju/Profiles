<?php
use App\Core\CSRF;

$statusBadge = function(string $s): string {
    $map = [
        'lead'     => 'badge badge-gray',
        'prospect' => 'badge badge-blue',
        'klant'    => 'badge badge-green',
        'inactief' => 'badge badge-red',
    ];
    return $map[$s] ?? 'badge badge-gray';
};

$meetingStatusBadge = function(string $s): string {
    $map = [
        'gepland'   => 'badge badge-blue',
        'bevestigd' => 'badge badge-green',
        'geweest'   => 'badge badge-gray',
        'afgeslagen'=> 'badge badge-red',
    ];
    return $map[$s] ?? 'badge badge-gray';
};

$logTypeBadge = function(string $t): string {
    $map = [
        'telefoongesprek' => 'badge badge-blue',
        'email'           => 'badge badge-orange',
        'meeting'         => 'badge badge-green',
        'overig'          => 'badge badge-gray',
    ];
    return $map[$t] ?? 'badge badge-gray';
};
?>

<!-- ── Paginakop ──────────────────────────────────────────────────────────── -->
<div class="flex justify-between items-center mb-2">
    <div class="flex items-center gap-2">
        <a href="<?= APP_URL ?>/crm" class="btn btn-secondary btn-sm">← Terug</a>
        <h2 style="font-size:1.1rem;font-weight:700;">
            <?= htmlspecialchars($company['name'], ENT_QUOTES, 'UTF-8') ?>
        </h2>
        <span class="<?= $statusBadge($company['status']) ?>">
            <?= htmlspecialchars(ucfirst($company['status']), ENT_QUOTES, 'UTF-8') ?>
        </span>
    </div>
    <div class="flex gap-1">
        <a href="<?= APP_URL ?>/crm/<?= (int)$company['id'] ?>/meeting" class="btn btn-secondary btn-sm">+ Afspraak</a>
        <a href="<?= APP_URL ?>/crm/<?= (int)$company['id'] ?>/edit" class="btn btn-primary btn-sm">Bewerken</a>
    </div>
</div>

<!-- ── Bedrijfsgegevens ───────────────────────────────────────────────────── -->
<div class="card mb-2">
    <div class="card-header">
        <span class="card-title">Bedrijfsgegevens</span>
    </div>
    <div class="card-body">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem 1.5rem;">
            <div>
                <span class="text-muted text-sm">Sector</span>
                <div><?= htmlspecialchars($company['sector'] ?? '—', ENT_QUOTES, 'UTF-8') ?></div>
            </div>
            <div>
                <span class="text-muted text-sm">Telefoon</span>
                <div>
                    <?php if ($company['phone']): ?>
                        <a href="tel:<?= htmlspecialchars($company['phone'], ENT_QUOTES, 'UTF-8') ?>">
                            <?= htmlspecialchars($company['phone'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    <?php else: ?>—<?php endif; ?>
                </div>
            </div>
            <div>
                <span class="text-muted text-sm">E-mailadres</span>
                <div>
                    <?php if ($company['email']): ?>
                        <a href="mailto:<?= htmlspecialchars($company['email'], ENT_QUOTES, 'UTF-8') ?>">
                            <?= htmlspecialchars($company['email'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    <?php else: ?>—<?php endif; ?>
                </div>
            </div>
            <div>
                <span class="text-muted text-sm">Website</span>
                <div>
                    <?php if ($company['website']): ?>
                        <a href="<?= htmlspecialchars($company['website'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer">
                            <?= htmlspecialchars($company['website'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    <?php else: ?>—<?php endif; ?>
                </div>
            </div>
            <div>
                <span class="text-muted text-sm">Adres</span>
                <div><?= htmlspecialchars($company['address'] ?? '—', ENT_QUOTES, 'UTF-8') ?></div>
            </div>
            <div>
                <span class="text-muted text-sm">Stad / Postcode</span>
                <div>
                    <?php
                    $loc = array_filter([$company['city'] ?? '', $company['postal_code'] ?? '']);
                    echo $loc ? htmlspecialchars(implode(', ', $loc), ENT_QUOTES, 'UTF-8') : '—';
                    ?>
                </div>
            </div>
            <?php if ($company['notes']): ?>
            <div style="grid-column:1 / -1;">
                <span class="text-muted text-sm">Notities</span>
                <div style="white-space:pre-wrap;"><?= htmlspecialchars($company['notes'], ENT_QUOTES, 'UTF-8') ?></div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ── Contactpersonen ───────────────────────────────────────────────────── -->
<div class="card mb-2">
    <div class="card-header">
        <span class="card-title">Contactpersonen</span>
    </div>
    <?php if (!empty($contacts)): ?>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Naam</th>
                    <th>Functie</th>
                    <th>E-mail</th>
                    <th>Telefoon</th>
                    <th>Acties</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($contacts as $contact): ?>
                <tr>
                    <td class="font-bold"><?= htmlspecialchars($contact['name'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="text-muted text-sm"><?= htmlspecialchars($contact['function'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="text-sm">
                        <?php if ($contact['email']): ?>
                            <a href="mailto:<?= htmlspecialchars($contact['email'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($contact['email'], ENT_QUOTES, 'UTF-8') ?></a>
                        <?php else: ?>—<?php endif; ?>
                    </td>
                    <td class="text-sm">
                        <?php if ($contact['phone']): ?>
                            <a href="tel:<?= htmlspecialchars($contact['phone'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($contact['phone'], ENT_QUOTES, 'UTF-8') ?></a>
                        <?php else: ?>—<?php endif; ?>
                    </td>
                    <td>
                        <form method="POST" action="<?= APP_URL ?>/crm/contact/<?= (int)$contact['id'] ?>/delete" style="display:inline;" onsubmit="return confirm('Contactpersoon verwijderen?');">
                            <?= CSRF::field() ?>
                            <button type="submit" class="btn btn-danger btn-sm">Verwijderen</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
        <div class="card-body text-muted text-sm">Nog geen contactpersonen toegevoegd.</div>
    <?php endif; ?>

    <!-- Inline toevoegformulier -->
    <div class="card-body" style="border-top:1px solid var(--border);padding-top:1rem;">
        <p class="font-bold text-sm" style="margin-bottom:.75rem;">Contactpersoon toevoegen</p>
        <form method="POST" action="<?= APP_URL ?>/crm/<?= (int)$company['id'] ?>/contact">
            <?= CSRF::field() ?>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="contact_name">Naam <span style="color:var(--danger);">*</span></label>
                    <input type="text" id="contact_name" name="name" class="form-input" maxlength="150" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="contact_function">Functie</label>
                    <input type="text" id="contact_function" name="function" class="form-input" maxlength="100">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="contact_email">E-mailadres</label>
                    <input type="email" id="contact_email" name="email" class="form-input" maxlength="150">
                </div>
                <div class="form-group">
                    <label class="form-label" for="contact_phone">Telefoon</label>
                    <input type="text" id="contact_phone" name="phone" class="form-input" maxlength="30">
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Contactpersoon toevoegen</button>
        </form>
    </div>
</div>

<!-- ── Contact logboek ───────────────────────────────────────────────────── -->
<div class="card mb-2">
    <div class="card-header">
        <span class="card-title">Contactlogboek</span>
    </div>
    <?php if (!empty($logs)): ?>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Datum</th>
                    <th>Door</th>
                    <th>Contact</th>
                    <th>Notities</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                <tr>
                    <td>
                        <span class="<?= $logTypeBadge($log['type']) ?>">
                            <?= htmlspecialchars(ucfirst($log['type']), ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </td>
                    <td class="text-sm text-muted">
                        <?= htmlspecialchars(date('d-m-Y H:i', strtotime($log['logged_at'])), ENT_QUOTES, 'UTF-8') ?>
                    </td>
                    <td class="text-sm"><?= htmlspecialchars($log['user_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="text-sm"><?= htmlspecialchars($log['contact_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td style="white-space:pre-wrap;max-width:340px;"><?= htmlspecialchars($log['notes'], ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
        <div class="card-body text-muted text-sm">Nog geen logboekregels.</div>
    <?php endif; ?>

    <!-- Log toevoegen -->
    <div class="card-body" style="border-top:1px solid var(--border);padding-top:1rem;">
        <p class="font-bold text-sm" style="margin-bottom:.75rem;">Contactmoment registreren</p>
        <form method="POST" action="<?= APP_URL ?>/crm/<?= (int)$company['id'] ?>/log">
            <?= CSRF::field() ?>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="log_type">Type</label>
                    <select id="log_type" name="type" class="form-select">
                        <option value="telefoongesprek">Telefoongesprek</option>
                        <option value="email">E-mail</option>
                        <option value="meeting">Meeting</option>
                        <option value="overig">Overig</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="log_contact">Contactpersoon</label>
                    <select id="log_contact" name="contact_id" class="form-select">
                        <option value="">— geen —</option>
                        <?php foreach ($contacts as $contact): ?>
                            <option value="<?= (int)$contact['id'] ?>">
                                <?= htmlspecialchars($contact['name'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="log_date">Datum &amp; tijd</label>
                    <input
                        type="datetime-local"
                        id="log_date"
                        name="logged_at"
                        class="form-input"
                        value="<?= date('Y-m-d\TH:i') ?>"
                    >
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" for="log_notes">Notities <span style="color:var(--danger);">*</span></label>
                <textarea id="log_notes" name="notes" class="form-textarea" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Loggen</button>
        </form>
    </div>
</div>

<!-- ── Afspraken ─────────────────────────────────────────────────────────── -->
<div class="card">
    <div class="card-header flex justify-between items-center">
        <span class="card-title">Afspraken</span>
        <a href="<?= APP_URL ?>/crm/<?= (int)$company['id'] ?>/meeting" class="btn btn-primary btn-sm">+ Nieuwe afspraak</a>
    </div>
    <?php if (!empty($meetings)): ?>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Datum</th>
                    <th>Titel</th>
                    <th>Contactpersoon</th>
                    <th>Locatie</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($meetings as $meeting): ?>
                <tr>
                    <td class="text-sm text-muted">
                        <?= htmlspecialchars(date('d-m-Y H:i', strtotime($meeting['meeting_date'])), ENT_QUOTES, 'UTF-8') ?>
                    </td>
                    <td class="font-bold"><?= htmlspecialchars($meeting['title'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="text-sm"><?= htmlspecialchars($meeting['contact_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="text-sm"><?= htmlspecialchars($meeting['location'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <span class="<?= $meetingStatusBadge($meeting['status']) ?>">
                            <?= htmlspecialchars(ucfirst($meeting['status']), ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
        <div class="card-body text-muted text-sm">Nog geen afspraken gepland.</div>
    <?php endif; ?>
</div>
