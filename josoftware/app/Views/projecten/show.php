<?php
use App\Core\CSRF;

$projectStatusBadge = function(string $s): string {
    $map = [
        'actief'      => 'badge badge-green',
        'on-hold'     => 'badge badge-orange',
        'afgerond'    => 'badge badge-gray',
        'geannuleerd' => 'badge badge-red',
    ];
    return $map[$s] ?? 'badge badge-gray';
};

$projectStatusLabel = function(string $s): string {
    $map = [
        'actief'      => 'Actief',
        'on-hold'     => 'On-hold',
        'afgerond'    => 'Afgerond',
        'geannuleerd' => 'Geannuleerd',
    ];
    return $map[$s] ?? ucfirst($s);
};

$priorityBadge = function(string $p): string {
    $map = [
        'laag'    => 'badge badge-gray',
        'normaal' => 'badge badge-blue',
        'hoog'    => 'badge badge-orange',
        'urgent'  => 'badge badge-red',
    ];
    return $map[$p] ?? 'badge badge-gray';
};

$priorityLabel = function(string $p): string {
    $map = [
        'laag'    => 'Laag',
        'normaal' => 'Normaal',
        'hoog'    => 'Hoog',
        'urgent'  => 'Urgent',
    ];
    return $map[$p] ?? ucfirst($p);
};

$taskStatusBadge = function(string $s): string {
    $map = [
        'te-doen' => 'badge badge-gray',
        'bezig'   => 'badge badge-blue',
        'review'  => 'badge badge-orange',
        'klaar'   => 'badge badge-green',
    ];
    return $map[$s] ?? 'badge badge-gray';
};

$totalTasks = (int)($taskCounts['te-doen'] + $taskCounts['bezig'] + $taskCounts['review'] + $taskCounts['klaar']);
$doneTasks  = (int)$taskCounts['klaar'];
$progress   = $totalTasks > 0 ? round($doneTasks / $totalTasks * 100) : 0;
?>

<style>
.task-board { display: grid; grid-template-columns: repeat(4,1fr); gap: 1rem; margin-top: 1rem; }
.task-column { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1rem; min-height: 200px; }
.task-column-header { font-size: .75rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #64748b; margin-bottom: .75rem; }
.task-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 6px; padding: .75rem; margin-bottom: .5rem; }
.task-card-title { font-size: .875rem; font-weight: 600; margin-bottom: .35rem; }
@media (max-width: 900px) { .task-board { grid-template-columns: 1fr 1fr; } }
@media (max-width: 600px) { .task-board { grid-template-columns: 1fr; } }
</style>

<!-- ── Paginakop ──────────────────────────────────────────────────────────── -->
<div class="flex justify-between items-center mb-2">
    <div class="flex items-center gap-2">
        <a href="<?= APP_URL ?>/projecten" class="btn btn-secondary btn-sm">← Terug</a>
        <h2 style="font-size:1.1rem;font-weight:700;">
            <?= htmlspecialchars($project['name'], ENT_QUOTES, 'UTF-8') ?>
        </h2>
        <span class="<?= $projectStatusBadge($project['status']) ?>">
            <?= $projectStatusLabel($project['status']) ?>
        </span>
    </div>
    <div class="flex gap-1">
        <a href="<?= APP_URL ?>/projecten/<?= (int)$project['id'] ?>/edit" class="btn btn-secondary btn-sm">Bewerken</a>
        <form method="POST"
              action="<?= APP_URL ?>/projecten/<?= (int)$project['id'] ?>/delete"
              style="display:inline;"
              onsubmit="return confirm('Project verwijderen? Alle taken worden ook verwijderd. Dit kan niet ongedaan worden gemaakt.');">
            <?= CSRF::field() ?>
            <button type="submit" class="btn btn-danger btn-sm">Verwijderen</button>
        </form>
    </div>
</div>

