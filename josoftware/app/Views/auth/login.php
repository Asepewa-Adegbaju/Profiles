<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Inloggen — JO Software Solutions</title>
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css">
</head>
<body class="login-page">

<div class="login-container">
    <div class="login-card">

        <div class="login-header">
            <div class="login-badge" aria-hidden="true">JO</div>
            <h1 class="login-title">JO Software Solutions</h1>
            <p class="login-subtitle">Beheerpaneel — log in om verder te gaan</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error" role="alert">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form action="<?= APP_URL ?>/login" method="POST" novalidate autocomplete="on">
            <?= \App\Core\CSRF::field() ?>

            <div class="form-group">
                <label for="email" class="form-label">E-mailadres</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-input"
                    required
                    autocomplete="email"
                    placeholder="naam@josoftware.nl"
                    spellcheck="false"
                >
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Wachtwoord</label>
                <div class="input-wrapper">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-input"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••••••"
                    >
                    <button type="button" class="password-toggle" aria-label="Wachtwoord tonen/verbergen" data-target="password">
                        <svg class="eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg class="eye-closed" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-full">
                Inloggen
            </button>
        </form>

    </div>
</div>

<script>
document.querySelectorAll('.password-toggle').forEach(btn => {
    btn.addEventListener('click', () => {
        const input = document.getElementById(btn.dataset.target);
        const isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';
        btn.querySelector('.eye-open').style.display  = isPassword ? 'none'  : '';
        btn.querySelector('.eye-closed').style.display = isPassword ? '' : 'none';
    });
});
</script>
</body>
</html>
