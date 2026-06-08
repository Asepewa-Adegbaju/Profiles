<?php
use App\Core\CSRF;

// Voorbeeld CSV inline downloaden
if (isset($_GET['example'])) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="voorbeeld_import.csv"');
    echo "naam,sector,telefoon,email,website,stad,postcode,status\n";
    echo "Voorbeeld BV,IT,0201234567,info@voorbeeld.nl,https://voorbeeld.nl,Amsterdam,1012 AB,lead\n";
    echo "Testbedrijf VOF,Retail,0101234567,info@testbedrijf.nl,,Rotterdam,3011 CA,prospect\n";
    exit;
}
?>

<div class="flex justify-between items-center mb-2">
    <h2 style="font-size:1.1rem;font-weight:700;">Bedrijven importeren via CSV</h2>
    <a href="<?= APP_URL ?>/crm" class="btn btn-secondary btn-sm">← Terug</a>
</div>

<div class="card mb-2">
    <div class="card-header">
        <span class="card-title">Instructies</span>
    </div>
    <div class="card-body">
        <p style="margin-bottom:.75rem;">Upload een CSV-bestand met de volgende kolommen (eerste rij wordt als headerrij overgeslagen):</p>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Kolom</th>
                        <th>Veld</th>
                        <th>Verplicht</th>
                        <th>Voorbeeld</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td><code>naam</code></td>
                        <td><span class="badge badge-red">Ja</span></td>
                        <td>Voorbeeld BV</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td><code>sector</code></td>
                        <td><span class="badge badge-gray">Nee</span></td>
                        <td>IT</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td><code>telefoon</code></td>
                        <td><span class="badge badge-gray">Nee</span></td>
                        <td>0201234567</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td><code>email</code></td>
                        <td><span class="badge badge-gray">Nee</span></td>
                        <td>info@voorbeeld.nl</td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td><code>website</code></td>
                        <td><span class="badge badge-gray">Nee</span></td>
                        <td>https://voorbeeld.nl</td>
                    </tr>
                    <tr>
                        <td>6</td>
                        <td><code>stad</code></td>
                        <td><span class="badge badge-gray">Nee</span></td>
                        <td>Amsterdam</td>
                    </tr>
                    <tr>
                        <td>7</td>
                        <td><code>postcode</code></td>
                        <td><span class="badge badge-gray">Nee</span></td>
                        <td>1012 AB</td>
                    </tr>
                    <tr>
                        <td>8</td>
                        <td><code>status</code></td>
                        <td><span class="badge badge-gray">Nee</span></td>
                        <td>lead / prospect / klant / inactief (standaard: lead)</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="mt-2">
            <a href="<?= APP_URL ?>/crm/import?example=1" class="btn btn-secondary btn-sm">Voorbeeld CSV downloaden</a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">CSV-bestand uploaden</span>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= APP_URL ?>/crm/import" enctype="multipart/form-data">
            <?= CSRF::field() ?>
            <div class="form-group">
                <label class="form-label" for="csv_file">CSV-bestand <span style="color:var(--danger);">*</span></label>
                <input
                    type="file"
                    id="csv_file"
                    name="csv_file"
                    class="form-input"
                    accept=".csv,text/csv"
                    required
                >
                <p class="text-muted text-sm mt-1">Ondersteunde bestandsformaten: .csv — maximaal 2 MB</p>
            </div>
            <div class="flex gap-1 mt-2">
                <button type="submit" class="btn btn-primary">Importeren starten</button>
                <a href="<?= APP_URL ?>/crm" class="btn btn-secondary">Annuleren</a>
            </div>
        </form>
    </div>
</div>
