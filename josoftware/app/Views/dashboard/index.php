<div class="dashboard-welcome">
    <h2>Welkom terug, <?= htmlspecialchars(explode(' ', $user['name'])[0], ENT_QUOTES, 'UTF-8') ?>.</h2>
    <p>Kies een module in de navigatie om te beginnen.</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon stat-icon--blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
        </div>
        <div class="stat-content">
            <span class="stat-label">CRM contacten</span>
            <span class="stat-value">—</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon--green">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div class="stat-content">
            <span class="stat-label">Uren deze maand</span>
            <span class="stat-value">—</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon--orange">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
        </div>
        <div class="stat-content">
            <span class="stat-label">Actieve projecten</span>
            <span class="stat-value">—</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon--purple">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        </div>
        <div class="stat-content">
            <span class="stat-label">Openstaande facturen</span>
            <span class="stat-value">—</span>
        </div>
    </div>
</div>
