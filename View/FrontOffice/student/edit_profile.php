<?php
/**
 * APPOLIOS - Edit Profile Page
 */

$studentSidebarActive = 'profile';
?>
<style>
.student-edit-profile-page.student-events-page .admin-main {
    background: transparent;
    padding: 1rem 0 2rem 0;
}
.student-edit-profile-page .student-edit-profile-header h1 {
    font-size: 1.75rem;
    font-weight: 800;
    color: #1e293b;
    margin: 0 0 0.35rem 0;
}
.student-edit-profile-page .student-edit-profile-header p {
    color: #64748b;
    font-size: 0.95rem;
    margin: 0 0 1.25rem 0;
}
.student-edit-profile-page .student-edit-profile-grid {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 1.75rem;
    max-width: 1000px;
}
@media (max-width: 991px) {
    .student-edit-profile-page .student-edit-profile-grid { grid-template-columns: 1fr; }
}
.student-edit-profile-page .student-edit-profile-card.table-container {
    margin-top: 0;
    border-radius: 16px;
    border: 1px solid #eef2f6;
    box-shadow: 0 4px 18px rgba(15, 23, 42, 0.06);
    overflow: hidden;
    align-self: start;
}
.student-edit-profile-page .student-edit-profile-card .table-header {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 1rem 1.25rem;
}
.student-edit-profile-page .student-edit-profile-card .table-header h3 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 800;
    color: #1e293b;
}
.student-edit-profile-page .student-edit-profile-name {
    font-size: 1.35rem;
    font-weight: 800;
    color: #1e293b;
    margin: 0.35rem 0 0.25rem;
}
.student-edit-profile-page .student-edit-profile-email {
    color: #64748b;
    font-size: 0.92rem;
    margin: 0 0 1rem;
}
.student-edit-profile-page .student-edit-profile-role {
    display: inline-block;
    padding: 0.35rem 1rem;
    background: #10b981;
    color: #fff;
    border-radius: 999px;
    font-size: 0.82rem;
    font-weight: 700;
}
.student-edit-profile-page .student-edit-profile-avatar-wrap {
    position: relative;
    width: 160px;
    height: 160px;
    margin: 0 auto 1rem;
}
.student-edit-profile-page .student-edit-profile-avatar-wrap img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #0ea5e9;
    padding: 3px;
    background: #fff;
    box-sizing: border-box;
}
.student-edit-profile-page .student-edit-profile-upload-btn {
    position: absolute;
    bottom: 0;
    right: 10px;
    background: #0ea5e9;
    color: #fff;
    width: 42px;
    height: 42px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    border: 3px solid #fff;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.12);
}
.student-edit-profile-page .student-edit-profile-field-label {
    display: block;
    margin-bottom: 0.45rem;
    font-weight: 600;
    font-size: 0.88rem;
    color: #1e3a5f;
}
.student-edit-profile-page .student-edit-profile-input,
.student-edit-profile-page .student-edit-profile-readonly {
    width: 100%;
    padding: 12px 16px;
    border-radius: 10px;
    font-size: 1rem;
    box-sizing: border-box;
}
.student-edit-profile-page .student-edit-profile-input {
    border: 1px solid #e2e8f0;
    background: #fff;
    color: #334155;
}
.student-edit-profile-page .student-edit-profile-input:focus {
    outline: none;
    border-color: #0ea5e9;
    box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.2);
}
.student-edit-profile-page .student-edit-profile-readonly {
    background: #f1f5f9;
    border: 1px solid transparent;
    color: #475569;
}
.student-edit-profile-page .student-edit-profile-section-title {
    margin-bottom: 1rem;
    color: #1e3a5f;
    font-size: 1rem;
    font-weight: 700;
}
.student-edit-profile-page .student-edit-profile-flash {
    margin-bottom: 1.25rem;
    padding: 12px 18px;
    border-radius: 10px;
    font-size: 0.92rem;
}
.student-edit-profile-page .student-edit-profile-flash--ok {
    background: rgba(16, 185, 129, 0.1);
    border: 1px solid rgba(16, 185, 129, 0.35);
    color: #047857;
}
.student-edit-profile-page .student-edit-profile-flash--err {
    background: rgba(220, 53, 69, 0.08);
    border: 1px solid rgba(220, 53, 69, 0.35);
    color: #b91c1c;
}
.student-edit-profile-page .student-edit-profile-hr {
    margin: 1.75rem 0;
    border: none;
    border-top: 1px solid #e2e8f0;
}
.student-edit-profile-page .student-edit-profile-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
}
</style>

