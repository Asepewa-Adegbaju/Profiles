<?php
use App\Core\CSRF;
?>

<!-- ── Paginakop ──────────────────────────────────────────────────────────── -->
<div class="flex justify-between items-center mb-2">
    <h2 style="font-size:1.1rem;font-weight:700;">Uren & Kilometers</h2>
    <div class="flex gap-1">
        <a href="<?= APP_URL ?>/uren/export?month=<?= htmlspecialchars($month, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-secondary btn-sm">CSV Exporteren</a>
        <a href="<?= APP_URL ?>/uren/nieuw" class="btn btn-primary btn-sm">+ Uren invoeren</a>
    </div>
</div>

<!-- ── Tabs ───────────────────────────────────────────────────────────────── -->
<div class="flex gap-1 mb-2">
    <a href="<?= APP_URL ?>/uren?month=<?= htmlspecialchars($month, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-primary btn-sm">Uren</a>
    <a href="<?= APP_URL ?>/kilometers?month=<?= htmlspecialchars($month, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-secondary btn-sm">Kilometers</a>
</div>

<!-- ── Maandnavigatie ─────────────────────────────────────────────────────── -->
<div class="flex items-center gap-2 mb-2" style="justify-content:center;">
    <a href="<?= APP_URL ?>/uren?month=<?= htmlspecialchars($prev, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-secondary btn-sm">&#8592;</a>
    <span style="font-size:1rem;font-weight:700;"><?= htmlspecialchars(ucfirst($label), ENT_QUOTES, 'UTF-8') ?></span>
    <a href="<?= APP_URL ?>/uren?month=<?= htmlspecialchars($next, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-secondary btn-sm">&#8594;</a>
</div>

<!-- ── Statistieken ───────────────────────────────────────────────────────── -->
<div class="stats-grid" style="margin-bottom:1rem;">
    <div class="stat-card">
        <div class="stat-icon stat-icon--blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div class="stat-content">
            <span class="stat-label">Zakelijk uren</span>
            <span class="stat-value"><?= number_format((float)($totals['zakelijk'] ?? 0), 2, ',', '.') ?></span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:var(--gray-100);color:var(--gray-600);">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div class="stat-content">
            <span class="stat-label">Privé uren</span>
            <span class="stat-value"><?= number_format((float)($totals['prive'] ?? 0), 2, ',', '.') ?></span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon--green">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        </div>
        <div class="stat-content">
            <span class="stat-label">Factureerbaar bedrag</span>
            <span class="stat-value">&euro; <?= number_format((float)($totals['amount'] ?? 0), 2, ',', '.') ?></span>
        </div>
    </div>
</div>

<!-- ── Tabel ─────────────────────────────────────────────────────────────── -->
<div class="card">
    <div class="table-wrapper">
        <?php if (empty($entries)): ?>
            <div style="padding:2rem;text-align:center;" class="text-muted">
                Geen urenregistraties gevonden voor <?= htmlspecialchars(ucfirst($label), ENT_QUOTES, 'UTF-8') ?>.
                <a href="<?= APP_URL ?>/uren/nieuw">Voeg de eerste toe.</a>
            </div>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Datum</th>
                    <th>Omschrijving</th>
                    <th>Project</th>
                    <th>Uren</th>
                    <th>Uurtarief</th>
                    <th>Type</th>
                    <th>Factureerbaar</th>
                    <th>Acties</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($entries as $entry): ?>
                <tr>
                    <td class="text-sm"><?= htmlspecialchars(date('d-m-Y', strtotime($entry['entry_date'])), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($entry['description'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="text-muted text-sm"><?= htmlspecialchars($entry['project_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="text-sm"><?= number_format((float)$entry['hours'], 2, ',', '.') ?></td>
                    <td class="text-sm">&euro; <?= number_format((float)$entry['hourly_rate'], 2, ',', '.') ?></td>
                    <td>
                        <?php if ($entry['type'] === 'zakelijk'): ?>
                            <span class="badge badge-blue">Zakelijk</span>
                        <?php else: ?>
                            <span class="badge badge-gray">Privé</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-sm">
                        <?php if ($entry['billable']): ?>
                            <span class="badge badge-green">Ja</span>
                        <?php else: ?>
                            <span class="badge badge-gray">Nee</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="flex gap-1">
                            <a href="<?= APP_URL ?>/uren/<?= (int)$entry['id'] ?>/edit" class="btn btn-secondary btn-sm">Bewerken</a>
                            <form method="POST" action="<?= APP_URL ?>/uren/<?= (int)$entry['id'] ?>/delete" style="display:inline;" onsubmit="return confirm('Urenboeking verwijderen? Dit kan niet ongedaan worden gemaakt.');">
                                <?= CSRF::field() ?>
                                <button type="submit" class="btn btn-danger btn-sm">Verwijderen</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>
