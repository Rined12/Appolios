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

        <a href="<?= APP_ENTRY ?>?url=admin/teacher-applications" class="admin-side-link <?= $adminSidebarActive === 'teacher-applications' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
            </span>
            <span>Teacher Applications</span>
            <?php
            // Show pending count badge
            if (isset($pendingTeacherApps) && $pendingTeacherApps > 0): ?>
                <span style="margin-left: auto; background: #E19864; color: white; padding: 2px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: 600;"><?= $pendingTeacherApps ?></span>
            <?php endif; ?>
        </a>

        <a href="<?= APP_ENTRY ?>?url=admin/add-evenement" class="admin-side-link <?= $adminSidebarActive === 'add-evenement' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="12" y1="12" x2="12" y2="12"></line>
                    <line x1="8" y1="12" x2="16" y2="12"></line>
                    <line x1="12" y1="8" x2="12" y2="16"></line>
                </svg>
            </span>
            <span>Add Evenement</span>
        </a>

        <a href="<?= APP_ENTRY ?>?url=admin/add-course" class="admin-side-link <?= $adminSidebarActive === 'add-course' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    <line x1="12" y1="8" x2="12" y2="16"></line>
                    <line x1="8" y1="12" x2="16" y2="12"></line>
                </svg>
            </span>
            <span>Add Course</span>
        </a>

        <a href="<?= APP_ENTRY ?>?url=admin/courses" class="admin-side-link <?= $adminSidebarActive === 'courses' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    <line x1="8" y1="12" x2="16" y2="12"></line>
                </svg>
            </span>
            <span>Manage Courses</span>
        </a>

        <a href="<?= APP_ENTRY ?>?url=admin/users" class="admin-side-link <?= $adminSidebarActive === 'users' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </span>
            <span>Manage Users</span>
        </a>

        <a href="<?= APP_ENTRY ?>?url=admin/teachers" class="admin-side-link <?= $adminSidebarActive === 'teachers' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </span>
            <span>Manage Teachers</span>
        </a>

        <a href="<?= APP_ENTRY ?>?url=admin/contact-messages" class="admin-side-link <?= $adminSidebarActive === 'contact-messages' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"></path>
                </svg>
            </span>
            <span>Messages</span>
            <?php
            // Show unread messages count badge
            require_once __DIR__ . '/../../../../Model/ContactMessage.php';
            $contactModel = new ContactMessage();
            $unreadMessages = $contactModel->getUnreadCount();
            if ($unreadMessages > 0): ?>
                <span style="margin-left: auto; background: #dc3545; color: white; padding: 2px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: 600;"><?= $unreadMessages ?></span>
            <?php endif; ?>
        </a>
    </nav>
</aside>
