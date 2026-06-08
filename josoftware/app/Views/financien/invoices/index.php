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

$badgeClass = function(string $s): string {
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
    <h2 style="font-size:1.1rem;font-weight:700;">Facturen</h2>
    <a href="<?= APP_URL ?>/financien/facturen/nieuw" class="btn btn-primary btn-sm">+ Nieuwe factuur</a>
</div>

<!-- ── Status filter tabs ──────────────────────────────────────────────────── -->
<div class="card" style="margin-bottom:1rem;">
    <div class="card-body" style="padding:.6rem 1rem;">
        <div class="flex gap-1" style="flex-wrap:wrap;">
            <a href="<?= APP_URL ?>/financien/facturen" class="btn btn-sm <?= $status === '' ? 'btn-primary' : 'btn-secondary' ?>">Alle</a>
            <a href="<?= APP_URL ?>/financien/facturen?status=concept" class="btn btn-sm <?= $status === 'concept' ? 'btn-primary' : 'btn-secondary' ?>">Concept</a>
            <a href="<?= APP_URL ?>/financien/facturen?status=verzonden" class="btn btn-sm <?= $status === 'verzonden' ? 'btn-primary' : 'btn-secondary' ?>">Verzonden</a>
            <a href="<?= APP_URL ?>/financien/facturen?status=betaald" class="btn btn-sm <?= $status === 'betaald' ? 'btn-primary' : 'btn-secondary' ?>">Betaald</a>
            <a href="<?= APP_URL ?>/financien/facturen?status=te-laat" class="btn btn-sm <?= $status === 'te-laat' ? 'btn-primary' : 'btn-secondary' ?>">Te laat</a>
            <a href="<?= APP_URL ?>/financien/facturen?status=geannuleerd" class="btn btn-sm <?= $status === 'geannuleerd' ? 'btn-primary' : 'btn-secondary' ?>">Geannuleerd</a>
        </div>
    </div>
</div>

<!-- ── Tabel ──────────────────────────────────────────────────────────────── -->
<div class="card">
    <div class="table-wrapper">
        <?php if (empty($invoices)): ?>
            <div style="padding:2rem;text-align:center;" class="text-muted">
                Geen facturen gevonden.
                <a href="<?= APP_URL ?>/financien/facturen/nieuw">Maak de eerste factuur aan.</a>
            </div>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Nummer</th>
                    <th>Bedrijf</th>
                    <th>Factuurdatum</th>
                    <th>Vervaldatum</th>
                    <th>Bedrag excl. BTW</th>
                    <th>Status</th>
                    <th>Acties</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($invoices as $inv): ?>
                <tr>
                    <td>
                        <a href="<?= APP_URL ?>/financien/facturen/<?= (int) $inv['id'] ?>" class="font-bold">
                            <?= htmlspecialchars($inv['invoice_number'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </td>
                    <td><?= htmlspecialchars($inv['company_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="text-sm text-muted"><?= htmlspecialchars(fmt_date($inv['issue_date']), ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="text-sm <?= $inv['status'] === 'te-laat' ? 'font-bold' : 'text-muted' ?>"
                        style="<?= $inv['status'] === 'te-laat' ? 'color:#ef4444;' : '' ?>">
                        <?= htmlspecialchars(fmt_date($inv['due_date']), ENT_QUOTES, 'UTF-8') ?>
                    </td>
                    <td class="text-sm"><?= htmlspecialchars(fmt_money((float) $inv['subtotal']), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><span class="<?= $badgeClass($inv['status']) ?>"><?= htmlspecialchars($inv['status'], ENT_QUOTES, 'UTF-8') ?></span></td>
                    <td>
                        <div class="flex gap-1" style="flex-wrap:wrap;">
                            <a href="<?= APP_URL ?>/financien/facturen/<?= (int) $inv['id'] ?>" class="btn btn-secondary btn-sm">Details</a>
                            <a href="<?= APP_URL ?>/financien/facturen/<?= (int) $inv['id'] ?>/print" class="btn btn-secondary btn-sm" target="_blank">Printen</a>

                            <!-- Status wijzigen inline -->
                            <form method="POST" action="<?= APP_URL ?>/financien/facturen/<?= (int) $inv['id'] ?>/status" class="flex gap-1 items-center">
                                <?= CSRF::field() ?>
                                <select name="status" class="form-select" style="padding:.25rem .5rem;font-size:.75rem;height:auto;">
                                    <?php foreach (['concept','verzonden','betaald','te-laat','geannuleerd'] as $opt): ?>
                                        <option value="<?= $opt ?>" <?= $inv['status'] === $opt ? 'selected' : '' ?>>
                                            <?= htmlspecialchars(ucfirst(str_replace('-', ' ', $opt)), ENT_QUOTES, 'UTF-8') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn btn-secondary btn-sm">OK</button>
                            </form>

                            <!-- Verwijderen -->
                            <form method="POST" action="<?= APP_URL ?>/financien/facturen/<?= (int) $inv['id'] ?>/delete" style="display:inline;"
                                  onsubmit="return confirm('Factuur verwijderen? Dit kan niet ongedaan worden gemaakt.');">
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
