<?php
/**
 * APPOLIOS - Student Sidebar (Admin Style)
 */

$studentSidebarActive = $studentSidebarActive ?? '';
?>

<aside class="admin-sidebar dark-theme student-sidebar-admin">
    <nav class="admin-sidebar-nav" aria-label="Student Navigation">
        <a href="<?= APP_ENTRY ?>?url=student/dashboard" class="admin-side-link <?= $studentSidebarActive === 'dashboard' ? 'active' : '' ?>">
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

        <a href="<?= APP_ENTRY ?>?url=student/courses" class="admin-side-link <?= $studentSidebarActive === 'courses' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 6h16v12H4z"></path>
                    <path d="M8 10h8"></path>
                    <path d="M8 14h5"></path>
                </svg>
            </span>
            <span>Courses</span>
        </a>

        <a href="<?= APP_ENTRY ?>?url=student/evenements" class="admin-side-link <?= $studentSidebarActive === 'evenements' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M8 2v4"></path>
                    <path d="M16 2v4"></path>
                    <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                    <path d="M3 10h18"></path>
                </svg>
            </span>
            <span>Events</span>
        </a>

        <a href="<?= APP_ENTRY ?>?url=student/my-courses" class="admin-side-link <?= $studentSidebarActive === 'my-courses' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 6h7v12H3z"></path>
                    <path d="M14 6h7v12h-7z"></path>
                </svg>
            </span>
            <span>My Courses</span>
        </a>

        <a href="<?= APP_ENTRY ?>?url=student/certificates" class="admin-side-link <?= $studentSidebarActive === 'certificates' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="16" rx="2"></rect>
                    <path d="M12 4v8l-3-2-3 2V4"></path>
                </svg>
            </span>
            <span>Certificates</span>
        </a>

        <a href="<?= APP_ENTRY ?>?url=student-quiz/quiz" class="admin-side-link <?= $studentSidebarActive === 'quiz' ? 'active' : '' ?>">
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

        <a href="<?= APP_ENTRY ?>?url=student/questions-bank" class="admin-side-link <?= $studentSidebarActive === 'questions' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 4h16v16H4z"></path>
                    <path d="M8 9h8"></path>
                    <path d="M8 13h6"></path>
                </svg>
            </span>
            <span>Questions</span>
        </a>
        
        <a href="<?= APP_ENTRY ?>?url=student/badges" class="admin-side-link <?= $studentSidebarActive === 'badges' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="8" r="7"></circle>
                    <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
                </svg>
            </span>
            <span>Badges</span>
        </a>

        <a href="<?= APP_ENTRY ?>?url=student/profile" class="admin-side-link <?= $studentSidebarActive === 'profile' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </span>
            <span>Profile</span>
        </a>
    </nav>
</aside>
