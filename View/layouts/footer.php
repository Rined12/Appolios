</main>

<footer class="app-footer">
    <div class="container app-footer-inner">
        <p>© <?= date('Y') ?> APPOLIOS. Built for professional e-learning workflows.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?= APP_URL ?>/View/assets/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= APP_URL ?>/View/assets/js/functions.js"></script>
<script src="<?= APP_URL ?>/View/assets/js/main.js"></script>
<script src="<?= APP_URL ?>/View/assets/js/module-focus.js"></script>
<style>
#page-loader { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.95); z-index: 99999; justify-content: center; align-items: center; flex-direction: column; }
#page-loader.active { display: flex; }
#page-loader .spinner { width: 40px; height: 40px; border: 4px solid #e2e8f0; border-top-color: #3b82f6; border-radius: 50%; animation: spin 1s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }
</style>
<div id="page-loader"><div class="spinner"></div><p style="margin-top:1rem;color:#64748b;">Loading...</p></div>
<script>
    window.APP_URL = "<?= addslashes(APP_URL) ?>";
    (function () {
        function hidePageLoader() {
            var el = document.getElementById('page-loader');
            if (el) {
                el.classList.remove('active');
            }
        }
        function showPageLoader(message) {
            var el = document.getElementById('page-loader');
            if (!el) return;
            var p = el.querySelector('p');
            if (p) {
                p.textContent = message || 'Loading...';
            }
            el.classList.add('active');
        }
        document.addEventListener('DOMContentLoaded', hidePageLoader);
        window.addEventListener('load', hidePageLoader);
        window.addEventListener('pageshow', function (ev) {
            if (ev.persisted) {
                hidePageLoader();
            }
        });
        window.addEventListener('beforeunload', function () {
            showPageLoader();
        });
        setTimeout(hidePageLoader, 8000);
    })();
    window.showLoading = function(message) {
        var el = document.getElementById('page-loader');
        if (!el) return;
        var p = el.querySelector('p');
        if (p) {
            p.textContent = message || 'Loading...';
        }
        el.classList.add('active');
    };
    window.hideLoading = function() {
        var el = document.getElementById('page-loader');
        if (el) {
            el.classList.remove('active');
        }
    };
</script>
<script src="<?= APP_URL ?>/View/assets/js/chatbot.js"></script>

<!-- SweetAlert2 Flash Messages -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if (isset($flash) && $flash): ?>
        const flash = <?= json_encode($flash) ?>;
        if (flash.type === 'success') {
            Swal.fire({
                title: flash.message,
                icon: "success",
                draggable: true
            });
        } else if (flash.type === 'error') {
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: flash.message,
                footer: '<a href="#">Why do I have this issue?</a>'
            });
        }
    <?php endif; ?>

    <?php if (isset($errors) && !empty($errors)): ?>
        const errors = <?= json_encode($errors) ?>;
        Swal.fire({
            icon: "error",
            title: "Validation Errors",
            html: '<ul style="text-align: left; list-style: disc; padding-left: 20px;">' + 
                  errors.map(err => `<li>${err}</li>`).join('') + 
                  '</ul>',
            footer: '<a href="#">Please fix these issues to proceed</a>'
        });
    <?php endif; ?>

    // Handle session-based flash messages that might not be in $flash variable
    <?php if (isset($_SESSION['flash'])): 
        $sFlash = $_SESSION['flash'];
        unset($_SESSION['flash']);
    ?>
        const sFlash = <?= json_encode($sFlash) ?>;
        if (sFlash.type === 'success') {
            Swal.fire({
                title: sFlash.message,
                icon: "success",
                draggable: true
            });
        } else if (sFlash.type === 'error') {
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: sFlash.message,
                footer: '<a href="#">Why do I have this issue?</a>'
            });
        }
    <?php endif; ?>
});
</script>
<script>
(function () {
    var KEY = 'appolios-theme';
    function syncThemeToggleUi() {
        var dark = document.body.classList.contains('dark-mode');
        var btn = document.getElementById('appolios-theme-toggle');
        if (btn) {
            btn.setAttribute('aria-pressed', dark ? 'true' : 'false');
            btn.setAttribute('title', dark ? 'Mode clair' : 'Mode sombre');
            var toDark = btn.querySelector('.neo-theme-toggle__icon--to-dark');
            var toLight = btn.querySelector('.neo-theme-toggle__icon--to-light');
            if (toDark) {
                toDark.hidden = dark;
            }
            if (toLight) {
                toLight.hidden = !dark;
            }
        }
        try {
            document.documentElement.classList.remove('appolios-preload-dark');
        } catch (e) {}
    }
    document.addEventListener('DOMContentLoaded', function () {
        try {
            var stored = localStorage.getItem(KEY);
            if (stored === 'dark') {
                document.body.classList.add('dark-mode');
            } else if (stored === 'light') {
                document.body.classList.remove('dark-mode');
            }
        } catch (e) {}
        syncThemeToggleUi();
        var toggle = document.getElementById('appolios-theme-toggle');
        if (toggle) {
            toggle.addEventListener('click', function () {
                var nextDark = !document.body.classList.contains('dark-mode');
                try {
                    localStorage.setItem(KEY, nextDark ? 'dark' : 'light');
                } catch (e) {}
                document.body.classList.toggle('dark-mode', nextDark);
                syncThemeToggleUi();
            });
        }
    });
})();
</script>
</body>
</html>
