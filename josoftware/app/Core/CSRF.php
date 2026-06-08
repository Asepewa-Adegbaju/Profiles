<?php

declare(strict_types=1);

namespace App\Core;

class CSRF
{
    public static function token(): string
    {
        if (!Session::has('csrf_token')) {
            Session::set('csrf_token', bin2hex(random_bytes(CSRF_BYTES)));
        }
        return Session::get('csrf_token');
    }

    // Geeft een verborgen formulierveld terug — gebruik in elke <form>
    public static function field(): string
    {
        return '<input type="hidden" name="csrf_token" value="'
            . htmlspecialchars(self::token(), ENT_QUOTES, 'UTF-8') . '">';
    }

    public static function verify(string $token): bool
    {
        $stored = Session::get('csrf_token', '');
        return $stored !== '' && hash_equals($stored, $token);
    }

    // Aanroepen aan het begin van elke POST-handler
    public static function validateOrDie(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $submitted = $_POST['csrf_token'] ?? '';

        if (!self::verify($submitted)) {
            http_response_code(403);
            die('Ongeldige beveiligingstoken. Vernieuw de pagina en probeer opnieuw.');
        }

        // Roteer token na succesvolle verificatie
        Session::delete('csrf_token');
    }
}
