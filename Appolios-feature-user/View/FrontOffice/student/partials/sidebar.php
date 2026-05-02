<?php
/**
 * APPOLIOS - Student Sidebar
 */

$studentSidebarActive = $studentSidebarActive ?? '';

require_once 'C:/xampp/htdocs/Appolios-feature-user/Appolios-feature-user/Model/Notification.php';
$notificationModel = new Notification();
$unreadCount = 0;
if (isset($_SESSION['user_id'])) {
    $unreadCount = $notificationModel->getUnreadCount($_SESSION['user_id']);
}
?>

<aside class="admin-sidebar student-space-sidebar dark-theme">
    <div class="student-sidebar-panel">
        <a class="student-sidebar-brand" href="<?= APP_ENTRY ?>?url=student/dashboard" style="display: flex; align-items: center; font-size: 1.25rem; font-weight: 700; color: #f5f7fb;">
            Appolios
        </a>

        <nav class="admin-sidebar-nav student-sidebar-nav" aria-label="Front Office Navigation">
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
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                </span>
                <span>Events</span>
            </a>

            <a href="<?= APP_ENTRY ?>?url=student/badges" class="admin-side-link <?= $studentSidebarActive === 'badges' ? 'active' : '' ?>">
                <span class="admin-side-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="8" r="6"></circle>
                        <path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"></path>
                    </svg>
                </span>
                <span>My Badges</span>
            </a>

            <a href="<?= APP_ENTRY ?>?url=student/certificates" class="admin-side-link <?= $studentSidebarActive === 'certificates' ? 'active' : '' ?>">
                <span class="admin-side-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="14" rx="2"></rect>
                        <path d="M7 8h10"></path>
                        <path d="M7 12h6"></path>
                    </svg>
                </span>
                <span>Certificates</span>
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

            <p class="student-sidebar-section">Notifications</p>

            <a href="<?= APP_ENTRY ?>?url=student/notifications" class="admin-side-link <?= $studentSidebarActive === 'notifications' ? 'active' : '' ?>">
                <span class="admin-side-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                </span>
                <span>Notifications</span>
                <?php if ($unreadCount > 0): ?>
                    <span style="background: #ef4444; color: white; font-size: 0.75rem; padding: 2px 8px; border-radius: 10px; margin-left: auto;"><?= $unreadCount ?></span>
                <?php endif; ?>
            </a>
        </nav>
    </div>
</aside>
