<?php
/**
 * APPOLIOS - Student Profile Page
 */

$studentSidebarActive = 'profile';
?>

<div class="dashboard student-profile-page">
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
                            <img id="profile-avatar-img" src="<?= APP_URL ?>/uploads/avatars/<?= htmlspecialchars($user['avatar']) ?>"
                                 alt="<?= htmlspecialchars($user['name']) ?>"
                                 style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid var(--primary-color);">
                        <?php else: ?>
                            <div id="profile-avatar-img" style="width: 120px; height: 120px; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 4px solid var(--primary-color);">
                                <svg viewBox="0 0 24 24" width="60" height="60" fill="white">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                </svg>
                            </div>
                        <?php endif; ?>

                        <!-- Upload Button -->
                        <form id="avatarUploadForm" style="position: absolute; bottom: 0; right: 0;" enctype="multipart/form-data">
                            <input type="file" id="avatarInput" name="avatar" accept="image/*" style="display: none;" onchange="uploadAvatar()">
                            <label for="avatarInput" style="cursor: pointer; background: var(--primary-color); color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.2);" title="Change profile picture">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor">
                                    <path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/>
                                </svg>
                            </label>
                        </form>
                    </div>
                    <h2 style="font-size: 1.5rem;"><?= htmlspecialchars($user['name']) ?></h2>
                    <p style="color: var(--gray-dark); font-size: 0.9rem;"><?= htmlspecialchars($user['email']) ?></p>
                    <p style="margin-top: 15px;">
                        <span style="padding: 6px 16px; background: var(--secondary-color); color: white; border-radius: 20px; font-size: 0.85rem;">
                            <?= ucfirst(htmlspecialchars($user['role'])) ?>
                        </span>
                    </p>

                    <!-- Generate Avatar Button -->
                    <div style="margin-top: 20px;">
                        <button type="button" class="btn btn-primary" onclick="openAvatarGenerator()" style="display: inline-flex; align-items: center; gap: 8px; font-size: 0.9rem;">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                            Generate Avatar from Photo
                        </button>
                    </div>
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
                            <?= ucfirst(htmlspecialchars($user['role'])) ?>
                        </div>
                    </div>

                    <div style="margin-bottom: 25px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--primary-color);">Member Since</label>
                        <div style="padding: 12px 16px; background: var(--gray-light); border-radius: var(--border-radius-sm);">
                            <?= date('F d, Y', strtotime($user['created_at'])) ?>
                        </div>
                    </div>

                    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid var(--gray); display: flex; gap: 15px;">
                        <a href="<?= APP_ENTRY ?>?url=student/edit-profile" class="btn btn-primary">Edit Profile</a>
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

