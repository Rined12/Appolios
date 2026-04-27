<?php
/**
 * APPOLIOS - Admin Login Page (with Face ID)
 */
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<div
    style="padding-top:120px;padding-bottom:80px;min-height:100vh;background:linear-gradient(135deg,var(--primary-color) 0%,var(--secondary-color) 100%);">
    <div class="container">
        <div class="form-container" style="max-width:440px;margin:0 auto;">
            <div style="margin-bottom:20px;">
                <a href="<?= APP_ENTRY ?>?url=home/index"
                    style="display:inline-flex;align-items:center;gap:6px;color:#64748b;text-decoration:none;font-size:.9rem;font-weight:600;transition:color .2s;"
                    onmouseover="this.style.color='#2B4865'" onmouseout="this.style.color='#64748b'">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12" />
                        <polyline points="12 19 5 12 12 5" />
                    </svg>
                    Back to Home
                </a>
            </div>
            <div style="text-align:center;margin-bottom:30px;">
                <div
                    style="width:64px;height:64px;background:linear-gradient(135deg,#2B4865,#548CA8);border-radius:18px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;box-shadow:0 8px 24px rgba(43,72,101,.3);">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                    </svg>
                </div>
                <h2 style="color:var(--primary-color);margin:0 0 6px;font-size:1.7rem;font-weight:800;">Administrator
                    Portal</h2>
                <p style="color:var(--gray-dark);margin:0;font-size:.95rem;">Sign in with credentials or Face ID</p>
            </div>

            <?php if ($flash): ?>
                <div
                    style="padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:.9rem;<?= $flash['type'] === 'error' ? 'background:#f8d7da;color:#721c24;border:1px solid #f5c6cb;' : 'background:#d4edda;color:#155724;border:1px solid #c3e6cb;' ?>">
                    <?= htmlspecialchars($flash['message']) ?>
                </div>
            <?php endif; ?>

            <form action="<?= APP_ENTRY ?>?url=authenticate" method="POST">
                <input type="hidden" name="admin_login" value="1">
                <div class="form-group"><label for="email">Admin Email</label><input type="email" id="email"
                        name="email" placeholder="Enter admin email" required></div>
                <div class="form-group"><label for="password">Password</label><input type="password" id="password"
                        name="password" placeholder="Enter password" required></div>
                <button type="submit" class="btn btn-yellow btn-block" style="margin-bottom:12px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:6px;">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                    </svg>
                    Administrator Sign In
                </button>
            </form>

            <div style="display:flex;align-items:center;gap:12px;margin:16px 0;">
                <div style="flex:1;height:1px;background:#e2e8f0;"></div>
                <span style="color:#94a3b8;font-size:.85rem;font-weight:600;">OR</span>
                <div style="flex:1;height:1px;background:#e2e8f0;"></div>
            </div>

            <button id="face-id-login-btn" type="button" onclick="openFaceLoginModal()"
                style="width:100%;display:flex;align-items:center;justify-content:center;gap:10px;padding:13px 20px;border:2px solid #2B4865;border-radius:10px;background:white;color:#2B4865;font-size:1rem;font-weight:700;cursor:pointer;transition:all .25s;box-shadow:0 2px 8px rgba(43,72,101,.08);"
                onmouseover="this.style.background='#2B4865';this.style.color='white';"
                onmouseout="this.style.background='white';this.style.color='#2B4865';">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                    <circle cx="12" cy="12" r="3" />
                </svg>
                Login with Face ID
            </button>

            <p class="form-text" style="margin-top:20px;text-align:center;">
                <a href="<?= APP_ENTRY ?>?url=login" style="color:var(--secondary-color);">← Back to Student Login</a>
            </p>
        </div>
    </div>
</div>

<!-- Face ID Login Modal -->
<div id="face-login-modal"
    style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(10,15,30,.85);backdrop-filter:blur(6px);align-items:center;justify-content:center;">
    <div
        style="background:white;border-radius:24px;padding:2rem;max-width:480px;width:95%;text-align:center;box-shadow:0 24px 80px rgba(0,0,0,.4);position:relative;animation:faceSlideUp .3s ease;">
        <button onclick="closeFaceLoginModal()"
            style="position:absolute;top:16px;right:16px;background:#f1f5f9;border:none;width:36px;height:36px;border-radius:50%;cursor:pointer;font-size:1.1rem;color:#64748b;display:flex;align-items:center;justify-content:center;">✕</button>
        <div
            style="width:56px;height:56px;background:linear-gradient(135deg,#2B4865,#548CA8);border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                <circle cx="12" cy="12" r="3" />
            </svg>
        </div>
        <h3 style="margin:0 0 6px;color:#1e293b;font-size:1.3rem;font-weight:800;">Face ID Login</h3>
        <p id="flm-status-text" style="margin:0 0 20px;color:#64748b;font-size:.9rem;">Loading face recognition models…
        </p>
        <div
            style="position:relative;display:inline-block;border-radius:16px;overflow:hidden;box-shadow:0 8px 30px rgba(0,0,0,.15);">
            <video id="flm-video" autoplay muted playsinline width="380" height="285"
                style="display:block;border-radius:16px;background:#0f172a;"></video>
            <canvas id="flm-canvas" width="380" height="285"
                style="position:absolute;top:0;left:0;border-radius:16px;pointer-events:none;"></canvas>
            <div id="flm-ring"
                style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:160px;height:160px;border-radius:50%;border:3px solid rgba(84,140,168,.5);animation:faceRingPulse 2s ease-in-out infinite;pointer-events:none;">
            </div>
        </div>
        <div
            style="margin:16px auto 0;max-width:340px;height:6px;background:#e2e8f0;border-radius:3px;overflow:hidden;">
            <div id="flm-progress"
                style="height:100%;width:0%;background:linear-gradient(90deg,#2B4865,#548CA8);border-radius:3px;transition:width .4s ease;">
            </div>
        </div>
        <div id="flm-error"
            style="display:none;margin-top:12px;padding:10px 14px;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;color:#b91c1c;font-size:.88rem;">
        </div>
        <button onclick="closeFaceLoginModal()"
            style="margin-top:20px;padding:10px 28px;border:1px solid #e2e8f0;border-radius:8px;background:white;color:#64748b;cursor:pointer;font-size:.9rem;font-weight:600;"
            onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">Cancel</button>
    </div>
