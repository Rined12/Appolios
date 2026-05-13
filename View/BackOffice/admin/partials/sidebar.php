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

        <a href="<?= APP_ENTRY ?>?url=event/evenements" class="admin-side-link <?= $adminSidebarActive === 'evenements' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
            </span>
            <span>Evenements</span>
        </a>

        <a href="<?= APP_ENTRY ?>?url=admin/stat-evenements" class="admin-side-link <?= $adminSidebarActive === 'stat-evenements' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="20" x2="18" y2="10"></line>
                    <line x1="12" y1="20" x2="12" y2="4"></line>
                    <line x1="6" y1="20" x2="6" y2="14"></line>
                </svg>
            </span>
            <span>Stat Evenements</span>
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

        <a href="<?= APP_ENTRY ?>?url=event/add-evenement" class="admin-side-link <?= $adminSidebarActive === 'add-evenement' ? 'active' : '' ?>">
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

        <a href="<?= APP_ENTRY ?>?url=admin/course-requests" class="admin-side-link <?= $adminSidebarActive === 'course-requests' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    <line x1="8" y1="12" x2="16" y2="12"></line>
                </svg>
            </span>
            <span>Course Requests</span>
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
            // Show unread messages count badge - uses variable from controller
            if (isset($unreadCount) && $unreadCount > 0): ?>
                <span style="margin-left: auto; background: #dc3545; color: white; padding: 2px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: 600;"><?= $unreadCount ?></span>
            <?php endif; ?>
        </a>

        <a href="<?= APP_ENTRY ?>?url=admin/activity-log" class="admin-side-link <?= $adminSidebarActive === 'activity-log' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
            </span>
            <span>Activity Log</span>
        </a>

        <a href="<?= APP_ENTRY ?>?url=admin/activity-map" class="admin-side-link <?= $adminSidebarActive === 'activity-map' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"></polygon>
                    <line x1="8" y1="2" x2="8" y2="18"></line>
                    <line x1="16" y1="6" x2="16" y2="22"></line>
                </svg>
            </span>
            <span>Carte d'Activité</span>
        </a>

        <a href="<?= APP_ENTRY ?>?url=admin/statistics" class="admin-side-link <?= $adminSidebarActive === 'statistics' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path>
                    <path d="M22 12A10 10 0 0 0 12 2v10z"></path>
                </svg>
            </span>
            <span>Statistiques</span>
        </a>

        <a href="<?= APP_ENTRY ?>?url=admin/quizzes" class="admin-side-link <?= $adminSidebarActive === 'quizzes' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="9 11 12 14 22 4"></polyline>
                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                </svg>
            </span>
            <span>Quiz & Examens</span>
        </a>

        <a href="<?= APP_ENTRY ?>?url=admin/questions" class="admin-side-link <?= $adminSidebarActive === 'questions_bank' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                    <circle cx="12" cy="13" r="2"></circle>
                    <line x1="12" y1="17" x2="12" y2="17.5"></line>
                </svg>
            </span>
            <span>Banque de Questions</span>
        </a>


            <div class="sidebar-divider" style="height: 1px; background: rgba(255,255,255,0.1); margin: 15px 20px;"></div>
        <p style="padding-left: 20px; font-size: 0.7rem; color: rgba(255,255,255,0.5); text-transform: uppercase; margin-bottom: 5px;">Aperçu des Espaces</p>

        <!-- Conteneur pour l'Espace Étudiant -->
        <a href="<?= APP_ENTRY ?>?url=student/dashboard" class="admin-side-link">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4z"></path>
                    <path d="M12 14c-4.42 0-8 2.24-8 5v2h16v-2c0-2.76-3.58-5-8-5z"></path>
                    <polyline points="12 4 4 8 12 12 20 8 12 4"></polyline>
                </svg>
            </span>
            <span>Espace Étudiant</span>
        </a>

        <!-- Conteneur pour l'Espace Teacher -->
        <a href="<?= APP_ENTRY ?>?url=teacher/dashboard" class="admin-side-link">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                </svg>
            </span>
            <span>Espace Teacher</span>
        </a>

    </nav>
</aside>