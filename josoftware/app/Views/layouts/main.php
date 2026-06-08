<?php
use App\Core\Auth;
use App\Core\CSRF;

$currentUser = Auth::user();
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
$path = '/' . ltrim(substr($currentPath, strlen($base)), '/');

function navActive(string $route, string $current): string {
    return str_starts_with($current, $route) ? 'nav-link active' : 'nav-link';
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?= htmlspecialchars($title ?? 'JO Software Solutions', ENT_QUOTES, 'UTF-8') ?> — JO Software Solutions</title>
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css">
</head>
<body>

<div class="app-shell">

    <!-- ── Sidebar ────────────────────────────────────────────── -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <span class="logo-badge">JO</span>
                <span class="logo-name">Software Solutions</span>
            </div>
        </div>

        <nav class="sidebar-nav" aria-label="Hoofdnavigatie">
            <div class="nav-group">
                <span class="nav-group-label">Overzicht</span>
                <a href="<?= APP_URL ?>/dashboard" class="<?= navActive('/dashboard', $path) ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    Dashboard
                </a>
            </div>

            <div class="nav-group">
                <span class="nav-group-label">Modules</span>
                <a href="<?= APP_URL ?>/crm" class="<?= navActive('/crm', $path) ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    CRM
                </a>
                <a href="<?= APP_URL ?>/uren" class="<?= navActive('/uren', $path) ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Uren & Kilometers
                </a>
                <a href="<?= APP_URL ?>/projecten" class="<?= navActive('/projecten', $path) ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                    Projecten
                </a>
                <a href="<?= APP_URL ?>/financien" class="<?= navActive('/financien', $path) ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    Financiën
                </a>
            </div>
        </nav>

        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar" aria-hidden="true">
                    <?= strtoupper(substr($currentUser['name'], 0, 2)) ?>
                </div>
                <div class="user-meta">
                    <span class="user-name"><?= htmlspecialchars($currentUser['name'], ENT_QUOTES, 'UTF-8') ?></span>
                    <span class="user-role">Beheerder</span>
                </div>
            </div>
            <form action="<?= APP_URL ?>/logout" method="POST" class="logout-form">
                <?= CSRF::field() ?>
                <button type="submit" class="btn-logout" title="Uitloggen" aria-label="Uitloggen">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                </button>
            </form>
        </div>
    </aside>

    <!-- ── Hoofdinhoud ────────────────────────────────────────── -->
    <div class="main-wrapper">
        <header class="topbar">
            <button class="sidebar-toggle" id="sidebarToggle" aria-label="Menu openen/sluiten">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
            <h1 class="page-title"><?= htmlspecialchars($title ?? '', ENT_QUOTES, 'UTF-8') ?></h1>
        </header>

        <main class="content" id="main-content">
            <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
                <div class="alert alert-success" role="alert">
                    <?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>
            <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
                <div class="alert alert-error" role="alert">
                    <?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <?php
            // Laad de view-inhoud via $view variabele of direct
            if (isset($view)) {
                require APP_ROOT . '/app/Views/' . $view . '.php';
            }
            ?>
        </main>
    </div>
</div>

<script src="<?= APP_URL ?>/assets/js/app.js"></script>
</body>
</html>
