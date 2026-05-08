<?php
/**
 * APPOLIOS - Login Page (Premium Neo Design)
 */

// Flash message is passed from controller via $data['flash']
?>

<section class="neo-auth-wrap neo-login-page">
    <div class="neo-glass-card neo-auth-grid">
        <aside class="neo-auth-info">
            <h2>Welcome Back</h2>
            <p class="neo-muted" style="margin-top: 0.5rem;">Access your dashboard, continue courses, and track your
                achievements.</p>
            <div class="neo-badges" style="margin-top: 1rem;">
                <span class="neo-badge primary">Dark Mode</span>
                <span class="neo-badge success">Progress Tracking</span>
                <span class="neo-badge warning">Gamification</span>
            </div>

            <div class="neo-login-hero-visual">
                <div class="neo-login-hero-circle"></div>
                <img src="<?= APP_URL ?>/View/assets/images/instructor/06.jpg" alt="Student learning"
                    class="neo-login-hero-photo">
            </div>
        </aside>

        <div class="neo-auth-form">
            <!-- Back to Home Button -->
            <a href="<?= APP_ENTRY ?>?url=home/index"
                style="display: inline-flex; align-items: center; gap: 6px; color: #64748b; text-decoration: none; font-size: 0.85rem; font-weight: 600; margin-bottom: 1rem; transition: color 0.2s;"
                onmouseover="this.style.color='#2B4865'" onmouseout="this.style.color='#64748b'">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                    stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Back to Home
            </a>
            <h2>Sign In</h2>
            <p class="neo-muted" style="margin-top: 0.45rem;">Use your APPOLIOS account credentials.</p>


            <form id="loginForm" action="<?= APP_ENTRY ?>?url=authenticate" method="POST"
                onsubmit="return validateRecaptcha()">
                <div class="neo-field">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="you@example.com" required>
                </div>

                <div class="neo-field">
                    <label for="password">Password</label>
                    <div style="position: relative;">
                        <input type="password" id="password" name="password" placeholder="Enter your password" required
                            style="padding-right: 50px; width: 100%;">
                        <button type="button" onclick="togglePassword('password', 'eye-password')" id="eye-password"
                            style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; padding: 8px; color: #64748b; transition: color 0.2s;"
                            onmouseover="this.style.color='#2B4865'" onmouseout="this.style.color='#64748b'">
                            <!-- Eye icon -->
                            <svg id="eye-icon-password" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <!-- Eye-off icon (hidden by default) -->
                            <svg id="eye-off-icon-password" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                style="display: none;">
                                <path
                                    d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24">
                                </path>
                                <line x1="1" y1="1" x2="23" y2="23"></line>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Google reCAPTCHA v2 -->
                <div class="g-recaptcha" data-sitekey="<?= RECAPTCHA_SITE_KEY ?>" style="margin: 1rem 0;"></div>

                <button type="submit" class="neo-btn neo-btn-primary" style="margin-top: 0.95rem; width: 100%;">Sign
                    In</button>
            </form>

            <div
                style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.85rem; font-size: 0.9rem;">
                <p class="neo-muted" style="margin: 0;">Don't have an account? <a href="<?= APP_ENTRY ?>?url=register"
                        style="color: #93c5fd;">Create one</a></p>
                <a href="#" onclick="openForgotModal(); return false;" style="color: #93c5fd; font-weight: 600;">Forgot
                    Password?</a>
            </div>

            <!-- Face ID Divider -->
            <div style="display:flex;align-items:center;gap:12px;margin:18px 0 14px;">
                <div style="flex:1;height:1px;background:rgba(255,255,255,0.15);"></div>
                <span style="color:#94a3b8;font-size:.8rem;font-weight:600;">OR</span>
                <div style="flex:1;height:1px;background:rgba(255,255,255,0.15);"></div>
            </div>

            <!-- Face ID Button -->
            <button type="button" id="login-faceid-btn" onclick="openLoginFaceModal()"
                style="width:100%;display:flex;align-items:center;justify-content:center;gap:10px;padding:12px 20px;border:2px solid rgba(43,72,101,0.5);border-radius:10px;background:rgba(43,72,101,0.08);color:#2B4865;font-size:.95rem;font-weight:700;cursor:pointer;transition:all .25s;"
                onmouseover="this.style.background='rgba(43,72,101,0.18)';"
                onmouseout="this.style.background='rgba(43,72,101,0.08)';">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                    <circle cx="12" cy="12" r="3" />
                </svg>
                Login with Face ID
            </button>

            <!-- Google Login Button -->
            <a href="<?= APP_ENTRY ?>?url=auth/google-login"
                style="width:100%;display:flex;align-items:center;justify-content:center;gap:10px;padding:12px 20px;border:1px solid #e2e8f0;border-radius:10px;background:white;color:#1e293b;font-size:.95rem;font-weight:700;cursor:pointer;transition:all .25s;text-decoration:none;margin-top:12px;box-shadow: 0 1px 3px rgba(0,0,0,0.1);"
                onmouseover="this.style.background='#f8fafc';this.style.borderColor='#cbd5e1';"
                onmouseout="this.style.background='white';this.style.borderColor='#e2e8f0';">
                <svg width="18" height="18" viewBox="0 0 48 48">
                    <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                    <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                    <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24s.92 7.54 2.56 10.78l7.97-6.19z"/>
                    <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                    <path fill="none" d="M0 0h48v48H0z"/>
                </svg>
                Sign in with Google
            </a>
        </div>

        <script>
            function togglePassword(inputId, buttonId) {
                const input = document.getElementById(inputId);
                const eyeIcon = document.getElementById('eye-icon-' + inputId);
                const eyeOffIcon = document.getElementById('eye-off-icon-' + inputId);

                if (input.type === 'password') {
                    input.type = 'text';
                    eyeIcon.style.display = 'none';
                    eyeOffIcon.style.display = 'block';
                } else {
                    input.type = 'password';
                    eyeIcon.style.display = 'block';
                    eyeOffIcon.style.display = 'none';
                }
            }

            function validateRecaptcha() {
                var response = grecaptcha.getResponse();
                if (response.length == 0) {
                    Swal.fire({
                        icon: "warning",
                        title: "reCAPTCHA",
                        text: "Veuillez cocher la case 'Je ne suis pas un robot' avant de continuer.",
                        draggable: true
                    });
                    return false;
                }
                return true;
            }
        </script>
    </div>
