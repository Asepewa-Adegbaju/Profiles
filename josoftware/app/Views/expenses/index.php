<?php
$maanden = ['','januari','februari','maart','april','mei','juni',
            'juli','augustus','september','oktober','november','december'];
?>
<style>
.cat-dot { display:inline-block;width:10px;height:10px;border-radius:50%;margin-right:.4rem;flex-shrink:0; }
.cat-bar-wrap { background:#e2e8f0;border-radius:99px;height:8px;overflow:hidden; }
.cat-bar-fill { height:100%;border-radius:99px; }
.receipt-thumb { width:36px;height:36px;object-fit:cover;border-radius:4px;border:1px solid #e2e8f0; }
</style>

<div class="flex justify-between items-center mb-2">
    <div>
        <h2 style="font-size:1.1rem;font-weight:700;">Uitgaven & Bonnetjes</h2>
        <p class="text-muted text-sm">Maandoverzicht zakelijke kosten</p>
    </div>
    <div class="flex gap-1">
        <a href="<?= APP_URL ?>/uitgaven/export?month=<?= urlencode($month) ?>" class="btn btn-secondary btn-sm">↓ Export CSV</a>
        <a href="<?= APP_URL ?>/uitgaven/nieuw" class="btn btn-primary btn-sm">+ Nieuwe uitgave</a>
    </div>
</div>

<!-- Maandnavigatie -->
<div class="flex items-center gap-2 mb-2" style="justify-content:center;">
    <a href="<?= APP_URL ?>/uitgaven?month=<?= $prevMonth ?>" class="btn btn-secondary btn-sm">← vorige</a>
    <span style="font-weight:700;font-size:1rem;min-width:160px;text-align:center;"><?= htmlspecialchars($monthLabel, ENT_QUOTES, 'UTF-8') ?></span>
    <a href="<?= APP_URL ?>/uitgaven?month=<?= $nextMonth ?>" class="btn btn-secondary btn-sm">volgende →</a>
</div>

<!-- Totalen -->
<div class="stats-grid" style="margin-bottom:1.25rem;">
    <div class="stat-card">
        <div class="stat-icon stat-icon--blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        </div>
        <div class="stat-content">
            <span class="stat-label">Totaal incl. BTW</span>
            <span class="stat-value">€ <?= number_format((float)($totals['total_incl'] ?? 0), 2, ',', '.') ?></span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon--green">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 7H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="2"/></svg>
        </div>
        <div class="stat-content">
            <span class="stat-label">BTW terugvordering</span>
            <span class="stat-value">€ <?= number_format((float)($totals['total_vat'] ?? 0), 2, ',', '.') ?></span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon--orange">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
        </div>
        <div class="stat-content">
            <span class="stat-label">Zakelijk excl. BTW</span>
            <span class="stat-value">€ <?= number_format((float)($totals['zakelijk'] ?? 0), 2, ',', '.') ?></span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon--purple">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
        </div>
        <div class="stat-content">
            <span class="stat-label">Bonnetjes</span>
            <span class="stat-value"><?= (int)($totals['count'] ?? 0) ?></span>
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 280px;gap:1.25rem;align-items:start;">

    <!-- Tabel -->
    <div class="card">
        <div class="card-header">
            <span class="card-title">Uitgaven <?= htmlspecialchars($monthLabel, ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <?php if (empty($expenses)): ?>
            <div class="card-body text-muted text-sm">Geen uitgaven in deze maand.</div>
        <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Datum</th>
                        <th>Omschrijving</th>
                        <th>Leverancier</th>
                        <th>Categorie</th>
                        <th style="text-align:right">Bedrag</th>
                        <th>BTW%</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Bon</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($expenses as $e): ?>
                    <?php
                    $statusCls = ['ingediend'=>'badge-blue','goedgekeurd'=>'badge-green','afgewezen'=>'badge-red'][$e['status']] ?? 'badge-gray';
                    $typeCls   = $e['type'] === 'zakelijk' ? 'badge-blue' : 'badge-gray';
                    ?>
                    <tr>
                        <td class="text-sm"><?= date('d-m-Y', strtotime($e['entry_date'])) ?></td>
                        <td>
                            <a href="<?= APP_URL ?>/uitgaven/<?= $e['id'] ?>" style="font-weight:500;">
                                <?= htmlspecialchars(mb_strimwidth($e['description'], 0, 40, '…'), ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        </td>
                        <td class="text-sm text-muted"><?= htmlspecialchars($e['supplier'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <?php if ($e['category_name']): ?>
                                <span class="flex items-center gap-1" style="font-size:.8rem;">
                                    <span class="cat-dot" style="background:<?= htmlspecialchars($e['category_color'] ?? '#64748b', ENT_QUOTES, 'UTF-8') ?>"></span>
                                    <?= htmlspecialchars($e['category_name'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            <?php else: ?>
                                <span class="text-muted text-sm">—</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align:right;font-weight:600;">€ <?= number_format((float)$e['amount_incl'], 2, ',', '.') ?></td>
                        <td class="text-sm text-muted"><?= $e['vat_rate'] ?>%</td>
                        <td><span class="badge <?= $typeCls ?>"><?= $e['type'] ?></span></td>
                        <td><span class="badge <?= $statusCls ?>"><?= $e['status'] ?></span></td>
                        <td>
                            <?php if ($e['receipt_filename']): ?>
                                <a href="<?= APP_URL ?>/uitgaven/<?= $e['id'] ?>/bonnetje" target="_blank" title="Bonnetje bekijken">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                </a>
                            <?php else: ?>
                                <span class="text-muted text-sm">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="flex gap-1">
                                <a href="<?= APP_URL ?>/uitgaven/<?= $e['id'] ?>/edit" class="btn btn-secondary btn-sm">Bewerk</a>
                                <form method="POST" action="<?= APP_URL ?>/uitgaven/<?= $e['id'] ?>/delete" onsubmit="return confirm('Zeker verwijderen?')">
                                    <?= \App\Core\CSRF::field() ?>
                                    <button type="submit" class="btn btn-danger btn-sm">✕</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <!-- Per categorie -->
    <div class="card">
        <div class="card-header"><span class="card-title">Per categorie</span></div>
        <div class="card-body">
            <?php if (empty($perCategory)): ?>
                <p class="text-muted text-sm">Nog geen data.</p>
            <?php else: ?>
                <?php
                $maxTotal = max(array_column($perCategory, 'total')) ?: 1;
                ?>
                <?php foreach ($perCategory as $cat): ?>
                    <div style="margin-bottom:.85rem;">
                        <div class="flex justify-between" style="margin-bottom:.3rem;">
                            <span class="flex items-center" style="font-size:.8rem;">
                                <span class="cat-dot" style="background:<?= htmlspecialchars($cat['color'] ?? '#64748b', ENT_QUOTES, 'UTF-8') ?>"></span>
                                <?= htmlspecialchars($cat['category'] ?? 'Overig', ENT_QUOTES, 'UTF-8') ?>
                            </span>
                            <span style="font-size:.8rem;font-weight:600;">€ <?= number_format((float)$cat['total'], 0, ',', '.') ?></span>
                        </div>
                        <div class="cat-bar-wrap">
                            <div class="cat-bar-fill" style="width:<?= round((float)$cat['total'] / $maxTotal * 100) ?>%;background:<?= htmlspecialchars($cat['color'] ?? '#64748b', ENT_QUOTES, 'UTF-8') ?>"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</div>
