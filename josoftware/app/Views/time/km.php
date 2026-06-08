<?php
use App\Core\CSRF;
?>

<!-- ── Paginakop ──────────────────────────────────────────────────────────── -->
<div class="flex justify-between items-center mb-2">
    <h2 style="font-size:1.1rem;font-weight:700;">Uren & Kilometers</h2>
    <a href="<?= APP_URL ?>/kilometers/nieuw" class="btn btn-primary btn-sm">+ Kilometers invoeren</a>
</div>

<!-- ── Tabs ───────────────────────────────────────────────────────────────── -->
<div class="flex gap-1 mb-2">
    <a href="<?= APP_URL ?>/uren?month=<?= htmlspecialchars($month, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-secondary btn-sm">Uren</a>
    <a href="<?= APP_URL ?>/kilometers?month=<?= htmlspecialchars($month, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-primary btn-sm">Kilometers</a>
</div>

<!-- ── Maandnavigatie ─────────────────────────────────────────────────────── -->
<div class="flex items-center gap-2 mb-2" style="justify-content:center;">
    <a href="<?= APP_URL ?>/kilometers?month=<?= htmlspecialchars($prev, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-secondary btn-sm">&#8592;</a>
    <span style="font-size:1rem;font-weight:700;"><?= htmlspecialchars(ucfirst($label), ENT_QUOTES, 'UTF-8') ?></span>
    <a href="<?= APP_URL ?>/kilometers?month=<?= htmlspecialchars($next, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-secondary btn-sm">&#8594;</a>
</div>

<!-- ── Statistieken ───────────────────────────────────────────────────────── -->
<div class="stats-grid" style="margin-bottom:1rem;">
    <div class="stat-card">
        <div class="stat-icon stat-icon--blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 12h18M3 6l9-3 9 3M3 18l9 3 9-3"/></svg>
        </div>
        <div class="stat-content">
            <span class="stat-label">Zakelijk km</span>
            <span class="stat-value"><?= number_format((float)($totals['zakelijk'] ?? 0), 1, ',', '.') ?> km</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:var(--gray-100);color:var(--gray-600);">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 12h18M3 6l9-3 9 3M3 18l9 3 9-3"/></svg>
        </div>
        <div class="stat-content">
            <span class="stat-label">Privé km</span>
            <span class="stat-value"><?= number_format((float)($totals['prive'] ?? 0), 1, ',', '.') ?> km</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon--green">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        </div>
        <div class="stat-content">
            <span class="stat-label">Vergoeding</span>
            <span class="stat-value">&euro; <?= number_format((float)($totals['amount'] ?? 0), 2, ',', '.') ?></span>
        </div>
    </div>
</div>

<!-- ── Tabel ─────────────────────────────────────────────────────────────── -->
<div class="card">
    <div class="table-wrapper">
        <?php if (empty($entries)): ?>
            <div style="padding:2rem;text-align:center;" class="text-muted">
                Geen kilometerregistraties gevonden voor <?= htmlspecialchars(ucfirst($label), ENT_QUOTES, 'UTF-8') ?>.
                <a href="<?= APP_URL ?>/kilometers/nieuw">Voeg de eerste toe.</a>
            </div>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Datum</th>
                    <th>Van</th>
                    <th>Naar</th>
                    <th>KM</th>
                    <th>Doel</th>
                    <th>Type</th>
                    <th>Vergoeding</th>
                    <th>Acties</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($entries as $entry): ?>
                <tr>
                    <td class="text-sm"><?= htmlspecialchars(date('d-m-Y', strtotime($entry['entry_date'])), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($entry['from_location'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($entry['to_location'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="text-sm"><?= number_format((float)$entry['km'], 1, ',', '.') ?></td>
                    <td class="text-muted text-sm"><?= htmlspecialchars($entry['purpose'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <?php if ($entry['type'] === 'zakelijk'): ?>
                            <span class="badge badge-blue">Zakelijk</span>
                        <?php else: ?>
                            <span class="badge badge-gray">Privé</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-sm">&euro; <?= number_format((float)$entry['km'] * (float)$entry['rate_per_km'], 2, ',', '.') ?></td>
                    <td>
                        <div class="flex gap-1">
                            <a href="<?= APP_URL ?>/kilometers/<?= (int)$entry['id'] ?>/edit" class="btn btn-secondary btn-sm">Bewerken</a>
                            <form method="POST" action="<?= APP_URL ?>/kilometers/<?= (int)$entry['id'] ?>/delete" style="display:inline;" onsubmit="return confirm('Kilometerregistratie verwijderen? Dit kan niet ongedaan worden gemaakt.');">
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
