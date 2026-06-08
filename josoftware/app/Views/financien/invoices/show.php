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

$badgeClass = match ($invoice['status']) {
    'concept'     => 'badge badge-gray',
    'verzonden'   => 'badge badge-blue',
    'betaald'     => 'badge badge-green',
    'te-laat'     => 'badge badge-red',
    'geannuleerd' => 'badge badge-gray',
    default       => 'badge badge-gray',
};

$isOverdue = ($invoice['status'] === 'te-laat');
?>

<!-- ── Paginakop ──────────────────────────────────────────────────────────── -->
<div class="flex justify-between items-center mb-2" style="flex-wrap:wrap;gap:.5rem;">
    <div class="flex items-center gap-2">
        <h2 style="font-size:1.1rem;font-weight:700;">
            <?= htmlspecialchars($invoice['invoice_number'], ENT_QUOTES, 'UTF-8') ?>
        </h2>
        <span class="<?= $badgeClass ?>"><?= htmlspecialchars($invoice['status'], ENT_QUOTES, 'UTF-8') ?></span>
    </div>
    <div class="flex gap-1" style="flex-wrap:wrap;">
        <a href="<?= APP_URL ?>/financien/facturen/<?= (int) $invoice['id'] ?>/print"
           class="btn btn-secondary btn-sm" target="_blank">Afdrukken / PDF</a>
        <a href="<?= APP_URL ?>/financien/facturen" class="btn btn-secondary btn-sm">Terug naar facturen</a>
        <form method="POST" action="<?= APP_URL ?>/financien/facturen/<?= (int) $invoice['id'] ?>/delete"
              onsubmit="return confirm('Factuur verwijderen? Dit kan niet ongedaan worden gemaakt.');">
            <?= CSRF::field() ?>
            <button type="submit" class="btn btn-danger btn-sm">Verwijderen</button>
        </form>
    </div>
</div>

<?php if ($isOverdue): ?>
<div class="alert alert-error" role="alert" style="margin-bottom:1rem;">
    Deze factuur is achterstallig. De vervaldatum (<?= htmlspecialchars(fmt_date($invoice['due_date']), ENT_QUOTES, 'UTF-8') ?>) is verstreken.
</div>
<?php endif; ?>

