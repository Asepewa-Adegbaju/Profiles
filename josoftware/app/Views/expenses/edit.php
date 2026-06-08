<?php
/** @var array $expense */
/** @var array $categories */
/** @var array $companies */
/** @var array $projects */
use App\Core\CSRF;
$v = fn(string $field) => htmlspecialchars($_POST[$field] ?? $expense[$field] ?? '', ENT_QUOTES, 'UTF-8');
?>
<div class="page-header">
    <h2 class="page-subtitle">Uitgave bewerken</h2>
    <a href="<?= APP_URL ?>/uitgaven/<?= $expense['id'] ?>" class="btn btn-secondary">← Terug</a>
</div>

<div class="card" style="max-width:700px">
    <form method="POST" action="<?= APP_URL ?>/uitgaven/<?= $expense['id'] ?>/bijwerken"
          enctype="multipart/form-data" class="form-grid">
        <?= CSRF::field() ?>

        <div class="form-row">
            <div class="form-group">
                <label for="entry_date">Datum <span class="required">*</span></label>
                <input type="date" id="entry_date" name="entry_date"
                       value="<?= $v('entry_date') ?>" required class="form-control">
            </div>
            <div class="form-group">
                <label for="amount">Bedrag excl. BTW (€) <span class="required">*</span></label>
                <input type="text" id="amount" name="amount"
                       value="<?= $v('amount') ?>" required class="form-control">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="vat_rate">BTW-tarief</label>
                <select id="vat_rate" name="vat_rate" class="form-control">
                    <?php foreach ([21, 9, 0] as $r): ?>
                        <option value="<?= $r ?>"
                            <?= (($_POST['vat_rate'] ?? $expense['vat_rate']) == $r) ? 'selected' : '' ?>><?= $r ?>%</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="type">Type</label>
                <select id="type" name="type" class="form-control">
                    <?php $curType = $_POST['type'] ?? $expense['type'] ?? 'zakelijk'; ?>
                    <option value="zakelijk" <?= $curType === 'zakelijk' ? 'selected' : '' ?>>Zakelijk</option>
                    <option value="prive"    <?= $curType === 'prive'    ? 'selected' : '' ?>>Privé</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="description">Omschrijving <span class="required">*</span></label>
            <input type="text" id="description" name="description"
                   value="<?= $v('description') ?>" required class="form-control">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="supplier">Leverancier</label>
                <input type="text" id="supplier" name="supplier"
                       value="<?= $v('supplier') ?>" class="form-control">
            </div>
            <div class="form-group">
                <label for="category_id">Categorie</label>
                <select id="category_id" name="category_id" class="form-control">
                    <option value="">— Geen categorie —</option>
                    <?php
                    $curCat = $_POST['category_id'] ?? $expense['category_id'] ?? '';
                    foreach ($categories as $cat):
                    ?>
                        <option value="<?= $cat['id'] ?>" <?= $curCat == $cat['id'] ? 'selected' : '' ?>>
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
                    <?php
                    $curCo = $_POST['company_id'] ?? $expense['company_id'] ?? '';
                    foreach ($companies as $co):
                    ?>
                        <option value="<?= $co['id'] ?>" <?= $curCo == $co['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($co['name'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="project_id">Project (optioneel)</label>
                <select id="project_id" name="project_id" class="form-control">
                    <option value="">— Geen project —</option>
                    <?php
                    $curPr = $_POST['project_id'] ?? $expense['project_id'] ?? '';
                    foreach ($projects as $pr):
                    ?>
                        <option value="<?= $pr['id'] ?>" <?= $curPr == $pr['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($pr['name'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="status">Status</label>
            <select id="status" name="status" class="form-control">
                <?php $curStatus = $_POST['status'] ?? $expense['status'] ?? 'ingediend'; ?>
                <option value="ingediend"   <?= $curStatus === 'ingediend'   ? 'selected' : '' ?>>Ingediend</option>
                <option value="goedgekeurd" <?= $curStatus === 'goedgekeurd' ? 'selected' : '' ?>>Goedgekeurd</option>
                <option value="afgewezen"   <?= $curStatus === 'afgewezen'   ? 'selected' : '' ?>>Afgewezen</option>
            </select>
        </div>

        <div class="form-group">
            <label for="notes">Notities</label>
            <textarea id="notes" name="notes" rows="2" class="form-control"><?= $v('notes') ?></textarea>
        </div>

        <div class="form-group">
            <label>Bonnetje</label>
            <?php if ($expense['receipt_filename']): ?>
                <p class="form-hint">
                    Huidig bonnetje:
                    <a href="<?= APP_URL ?>/uitgaven/<?= $expense['id'] ?>/bonnetje" target="_blank">bekijken</a>
                    — Upload een nieuw bestand om het te vervangen.
                </p>
            <?php endif; ?>
            <input type="file" id="receipt" name="receipt" class="form-control"
                   accept="image/jpeg,image/png,image/gif,application/pdf">
            <small class="form-hint">JPG, PNG, GIF of PDF — max 5 MB</small>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Opslaan</button>
            <a href="<?= APP_URL ?>/uitgaven/<?= $expense['id'] ?>" class="btn btn-secondary">Annuleren</a>
        </div>
    </form>
</div>
