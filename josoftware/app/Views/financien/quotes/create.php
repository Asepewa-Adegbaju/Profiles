<?php
use App\Core\CSRF;
?>

<style>
.items-table { width: 100%; border-collapse: collapse; margin: 1rem 0; }
.items-table th { padding: .5rem; text-align: left; font-size: .75rem; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: #64748b; border-bottom: 2px solid #e2e8f0; }
.items-table td { padding: .5rem; border-bottom: 1px solid #e2e8f0; vertical-align: middle; }
.items-table input { width: 100%; padding: .4rem .6rem; border: 1px solid #e2e8f0; border-radius: 6px; font-size: .875rem; }
.items-table select { padding: .4rem .6rem; border: 1px solid #e2e8f0; border-radius: 6px; font-size: .875rem; }
.totals-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1rem; max-width: 380px; margin-left: auto; }
.totals-row { display: flex; justify-content: space-between; padding: .35rem 0; font-size: .875rem; }
.totals-row.total { font-weight: 700; font-size: 1rem; border-top: 2px solid #e2e8f0; margin-top: .35rem; padding-top: .6rem; }
</style>

<!-- ── Paginakop ──────────────────────────────────────────────────────────── -->
<div class="flex justify-between items-center mb-2">
    <h2 style="font-size:1.1rem;font-weight:700;">Nieuwe offerte</h2>
    <a href="<?= APP_URL ?>/financien/offertes" class="btn btn-secondary btn-sm">Terug naar offertes</a>
</div>

<form method="POST" action="<?= APP_URL ?>/financien/offertes/nieuw" id="quoteForm">
    <?= CSRF::field() ?>

    <!-- ── Offerte details ──────────────────────────────────────────────── -->
    <div class="card" style="margin-bottom:1rem;">
        <div class="card-header"><span class="card-title">Offertegegevens</span></div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="company_id">Bedrijf <span style="color:#ef4444;">*</span></label>
                    <select name="company_id" id="company_id" class="form-select w-full" required>
                        <option value="">— Selecteer bedrijf —</option>
                        <?php foreach ($companies as $c): ?>
                            <option value="<?= (int) $c['id'] ?>">
                                <?= htmlspecialchars($c['name'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="issue_date">Offertedatum <span style="color:#ef4444;">*</span></label>
                    <input type="date" name="issue_date" id="issue_date" class="form-input"
                           value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="valid_until">Geldig tot <span style="color:#ef4444;">*</span></label>
                    <input type="date" name="valid_until" id="valid_until" class="form-input"
                           value="<?= date('Y-m-d', strtotime('+30 days')) ?>" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="default_vat">BTW-tarief standaard voor nieuwe regels</label>
                    <select id="default_vat" class="form-select">
                        <option value="21">21% (standaard)</option>
                        <option value="9">9% (laag tarief)</option>
                        <option value="0">0% (vrijgesteld)</option>
                    </select>
                </div>
                <div class="form-group" style="grid-column:span 2;">
                    <label class="form-label" for="notes">Notities</label>
                    <textarea name="notes" id="notes" class="form-textarea" rows="3"
                              placeholder="Optionele opmerkingen voor de offerte…"></textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Regelitems ───────────────────────────────────────────────────── -->
    <div class="card" style="margin-bottom:1rem;">
        <div class="card-header">
            <div class="flex justify-between items-center">
                <span class="card-title">Regelitems</span>
                <button type="button" id="addRowBtn" class="btn btn-secondary btn-sm">+ Regel toevoegen</button>
            </div>
        </div>
        <div class="card-body" style="overflow-x:auto;">
            <table class="items-table" id="itemsTable">
                <thead>
                    <tr>
                        <th style="width:45%;">Omschrijving</th>
                        <th style="width:10%;">Aantal</th>
                        <th style="width:15%;">Eenheidsprijs</th>
                        <th style="width:10%;">BTW %</th>
                        <th style="width:13%;text-align:right;">Subtotaal</th>
                        <th style="width:7%;"></th>
                    </tr>
                </thead>
                <tbody id="itemsBody">
                    <tr class="item-row" data-index="0">
                        <td><input type="text" name="items[0][description]" placeholder="Omschrijving dienst of product" required></td>
                        <td><input type="number" name="items[0][quantity]" value="1" min="0.01" step="0.01" class="qty-input"></td>
                        <td><input type="number" name="items[0][unit_price]" value="0.00" min="0" step="0.01" class="price-input"></td>
                        <td>
                            <select name="items[0][vat_rate]" class="vat-input">
                                <option value="21">21%</option>
                                <option value="9">9%</option>
                                <option value="0">0%</option>
                            </select>
                        </td>
                        <td style="text-align:right;"><span class="line-total">€ 0,00</span></td>
                        <td style="text-align:center;">
                            <button type="button" class="btn btn-danger btn-sm delete-row" title="Regel verwijderen">×</button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Totalen -->
            <div class="totals-box" id="totalsBox">
                <div class="totals-row">
                    <span>Subtotaal excl. BTW</span>
                    <span id="totalSubtotal">€ 0,00</span>
                </div>
                <div class="totals-row" id="vat21Row" style="display:none;">
                    <span>BTW 21%</span>
                    <span id="totalVat21">€ 0,00</span>
                </div>
                <div class="totals-row" id="vat9Row" style="display:none;">
                    <span>BTW 9%</span>
                    <span id="totalVat9">€ 0,00</span>
                </div>
                <div class="totals-row" id="vat0Row" style="display:none;">
                    <span>BTW 0%</span>
                    <span id="totalVat0">€ 0,00</span>
                </div>
                <div class="totals-row total">
                    <span>Totaal incl. BTW</span>
                    <span id="totalIncl">€ 0,00</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Opslaan ──────────────────────────────────────────────────────── -->
    <div class="flex gap-1" style="justify-content:flex-end;">
        <a href="<?= APP_URL ?>/financien/offertes" class="btn btn-secondary">Annuleren</a>
        <button type="submit" class="btn btn-primary">Offerte opslaan</button>
    </div>
</form>

<script>
(function () {
    'use strict';

    const tbody      = document.getElementById('itemsBody');
    const addRowBtn  = document.getElementById('addRowBtn');
    const defaultVat = document.getElementById('default_vat');

    function fmtEuro(val) {
        return '€ ' + val.toLocaleString('nl-NL', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function recalcRow(row) {
        const qty   = parseFloat(row.querySelector('.qty-input').value)   || 0;
        const price = parseFloat(row.querySelector('.price-input').value) || 0;
        const total = qty * price;
        row.querySelector('.line-total').textContent = fmtEuro(total);
        return { total, vat: parseFloat(row.querySelector('.vat-input').value) || 0 };
    }

    function recalcAll() {
        let subtotal = 0;
        const vat    = { 21: 0, 9: 0, 0: 0 };

        Array.from(tbody.querySelectorAll('.item-row')).forEach(function (row) {
            const r = recalcRow(row);
            subtotal += r.total;
            const vatKey = r.vat >= 21 ? 21 : (r.vat >= 9 ? 9 : 0);
            vat[vatKey] += r.total * (r.vat / 100);
        });

        const totalVat  = vat[21] + vat[9] + vat[0];
        const totalIncl = subtotal + totalVat;

        document.getElementById('totalSubtotal').textContent = fmtEuro(subtotal);
        document.getElementById('totalVat21').textContent    = fmtEuro(vat[21]);
        document.getElementById('totalVat9').textContent     = fmtEuro(vat[9]);
        document.getElementById('totalVat0').textContent     = fmtEuro(vat[0]);
        document.getElementById('totalIncl').textContent     = fmtEuro(totalIncl);

        document.getElementById('vat21Row').style.display = vat[21] > 0 ? '' : 'none';
        document.getElementById('vat9Row').style.display  = vat[9]  > 0 ? '' : 'none';
        document.getElementById('vat0Row').style.display  = vat[0]  > 0 ? '' : 'none';
    }

    function reindexRows() {
        Array.from(tbody.querySelectorAll('.item-row')).forEach(function (row, idx) {
            row.dataset.index = idx;
            row.querySelectorAll('[name]').forEach(function (el) {
                el.name = el.name.replace(/items\[\d+\]/, 'items[' + idx + ']');
            });
            const hidden = row.querySelector('input[name*="sort_order"]');
            if (hidden) hidden.value = idx;
        });
    }

    function addDeleteListener(btn) {
        btn.addEventListener('click', function () {
            if (tbody.querySelectorAll('.item-row').length <= 1) {
                alert('Er moet minimaal één regelitem aanwezig zijn.');
                return;
            }
            btn.closest('.item-row').remove();
            reindexRows();
            recalcAll();
        });
    }

    function addInputListeners(row) {
        row.querySelectorAll('input, select').forEach(function (el) {
            el.addEventListener('input', recalcAll);
            el.addEventListener('change', recalcAll);
        });
    }

    // Add row button
    addRowBtn.addEventListener('click', function () {
        const rows  = tbody.querySelectorAll('.item-row');
        const idx   = rows.length;
        const vatVal = defaultVat.value;

        const tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.dataset.index = idx;
        tr.innerHTML = `
            <td><input type="text" name="items[${idx}][description]" placeholder="Omschrijving dienst of product"></td>
            <td><input type="number" name="items[${idx}][quantity]" value="1" min="0.01" step="0.01" class="qty-input"></td>
            <td><input type="number" name="items[${idx}][unit_price]" value="0.00" min="0" step="0.01" class="price-input"></td>
            <td>
                <select name="items[${idx}][vat_rate]" class="vat-input">
                    <option value="21" ${vatVal === '21' ? 'selected' : ''}>21%</option>
                    <option value="9"  ${vatVal === '9'  ? 'selected' : ''}>9%</option>
                    <option value="0"  ${vatVal === '0'  ? 'selected' : ''}>0%</option>
                </select>
            </td>
            <td style="text-align:right;"><span class="line-total">€ 0,00</span></td>
            <td style="text-align:center;">
                <button type="button" class="btn btn-danger btn-sm delete-row" title="Regel verwijderen">×</button>
            </td>`;
        tbody.appendChild(tr);
        addDeleteListener(tr.querySelector('.delete-row'));
        addInputListeners(tr);
        tr.querySelector('input[type="text"]').focus();
    });

    // Init existing rows
    Array.from(tbody.querySelectorAll('.item-row')).forEach(function (row) {
        addDeleteListener(row.querySelector('.delete-row'));
        addInputListeners(row);
    });

    recalcAll();
})();
</script>
