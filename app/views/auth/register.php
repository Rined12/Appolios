<?php
/**
 * APPOLIOS - Registration Page
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

<div style="padding-top: 120px; padding-bottom: 80px; min-height: 100vh; background-color: var(--gray-light);">
    <div class="container">
        <div class="form-container">
            <div style="text-align: center; margin-bottom: 30px;">
                <h2 style="color: var(--primary-color);">Create Account</h2>
                <p style="color: var(--gray-dark);">Join APPOLIOS and start learning today</p>
                <p style="color: var(--gray-dark); font-size: 0.88rem; text-align: left; margin-top: 12px; line-height: 1.5;">
                    <strong>Rôles :</strong> l’inscription crée un compte <strong>étudiant</strong> (cours, chapitres, quiz à passer, soumission des réponses).
                    Les comptes <strong>enseignant</strong> et <strong>admin</strong> sont créés par l’administrateur (gestion des cours, chapitres, quiz, banque de questions, événements).
                </p>
            </div>

            <?php if ($flash): ?>
                <div style="padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem; <?= $flash['type'] === 'error' ? 'background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;' : 'background: #d4edda; color: #155724; border: 1px solid #c3e6cb;' ?>">
                    <?= htmlspecialchars($flash['message']) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div style="background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px;">
                    <?php foreach ($errors as $error): ?>
                        <p style="margin: 0; font-size: 0.9rem;">• <?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="<?= APP_URL ?>/index.php?url=signup" method="POST">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" placeholder="Enter your full name" value="<?= htmlspecialchars($old['name'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Create a password (min 6 characters)" required>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
                </div>

                <div class="form-group">
                    <label>I want to register as:</label>
                    <div style="display: flex; gap: 20px; margin-top: 10px;">
                        <label style="display: flex; align-items: center; cursor: pointer; font-weight: normal;">
                            <input type="radio" name="role" value="student" checked style="margin-right: 8px; width: auto;">
                            Student
                        </label>
                        <label style="display: flex; align-items: center; cursor: pointer; font-weight: normal;">
                            <input type="radio" name="role" value="teacher" style="margin-right: 8px; width: auto;">
                            Teacher
                        </label>
                    </div>
                    <small style="color: var(--gray-dark); font-size: 0.85rem; display: block; margin-top: 8px;">
                        * Teacher accounts require admin approval
                    </small>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Create Account</button>
            </form>

            <p class="form-text">
                Already have an account? <a href="<?= APP_URL ?>/index.php?url=login">Sign In</a>
            </p>
        </div>
    </div>
</div>