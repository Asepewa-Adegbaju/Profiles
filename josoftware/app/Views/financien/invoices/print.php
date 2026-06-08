<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Factuur <?= htmlspecialchars($invoice['invoice_number'], ENT_QUOTES, 'UTF-8') ?></title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size: 13px; color: #1a1a1a; background: #fff; padding: 40px; }
.doc-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 40px; }
.company-brand { font-size: 22px; font-weight: 800; color: #2563eb; }
.company-brand span { display: block; font-size: 12px; font-weight: 400; color: #64748b; margin-top: 4px; }
.doc-title { text-align: right; }
.doc-title h1 { font-size: 28px; font-weight: 800; color: #0f172a; text-transform: uppercase; letter-spacing: .05em; }
.doc-title .doc-number { color: #64748b; font-size: 14px; margin-top: 4px; }
.addresses { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 30px; }
.address-block h3 { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: #94a3b8; margin-bottom: 8px; }
.address-block p { font-size: 13px; line-height: 1.6; }
.address-block .company-name { font-weight: 700; font-size: 15px; }
.meta-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; background: #f8fafc; border-radius: 8px; padding: 16px; margin-bottom: 30px; }
.meta-item label { font-size: 10px; text-transform: uppercase; letter-spacing: .08em; color: #94a3b8; font-weight: 700; display: block; margin-bottom: 4px; }
.meta-item span { font-weight: 600; font-size: 13px; }
.meta-item span.overdue { color: #ef4444; }
table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
thead th { background: #0f172a; color: #fff; padding: 10px 12px; text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: .05em; }
thead th:nth-child(2),
thead th:nth-child(3),
thead th:nth-child(4),
thead th:last-child { text-align: right; }
tbody td { padding: 10px 12px; border-bottom: 1px solid #e2e8f0; font-size: 13px; }
tbody td:nth-child(2),
tbody td:nth-child(3),
tbody td:nth-child(4),
tbody td:last-child { text-align: right; white-space: nowrap; }
tbody tr:nth-child(even) { background: #f8fafc; }
.totals { margin-left: auto; width: 320px; }
.totals-row { display: flex; justify-content: space-between; padding: 5px 0; font-size: 13px; border-bottom: 1px solid #f1f5f9; }
.totals-row.grand-total { font-size: 16px; font-weight: 800; border-top: 2px solid #0f172a; border-bottom: none; margin-top: 8px; padding-top: 10px; }
.notes { background: #fffbeb; border: 1px solid #fde68a; border-radius: 6px; padding: 12px 16px; margin-top: 24px; font-size: 12px; line-height: 1.6; }
.notes strong { font-size: 11px; text-transform: uppercase; letter-spacing: .08em; color: #92400e; display: block; margin-bottom: 4px; }
.overdue-banner { background: #fef2f2; border: 1px solid #fecaca; border-radius: 6px; padding: 10px 16px; margin-bottom: 20px; color: #dc2626; font-weight: 600; font-size: 13px; }
.footer { text-align: center; margin-top: 50px; color: #94a3b8; font-size: 11px; border-top: 1px solid #e2e8f0; padding-top: 20px; }
.print-btn { position: fixed; bottom: 24px; right: 24px; background: #2563eb; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; box-shadow: 0 4px 12px rgba(37,99,235,.3); }
.print-btn:hover { background: #1d4ed8; }
@media print {
  .print-btn { display: none; }
  body { padding: 20px; }
}
</style>
</head>
<body>

<button class="print-btn" onclick="window.print()">Afdrukken / PDF</button>

<?php if ($invoice['status'] === 'te-laat'): ?>
<div class="overdue-banner">
    LET OP: Deze factuur is achterstallig. Vervaldatum: <?= htmlspecialchars(date('d-m-Y', strtotime($invoice['due_date'])), ENT_QUOTES, 'UTF-8') ?>
</div>
<?php endif; ?>

<!-- ── Header ── -->
<div class="doc-header">
    <div class="company-brand">
        JO Software Solutions
        <span>Softwareontwikkeling &amp; Consultancy</span>
    </div>
    <div class="doc-title">
        <h1>Factuur</h1>
        <div class="doc-number"><?= htmlspecialchars($invoice['invoice_number'], ENT_QUOTES, 'UTF-8') ?></div>
    </div>
</div>

<!-- ── Adressen ── -->
<div class="addresses">
    <div class="address-block">
        <h3>Van</h3>
        <p class="company-name">JO Software Solutions</p>
        <p>Uw straat 1<br>1234 AB Uw stad<br>Nederland</p>
        <p style="margin-top:6px;">info@josoftware.nl<br>www.josoftware.nl</p>
    </div>
    <div class="address-block">
        <h3>Aan</h3>
        <p class="company-name"><?= htmlspecialchars($invoice['company_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></p>
        <?php if (!empty($invoice['company_address'])): ?>
            <p><?= htmlspecialchars($invoice['company_address'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
        <?php if (!empty($invoice['company_postal_code']) || !empty($invoice['company_city'])): ?>
            <p><?= htmlspecialchars(trim(($invoice['company_postal_code'] ?? '') . ' ' . ($invoice['company_city'] ?? '')), ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </div>
</div>

<!-- ── Metagegevens ── -->
<div class="meta-grid">
    <div class="meta-item">
        <label>Factuurdatum</label>
        <span><?= htmlspecialchars(date('d-m-Y', strtotime($invoice['issue_date'])), ENT_QUOTES, 'UTF-8') ?></span>
    </div>
    <div class="meta-item">
        <label>Vervaldatum</label>
        <span class="<?= $invoice['status'] === 'te-laat' ? 'overdue' : '' ?>">
            <?= htmlspecialchars(date('d-m-Y', strtotime($invoice['due_date'])), ENT_QUOTES, 'UTF-8') ?>
        </span>
    </div>
    <div class="meta-item">
        <label>Status</label>
        <span><?= htmlspecialchars(ucfirst(str_replace('-', ' ', $invoice['status'])), ENT_QUOTES, 'UTF-8') ?></span>
    </div>
</div>

<!-- ── Regelitems ── -->
<table>
    <thead>
        <tr>
            <th>Omschrijving</th>
            <th>Aantal</th>
            <th>Eenheidsprijs</th>
            <th>BTW %</th>
            <th>Regeltotaal</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($items as $item): ?>
        <tr>
            <td><?= htmlspecialchars($item['description'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= number_format((float) $item['quantity'], 2, ',', '.') ?></td>
            <td>€ <?= number_format((float) $item['unit_price'], 2, ',', '.') ?></td>
            <td><?= number_format((float) $item['vat_rate'], 0) ?>%</td>
            <td>€ <?= number_format((float) $item['quantity'] * (float) $item['unit_price'], 2, ',', '.') ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- ── Totalen ── -->
<div class="totals">
    <div class="totals-row">
        <span>Subtotaal excl. BTW</span>
        <span>€ <?= number_format($totals['subtotal'], 2, ',', '.') ?></span>
    </div>
    <?php if ($totals['vat']['21%'] > 0): ?>
    <div class="totals-row">
        <span>BTW 21%</span>
        <span>€ <?= number_format($totals['vat']['21%'], 2, ',', '.') ?></span>
    </div>
    <?php endif; ?>
    <?php if ($totals['vat']['9%'] > 0): ?>
    <div class="totals-row">
        <span>BTW 9%</span>
        <span>€ <?= number_format($totals['vat']['9%'], 2, ',', '.') ?></span>
    </div>
    <?php endif; ?>
    <?php if ($totals['vat']['0%'] > 0): ?>
    <div class="totals-row">
        <span>BTW 0%</span>
        <span>€ <?= number_format($totals['vat']['0%'], 2, ',', '.') ?></span>
    </div>
    <?php endif; ?>
    <div class="totals-row grand-total">
        <span>Totaal incl. BTW</span>
        <span>€ <?= number_format($totals['total'], 2, ',', '.') ?></span>
    </div>
</div>

<!-- ── Betaalinformatie ── -->
<div style="background:#f0f9ff;border:1px solid #bae6fd;border-radius:6px;padding:12px 16px;margin-top:24px;font-size:12px;">
    <strong style="font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:#0369a1;display:block;margin-bottom:4px;">Betaalinformatie</strong>
    Gelieve het bedrag van <strong>€ <?= number_format($totals['total'], 2, ',', '.') ?></strong>
    voor <?= htmlspecialchars(date('d-m-Y', strtotime($invoice['due_date'])), ENT_QUOTES, 'UTF-8') ?>
    over te maken onder vermelding van factuurnummer <strong><?= htmlspecialchars($invoice['invoice_number'], ENT_QUOTES, 'UTF-8') ?></strong>.
</div>

<!-- ── Notities ── -->
<?php if (!empty($invoice['notes'])): ?>
<div class="notes">
    <strong>Notities</strong>
    <?= nl2br(htmlspecialchars($invoice['notes'], ENT_QUOTES, 'UTF-8')) ?>
</div>
<?php endif; ?>

<!-- ── Footer ── -->
<div class="footer">
    JO Software Solutions &mdash; KVK: &mdash; BTW: &mdash;
    Factuur <?= htmlspecialchars($invoice['invoice_number'], ENT_QUOTES, 'UTF-8') ?> &mdash;
    Vervaldatum: <?= htmlspecialchars(date('d-m-Y', strtotime($invoice['due_date'])), ENT_QUOTES, 'UTF-8') ?>
</div>

</body>
</html>
