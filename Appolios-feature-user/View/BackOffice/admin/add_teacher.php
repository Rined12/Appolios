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
            <div style="display: flex; gap: 10px;">
                <a href="javascript:history.back()" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; background: #6c757d;">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" style="transform: rotate(180deg);">
                        <path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/>
                    </svg>
                    Back
                </a>
            </div>
        </div>

        <div class="form-container" style="max-width: 500px;">
            <form action="<?= APP_ENTRY ?>?url=admin/store-teacher" method="POST" novalidate>
                <div class="form-group">
                    <label for="name">Full Name *</label>
                    <input type="text" id="name" name="name" placeholder="Enter teacher's full name" value="<?= htmlspecialchars($old['name'] ?? '') ?>" style="<?= isset($errors['name']) ? 'border-color: #ef4444; background: #fef2f2;' : '' ?>">
                    <?php if (isset($errors['name'])): ?>
                        <div style="color: #ef4444; font-size: 0.85rem; font-weight: 600; margin-top: 4px; display: flex; align-items: center; gap: 6px;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                            <?= htmlspecialchars($errors['name']) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" placeholder="Enter teacher's email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" style="<?= isset($errors['email']) ? 'border-color: #ef4444; background: #fef2f2;' : '' ?>">
                    <?php if (isset($errors['email'])): ?>
                        <div style="color: #ef4444; font-size: 0.85rem; font-weight: 600; margin-top: 4px; display: flex; align-items: center; gap: 6px;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                            <?= htmlspecialchars($errors['email']) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="password">Password *</label>
                    <input type="password" id="password" name="password" placeholder="Create a password (min 6 characters)" style="<?= isset($errors['password']) ? 'border-color: #ef4444; background: #fef2f2;' : '' ?>">
                    <?php if (isset($errors['password'])): ?>
                        <div style="color: #ef4444; font-size: 0.85rem; font-weight: 600; margin-top: 4px; display: flex; align-items: center; gap: 6px;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                            <?= htmlspecialchars($errors['password']) ?>
                        </div>
                    <?php else: ?>
                        <small style="color: var(--gray-dark); font-size: 0.85rem;">Minimum 6 characters</small>
                    <?php endif; ?>
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