<div class="dashboard student-events-page student-edit-profile-page">
    <div class="container admin-dashboard-container" style="max-width: 1400px; width: 100%;">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>

            <div class="admin-main">
                <div class="dashboard-header student-edit-profile-header">
                    <h1>Edit Profile</h1>
                    <p>Update your account information</p>
                </div>

        <?php if (isset($_SESSION['flash'])): ?>
            <div class="student-edit-profile-flash student-edit-profile-flash--<?= $_SESSION['flash']['type'] === 'error' ? 'err' : 'ok' ?>">
                <?= htmlspecialchars($_SESSION['flash']['message']) ?>
            </div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>

        <div class="student-edit-profile-grid">
            <!-- Colonne Gauche : Widget Image de Profil -->
            <div class="table-container student-edit-profile-card">
                <div style="padding: 40px; text-align: center;">
                    <div class="student-edit-profile-avatar-wrap">
                        <?php
                        $avatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode($user['name'] ?? 'S') . '&background=e2e8f0&color=475569&size=160';
                        if (!empty($user['avatar'])) {
                            $avatarUrl = APP_URL . '/uploads/avatars/' . $user['avatar'] . '?' . time();
                        }
                        ?>
                        <img id="avatar-img" src="<?= $avatarUrl ?>" alt="Profile">
                        
                        <label for="avatarInput" class="student-edit-profile-upload-btn">
                            <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="17 8 12 3 7 8"></polyline>
                                <line x1="12" y1="3" x2="12" y2="15"></line>
                            </svg>
                        </label>
                        <input type="file" id="avatarInput" name="avatar" accept="image/*" style="display: none;" onchange="uploadAvatar()">
                    </div>
                    
                    <h2 class="student-edit-profile-name"><?= htmlspecialchars($user['name'] ?? '') ?></h2>
                    <p class="student-edit-profile-email"><?= htmlspecialchars($user['email'] ?? '') ?></p>
                    
                    <span class="student-edit-profile-role"><?= ucfirst(htmlspecialchars($user['role'] ?? 'student')) ?></span>
                </div>
            </div>

            <!-- Colonne Droite : Formulaire -->
            <div class="table-container student-edit-profile-card">
                <div class="table-header">
                    <h3>Edit Your Information</h3>
                </div>
                <div style="padding: 30px;">
                    <form action="<?= APP_ENTRY ?>?url=student/update-profile" method="POST">
                        <div style="margin-bottom: 25px;">
                            <label class="student-edit-profile-field-label">Full Name *</label>
                            <input type="text" name="name" class="student-edit-profile-input" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
                        </div>

                        <div style="margin-bottom: 25px;">
                            <label class="student-edit-profile-field-label">Email Address *</label>
                            <input type="email" name="email" class="student-edit-profile-input" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                        </div>

                        <div style="margin-bottom: 25px;">
                            <label class="student-edit-profile-field-label">Account Type</label>
                            <div class="student-edit-profile-readonly">
                                <?= ucfirst(htmlspecialchars($user['role'] ?? 'student')) ?> (cannot be changed)
                            </div>
                        </div>

                        <hr class="student-edit-profile-hr">

                        <h4 class="student-edit-profile-section-title">Change Password (optional)</h4>

                        <div style="margin-bottom: 20px;">
                            <label class="student-edit-profile-field-label">Current Password</label>
                            <input type="password" name="current_password" class="student-edit-profile-input" placeholder="Enter current password to change password">
                        </div>

                        <div style="margin-bottom: 20px;">
                            <label class="student-edit-profile-field-label">New Password</label>
                            <input type="password" name="new_password" class="student-edit-profile-input" placeholder="Enter new password (min 6 characters)">
                        </div>

                        <div style="margin-bottom: 30px;">
                            <label class="student-edit-profile-field-label">Confirm New Password</label>
                            <input type="password" name="confirm_password" class="student-edit-profile-input" placeholder="Confirm new password">
                        </div>

                        <div class="student-edit-profile-actions">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                            <a href="<?= APP_ENTRY ?>?url=student/profile" class="btn btn-outline">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
            </div>
        </div>
    </div>
</div>

<script>
function uploadAvatar() {
    const fileInput = document.getElementById('avatarInput');
    const file = fileInput.files[0];

    if (!file) return;

    if (file.size > 10 * 1024 * 1024) {
        alert('File size must be less than 10MB');
        return;
    }

    if (!file.type.startsWith('image/')) {
        alert('Please select an image file');
        return;
    }

    const formData = new FormData();
    formData.append('avatar', file);
    
    const uploadUrl = '<?= APP_ENTRY ?>?url=student/upload-avatar';
    
    fetch(uploadUrl, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
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
