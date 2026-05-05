<?php
/**
 * APPOLIOS - Student Sidebar
 */

$studentSidebarActive = $studentSidebarActive ?? '';
$unreadDiscussionsTotal = max(0, (int) ($unread_discussions_total ?? 0));
?>

<div class="aside admin-sidebar student-space-sidebar">
    <div class="student-sidebar-panel">
        <a class="student-sidebar-brand" href="<?= APP_ENTRY ?>?url=student/dashboard" style="display: flex; align-items: center;">
            <span class="student-sidebar-brand-mark" aria-hidden="true">a</span>
            <span class="student-sidebar-brand-text">APPOLIOS</span>
        </a>

        <div class="nav admin-sidebar-nav student-sidebar-nav" aria-label="Front Office Navigation">
            <a href="<?= APP_ENTRY ?>?url=student/dashboard" class="admin-side-link <?= $studentSidebarActive === 'dashboard' ? 'active' : '' ?>">
                <span class="admin-side-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 10.5L12 3l9 7.5"></path>
                        <path d="M5 9.5V21h14V9.5"></path>
                    </svg>
                </span>
                <span>Dashboard</span>
            </a>

            <p class="student-sidebar-section">Pages</p>

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

            <a href="<?= APP_ENTRY ?>?url=student/groupes" class="admin-side-link <?= $studentSidebarActive === 'groupes' ? 'active' : '' ?>">
                <span class="admin-side-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="9" cy="8" r="3"></circle>
                        <circle cx="17" cy="8" r="3"></circle>
                        <path d="M3 20c1.6-3 4-5 6-5"></path>
                        <path d="M11 20c1.4-3 3.8-5 6-5"></path>
                    </svg>
                </span>
                <span>Groups</span>
            </a>

            <a href="<?= APP_ENTRY ?>?url=student/discussions" class="admin-side-link <?= $studentSidebarActive === 'discussions' ? 'active' : '' ?>">
                <span class="admin-side-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15a2 2 0 0 1-2 2H8l-5 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                    </svg>
                    <?php if ($unreadDiscussionsTotal > 0): ?>
                        <span class="student-unread-dot"><?= (int) $unreadDiscussionsTotal ?></span>
                    <?php endif; ?>
                </span>
                <span>Discussions</span>
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

            <a href="<?= APP_ENTRY ?>?url=student/my-events" class="admin-side-link <?= $studentSidebarActive === 'my-events' ? 'active' : '' ?>">
                <span class="admin-side-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                    </svg>
                </span>
                <span>My Events</span>
            </a>

            <a href="<?= APP_ENTRY ?>?url=student/profile" class="admin-side-link <?= $studentSidebarActive === 'profile' ? 'active' : '' ?>">
                <span class="admin-side-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="8" r="4"></circle>
                        <path d="M4 20c2-4 5-6 8-6s6 2 8 6"></path>
                    </svg>
                </span>
                <span>Profile</span>
            </a>
        </div>
    </div>
</div>
