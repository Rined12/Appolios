<?php
/**
 * APPOLIOS - Registration Page (Premium Neo Design)
 */

// Get old input if available
$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);

// Get flash messages
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// Get errors
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);
?>

<section class="neo-auth-wrap">
    <div class="neo-glass-card neo-auth-grid">
        <aside class="neo-auth-info">
            <h2>Create Your Learning Profile</h2>
            <p class="neo-muted" style="margin-top: 0.5rem;">Join APPOLIOS to unlock courses, projects, and
                career-focused paths.</p>
            <div style="margin-top: 1.5rem;">
                <?php include __DIR__ . '/../student/ocr_component.php'; ?>
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
            <h2>Register</h2>
            <p class="neo-muted" style="margin-top: 0.45rem;">Create your account and start your first track.</p>



            <form action="<?= APP_ENTRY ?>?url=signup" method="POST" enctype="multipart/form-data"
                onsubmit="return validateRecaptcha()">
                <div class="neo-field">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" placeholder="Your full name"
                        value="<?= htmlspecialchars($old['name'] ?? '') ?>" required>
                </div>

                <div class="neo-field">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="you@example.com"
                        value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>
                </div>

                <div class="neo-field">
                    <label for="password">Password</label>
                    <div style="position: relative; display: flex; align-items: center; gap: 8px;">
                        <input type="password" id="password" name="password" placeholder="Minimum 8 characters" required
                            style="padding-right: 80px; width: 100%;">
                        <!-- Generate Password Button -->
                        <button type="button" onclick="generatePassword()"
                            style="position: absolute; right: 44px; top: 50%; transform: translateY(-50%); background: linear-gradient(135deg, #10b981, #059669); border: none; cursor: pointer; padding: 8px 12px; border-radius: 8px; color: white; font-size: 0.75rem; font-weight: 700; transition: all 0.2s; box-shadow: 0 2px 8px rgba(16,185,129,0.3);"
                            onmouseover="this.style.transform='translateY(-50%) scale(1.05)'; this.style.boxShadow='0 4px 12px rgba(16,185,129,0.4)';"
                            onmouseout="this.style.transform='translateY(-50%)'; this.style.boxShadow='0 2px 8px rgba(16,185,129,0.3)';"
                            title="Generate strong password">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                                style="display: inline; margin-right: 4px;">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                            GEN
                        </button>
                        <button type="button" onclick="togglePassword('password')"
                            style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; padding: 8px; color: #64748b; transition: color 0.2s;"
                            onmouseover="this.style.color='#2B4865'" onmouseout="this.style.color='#64748b'">
                            <svg id="eye-icon-password" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
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

                    <!-- Password Strength Checker UI -->
                    <div class="password-strength-checker"
                        style="margin-top: 12px; padding: 12px; background: rgba(15, 23, 42, 0.2); border-radius: 12px; border: 1px solid rgba(84, 140, 168, 0.2);">
                        <div
                            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                            <span
                                style="font-size: 0.75rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em;">Strength</span>
                            <span id="strength-text" style="font-size: 0.75rem; font-weight: 700; color: #64748b;">Too
                                Weak</span>
                        </div>
                        <div
                            style="height: 6px; background: rgba(226, 232, 240, 0.1); border-radius: 10px; overflow: hidden; margin-bottom: 12px;">
                            <div id="strength-bar"
                                style="height: 100%; width: 0%; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); background: #ef4444;">
                            </div>
                        </div>
                        <ul style="list-style: none; padding: 0; margin: 0; display: grid; gap: 6px;">
                            <li id="req-length"
                                style="font-size: 0.8rem; color: #ef4444; display: flex; align-items: center; gap: 8px; transition: all 0.3s;">
                                <div class="req-dot"
                                    style="width: 6px; height: 6px; border-radius: 50%; background: currentColor;">
                                </div>
                                At least 8 characters
                            </li>
                            <li id="req-number"
                                style="font-size: 0.8rem; color: #ef4444; display: flex; align-items: center; gap: 8px; transition: all 0.3s;">
                                <div class="req-dot"
                                    style="width: 6px; height: 6px; border-radius: 50%; background: currentColor;">
                                </div>
                                Contains a number
                            </li>
                            <li id="req-special"
                                style="font-size: 0.8rem; color: #ef4444; display: flex; align-items: center; gap: 8px; transition: all 0.3s;">
                                <div class="req-dot"
                                    style="width: 6px; height: 6px; border-radius: 50%; background: currentColor;">
                                </div>
                                Contains a special character
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="neo-field">
                    <label for="confirm_password">Confirm Password</label>
                    <div style="position: relative;">
                        <input type="password" id="confirm_password" name="confirm_password"
                            placeholder="Confirm password" required style="padding-right: 50px; width: 100%;">
                        <button type="button" onclick="togglePassword('confirm_password')"
                            style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; padding: 8px; color: #64748b; transition: color 0.2s;"
                            onmouseover="this.style.color='#2B4865'" onmouseout="this.style.color='#64748b'">
                            <svg id="eye-icon-confirm_password" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <svg id="eye-off-icon-confirm_password" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" style="display: none;">
                                <path
                                    d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24">
                                </path>
                                <line x1="1" y1="1" x2="23" y2="23"></line>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="neo-field">
                    <label>Register as</label>
                    <div style="display: flex; gap: 0.9rem; color: #cbd5e1; font-size: 0.92rem; margin-top: 0.32rem;">
                        <label><input type="radio" name="role" value="student" checked onchange="toggleCvField()">
                            Student</label>
                        <label><input type="radio" name="role" value="teacher" onchange="toggleCvField()">
                            Teacher</label>
                    </div>
                    <div class="neo-muted" style="font-size: 0.82rem; margin-top: 0.35rem;">Teacher accounts require
                        admin validation.</div>
                </div>

                <div class="neo-field" id="cv-field" style="display: none;">
                    <label for="cv_file">Upload CV (PDF)</label>
                    <div class="neo-file-upload">
                        <input type="file" id="cv_file" name="cv_file" accept=".pdf">
                        <label for="cv_file" class="neo-file-label">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="17 8 12 3 7 8"></polyline>
                                <line x1="12" y1="3" x2="12" y2="15"></line>
                            </svg>
                            <span>Choose PDF file</span>
                        </label>
                        <span class="neo-file-name" id="cv-file-name">No file selected</span>
                    </div>
                    <div class="neo-muted" style="font-size: 0.82rem; margin-top: 0.5rem;">Required for teacher
                        applications. PDF only, max 5MB.</div>
                </div>

                <style>
                    .neo-file-upload {
                        position: relative;
                        display: flex;
                        align-items: center;
                        gap: 0.75rem;
                        flex-wrap: wrap;
                    }

                    .neo-file-upload input[type="file"] {
                        position: absolute;
                        opacity: 0;
                        width: 0;
                        height: 0;
                    }

                    .neo-file-label {
                        display: inline-flex;
                        align-items: center;
                        gap: 0.5rem;
                        padding: 10px 20px;
                        background: linear-gradient(135deg, #548CA8 0%, #355C7D 100%);
                        color: #fff;
                        border-radius: 12px;
                        font-weight: 600;
                        font-size: 0.9rem;
                        cursor: pointer;
                        transition: all 0.2s ease;
                        box-shadow: 0 4px 15px rgba(84, 140, 168, 0.3);
                        border: none;
                    }

                    .neo-file-label:hover {
                        transform: translateY(-2px);
                        box-shadow: 0 6px 20px rgba(84, 140, 168, 0.4);
                    }

                    .neo-file-label svg {
                        stroke: #E19864;
                    }

                    .neo-file-name {
                        color: #94a3b8;
                        font-size: 0.85rem;
                        font-style: italic;
                    }

                    .neo-file-name.has-file {
                        color: #E19864;
                        font-weight: 600;
                        font-style: normal;
                    }
                </style>

                <script>
                    function toggleCvField() {
                        const role = document.querySelector('input[name="role"]:checked').value;
                        const cvField = document.getElementById('cv-field');
                        if (role === 'teacher') {
                            cvField.style.display = 'block';
                        } else {
                            cvField.style.display = 'none';
                        }
                    }

                    // Display selected filename
                    document.getElementById('cv_file').addEventListener('change', function () {
                        const fileName = this.files[0] ? this.files[0].name : 'No file selected';
                        const fileNameEl = document.getElementById('cv-file-name');
                        fileNameEl.textContent = fileName;
                        if (this.files[0]) {
                            fileNameEl.classList.add('has-file');
                        } else {
                            fileNameEl.classList.remove('has-file');
                        }
                    });
                </script>

                <!-- Hidden face descriptor container -->
                <input type="hidden" name="face_descriptor" id="face_descriptor_input" value="">

                <!-- Optional Face ID Step -->
                <div id="faceid-enroll-section"
                    style="border:1.5px dashed rgba(84,140,168,0.35);border-radius:14px;padding:1rem 1.1rem;margin:0.5rem 0 0.85rem;background:rgba(43,72,101,0.05);">
                    <!-- Header row -->
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div
                                style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#2B4865,#548CA8);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10" />
                                    <path d="M8 14s1.5 2 4 2 4-2 4-2" />
                                    <line x1="9" y1="9" x2="9.01" y2="9" />
                                    <line x1="15" y1="9" x2="15.01" y2="9" />
                                </svg>
                            </div>
                            <div>
                                <div style="color:#cbd5e1;font-weight:700;font-size:.93rem;line-height:1.2;">
                                    Register Face ID <span
                                        style="font-size:.75rem;color:#94a3b8;font-weight:500;">(Optional)</span>
                                </div>
                                <div style="color:#64748b;font-size:.78rem;">Log in instantly next time with your face
                                </div>
                            </div>
                        </div>
                        <button type="button" id="faceid-toggle-btn" onclick="toggleFaceIdEnroll()"
                            style="display:flex;align-items:center;gap:6px;padding:7px 14px;border-radius:8px;border:1.5px solid rgba(84,140,168,0.5);background:rgba(84,140,168,0.1);color:#93c5fd;font-size:.82rem;font-weight:700;cursor:pointer;white-space:nowrap;transition:all .2s;"
                            onmouseover="this.style.background='rgba(84,140,168,0.22)'"
                            onmouseout="this.style.background='rgba(84,140,168,0.1)'">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M23 7l-7 5 7 5V7z" />
                                <rect x="1" y="5" width="15" height="14" rx="2" ry="2" />
                            </svg>
                            Activate Camera
                        </button>
                    </div>

                    <!-- Status badge (shows after capture) -->
                    <div id="faceid-captured-badge"
                        style="display:none;margin-top:10px;padding:8px 12px;background:rgba(34,197,94,0.12);border:1px solid rgba(34,197,94,0.35);border-radius:8px;display:none;align-items:center;gap:8px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2.5"
                            stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12" />
                        </svg>
                        <span style="color:#22c55e;font-weight:700;font-size:.85rem;">Face ID captured! It will be
                            linked to your account.</span>
                        <button type="button" onclick="clearCapturedFace()"
                            style="margin-left:auto;background:none;border:none;color:#94a3b8;cursor:pointer;font-size:.8rem;padding:2px 6px;border-radius:5px;"
                            title="Remove captured face">✕ Remove</button>
                    </div>

                    <!-- Error banner -->
                    <div id="faceid-enroll-error"
                        style="display:none;margin-top:10px;padding:8px 12px;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;color:#b91c1c;font-size:.84rem;">
                    </div>
                </div>

                <!-- Google reCAPTCHA v2 -->
                <div class="g-recaptcha" data-sitekey="<?= RECAPTCHA_SITE_KEY ?>" style="margin: 1rem 0;"></div>

                <button type="submit" id="create-account-btn" class="neo-btn neo-btn-primary"
                    style="margin-top: 0.95rem; width: 100%;">Create Account</button>
            </form>

            <script>
                function togglePassword(inputId) {
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

                // Generate Strong Password - Local Crypto Secure (No API, Works Offline)
                function generatePassword() {
                    const passwordField = document.getElementById('password');
                    const confirmPasswordField = document.getElementById('confirm_password');
                    const generateBtn = document.querySelector('button[onclick="generatePassword()"]');

                    // Generate password using crypto-secure random
                    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*(),.?":{}|<>';
                    const array = new Uint32Array(16);
                    crypto.getRandomValues(array);

                    let password = '';
                    for (let i = 0; i < 16; i++) {
                        password += chars[array[i] % chars.length];
                    }

                    // Set password to both fields
                    passwordField.value = password;
                    confirmPasswordField.value = password;

                    // Trigger input events for strength checker and validation
                    passwordField.dispatchEvent(new Event('input'));
                    confirmPasswordField.dispatchEvent(new Event('input'));

                    // Show success feedback
                    const originalContent = generateBtn.innerHTML;
                    generateBtn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> OK';
                    generateBtn.style.background = 'linear-gradient(135deg, #22c55e, #16a34a)';

                    setTimeout(() => {
                        generateBtn.innerHTML = originalContent;
                        generateBtn.style.background = 'linear-gradient(135deg, #10b981, #059669)';
                    }, 1500);
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

            <script>
                // Password Strength Logic
                (function () {
                    const pwd = document.getElementById('password');
                    const bar = document.getElementById('strength-bar');
                    const text = document.getElementById('strength-text');
                    const reqLen = document.getElementById('req-length');
                    const reqNum = document.getElementById('req-number');
                    const reqSpec = document.getElementById('req-special');

                    pwd.addEventListener('input', () => {
                        const val = pwd.value;
                        let strength = 0;

                        const hasLen = val.length >= 8;
                        const hasNum = /\d/.test(val);
                        const hasSpec = /[!@#$%^&*(),.?":{}|<>]/.test(val);

                        if (hasLen) strength += 33.33;
                        if (hasNum) strength += 33.33;
                        if (hasSpec) strength += 33.34;

                        bar.style.width = strength + '%';

                        // Update UI colors and text
                        updateReq(reqLen, hasLen);
                        updateReq(reqNum, hasNum);
                        updateReq(reqSpec, hasSpec);

                        if (strength === 0) {
                            bar.style.background = '#ef4444';
                            text.textContent = 'Too Weak';
                            text.style.color = '#ef4444';
                        } else if (strength < 34) {
                            bar.style.background = '#ef4444';
                            text.textContent = 'Weak';
                            text.style.color = '#ef4444';
                        } else if (strength < 67) {
                            bar.style.background = '#f59e0b';
                            text.textContent = 'Medium';
                            text.style.color = '#f59e0b';
                        } else if (strength < 100) {
                            bar.style.background = '#10b981';
                            text.textContent = 'Strong';
                            text.style.color = '#10b981';
                        } else {
                            bar.style.background = '#22c55e';
                            text.textContent = 'Very Strong';
                            text.style.color = '#22c55e';
                        }
                    });

                    function updateReq(el, met) {
                        if (met) {
                            el.style.color = '#22c55e';
                        } else {
                            el.style.color = '#ef4444';
                        }
                    }
                })();
            </script>

            <p class="neo-muted" style="margin-top: 0.85rem; font-size: 0.9rem;">Already have an account? <a
                    href="<?= APP_ENTRY ?>?url=login" style="color: #93c5fd;">Sign in</a></p>

            <hr style="border: 0; border-top: 1px dashed rgba(255,255,255,0.1); margin: 2rem 0;">

            <!-- OCR Tool for Registration (Optional) -->
            <!-- OCR Tool moved to sidebar -->
        </div>
    </div>
</section>

<!-- Face ID Enrollment Modal -->
<div id="reg-face-modal"
    style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(10,15,30,.88);backdrop-filter:blur(6px);align-items:center;justify-content:center;">
    <div
        style="background:#fff;border-radius:24px;padding:2rem;max-width:460px;width:95%;text-align:center;box-shadow:0 24px 80px rgba(0,0,0,.4);position:relative;animation:rfSlideUp .3s ease;">
        <button onclick="closeRegFaceModal()"
            style="position:absolute;top:16px;right:16px;background:#f1f5f9;border:none;width:36px;height:36px;border-radius:50%;cursor:pointer;font-size:1.1rem;color:#64748b;display:flex;align-items:center;justify-content:center;">✕</button>

        <div
            style="width:56px;height:56px;background:linear-gradient(135deg,#2B4865,#548CA8);border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10" />
                <path d="M8 14s1.5 2 4 2 4-2 4-2" />
                <line x1="9" y1="9" x2="9.01" y2="9" />
                <line x1="15" y1="9" x2="15.01" y2="9" />
            </svg>
        </div>
        <h3 style="margin:0 0 4px;color:#1e293b;font-size:1.2rem;font-weight:800;">Register Your Face ID</h3>
        <p style="margin:0 0 16px;color:#64748b;font-size:.85rem;">Position your face in the ring then click
            <strong>Capture</strong>. Each face can only be linked to one account.
        </p>

        <p id="rf-status" style="margin:0 0 12px;color:#64748b;font-size:.85rem;">Loading models…</p>

        <div
            style="position:relative;display:inline-block;border-radius:14px;overflow:hidden;box-shadow:0 8px 30px rgba(0,0,0,.15);">
            <video id="rf-video" autoplay muted playsinline width="360" height="270"
                style="display:block;border-radius:14px;background:#0f172a;"></video>
            <canvas id="rf-canvas" width="360" height="270"
                style="position:absolute;top:0;left:0;pointer-events:none;"></canvas>
            <div id="rf-ring"
                style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:148px;height:148px;border-radius:50%;border:3px solid rgba(84,140,168,.5);animation:rfRingPulse 2s ease-in-out infinite;pointer-events:none;">
            </div>
        </div>

        <div
            style="margin:12px auto 0;max-width:320px;height:5px;background:#e2e8f0;border-radius:3px;overflow:hidden;">
            <div id="rf-prog"
                style="height:100%;width:0%;background:linear-gradient(90deg,#2B4865,#548CA8);border-radius:3px;transition:width .4s;">
            </div>
        </div>

        <div id="rf-error"
            style="display:none;margin-top:10px;padding:9px 13px;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;color:#b91c1c;font-size:.85rem;">
        </div>

        <div style="display:flex;gap:10px;margin-top:18px;justify-content:center;">
            <button onclick="closeRegFaceModal()"
                style="padding:9px 22px;border:1px solid #e2e8f0;border-radius:8px;background:white;color:#64748b;cursor:pointer;font-size:.88rem;font-weight:600;">Cancel</button>
            <button id="rf-capture-btn" onclick="captureRegFace()" disabled
                style="padding:9px 22px;border:none;border-radius:8px;background:linear-gradient(135deg,#2B4865,#548CA8);color:white;font-weight:700;font-size:.88rem;cursor:not-allowed;opacity:.5;transition:all .2s;">
                📸 Capture Face
            </button>
        </div>
    </div>
</div>

<style>
    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    @keyframes rfSlideUp {
        from {
            opacity: 0;
            transform: translateY(28px) scale(.97)
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1)
        }
    }

    @keyframes rfRingPulse {

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
        const CHECK_URL = '<?= APP_ENTRY ?>?url=auth/check-face-unique';
        let stream = null, loaded = false, faceReady = false, currentDescriptor = null;

        function el(id) { return document.getElementById(id); }
        function setStatus(t, p) { el('rf-status').textContent = t; if (p !== undefined) el('rf-prog').style.width = p + '%'; }
        function showErr(m) { el('rf-error').style.display = 'block'; el('rf-error').textContent = m; }
        function hideErr() { el('rf-error').style.display = 'none'; }

        async function loadModels() {
            if (loaded) return;
            setStatus('Loading face recognition models…', 10);
            await Promise.all([
                faceapi.nets.tinyFaceDetector.loadFromUri(MODELS),
                faceapi.nets.faceLandmark68TinyNet.loadFromUri(MODELS),
                faceapi.nets.faceRecognitionNet.loadFromUri(MODELS)
            ]);
            loaded = true;
        }

        window.toggleFaceIdEnroll = async function () {
            if (!navigator.onLine && !loaded) {
                Swal.fire({
                    icon: 'error',
                    title: 'Offline',
                    text: 'Internet connection is required to load face recognition models for the first time.'
                });
                return;
            }

            el('reg-face-modal').style.display = 'flex';
            hideErr();
            faceReady = false;
            el('rf-capture-btn').disabled = true;
            el('rf-capture-btn').style.opacity = '.5';
            el('rf-capture-btn').style.cursor = 'not-allowed';
            setStatus('Loading models…', 5);

            try {
                await loadModels();
                setStatus('Starting camera…', 20);
                stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user', width: 360, height: 270 } });
                const v = el('rf-video'); v.srcObject = stream; await v.play();
                setStatus('Position your face in the ring…', 40);
                startPreviewLoop();
            } catch (e) {
                setStatus('Error', 0);
                const msg = !navigator.onLine ? 'Network error: Check your internet connection.' : 'Camera error: ' + e.message;
                showErr(msg);
            }
        };

        let previewLoop = null;
        function startPreviewLoop() {
            const v = el('rf-video'), c = el('rf-canvas'), ctx = c.getContext('2d');
            const opts = new faceapi.TinyFaceDetectorOptions({ inputSize: 320, scoreThreshold: .5 });
            previewLoop = setInterval(async () => {
                if (v.readyState < 2) return;
                ctx.clearRect(0, 0, c.width, c.height);
                try {
                    const r = await faceapi.detectSingleFace(v, opts).withFaceLandmarks(true).withFaceDescriptor();
                    if (!r) {
                        faceReady = false;
                        el('rf-ring').style.borderColor = 'rgba(84,140,168,.5)';
                        setStatus('No face detected — look at the camera…', 40);
                        el('rf-capture-btn').disabled = true;
                        el('rf-capture-btn').style.opacity = '.5';
                        el('rf-capture-btn').style.cursor = 'not-allowed';
                    } else {
                        const dims = faceapi.matchDimensions(c, v, true);
                        faceapi.draw.drawDetections(c, faceapi.resizeResults(r, dims));
                        el('rf-ring').style.borderColor = 'rgba(34,197,94,.8)';
                        currentDescriptor = Array.from(r.descriptor);
                        faceReady = true;
                        setStatus('✓ Face detected — click Capture!', 80);
                        el('rf-capture-btn').disabled = false;
                        el('rf-capture-btn').style.opacity = '1';
                        el('rf-capture-btn').style.cursor = 'pointer';
                    }
                } catch (e) { }
            }, 700);
        }

        window.captureRegFace = async function () {
            if (!faceReady || !currentDescriptor) return;
            clearInterval(previewLoop); previewLoop = null;
            hideErr();
            setStatus('Checking if face is unique…', 90);
            el('rf-capture-btn').disabled = true;

            try {
                const res = await fetch(CHECK_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ descriptor: currentDescriptor })
                });
                const d = await res.json();

                if (!d.unique) {
                    showErr('⚠ ' + (d.message || 'This face is already linked to an account.'));
                    setStatus('Face already registered.', 0);
                    el('rf-ring').style.borderColor = 'rgba(239,68,68,.7)';
                    // restart preview after 2.5s
                    setTimeout(() => {
                        hideErr();
                        setStatus('Try a different angle or use email/password.', 40);
                        el('rf-ring').style.borderColor = 'rgba(84,140,168,.5)';
                        el('rf-capture-btn').disabled = false;
                        el('rf-capture-btn').style.opacity = '1';
                        el('rf-capture-btn').style.cursor = 'pointer';
                        startPreviewLoop();
                    }, 2800);
                    return;
                }

                // Unique — save to hidden field and close modal
                el('face_descriptor_input').value = JSON.stringify(currentDescriptor);
                stopCam();
                el('reg-face-modal').style.display = 'none';

                // Show success badge
                const badge = el('faceid-captured-badge');
                badge.style.display = 'flex';

                // Update toggle button
                const btn = el('faceid-toggle-btn');
                btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Face Captured';
                btn.style.borderColor = 'rgba(34,197,94,0.6)';
                btn.style.background = 'rgba(34,197,94,0.1)';
                btn.style.color = '#22c55e';
            } catch (e) { showErr('Network error: ' + e.message); }
        };

        window.clearCapturedFace = function () {
            el('face_descriptor_input').value = '';
            el('faceid-captured-badge').style.display = 'none';
            currentDescriptor = null;
            const btn = el('faceid-toggle-btn');
            btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M23 7l-7 5 7 5V7z"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg> Activate Camera';
            btn.style.borderColor = 'rgba(84,140,168,0.5)';
            btn.style.background = 'rgba(84,140,168,0.1)';
            btn.style.color = '#93c5fd';
        };

        function stopCam() {
            if (previewLoop) { clearInterval(previewLoop); previewLoop = null; }
            if (stream) { stream.getTracks().forEach(t => t.stop()); stream = null; }
            const v = el('rf-video'); if (v) v.srcObject = null;
        }

        window.closeRegFaceModal = function () {
            stopCam();
            el('reg-face-modal').style.display = 'none';
            hideErr();
            setStatus('Loading models…', 0);
            el('rf-prog').style.width = '0%';
            el('rf-ring').style.borderColor = 'rgba(84,140,168,.5)';
            faceReady = false;
            currentDescriptor = null;
        };
    })();
</script>

<!-- Google reCAPTCHA v2 Script -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>