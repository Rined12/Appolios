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

        <a href="<?= APP_ENTRY ?>?url=teacher/chapitres" class="admin-side-link <?= $teacherSidebarActive === 'chapitres' ? 'active' : '' ?>">
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

        <a href="<?= APP_ENTRY ?>?url=teacher/quiz" class="admin-side-link <?= $teacherSidebarActive === 'quiz' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="9"></circle>
                    <path d="M9 12l2 2 4-4"></path>
                </svg>
            </span>
            <span>Quiz</span>
        </a>

        <a href="<?= APP_ENTRY ?>?url=teacher/questions" class="admin-side-link <?= $teacherSidebarActive === 'questions' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 4h16v16H4z"></path>
                    <path d="M8 9h8"></path>
                    <path d="M8 13h6"></path>
                </svg>
            </span>
            <span>Questions</span>
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
    </nav>
</aside>
