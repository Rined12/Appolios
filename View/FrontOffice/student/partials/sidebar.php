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

            <a href="<?= APP_ENTRY ?>?url=student/groupes"
                class="admin-side-link <?= $studentSidebarActive === 'groupes' ? 'active' : '' ?>">
                <span class="admin-side-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 11c1.66 0 3-1.34 3-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3z"></path>
                        <path d="M8 11c1.66 0 3-1.34 3-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3z"></path>
                        <path d="M8 13c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" transform="translate(4 0)"></path>
                    </svg>
                </span>
                <span>Groups</span>
            </a>

            <a href="<?= APP_ENTRY ?>?url=student/discussions"
                class="admin-side-link <?= $studentSidebarActive === 'discussions' ? 'active' : '' ?>">
                <span class="admin-side-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15a4 4 0 0 1-4 4H7l-4 4V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"></path>
                    </svg>
                </span>
                <span>Discussions</span>
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

            <a href="<?= APP_ENTRY ?>?url=student/certificates"
                class="admin-side-link <?= $studentSidebarActive === 'certificates' ? 'active' : '' ?>">
                <span class="admin-side-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 15l-2 5l9-5z"></path>
                        <circle cx="12" cy="8" r="6"></circle>
                    </svg>
                </span>
                <span>Certificates</span>
            </a>

            <a href="<?= APP_ENTRY ?>?url=student-quiz/quiz"
                class="admin-side-link <?= $studentSidebarActive === 'quiz' ? 'active' : '' ?>">
                <span class="admin-side-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M2 12h4l2-9 5 18 3-13 4 4h4"></path>
                    </svg>
                </span>
                <span>Quiz</span>
            </a>

            <a href="<?= APP_ENTRY ?>?url=student/questions-bank"
                class="admin-side-link <?= $studentSidebarActive === 'questions-bank' ? 'active' : '' ?>">
                <span class="admin-side-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                    </svg>
                </span>
                <span>Questions</span>
            </a>

            <a href="<?= APP_ENTRY ?>?url=student/badges"
                class="admin-side-link <?= $studentSidebarActive === 'badges' ? 'active' : '' ?>">
                <span class="admin-side-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2l3 6 7 1-5 5 1 7-6-3-6 3 1-7-5-5 7-1z"></path>
                    </svg>
                </span>
                <span>Badges</span>
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

            <a href="<?= APP_ENTRY ?>?url=student/leaderboard"
                class="admin-side-link <?= $studentSidebarActive === 'leaderboard' ? 'active' : '' ?>">
                <span class="admin-side-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"></path>
                    </svg>
                </span>
                <span>Leaderboard</span>
            </a>

            <a href="<?= APP_ENTRY ?>?url=student/ranks"
                class="admin-side-link <?= $studentSidebarActive === 'ranks' ? 'active' : '' ?>">
                <span class="admin-side-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2l3 6 7 1-5 5 1 7-6-3-6 3 1-7-5-5 7-1z"></path>
                    </svg>
                </span>
                <span>Ranks</span>
            </a>

            <a href="<?= APP_ENTRY ?>?url=student/notifications"
                class="admin-side-link <?= $studentSidebarActive === 'notifications' ? 'active' : '' ?>">
                <span class="admin-side-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                </span>
                <span>Notifications</span>
            </a>

        </nav>
    </div>
</aside>