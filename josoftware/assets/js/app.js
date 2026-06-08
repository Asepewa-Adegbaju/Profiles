'use strict';

// ── Sidebar toggle (mobiel) ──────────────────────────────────────────────────
const sidebar = document.getElementById('sidebar');
const toggle  = document.getElementById('sidebarToggle');

if (toggle && sidebar) {
    toggle.addEventListener('click', () => {
        sidebar.classList.toggle('open');
    });

    // Sluit sidebar bij klik buiten
    document.addEventListener('click', e => {
        if (sidebar.classList.contains('open') &&
            !sidebar.contains(e.target) &&
            !toggle.contains(e.target)) {
            sidebar.classList.remove('open');
        }
    });
}
