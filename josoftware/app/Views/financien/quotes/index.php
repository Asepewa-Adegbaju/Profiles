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
        'concept'      => 'badge badge-gray',
        'verzonden'    => 'badge badge-blue',
        'geaccepteerd' => 'badge badge-green',
        'afgewezen'    => 'badge badge-red',
        'verlopen'     => 'badge badge-orange',
    ];
    return $map[$s] ?? 'badge badge-gray';
};
?>

<!-- ── Paginakop ──────────────────────────────────────────────────────────── -->
<div class="flex justify-between items-center mb-2">
    <h2 style="font-size:1.1rem;font-weight:700;">Offertes</h2>
    <a href="<?= APP_URL ?>/financien/offertes/nieuw" class="btn btn-primary btn-sm">+ Nieuwe offerte</a>
</div>

<!-- ── Status filter tabs ──────────────────────────────────────────────────── -->
<div class="card" style="margin-bottom:1rem;">
    <div class="card-body" style="padding:.6rem 1rem;">
        <div class="flex gap-1" style="flex-wrap:wrap;">
            <a href="<?= APP_URL ?>/financien/offertes" class="btn btn-sm <?= $status === '' ? 'btn-primary' : 'btn-secondary' ?>">Alle</a>
            <a href="<?= APP_URL ?>/financien/offertes?status=concept" class="btn btn-sm <?= $status === 'concept' ? 'btn-primary' : 'btn-secondary' ?>">Concept</a>
            <a href="<?= APP_URL ?>/financien/offertes?status=verzonden" class="btn btn-sm <?= $status === 'verzonden' ? 'btn-primary' : 'btn-secondary' ?>">Verzonden</a>
            <a href="<?= APP_URL ?>/financien/offertes?status=geaccepteerd" class="btn btn-sm <?= $status === 'geaccepteerd' ? 'btn-primary' : 'btn-secondary' ?>">Geaccepteerd</a>
            <a href="<?= APP_URL ?>/financien/offertes?status=afgewezen" class="btn btn-sm <?= $status === 'afgewezen' ? 'btn-primary' : 'btn-secondary' ?>">Afgewezen</a>
            <a href="<?= APP_URL ?>/financien/offertes?status=verlopen" class="btn btn-sm <?= $status === 'verlopen' ? 'btn-primary' : 'btn-secondary' ?>">Verlopen</a>
        </div>
    </div>
</div>

<!-- ── Tabel ──────────────────────────────────────────────────────────────── -->
<div class="card">
    <div class="table-wrapper">
        <?php if (empty($quotes)): ?>
            <div style="padding:2rem;text-align:center;" class="text-muted">
                Geen offertes gevonden.
                <a href="<?= APP_URL ?>/financien/offertes/nieuw">Maak de eerste offerte aan.</a>
            </div>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Nummer</th>
                    <th>Bedrijf</th>
                    <th>Datum</th>
                    <th>Geldig tot</th>
                    <th>Bedrag excl. BTW</th>
                    <th>Status</th>
                    <th>Acties</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($quotes as $q): ?>
                <tr>
                    <td>
                        <a href="<?= APP_URL ?>/financien/offertes/<?= (int) $q['id'] ?>" class="font-bold">
                            <?= htmlspecialchars($q['quote_number'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </td>
                    <td><?= htmlspecialchars($q['company_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="text-sm text-muted"><?= htmlspecialchars(fmt_date($q['issue_date']), ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="text-sm text-muted"><?= htmlspecialchars(fmt_date($q['valid_until']), ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="text-sm"><?= htmlspecialchars(fmt_money((float) $q['subtotal']), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><span class="<?= $badgeClass($q['status']) ?>"><?= htmlspecialchars($q['status'], ENT_QUOTES, 'UTF-8') ?></span></td>
                    <td>
                        <div class="flex gap-1" style="flex-wrap:wrap;">
                            <a href="<?= APP_URL ?>/financien/offertes/<?= (int) $q['id'] ?>" class="btn btn-secondary btn-sm">Details</a>
                            <a href="<?= APP_URL ?>/financien/offertes/<?= (int) $q['id'] ?>/print" class="btn btn-secondary btn-sm" target="_blank">Printen</a>

                            <!-- Status wijzigen inline -->
                            <form method="POST" action="<?= APP_URL ?>/financien/offertes/<?= (int) $q['id'] ?>/status" class="flex gap-1 items-center">
                                <?= CSRF::field() ?>
                                <select name="status" class="form-select" style="padding:.25rem .5rem;font-size:.75rem;height:auto;">
                                    <?php foreach (['concept','verzonden','geaccepteerd','afgewezen','verlopen'] as $opt): ?>
                                        <option value="<?= $opt ?>" <?= $q['status'] === $opt ? 'selected' : '' ?>><?= htmlspecialchars(ucfirst($opt), ENT_QUOTES, 'UTF-8') ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn btn-secondary btn-sm">OK</button>
                            </form>

                            <!-- Verwijderen -->
                            <form method="POST" action="<?= APP_URL ?>/financien/offertes/<?= (int) $q['id'] ?>/delete" style="display:inline;"
                                  onsubmit="return confirm('Offerte verwijderen? Dit kan niet ongedaan worden gemaakt.');">
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
