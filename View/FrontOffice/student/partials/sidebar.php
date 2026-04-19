<?php
/**
 * APPOLIOS - Student Sidebar
 */

$studentSidebarActive = $studentSidebarActive ?? '';
$sidebarBaseLinkStyle = 'display:flex;align-items:center;gap:0.6rem;padding:0.8rem 0.9rem;border-radius:12px;border:1px solid #2d507e !important;background:#16345d !important;color:#f5f9ff !important;font-weight:750;font-size:0.99rem;text-decoration:none;opacity:1 !important;';
$sidebarActiveLinkStyle = 'display:flex;align-items:center;gap:0.6rem;padding:0.8rem 0.9rem;border-radius:12px;border:1px solid #a8d5ff !important;background:linear-gradient(135deg,#2f6fed 0%,#5ca0ff 55%,#72c9ff 100%) !important;color:#ffffff !important;font-weight:800;font-size:0.99rem;text-decoration:none;box-shadow:0 14px 28px rgba(47,111,237,0.38) !important;opacity:1 !important;';
$iconBaseStyle = 'color:#d7e8ff !important;opacity:1;';
$iconActiveStyle = 'color:#ffffff !important;opacity:1;';
$textBaseStyle = 'color:#f5f9ff !important;font-weight:750;opacity:1;';
$textActiveStyle = 'color:#ffffff !important;font-weight:800;opacity:1;';
?>

<style>
    #student-sidebar-premium {
        background: linear-gradient(180deg, #0b1f3a 0%, #132b4f 55%, #1a3967 100%) !important;
        border: 1px solid #2a4d7d !important;
        box-shadow: 0 20px 40px rgba(12, 24, 45, 0.42) !important;
        opacity: 1 !important;
    }
    #student-sidebar-premium .student-sidebar-section {
        color: rgba(225, 238, 255, 0.78) !important;
        font-weight: 700 !important;
    }
    #student-sidebar-premium .admin-side-link {
        background: rgba(96, 137, 194, 0.14) !important;
        border: 1px solid rgba(131, 168, 217, 0.24) !important;
        color: rgba(240, 247, 255, 0.98) !important;
        opacity: 1 !important;
    }
    #student-sidebar-premium .admin-side-link * {
        opacity: 1 !important;
        color: inherit !important;
    }
    #student-sidebar-premium .admin-side-link.active {
        background: linear-gradient(135deg, #2f6fed 0%, #5ca0ff 55%, #72c9ff 100%) !important;
        border-color: #a8d5ff !important;
        color: #fff !important;
        box-shadow: 0 14px 28px rgba(47, 111, 237, 0.38) !important;
    }
</style>

