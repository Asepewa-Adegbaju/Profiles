<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;

class DashboardController
{
    public function index(): void
    {
        Auth::require();

        $user  = Auth::user();
        $title = 'Dashboard';
        $view  = 'dashboard/index';

        require APP_ROOT . '/app/Views/layouts/main.php';
    }
}
