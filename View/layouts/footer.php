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
<style>
#page-loader { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.95); z-index: 99999; justify-content: center; align-items: center; flex-direction: column; }
#page-loader.active { display: flex; }
#page-loader .spinner { width: 40px; height: 40px; border: 4px solid #e2e8f0; border-top-color: #3b82f6; border-radius: 50%; animation: spin 1s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }
</style>
<div id="page-loader"><div class="spinner"></div><p style="margin-top:1rem;color:#64748b;">Loading...</p></div>
<script>
    window.APP_URL = "<?= addslashes(APP_URL) ?>";
    window.addEventListener('beforeunload', function() {
        document.getElementById('page-loader').classList.add('active');
    });
    window.addEventListener('load', function() {
        document.getElementById('page-loader').classList.remove('active');
    });
    // Auto hide after 5 seconds just in case
    setTimeout(function(){ document.getElementById('page-loader').classList.remove('active'); }, 5000);
    window.showLoading = function(message) {
        document.querySelector('#page-loader p').textContent = message || 'Loading...';
        document.getElementById('page-loader').classList.add('active');
    };
    window.hideLoading = function() {
        document.getElementById('page-loader').classList.remove('active');
    };
</script>
<script src="<?= APP_URL ?>/View/assets/js/chatbot.js"></script>
</body>
</html>
