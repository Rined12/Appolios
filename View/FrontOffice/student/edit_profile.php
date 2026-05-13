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

                    <!-- Face ID Settings -->
                    <div style="margin-top: 40px; padding-top: 30px; border-top: 1px solid #e2e8f0;">
                        <h4 style="color: #2B4865; font-size: 1.1rem; margin-bottom: 10px; font-weight: 700;">Face ID Login</h4>
                        <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 20px;">Set up Face ID to login securely without your password.</p>
                        
                        <div style="display: flex; flex-direction: column; max-width: 400px; gap: 15px;">
                            <div id="face-id-status-msg" style="padding: 10px; border-radius: 8px; display: none; font-size: 0.9rem; font-weight: 600;"></div>
                            
                            <div id="face-video-container" style="display: none; position: relative; width: 100%; aspect-ratio: 4/3; border-radius: 12px; overflow: hidden; background: #000; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
                                <video id="face-video" autoplay muted playsinline style="width: 100%; height: 100%; object-fit: cover;"></video>
                                <canvas id="face-canvas" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none;"></canvas>
                                <div id="face-ring" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 60%; aspect-ratio: 1/1; border: 3px dashed rgba(255,255,255,0.4); border-radius: 50%; transition: border-color 0.3s; pointer-events: none;"></div>
                            </div>

                            <button type="button" id="setup-face-btn" class="btn btn-outline" style="border-color: #2B4865; color: #2B4865; width: max-content;">
                                Setup Face ID
                            </button>
                            <button type="button" id="cancel-face-btn" class="btn btn-outline" style="display: none; border-color: #ef4444; color: #ef4444; width: max-content;">
                                Cancel Setup
                            </button>
                        </div>
                    </div>

                    <!-- Advanced Settings / Account Deletion -->
                    <div style="margin-top: 40px; padding-top: 30px; border-top: 1px solid #e2e8f0;">
                        <h4 style="color: #ef4444; font-size: 1.1rem; margin-bottom: 10px; font-weight: 700;">Danger Zone</h4>
                        <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 20px;">Deleting your account cannot be undone. All your progress will be lost permanently.</p>
                        <form action="<?= APP_ENTRY ?>?url=student/delete-account" method="POST" onsubmit="return confirm('Are you absolutely sure you want to permanently delete your account? This action cannot be reversed.');">
                            <button type="submit" style="padding: 10px 20px; background: #fff1f2; color: #e11d48; border: 1px solid #fecdd3; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#ffe4e6'" onmouseout="this.style.background='#fff1f2'">
                                Delete My Account
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const setupBtn = document.getElementById('setup-face-btn');
    const cancelBtn = document.getElementById('cancel-face-btn');
    const container = document.getElementById('face-video-container');
    const msg = document.getElementById('face-id-status-msg');
    
    let stream = null, loop = null, modelsLoaded = false, detecting = false;

    function showMsg(text, type = 'info') {
        msg.style.display = 'block';
        msg.textContent = text;
        msg.style.backgroundColor = type === 'error' ? '#fee2e2' : (type === 'success' ? '#dcfce7' : '#f1f5f9');
        msg.style.color = type === 'error' ? '#dc2626' : (type === 'success' ? '#16a34a' : '#1e293b');
    }

    async function loadModels() {
        if (modelsLoaded) return;
        showMsg('Loading AI models...', 'info');
        const MODEL_URL = 'https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights';
        await Promise.all([
            faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
            faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
            faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL)
        ]);
        modelsLoaded = true;
    }

    function stopCam() {
        if (loop) clearInterval(loop);
        if (stream) stream.getTracks().forEach(t => t.stop());
        const v = document.getElementById('face-video');
        if (v) v.srcObject = null;
        loop = null; stream = null; detecting = false;
        container.style.display = 'none';
        setupBtn.style.display = 'block';
        cancelBtn.style.display = 'none';
    }

    if (setupBtn) {
        setupBtn.addEventListener('click', async () => {
            setupBtn.style.display = 'none';
            cancelBtn.style.display = 'block';
            container.style.display = 'block';
            showMsg('Initializing camera...', 'info');
            try {
                await loadModels();
                stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user', width: 360, height: 270 } });
                const v = document.getElementById('face-video');
                v.srcObject = stream;
                await v.play();
                showMsg('Position your face in the circle...', 'info');
                startDetection();
            } catch(e) {
                showMsg('Camera error: ' + e.message, 'error');
                stopCam();
            }
        });
    }

    if (cancelBtn) {
        cancelBtn.addEventListener('click', () => {
            stopCam();
            msg.style.display = 'none';
        });
    }

    function startDetection() {
        const v = document.getElementById('face-video');
        const c = document.getElementById('face-canvas');
        const ring = document.getElementById('face-ring');
        const ctx = c.getContext('2d');
        let hits = 0;
        const opts = new faceapi.TinyFaceDetectorOptions({ inputSize: 320, scoreThreshold: 0.5 });
        
        loop = setInterval(async () => {
            if (detecting || v.readyState < 2) return;
            detecting = true;
            ctx.clearRect(0, 0, c.width, c.height);
            try {
                const r = await faceapi.detectSingleFace(v, opts).withFaceLandmarks(true).withFaceDescriptor();
                if (!r) {
                    hits = 0;
                    ring.style.borderColor = 'rgba(255,255,255,0.4)';
                    showMsg('No face detected, look at the camera.', 'info');
                } else {
                    const dims = faceapi.matchDimensions(c, v, true);
                    faceapi.draw.drawDetections(c, faceapi.resizeResults(r, dims));
                    ring.style.borderColor = '#16a34a';
                    hits++;
                    showMsg('Scanning... Keep still (' + hits + '/3)', 'info');
                    if (hits >= 3) {
                        clearInterval(loop);
                        loop = null;
                        showMsg('Face captured! Saving...', 'info');
                        await saveFace(Array.from(r.descriptor));
                    }
                }
            } catch(e) { }
            detecting = false;
        }, 800);
    }

    async function saveFace(descriptor) {
        try {
            const res = await fetch('<?= APP_ENTRY ?>?url=auth/save-face-descriptor', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ descriptor })
            });
            const data = await res.json();
            if (data.success) {
                showMsg(data.message, 'success');
            } else {
                showMsg(data.message || 'Failed to save', 'error');
            }
        } catch(e) {
            showMsg('Network error: ' + e.message, 'error');
        }
        stopCam();
        setTimeout(() => msg.style.display = 'none', 5000);
    }
});

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
