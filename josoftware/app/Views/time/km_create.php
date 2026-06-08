<?php
use App\Core\CSRF;
?>

<!-- ── Paginakop ──────────────────────────────────────────────────────────── -->
<div class="flex justify-between items-center mb-2">
    <h2 style="font-size:1.1rem;font-weight:700;">Kilometers registreren</h2>
    <a href="<?= APP_URL ?>/kilometers" class="btn btn-secondary btn-sm">&#8592; Terug</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?= APP_URL ?>/kilometers/nieuw" id="km-form">
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

            <div class="form-row">
                <div class="form-group">
                    <label for="from_location" class="form-label">Van <span style="color:var(--danger);">*</span></label>
                    <input
                        type="text"
                        id="from_location"
                        name="from_location"
                        class="form-input"
                        placeholder="Vertreklocatie"
                        maxlength="200"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="to_location" class="form-label">Naar <span style="color:var(--danger);">*</span></label>
                    <input
                        type="text"
                        id="to_location"
                        name="to_location"
                        class="form-input"
                        placeholder="Aankomstlocatie"
                        maxlength="200"
                        required
                    >
                </div>
            </div>

            <div class="form-group">
                <label for="purpose" class="form-label">Doel <span style="color:var(--danger);">*</span></label>
                <input
                    type="text"
                    id="purpose"
                    name="purpose"
                    class="form-input"
                    placeholder="Omschrijf het reisdoel..."
                    maxlength="300"
                    required
                >
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="km" class="form-label">Kilometers <span style="color:var(--danger);">*</span></label>
                    <input
                        type="number"
                        id="km"
                        name="km"
                        class="form-input"
                        step="0.1"
                        min="0.1"
                        placeholder="0.0"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="rate_per_km" class="form-label">Tarief per km (€)</label>
                    <input
                        type="number"
                        id="rate_per_km"
                        name="rate_per_km"
                        class="form-input"
                        step="0.001"
                        min="0"
                        value="0.230"
                        readonly
                    >
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Berekende vergoeding</label>
                <div class="form-input" style="background:var(--gray-50);cursor:default;" id="vergoeding-display">&euro; 0,00</div>
            </div>

            <div class="flex gap-1 mt-2">
                <button type="submit" class="btn btn-primary">Opslaan</button>
                <a href="<?= APP_URL ?>/kilometers" class="btn btn-secondary">Annuleren</a>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    var kmInput   = document.getElementById('km');
    var rateInput = document.getElementById('rate_per_km');
    var display   = document.getElementById('vergoeding-display');

    function updateVergoeding() {
        var km   = parseFloat(kmInput.value)   || 0;
        var rate = parseFloat(rateInput.value) || 0;
        var amount = km * rate;
        display.textContent = '€ ' + amount.toFixed(2).replace('.', ',');
    }

    kmInput.addEventListener('input', updateVergoeding);
    rateInput.addEventListener('input', updateVergoeding);
    updateVergoeding();
})();
</script>
