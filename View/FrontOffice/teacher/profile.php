<?php
/**
 * APPOLIOS - Teacher Profile Page
 */

$teacherSidebarActive = 'profile';
?>

<div class="dashboard">
    <div class="container admin-dashboard-container" style="max-width: 1400px; width: 100%;">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main" style="background: transparent; padding: 1rem 0 2rem 0;">
        <div class="dashboard-header">
            <h1>My Profile</h1>
            <p>Manage your account information</p>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px;">
            <!-- Profile Card -->
            <div class="table-container">
                <div style="padding: 40px; text-align: center;">
                    <div style="position: relative; width: 120px; height: 120px; margin: 0 auto 20px;">
                        <?php if (!empty($user['avatar'])): ?>
                            <img src="<?= APP_URL ?>/uploads/avatars/<?= htmlspecialchars($user['avatar']) ?>"
                                 alt="<?= htmlspecialchars($user['name']) ?>"
                                 style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid var(--yellow);">
                        <?php else: ?>
                            <div style="width: 120px; height: 120px; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 4px solid var(--yellow);">
                                <svg viewBox="0 0 24 24" width="60" height="60" fill="white">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                </svg>
                            </div>
                        <?php endif; ?>

                        <!-- Upload Button -->
                        <form id="avatarUploadForm" style="position: absolute; bottom: 0; right: 0;" enctype="multipart/form-data">
                            <input type="file" id="avatarInput" name="avatar" accept="image/*" style="display: none;" onchange="uploadAvatar()">
                            <label for="avatarInput" style="cursor: pointer; background: var(--yellow); color: var(--primary-color); width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.2);" title="Change profile picture">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor">
                                    <path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/>
                                </svg>
                            </label>
                        </form>
                    </div>
                    <h2 style="font-size: 1.5rem;"><?= htmlspecialchars($user['name']) ?></h2>
                    <p style="color: var(--gray-dark); font-size: 0.9rem;"><?= htmlspecialchars($user['email']) ?></p>
                    <p style="margin-top: 15px;">
                        <span style="padding: 6px 16px; background: var(--yellow); color: var(--primary-color); border-radius: 20px; font-size: 0.85rem; font-weight: 600;">
                            TEACHER
                        </span>
                    </p>

                </div>
            </div>

            <!-- Account Info -->
            <div class="table-container">
                <div class="table-header">
                    <h3 style="margin: 0;">Account Information</h3>
                </div>
                <div style="padding: 30px;">
                    <div style="margin-bottom: 25px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--primary-color);">Full Name</label>
                        <div style="padding: 12px 16px; background: var(--gray-light); border-radius: var(--border-radius-sm);">
                            <?= htmlspecialchars($user['name']) ?>
                        </div>
                    </div>

                    <div style="margin-bottom: 25px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--primary-color);">Email Address</label>
                        <div style="padding: 12px 16px; background: var(--gray-light); border-radius: var(--border-radius-sm);">
                            <?= htmlspecialchars($user['email']) ?>
                        </div>
                    </div>

                    <div style="margin-bottom: 25px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--primary-color);">Account Type</label>
                        <div style="padding: 12px 16px; background: var(--gray-light); border-radius: var(--border-radius-sm);">
                            Teacher (Courses can be created by you)
                        </div>
                    </div>

                    <div style="margin-bottom: 25px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--primary-color);">Member Since</label>
                        <div style="padding: 12px 16px; background: var(--gray-light); border-radius: var(--border-radius-sm);">
                            <?= date('F d, Y', strtotime($user['created_at'])) ?>
                        </div>
                    </div>

                    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid var(--gray); display: flex; gap: 15px;">
                        <a href="<?= APP_ENTRY ?>?url=teacher/edit-profile" class="btn btn-primary">Edit Profile</a>
                        <a href="<?= APP_ENTRY ?>?url=logout" class="btn btn-outline" style="color: #dc3545; border-color: #dc3545;">
                            Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Face ID Section -->
        <div style="grid-column: 1 / -1; margin-top: 10px;">
            <div class="table-container">
                <div class="table-header">
                    <h3 style="margin: 0;">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor" style="vertical-align: middle; margin-right: 8px;">
                            <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/>
                        </svg>
                        Face ID
                    </h3>
                </div>
                <div style="padding: 30px;">
                    <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 20px;">
                        <div style="width: 60px; height: 60px; background: <?= !empty($user['face_descriptor']) ? 'var(--success-color)' : 'var(--gray)' ?>; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <svg viewBox="0 0 24 24" width="30" height="30" fill="white">
                                <?php if (!empty($user['face_descriptor'])): ?>
                                    <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/>
                                <?php else: ?>
                                    <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/>
                                <?php endif; ?>
                            </svg>
                        </div>
                        <div>
                            <h4 style="margin: 0 0 5px 0;">
                                <?= !empty($user['face_descriptor']) ? 'Face ID is Enabled' : 'Face ID is Not Set' ?>
                            </h4>
                            <p style="margin: 0; color: var(--gray-dark); font-size: 0.9rem;">
                                <?= !empty($user['face_descriptor']) ? 'You can use your face to login securely.' : 'Add Face ID for quick and secure login.' ?>
                            </p>
                        </div>
                    </div>

                    <div style="display: flex; gap: 10px;">
                        <button type="button" class="btn btn-primary" onclick="openFaceIdModal()">
                            <?= !empty($user['face_descriptor']) ? 'Update Face ID' : 'Add Face ID' ?>
                        </button>
                        <?php if (!empty($user['face_descriptor'])): ?>
                            <button type="button" class="btn btn-outline" onclick="removeFaceId()" style="color: #dc3545; border-color: #dc3545;">
                                Remove Face ID
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
            </div>
        </div>
    </div>
