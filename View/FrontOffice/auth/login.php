<?php
/**
 * APPOLIOS - Login Page (Premium Neo Design)
 */

// Get flash messages
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>

<section class="neo-auth-wrap neo-login-page">
    <div class="neo-glass-card neo-auth-grid">
        <aside class="neo-auth-info">
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
        </aside>

        <div class="neo-auth-form">
            <h2>Sign In</h2>
            <p class="neo-muted" style="margin-top: 0.45rem;">Use your APPOLIOS account credentials.</p>

            <?php if ($flash): ?>
                <div class="neo-alert <?= $flash['type'] === 'error' ? 'error' : 'success' ?>">
                    <?= htmlspecialchars($flash['message']) ?>
                </div>
            <?php endif; ?>

            <form action="<?= APP_ENTRY ?>?url=authenticate" method="POST">
                <div class="neo-field">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="you@example.com" required>
                </div>

                <div class="neo-field">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>

                <div class="neo-field">
                    <label>I am a</label>
                    <div style="display: flex; gap: 0.9rem; color: #334155; font-size: 0.92rem; margin-top: 0.32rem;">
                        <label><input type="radio" name="user_type" value="student" checked> Student</label>
                        <label><input type="radio" name="user_type" value="teacher"> Teacher</label>
                    </div>
                </div>

                <button type="submit" class="neo-btn neo-btn-primary" style="margin-top: 0.95rem; width: 100%;">Sign In</button>
            </form>

            <p class="neo-muted" style="margin-top: 0.85rem; font-size: 0.9rem;">Don't have an account? <a href="<?= APP_ENTRY ?>?url=register" style="color: #93c5fd;">Create one</a></p>
            <a href="<?= APP_ENTRY ?>?url=admin/login" class="neo-btn neo-btn-warning" style="margin-top: 0.65rem; width: 100%;">Admin Login</a>
        </div>
    </div>
</section>