</div>

<style>
    @keyframes faceSlideUp {
        from {
            opacity: 0;
            transform: translateY(30px) scale(.97)
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1)
        }
    }

    @keyframes faceRingPulse {

        0%,
        100% {
            transform: translate(-50%, -50%) scale(.95);
            opacity: .5
        }

        50% {
            transform: translate(-50%, -50%) scale(1.05);
            opacity: 1
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script>
    (function () {
        const MODELS = 'https://cdn.jsdelivr.net/gh/justadudewhohacks/face-api.js@0.22.2/weights';
        const LOGIN_URL = '<?= APP_ENTRY ?>?url=auth/face-login-admin';
        let stream = null, loop = null, loaded = false, detecting = false;

        function el(id) { return document.getElementById(id); }
        function setStatus(t, p) { el('flm-status-text').textContent = t; if (p !== undefined) el('flm-progress').style.width = p + '%'; }
        function showErr(m) { el('flm-error').style.display = 'block'; el('flm-error').textContent = m; }
        function hideErr() { el('flm-error').style.display = 'none'; }

        async function loadModels() {
            if (loaded) return;
            await Promise.all([
                faceapi.nets.tinyFaceDetector.loadFromUri(MODELS),
                faceapi.nets.faceLandmark68TinyNet.loadFromUri(MODELS),
                faceapi.nets.faceRecognitionNet.loadFromUri(MODELS)
            ]);
            loaded = true;
        }

        window.openFaceLoginModal = async function () {
            el('face-login-modal').style.display = 'flex';
            hideErr();
            setStatus('Loading face recognition models…', 5);
            try {
                await loadModels();
                setStatus('Starting camera…', 20);
                stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user', width: 380, height: 285 } });
                const v = el('flm-video');
                v.srcObject = stream;
                await v.play();
                setStatus('Position your face in the ring…', 40);
                startLoop();
            } catch (e) { setStatus('Error', 0); showErr('Error: ' + e.message); }
        };

        function startLoop() {
            detecting = false;
            const v = el('flm-video'), c = el('flm-canvas'), ctx = c.getContext('2d');
            let hits = 0;
            const opts = new faceapi.TinyFaceDetectorOptions({ inputSize: 320, scoreThreshold: .5 });
            loop = setInterval(async () => {
                if (detecting || v.readyState < 2) return;
                detecting = true;
                ctx.clearRect(0, 0, c.width, c.height);
                try {
                    const r = await faceapi.detectSingleFace(v, opts).withFaceLandmarks(true).withFaceDescriptor();
                    if (!r) { hits = 0; setStatus('No face detected — look at the camera…', 50); el('flm-ring').style.borderColor = 'rgba(84,140,168,.5)'; }
                    else {
                        const dims = faceapi.matchDimensions(c, v, true);
                        faceapi.draw.drawDetections(c, faceapi.resizeResults(r, dims));
                        el('flm-ring').style.borderColor = 'rgba(34,197,94,.8)';
                        hits++;
                        setStatus('Face detected! Verifying…', Math.min(40 + hits * 20, 90));
                        if (hits >= 3) { clearInterval(loop); loop = null; setStatus('Authenticating…', 95); await doLogin(Array.from(r.descriptor)); }
                    }
                } catch (e) { }
                detecting = false;
            }, 700);
        }

        async function doLogin(descriptor) {
            hideErr();
            try {
                const res = await fetch(LOGIN_URL, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ descriptor }) });
                const d = await res.json();
                if (d.success) {
                    setStatus('✓ Identity confirmed! Redirecting…', 100);
                    el('flm-ring').style.borderColor = 'rgba(34,197,94,1)';
                    stopCam();
                    setTimeout(() => { window.location.href = d.redirect; }, 800);
                } else {
                    setStatus('Face not recognized.', 0);
                    showErr(d.message || 'Face not recognized. Please try again.');
                    el('flm-ring').style.borderColor = 'rgba(239,68,68,.7)';
                    setTimeout(() => { hideErr(); setStatus('Try again — position your face…', 40); el('flm-ring').style.borderColor = 'rgba(84,140,168,.5)'; startLoop(); }, 2500);
                }
            } catch (e) { setStatus('Network error', 0); showErr('Network error: ' + e.message); }
        }

        function stopCam() {
            if (loop) { clearInterval(loop); loop = null; }
            if (stream) { stream.getTracks().forEach(t => t.stop()); stream = null; }
            const v = el('flm-video'); if (v) v.srcObject = null;
            detecting = false;
        }

        window.closeFaceLoginModal = function () {
            stopCam();
            el('face-login-modal').style.display = 'none';
            hideErr();
            setStatus('Loading face recognition models…', 0);
            el('flm-progress').style.width = '0%';
            el('flm-ring').style.borderColor = 'rgba(84,140,168,.5)';
        };
    })();
</script>