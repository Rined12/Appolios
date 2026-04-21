<?php
/**
 * APPOLIOS - Login Page (Premium Neo Design)
 */

// Flash message is passed from controller via $data['flash']
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

            <form action="<?= APP_ENTRY ?>?url=authenticate" method="POST">
                <div class="neo-field">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="you@example.com" required>
                </div>

                <div class="neo-field">
                    <label for="password">Password</label>
                    <div style="position: relative;">
                        <input type="password" id="password" name="password" placeholder="Enter your password" required style="padding-right: 50px; width: 100%;">
                        <button type="button" onclick="togglePassword('password', 'eye-password')" id="eye-password" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; padding: 8px; color: #64748b; transition: color 0.2s;" onmouseover="this.style.color='#2B4865'" onmouseout="this.style.color='#64748b'">
                            <!-- Eye icon -->
                            <svg id="eye-icon-password" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <!-- Eye-off icon (hidden by default) -->
                            <svg id="eye-off-icon-password" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                <line x1="1" y1="1" x2="23" y2="23"></line>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="neo-btn neo-btn-primary" style="margin-top: 0.95rem; width: 100%;">Sign In</button>
            </form>

            <p class="neo-muted" style="margin-top: 0.85rem; font-size: 0.9rem;">Don't have an account? <a href="<?= APP_ENTRY ?>?url=register" style="color: #93c5fd;">Create one</a></p>
        </div>

        <script>
            function togglePassword(inputId, buttonId) {
                const input = document.getElementById(inputId);
                const eyeIcon = document.getElementById('eye-icon-' + inputId);
                const eyeOffIcon = document.getElementById('eye-off-icon-' + inputId);

                if (input.type === 'password') {
                    input.type = 'text';
                    eyeIcon.style.display = 'none';
                    eyeOffIcon.style.display = 'block';
                } else {
                    input.type = 'password';
                    eyeIcon.style.display = 'block';
                    eyeOffIcon.style.display = 'none';
                }
            }
        </script>
    </div>
</section>