</section>

<!-- Google reCAPTCHA v2 Script -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<!-- Forgot Password Modal -->
<div id="forgot-modal"
    style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(10,15,30,.85);backdrop-filter:blur(6px);align-items:center;justify-content:center;">
    <div
        style="background:white;border-radius:24px;padding:2rem;max-width:460px;width:95%;text-align:center;box-shadow:0 24px 80px rgba(0,0,0,.4);position:relative;animation:fpSlideUp .3s ease;">
        <button onclick="closeForgotModal()"
            style="position:absolute;top:16px;right:16px;background:#f1f5f9;border:none;width:36px;height:36px;border-radius:50%;cursor:pointer;font-size:1.1rem;color:#64748b;display:flex;align-items:center;justify-content:center;">✕</button>
        <div
            style="width:56px;height:56px;background:linear-gradient(135deg,#2B4865,#548CA8);border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                <circle cx="12" cy="12" r="3" />
            </svg>
        </div>
        <h3 style="margin:0 0 6px;color:#1e293b;font-size:1.25rem;font-weight:800;">Forgot Password?</h3>
        <p style="margin:0 0 18px;color:#64748b;font-size:.88rem;">Enter your email and we'll send you a verification
            code.</p>

        <?php if (isset($flash)): ?>
            <div class="neo-alert <?= $flash['type'] === 'error' ? 'error' : 'success' ?>" style="margin-bottom: 1rem;">
                <?= htmlspecialchars($flash['message']) ?>
            </div>
        <?php endif; ?>

        <form action="<?= APP_ENTRY ?>?url=request-password-reset" method="POST" style="text-align: left;">
            <div class="neo-field" style="margin-bottom: 1.25rem;">
                <label for="forgot-email"
                    style="display: block; margin-bottom: 6px; font-weight: 600; color: #1e293b;">Email Address</label>
                <input type="email" id="forgot-email" name="email" placeholder="you@example.com" required
                    style="width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem;">
            </div>
            <button type="submit" class="neo-btn neo-btn-primary" style="width: 100%;">Send Verification Code</button>
        </form>

        <p style="margin: 16px 0 0; color: #64748b; font-size: 0.85rem;">
            Remember your password? <a href="#" onclick="closeForgotModal(); return false;"
                style="color: #548CA8; font-weight: 600;">Sign In</a>
        </p>
    </div>
