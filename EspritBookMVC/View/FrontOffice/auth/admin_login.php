<?php
/**
 * APPOLIOS - Admin Login Page
 */

// Get flash messages
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>

<div style="padding-top: 120px; padding-bottom: 80px; min-height: 100vh; background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);">
    <div class="container">
        <div class="form-container">
            <!-- Back to Home Button -->
            <div style="margin-bottom: 20px;">
                <a href="<?= APP_ENTRY ?>?url=home/index" style="display: inline-flex; align-items: center; gap: 6px; color: #64748b; text-decoration: none; font-size: 0.9rem; font-weight: 600; transition: color 0.2s;" onmouseover="this.style.color='#2B4865'" onmouseout="this.style.color='#64748b'">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    Back to Home
                </a>
            </div>
            <div style="text-align: center; margin-bottom: 30px;">
                <h2 style="color: var(--primary-color);">Administrator Portal</h2>
                <p style="color: var(--gray-dark);">Sign in with your admin credentials</p>
            </div>

            <?php if ($flash): ?>
                <div style="padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem; <?= $flash['type'] === 'error' ? 'background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;' : 'background: #d4edda; color: #155724; border: 1px solid #c3e6cb;' ?>">
                    <?= htmlspecialchars($flash['message']) ?>
                </div>
            <?php endif; ?>

            <form action="<?= APP_ENTRY ?>?url=authenticate" method="POST" novalidate>
                <input type="hidden" name="admin_login" value="1">
                <div class="form-group">
                    <label for="email">Admin Email</label>
                    <input type="text" id="email" name="email" placeholder="Enter admin email" data-js-required="1">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter password" data-js-required="1">
                </div>

                <button type="submit" class="btn btn-yellow btn-block">Administrator Sign In</button>
            </form>

            <p class="form-text">
                <a href="<?= APP_ENTRY ?>?url=login" style="color: var(--secondary-color);">← Back to Student Login</a>
            </p>
        </div>
    </div>
</div>