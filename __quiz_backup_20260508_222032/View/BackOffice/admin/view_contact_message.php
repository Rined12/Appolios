<?php
/**
 * APPOLIOS - Admin View Contact Message
 */
?>

<div class="dashboard">
    <div class="container admin-dashboard-container" style="max-width: 1400px; width: 100%;">
        <div class="admin-layout">
            <?php $adminSidebarActive = 'contact-messages'; require __DIR__ . '/partials/sidebar.php'; ?>

            <div class="admin-main" style="background: transparent; padding: 1rem 0 2rem 0; font-family: 'Inter', sans-serif;">
    <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: flex-start;">
        <div>
            <h1>View Message</h1>
            <p>Contact message details</p>
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

    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px;">
        <!-- Sender Info -->
        <div class="table-container">
            <div class="table-header">
                <h3 style="margin: 0;">Sender Information</h3>
            </div>
            <div style="padding: 30px;">
                <div style="text-align: center; margin-bottom: 25px;">
                    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border-radius: 50%; margin: 0 auto 15px; display: flex; align-items: center; justify-content: center;">
                        <svg viewBox="0 0 24 24" width="40" height="40" fill="white">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                    </div>
                    <h3 style="font-size: 1.2rem; margin-bottom: 5px;"><?= htmlspecialchars($message['name']) ?></h3>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: var(--primary-color); font-size: 0.85rem;">Email Address</label>
                    <div style="padding: 10px 12px; background: var(--gray-light); border-radius: var(--border-radius-sm);">
                        <a href="mailto:<?= htmlspecialchars($message['email']) ?>" style="color: var(--primary-color);">
                            <?= htmlspecialchars($message['email']) ?>
                        </a>
                    </div>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: var(--primary-color); font-size: 0.85rem;">Sent Date</label>
                    <div style="padding: 10px 12px; background: var(--gray-light); border-radius: var(--border-radius-sm);">
                        <?= date('F d, Y', strtotime($message['created_at'])) ?><br>
                        <small><?= date('H:i:s', strtotime($message['created_at'])) ?></small>
                    </div>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: var(--primary-color); font-size: 0.85rem;">Status</label>
                    <div style="padding: 10px 12px; border-radius: var(--border-radius-sm);">
                        <?php if ($message['is_read']): ?>
                            <span style="color: #28a745;">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" style="vertical-align: middle; margin-right: 5px;">
                                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                                </svg>
                                Read
                            </span>
                            <?php if ($message['reader_name']): ?>
                                <br><small style="color: var(--gray-dark);">by <?= htmlspecialchars($message['reader_name']) ?> on <?= date('M d, Y H:i', strtotime($message['read_at'])) ?></small>
                            <?php endif; ?>
                        <?php else: ?>
                            <span style="color: #dc3545;">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" style="vertical-align: middle; margin-right: 5px;">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                                Unread
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <div style="margin-top: 25px; padding-top: 20px; border-top: 1px solid var(--gray);">
                    <a href="mailto:<?= htmlspecialchars($message['email']) ?>?subject=Re: <?= htmlspecialchars($message['subject']) ?>" class="btn btn-primary" style="width: 100%; text-align: center;">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" style="vertical-align: middle; margin-right: 8px;">
                            <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                        </svg>
                        Reply via Email
                    </a>
                </div>
            </div>
        </div>

        <!-- Message Content -->
        <div class="table-container">
            <div class="table-header">
                <h3 style="margin: 0;">Message Content</h3>
            </div>
            <div style="padding: 30px;">
                <div style="margin-bottom: 25px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--primary-color); font-size: 0.85rem;">Subject</label>
                    <div style="padding: 15px; background: var(--gray-light); border-radius: var(--border-radius-sm); font-weight: 600; font-size: 1.1rem;">
                        <?= htmlspecialchars($message['subject']) ?>
                    </div>
                </div>

                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--primary-color); font-size: 0.85rem;">Message</label>
                    <div style="padding: 20px; background: var(--gray-light); border-radius: var(--border-radius-sm); min-height: 200px; white-space: pre-wrap; line-height: 1.6;">
                        <?= nl2br(htmlspecialchars($message['message'])) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div style="margin-top: 30px; display: flex; gap: 15px;">
        <a href="<?= APP_ENTRY ?>?url=admin/contact-messages" class="btn btn-outline">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" style="vertical-align: middle; margin-right: 8px;">
                <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
            </svg>
            Back to Inbox
        </a>
        <?php if ($message['is_read']): ?>
            <a href="<?= APP_ENTRY ?>?url=admin/mark-message-unread/<?= $message['id'] ?>" class="btn action-btn" style="background: #6c757d; color: white;">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" style="vertical-align: middle; margin-right: 8px;">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                </svg>
                Mark as Unread
            </a>
        <?php endif; ?>
        <a href="<?= APP_ENTRY ?>?url=admin/delete-contact-message/<?= $message['id'] ?>" class="btn action-btn danger" onclick="return confirm('Are you sure you want to delete this message?')">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" style="vertical-align: middle; margin-right: 8px;">
                <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
            </svg>
            Delete Message
        </a>
    </div>
            </div>
        </div>
    </div>
</div>