<aside class="admin-sidebar student-space-sidebar">
    <div id="student-sidebar-premium" class="student-sidebar-panel" style="background:linear-gradient(180deg,#0b1f3a 0%,#132b4f 55%,#1a3967 100%);border:1px solid #2a4d7d;box-shadow:0 20px 40px rgba(12,24,45,0.42);opacity:1;">
        <a class="student-sidebar-brand" href="<?= APP_ENTRY ?>?url=student/dashboard" style="display: flex; align-items: center;">
            <img src="<?= APP_URL ?>/View/assets/images/logo.svg" alt="Appolios" style="height: 35px; width: auto;">
        </a>

        <nav class="admin-sidebar-nav student-sidebar-nav" aria-label="Front Office Navigation" style="background:transparent !important;border:0 !important;box-shadow:none !important;opacity:1 !important;">
            <a href="<?= APP_ENTRY ?>?url=student/dashboard" class="admin-side-link <?= $studentSidebarActive === 'dashboard' ? 'active' : '' ?>" style="<?= $studentSidebarActive === 'dashboard' ? $sidebarActiveLinkStyle : $sidebarBaseLinkStyle ?>">
                <span class="admin-side-icon" aria-hidden="true" style="<?= $studentSidebarActive === 'dashboard' ? $iconActiveStyle : $iconBaseStyle ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 10.5L12 3l9 7.5"></path>
                        <path d="M5 9.5V21h14V9.5"></path>
                    </svg>
                </span>
                <span style="<?= $studentSidebarActive === 'dashboard' ? $textActiveStyle : $textBaseStyle ?>">Dashboard</span>
            </a>

            <p class="student-sidebar-section" style="color:rgba(225,238,255,0.74);font-weight:700;">Pages</p>

            <a href="<?= APP_ENTRY ?>?url=student/courses" class="admin-side-link <?= $studentSidebarActive === 'courses' ? 'active' : '' ?>" style="<?= $studentSidebarActive === 'courses' ? $sidebarActiveLinkStyle : $sidebarBaseLinkStyle ?>">
                <span class="admin-side-icon" aria-hidden="true" style="<?= $studentSidebarActive === 'courses' ? $iconActiveStyle : $iconBaseStyle ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 6h16v12H4z"></path>
                        <path d="M8 10h8"></path>
                        <path d="M8 14h5"></path>
                    </svg>
                </span>
                <span style="<?= $studentSidebarActive === 'courses' ? $textActiveStyle : $textBaseStyle ?>">Courses</span>
            </a>

            <a href="<?= APP_ENTRY ?>?url=student/evenements" class="admin-side-link <?= $studentSidebarActive === 'evenements' ? 'active' : '' ?>" style="<?= $studentSidebarActive === 'evenements' ? $sidebarActiveLinkStyle : $sidebarBaseLinkStyle ?>">
                <span class="admin-side-icon" aria-hidden="true" style="<?= $studentSidebarActive === 'evenements' ? $iconActiveStyle : $iconBaseStyle ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M8 2v4"></path>
                        <path d="M16 2v4"></path>
                        <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                        <path d="M3 10h18"></path>
                    </svg>
                </span>
                <span style="<?= $studentSidebarActive === 'evenements' ? $textActiveStyle : $textBaseStyle ?>">Events</span>
            </a>

            <a href="<?= APP_ENTRY ?>?url=student/my-courses" class="admin-side-link <?= $studentSidebarActive === 'my-courses' ? 'active' : '' ?>" style="<?= $studentSidebarActive === 'my-courses' ? $sidebarActiveLinkStyle : $sidebarBaseLinkStyle ?>">
                <span class="admin-side-icon" aria-hidden="true" style="<?= $studentSidebarActive === 'my-courses' ? $iconActiveStyle : $iconBaseStyle ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 6h7v12H3z"></path>
                        <path d="M14 6h7v12h-7z"></path>
                    </svg>
                </span>
                <span style="<?= $studentSidebarActive === 'my-courses' ? $textActiveStyle : $textBaseStyle ?>">My Courses</span>
            </a>

            <a href="<?= APP_ENTRY ?>?url=student/chapitres" class="admin-side-link <?= $studentSidebarActive === 'chapitres' ? 'active' : '' ?>" style="<?= $studentSidebarActive === 'chapitres' ? $sidebarActiveLinkStyle : $sidebarBaseLinkStyle ?>">
                <span class="admin-side-icon" aria-hidden="true" style="<?= $studentSidebarActive === 'chapitres' ? $iconActiveStyle : $iconBaseStyle ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 4h16v16H4z"></path>
                        <path d="M8 8h8"></path>
                        <path d="M8 12h8"></path>
                        <path d="M8 16h5"></path>
                    </svg>
                </span>
                <span style="<?= $studentSidebarActive === 'chapitres' ? $textActiveStyle : $textBaseStyle ?>">Chapitres</span>
            </a>

            <a href="<?= APP_ENTRY ?>?url=student/quiz" class="admin-side-link <?= $studentSidebarActive === 'quiz' ? 'active' : '' ?>" style="<?= $studentSidebarActive === 'quiz' ? $sidebarActiveLinkStyle : $sidebarBaseLinkStyle ?>">
                <span class="admin-side-icon" aria-hidden="true" style="<?= $studentSidebarActive === 'quiz' ? $iconActiveStyle : $iconBaseStyle ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="9"></circle>
                        <path d="M9 12l2 2 4-4"></path>
                    </svg>
                </span>
                <span style="<?= $studentSidebarActive === 'quiz' ? $textActiveStyle : $textBaseStyle ?>">Quiz</span>
            </a>

            <a href="<?= APP_ENTRY ?>?url=student/questions-bank" class="admin-side-link <?= $studentSidebarActive === 'questions' ? 'active' : '' ?>" style="<?= $studentSidebarActive === 'questions' ? $sidebarActiveLinkStyle : $sidebarBaseLinkStyle ?>">
                <span class="admin-side-icon" aria-hidden="true" style="<?= $studentSidebarActive === 'questions' ? $iconActiveStyle : $iconBaseStyle ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 4h16v16H4z"></path>
                        <path d="M8 9h8"></path>
                        <path d="M8 13h6"></path>
                    </svg>
                </span>
                <span style="<?= $studentSidebarActive === 'questions' ? $textActiveStyle : $textBaseStyle ?>">Questions</span>
            </a>

            <a href="<?= APP_ENTRY ?>?url=student/profile" class="admin-side-link <?= $studentSidebarActive === 'profile' ? 'active' : '' ?>" style="<?= $studentSidebarActive === 'profile' ? $sidebarActiveLinkStyle : $sidebarBaseLinkStyle ?>">
                <span class="admin-side-icon" aria-hidden="true" style="<?= $studentSidebarActive === 'profile' ? $iconActiveStyle : $iconBaseStyle ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="8" r="4"></circle>
                        <path d="M4 20c2-4 5-6 8-6s6 2 8 6"></path>
                    </svg>
                </span>
                <span style="<?= $studentSidebarActive === 'profile' ? $textActiveStyle : $textBaseStyle ?>">Profile</span>
            </a>
        </nav>
    </div>
</aside>
