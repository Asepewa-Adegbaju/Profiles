<?php use App\Core\CSRF; ?>

<div class="flex justify-between items-center mb-2">
    <h2 style="font-size:1.1rem;font-weight:700;">Nieuwe afspraak — <?= htmlspecialchars($company['name'], ENT_QUOTES, 'UTF-8') ?></h2>
    <a href="<?= APP_URL ?>/crm/<?= (int)$company['id'] ?>" class="btn btn-secondary btn-sm">← Terug</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?= APP_URL ?>/crm/<?= (int)$company['id'] ?>/meeting">
            <?= CSRF::field() ?>

            <div class="form-group">
                <label class="form-label" for="title">Titel <span style="color:var(--danger);">*</span></label>
                <input
                    type="text"
                    id="title"
                    name="title"
                    class="form-input"
                    maxlength="200"
                    required
                    autofocus
                    value="<?= htmlspecialchars($_POST['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                >
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="meeting_date">Datum &amp; tijd <span style="color:var(--danger);">*</span></label>
                    <input
                        type="datetime-local"
                        id="meeting_date"
                        name="meeting_date"
                        class="form-input"
                        required
                        value="<?= htmlspecialchars($_POST['meeting_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    >
                </div>
                <div class="form-group">
                    <label class="form-label" for="location">Locatie</label>
                    <input
                        type="text"
                        id="location"
                        name="location"
                        class="form-input"
                        maxlength="200"
                        value="<?= htmlspecialchars($_POST['location'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    >
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="contact_id">Contactpersoon</label>
                    <select id="contact_id" name="contact_id" class="form-select">
                        <option value="">— geen —</option>
                        <?php foreach ($contacts as $contact): ?>
                            <option value="<?= (int)$contact['id'] ?>" <?= (($_POST['contact_id'] ?? '') == $contact['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($contact['name'], ENT_QUOTES, 'UTF-8') ?>
                                <?php if ($contact['function']): ?>(<?= htmlspecialchars($contact['function'], ENT_QUOTES, 'UTF-8') ?>)<?php endif; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="status">Status</label>
                    <select id="status" name="status" class="form-select">
                        <option value="gepland"    <?= (($_POST['status'] ?? 'gepland') === 'gepland')    ? 'selected' : '' ?>>Gepland</option>
                        <option value="bevestigd"  <?= (($_POST['status'] ?? '') === 'bevestigd')         ? 'selected' : '' ?>>Bevestigd</option>
                        <option value="geweest"    <?= (($_POST['status'] ?? '') === 'geweest')           ? 'selected' : '' ?>>Geweest</option>
                        <option value="afgeslagen" <?= (($_POST['status'] ?? '') === 'afgeslagen')        ? 'selected' : '' ?>>Afgeslagen</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="notes">Notities</label>
                <textarea
                    id="notes"
                    name="notes"
                    class="form-textarea"
                    rows="4"
                ><?= htmlspecialchars($_POST['notes'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <div class="flex gap-1 mt-2">
                <button type="submit" class="btn btn-primary">Afspraak aanmaken</button>
                <a href="<?= APP_URL ?>/crm/<?= (int)$company['id'] ?>" class="btn btn-secondary">Annuleren</a>
            </div>
        </form>
    </div>
</div>
