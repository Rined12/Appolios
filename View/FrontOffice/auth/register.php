<?php
/**
 * APPOLIOS - Registration Page (Premium Neo Design)
 */

$old = isset($old) && is_array($old) ? $old : [];
$flash = isset($flash) ? $flash : null;
$errors = isset($errors) && is_array($errors) ? $errors : [];
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

            <form action="<?= APP_ENTRY ?>?url=signup" method="POST" onsubmit="return appValidateRegister(this);">
                <div class="neo-field">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" placeholder="Your full name" value="<?= htmlspecialchars($old['name'] ?? '') ?>" autocomplete="name">
                    <div class="neo-muted" id="name_err" style="margin-top:0.35rem; font-weight:700; color:#b91c1c; display:none;"></div>
                </div>

                <div class="neo-field">
                    <label for="email">Email Address</label>
                    <input type="text" id="email" name="email" placeholder="you@example.com" value="<?= htmlspecialchars($old['email'] ?? '') ?>" autocomplete="email">
                    <div class="neo-muted" id="email_err" style="margin-top:0.35rem; font-weight:700; color:#b91c1c; display:none;"></div>
                </div>

                <div class="neo-field">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Minimum 6 characters" autocomplete="new-password">
                    <div class="neo-muted" id="pass_err" style="margin-top:0.35rem; font-weight:700; color:#b91c1c; display:none;"></div>
                </div>

                <div class="neo-field">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm password" autocomplete="new-password">
                    <div class="neo-muted" id="cpass_err" style="margin-top:0.35rem; font-weight:700; color:#b91c1c; display:none;"></div>
                </div>

                <div class="neo-field">
                    <label>Register as</label>
                    <div style="display: flex; gap: 0.9rem; color: #cbd5e1; font-size: 0.92rem; margin-top: 0.32rem;">
                        <label><input type="radio" name="role" value="student" checked> Student</label>
                        <label><input type="radio" name="role" value="teacher"> Teacher</label>
                    </div>
                    <div class="neo-muted" style="font-size: 0.82rem; margin-top: 0.35rem;">Teacher accounts require admin validation.</div>
                </div>

                <button type="submit" class="neo-btn neo-btn-primary" style="margin-top: 0.95rem; width: 100%;">Create Account</button>
            </form>

            <p class="neo-muted" style="margin-top: 0.85rem; font-size: 0.9rem;">Already have an account? <a href="<?= APP_ENTRY ?>?url=login" style="color: #93c5fd;">Sign in</a></p>
        </div>
    </div>
</section>

<script>
function appTrim(s) { return String(s || '').replace(/^\s+|\s+$/g, ''); }
function appShowErr(id, msg) {
  var el = document.getElementById(id);
  if (!el) return;
  if (!msg) { el.style.display = 'none'; el.textContent = ''; return; }
  el.textContent = msg;
  el.style.display = 'block';
}
function appIsEmailLike(v) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v);
}
function appValidateRegister(f) {
  var name = appTrim(f.name && f.name.value);
  var email = appTrim(f.email && f.email.value);
  var pass = String((f.password && f.password.value) || '');
  var cpass = String((f.confirm_password && f.confirm_password.value) || '');
  var ok = true;

  if (!name) { appShowErr('name_err', 'Nom complet obligatoire.'); ok = false; }
  else if (name.length < 3) { appShowErr('name_err', 'Minimum 3 caractères.'); ok = false; }
  else appShowErr('name_err', '');

  if (!email) { appShowErr('email_err', 'Email obligatoire.'); ok = false; }
  else if (!appIsEmailLike(email)) { appShowErr('email_err', 'Format email invalide.'); ok = false; }
  else appShowErr('email_err', '');

  if (!pass) { appShowErr('pass_err', 'Mot de passe obligatoire.'); ok = false; }
  else if (pass.length < 6) { appShowErr('pass_err', 'Minimum 6 caractères.'); ok = false; }
  else appShowErr('pass_err', '');

  if (!cpass) { appShowErr('cpass_err', 'Confirmation obligatoire.'); ok = false; }
  else if (pass !== cpass) { appShowErr('cpass_err', 'Les mots de passe ne correspondent pas.'); ok = false; }
  else appShowErr('cpass_err', '');

  return ok;
}

document.addEventListener('DOMContentLoaded', function () {
  var f = document.querySelector('.neo-auth-form form');
  if (!f) return;
  ['name','email','password','confirm_password'].forEach(function (k) {
    if (!f[k]) return;
    f[k].addEventListener('input', function () { appValidateRegister(f); });
    f[k].addEventListener('blur', function () { appValidateRegister(f); });
  });
});
</script>