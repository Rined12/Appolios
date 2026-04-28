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

        <a href="<?= APP_ENTRY ?>?url=admin/users" class="admin-side-link <?= $adminSidebarActive === 'users' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="8" r="4"></circle>
                    <path d="M4 20c2-4 5-6 8-6s6 2 8 6"></path>
                </svg>
            </span>
            <span>Utilisateurs</span>
        </a>

        <a href="<?= APP_ENTRY ?>?url=admin/courses" class="admin-side-link <?= $adminSidebarActive === 'courses' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="16" rx="2"></rect>
                    <path d="M3 10h18"></path>
                </svg>
            </span>
            <span>Cours</span>
        </a>

        <a href="<?= APP_ENTRY ?>?url=admin/teachers" class="admin-side-link <?= $adminSidebarActive === 'teachers' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 19c2-4 5-6 8-6s6 2 8 6"></path>
                    <circle cx="12" cy="8" r="4"></circle>
                </svg>
            </span>
            <span>Enseignants</span>
        </a>

        <a href="<?= APP_ENTRY ?>?url=admin/chapitres" class="admin-side-link <?= $adminSidebarActive === 'chapitres' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 4h16v16H4z"></path>
                    <path d="M8 8h8"></path>
                    <path d="M8 12h8"></path>
                    <path d="M8 16h5"></path>
                </svg>
            </span>
            <span>Chapitres</span>
        </a>

        <a href="<?= APP_ENTRY ?>?url=admin/quizzes" class="admin-side-link <?= $adminSidebarActive === 'quiz' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="9"></circle>
                    <path d="M9 12l2 2 4-4"></path>
                </svg>
            </span>
            <span>Quiz</span>
        </a>

        <a href="<?= APP_ENTRY ?>?url=admin/quiz-history" class="admin-side-link <?= $adminSidebarActive === 'quiz_history' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 3v18h18"></path>
                    <path d="M7 15l4-4 3 3 5-6"></path>
                </svg>
            </span>
            <span>Historique des quiz</span>
        </a>

        <a href="<?= APP_ENTRY ?>?url=admin/quiz-stats" class="admin-side-link <?= $adminSidebarActive === 'quiz_stats' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 19V5"></path>
                    <path d="M10 19V9"></path>
                    <path d="M16 19V12"></path>
                    <path d="M22 19V7"></path>
                </svg>
            </span>
            <span>Statistiques quiz</span>
        </a>

        <a href="<?= APP_ENTRY ?>?url=admin/questions" class="admin-side-link <?= $adminSidebarActive === 'questions' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 4h16v16H4z"></path>
                    <path d="M8 9h8"></path>
                    <path d="M8 13h6"></path>
                </svg>
            </span>
            <span>Questions</span>
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
    </nav>
</aside>
