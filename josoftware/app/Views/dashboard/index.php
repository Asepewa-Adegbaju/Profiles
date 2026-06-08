<?php
$maanden = ['','jan','feb','mrt','apr','mei','jun','jul','aug','sep','okt','nov','dec'];
$mLabel  = $maanden[(int)date('m')] . ' ' . date('Y');
?>

<div class="flex justify-between items-center mb-2">
    <div>
        <h2 style="font-size:1.15rem;font-weight:700;">Welkom terug, <?= htmlspecialchars(explode(' ', $user['name'])[0], ENT_QUOTES, 'UTF-8') ?>.</h2>
        <p class="text-muted text-sm"><?= date('l d F Y') ?> &mdash; overzicht van vandaag</p>
    </div>
    <div class="flex gap-1">
        <a href="<?= APP_URL ?>/crm/create" class="btn btn-secondary btn-sm">+ Bedrijf</a>
        <a href="<?= APP_URL ?>/uren/nieuw" class="btn btn-secondary btn-sm">+ Uren</a>
        <a href="<?= APP_URL ?>/financien/facturen/nieuw" class="btn btn-primary btn-sm">+ Factuur</a>
    </div>
</div>

<!-- Stat cards -->
<div class="stats-grid" style="margin-bottom:1.5rem;">
    <div class="stat-card">
        <div class="stat-icon stat-icon--blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
        </div>
        <div class="stat-content">
            <span class="stat-label">Actieve CRM-bedrijven</span>
            <span class="stat-value"><?= $crmCount ?></span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon--green">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div class="stat-content">
            <span class="stat-label">Uren <?= $mLabel ?></span>
            <span class="stat-value"><?= number_format($urenTotal, 1, ',', '.') ?></span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon--orange">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
        </div>
        <div class="stat-content">
            <span class="stat-label">Actieve projecten</span>
            <span class="stat-value"><?= $projectCount ?></span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon--purple" style="<?= $overdueCount > 0 ? 'background:#fef2f2;color:#dc2626;' : '' ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        </div>
        <div class="stat-content">
            <span class="stat-label">Open facturen<?= $overdueCount > 0 ? ' · <span style="color:#dc2626;">' . $overdueCount . ' te laat</span>' : '' ?></span>
            <span class="stat-value">€ <?= number_format($openInvoiceAmount, 0, ',', '.') ?></span>
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.25rem;">

    <!-- Komende afspraken -->
    <div class="card">
        <div class="card-header">
            <span class="card-title">Komende afspraken</span>
            <a href="<?= APP_URL ?>/crm/meetings" class="btn btn-secondary btn-sm">Alle</a>
        </div>
        <?php if (empty($upcomingMeetings)): ?>
            <div class="card-body text-muted text-sm">Geen geplande afspraken.</div>
        <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead><tr><th>Datum</th><th>Bedrijf</th><th>Status</th></tr></thead>
                <tbody>
                <?php foreach ($upcomingMeetings as $m): ?>
                    <tr>
                        <td>
                            <span style="font-weight:600;"><?= date('d-m', strtotime($m['meeting_date'])) ?></span>
                            <span class="text-muted text-sm"> <?= date('H:i', strtotime($m['meeting_date'])) ?></span>
                        </td>
                        <td>
                            <a href="<?= APP_URL ?>/crm/<?= $m['company_id'] ?>"><?= htmlspecialchars($m['company_name'], ENT_QUOTES, 'UTF-8') ?></a>
                            <?php if ($m['contact_name']): ?>
                                <br><span class="text-muted text-sm"><?= htmlspecialchars($m['contact_name'], ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            $sc = ['gepland'=>'badge-blue','bevestigd'=>'badge-green','geweest'=>'badge-gray','afgeslagen'=>'badge-red'];
                            $cls = $sc[$m['status']] ?? 'badge-gray';
                            ?>
                            <span class="badge <?= $cls ?>"><?= htmlspecialchars($m['status'], ENT_QUOTES, 'UTF-8') ?></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <!-- Recente uren -->
    <div class="card">
        <div class="card-header">
            <span class="card-title">Mijn recente uren</span>
            <a href="<?= APP_URL ?>/uren" class="btn btn-secondary btn-sm">Alle</a>
        </div>
        <?php if (empty($recentTime)): ?>
            <div class="card-body text-muted text-sm">Nog geen uren ingevoerd.</div>
        <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead><tr><th>Datum</th><th>Omschrijving</th><th>Uren</th></tr></thead>
                <tbody>
                <?php foreach ($recentTime as $t): ?>
                    <tr>
                        <td class="text-sm"><?= date('d-m', strtotime($t['entry_date'])) ?></td>
                        <td>
                            <?= htmlspecialchars(mb_strimwidth($t['description'], 0, 40, '…'), ENT_QUOTES, 'UTF-8') ?>
                            <?php if ($t['project_name']): ?>
                                <br><span class="text-muted text-sm"><?= htmlspecialchars($t['project_name'], ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                        </td>
                        <td style="font-weight:600;"><?= number_format((float)$t['hours'], 1, ',', '.') ?>u</td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

</div>

<!-- Recente activiteit -->
<div class="card">
    <div class="card-header">
        <span class="card-title">Recente activiteit</span>
    </div>
    <?php if (empty($recentActivity)): ?>
        <div class="card-body text-muted text-sm">Nog geen activiteit geregistreerd.</div>
    <?php else: ?>
    <div class="table-wrapper">
        <table>
            <thead><tr><th>Tijd</th><th>Gebruiker</th><th>Actie</th><th>Omschrijving</th></tr></thead>
            <tbody>
            <?php foreach ($recentActivity as $a): ?>
                <tr>
                    <td class="text-sm text-muted"><?= date('d-m H:i', strtotime($a['created_at'])) ?></td>
                    <td class="text-sm"><?= htmlspecialchars($a['user_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><span class="badge badge-gray"><?= htmlspecialchars($a['action'], ENT_QUOTES, 'UTF-8') ?></span></td>
                    <td class="text-sm"><?= htmlspecialchars($a['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