<!-- ── Infokaarten bovenaan ──────────────────────────────────────────────── -->
<div class="flex gap-2" style="margin-bottom:1rem;flex-wrap:wrap;">

    <!-- Bedrijfsinfo -->
    <div class="card" style="flex:1;min-width:220px;">
        <div class="card-header"><span class="card-title">Bedrijf</span></div>
        <div class="card-body">
            <p class="font-bold"><?= htmlspecialchars($invoice['company_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></p>
            <?php if (!empty($invoice['company_address'])): ?>
                <p class="text-sm text-muted"><?= htmlspecialchars($invoice['company_address'], ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
            <?php if (!empty($invoice['company_postal_code']) || !empty($invoice['company_city'])): ?>
                <p class="text-sm text-muted">
                    <?= htmlspecialchars(trim(($invoice['company_postal_code'] ?? '') . ' ' . ($invoice['company_city'] ?? '')), ENT_QUOTES, 'UTF-8') ?>
                </p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Factuurdetails -->
    <div class="card" style="flex:1;min-width:220px;">
        <div class="card-header"><span class="card-title">Details</span></div>
        <div class="card-body">
            <table style="width:100%;border-collapse:collapse;">
                <tr>
                    <td class="text-sm text-muted" style="padding:.25rem 0;width:45%;">Factuurdatum</td>
                    <td class="text-sm font-bold"><?= htmlspecialchars(fmt_date($invoice['issue_date']), ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
                <tr>
                    <td class="text-sm text-muted" style="padding:.25rem 0;">Vervaldatum</td>
                    <td class="text-sm font-bold" style="<?= $isOverdue ? 'color:#ef4444;' : '' ?>">
                        <?= htmlspecialchars(fmt_date($invoice['due_date']), ENT_QUOTES, 'UTF-8') ?>
                    </td>
                </tr>
                <tr>
                    <td class="text-sm text-muted" style="padding:.25rem 0;">Aangemaakt door</td>
                    <td class="text-sm"><?= htmlspecialchars($invoice['created_by_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
                <?php if (!empty($invoice['quote_number'])): ?>
                <tr>
                    <td class="text-sm text-muted" style="padding:.25rem 0;">Offerte</td>
                    <td class="text-sm">
                        <a href="<?= APP_URL ?>/financien/offertes/<?= (int) $invoice['quote_id'] ?>">
                            <?= htmlspecialchars($invoice['quote_number'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td class="text-sm text-muted" style="padding:.25rem 0;">Status</td>
                    <td><span class="<?= $badgeClass ?>"><?= htmlspecialchars($invoice['status'], ENT_QUOTES, 'UTF-8') ?></span></td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Status wijzigen -->
    <div class="card" style="flex:1;min-width:220px;">
        <div class="card-header"><span class="card-title">Status wijzigen</span></div>
        <div class="card-body">
            <form method="POST" action="<?= APP_URL ?>/financien/facturen/<?= (int) $invoice['id'] ?>/status">
                <?= CSRF::field() ?>
                <div class="form-group">
                    <select name="status" class="form-select w-full">
                        <?php foreach (['concept','verzonden','betaald','te-laat','geannuleerd'] as $opt): ?>
                            <option value="<?= $opt ?>" <?= $invoice['status'] === $opt ? 'selected' : '' ?>>
                                <?= htmlspecialchars(ucfirst(str_replace('-', ' ', $opt)), ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-sm w-full">Status opslaan</button>
            </form>
        </div>
    </div>

</div>

<!-- ── Regelitems ────────────────────────────────────────────────────────── -->
<div class="card" style="margin-bottom:1rem;">
    <div class="card-header"><span class="card-title">Regelitems</span></div>
    <div class="table-wrapper">
        <?php if (empty($items)): ?>
            <div style="padding:1.5rem;text-align:center;" class="text-muted text-sm">Geen regelitems voor deze factuur.</div>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Omschrijving</th>
                    <th style="text-align:right;">Aantal</th>
                    <th style="text-align:right;">Eenheidsprijs</th>
                    <th style="text-align:right;">BTW %</th>
                    <th style="text-align:right;">Regeltotaal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['description'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td style="text-align:right;"><?= number_format((float) $item['quantity'], 2, ',', '.') ?></td>
                    <td style="text-align:right;"><?= htmlspecialchars(fmt_money((float) $item['unit_price']), ENT_QUOTES, 'UTF-8') ?></td>
                    <td style="text-align:right;"><?= number_format((float) $item['vat_rate'], 0) ?>%</td>
                    <td style="text-align:right;font-weight:600;">
                        <?= htmlspecialchars(fmt_money((float) $item['quantity'] * (float) $item['unit_price']), ENT_QUOTES, 'UTF-8') ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
    <?php if (!empty($items)): ?>
    <div class="card-body" style="padding-top:0;">
        <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:1rem;max-width:380px;margin-left:auto;">
            <div class="flex justify-between" style="padding:.35rem 0;font-size:.875rem;">
                <span>Subtotaal excl. BTW</span>
                <span><?= htmlspecialchars(fmt_money($totals['subtotal']), ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <?php if ($totals['vat']['21%'] > 0): ?>
            <div class="flex justify-between" style="padding:.35rem 0;font-size:.875rem;">
                <span>BTW 21%</span>
                <span><?= htmlspecialchars(fmt_money($totals['vat']['21%']), ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <?php endif; ?>
            <?php if ($totals['vat']['9%'] > 0): ?>
            <div class="flex justify-between" style="padding:.35rem 0;font-size:.875rem;">
                <span>BTW 9%</span>
                <span><?= htmlspecialchars(fmt_money($totals['vat']['9%']), ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <?php endif; ?>
            <?php if ($totals['vat']['0%'] > 0): ?>
            <div class="flex justify-between" style="padding:.35rem 0;font-size:.875rem;">
                <span>BTW 0%</span>
                <span><?= htmlspecialchars(fmt_money($totals['vat']['0%']), ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <?php endif; ?>
            <div class="flex justify-between" style="padding:.6rem 0 .35rem;font-size:1rem;font-weight:700;border-top:2px solid #e2e8f0;margin-top:.35rem;">
                <span>Totaal incl. BTW</span>
                <span><?= htmlspecialchars(fmt_money($totals['total']), ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- ── Notities ──────────────────────────────────────────────────────────── -->
<?php if (!empty($invoice['notes'])): ?>
<div class="card">
    <div class="card-header"><span class="card-title">Notities</span></div>
    <div class="card-body">
        <p class="text-sm"><?= nl2br(htmlspecialchars($invoice['notes'], ENT_QUOTES, 'UTF-8')) ?></p>
    </div>
</div>
<?php endif; ?>