<!-- Avatar Generator Modal -->
<div id="avatar-generator-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 9998; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: var(--border-radius-lg); padding: 30px; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto;">
        <h3 style="margin: 0 0 10px 0;">Generate Your Avatar</h3>
        <p style="margin-bottom: 20px; color: var(--gray-dark); font-size: 0.9rem;">Upload a photo of your face and we'll create a cartoon avatar for you.</p>

        <!-- Step 1: Upload -->
        <div id="avatar-step-upload">
            <div style="border: 2px dashed var(--primary-color); border-radius: var(--border-radius-lg); padding: 40px; text-align: center; cursor: pointer; transition: all 0.3s;"
                 ondragover="event.preventDefault(); this.style.background='#f0f0f0'"
                 ondragleave="this.style.background='transparent'"
                 ondrop="event.preventDefault(); this.style.background='transparent'; handleAvatarDrop(event)">
                <input type="file" id="avatar-gen-input" accept="image/*" style="display: none;" onchange="handleAvatarSelect()">
                <label for="avatar-gen-input" style="cursor: pointer; display: block;">
                    <svg viewBox="0 0 24 24" width="48" height="48" fill="var(--primary-color)" style="margin: 0 auto 15px;">
                        <path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96zM14 13v4h-4v-4H7l5-5 5 5h-3z"/>
                    </svg>
                    <strong>Click to upload</strong> or drag and drop
                    <div style="font-size: 0.85rem; color: var(--gray-dark); margin-top: 8px;">JPG, PNG or WEBP (max 10MB)</div>
                </label>
            </div>
            <div style="display: flex; gap: 10px; justify-content: center; margin-top: 20px;">
                <button type="button" class="btn btn-outline" onclick="closeAvatarGenerator()">Cancel</button>
            </div>
        </div>

        <!-- Step 2: Preview & Generate -->
        <div id="avatar-step-preview" style="display: none;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <h4 style="margin: 0 0 10px 0; font-size: 0.95rem;">Your Photo</h4>
                    <img id="avatar-gen-preview" src="" alt="Your photo" style="width: 100%; border-radius: var(--border-radius-lg); border: 2px solid var(--gray);">
                </div>
                <div>
                    <h4 style="margin: 0 0 10px 0; font-size: 0.95rem;">Generated Avatar</h4>
                    <div id="avatar-gen-result" style="width: 100%; aspect-ratio: 1; background: #f5f5f5; border-radius: var(--border-radius-lg); display: flex; align-items: center; justify-content: center;">
                        <span style="color: var(--gray-dark); font-size: 0.85rem;">Avatar will appear here</span>
                    </div>
                </div>
            </div>

            <div id="avatar-gen-status" style="padding: 10px; border-radius: var(--border-radius-sm); background: var(--gray-light); text-align: center; margin-bottom: 15px;">
                Analyzing your photo...
            </div>

            <div style="display: flex; gap: 10px; justify-content: center;">
                <button type="button" class="btn btn-primary" id="avatar-generate-btn" onclick="generateAvatar()">
                    Generate Avatar
                </button>
                <button type="button" class="btn btn-outline" onclick="resetAvatarGenerator()">Try Another Photo</button>
                <button type="button" class="btn btn-outline" onclick="closeAvatarGenerator()">Cancel</button>
            </div>
        </div>
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

    fetch('<?= APP_ENTRY ?>?url=student/upload-avatar', {
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
        const checkResponse = await fetch('<?= APP_ENTRY ?>?url=student/check-face-unique', {
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

        const response = await fetch('<?= APP_ENTRY ?>?url=student/update-face-id', {
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
        const response = await fetch('<?= APP_ENTRY ?>?url=student/remove-face-id', {
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

// Avatar Generator Functions
let uploadedFile = null;
let detectedFaceData = null;
let currentImageUrl = null;

function openAvatarGenerator() {
    document.getElementById('avatar-generator-modal').style.display = 'flex';
    resetAvatarGenerator();
}

function closeAvatarGenerator() {
    document.getElementById('avatar-generator-modal').style.display = 'none';
}

function resetAvatarGenerator() {
    document.getElementById('avatar-step-upload').style.display = 'block';
    document.getElementById('avatar-step-preview').style.display = 'none';
    document.getElementById('avatar-gen-input').value = '';
    document.getElementById('avatar-generate-btn').disabled = true;
    document.getElementById('avatar-gen-result').innerHTML = '<span style="color: var(--gray-dark); font-size: 0.85rem;">Avatar will appear here</span>';
    uploadedFile = null;
    detectedFaceData = null;
    if (currentImageUrl) {
        URL.revokeObjectURL(currentImageUrl);
        currentImageUrl = null;
    }
}

function handleAvatarDrop(e) {
    const file = e.dataTransfer.files[0];
    if (file && file.type.startsWith('image/')) {
        processAvatarFile(file);
    }
}

function handleAvatarSelect() {
    const fileInput = document.getElementById('avatar-gen-input');
    if (fileInput.files.length > 0) {
        processAvatarFile(fileInput.files[0]);
    }
}

function processAvatarFile(file) {
    uploadedFile = file;
    currentImageUrl = URL.createObjectURL(file);
    
    document.getElementById('avatar-step-upload').style.display = 'none';
    document.getElementById('avatar-step-preview').style.display = 'block';
    document.getElementById('avatar-gen-preview').src = currentImageUrl;
    
    analyzeFace(currentImageUrl);
}

async function analyzeFace(imageUrl) {
    const statusEl = document.getElementById('avatar-gen-status');
    statusEl.textContent = 'Loading face recognition models...';
    statusEl.style.background = 'var(--gray-light)';

    try {
        const MODELS = 'https://cdn.jsdelivr.net/gh/justadudewhohacks/face-api.js@0.22.2/weights';

        // Load models
        await faceapi.nets.tinyFaceDetector.loadFromUri(MODELS);
        await faceapi.nets.faceLandmark68Net.loadFromUri(MODELS);
        await faceapi.nets.faceRecognitionNet.loadFromUri(MODELS);
        await faceapi.nets.ageGenderNet.loadFromUri(MODELS);

        statusEl.textContent = 'Analyzing your photo...';

        // Load image using HTML Image element
        const img = new Image();
        img.src = imageUrl;
        await new Promise((resolve, reject) => {
            img.onload = resolve;
            img.onerror = reject;
        });

        // Detect face with landmarks and gender
        const detection = await faceapi.detectSingleFace(img, new faceapi.TinyFaceDetectorOptions())
            .withFaceLandmarks()
            .withAgeAndGender();

        if (!detection) {
            statusEl.textContent = 'No face detected. Please upload a photo with a visible face.';
            statusEl.style.background = '#ffebee';
            document.getElementById('avatar-generate-btn').disabled = true;
            return;
        }

        // Extract face data for avatar generation
        const landmarks = detection.landmarks;
        const jawline = landmarks.getJawOutline();
        const nose = landmarks.getNose();
        const mouth = landmarks.getMouth();
        const leftEye = landmarks.getLeftEye();
        const rightEye = landmarks.getRightEye();

        // Calculate face proportions
        const jawWidth = jawline[16].x - jawline[0].x;
        const jawHeight = jawline[8].y - jawline[0].y;
        const faceShape = jawWidth > jawHeight * 1.2 ? 'square' : jawWidth < jawHeight * 0.9 ? 'long' : 'oval';

        // Estimate skin tone from cheek area
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = img.width;
        canvas.height = img.height;
        ctx.drawImage(img, 0, 0);

        // Get cheek pixel (approximate)
        const cheekX = Math.floor(jawline[2].x + 10);
        const cheekY = Math.floor(jawline[2].y - 10);
        const pixelData = ctx.getImageData(cheekX, cheekY, 1, 1).data;
        
        // Convert to hex
        const rgbToHex = (r, g, b) => '#' + [r, g, b].map(x => {
            const hex = x.toString(16);
            return hex.length === 1 ? '0' + hex : hex;
        }).join('');
        
        const skinTone = rgbToHex(pixelData[0], pixelData[1], pixelData[2]);

        // Estimate hair color (sample from top of bounding box)
        const box = detection.detection.box;
        const hairX = Math.floor(box.x + box.width / 2);
        const hairY = Math.floor(box.y + 15); // Look slightly inside the top of the box
        
        const hairPixel = ctx.getImageData(hairX, hairY, 1, 1).data;
        const hairColor = rgbToHex(hairPixel[0], hairPixel[1], hairPixel[2]);

        detectedFaceData = {
            skinTone: skinTone,
            hairColor: hairColor,
            faceShape: faceShape,
            gender: detection.gender || 'male', // pass gender
            eyeColor: '#6B4425' // Default
        };

        statusEl.textContent = 'Analysis complete! Ready to generate.';
        statusEl.style.background = '#e8f5e9';
        document.getElementById('avatar-generate-btn').disabled = false;

    } catch (error) {
        console.error('Face analysis error:', error);
        statusEl.textContent = 'Error analyzing photo. You can still try generating.';
        statusEl.style.background = '#ffebee';
        // Allow generating with defaults
        document.getElementById('avatar-generate-btn').disabled = false;
        detectedFaceData = {}; 
    }
}

function generateAvatar() {
    const statusEl = document.getElementById('avatar-gen-status');
    const resultContainer = document.getElementById('avatar-gen-result');
    const btn = document.getElementById('avatar-generate-btn');
    
    btn.disabled = true;
    statusEl.textContent = 'Generating cartoon avatar...';
    statusEl.style.background = 'var(--gray-light)';
    resultContainer.innerHTML = '<div class="spinner" style="width: 30px; height: 30px; border: 3px solid #f3f3f3; border-top: 3px solid var(--primary-color); border-radius: 50%; animation: spin 1s linear infinite;"></div>';

    const formData = new FormData();
    formData.append('faceImage', uploadedFile);
    
    if (detectedFaceData) {
        formData.append('faceData', JSON.stringify(detectedFaceData));
    }

    fetch('<?= APP_ENTRY ?>?url=student/generate-avatar', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        // Prevent generic JSON parsing error message if not json
        const contentType = response.headers.get("content-type");
        if (contentType && contentType.indexOf("application/json") !== -1) {
            return response.json();
        } else {
            throw new Error("Oops, we haven't got JSON!");
        }
    })
    .then(data => {
        if (data.success) {
            statusEl.textContent = 'Avatar generated successfully!';
            statusEl.style.background = '#e8f5e9';
            
            // Display result
            resultContainer.innerHTML = `<img src="${data.url}" alt="Generated Avatar" style="width: 100%; height: 100%; object-fit: cover; border-radius: var(--border-radius-lg);">`;
            
            // Update profile image
            const profileImg = document.getElementById('profile-avatar-img');
            if (profileImg) {
                // Replace with actual img tag if it was a div (no avatar set before)
                if (profileImg.tagName === 'DIV') {
                    const img = document.createElement('img');
                    img.id = 'profile-avatar-img';
                    img.src = data.url;
                    img.alt = 'Avatar';
                    img.style.cssText = 'width:120px;height:120px;border-radius:50%;object-fit:cover;border:4px solid var(--primary-color);';
                    profileImg.replaceWith(img);
                } else {
                    profileImg.src = data.url;
                }
            }
            
            // Update nav image if exists
            const navImg = document.querySelector('.nav-user-img');
            if (navImg) navImg.src = data.url;
            
            // Close after 3 seconds
            setTimeout(() => {
                closeAvatarGenerator();
            }, 3000);
        } else {
            throw new Error(data.error || 'Failed to generate avatar');
        }
    })
    .catch(error => {
        console.error('Generation error:', error);
        statusEl.textContent = 'Error: ' + error.message;
        statusEl.style.background = '#ffebee';
        resultContainer.innerHTML = '<span style="color: #dc3545;">Generation failed</span>';
        btn.disabled = false;
    });
}

function rgbToHex(r, g, b) {
    return '#' + [r, g, b].map(x => {
        const hex = x.toString(16);
        return hex.length === 1 ? '0' + hex : hex;
    }).join('');
}
</script>