</div>

<!-- Verification Code Modal -->
<div id="verify-code-modal"
    style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(10,15,30,.85);backdrop-filter:blur(6px);align-items:center;justify-content:center;">
    <div
        style="background:white;border-radius:24px;padding:2rem;max-width:460px;width:95%;text-align:center;box-shadow:0 24px 80px rgba(0,0,0,.4);position:relative;animation:fpSlideUp .3s ease;">
        <button onclick="closeVerifyCodeModal()"
            style="position:absolute;top:16px;right:16px;background:#f1f5f9;border:none;width:36px;height:36px;border-radius:50%;cursor:pointer;font-size:1.1rem;color:#64748b;display:flex;align-items:center;justify-content:center;">✕</button>
        <div
            style="width:56px;height:56px;background:linear-gradient(135deg,#2B4865,#548CA8);border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                <circle cx="12" cy="12" r="3" />
            </svg>
        </div>
        <h3 style="margin:0 0 6px;color:#1e293b;font-size:1.25rem;font-weight:800;">Enter Verification Code</h3>
        <p style="margin:0 0 18px;color:#64748b;font-size:.88rem;">Check your email for the 4-digit code.</p>

        <form action="<?= APP_ENTRY ?>?url=verify-reset-code" method="POST" style="text-align: left;">
            <div class="neo-field" style="margin-bottom: 1.25rem;">
                <label for="verify-code"
                    style="display: block; margin-bottom: 6px; font-weight: 600; color: #1e293b;">Verification
                    Code</label>
                <input type="text" id="verify-code" name="code" placeholder="1234" maxlength="4" pattern="[0-9]{4}"
                    required
                    style="width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 1.5rem; text-align: center; letter-spacing: 8px; font-weight: 700;">
            </div>
            <button type="submit" class="neo-btn neo-btn-primary" style="width: 100%;">Verify Code</button>
        </form>

        <p style="margin: 16px 0 0; color: #64748b; font-size: 0.85rem;">
            Didn't receive the code? <a href="#" onclick="closeVerifyCodeModal(); openForgotModal(); return false;"
                style="color: #548CA8; font-weight: 600;">Request new code</a>
        </p>
    </div>
</div>

<style>
    @keyframes vcSlideUp {
        from {
            opacity: 0;
            transform: translateY(28px) scale(.97)
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1)
        }
    }
</style>

<script>
    function openVerifyCodeModal() {
        document.getElementById('verify-code-modal').style.display = 'flex';
    }

    function closeVerifyCodeModal() {
        document.getElementById('verify-code-modal').style.display = 'none';
    }

    // Close modal when clicking outside
    window.onclick = function (event) {
        if (event.target.id === 'verify-code-modal') {
            closeVerifyCodeModal();
        }
    }
</script>

<style>
    @keyframes fpSlideUp {
        from {
            opacity: 0;
            transform: translateY(28px) scale(.97)
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1)
        }
    }
</style>

<script>
    function openForgotModal() {
        document.getElementById('forgot-modal').style.display = 'flex';
    }

    function closeForgotModal() {
        document.getElementById('forgot-modal').style.display = 'none';
    }

    // Close modal when clicking outside
    window.onclick = function (event) {
        if (event.target.id === 'forgot-modal') {
            closeForgotModal();
        }
    }
</script>

