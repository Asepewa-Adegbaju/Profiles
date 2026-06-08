<?php
use App\Core\CSRF;
?>

<!-- ── Paginakop ──────────────────────────────────────────────────────────── -->
<div class="flex justify-between items-center mb-2">
    <h2 style="font-size:1.1rem;font-weight:700;">Uren bewerken</h2>
    <a href="<?= APP_URL ?>/uren" class="btn btn-secondary btn-sm">&#8592; Terug</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?= APP_URL ?>/uren/<?= (int)$entry['id'] ?>/edit">
            <?= CSRF::field() ?>

            <div class="form-row">
                <div class="form-group">
                    <label for="entry_date" class="form-label">Datum <span style="color:var(--danger);">*</span></label>
                    <input
                        type="date"
                        id="entry_date"
                        name="entry_date"
                        class="form-input"
                        value="<?= htmlspecialchars($entry['entry_date'], ENT_QUOTES, 'UTF-8') ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="type" class="form-label">Type <span style="color:var(--danger);">*</span></label>
                    <select id="type" name="type" class="form-select" required>
                        <option value="zakelijk" <?= $entry['type'] === 'zakelijk' ? 'selected' : '' ?>>Zakelijk</option>
                        <option value="prive" <?= $entry['type'] === 'prive' ? 'selected' : '' ?>>Privé</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Omschrijving <span style="color:var(--danger);">*</span></label>
                <input
                    type="text"
                    id="description"
                    name="description"
                    class="form-input"
                    value="<?= htmlspecialchars($entry['description'], ENT_QUOTES, 'UTF-8') ?>"
                    maxlength="500"
                    required
                >
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="project_id" class="form-label">Project</label>
                    <select id="project_id" name="project_id" class="form-select">
                        <option value="0">— Geen project —</option>
                        <?php foreach ($projects as $project): ?>
                            <option value="<?= (int)$project['id'] ?>" <?= (int)($entry['project_id'] ?? 0) === (int)$project['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($project['name'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="company_id" class="form-label">Bedrijf</label>
                    <select id="company_id" name="company_id" class="form-select">
                        <option value="0">— Geen bedrijf —</option>
                        <?php foreach ($companies as $company): ?>
                            <option value="<?= (int)$company['id'] ?>" <?= (int)($entry['company_id'] ?? 0) === (int)$company['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($company['name'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="hours" class="form-label">Uren <span style="color:var(--danger);">*</span></label>
                    <input
                        type="number"
                        id="hours"
                        name="hours"
                        class="form-input"
                        step="0.25"
                        min="0.25"
                        max="24"
                        value="<?= htmlspecialchars(number_format((float)$entry['hours'], 2, '.', ''), ENT_QUOTES, 'UTF-8') ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="hourly_rate" class="form-label">Uurtarief (€)</label>
                    <input
                        type="number"
                        id="hourly_rate"
                        name="hourly_rate"
                        class="form-input"
                        step="0.01"
                        min="0"
                        value="<?= htmlspecialchars(number_format((float)$entry['hourly_rate'], 2, '.', ''), ENT_QUOTES, 'UTF-8') ?>"
                    >
                </div>
            </div>

            <div class="form-group">
                <label class="flex items-center gap-1" style="cursor:pointer;">
                    <input type="checkbox" name="billable" value="1" <?= $entry['billable'] ? 'checked' : '' ?> style="width:auto;">
                    <span class="form-label" style="margin-bottom:0;">Factureerbaar</span>
                </label>
            </div>

            <div class="flex gap-1 mt-2">
                <button type="submit" class="btn btn-primary">Opslaan</button>
                <a href="<?= APP_URL ?>/uren" class="btn btn-secondary">Annuleren</a>
            </div>
        </form>
    </div>
</div>
