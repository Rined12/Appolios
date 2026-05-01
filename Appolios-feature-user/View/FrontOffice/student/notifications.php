<?php
/**
 * APPOLIOS - Student Notifications Page
 */

$studentSidebarActive = 'notifications';

require_once __DIR__ . '/../../../Model/Notification.php';
$notificationModel = new Notification();
$notifications = $notificationModel->getByUserId($_SESSION['user_id'], 50);

if (isset($_POST['mark_all_read'])) {
    $notificationModel->markAllAsRead($_SESSION['user_id']);
    $this->setFlash('success', 'All notifications marked as read');
    $this->redirect('student/notifications');
}
?>

<div class="dashboard student-events-page">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>

            <div class="admin-main">
                <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                    <div>
                        <h1>Notifications</h1>
                        <p style="color: #64748b;">Stay updated with your learning progress</p>
                    </div>
                    <?php if (count($notifications) > 0): ?>
                        <form method="POST">
                            <button type="submit" name="mark_all_read" style="background: #3b82f6; color: white; padding: 10px 20px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600;">
                                Mark All as Read
                            </button>
                        </form>
                    <?php endif; ?>
                </div>

                <?php if (!empty($notifications)): ?>
                    <div style="background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                        <?php foreach ($notifications as $notif): ?>
                            <a href="<?= !empty($notif['link']) ? APP_ENTRY . '?url=' . $notif['link'] : '#' ?>" style="display: flex; gap: 1rem; padding: 1.25rem; border-bottom: 1px solid #e5e7eb; text-decoration: none; <?= $notif['is_read'] ? 'background: white;' : 'background: #f0f9ff;' ?> transition: background 0.2s;" onmouseover="this.style.background='#f8fafc';" onmouseout="this.style.background='<?= $notif['is_read'] ? 'white' : '#f0f9ff' ?>';">
                                <div style="width: 40px; height: 40px; border-radius: 50%; background: <?= $notif['is_read'] ? '#e5e7eb' : '#3b82f6' ?>; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <?php if ($notif['type'] === 'badge'): ?>
                                        <svg viewBox="0 0 24 24" width="20" height="20" fill="white">
                                            <circle cx="12" cy="8" r="6"></circle>
                                            <path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11" fill="white"></path>
                                        </svg>
                                    <?php else: ?>
                                        <svg viewBox="0 0 24 24" width="20" height="20" fill="white">
                                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                        </svg>
                                    <?php endif; ?>
                                </div>
                                <div style="flex: 1; min-width: 0;">
                                    <h4 style="margin: 0 0 0.25rem 0; color: #1e293b; font-size: 1rem;"><?= htmlspecialchars($notif['title']) ?></h4>
                                    <p style="margin: 0 0 0.5rem 0; color: #64748b; font-size: 0.9rem;"><?= htmlspecialchars($notif['message'] ?? '') ?></p>
                                    <span style="color: #9ca3af; font-size: 0.8rem;"><?= date('M d, Y h:i A', strtotime($notif['created_at'])) ?></span>
                                </div>
                                <?php if (!$notif['is_read']): ?>
                                    <div style="width: 8px; height: 8px; border-radius: 50%; background: #3b82f6; flex-shrink: 0; align-self: center;"></div>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 4rem; background: white; border-radius: 16px;">
                        <svg viewBox="0 0 24 24" width="60" height="60" fill="#9ca3af" style="opacity: 0.5;">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                        <h3 style="margin: 1rem 0 0.5rem 0; color: #374151;">No Notifications</h3>
                        <p style="color: #6b7280;">You're all caught up!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>