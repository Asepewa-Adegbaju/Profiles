<?php

declare(strict_types=1);

// ─── Bootstrap ───────────────────────────────────────────────────────────────
define('APP_ROOT', __DIR__);

require_once APP_ROOT . '/app/config/config.php';

// Autoloader — geen Composer nodig
spl_autoload_register(function (string $class): void {
    // App\Core\Database → app/Core/Database.php
    $relative = str_replace(['App\\', '\\'], ['app/', '/'], $class);
    $file      = APP_ROOT . '/' . $relative . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

// ─── Beveiliging ─────────────────────────────────────────────────────────────
// Verberg PHP-versie
header_remove('X-Powered-By');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');

if (APP_ENV === 'production') {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
}

// ─── Sessie starten ───────────────────────────────────────────────────────────
\App\Core\Session::start();

// ─── Routes definiëren ───────────────────────────────────────────────────────
$router = new \App\Core\Router();

// Authenticatie
$router->get('/login',  'AuthController', 'showLogin');
$router->post('/login', 'AuthController', 'login');
$router->post('/logout','AuthController', 'logout');

// Dashboard
$router->get('/',          'DashboardController', 'index');
$router->get('/dashboard', 'DashboardController', 'index');

// Modules
require APP_ROOT . '/routes_crm.php';
require APP_ROOT . '/routes_time.php';
require APP_ROOT . '/routes_projects.php';
require APP_ROOT . '/routes_finance.php';

// ─── Dispatchen ──────────────────────────────────────────────────────────────
$router->dispatch(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI']
);
