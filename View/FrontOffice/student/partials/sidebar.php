<?php
/**
 * APPOLIOS - Student Sidebar
 */

$studentSidebarActive = $studentSidebarActive ?? '';
?>

<aside class="admin-sidebar student-space-sidebar">
    <div class="student-sidebar-panel">
        <a class="student-sidebar-brand" href="<?= APP_ENTRY ?>?url=student/dashboard">
            <span class="student-sidebar-brand-mark" aria-hidden="true">a</span>
            <span class="student-sidebar-brand-text">Appolios</span>
        </a>

        <nav class="admin-sidebar-nav student-sidebar-nav" aria-label="Front Office Navigation">
            <a href="<?= APP_ENTRY ?>?url=student/dashboard"
                class="admin-side-link <?= $studentSidebarActive === 'dashboard' ? 'active' : '' ?>">
                <span class="admin-side-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 10.5L12 3l9 7.5"></path>
                        <path d="M5 9.5V21h14V9.5"></path>
                    </svg>
                </span>
                <span>Dashboard</span>
            </a>

            <p class="student-sidebar-section">Pages</p>

            <a href="<?= APP_ENTRY ?>?url=student/courses"
                class="admin-side-link <?= $studentSidebarActive === 'courses' ? 'active' : '' ?>">
                <span class="admin-side-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 6h16v12H4z"></path>
                        <path d="M8 10h8"></path>
                        <path d="M8 14h5"></path>
                    </svg>
                </span>
                <span>Courses</span>
            </a>

            <a href="<?= APP_ENTRY ?>?url=student/evenements"
                class="admin-side-link <?= $studentSidebarActive === 'evenements' ? 'active' : '' ?>">
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

            <a href="<?= APP_ENTRY ?>?url=student/my-courses"
                class="admin-side-link <?= $studentSidebarActive === 'my-courses' ? 'active' : '' ?>">
                <span class="admin-side-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 6h7v12H3z"></path>
                        <path d="M14 6h7v12h-7z"></path>
                    </svg>
                </span>
                <span>My Courses</span>
            </a>

            <a href="<?= APP_ENTRY ?>?url=student/profile"
                class="admin-side-link <?= $studentSidebarActive === 'profile' ? 'active' : '' ?>">
                <span class="admin-side-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="8" r="4"></circle>
                        <path d="M4 20c2-4 5-6 8-6s6 2 8 6"></path>
                    </svg>
                </span>
                <span>Profile</span>
            </a>
        </nav>
    </div>
</aside>