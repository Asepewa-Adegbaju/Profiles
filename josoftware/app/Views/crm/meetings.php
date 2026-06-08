<?php
use App\Core\CSRF;

$meetingStatusBadge = function(string $s): string {
    $map = [
        'gepland'   => 'badge badge-blue',
        'bevestigd' => 'badge badge-green',
        'geweest'   => 'badge badge-gray',
        'afgeslagen'=> 'badge badge-red',
    ];
    return $map[$s] ?? 'badge badge-gray';
};
?>

<div class="flex justify-between items-center mb-2">
    <h2 style="font-size:1.1rem;font-weight:700;">Aankomende afspraken</h2>
    <a href="<?= APP_URL ?>/crm" class="btn btn-secondary btn-sm">← Terug naar CRM</a>
</div>

<div class="card">
    <div class="table-wrapper">
        <?php if (empty($upcomingMeetings)): ?>
            <div style="padding:2rem;text-align:center;" class="text-muted">
                Geen aankomende afspraken gevonden.
            </div>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Datum</th>
                    <th>Tijd</th>
                    <th>Titel</th>
                    <th>Bedrijf</th>
                    <th>Contactpersoon</th>
                    <th>Locatie</th>
                    <th>Status</th>
                    <th>Acties</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($upcomingMeetings as $meeting): ?>
                <tr>
                    <td class="text-sm">
                        <?= htmlspecialchars(date('d-m-Y', strtotime($meeting['meeting_date'])), ENT_QUOTES, 'UTF-8') ?>
                    </td>
                    <td class="text-sm text-muted">
                        <?= htmlspecialchars(date('H:i', strtotime($meeting['meeting_date'])), ENT_QUOTES, 'UTF-8') ?>
                    </td>
                    <td class="font-bold"><?= htmlspecialchars($meeting['title'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="text-sm">
                        <a href="<?= APP_URL ?>/crm/<?= (int)$meeting['company_id'] ?>">
                            <?= htmlspecialchars($meeting['company_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </td>
                    <td class="text-sm"><?= htmlspecialchars($meeting['contact_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="text-sm text-muted"><?= htmlspecialchars($meeting['location'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <span class="<?= $meetingStatusBadge($meeting['status']) ?>">
                            <?= htmlspecialchars(ucfirst($meeting['status']), ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </td>
                    <td>
                        <form method="POST" action="<?= APP_URL ?>/crm/meeting/<?= (int)$meeting['id'] ?>/status" class="flex gap-1 items-center">
                            <?= CSRF::field() ?>
                            <select name="status" class="form-select" style="min-width:110px;padding:.25rem .5rem;font-size:.8rem;">
                                <option value="gepland"    <?= $meeting['status'] === 'gepland'    ? 'selected' : '' ?>>Gepland</option>
                                <option value="bevestigd"  <?= $meeting['status'] === 'bevestigd'  ? 'selected' : '' ?>>Bevestigd</option>
                                <option value="geweest"    <?= $meeting['status'] === 'geweest'    ? 'selected' : '' ?>>Geweest</option>
                                <option value="afgeslagen" <?= $meeting['status'] === 'afgeslagen' ? 'selected' : '' ?>>Afgeslagen</option>
                            </select>
                            <button type="submit" class="btn btn-secondary btn-sm">OK</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>
