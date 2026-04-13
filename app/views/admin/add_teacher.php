<?php
/**
 * APPOLIOS - Admin Add Teacher Page
 */

$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);
?>

<div class="dashboard">
    <div class="container">
        <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1>Add New Teacher</h1>
                <p>Create a teacher account (only admin can create teachers)</p>
            </div>
            <a href="<?= APP_URL ?>/index.php?url=admin/teachers" class="btn btn-outline" style="padding: 10px 20px;">← Back to Teachers</a>
        </div>

        <div class="form-container" style="max-width: 500px;">
            <form action="<?= APP_URL ?>/index.php?url=admin/store-teacher" method="POST">
                <div class="form-group">
                    <label for="name">Full Name *</label>
                    <input type="text" id="name" name="name" placeholder="Enter teacher's full name" value="<?= htmlspecialchars($old['name'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" placeholder="Enter teacher's email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="password">Password *</label>
                    <input type="password" id="password" name="password" placeholder="Create a password (min 6 characters)" required>
                    <small style="color: var(--gray-dark); font-size: 0.85rem;">Minimum 6 characters</small>
                </div>

                <div style="background: var(--yellow); padding: 15px; border-radius: var(--border-radius-sm); margin-bottom: 20px;">
                    <p style="margin: 0; font-size: 0.9rem; color: var(--primary-color);">
                        <strong>⚠️ Important:</strong> Teacher accounts can create and manage their own courses. Only admins can create teacher accounts.
                    </p>
                </div>

                <button type="submit" class="btn btn-yellow btn-block" style="margin-top: 20px;">Create Teacher Account</button>
            </form>
        </div>
    </div>
</div>
