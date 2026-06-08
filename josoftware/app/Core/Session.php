<?php

declare(strict_types=1);

namespace App\Core;

class Session
{
    public static function start(): void
    {
        if (session_status() !== PHP_SESSION_NONE) {
            return;
        }

        session_name(SESSION_NAME);

        session_set_cookie_params([
            'lifetime' => SESSION_LIFETIME,
            'path'     => '/',
            'secure'   => APP_ENV === 'production',
            'httponly' => true,
            'samesite' => 'Strict',
        ]);

        session_start();

        // Fixation-preventie: regenereer ID bij eerste gebruik
        if (empty($_SESSION['_started'])) {
            session_regenerate_id(true);
            $_SESSION['_started'] = true;
        }
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function delete(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function destroy(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    // Flash berichten (eenmalig, verdwijnen na ophalen)
    public static function flash(string $key, mixed $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }

    public static function getFlash(string $key, mixed $default = null): mixed
    {
        $value = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }
}
