<?php

declare(strict_types=1);

// ─── Database ────────────────────────────────────────────────────────────────
define('DB_HOST',    'localhost');
define('DB_NAME',    'josoftware_db');
define('DB_USER',    'root');
define('DB_PASS',    '');
define('DB_CHARSET', 'utf8mb4');

// ─── Applicatie ──────────────────────────────────────────────────────────────
define('APP_NAME',    'JO Software Solutions');
define('APP_URL',     'http://localhost/josoftware'); // Productie: https://jouwdomein.nl
define('APP_ENV',     'development');                // Productie: 'production'
defined('APP_ROOT') || define('APP_ROOT', dirname(__DIR__, 2));

// ─── Sessie ───────────────────────────────────────────────────────────────────
define('SESSION_NAME',     'jos_sess');
define('SESSION_LIFETIME', 7200); // 2 uur

// ─── Beveiliging ─────────────────────────────────────────────────────────────
define('CSRF_BYTES', 32);
define('BCRYPT_COST', 12);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_SECONDS',    300); // 5 minuten
