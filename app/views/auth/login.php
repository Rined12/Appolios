<?php
/**
 * APPOLIOS - Login Page
 */

// Get flash messages
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>

<div style="padding-top: 120px; padding-bottom: 80px; min-height: 100vh; background-color: var(--gray-light);">
    <div class="container">
        <div class="form-container">
            <div style="text-align: center; margin-bottom: 30px;">
                <h2 style="color: var(--primary-color);">Welcome Back</h2>
                <p style="color: var(--gray-dark);">Sign in to your APPOLIOS account</p>
            </div>

            <?php if ($flash): ?>
                <div style="padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem; <?= $flash['type'] === 'error' ? 'background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;' : 'background: #d4edda; color: #155724; border: 1px solid #c3e6cb;' ?>">
                    <?= htmlspecialchars($flash['message']) ?>
                </div>
            <?php endif; ?>

            <form action="<?= APP_URL ?>/index.php?url=authenticate" method="POST">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>

                <div class="form-group">
                    <label>I am a:</label>
                    <div style="display: flex; gap: 20px; margin-top: 10px;">
                        <label style="display: flex; align-items: center; cursor: pointer; font-weight: normal;">
                            <input type="radio" name="user_type" value="student" checked style="margin-right: 8px; width: auto;">
                            Student
                        </label>
                        <label style="display: flex; align-items: center; cursor: pointer; font-weight: normal;">
                            <input type="radio" name="user_type" value="teacher" style="margin-right: 8px; width: auto;">
                            Teacher
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Sign In</button>
            </form>

            <p class="form-text">
                Don't have an account? <a href="<?= APP_URL ?>/index.php?url=register">Sign Up</a>
            </p>

            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--gray); text-align: center;">
                <p style="font-size: 0.9rem; color: var(--gray-dark); margin-bottom: 10px;">Administrator?</p>
                <a href="<?= APP_URL ?>/index.php?url=admin/login" class="btn btn-yellow" style="padding: 10px 20px;">Admin Login</a>
            </div>
        </div>
    </div>
</div>