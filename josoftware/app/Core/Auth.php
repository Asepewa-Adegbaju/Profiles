<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\User;

class Auth
{
    public static function attempt(string $email, string $password): bool
    {
        $user = User::findByEmail($email);

        if ($user === null || !password_verify($password, $user['password'])) {
            return false;
        }

        // Nieuwe sessie-ID bij inloggen (session fixation-preventie)
        session_regenerate_id(true);

        Session::set('user_id',    (int) $user['id']);
        Session::set('user_name',  $user['name']);
        Session::set('user_email', $user['email']);
        Session::set('logged_in',  true);
        Session::set('login_time', time());

        AuditLog::log('login', 'users', (int) $user['id'], 'Gebruiker ingelogd');

        return true;
    }

    public static function check(): bool
    {
        if (!Session::get('logged_in', false)) {
            return false;
        }

        // Sessie-verloop controleren
        if ((time() - (int) Session::get('login_time', 0)) > SESSION_LIFETIME) {
            self::logout();
            return false;
        }

        return true;
    }

    public static function require(): void
    {
        if (!self::check()) {
            header('Location: ' . APP_URL . '/login');
            exit;
        }
    }

    public static function id(): ?int
    {
        return Session::get('user_id');
    }

    public static function user(): array
    {
        return [
            'id'    => Session::get('user_id'),
            'name'  => Session::get('user_name'),
            'email' => Session::get('user_email'),
        ];
    }

    public static function logout(): void
    {
        AuditLog::log('logout', 'users', Session::get('user_id'), 'Gebruiker uitgelogd');
        Session::destroy();
    }
}
