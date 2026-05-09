<?php
/**
 * APPOLIOS - Teacher Sidebar (Admin Style)
 */

$teacherSidebarActive = $teacherSidebarActive ?? '';
?>

<aside class="admin-sidebar dark-theme teacher-sidebar-admin">
    <nav class="admin-sidebar-nav" aria-label="Teacher Navigation">
        <a href="<?= APP_ENTRY ?>?url=teacher/dashboard" class="admin-side-link <?= $teacherSidebarActive === 'dashboard' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7" rx="1"></rect>
                    <rect x="14" y="3" width="7" height="7" rx="1"></rect>
                    <rect x="3" y="14" width="7" height="7" rx="1"></rect>
                    <rect x="14" y="14" width="7" height="7" rx="1"></rect>
                </svg>
            </span>
            <span>Dashboard</span>
        </a>

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

        <a href="<?= APP_ENTRY ?>?url=teacher/add-course" class="admin-side-link <?= $teacherSidebarActive === 'add-course' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
            </span>
            <span>Add Course</span>
        </a>

        <a href="<?= APP_ENTRY ?>?url=teacher/add-evenement" class="admin-side-link <?= $teacherSidebarActive === 'add-evenement' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
            </span>
            <span>Add Evenement</span>
        </a>

        <a href="<?= APP_ENTRY ?>?url=teacher-quiz/quiz" class="admin-side-link <?= $teacherSidebarActive === 'teacher-quiz' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    <path d="M8 7h8"></path>
                    <path d="M8 11h6"></path>
                    <path d="M8 15h4"></path>
                </svg>
            </span>
            <span>Quiz</span>
        </a>

        <a href="<?= APP_ENTRY ?>?url=teacher-quiz/questions" class="admin-side-link <?= $teacherSidebarActive === 'teacher-questions' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="M9.5 9a2.5 2.5 0 0 1 5 0c0 2.5-2.5 2-2.5 4"></path>
                    <path d="M12 17h.01"></path>
                </svg>
            </span>
            <span>Question Bank</span>
        </a>
    </nav>
</aside>
