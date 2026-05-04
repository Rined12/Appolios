<?php
/**
 * Global Quick Navigation Header
 */
$currentPage = $_GET['url'] ?? '';
?>

<!-- Quick Navigation Header -->
<div class="dashboard-quick-nav">
    <div class="nav-left">
        <a href="<?= APP_ENTRY ?>?url=home" class="nav-logo">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--admin-secondary);"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path></svg>
            <span>APPOLIOS</span>
        </a>
    </div>
    <div class="nav-center">
        <a href="<?= APP_ENTRY ?>?url=home" class="quick-link">Home</a>
        <a href="<?= APP_ENTRY ?>?url=courses" class="quick-link">Courses</a>
        <a href="<?= APP_ENTRY ?>?url=about" class="quick-link">About</a>
        <a href="<?= APP_ENTRY ?>?url=contact" class="quick-link">Contact</a>
        <a href="<?= APP_ENTRY ?>?url=admin/dashboard" class="quick-link <?= ($currentPage === 'admin/dashboard') ? 'active' : '' ?>">Dashboard</a>
    </div>
    <div class="nav-right">
        <!-- Notification Bell (Integrated) -->
        <div class="notification-wrapper">
            <button class="theme-toggle" title="Notifications" id="quick-notif-btn">
                <i class="bi bi-bell-fill"></i>
                <?php if (isset($pendingTeacherApps) && $pendingTeacherApps > 0 || isset($unreadCount) && $unreadCount > 0): ?>
                    <span class="notif-indicator"></span>
                <?php endif; ?>
            </button>
            
            <div class="notif-dropdown" id="quick-notif-dropdown">
                <div class="notif-header">Notifications</div>
                <div class="notif-body">
                    <?php if (isset($pendingTeacherApps) && $pendingTeacherApps > 0): ?>
                        <a href="<?= APP_ENTRY ?>?url=admin/teacher-applications" class="notif-item">
                            <div class="notif-icon" style="background: #e0e7ff; color: #4338ca;"><i class="bi bi-person-badge"></i></div>
                            <div class="notif-content">
                                <p><strong><?= $pendingTeacherApps ?></strong> nouvelles candidatures</p>
                                <small>En attente de révision</small>
                            </div>
                        </a>
                    <?php endif; ?>

                    <?php if (isset($unreadCount) && $unreadCount > 0): ?>
                        <a href="<?= APP_ENTRY ?>?url=admin/contact-messages" class="notif-item">
                            <div class="notif-icon" style="background: #fef3c7; color: #b45309;"><i class="bi bi-chat-dots"></i></div>
                            <div class="notif-content">
                                <p><strong><?= $unreadCount ?></strong> nouveaux messages</p>
                                <small>Consulter la boîte de réception</small>
                            </div>
                        </a>
                    <?php endif; ?>

                    <?php if ((!isset($pendingTeacherApps) || $pendingTeacherApps == 0) && (!isset($unreadCount) || $unreadCount == 0)): ?>
                        <div style="padding: 2rem; text-align: center; color: #94a3b8; font-size: 0.9rem;">
                            <i class="bi bi-check2-circle" style="font-size: 1.5rem; display: block; margin-bottom: 0.5rem;"></i>
                            Aucune notification
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <button class="theme-toggle" onclick="location.reload()" title="Rafraîchir">
            <i class="bi bi-arrow-clockwise"></i>
        </button>

        <button class="theme-toggle"><i class="bi bi-sun"></i></button>
        
        <a href="<?= APP_ENTRY ?>?url=logout" class="quick-logout">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </div>
</div>

<script>
// Toggle quick notification dropdown
(function() {
    const qNotifBtn = document.getElementById('quick-notif-btn');
    const qNotifDropdown = document.getElementById('quick-notif-dropdown');

    if (qNotifBtn) {
        qNotifBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            qNotifDropdown.classList.toggle('active');
        });
    }

    document.addEventListener('click', () => {
        if (qNotifDropdown) qNotifDropdown.classList.remove('active');
    });

    if (qNotifDropdown) {
        qNotifDropdown.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    }
})();
</script>

<style>
.dashboard-quick-nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: white;
    padding: 0.8rem 2rem;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    margin-bottom: 2.5rem;
    border: 1px solid #f1f5f9;
}
.nav-logo {
    display: flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
    color: #1e293b;
    font-weight: 800;
    font-size: 1.1rem;
    letter-spacing: 0.5px;
}
.nav-logo svg {
    width: 24px;
    height: 24px;
    flex-shrink: 0;
}
.nav-center {
    display: flex;
    gap: 2rem;
}
.quick-link {
    text-decoration: none;
    color: #64748b;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.2s;
    position: relative;
    padding: 5px 0;
}
.quick-link:hover { color: #1e293b; }
.quick-link.active { color: #E19864; }
.quick-link.active::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 2px;
    background: #E19864;
    border-radius: 2px;
}
.nav-right {
    display: flex;
    align-items: center;
    gap: 1rem;
}
.notification-wrapper { position: relative; }
.notif-indicator {
    position: absolute;
    top: 8px;
    right: 8px;
    width: 9px;
    height: 9px;
    background: #ef4444;
    border: 2px solid white;
    border-radius: 50%;
}
.notif-dropdown {
    position: absolute;
    top: calc(100% + 15px);
    right: 0;
    width: 280px;
    background: white;
    border-radius: 15px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    border: 1px solid #f1f5f9;
    opacity: 0;
    transform: translateY(10px);
    pointer-events: none;
    transition: all 0.2s;
    z-index: 2000;
}
.notif-dropdown.active {
    opacity: 1;
    transform: translateY(0);
    pointer-events: all;
}
.notif-header {
    padding: 1rem 1.2rem;
    font-weight: 700;
    border-bottom: 1px solid #f1f5f9;
    color: #1e293b;
}
.notif-body {
    max-height: 400px;
    overflow-y: auto;
}
.notif-item {
    display: flex;
    gap: 10px;
    padding: 0.8rem 1.2rem;
    text-decoration: none;
    color: inherit;
    transition: background 0.2s;
    border-bottom: 1px solid #f8fafc;
}
.notif-item:hover { background: #f8fafc; }
.notif-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 0.9rem;
}
.notif-content p { margin: 0; font-size: 0.8rem; color: #1e293b; }
.notif-content small { color: #64748b; font-size: 0.7rem; }

.theme-toggle {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    width: 38px;
    height: 38px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #64748b;
    cursor: pointer;
    transition: all 0.2s;
}
.theme-toggle:hover { background: #f1f5f9; color: #1e293b; border-color: #cbd5e1; }

.quick-logout {
    display: flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    background: white;
    border: 1px solid #e2e8f0;
    padding: 8px 18px;
    border-radius: 10px;
    color: #475569;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.2s;
}
.quick-logout:hover {
    background: #fff1f2;
    border-color: #fecaca;
    color: #be123c;
}
</style>