<!-- Face ID Login Modal -->
<div id="login-face-modal"
    style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(10,15,30,.85);backdrop-filter:blur(6px);align-items:center;justify-content:center;">
    <div
        style="background:white;border-radius:24px;padding:2rem;max-width:460px;width:95%;text-align:center;box-shadow:0 24px 80px rgba(0,0,0,.4);position:relative;animation:lfSlideUp .3s ease;">
        <button onclick="closeLoginFaceModal()"
            style="position:absolute;top:16px;right:16px;background:#f1f5f9;border:none;width:36px;height:36px;border-radius:50%;cursor:pointer;font-size:1.1rem;color:#64748b;display:flex;align-items:center;justify-content:center;">✕</button>
        <div
            style="width:56px;height:56px;background:linear-gradient(135deg,#2B4865,#548CA8);border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
            </svg>
        </div>
        <h3 style="margin:0 0 6px;color:#1e293b;font-size:1.25rem;font-weight:800;">Face ID Login</h3>
        <p id="lf-status" style="margin:0 0 18px;color:#64748b;font-size:.88rem;">Loading face recognition models…</p>
        <div
            style="position:relative;display:inline-block;border-radius:14px;overflow:hidden;box-shadow:0 8px 30px rgba(0,0,0,.15);">
            <video id="lf-video" autoplay muted playsinline width="360" height="270"
                style="display:block;border-radius:14px;background:#0f172a;"></video>
            <canvas id="lf-canvas" width="360" height="270"
                style="position:absolute;top:0;left:0;pointer-events:none;"></canvas>
            <div id="lf-ring"
                style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:148px;height:148px;border-radius:50%;border:3px solid rgba(84,140,168,.5);animation:lfRingPulse 2s ease-in-out infinite;pointer-events:none;">
            </div>
        </div>
        <div
            style="margin:14px auto 0;max-width:320px;height:5px;background:#e2e8f0;border-radius:3px;overflow:hidden;">
            <div id="lf-prog"
                style="height:100%;width:0%;background:linear-gradient(90deg,#2B4865,#548CA8);border-radius:3px;transition:width .4s;">
            </div>
        </div>
        <div id="lf-error"
            style="display:none;margin-top:10px;padding:9px 13px;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;color:#b91c1c;font-size:.85rem;">
        </div>
        <button onclick="closeLoginFaceModal()"
            style="margin-top:18px;padding:9px 26px;border:1px solid #e2e8f0;border-radius:8px;background:white;color:#64748b;cursor:pointer;font-size:.88rem;font-weight:600;">Cancel</button>
    </div>
</div>

