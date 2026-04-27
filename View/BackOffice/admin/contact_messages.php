<?php
/**
 * APPOLIOS - Admin Contact Messages Inbox
 */
?>

<div class="dashboard">
    <div class="container admin-dashboard-container" style="max-width: 1400px; width: 100%;">
        <div class="admin-layout">
            <?php $adminSidebarActive = 'contact-messages'; require __DIR__ . '/partials/sidebar.php'; ?>

            <div class="admin-main" style="background: transparent; padding: 1rem 0 2rem 0; font-family: 'Inter', sans-serif;">
    <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: flex-start;">
        <div>
            <h1>Contact Messages Inbox</h1>
            <p>View and manage messages from Contact Us form</p>
        </div>
        <a href="javascript:history.back()" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; background: #6c757d;">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" style="transform: rotate(180deg);">
                <path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/>
            </svg>
            Back
        </a>
    </div>

    <?php if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] ?>" style="margin-bottom: 20px;">
            <?= htmlspecialchars($flash['message']) ?>
        </div>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div class="stat-card" style="background: linear-gradient(135deg, rgba(84, 140, 168, 0.1) 0%, rgba(84, 140, 168, 0.05) 100%); border: 1px solid rgba(84, 140, 168, 0.2); border-radius: 12px; padding: 20px;">
            <div style="font-size: 2rem; font-weight: 700; color: #548CA8;"><?= count($messages) ?></div>
            <div style="color: #64748b; font-size: 0.9rem;">Total Messages</div>
        </div>
        <div class="stat-card" style="background: linear-gradient(135deg, rgba(220, 53, 69, 0.1) 0%, rgba(220, 53, 69, 0.05) 100%); border: 1px solid rgba(220, 53, 69, 0.2); border-radius: 12px; padding: 20px;">
            <div style="font-size: 2rem; font-weight: 700; color: #dc3545;"><?= $unreadCount ?></div>
            <div style="color: #64748b; font-size: 0.9rem;">Unread Messages</div>
        </div>
        <div class="stat-card" style="background: linear-gradient(135deg, rgba(40, 167, 69, 0.1) 0%, rgba(40, 167, 69, 0.05) 100%); border: 1px solid rgba(40, 167, 69, 0.2); border-radius: 12px; padding: 20px;">
            <div style="font-size: 2rem; font-weight: 700; color: #28a745;"><?= count($messages) - $unreadCount ?></div>
            <div style="color: #64748b; font-size: 0.9rem;">Read Messages</div>
        </div>
    </div>

    <!-- Messages List -->
    <div class="table-container">
        <div class="table-header">
            <h3 style="margin: 0;">
                All Messages
                <?php if ($unreadCount > 0): ?>
                    <span style="background: #dc3545; color: white; padding: 2px 8px; border-radius: 12px; font-size: 0.75rem; margin-left: 10px;">
                        <?= $unreadCount ?> new
                    </span>
                <?php endif; ?>
            </h3>
        </div>

        <?php if (empty($messages)): ?>
            <div style="padding: 60px 20px; text-align: center; color: var(--gray-dark);">
                <svg viewBox="0 0 24 24" width="64" height="64" fill="var(--gray)" style="margin-bottom: 20px;">
                    <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                </svg>
                <h3 style="margin-bottom: 10px; color: var(--primary-color);">No Messages Yet</h3>
                <p>Messages sent through the Contact Us form will appear here.</p>
            </div>
        <?php else: ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">Status</th>
                        <th>From</th>
                        <th>Subject</th>
                        <th>Date</th>
                        <th style="width: 180px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($messages as $msg): ?>
                        <tr style="<?= $msg['is_read'] ? '' : 'background: rgba(84, 140, 168, 0.05); font-weight: 600;' ?>">
                            <td style="text-align: center;">
                                <?php if ($msg['is_read']): ?>
                                    <span style="color: #28a745;" title="Read">
                                        <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
                                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                                        </svg>
                                    </span>
                                <?php else: ?>
                                    <span style="color: #dc3545;" title="Unread">
                                        <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
                                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                        </svg>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div><?= htmlspecialchars($msg['name']) ?></div>
                                <small style="color: var(--gray-dark);"><?= htmlspecialchars($msg['email']) ?></small>
                            </td>
                            <td>
                                <?= htmlspecialchars($msg['subject']) ?>
                                <?php if (!$msg['is_read']): ?>
                                    <span style="background: #dc3545; color: white; padding: 2px 6px; border-radius: 4px; font-size: 0.7rem; margin-left: 8px;">NEW</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= date('M d, Y H:i', strtotime($msg['created_at'])) ?>
                            </td>
                            <td>
                                <a href="<?= APP_ENTRY ?>?url=admin/view-contact-message/<?= $msg['id'] ?>" class="btn action-btn" style="padding: 5px 10px; font-size: 0.8rem;">
                                    View
                                </a>
                                <?php if ($msg['is_read']): ?>
                                    <a href="<?= APP_ENTRY ?>?url=admin/mark-message-unread/<?= $msg['id'] ?>" class="btn action-btn" style="padding: 5px 10px; font-size: 0.8rem; background: #6c757d; color: white;">
                                        Mark Unread
                                    </a>
                                <?php endif; ?>
                                <a href="<?= APP_ENTRY ?>?url=admin/delete-contact-message/<?= $msg['id'] ?>" class="btn action-btn danger" style="padding: 5px 10px; font-size: 0.8rem;" onclick="return confirm('Are you sure you want to delete this message?')">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
            </div>
        </div>
    </div>
</div>
