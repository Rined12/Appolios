<?php
/**
 * APPOLIOS - Neo Admin Pro Header & Sidebar
 */
$adminSidebarActive = $adminSidebarActive ?? '';
$userAvatar = $_SESSION['user_avatar'] ?? 'default-admin.png';
$userName = $_SESSION['user_name'] ?? 'Administrator';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Pro | <?= htmlspecialchars($title ?? 'Dashboard') ?></title>
    <link rel="stylesheet" href="<?= APP_URL ?>/View/assets/vendor/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/View/assets/vendor/font-awesome/css/all.min.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/View/assets/css/admin-neo.css?v=<?= time() ?>">
    <script>
        // Pre-load theme to prevent flash
        (function() {
            const theme = localStorage.getItem('theme') || 'light';
            if (theme === 'dark') {
                document.documentElement.classList.add('dark-theme');
                // Also add to body once it's ready
                document.addEventListener('DOMContentLoaded', () => {
                    document.body.classList.add('dark-theme');
                    const icon = document.getElementById('theme-icon');
                    if (icon) icon.classList.replace('bi-sun', 'bi-moon-fill');
                });
            }
        })();
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="admin-body">

    <!-- ── Sidebar ────────────────────────────────────────────────── -->
    <aside class="admin-sidebar-pro">
        <a href="<?= APP_ENTRY ?>?url=home/index" class="brand">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#E19864" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"></path><path d="M2 17l10 5 10-5"></path><path d="M2 12l10 5 10-5"></path></svg>
            <span>APPOLIOS</span>
        </a>

        <nav class="nav-list">
            <a href="<?= APP_ENTRY ?>?url=admin/dashboard" class="nav-item <?= $adminSidebarActive === 'dashboard' ? 'active' : '' ?>">
                <i class="bi bi-grid-fill"></i>
                <span>Tableau de bord</span>
            </a>
            
            <a href="<?= APP_ENTRY ?>?url=admin/teacher-applications" class="nav-item <?= $adminSidebarActive === 'teacher-applications' ? 'active' : '' ?>">
                <i class="bi bi-person-badge-fill"></i>
                <span>Candidatures</span>
                <?php if (isset($pendingTeacherApps) && $pendingTeacherApps > 0): ?>
                    <span style="margin-left:auto; background:var(--admin-secondary); color:white; font-size:0.7rem; padding:2px 8px; border-radius:10px;"><?= $pendingTeacherApps ?></span>
                <?php endif; ?>
            </a>

            <a href="<?= APP_ENTRY ?>?url=admin/users" class="nav-item <?= $adminSidebarActive === 'users' ? 'active' : '' ?>">
                <i class="bi bi-people-fill"></i>
                <span>Utilisateurs</span>
            </a>

            <a href="<?= APP_ENTRY ?>?url=admin/teachers" class="nav-item <?= $adminSidebarActive === 'teachers' ? 'active' : '' ?>">
                <i class="bi bi-mortarboard-fill"></i>
                <span>Enseignants</span>
            </a>

            <a href="<?= APP_ENTRY ?>?url=admin/contact-messages" class="nav-item <?= $adminSidebarActive === 'contact-messages' ? 'active' : '' ?>">
                <i class="bi bi-chat-dots-fill"></i>
                <span>Messages</span>
                <?php if (isset($unreadCount) && $unreadCount > 0): ?>
                    <span style="margin-left:auto; background:#ef4444; color:white; font-size:0.7rem; padding:2px 8px; border-radius:10px;"><?= $unreadCount ?></span>
                <?php endif; ?>
            </a>

            <a href="<?= APP_ENTRY ?>?url=admin/activity-log" class="nav-item <?= $adminSidebarActive === 'activity-log' ? 'active' : '' ?>">
                <i class="bi bi-clock-history"></i>
                <span>Historique</span>
            </a>

            <a href="<?= APP_ENTRY ?>?url=admin/statistics" class="nav-item <?= $adminSidebarActive === 'statistics' ? 'active' : '' ?>">
                <i class="bi bi-pie-chart-fill"></i>
                <span>Statistiques</span>
            </a>
            
            <hr style="border:0; border-top:1px solid rgba(255,255,255,0.05); margin:1rem 0;">
            
            <a href="<?= APP_ENTRY ?>?url=admin/evenements" class="nav-item <?= $adminSidebarActive === 'evenements' ? 'active' : '' ?>">
                <i class="bi bi-calendar-event-fill"></i>
                <span>Événements</span>
            </a>
            
            <a href="<?= APP_ENTRY ?>?url=admin/courses" class="nav-item <?= $adminSidebarActive === 'courses' ? 'active' : '' ?>">
                <i class="bi bi-book-half"></i>
                <span>Cours</span>
            </a>

            <hr style="border:0; border-top:1px solid rgba(255,255,255,0.05); margin:1rem 0;">
            <p style="padding: 0 1.5rem; font-size: 0.65rem; color: rgba(255,255,255,0.3); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.5rem;">Aperçu Espaces</p>

            <a href="<?= APP_ENTRY ?>?url=student/dashboard" class="nav-item">
                <i class="bi bi-mortarboard"></i>
                <span>Espace Étudiant</span>
            </a>

            <a href="<?= APP_ENTRY ?>?url=teacher/dashboard" class="nav-item">
                <i class="bi bi-person-video3"></i>
                <span>Espace Teacher</span>
            </a>

            <hr style="border:0; border-top:1px solid rgba(255,255,255,0.05); margin:1rem 0;">

            <a href="<?= APP_ENTRY ?>?url=student/profile" class="nav-item <?= $adminSidebarActive === 'profile' ? 'active' : '' ?>">
                <i class="bi bi-person-circle"></i>
                <span>Mon Profil</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <a href="<?= APP_ENTRY ?>?url=logout" class="nav-item" style="color:#f87171;">
                <i class="bi bi-box-arrow-left"></i>
                <span>Déconnexion</span>
            </a>
        </div>
    </aside>

    <!-- ── Content Wrapper ────────────────────────────────────────── -->
    <main class="admin-content-pro">
        
        <!-- ── Top Navigation ─────────────────────────────────────────── -->
        <!-- Top Navigation Removed as requested -->


        <div class="admin-page-container">
            <?php require_once __DIR__ . '/quick_nav.php'; ?>