</div>

<!-- Face ID Modal -->
<div id="faceid-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: var(--border-radius-lg); padding: 30px; max-width: 500px; width: 90%; text-align: center;">
        <h3 style="margin: 0 0 20px 0;">Face ID Setup</h3>
        <p style="margin-bottom: 20px; color: var(--gray-dark);">Position your face in the center of the camera and click "Capture" when ready.</p>

        <div style="position: relative; width: 300px; height: 300px; margin: 0 auto 20px; background: #f5f5f5; border-radius: var(--border-radius-lg); overflow: hidden;">
            <video id="faceid-video" width="300" height="300" autoplay playsinline style="width: 100%; height: 100%; object-fit: cover;"></video>
            <canvas id="faceid-canvas" width="300" height="300" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></canvas>
            <div id="faceid-ring" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 200px; height: 200px; border: 4px solid rgba(84,140,168,.5); border-radius: 50%; pointer-events: none; transition: all 0.3s;"></div>
        </div>

        <div id="faceid-status" style="margin-bottom: 20px; padding: 10px; border-radius: var(--border-radius-sm); background: var(--gray-light);">
            Initializing camera...
        </div>

        <div style="display: flex; gap: 10px; justify-content: center;">
            <button type="button" id="faceid-capture-btn" class="btn btn-primary" onclick="captureFaceId()" disabled>
                Capture Face
            </button>
            <button type="button" class="btn btn-outline" onclick="closeFaceIdModal()">
                Cancel
            </button>
        </div>

        <input type="hidden" id="faceid-descriptor" value="">
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>

<script>
function uploadAvatar() {
    const fileInput = document.getElementById('avatarInput');
    const file = fileInput.files[0];

    if (!file) return;

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

    fetch('<?= APP_ENTRY ?>?url=teacher/upload-avatar', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'Failed to upload avatar');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Failed to upload avatar');
    });
}

// Face ID Functions
const MODELS = 'https://cdn.jsdelivr.net/gh/justadudewhohacks/face-api.js@0.22.2/weights';
let stream = null, loaded = false, faceReady = false, currentDescriptor = null;

async function openFaceIdModal() {
    document.getElementById('faceid-modal').style.display = 'flex';
    document.getElementById('faceid-status').textContent = 'Loading face recognition models...';
    document.getElementById('faceid-capture-btn').disabled = true;

    try {
        console.log('Loading models from:', MODELS);
        await faceapi.nets.tinyFaceDetector.loadFromUri(MODELS);
        console.log('TinyFaceDetector loaded');
        await faceapi.nets.faceLandmark68Net.loadFromUri(MODELS);
        console.log('FaceLandmark68Net loaded');
        await faceapi.nets.faceRecognitionNet.loadFromUri(MODELS);
        console.log('FaceRecognitionNet loaded');
        loaded = true;
        document.getElementById('faceid-status').textContent = 'Starting camera...';

        const v = document.getElementById('faceid-video');
        stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user', width: 300, height: 300 } });
        v.srcObject = stream;

        v.onloadedmetadata = () => {
            v.play();
            console.log('Camera started, starting detection loop');
            setTimeout(detectLoop, 500);
        };
    } catch (e) {
        console.error('Error loading Face ID:', e);
        document.getElementById('faceid-status').textContent = 'Error: ' + e.message;
    }
}

function closeFaceIdModal() {
    document.getElementById('faceid-modal').style.display = 'none';
    if (stream) {
        stream.getTracks().forEach(t => t.stop());
        stream = null;
    }
    loaded = false;
    faceReady = false;
    currentDescriptor = null;
}

