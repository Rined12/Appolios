<?php
/**
 * APPOLIOS - Teacher Profile Page
 */

$teacherSidebarActive = 'courses';
?>

<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main">
        <div class="dashboard-header">
            <h1>My Profile</h1>
            <p>Manage your account information</p>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px;">
            <!-- Profile Card -->
            <div class="table-container">
                <div style="padding: 40px; text-align: center;">
                    <div style="width: 120px; height: 120px; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border-radius: 50%; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center;">
                        <svg viewBox="0 0 24 24" width="60" height="60" fill="white">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                    </div>
                    <h2 style="font-size: 1.5rem;"><?= htmlspecialchars($user['name']) ?></h2>
                    <p style="color: var(--gray-dark); font-size: 0.9rem;"><?= htmlspecialchars($user['email']) ?></p>
                    <p style="margin-top: 15px;">
                        <span style="padding: 6px 16px; background: var(--yellow); color: var(--primary-color); border-radius: 20px; font-size: 0.85rem; font-weight: 600;">
                            TEACHER
                        </span>
                    </p>
                </div>
            </div>

            <!-- Account Info -->
            <div class="table-container">
                <div class="table-header">
                    <h3 style="margin: 0;">Account Information</h3>
                </div>
                <div style="padding: 30px;">
                    <div style="margin-bottom: 25px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--primary-color);">Full Name</label>
                        <div style="padding: 12px 16px; background: var(--gray-light); border-radius: var(--border-radius-sm);">
                            <?= htmlspecialchars($user['name']) ?>
                        </div>
                    </div>

                    <div style="margin-bottom: 25px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--primary-color);">Email Address</label>
                        <div style="padding: 12px 16px; background: var(--gray-light); border-radius: var(--border-radius-sm);">
                            <?= htmlspecialchars($user['email']) ?>
                        </div>
                    </div>

                    <div style="margin-bottom: 25px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--primary-color);">Account Type</label>
                        <div style="padding: 12px 16px; background: var(--gray-light); border-radius: var(--border-radius-sm);">
                            Teacher (Courses can be created by you)
                        </div>
                    </div>

                    <div style="margin-bottom: 25px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--primary-color);">Member Since</label>
                        <div style="padding: 12px 16px; background: var(--gray-light); border-radius: var(--border-radius-sm);">
                            <?= date('F d, Y', strtotime($user['created_at'])) ?>
                        </div>
                    </div>

                    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid var(--gray);">
                        <a href="<?= APP_ENTRY ?>?url=logout" class="btn btn-outline" style="color: #dc3545; border-color: #dc3545;">
                            Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
            </div>
        </div>
    </div>
</div>
