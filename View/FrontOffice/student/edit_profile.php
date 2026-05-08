<?php
/**
 * APPOLIOS - Edit Profile Page
 */

$studentSidebarActive = 'profile';
?>

<div class="dashboard student-edit-profile-page">
    <div class="container admin-dashboard-container" style="max-width: 1400px; width: 100%;">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>

            <div class="admin-main" style="background: transparent; padding: 1rem 0 2rem 0;">
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

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px; max-width: 1000px;">
            <!-- Colonne Gauche : Widget Image de Profil -->
            <div class="table-container" style="align-self: start;">
                <div style="padding: 40px; text-align: center;">
                    <div style="position: relative; width: 160px; height: 160px; margin: 0 auto 20px;">
                        <?php
                        $avatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode($user['name'] ?? 'S') . '&background=e2e8f0&color=475569&size=160';
                        if (!empty($user['avatar'])) {
                            $avatarUrl = APP_URL . '/uploads/avatars/' . $user['avatar'] . '?' . time();
                        }
                        ?>
                        <img id="avatar-img" src="<?= $avatarUrl ?>" alt="Profile" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover; border: 4px solid #007bff; padding: 3px; background: white; box-sizing: border-box;">
                        
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
                        Student
                    </span>
                </div>
            </div>

            <!-- Colonne Droite : Formulaire -->
            <div class="table-container">
                <div class="table-header">
                    <h3 style="margin: 0;">Edit Your Information</h3>
                </div>
                <div style="padding: 30px;">
                    <form action="<?= APP_ENTRY ?>?url=student/update-profile" method="POST">
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
                                <?= ucfirst(htmlspecialchars($user['role'] ?? 'student')) ?> (cannot be changed)
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
