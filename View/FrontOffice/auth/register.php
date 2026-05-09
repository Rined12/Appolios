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
            <p class="neo-muted" style="margin-top: 0.5rem;">Join APPOLIOS to unlock courses, projects, and career-focused paths.</p>
            <div class="neo-badges" style="margin-top: 1rem;">
                <span class="neo-badge primary">Premium UI</span>
                <span class="neo-badge success">Certificates</span>
                <span class="neo-badge warning">Skill Levels</span>
            </div>
        </aside>

        <div class="neo-auth-form">
            <!-- Back to Home Button -->
            <a href="<?= APP_ENTRY ?>?url=home/index" style="display: inline-flex; align-items: center; gap: 6px; color: #64748b; text-decoration: none; font-size: 0.85rem; font-weight: 600; margin-bottom: 1rem; transition: color 0.2s;" onmouseover="this.style.color='#2B4865'" onmouseout="this.style.color='#64748b'">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Back to Home
            </a>
            <h2>Register</h2>
            <p class="neo-muted" style="margin-top: 0.45rem;">Create your account and start your first track.</p>

            <?php if ($flash): ?>
                <div class="neo-alert <?= $flash['type'] === 'error' ? 'error' : 'success' ?>">
                    <?= htmlspecialchars($flash['message']) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="neo-alert error">
                    <?php foreach ($errors as $error): ?>
                        <div>• <?= htmlspecialchars($error) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="<?= APP_ENTRY ?>?url=signup" method="POST" enctype="multipart/form-data">
                <div class="neo-field">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" placeholder="Your full name" value="<?= htmlspecialchars($old['name'] ?? '') ?>" required>
                </div>

                <div class="neo-field">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="you@example.com" value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>
                </div>

                <div class="neo-field">
                    <label for="password">Password</label>
                    <div style="position: relative;">
                        <input type="password" id="password" name="password" placeholder="Minimum 6 characters" required style="padding-right: 50px; width: 100%;">
                        <button type="button" onclick="togglePassword('password')" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; padding: 8px; color: #64748b; transition: color 0.2s;" onmouseover="this.style.color='#2B4865'" onmouseout="this.style.color='#64748b'">
                            <svg id="eye-icon-password" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <svg id="eye-off-icon-password" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                <line x1="1" y1="1" x2="23" y2="23"></line>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="neo-field">
                    <label for="confirm_password">Confirm Password</label>
                    <div style="position: relative;">
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm password" required style="padding-right: 50px; width: 100%;">
                        <button type="button" onclick="togglePassword('confirm_password')" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; padding: 8px; color: #64748b; transition: color 0.2s;" onmouseover="this.style.color='#2B4865'" onmouseout="this.style.color='#64748b'">
                            <svg id="eye-icon-confirm_password" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <svg id="eye-off-icon-confirm_password" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                <line x1="1" y1="1" x2="23" y2="23"></line>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="neo-field">
                    <label>Register as</label>
                    <div style="display: flex; gap: 0.9rem; color: #cbd5e1; font-size: 0.92rem; margin-top: 0.32rem;">
                        <label><input type="radio" name="role" value="student" checked onchange="toggleCvField()"> Student</label>
                        <label><input type="radio" name="role" value="teacher" onchange="toggleCvField()"> Teacher</label>
                    </div>
                    <div class="neo-muted" style="font-size: 0.82rem; margin-top: 0.35rem;">Teacher accounts require admin validation.</div>
                </div>

                <div class="neo-field" id="cv-field" style="display: none;">
                    <label for="cv_file">Upload CV (PDF)</label>
                    <div class="neo-file-upload">
                        <input type="file" id="cv_file" name="cv_file" accept=".pdf">
                        <label for="cv_file" class="neo-file-label">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="17 8 12 3 7 8"></polyline>
                                <line x1="12" y1="3" x2="12" y2="15"></line>
                            </svg>
                            <span>Choose PDF file</span>
                        </label>
                        <span class="neo-file-name" id="cv-file-name">No file selected</span>
                    </div>
                    <div class="neo-muted" style="font-size: 0.82rem; margin-top: 0.5rem;">Required for teacher applications. PDF only, max 5MB.</div>
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
                document.getElementById('cv_file').addEventListener('change', function() {
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

                <button type="submit" class="neo-btn neo-btn-primary" style="margin-top: 0.95rem; width: 100%;">Create Account</button>
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
            </script>

            <p class="neo-muted" style="margin-top: 0.85rem; font-size: 0.9rem;">Already have an account? <a href="<?= APP_ENTRY ?>?url=login" style="color: #93c5fd;">Sign in</a></p>
        </div>
    </div>
</section>