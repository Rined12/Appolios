<?php
$studentSidebarActive = 'notifications';
?>
<div class="dashboard student-events-page">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main" style="grid-template-rows: min-content; align-items: start; height: auto; align-content: start;">
                <h1 style="margin: 0 0 0.5rem 0; font-size: 24px;">Notifications</h1>
                <p style="color: #64748b; margin: 0 0 1.5rem 0;"><?= count($notifications ?? []) ?> notifications</p>

                <?php if (!empty($notifications)): ?>
                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <?php foreach ($notifications as $notif): ?>
                            <div style="background: white; border-radius: 12px; padding: 1rem; display: flex; gap: 1rem; box-shadow: 0 2px 8px rgba(0,0,0,0.05); border-left: 4px solid <?= $notif['type'] === 'success' ? '#10b981' : ($notif['type'] === 'error' ? '#ef4444' : '#3b82f6') ?>;">
                                <div style="width: 40px; height: 40px; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center;">
                                    <?php if ($notif['type'] === 'success'): ?>
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    <?php elseif ($notif['type'] === 'error'): ?>
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                                    <?php else: ?>
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                                    <?php endif; ?>
                                </div>
                                <div style="flex: 1;">
                                    <p style="margin: 0 0 0.5rem 0; color: #1e293b;"><?= htmlspecialchars($notif['message']) ?></p>
                                    <span style="font-size: 0.75rem; color: #94a3b8;"><?= date('M d, Y H:i', strtotime($notif['created_at'])) ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 3rem; background: white; border-radius: 12px;">
                        <svg width="60" height="60" viewBox="0 0 24 24" fill="#94a3b8" style="opacity: 0.3;">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                        <p style="color: #64748b; margin-top: 1rem;">No notifications</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>