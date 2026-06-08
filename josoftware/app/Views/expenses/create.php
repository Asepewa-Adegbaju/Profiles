<?php
/** @var array $categories */
/** @var array $companies */
/** @var array $projects */
use App\Core\CSRF;
?>
<div class="page-header">
    <h2 class="page-subtitle">Nieuwe uitgave registreren</h2>
    <a href="<?= APP_URL ?>/uitgaven" class="btn btn-secondary">← Terug</a>
</div>

<div class="card" style="max-width:700px">
    <form method="POST" action="<?= APP_URL ?>/uitgaven/opslaan" enctype="multipart/form-data" class="form-grid">
        <?= CSRF::field() ?>

        <div class="form-row">
            <div class="form-group">
                <label for="entry_date">Datum <span class="required">*</span></label>
                <input type="date" id="entry_date" name="entry_date"
                       value="<?= htmlspecialchars($_POST['entry_date'] ?? date('Y-m-d'), ENT_QUOTES, 'UTF-8') ?>"
                       required class="form-control">
            </div>
            <div class="form-group">
                <label for="amount">Bedrag excl. BTW (€) <span class="required">*</span></label>
                <input type="text" id="amount" name="amount"
                       value="<?= htmlspecialchars($_POST['amount'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       placeholder="0,00" required class="form-control">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="vat_rate">BTW-tarief</label>
                <select id="vat_rate" name="vat_rate" class="form-control">
                    <?php foreach ([21, 9, 0] as $r): ?>
                        <option value="<?= $r ?>" <?= (($_POST['vat_rate'] ?? 21) == $r) ? 'selected' : '' ?>><?= $r ?>%</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="type">Type</label>
                <select id="type" name="type" class="form-control">
                    <option value="zakelijk" <?= (($_POST['type'] ?? 'zakelijk') === 'zakelijk') ? 'selected' : '' ?>>Zakelijk</option>
                    <option value="prive"    <?= (($_POST['type'] ?? '') === 'prive') ? 'selected' : '' ?>>Privé</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="description">Omschrijving <span class="required">*</span></label>
            <input type="text" id="description" name="description"
                   value="<?= htmlspecialchars($_POST['description'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                   placeholder="Bijv. Kantoorbenodigdheden" required class="form-control">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="supplier">Leverancier</label>
                <input type="text" id="supplier" name="supplier"
                       value="<?= htmlspecialchars($_POST['supplier'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       placeholder="Naam winkel / leverancier" class="form-control">
            </div>
            <div class="form-group">
                <label for="category_id">Categorie</label>
                <select id="category_id" name="category_id" class="form-control">
                    <option value="">— Geen categorie —</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"
                            <?= (($_POST['category_id'] ?? '') == $cat['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="company_id">Bedrijf (optioneel)</label>
                <select id="company_id" name="company_id" class="form-control">
                    <option value="">— Geen bedrijf —</option>
                    <?php foreach ($companies as $co): ?>
                        <option value="<?= $co['id'] ?>"
                            <?= (($_POST['company_id'] ?? '') == $co['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($co['name'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="project_id">Project (optioneel)</label>
                <select id="project_id" name="project_id" class="form-control">
                    <option value="">— Geen project —</option>
                    <?php foreach ($projects as $pr): ?>
                        <option value="<?= $pr['id'] ?>"
                            <?= (($_POST['project_id'] ?? '') == $pr['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($pr['name'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="notes">Notities</label>
            <textarea id="notes" name="notes" rows="2" class="form-control"
                      placeholder="Extra opmerkingen…"><?= htmlspecialchars($_POST['notes'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <div class="form-group">
            <label for="receipt">Bonnetje (JPG, PNG, GIF of PDF — max 5 MB)</label>
            <input type="file" id="receipt" name="receipt" class="form-control"
                   accept="image/jpeg,image/png,image/gif,application/pdf">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Opslaan</button>
            <a href="<?= APP_URL ?>/uitgaven" class="btn btn-secondary">Annuleren</a>
        </div>
    </form>
</div>
