<?php
/**
 * EENMALIG GEBRUIK — VERWIJDER DIT BESTAND NA GEBRUIK
 * Aanroepen via browser: http://localhost/josoftware/setup.php
 */

define('APP_ROOT', __DIR__);
require_once APP_ROOT . '/app/config/config.php';

if (APP_ENV === 'production') {
    die('Setup is uitgeschakeld in productie-modus.');
}

spl_autoload_register(function (string $class): void {
    $file = APP_ROOT . '/' . str_replace(['App\\', '\\'], ['app/', '/'], $class) . '.php';
    if (file_exists($file)) require_once $file;
});

// ── Stap 1: Database + tabellen aanmaken ─────────────────────────────────────
try {
    // Verbind ZONDER dbname zodat we CREATE DATABASE kunnen uitvoeren
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $schema = file_get_contents(APP_ROOT . '/database/schema.sql');

    // Splits op ; maar sla lege regels en pure commentaarregels over
    $statements = array_filter(
        array_map('trim', explode(';', $schema)),
        fn($s) => $s !== '' && !str_starts_with($s, '--')
    );

    foreach ($statements as $stmt) {
        $pdo->exec($stmt);
    }

    echo "✓ Database <strong>josoftware_db</strong> en alle tabellen aangemaakt.<br>\n";

} catch (PDOException $e) {
    die('❌ Database fout: ' . htmlspecialchars($e->getMessage()));
}

// ── Stap 2: Gebruikers aanmaken ───────────────────────────────────────────────
$users = [
    [
        'name'     => 'Asepewa',
        'email'    => 'asepewa123@gmail.com',
        'password' => 'Asepewa@Jos#2025!',   // ← sterk wachtwoord, onthoud dit
    ],
    [
        'name'     => 'Michael',
        'email'    => 'Michaelnwosu2005@gmail.com',
        'password' => 'Nwosu@Jos#2025!',     // ← sterk wachtwoord, onthoud dit
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

// ── Klaar ─────────────────────────────────────────────────────────────────────
echo "<br>";
echo "<strong style='color:red;font-size:1.1em;'>⚠ VERWIJDER NU DIT BESTAND: setup.php</strong><br>\n";
echo "<small>Zolang dit bestand bestaat kan iedereen met toegang tot de server de database opnieuw aanmaken.</small><br><br>\n";
echo "<a href='" . APP_URL . "/login' style='background:#2563eb;color:#fff;padding:.6rem 1.2rem;border-radius:6px;text-decoration:none;'>→ Naar het loginscherm</a>\n";
