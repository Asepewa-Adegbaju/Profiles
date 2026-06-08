<?php use App\Core\CSRF; ?>

<div class="flex justify-between items-center mb-2">
    <h2 style="font-size:1.1rem;font-weight:700;">Nieuw project</h2>
    <a href="<?= APP_URL ?>/projecten" class="btn btn-secondary btn-sm">← Terug</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?= APP_URL ?>/projecten/nieuw">
            <?= CSRF::field() ?>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="name">Projectnaam <span style="color:var(--danger);">*</span></label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-input"
                        maxlength="200"
                        required
                        autofocus
                        value="<?= htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    >
                </div>
                <div class="form-group">
                    <label class="form-label" for="company_id">Bedrijf</label>
                    <select id="company_id" name="company_id" class="form-select">
                        <option value="">— Geen bedrijf —</option>
                        <?php foreach ($companies as $company): ?>
                            <option value="<?= (int)$company['id'] ?>"
                                <?= (($_POST['company_id'] ?? '') == $company['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($company['name'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="status">Status</label>
                    <select id="status" name="status" class="form-select">
                        <?php foreach (['actief' => 'Actief', 'on-hold' => 'On-hold', 'geannuleerd' => 'Geannuleerd'] as $val => $lbl): ?>
                            <option value="<?= $val ?>"
                                <?= (($_POST['status'] ?? 'actief') === $val) ? 'selected' : '' ?>>
                                <?= $lbl ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="budget">Budget (€)</label>
                    <input
                        type="number"
                        id="budget"
                        name="budget"
                        class="form-input"
                        min="0"
                        step="0.01"
                        placeholder="0.00"
                        value="<?= htmlspecialchars($_POST['budget'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    >
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="start_date">Startdatum</label>
                    <input
                        type="date"
                        id="start_date"
                        name="start_date"
                        class="form-input"
                        value="<?= htmlspecialchars($_POST['start_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    >
                </div>
                <div class="form-group">
                    <label class="form-label" for="end_date">Einddatum</label>
                    <input
                        type="date"
                        id="end_date"
                        name="end_date"
                        class="form-input"
                        value="<?= htmlspecialchars($_POST['end_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    >
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Omschrijving</label>
                <textarea
                    id="description"
                    name="description"
                    class="form-textarea"
                    rows="4"
                ><?= htmlspecialchars($_POST['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <div class="flex gap-1 mt-2">
                <button type="submit" class="btn btn-primary">Project opslaan</button>
                <a href="<?= APP_URL ?>/projecten" class="btn btn-secondary">Annuleren</a>
            </div>
        </form>
    </div>
</div>
