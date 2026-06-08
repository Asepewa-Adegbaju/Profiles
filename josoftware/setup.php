<?php
/**
 * EENMALIG GEBRUIK — VERWIJDER DIT BESTAND NA GEBRUIK
 *
 * Aanroepen via browser: http://localhost/josoftware/setup.php
 * Of CLI: php setup.php
 *
 * Maakt de twee beheerdersaccounts aan.
 */

define('APP_ROOT', __DIR__);
require_once APP_ROOT . '/app/config/config.php';

// Veiligheidscheck: blokkeer op productie
if (APP_ENV === 'production') {
    die('Setup is uitgeschakeld in productie-modus.');
}

spl_autoload_register(function (string $class): void {
    $file = APP_ROOT . '/' . str_replace(['App\\', '\\'], ['app/', '/'], $class) . '.php';
    if (file_exists($file)) require_once $file;
});

// Database aanmaken en schema inladen
try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';charset=utf8mb4',
        DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $schema = file_get_contents(APP_ROOT . '/database/schema.sql');
    foreach (array_filter(array_map('trim', explode(';', $schema))) as $stmt) {
        if ($stmt !== '') $pdo->exec($stmt);
    }

    echo "✓ Database en tabellen aangemaakt.<br>\n";

} catch (PDOException $e) {
    die('Database fout: ' . htmlspecialchars($e->getMessage()));
}

// Gebruikers aanmaken
$users = [
    [
        'name'     => 'JO Admin',               // ← Pas aan
        'email'    => 'jij@josoftware.nl',       // ← Pas aan
        'password' => 'Verander-Dit-Wachtwoord!1', // ← PAS AAN VOOR GEBRUIK
    ],
    [
        'name'     => 'Vriend Admin',            // ← Pas aan
        'email'    => 'vriend@josoftware.nl',    // ← Pas aan
        'password' => 'Verander-Dit-Wachtwoord!2', // ← PAS AAN VOOR GEBRUIK
    ],
];

foreach ($users as $u) {
    try {
        \App\Models\User::create($u['name'], $u['email'], $u['password']);
        echo "✓ Gebruiker aangemaakt: <strong>" . htmlspecialchars($u['email']) . "</strong><br>\n";
    } catch (\Exception $e) {
        echo "⚠ " . htmlspecialchars($u['email']) . " bestaat al of kon niet aangemaakt worden.<br>\n";
    }
}

echo "<br><strong style='color:red'>⚠ VERWIJDER DIT BESTAND NU: setup.php</strong><br>\n";
echo "<a href='" . APP_URL . "/login'>→ Naar het loginscherm</a>\n";
