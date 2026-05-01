</main>

<footer class="app-footer">
    <div class="container app-footer-inner">
        <p>© <?= date('Y') ?> APPOLIOS. Built for professional e-learning workflows.</p>
    </div>
</footer>

<script src="<?= APP_URL ?>/View/assets/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= APP_URL ?>/View/assets/js/functions.js"></script>
<script src="<?= APP_URL ?>/View/assets/js/main.js"></script>

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
</body>
</html>