<style>
    @keyframes lfSlideUp {
        from {
            opacity: 0;
            transform: translateY(28px) scale(.97)
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1)
        }
    }

    @keyframes lfRingPulse {

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
            function setStatus(t, p) { el('lf-status').textContent = t; if (p !== undefined) el('lf-prog').style.width = p + '%'; }
            function showErr(m) { el('lf-error').style.display = 'block'; el('lf-error').textContent = m; }
            function hideErr() { el('lf-error').style.display = 'none'; }

            async function loadModels() {
                if (loaded) return;
                await Promise.all([
                    faceapi.nets.tinyFaceDetector.loadFromUri(MODELS),
                    faceapi.nets.faceLandmark68TinyNet.loadFromUri(MODELS),
                    faceapi.nets.faceRecognitionNet.loadFromUri(MODELS)
                ]);
                loaded = true;
            }

            window.openLoginFaceModal = async function () {
                el('login-face-modal').style.display = 'flex';
                hideErr();
                setStatus('Loading face recognition models…', 5);
                try {
                    await loadModels();
                    setStatus('Starting camera…', 20);
                    stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user', width: 360, height: 270 } });
                    const v = el('lf-video'); v.srcObject = stream; await v.play();
                    setStatus('Position your face in the ring and hold still…', 40);
                    startLoop();
                } catch (e) { setStatus('Error', 0); showErr('Error: ' + e.message); }
            };

            function startLoop() {
                detecting = false;
                const v = el('lf-video'), c = el('lf-canvas'), ctx = c.getContext('2d');
                let hits = 0;
                const opts = new faceapi.TinyFaceDetectorOptions({ inputSize: 320, scoreThreshold: .5 });
                loop = setInterval(async () => {
                    if (detecting || v.readyState < 2) return;
                    detecting = true;
                    ctx.clearRect(0, 0, c.width, c.height);
                    try {
                        const r = await faceapi.detectSingleFace(v, opts).withFaceLandmarks(true).withFaceDescriptor();
                        if (!r) { hits = 0; setStatus('No face detected — look at the camera…', 50); el('lf-ring').style.borderColor = 'rgba(84,140,168,.5)'; }
                        else {
                            const dims = faceapi.matchDimensions(c, v, true);
                            faceapi.draw.drawDetections(c, faceapi.resizeResults(r, dims));
                            el('lf-ring').style.borderColor = 'rgba(34,197,94,.8)';
                            hits++;
                            setStatus('Face detected! Verifying identity…', Math.min(45 + hits * 18, 90));
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
                        el('lf-ring').style.borderColor = 'rgba(34,197,94,1)';
                        stopCam();
                        setTimeout(() => { window.location.href = d.redirect; }, 800);
                    } else {
                        setStatus('Not recognized.', 0);
                        showErr(d.message || 'Face not recognized. Try again or use email/password.');
                        el('lf-ring').style.borderColor = 'rgba(239,68,68,.7)';
                        setTimeout(() => { hideErr(); setStatus('Try again — position your face…', 40); el('lf-ring').style.borderColor = 'rgba(84,140,168,.5)'; startLoop(); }, 2500);
                    }
                } catch (e) { showErr('Network error: ' + e.message); }
            }

            function stopCam() {
                if (loop) { clearInterval(loop); loop = null; }
                if (stream) { stream.getTracks().forEach(t => t.stop()); stream = null; }
                const v = el('lf-video'); if (v) v.srcObject = null;
                detecting = false;
            }

            window.closeLoginFaceModal = function () {
                stopCam();
                el('login-face-modal').style.display = 'none';
                hideErr();
                setStatus('Loading face recognition models…', 0);
                el('lf-prog').style.width = '0%';
                el('lf-ring').style.borderColor = 'rgba(84,140,168,.5)';
            };
        })();

    // Check if we need to show verify code modal or reset password modal
    window.addEventListener('DOMContentLoaded', function () {
        const urlParams = new URLSearchParams(window.location.search);
        const showVerifyCode = urlParams.get('verify');
        const showReset = urlParams.get('reset');

        if (showVerifyCode === '1') {
            openVerifyCodeModal();
        }
        if (showReset === '1') {
            openResetModal();
        }
    });
</script>

<!-- Reset Password Modal -->
<div id="reset-modal"
    style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(10,15,30,.85);backdrop-filter:blur(6px);align-items:center;justify-content:center;">
    <div
        style="background:white;border-radius:24px;padding:2rem;max-width:460px;width:95%;text-align:center;box-shadow:0 24px 80px rgba(0,0,0,.4);position:relative;animation:rpSlideUp .3s ease;">
        <button onclick="closeResetModal()"
            style="position:absolute;top:16px;right:16px;background:#f1f5f9;border:none;width:36px;height:36px;border-radius:50%;cursor:pointer;font-size:1.1rem;color:#64748b;display:flex;align-items:center;justify-content:center;">✕</button>
        <div
            style="width:56px;height:56px;background:linear-gradient(135deg,#2B4865,#548CA8);border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                <keyPath d="M21 2l-2 2m-7 7h.01M7 17l-2 2m0 0l2 2m-2-2l2-2m14-8a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
        </div>
        <h3 style="margin:0 0 6px;color:#1e293b;font-size:1.25rem;font-weight:800;">Reset Password</h3>
        <p style="margin:0 0 18px;color:#64748b;font-size:.88rem;">Enter your new password below.</p>

        <form action="<?= APP_ENTRY ?>?url=process-reset-password" method="POST" style="text-align: left;">
            <div class="neo-field" style="margin-bottom: 1rem;">
                <label for="reset-password"
                    style="display: block; margin-bottom: 6px; font-weight: 600; color: #1e293b;">New Password</label>
                <input type="password" id="reset-password" name="password" placeholder="Enter new password" required
                    minlength="6"
                    style="width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem;">
            </div>

            <div class="neo-field" style="margin-bottom: 1.25rem;">
                <label for="reset-confirm-password"
                    style="display: block; margin-bottom: 6px; font-weight: 600; color: #1e293b;">Confirm
                    Password</label>
                <input type="password" id="reset-confirm-password" name="confirm_password"
                    placeholder="Confirm new password" required minlength="6"
                    style="width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem;">
            </div>

            <button type="submit" class="neo-btn neo-btn-primary" style="width: 100%;">Reset Password</button>
        </form>

        <p style="margin: 16px 0 0; color: #64748b; font-size: 0.85rem;">
            Remember your password? <a href="<?= APP_ENTRY ?>?url=login" style="color: #548CA8; font-weight: 600;">Sign
                In</a>
        </p>
    </div>
</div>

<style>
    @keyframes rpSlideUp {
        from {
            opacity: 0;
            transform: translateY(28px) scale(.97)
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1)
        }
    }
</style>

<script>
    function openResetModal() {
        document.getElementById('reset-modal').style.display = 'flex';
    }

    function closeResetModal() {
        document.getElementById('reset-modal').style.display = 'none';
    }

    // Close reset modal when clicking outside
    window.onclick = function (event) {
        if (event.target.id === 'reset-modal') {
            closeResetModal();
        }
    }
</script>