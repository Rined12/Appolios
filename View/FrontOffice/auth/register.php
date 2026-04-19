<?php
/**
 * APPOLIOS - Registration Page (Premium Neo Design)
 */

// Get old input if available
$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);

// Get flash messages
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// Get errors
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);
?>

<section class="neo-auth-wrap">
    <div class="neo-glass-card neo-auth-grid">
        <aside class="neo-auth-info">
            <h2>Create Your Learning Profile</h2>
            <p class="neo-muted" style="margin-top: 0.5rem;">Join APPOLIOS to unlock courses, projects, and career-focused paths.</p>
            <div class="neo-badges" style="margin-top: 1rem;">
                <span class="neo-badge primary">Premium UI</span>
                <span class="neo-badge success">Certificates</span>
                <span class="neo-badge warning">Skill Levels</span>
            </div>
        </aside>

        <div class="neo-auth-form">
            <h2>Register</h2>
            <p class="neo-muted" style="margin-top: 0.45rem;">Create your account and start your first track.</p>

            <?php if ($flash): ?>
                <div class="neo-alert <?= $flash['type'] === 'error' ? 'error' : 'success' ?>">
                    <?= htmlspecialchars($flash['message']) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="neo-alert error">
                    <?php foreach ($errors as $error): ?>
                        <div>• <?= htmlspecialchars($error) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="<?= APP_ENTRY ?>?url=signup" method="POST">
                <div class="neo-field">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" placeholder="Your full name" value="<?= htmlspecialchars($old['name'] ?? '') ?>" required>
                </div>

                <div class="neo-field">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="you@example.com" value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>
                </div>

                <div class="neo-field">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Minimum 6 characters" required>
                </div>

                <div class="neo-field">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm password" required>
                </div>

                <div class="neo-field">
                    <label>Register as</label>
                    <div style="display: flex; gap: 0.9rem; color: #cbd5e1; font-size: 0.92rem; margin-top: 0.32rem;">
                        <label><input type="radio" name="role" value="student" checked> Student</label>
                        <label><input type="radio" name="role" value="teacher"> Teacher</label>
                    </div>
                    <div class="neo-muted" style="font-size: 0.82rem; margin-top: 0.35rem;">Teacher accounts require admin validation.</div>
                </div>

                <button type="submit" class="neo-btn neo-btn-primary" style="margin-top: 0.95rem; width: 100%;">Create Account</button>
            </form>

            <p class="neo-muted" style="margin-top: 0.85rem; font-size: 0.9rem;">Already have an account? <a href="<?= APP_ENTRY ?>?url=login" style="color: #93c5fd;">Sign in</a></p>
        </div>
    </div>
</section>