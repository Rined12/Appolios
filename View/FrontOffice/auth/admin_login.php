<?php
/**
 * APPOLIOS - Admin Login Page
 */

$flash = isset($flash) ? $flash : null;
?>

<div style="padding-top: 120px; padding-bottom: 80px; min-height: 100vh; background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);">
    <div class="container">
        <div class="form-container">
            <div style="text-align: center; margin-bottom: 30px;">
                <h2 style="color: var(--primary-color);">Administrator Portal</h2>
                <p style="color: var(--gray-dark);">Sign in with your admin credentials</p>
            </div>

            <?php if ($flash): ?>
                <div style="padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem; <?= $flash['type'] === 'error' ? 'background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;' : 'background: #d4edda; color: #155724; border: 1px solid #c3e6cb;' ?>">
                    <?= htmlspecialchars($flash['message']) ?>
                </div>
            <?php endif; ?>

            <form action="<?= APP_ENTRY ?>?url=authenticate" method="POST">
                <input type="hidden" name="admin_login" value="1">
                <div class="form-group">
                    <label for="email">Admin Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter admin email" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter password" required>
                </div>

                <button type="submit" class="btn btn-yellow btn-block">Administrator Sign In</button>
            </form>

            <p class="form-text">
                <a href="<?= APP_ENTRY ?>?url=login" style="color: var(--secondary-color);">← Back to Student Login</a>
            </p>
        </div>
    </div>
</div>