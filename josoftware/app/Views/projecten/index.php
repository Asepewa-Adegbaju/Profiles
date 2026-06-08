<?php
use App\Core\CSRF;

$statusBadge = function(string $s): string {
    $map = [
        'actief'      => 'badge badge-green',
        'on-hold'     => 'badge badge-orange',
        'afgerond'    => 'badge badge-gray',
        'geannuleerd' => 'badge badge-red',
    ];
    return $map[$s] ?? 'badge badge-gray';
};

$statusLabel = function(string $s): string {
    $map = [
        'actief'      => 'Actief',
        'on-hold'     => 'On-hold',
        'afgerond'    => 'Afgerond',
        'geannuleerd' => 'Geannuleerd',
    ];
    return $map[$s] ?? ucfirst($s);
};

$currentFilter = $_GET['status'] ?? '';
$totalProjects = array_sum($statusCounts);
?>

<style>
.project-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px,1fr)); gap: 1rem; margin-top: 1rem; }
.project-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1.25rem; }
.project-card h3 { font-size: .95rem; font-weight: 700; margin-bottom: .25rem; }
.progress-bar { height: 6px; background: #e2e8f0; border-radius: 99px; margin: .75rem 0; }
.progress-fill { height: 100%; background: #2563eb; border-radius: 99px; }
</style>

<!-- ── Paginakop ──────────────────────────────────────────────────────────── -->
<div class="flex justify-between items-center mb-2">
    <h2 style="font-size:1.1rem;font-weight:700;">Projecten</h2>
    <a href="<?= APP_URL ?>/projecten/nieuw" class="btn btn-primary btn-sm">+ Nieuw project</a>
</div>

<!-- ── Statistieken strip ─────────────────────────────────────────────────── -->
<div class="stats-grid" style="margin-bottom:1rem;">
    <div class="stat-card">
        <div class="stat-icon stat-icon--green">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        </div>
        <div class="stat-content">
            <span class="stat-label">Actief</span>
            <span class="stat-value"><?= (int)($statusCounts['actief'] ?? 0) ?></span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon--orange">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        </div>
        <div class="stat-content">
            <span class="stat-label">On-hold</span>
            <span class="stat-value"><?= (int)($statusCounts['on-hold'] ?? 0) ?></span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon--blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <div class="stat-content">
            <span class="stat-label">Afgerond</span>
            <span class="stat-value"><?= (int)($statusCounts['afgerond'] ?? 0) ?></span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:var(--danger-light);color:var(--danger);">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
        </div>
        <div class="stat-content">
            <span class="stat-label">Geannuleerd</span>
            <span class="stat-value"><?= (int)($statusCounts['geannuleerd'] ?? 0) ?></span>
        </div>
    </div>
</div>

<!-- ── Filtertabs ─────────────────────────────────────────────────────────── -->
<div class="flex gap-1 mb-2" style="flex-wrap:wrap;">
    <a href="<?= APP_URL ?>/projecten"
       class="btn btn-sm <?= $currentFilter === '' ? 'btn-primary' : 'btn-secondary' ?>">
        Alle (<?= $totalProjects ?>)
    </a>
    <a href="<?= APP_URL ?>/projecten?status=actief"
       class="btn btn-sm <?= $currentFilter === 'actief' ? 'btn-primary' : 'btn-secondary' ?>">
        Actief
    </a>
    <a href="<?= APP_URL ?>/projecten?status=on-hold"
       class="btn btn-sm <?= $currentFilter === 'on-hold' ? 'btn-primary' : 'btn-secondary' ?>">
        On-hold
    </a>
    <a href="<?= APP_URL ?>/projecten?status=afgerond"
       class="btn btn-sm <?= $currentFilter === 'afgerond' ? 'btn-primary' : 'btn-secondary' ?>">
        Afgerond
    </a>
    <a href="<?= APP_URL ?>/projecten?status=geannuleerd"
       class="btn btn-sm <?= $currentFilter === 'geannuleerd' ? 'btn-primary' : 'btn-secondary' ?>">
        Geannuleerd
    </a>
</div>

<!-- ── Projecten grid ─────────────────────────────────────────────────────── -->
<?php if (empty($projects)): ?>
    <div class="card">
        <div class="card-body" style="text-align:center;padding:2rem;" >
            <p class="text-muted">Geen projecten gevonden.</p>
            <?php if ($currentFilter !== ''): ?>
                <a href="<?= APP_URL ?>/projecten">Toon alle projecten</a>
            <?php else: ?>
                <a href="<?= APP_URL ?>/projecten/nieuw">Maak het eerste project aan.</a>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
<div class="project-grid">
    <?php foreach ($projects as $project):
        $taskCount = (int)($project['task_count'] ?? 0);
        $tasksDone = (int)($project['tasks_done'] ?? 0);
        $progress  = $taskCount > 0 ? round($tasksDone / $taskCount * 100) : 0;
    ?>
    <div class="project-card">
        <div class="flex justify-between items-center" style="margin-bottom:.35rem;">
            <h3>
                <a href="<?= APP_URL ?>/projecten/<?= (int)$project['id'] ?>" style="color:inherit;text-decoration:none;">
                    <?= htmlspecialchars($project['name'], ENT_QUOTES, 'UTF-8') ?>
                </a>
            </h3>
            <span class="<?= $statusBadge($project['status']) ?>">
                <?= $statusLabel($project['status']) ?>
            </span>
        </div>

        <?php if (!empty($project['company_name'])): ?>
            <div class="text-muted text-sm" style="margin-bottom:.35rem;">
                <?= htmlspecialchars($project['company_name'], ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <!-- Progress -->
        <div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-muted"><?= $tasksDone ?>/<?= $taskCount ?> taken klaar</span>
                <span class="text-sm text-muted"><?= $progress ?>%</span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" style="width:<?= $progress ?>%;"></div>
            </div>
        </div>

        <!-- Datums & budget -->
        <div class="text-sm text-muted" style="margin-bottom:.75rem;">
            <?php if (!empty($project['start_date']) || !empty($project['end_date'])): ?>
                <div>
                    <?php if (!empty($project['start_date'])): ?>
                        <span><?= date('d-m-Y', strtotime($project['start_date'])) ?></span>
                    <?php endif; ?>
                    <?php if (!empty($project['end_date'])): ?>
                        <span> → <?= date('d-m-Y', strtotime($project['end_date'])) ?></span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($project['budget'])): ?>
                <div>Budget: €<?= number_format((float)$project['budget'], 2, ',', '.') ?></div>
            <?php endif; ?>
        </div>

        <!-- Acties -->
        <div class="flex gap-1" style="border-top:1px solid #e2e8f0;padding-top:.75rem;">
            <a href="<?= APP_URL ?>/projecten/<?= (int)$project['id'] ?>" class="btn btn-secondary btn-sm">Details</a>
            <a href="<?= APP_URL ?>/projecten/<?= (int)$project['id'] ?>/edit" class="btn btn-secondary btn-sm">Bewerken</a>
            <form method="POST"
                  action="<?= APP_URL ?>/projecten/<?= (int)$project['id'] ?>/delete"
                  style="display:inline;margin-left:auto;"
                  onsubmit="return confirm('Project verwijderen? Alle taken worden ook verwijderd. Dit kan niet ongedaan worden gemaakt.');">
                <?= CSRF::field() ?>
                <button type="submit" class="btn btn-danger btn-sm">Verwijderen</button>
            </form>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>
