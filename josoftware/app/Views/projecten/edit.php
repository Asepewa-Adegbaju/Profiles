<?php use App\Core\CSRF; ?>

<div class="flex justify-between items-center mb-2">
    <h2 style="font-size:1.1rem;font-weight:700;">Project bewerken</h2>
    <a href="<?= APP_URL ?>/projecten/<?= (int)$project['id'] ?>" class="btn btn-secondary btn-sm">← Terug</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?= APP_URL ?>/projecten/<?= (int)$project['id'] ?>/edit">
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
                        value="<?= htmlspecialchars($_POST['name'] ?? $project['name'], ENT_QUOTES, 'UTF-8') ?>"
                    >
                </div>
                <div class="form-group">
                    <label class="form-label" for="company_id">Bedrijf</label>
                    <select id="company_id" name="company_id" class="form-select">
                        <option value="">— Geen bedrijf —</option>
                        <?php
                        $selectedCompany = $_POST['company_id'] ?? $project['company_id'];
                        foreach ($companies as $company):
                        ?>
                            <option value="<?= (int)$company['id'] ?>"
                                <?= ($selectedCompany == $company['id']) ? 'selected' : '' ?>>
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
                        <?php
                        $selectedStatus = $_POST['status'] ?? $project['status'];
                        $statusOptions  = [
                            'actief'      => 'Actief',
                            'on-hold'     => 'On-hold',
                            'afgerond'    => 'Afgerond',
                            'geannuleerd' => 'Geannuleerd',
                        ];
                        foreach ($statusOptions as $val => $lbl):
                        ?>
                            <option value="<?= $val ?>" <?= $selectedStatus === $val ? 'selected' : '' ?>>
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
                        value="<?= htmlspecialchars($_POST['budget'] ?? $project['budget'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
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
                        value="<?= htmlspecialchars($_POST['start_date'] ?? $project['start_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    >
                </div>
                <div class="form-group">
                    <label class="form-label" for="end_date">Einddatum</label>
                    <input
                        type="date"
                        id="end_date"
                        name="end_date"
                        class="form-input"
                        value="<?= htmlspecialchars($_POST['end_date'] ?? $project['end_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
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
                ><?= htmlspecialchars($_POST['description'] ?? $project['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <div class="flex gap-1 mt-2">
                <button type="submit" class="btn btn-primary">Wijzigingen opslaan</button>
                <a href="<?= APP_URL ?>/projecten/<?= (int)$project['id'] ?>" class="btn btn-secondary">Annuleren</a>
            </div>
        </form>
    </div>
</div>
