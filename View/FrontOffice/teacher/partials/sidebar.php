<?php
/**
 * APPOLIOS - Teacher Sidebar (Admin Style)
 */

$teacherSidebarActive = $teacherSidebarActive ?? '';
?>

<aside class="admin-sidebar dark-theme teacher-sidebar-admin">
    <nav class="admin-sidebar-nav" aria-label="Teacher Navigation">
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

        <a href="<?= APP_ENTRY ?>?url=teacher/participation-requests" class="admin-side-link <?= $teacherSidebarActive === 'participations' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </span>
            <span>Participations</span>
        </a>

        <a href="<?= APP_ENTRY ?>?url=teacher/stats-evenements" class="admin-side-link <?= $teacherSidebarActive === 'stat_evenements' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="20" x2="18" y2="10"></line>
                    <line x1="12" y1="20" x2="12" y2="4"></line>
                    <line x1="6" y1="20" x2="6" y2="14"></line>
                </svg>
            </span>
            <span>Stats Evenement</span>
        </a>
    </nav>

    <!-- Quick Actions -->
    <div class="admin-sidebar-footer" style="padding: 1.5rem; border-top: 1px solid rgba(255,255,255,0.1); margin-top: 1rem;">
        <h3 style="margin: 0 0 1rem 0; font-size: 0.8rem; color: rgba(255,255,255,0.5); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Quick Actions</h3>
        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
            <a href="<?= APP_ENTRY ?>?url=teacher/add-course" style="padding: 10px 14px; background: linear-gradient(135deg, #548CA8 0%, #355C7D 100%); color: white; text-decoration: none; border-radius: 12px; font-weight: 600; font-size: 0.85rem; display: flex; align-items: center; gap: 8px; transition: all 0.2s; box-shadow: 0 4px 10px rgba(84, 140, 168, 0.3);">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Add Course
            </a>
            <a href="<?= APP_ENTRY ?>?url=teacher/add-evenement" style="padding: 10px 14px; background: linear-gradient(135deg, #E19864 0%, #d9804b 100%); color: white; text-decoration: none; border-radius: 12px; font-weight: 600; font-size: 0.85rem; display: flex; align-items: center; gap: 8px; transition: all 0.2s; box-shadow: 0 4px 10px rgba(225, 152, 100, 0.3);">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                Add Event
            </a>
            <a href="<?= APP_ENTRY ?>?url=teacher/courses" style="padding: 10px 14px; background: rgba(255,255,255,0.1); color: rgba(255,255,255,0.9); text-decoration: none; border-radius: 12px; font-weight: 600; font-size: 0.85rem; display: flex; align-items: center; gap: 8px; border: 1px solid rgba(255,255,255,0.2); transition: all 0.2s;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                My Courses
            </a>
            <a href="<?= APP_ENTRY ?>?url=teacher/evenements" style="padding: 10px 14px; background: rgba(255,255,255,0.1); color: rgba(255,255,255,0.9); text-decoration: none; border-radius: 12px; font-weight: 600; font-size: 0.85rem; display: flex; align-items: center; gap: 8px; border: 1px solid rgba(255,255,255,0.2); transition: all 0.2s;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                My Events
            </a>
        </div>
    </div>
</aside>
