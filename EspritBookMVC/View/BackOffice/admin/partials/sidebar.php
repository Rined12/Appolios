<?php
/**
 * APPOLIOS - Admin Sidebar
 */

$adminSidebarActive = $adminSidebarActive ?? '';
?>

<aside class="admin-sidebar dark-theme">
    <nav class="admin-sidebar-nav" aria-label="Admin Navigation">
        <a href="<?= APP_ENTRY ?>?url=admin/dashboard" class="admin-side-link <?= $adminSidebarActive === 'dashboard' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7" rx="1"></rect>
                    <rect x="14" y="3" width="7" height="7" rx="1"></rect>
                    <rect x="3" y="14" width="7" height="7" rx="1"></rect>
                    <rect x="14" y="14" width="7" height="7" rx="1"></rect>
                </svg>
            </span>
            <span>Espace</span>
        </a>

        <a href="<?= APP_ENTRY ?>?url=admin/evenements" class="admin-side-link <?= $adminSidebarActive === 'evenements' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M8 2v4"></path>
                    <path d="M16 2v4"></path>
                    <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                    <path d="M3 10h18"></path>
                </svg>
            </span>
            <span>Evenement</span>
        </a>

        <a href="<?= APP_ENTRY ?>?url=admin/sl-groupes" class="admin-side-link <?= $adminSidebarActive === 'sl-groupes' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="9" cy="8" r="3"></circle>
                    <circle cx="17" cy="10" r="3"></circle>
                    <path d="M3 20c1.5-3 4-4.5 6-4.5S13.5 17 15 20"></path>
                    <path d="M13 19c1.2-2.2 3-3.2 4.6-3.2 1.5 0 2.8.9 3.9 2.9"></path>
                </svg>
            </span>
            <span>SL Groupes</span>
        </a>

        <a href="<?= APP_ENTRY ?>?url=admin/sl-discussions" class="admin-side-link <?= $adminSidebarActive === 'sl-discussions' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                </svg>
            </span>
            <span>SL Discussions</span>
        </a>
    </nav>
</aside>
