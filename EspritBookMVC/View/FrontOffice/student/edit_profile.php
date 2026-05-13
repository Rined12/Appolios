<?php
/**
 * APPOLIOS - Edit Profile Page
 */
?>

<div class="dashboard">
    <div class="container">
        <div class="dashboard-header">
            <h1>Edit Profile</h1>
            <p>Update your account information</p>
        </div>

        <?php if (!empty($flash_banner)): ?>
            <div class="alert alert-<?= htmlspecialchars((string) ($flash_banner['alert_class'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" style="<?= htmlspecialchars((string) ($flash_banner['inner_style'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                <?= htmlspecialchars((string) ($flash_banner['message'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <div style="max-width: 600px; margin: 0 auto;">
            <div class="table-container">
                <div class="table-header">
                    <h3 style="margin: 0;">Edit Your Information</h3>
                </div>
                <div style="padding: 30px;">
                    <form action="<?= APP_ENTRY ?>?url=student/update-profile" method="POST" novalidate>
                        <div style="margin-bottom: 25px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--primary-color);">Full Name *</label>
                            <input type="text" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" data-js-required="1" style="width: 100%; padding: 12px 16px; border: 2px solid var(--gray); border-radius: var(--border-radius-sm); font-size: 1rem; box-sizing: border-box;">
                        </div>

                        <div style="margin-bottom: 25px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--primary-color);">Email Address *</label>
                            <input type="text" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" data-js-required="1" style="width: 100%; padding: 12px 16px; border: 2px solid var(--gray); border-radius: var(--border-radius-sm); font-size: 1rem; box-sizing: border-box;">
                        </div>

                        <div style="margin-bottom: 25px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--primary-color);">Account Type</label>
                            <div style="padding: 12px 16px; background: var(--gray-light); border-radius: var(--border-radius-sm); color: var(--gray-dark);">
                                <?= ucfirst(htmlspecialchars($user['role'] ?? 'student')) ?> (cannot be changed)
                            </div>
                        </div>

                        <hr style="margin: 30px 0; border: none; border-top: 1px solid var(--gray);">

                        <h4 style="margin-bottom: 20px; color: var(--primary-color);">Change Password (optional)</h4>

                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--primary-color);">Current Password</label>
                            <input type="password" name="current_password" placeholder="Enter current password to change password" style="width: 100%; padding: 12px 16px; border: 2px solid var(--gray); border-radius: var(--border-radius-sm); font-size: 1rem; box-sizing: border-box;">
                        </div>

                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--primary-color);">New Password</label>
                            <input type="password" name="new_password" placeholder="Enter new password (min 6 characters)" style="width: 100%; padding: 12px 16px; border: 2px solid var(--gray); border-radius: var(--border-radius-sm); font-size: 1rem; box-sizing: border-box;">
                        </div>

                        <div style="margin-bottom: 30px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--primary-color);">Confirm New Password</label>
                            <input type="password" name="confirm_password" placeholder="Confirm new password" style="width: 100%; padding: 12px 16px; border: 2px solid var(--gray); border-radius: var(--border-radius-sm); font-size: 1rem; box-sizing: border-box;">
                        </div>

                        <div style="display: flex; gap: 15px;">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                            <a href="<?= APP_ENTRY ?>?url=student/profile" class="btn btn-outline">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
