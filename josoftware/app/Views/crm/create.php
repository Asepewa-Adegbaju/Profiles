<?php use App\Core\CSRF; ?>

<div class="flex justify-between items-center mb-2">
    <h2 style="font-size:1.1rem;font-weight:700;">Nieuw bedrijf</h2>
    <a href="<?= APP_URL ?>/crm" class="btn btn-secondary btn-sm">← Terug</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?= APP_URL ?>/crm/create">
            <?= CSRF::field() ?>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="name">Bedrijfsnaam <span style="color:var(--danger);">*</span></label>
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
                    <label class="form-label" for="sector">Sector</label>
                    <input
                        type="text"
                        id="sector"
                        name="sector"
                        class="form-input"
                        maxlength="100"
                        value="<?= htmlspecialchars($_POST['sector'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    >
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="phone">Telefoon</label>
                    <input
                        type="text"
                        id="phone"
                        name="phone"
                        class="form-input"
                        maxlength="30"
                        value="<?= htmlspecialchars($_POST['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    >
                </div>
                <div class="form-group">
                    <label class="form-label" for="email">E-mailadres</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-input"
                        maxlength="150"
                        value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    >
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="website">Website</label>
                    <input
                        type="text"
                        id="website"
                        name="website"
                        class="form-input"
                        maxlength="200"
                        placeholder="https://…"
                        value="<?= htmlspecialchars($_POST['website'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    >
                </div>
                <div class="form-group">
                    <label class="form-label" for="status">Status</label>
                    <select id="status" name="status" class="form-select">
                        <?php foreach (['lead' => 'Lead', 'prospect' => 'Prospect', 'klant' => 'Klant', 'inactief' => 'Inactief'] as $val => $label): ?>
                            <option value="<?= $val ?>" <?= (($_POST['status'] ?? 'lead') === $val) ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="address">Adres</label>
                <input
                    type="text"
                    id="address"
                    name="address"
                    class="form-input"
                    maxlength="300"
                    value="<?= htmlspecialchars($_POST['address'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                >
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="city">Stad</label>
                    <input
                        type="text"
                        id="city"
                        name="city"
                        class="form-input"
                        maxlength="100"
                        value="<?= htmlspecialchars($_POST['city'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    >
                </div>
                <div class="form-group">
                    <label class="form-label" for="postal_code">Postcode</label>
                    <input
                        type="text"
                        id="postal_code"
                        name="postal_code"
                        class="form-input"
                        maxlength="20"
                        value="<?= htmlspecialchars($_POST['postal_code'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    >
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
                <button type="submit" class="btn btn-primary">Bedrijf opslaan</button>
                <a href="<?= APP_URL ?>/crm" class="btn btn-secondary">Annuleren</a>
            </div>
        </form>
    </div>
</div>
