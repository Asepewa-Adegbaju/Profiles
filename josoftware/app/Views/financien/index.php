<?php
use App\Core\CSRF;

if (!function_exists('fmt_money')) {
    function fmt_money(float $amount): string {
        return '€ ' . number_format($amount, 2, ',', '.');
    }
}
if (!function_exists('fmt_date')) {
    function fmt_date(string $date): string {
        if (empty($date)) return '—';
        return date('d-m-Y', strtotime($date));
    }
}

$quoteBadge = function(string $s): string {
    $map = [
        'concept'      => 'badge badge-gray',
        'verzonden'    => 'badge badge-blue',
        'geaccepteerd' => 'badge badge-green',
        'afgewezen'    => 'badge badge-red',
        'verlopen'     => 'badge badge-orange',
    ];
    return $map[$s] ?? 'badge badge-gray';
};

$invoiceBadge = function(string $s): string {
    $map = [
        'concept'     => 'badge badge-gray',
        'verzonden'   => 'badge badge-blue',
        'betaald'     => 'badge badge-green',
        'te-laat'     => 'badge badge-red',
        'geannuleerd' => 'badge badge-gray',
    ];
    return $map[$s] ?? 'badge badge-gray';
};
?>

<!-- ── Paginakop ──────────────────────────────────────────────────────────── -->
<div class="flex justify-between items-center mb-2">
    <h2 style="font-size:1.1rem;font-weight:700;">Financiën — Overzicht</h2>
    <div class="flex gap-1">
        <a href="<?= APP_URL ?>/financien/offertes/nieuw" class="btn btn-secondary btn-sm">+ Nieuwe offerte</a>
        <a href="<?= APP_URL ?>/financien/facturen/nieuw" class="btn btn-primary btn-sm">+ Nieuwe factuur</a>
    </div>
</div>

<!-- ── Statistieken ──────────────────────────────────────────────────────── -->
<div class="stats-grid" style="margin-bottom:1.5rem;">
    <div class="stat-card">
        <div class="stat-icon stat-icon--blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        </div>
        <div class="stat-content">
            <span class="stat-label">Openstaande facturen</span>
            <span class="stat-value" style="font-size:1.1rem;"><?= htmlspecialchars(fmt_money($openAmount), ENT_QUOTES, 'UTF-8') ?></span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:var(--danger-light);color:var(--danger);">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        </div>
        <div class="stat-content">
            <span class="stat-label">Achterstallig</span>
            <span class="stat-value"><?= (int) $overdueCount ?></span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon--orange">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        </div>
        <div class="stat-content">
            <span class="stat-label">Offertes totaal</span>
            <span class="stat-value"><?= (int) $quoteCount ?></span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon--green">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
        </div>
        <div class="stat-content">
            <span class="stat-label">Facturen totaal</span>
            <span class="stat-value"><?= (int) $invoiceCount ?></span>
        </div>
    </div>
</div>

<!-- ── Twee kolommen ─────────────────────────────────────────────────────── -->
<div class="flex gap-2" style="align-items:flex-start;">

    <!-- Recent offertes -->
    <div class="card" style="flex:1;min-width:0;">
        <div class="card-header">
            <div class="flex justify-between items-center">
                <span class="card-title">Recente offertes</span>
                <a href="<?= APP_URL ?>/financien/offertes" class="btn btn-secondary btn-sm">Alle offertes</a>
            </div>
        </div>
        <div class="table-wrapper">
            <?php if (empty($recentQuotes)): ?>
                <div style="padding:1.5rem;text-align:center;" class="text-muted text-sm">Nog geen offertes.</div>
            <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Nummer</th>
                        <th>Bedrijf</th>
                        <th>Status</th>
                        <th>Bedrag</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentQuotes as $q): ?>
                    <tr>
                        <td>
                            <a href="<?= APP_URL ?>/financien/offertes/<?= (int) $q['id'] ?>" class="font-bold">
                                <?= htmlspecialchars($q['quote_number'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        </td>
                        <td class="text-sm text-muted"><?= htmlspecialchars($q['company_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><span class="<?= $quoteBadge($q['status']) ?>"><?= htmlspecialchars($q['status'], ENT_QUOTES, 'UTF-8') ?></span></td>
                        <td class="text-sm"><?= htmlspecialchars(fmt_money((float) $q['subtotal']), ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent facturen -->
    <div class="card" style="flex:1;min-width:0;">
        <div class="card-header">
            <div class="flex justify-between items-center">
                <span class="card-title">Recente facturen</span>
                <a href="<?= APP_URL ?>/financien/facturen" class="btn btn-secondary btn-sm">Alle facturen</a>
            </div>
        </div>
        <div class="table-wrapper">
            <?php if (empty($recentInvoices)): ?>
                <div style="padding:1.5rem;text-align:center;" class="text-muted text-sm">Nog geen facturen.</div>
            <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Nummer</th>
                        <th>Bedrijf</th>
                        <th>Status</th>
                        <th>Bedrag</th>
                        <th>Vervaldatum</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentInvoices as $inv): ?>
                    <tr>
                        <td>
                            <a href="<?= APP_URL ?>/financien/facturen/<?= (int) $inv['id'] ?>" class="font-bold">
                                <?= htmlspecialchars($inv['invoice_number'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        </td>
                        <td class="text-sm text-muted"><?= htmlspecialchars($inv['company_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><span class="<?= $invoiceBadge($inv['status']) ?>"><?= htmlspecialchars($inv['status'], ENT_QUOTES, 'UTF-8') ?></span></td>
                        <td class="text-sm"><?= htmlspecialchars(fmt_money((float) $inv['subtotal']), ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="text-sm <?= $inv['status'] === 'te-laat' ? 'font-bold' : 'text-muted' ?>"
                            style="<?= $inv['status'] === 'te-laat' ? 'color:#ef4444;' : '' ?>">
                            <?= htmlspecialchars(fmt_date($inv['due_date']), ENT_QUOTES, 'UTF-8') ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>

</div>
