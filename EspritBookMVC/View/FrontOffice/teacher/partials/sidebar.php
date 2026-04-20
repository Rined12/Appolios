<?php
/**
 * APPOLIOS - Teacher Sidebar
 */

$teacherSidebarActive = $teacherSidebarActive ?? '';
?>

<aside class="admin-sidebar teacher-sidebar">
    <nav class="admin-sidebar-nav teacher-sidebar-nav" aria-label="Teacher Navigation">
        <a href="<?= APP_ENTRY ?>?url=teacher/courses" class="admin-side-link <?= $teacherSidebarActive === 'courses' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="16" rx="2"></rect>
                    <path d="M3 10h18"></path>
                </svg>
            </span>
            <span>Mes Cours</span>
        </a>

        <a href="<?= APP_ENTRY ?>?url=teacher/evenements" class="admin-side-link <?= $teacherSidebarActive === 'evenements' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M8 2v4"></path>
                    <path d="M16 2v4"></path>
                    <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                    <path d="M3 10h18"></path>
                </svg>
            </span>
            <span>Mes Evenements</span>
        </a>

        <a href="<?= APP_ENTRY ?>?url=teacher/groupes" class="admin-side-link <?= $teacherSidebarActive === 'groupes' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="9" cy="8" r="3"></circle>
                    <circle cx="17" cy="10" r="3"></circle>
                    <path d="M3 20c1.5-3 4-4.5 6-4.5S13.5 17 15 20"></path>
                    <path d="M13 19c1.2-2.2 3-3.2 4.6-3.2 1.5 0 2.8.9 3.9 2.9"></path>
                </svg>
            </span>
            <span>Groups</span>
        </a>

        <a href="<?= APP_ENTRY ?>?url=teacher/discussions" class="admin-side-link <?= $teacherSidebarActive === 'discussions' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                </svg>
            </span>
            <span>Discussions</span>
        </a>
    </nav>
</aside>
