<?php
/**
 * APPOLIOS - Login Page (Premium Neo Design)
 */

// Flash message is passed from controller via $data['flash']
?>

<div class="section neo-auth-wrap neo-login-page">
    <div class="neo-glass-card neo-auth-grid">
        <div class="aside neo-auth-info">
            <h2>Welcome Back</h2>
            <p class="neo-muted" style="margin-top: 0.5rem;">Access your dashboard, continue courses, and track your achievements.</p>
            <div class="neo-badges" style="margin-top: 1rem;">
                <span class="neo-badge primary">Dark Mode</span>
                <span class="neo-badge success">Progress Tracking</span>
                <span class="neo-badge warning">Gamification</span>
            </div>

            <div class="neo-login-hero-visual">
                <div class="neo-login-hero-circle"></div>
                <img src="<?= APP_URL ?>/View/assets/images/instructor/06.jpg" alt="Student learning" class="neo-login-hero-photo">
            </div>
        </div>

        <div class="neo-auth-form">
            <!-- Back to Home Button -->
            <a href="<?= APP_ENTRY ?>?url=home/index" style="display: inline-flex; align-items: center; gap: 6px; color: #64748b; text-decoration: none; font-size: 0.85rem; font-weight: 600; margin-bottom: 1rem; transition: color 0.2s;" onmouseover="this.style.color='#2B4865'" onmouseout="this.style.color='#64748b'">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Back to Home
            </a>
            <h2>Sign In</h2>
            <p class="neo-muted" style="margin-top: 0.45rem;">Use your APPOLIOS account credentials.</p>

            <?php if ($flash): ?>
                <div class="neo-alert <?= $flash['type'] === 'error' ? 'error' : 'success' ?>">
                    <?= htmlspecialchars($flash['message']) ?>
                </div>
            <?php endif; ?>

            <form action="<?= APP_ENTRY ?>?url=authenticate" method="POST" novalidate>
                <div class="neo-field">
                    <label for="email">Email Address</label>
                    <input type="text" id="email" name="email" placeholder="you@example.com" data-js-required="1">
                </div>

                <div class="neo-field">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" data-js-required="1">
                </div>

                <button type="submit" class="neo-btn neo-btn-primary" style="margin-top: 0.95rem; width: 100%;">Sign In</button>
            </form>

            <a href="<?= APP_ENTRY ?>?url=admin/login" class="neo-btn neo-btn-secondary" style="margin-top: 0.75rem; width: 100%; text-align: center; display: inline-block;">
                Admin Login
            </a>

            <p class="neo-muted" style="margin-top: 0.85rem; font-size: 0.9rem;">Don't have an account? <a href="<?= APP_ENTRY ?>?url=register" style="color: #93c5fd;">Create one</a></p>
        </div>
    </div>
</div>