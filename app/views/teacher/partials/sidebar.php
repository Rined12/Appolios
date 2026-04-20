<?php
/**
 * APPOLIOS - Teacher Sidebar (cours, chapitres, quiz, questions, événements)
 */
$teacherSidebarActive = $teacherSidebarActive ?? '';
?>

<?php
// Social Learning module base URL derived from APP_URL
$slBase = APP_URL . '/index.php?url=social-learning/';
$slGroupesUrlPrefix = 'teacher/groupes';
?>

<aside class="admin-sidebar">
    <nav class="admin-sidebar-nav" aria-label="Navigation enseignant">
        <a href="<?= APP_URL ?>/index.php?url=teacher/dashboard" class="admin-side-link <?= $teacherSidebarActive === 'espace' ? 'active' : '' ?>">
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

        <a href="<?= APP_URL ?>/index.php?url=teacher/courses" class="admin-side-link <?= $teacherSidebarActive === 'courses' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="16" rx="2"></rect>
                    <path d="M3 10h18"></path>
                </svg>
            </span>
            <span>Cours</span>
        </a>

        <a href="<?= APP_URL ?>/index.php?url=teacher/chapitres" class="admin-side-link <?= $teacherSidebarActive === 'chapitres' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                </svg>
            </span>
            <span>Chapitre</span>
        </a>

        <a href="<?= APP_URL ?>/index.php?url=teacher/quiz" class="admin-side-link <?= $teacherSidebarActive === 'quiz' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
            </span>
            <span>Quiz</span>
        </a>

        <a href="<?= APP_URL ?>/index.php?url=teacher/questions" class="admin-side-link <?= $teacherSidebarActive === 'questions' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                </svg>
            </span>
            <span>Question</span>
        </a>

        <a href="<?= APP_URL ?>/index.php?url=teacher/evenements" class="admin-side-link <?= $teacherSidebarActive === 'evenements' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M8 2v4"></path>
                    <path d="M16 2v4"></path>
                    <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                    <path d="M3 10h18"></path>
                </svg>
            </span>
            <span>Événement</span>
        </a>
        <!-- ====== Social Learning ====== -->
        <div class="sl-sidebar-separator">
            <span>Social Learning</span>
        </div>

        <a href="<?= APP_URL ?>/index.php?url=<?= $slGroupesUrlPrefix ?>" class="admin-side-link <?= $teacherSidebarActive === 'groupes' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                </svg>
            </span>
            <span>Groupes</span>
        </a>

        <a href="<?= $slBase ?>discussion" class="admin-side-link <?= $teacherSidebarActive === 'discussions' ? 'active' : '' ?>">
            <span class="admin-side-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2z"/>
                </svg>
            </span>
            <span>Discussions</span>
        </a>
    </nav>
</aside>
