<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Session;

class AuthController
{
    public function showLogin(): void
    {
        // Al ingelogd? → doorsturen
        if (Auth::check()) {
            header('Location: ' . APP_URL . '/dashboard');
            exit;
        }

        $error = Session::getFlash('error');
        require APP_ROOT . '/app/Views/auth/login.php';
    }

    public function login(): void
    {
        CSRF::validateOrDie();

        $email    = trim(filter_input(INPUT_POST, 'email',    FILTER_SANITIZE_EMAIL) ?? '');
        $password = $_POST['password'] ?? '';

        // Eenvoudige rate-limiting via sessie
        $attempts    = (int) Session::get('login_attempts', 0);
        $lastAttempt = (int) Session::get('last_attempt_time', 0);

        if ($attempts >= MAX_LOGIN_ATTEMPTS && (time() - $lastAttempt) < LOCKOUT_SECONDS) {
            $remaining = LOCKOUT_SECONDS - (time() - $lastAttempt);
            Session::flash('error', "Te veel inlogpogingen. Probeer opnieuw over {$remaining} seconden.");
            header('Location: ' . APP_URL . '/login');
            exit;
        }

        if (Auth::attempt($email, $password)) {
            Session::delete('login_attempts');
            Session::delete('last_attempt_time');
            header('Location: ' . APP_URL . '/dashboard');
            exit;
        }

        // Mislukte poging registreren
        Session::set('login_attempts', $attempts + 1);
        Session::set('last_attempt_time', time());
        Session::flash('error', 'Ongeldig e-mailadres of wachtwoord.');

        header('Location: ' . APP_URL . '/login');
        exit;
    }

    public function logout(): void
    {
        CSRF::validateOrDie();
        Auth::logout();
        header('Location: ' . APP_URL . '/login');
        exit;
    }
}