<!-- ── Project info kaart ─────────────────────────────────────────────────── -->
<div class="card mb-2">
    <div class="card-body">
        <div class="form-row" style="grid-template-columns:1fr 1fr;">
            <div>
                <table style="width:100%;border-collapse:collapse;">
                    <tbody>
                        <tr>
                            <td class="text-muted text-sm" style="padding:.35rem 1rem .35rem 0;white-space:nowrap;width:130px;">Bedrijf</td>
                            <td class="text-sm">
                                <?php if (!empty($project['company_name'])): ?>
                                    <?= htmlspecialchars($project['company_name'], ENT_QUOTES, 'UTF-8') ?>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted text-sm" style="padding:.35rem 1rem .35rem 0;">Startdatum</td>
                            <td class="text-sm">
                                <?= !empty($project['start_date']) ? date('d-m-Y', strtotime($project['start_date'])) : '—' ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted text-sm" style="padding:.35rem 1rem .35rem 0;">Einddatum</td>
                            <td class="text-sm">
                                <?= !empty($project['end_date']) ? date('d-m-Y', strtotime($project['end_date'])) : '—' ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted text-sm" style="padding:.35rem 1rem .35rem 0;">Budget</td>
                            <td class="text-sm">
                                <?= !empty($project['budget']) ? '€' . number_format((float)$project['budget'], 2, ',', '.') : '—' ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted text-sm" style="padding:.35rem 1rem .35rem 0;">Aangemaakt door</td>
                            <td class="text-sm">
                                <?= !empty($project['created_by_name'])
                                    ? htmlspecialchars($project['created_by_name'], ENT_QUOTES, 'UTF-8')
                                    : '—' ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted text-sm" style="padding:.35rem 1rem .35rem 0;">Voortgang</td>
                            <td class="text-sm">
                                <?= $doneTasks ?>/<?= $totalTasks ?> taken klaar (<?= $progress ?>%)
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div>
                <?php if (!empty($project['description'])): ?>
                    <div class="text-muted text-sm" style="margin-bottom:.25rem;font-weight:600;">Omschrijving</div>
                    <p class="text-sm" style="white-space:pre-line;"><?= htmlspecialchars($project['description'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php else: ?>
                    <p class="text-muted text-sm">Geen omschrijving.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ── Takenbord ─────────────────────────────────────────────────────────── -->
<div class="flex justify-between items-center mt-2 mb-1">
    <h3 style="font-size:1rem;font-weight:700;">Taken</h3>
</div>

<div class="task-board">
    <?php
    $columns = [
        'te-doen' => 'Te doen',
        'bezig'   => 'Bezig',
        'review'  => 'Review',
        'klaar'   => 'Klaar',
    ];
    foreach ($columns as $colStatus => $colLabel):
        $colTasks = $tasksByStatus[$colStatus] ?? [];
    ?>
    <div class="task-column">
        <div class="task-column-header">
            <?= $colLabel ?>
            <span class="badge badge-gray" style="margin-left:.35rem;"><?= count($colTasks) ?></span>
        </div>

        <?php if (empty($colTasks)): ?>
            <p class="text-muted text-sm" style="font-style:italic;">Geen taken.</p>
        <?php endif; ?>

        <?php foreach ($colTasks as $task): ?>
        <div class="task-card">
            <div class="task-card-title">
                <?= htmlspecialchars($task['title'], ENT_QUOTES, 'UTF-8') ?>
            </div>
            <div class="flex gap-1" style="margin-bottom:.5rem;flex-wrap:wrap;">
                <span class="<?= $priorityBadge($task['priority']) ?>">
                    <?= $priorityLabel($task['priority']) ?>
                </span>
            </div>
            <?php if (!empty($task['assigned_name'])): ?>
                <div class="text-muted text-sm" style="margin-bottom:.25rem;">
                    Toegewezen: <?= htmlspecialchars($task['assigned_name'], ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($task['due_date'])): ?>
                <div class="text-muted text-sm" style="margin-bottom:.5rem;">
                    Deadline: <?= date('d-m-Y', strtotime($task['due_date'])) ?>
                </div>
            <?php endif; ?>

            <!-- Snelle status update -->
            <form method="POST"
                  action="<?= APP_URL ?>/projecten/<?= (int)$project['id'] ?>/taak/<?= (int)$task['id'] ?>/status"
                  style="margin-bottom:.5rem;">
                <?= CSRF::field() ?>
                <div class="flex gap-1 items-center">
                    <select name="status" class="form-select" style="font-size:.8rem;padding:.2rem .5rem;">
                        <?php foreach (['te-doen' => 'Te doen','bezig' => 'Bezig','review' => 'Review','klaar' => 'Klaar'] as $val => $lbl): ?>
                            <option value="<?= $val ?>" <?= $task['status'] === $val ? 'selected' : '' ?>>
                                <?= $lbl ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-secondary btn-sm">OK</button>
                </div>
            </form>

            <!-- Bewerken & Verwijderen -->
            <div class="flex gap-1">
                <a href="<?= APP_URL ?>/projecten/<?= (int)$project['id'] ?>/taak/<?= (int)$task['id'] ?>/edit"
                   class="btn btn-secondary btn-sm">Bewerken</a>
                <form method="POST"
                      action="<?= APP_URL ?>/projecten/<?= (int)$project['id'] ?>/taak/<?= (int)$task['id'] ?>/delete"
                      style="display:inline;"
                      onsubmit="return confirm('Taak verwijderen?');">
                    <?= CSRF::field() ?>
                    <button type="submit" class="btn btn-danger btn-sm">Verwijderen</button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endforeach; ?>
</div>

<!-- ── Nieuwe taak toevoegen ──────────────────────────────────────────────── -->
<div class="card mt-2">
    <div class="card-header">
        <span class="card-title">Nieuwe taak toevoegen</span>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= APP_URL ?>/projecten/<?= (int)$project['id'] ?>/taak">
            <?= CSRF::field() ?>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="task_title">Titel <span style="color:var(--danger);">*</span></label>
                    <input
                        type="text"
                        id="task_title"
                        name="title"
                        class="form-input"
                        maxlength="300"
                        required
                        placeholder="Taaknaam…"
                    >
                </div>
                <div class="form-group">
                    <label class="form-label" for="task_assigned_to">Toegewezen aan</label>
                    <select id="task_assigned_to" name="assigned_to" class="form-select">
                        <option value="">— Niemand —</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= (int)$user['id'] ?>">
                                <?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="task_priority">Prioriteit</label>
                    <select id="task_priority" name="priority" class="form-select">
                        <option value="laag">Laag</option>
                        <option value="normaal" selected>Normaal</option>
                        <option value="hoog">Hoog</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="task_due_date">Deadline</label>
                    <input type="date" id="task_due_date" name="due_date" class="form-input">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="task_description">Omschrijving</label>
                <textarea
                    id="task_description"
                    name="description"
                    class="form-textarea"
                    rows="3"
                    placeholder="Optionele omschrijving…"
                ></textarea>
            </div>

            <div class="flex gap-1 mt-1">
                <button type="submit" class="btn btn-primary">Taak toevoegen</button>
            </div>
        </form>
    </div>
</div>
