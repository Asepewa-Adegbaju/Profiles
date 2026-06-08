<?php
/** @var array $expense */
use App\Core\CSRF;
$vatAmount   = round((float)$expense['amount'] * (float)$expense['vat_rate'] / 100, 2);
$amountIncl  = round((float)$expense['amount'] + $vatAmount, 2);
$statusClass = match($expense['status']) {
    'goedgekeurd' => 'badge-success',
    'afgewezen'   => 'badge-danger',
    default       => 'badge-warning',
};
$typeLabel = $expense['type'] === 'zakelijk' ? 'Zakelijk' : 'Privé';
?>
<div class="page-header">
    <h2 class="page-subtitle">Uitgave details</h2>
    <div class="header-actions">
        <a href="<?= APP_URL ?>/uitgaven/<?= $expense['id'] ?>/bewerken" class="btn btn-secondary">Bewerken</a>
        <a href="<?= APP_URL ?>/uitgaven" class="btn btn-secondary">← Terug</a>
    </div>
</div>

<div class="card" style="max-width:700px">
    <div class="detail-grid">
        <div class="detail-row">
            <span class="detail-label">Datum</span>
            <span class="detail-value"><?= htmlspecialchars(date('d-m-Y', strtotime($expense['entry_date'])), ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Omschrijving</span>
            <span class="detail-value"><?= htmlspecialchars($expense['description'], ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <?php if ($expense['supplier']): ?>
        <div class="detail-row">
            <span class="detail-label">Leverancier</span>
            <span class="detail-value"><?= htmlspecialchars($expense['supplier'], ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <?php endif; ?>
        <div class="detail-row">
            <span class="detail-label">Bedrag excl. BTW</span>
            <span class="detail-value">€ <?= number_format((float)$expense['amount'], 2, ',', '.') ?></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">BTW (<?= $expense['vat_rate'] ?>%)</span>
            <span class="detail-value">€ <?= number_format($vatAmount, 2, ',', '.') ?></span>
        </div>
        <div class="detail-row">
            <span class="detail-label"><strong>Totaal incl. BTW</strong></span>
            <span class="detail-value"><strong>€ <?= number_format($amountIncl, 2, ',', '.') ?></strong></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Type</span>
            <span class="detail-value"><?= $typeLabel ?></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Status</span>
            <span class="detail-value">
                <span class="badge <?= $statusClass ?>"><?= ucfirst($expense['status']) ?></span>
            </span>
        </div>
        <?php if ($expense['category_name']): ?>
        <div class="detail-row">
            <span class="detail-label">Categorie</span>
            <span class="detail-value">
                <?php if ($expense['category_color']): ?>
                    <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:<?= htmlspecialchars($expense['category_color'], ENT_QUOTES, 'UTF-8') ?>;margin-right:5px;"></span>
                <?php endif; ?>
                <?= htmlspecialchars($expense['category_name'], ENT_QUOTES, 'UTF-8') ?>
            </span>
        </div>
        <?php endif; ?>
        <?php if ($expense['company_name']): ?>
        <div class="detail-row">
            <span class="detail-label">Bedrijf</span>
            <span class="detail-value">
                <a href="<?= APP_URL ?>/crm/<?= $expense['company_id'] ?>"><?= htmlspecialchars($expense['company_name'], ENT_QUOTES, 'UTF-8') ?></a>
            </span>
        </div>
        <?php endif; ?>
        <?php if ($expense['project_name']): ?>
        <div class="detail-row">
            <span class="detail-label">Project</span>
            <span class="detail-value">
                <a href="<?= APP_URL ?>/projecten/<?= $expense['project_id'] ?>"><?= htmlspecialchars($expense['project_name'], ENT_QUOTES, 'UTF-8') ?></a>
            </span>
        </div>
        <?php endif; ?>
        <?php if ($expense['notes']): ?>
        <div class="detail-row">
            <span class="detail-label">Notities</span>
            <span class="detail-value"><?= nl2br(htmlspecialchars($expense['notes'], ENT_QUOTES, 'UTF-8')) ?></span>
        </div>
        <?php endif; ?>
        <div class="detail-row">
            <span class="detail-label">Ingevoerd door</span>
            <span class="detail-value"><?= htmlspecialchars($expense['user_name'] ?? '-', ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <?php if ($expense['receipt_filename']): ?>
        <div class="detail-row">
            <span class="detail-label">Bonnetje</span>
            <span class="detail-value">
                <a href="<?= APP_URL ?>/uitgaven/<?= $expense['id'] ?>/bonnetje" target="_blank" class="btn btn-secondary btn-sm">
                    📎 Bonnetje bekijken
                </a>
            </span>
        </div>
        <?php endif; ?>
    </div>

    <div class="form-actions" style="margin-top:1.5rem;border-top:1px solid var(--border);padding-top:1rem;">
        <a href="<?= APP_URL ?>/uitgaven/<?= $expense['id'] ?>/bewerken" class="btn btn-primary">Bewerken</a>
        <form method="POST" action="<?= APP_URL ?>/uitgaven/<?= $expense['id'] ?>/verwijderen"
              style="display:inline"
              onsubmit="return confirm('Weet je zeker dat je deze uitgave wilt verwijderen?');">
            <?= CSRF::field() ?>
            <button type="submit" class="btn btn-danger">Verwijderen</button>
        </form>
        <a href="<?= APP_URL ?>/uitgaven" class="btn btn-secondary">Terug</a>
    </div>
</div>
