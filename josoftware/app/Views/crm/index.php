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
?>

<!-- ── Paginakop ──────────────────────────────────────────────────────────── -->
<div class="flex justify-between items-center mb-2">
    <h2 style="font-size:1.1rem;font-weight:700;">Bedrijven</h2>
    <div class="flex gap-1">
        <a href="<?= APP_URL ?>/crm/import" class="btn btn-secondary btn-sm">CSV Importeren</a>
        <a href="<?= APP_URL ?>/crm/meetings" class="btn btn-secondary btn-sm">Afspraken</a>
        <a href="<?= APP_URL ?>/crm/create" class="btn btn-primary btn-sm">+ Nieuw bedrijf</a>
    </div>
</div>

<!-- ── Statistieken strip ─────────────────────────────────────────────────── -->
<div class="stats-grid" style="margin-bottom:1rem;">
    <div class="stat-card">
        <div class="stat-icon stat-icon--blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
        </div>
        <div class="stat-content">
            <span class="stat-label">Leads</span>
            <span class="stat-value"><?= (int)($counts['lead'] ?? 0) ?></span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon--orange">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        </div>
        <div class="stat-content">
            <span class="stat-label">Prospects</span>
            <span class="stat-value"><?= (int)($counts['prospect'] ?? 0) ?></span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon--green">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <div class="stat-content">
            <span class="stat-label">Klanten</span>
            <span class="stat-value"><?= (int)($counts['klant'] ?? 0) ?></span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:var(--danger-light);color:var(--danger);">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
        </div>
        <div class="stat-content">
            <span class="stat-label">Inactief</span>
            <span class="stat-value"><?= (int)($counts['inactief'] ?? 0) ?></span>
        </div>
    </div>
</div>

<!-- ── Filter & Zoek ─────────────────────────────────────────────────────── -->
<div class="card" style="margin-bottom:1rem;">
    <div class="card-body" style="padding:.75rem 1rem;">
        <form method="GET" action="<?= APP_URL ?>/crm" class="flex gap-1 items-center" style="flex-wrap:wrap;">
            <input
                type="text"
                name="q"
                value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>"
                placeholder="Zoek op naam, stad, sector…"
                class="form-input"
                style="max-width:280px;"
            >
            <button type="submit" class="btn btn-primary btn-sm">Zoeken</button>
            <?php if ($search !== ''): ?>
                <a href="<?= APP_URL ?>/crm" class="btn btn-secondary btn-sm">Wissen</a>
            <?php endif; ?>
            <span style="margin-left:auto;" class="flex gap-1">
                <a href="<?= APP_URL ?>/crm" class="btn btn-sm <?= $status === '' ? 'btn-primary' : 'btn-secondary' ?>">Alle (<?= $total ?>)</a>
                <a href="<?= APP_URL ?>/crm?status=lead" class="btn btn-sm <?= $status === 'lead' ? 'btn-primary' : 'btn-secondary' ?>">Lead</a>
                <a href="<?= APP_URL ?>/crm?status=prospect" class="btn btn-sm <?= $status === 'prospect' ? 'btn-primary' : 'btn-secondary' ?>">Prospect</a>
                <a href="<?= APP_URL ?>/crm?status=klant" class="btn btn-sm <?= $status === 'klant' ? 'btn-primary' : 'btn-secondary' ?>">Klant</a>
                <a href="<?= APP_URL ?>/crm?status=inactief" class="btn btn-sm <?= $status === 'inactief' ? 'btn-primary' : 'btn-secondary' ?>">Inactief</a>
            </span>
        </form>
    </div>
</div>

<!-- ── Tabel ─────────────────────────────────────────────────────────────── -->
<div class="card">
    <div class="table-wrapper">
        <?php if (empty($companies)): ?>
            <div style="padding:2rem;text-align:center;" class="text-muted">
                Geen bedrijven gevonden.
                <?php if ($search !== '' || $status !== ''): ?>
                    <a href="<?= APP_URL ?>/crm">Toon alle bedrijven</a>
                <?php else: ?>
                    <a href="<?= APP_URL ?>/crm/create">Voeg het eerste bedrijf toe.</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Naam</th>
                    <th>Sector</th>
                    <th>Stad</th>
                    <th>Status</th>
                    <th>Contacten</th>
                    <th>Acties</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($companies as $company): ?>
                <tr>
                    <td>
                        <a href="<?= APP_URL ?>/crm/<?= (int)$company['id'] ?>" class="font-bold">
                            <?= htmlspecialchars($company['name'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </td>
                    <td class="text-muted text-sm"><?= htmlspecialchars($company['sector'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="text-muted text-sm"><?= htmlspecialchars($company['city'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <span class="<?= $statusBadge($company['status']) ?>">
                            <?= htmlspecialchars(ucfirst($company['status']), ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </td>
                    <td class="text-sm"><?= (int)($company['contact_count'] ?? 0) ?></td>
                    <td>
                        <div class="flex gap-1">
                            <a href="<?= APP_URL ?>/crm/<?= (int)$company['id'] ?>" class="btn btn-secondary btn-sm">Details</a>
                            <a href="<?= APP_URL ?>/crm/<?= (int)$company['id'] ?>/edit" class="btn btn-secondary btn-sm">Bewerken</a>
                            <form method="POST" action="<?= APP_URL ?>/crm/<?= (int)$company['id'] ?>/delete" style="display:inline;" onsubmit="return confirm('Bedrijf verwijderen? Dit kan niet ongedaan worden gemaakt.');">
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
