<?php
use App\Core\CSRF;
?>

<!-- ── Paginakop ──────────────────────────────────────────────────────────── -->
<div class="flex justify-between items-center mb-2">
    <h2 style="font-size:1.1rem;font-weight:700;">Nieuwe urenregistratie</h2>
    <a href="<?= APP_URL ?>/uren" class="btn btn-secondary btn-sm">&#8592; Terug</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?= APP_URL ?>/uren/nieuw">
            <?= CSRF::field() ?>

            <div class="form-row">
                <div class="form-group">
                    <label for="entry_date" class="form-label">Datum <span style="color:var(--danger);">*</span></label>
                    <input
                        type="date"
                        id="entry_date"
                        name="entry_date"
                        class="form-input"
                        value="<?= htmlspecialchars(date('Y-m-d'), ENT_QUOTES, 'UTF-8') ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="type" class="form-label">Type <span style="color:var(--danger);">*</span></label>
                    <select id="type" name="type" class="form-select" required>
                        <option value="zakelijk">Zakelijk</option>
                        <option value="prive">Privé</option>
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
                    placeholder="Beschrijf de werkzaamheden..."
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
                            <option value="<?= (int)$project['id'] ?>">
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
                            <option value="<?= (int)$company['id'] ?>">
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
                        placeholder="0.00"
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
                        value="0"
                        placeholder="0.00"
                    >
                </div>
            </div>

            <div class="form-group">
                <label class="flex items-center gap-1" style="cursor:pointer;">
                    <input type="checkbox" name="billable" value="1" checked style="width:auto;">
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
