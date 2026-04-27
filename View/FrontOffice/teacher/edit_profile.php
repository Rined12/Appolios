<?php
/**
 * APPOLIOS - Teacher Edit Profile Page
 */

$teacherSidebarActive = 'profile';
?>

<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main">
                <div class="dashboard-header">
                    <h1>Edit Profile</h1>
                    <p>Update your account information</p>
                </div>

                <?php if (isset($_SESSION['flash'])): ?>
                    <div class="alert alert-<?= $_SESSION['flash']['type'] === 'error' ? 'danger' : 'success' ?>" style="margin-bottom: 20px; padding: 12px 20px; border-radius: 8px; background: <?= $_SESSION['flash']['type'] === 'error' ? 'rgba(220, 53, 69, 0.1)' : 'rgba(25, 135, 84, 0.1)' ?>; border: 1px solid <?= $_SESSION['flash']['type'] === 'error' ? 'rgba(220, 53, 69, 0.3)' : 'rgba(25, 135, 84, 0.3)' ?>; color: <?= $_SESSION['flash']['type'] === 'error' ? '#dc3545' : '#198754' ?>;">
                        <?= htmlspecialchars($_SESSION['flash']['message']) ?>
                    </div>
                    <?php unset($_SESSION['flash']); ?>
                <?php endif; ?>

                <form action="<?= APP_ENTRY ?>?url=teacher/update-profile" method="POST" enctype="multipart/form-data">
                <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px; max-width: 1000px;">
                    
                    <!-- Colonne Gauche : Widget Image de Profil -->
                    <div class="table-container" style="align-self: start;">
                        <div style="padding: 40px; text-align: center;">
                            <div style="position: relative; width: 160px; height: 160px; margin: 0 auto 20px;">
                                <!-- Image avec bordure bleue -->
                                <?php
                                $avatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode($user['name'] ?? 'T') . '&background=e2e8f0&color=475569&size=160';
                                if (!empty($user['avatar'])) {
                                    // Use absolute URL for avatar
                                    $avatarUrl = APP_URL . '/uploads/avatars/' . $user['avatar'] . '?' . time();
                                }
                                ?>
                                <img id="avatar-img" src="<?= $avatarUrl ?>" alt="Profile" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover; border: 4px solid #007bff; padding: 3px; background: white; box-sizing: border-box;">
                                
                                <!-- Bouton Upload superposé -->
                                <label for="avatarInput" style="position: absolute; bottom: 0; right: 10px; background: #007bff; color: white; width: 42px; height: 42px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; border: 3px solid white; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: all 0.2s;">
                                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="17 8 12 3 7 8"></polyline>
                                        <line x1="12" y1="3" x2="12" y2="15"></line>
                                    </svg>
                                </label>
                                <input type="file" id="avatarInput" name="avatar" accept="image/*" style="display: none;" onchange="uploadAvatar()">
                            </div>
                            
                            <h2 style="font-size: 1.6rem; font-weight: 700; margin-bottom: 5px; color: #1e293b;"><?= htmlspecialchars($user['name'] ?? '') ?></h2>
                            <p style="color: #64748b; font-size: 0.95rem; margin-bottom: 25px;"><?= htmlspecialchars($user['email'] ?? '') ?></p>
                            
                            <span style="display: inline-block; padding: 8px 30px; background: #10b981; color: white; border-radius: 20px; font-weight: 600; font-size: 0.9rem; letter-spacing: 0.5px;">
                                Teacher
                            </span>
                        </div>
                    </div>

                    <!-- Colonne Droite : Formulaire -->
                    <div class="table-container">
                        <div class="table-header">
                            <h3 style="margin: 0;">Edit Your Information</h3>
                        </div>
                        <div style="padding: 30px;">
                                <div style="margin-bottom: 25px;">
                                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--primary-color);">Full Name *</label>
                                    <input type="text" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required style="width: 100%; padding: 12px 16px; border: 2px solid var(--gray); border-radius: var(--border-radius-sm); font-size: 1rem; box-sizing: border-box;">
                                </div>

                                <div style="margin-bottom: 25px;">
                                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--primary-color);">Email Address *</label>
                                    <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required style="width: 100%; padding: 12px 16px; border: 2px solid var(--gray); border-radius: var(--border-radius-sm); font-size: 1rem; box-sizing: border-box;">
                                </div>

                                <div style="margin-bottom: 25px;">
                                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--primary-color);">Account Type</label>
                                    <div style="padding: 12px 16px; background: var(--gray-light); border-radius: var(--border-radius-sm); color: var(--gray-dark);">
                                        Teacher (cannot be changed)
                                    </div>
                                </div>

                                <hr style="margin: 30px 0; border: none; border-top: 1px solid var(--gray);">

                                <h4 style="margin-bottom: 20px; color: var(--primary-color);">Change Password (optional)</h4>

                                <div style="margin-bottom: 20px;">
                                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--primary-color);">Current Password</label>
                                    <input type="password" name="current_password" placeholder="Enter current password to change password" style="width: 100%; padding: 12px 16px; border: 2px solid var(--gray); border-radius: var(--border-radius-sm); font-size: 1rem; box-sizing: border-box;">
                                </div>

                                <div style="margin-bottom: 20px;">
                                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--primary-color);">New Password</label>
                                    <input type="password" name="new_password" placeholder="Enter new password (min 6 characters)" style="width: 100%; padding: 12px 16px; border: 2px solid var(--gray); border-radius: var(--border-radius-sm); font-size: 1rem; box-sizing: border-box;">
                                </div>

                                <div style="margin-bottom: 30px;">
                                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--primary-color);">Confirm New Password</label>
                                    <input type="password" name="confirm_password" placeholder="Confirm new password" style="width: 100%; padding: 12px 16px; border: 2px solid var(--gray); border-radius: var(--border-radius-sm); font-size: 1rem; box-sizing: border-box;">
                                </div>

                                <div style="display: flex; gap: 15px;">
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                    <a href="<?= APP_ENTRY ?>?url=teacher/profile" class="btn btn-outline">Cancel</a>
                                </div>
                        </div>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .dashboard input[type="text"],
    .dashboard input[type="email"],
    .dashboard input[type="password"] {
        background-color: #f8fafc !important; /* Gris très clair et épuré */
        border: 1px solid #e2e8f0 !important;
        transition: all 0.3s ease !important;
        color: #334155 !important;
    }

    .dashboard input[type="text"]:focus,
    .dashboard input[type="email"]:focus,
    .dashboard input[type="password"]:focus {
        background-color: #ffffff !important;
        border-color: #5594d6 !important; /* Bleu professionnel de votre thème */
        box-shadow: 0 0 0 3px rgba(85, 148, 214, 0.15) !important;
        outline: none !important;
    }
    
    .dashboard input:hover {
        background-color: #f1f5f9 !important;
    }
</style>

<script>
function uploadAvatar() {
    const fileInput = document.getElementById('avatarInput');
    const file = fileInput.files[0];

    if (!file) {
        alert('No file selected');
        return;
    }

    // Check file size (max 10MB)
    if (file.size > 10 * 1024 * 1024) {
        alert('File size must be less than 10MB');
        return;
    }

    // Check file type
    if (!file.type.startsWith('image/')) {
        alert('Please select an image file');
        return;
    }

    const formData = new FormData();
    formData.append('avatar', file);
    
    const uploadUrl = '<?= APP_ENTRY ?>?url=teacher/upload-avatar';
    
    fetch(uploadUrl, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update avatar image without reload
            const avatarImg = document.getElementById('avatar-img');
            const baseUrl = '<?= APP_URL ?>';
            avatarImg.src = baseUrl + '/uploads/avatars/' + data.avatar + '?' + Date.now();
        } else {
            alert('Error: ' + (data.error || 'Failed to upload avatar'));
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert('Failed to upload avatar');
    });
}
</script>