async function detectLoop() {
    if (!loaded) return;
    const v = document.getElementById('faceid-video');
    const c = document.getElementById('faceid-canvas');
    const ctx = c.getContext('2d');
    const ring = document.getElementById('faceid-ring');

    const opts = new faceapi.TinyFaceDetectorOptions({ inputSize: 224, scoreThreshold: 0.5 });

    async function frame() {
        if (!loaded) return;
        
        if (v.readyState < 2 || v.paused || v.ended) {
            requestAnimationFrame(frame);
            return;
        }

        if (v.videoWidth === 0 || v.videoHeight === 0) {
            requestAnimationFrame(frame);
            return;
        }

        try {
            const r = await faceapi.detectSingleFace(v, opts).withFaceLandmarks().withFaceDescriptor();
            if (!r) {
                faceReady = false;
                ring.style.borderColor = 'rgba(84,140,168,.5)';
                document.getElementById('faceid-status').textContent = 'No face detected — look at the camera...';
                document.getElementById('faceid-capture-btn').disabled = true;
            } else {
                const dims = faceapi.matchDimensions(c, { width: v.videoWidth, height: v.videoHeight }, true);
                faceapi.draw.drawDetections(c, faceapi.resizeResults(r, dims));
                faceapi.draw.drawFaceLandmarks(c, faceapi.resizeResults(r, dims));

                const box = r.detection.box;
                const cx = box.x + box.width / 2;
                const cy = box.y + box.height / 2;
                const vc = v.videoWidth / 2, vy = v.videoHeight / 2;
                const dist = Math.hypot(cx - vc, cy - vy);

                if (dist < 50 && r.detection.score > 0.9) {
                    ring.style.borderColor = 'rgba(40,167,69,0.9)';
                    faceReady = true;
                    currentDescriptor = Array.from(r.descriptor);
                    document.getElementById('faceid-status').textContent = 'Perfect! Click "Capture Face" to save.';
                    document.getElementById('faceid-capture-btn').disabled = false;
                } else {
                    ring.style.borderColor = 'rgba(255,193,7,0.9)';
                    faceReady = false;
                    document.getElementById('faceid-status').textContent = 'Center your face and move closer...';
                    document.getElementById('faceid-capture-btn').disabled = true;
                }
            }
        } catch (e) {
            console.error('Detection error:', e);
        }
        requestAnimationFrame(frame);
    }
    frame();
}

async function captureFaceId() {
    if (!currentDescriptor) return;

    document.getElementById('faceid-status').textContent = 'Checking face uniqueness...';
    document.getElementById('faceid-capture-btn').disabled = true;

    try {
        // Check if face is already registered to another user
        const checkResponse = await fetch('<?= APP_ENTRY ?>?url=teacher/check-face-unique', {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' }
        });
        const checkData = await checkResponse.json();

        if (checkData.success && checkData.users.length > 0) {
            // Compare with existing faces
            const currentFace = new Float32Array(currentDescriptor);
            const THRESHOLD = 0.6; // Euclidean distance threshold for face matching

            for (const user of checkData.users) {
                if (user.face_descriptor) {
                    try {
                        const existingDescriptor = JSON.parse(user.face_descriptor);
                        const existingFace = new Float32Array(existingDescriptor);
                        
                        // Calculate Euclidean distance
                        let distance = 0;
                        for (let i = 0; i < currentFace.length; i++) {
                            const diff = currentFace[i] - existingFace[i];
                            distance += diff * diff;
                        }
                        distance = Math.sqrt(distance);

                        if (distance < THRESHOLD) {
                            alert('This face is already registered to another account (' + user.email + '). Each face can only be associated with one account.');
                            document.getElementById('faceid-status').textContent = 'Face already registered - use a different face';
                            document.getElementById('faceid-capture-btn').disabled = false;
                            return;
                        }
                    } catch (e) {
                        console.error('Error comparing face with user', user.id, e);
                    }
                }
            }
        }

        // Face is unique, proceed to save
        document.getElementById('faceid-status').textContent = 'Saving Face ID...';

        const response = await fetch('<?= APP_ENTRY ?>?url=teacher/update-face-id', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ face_descriptor: JSON.stringify(currentDescriptor) })
        });

        const data = await response.json();
        if (data.success) {
            alert('Face ID saved successfully!');
            closeFaceIdModal();
            location.reload();
        } else {
            alert('Error: ' + (data.error || 'Failed to save Face ID'));
            document.getElementById('faceid-status').textContent = 'Error saving Face ID';
        }
    } catch (e) {
        alert('Error saving Face ID: ' + e.message);
        document.getElementById('faceid-status').textContent = 'Error: ' + e.message;
    }
}

async function removeFaceId() {
    if (!confirm('Are you sure you want to remove your Face ID?')) return;

    try {
        const response = await fetch('<?= APP_ENTRY ?>?url=teacher/remove-face-id', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' }
        });

        const data = await response.json();
        if (data.success) {
            alert('Face ID removed successfully!');
            location.reload();
        } else {
            alert('Error: ' + (data.error || 'Failed to remove Face ID'));
        }
    } catch (e) {
        alert('Error removing Face ID: ' + e.message);
    }
}
</script>
