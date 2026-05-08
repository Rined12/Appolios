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
    // Premium SweetAlert defaults
    const premiumSwal = Swal.mixin({
        customClass: {
            popup: 'premium-swal-popup',
            confirmButton: 'premium-swal-confirm',
            cancelButton: 'premium-swal-cancel',
            title: 'premium-swal-title',
            htmlContainer: 'premium-swal-text'
        },
        buttonsStyling: false
    });

    <?php if (isset($flash) && $flash): ?>
        const flash = <?= json_encode($flash) ?>;
        if (flash.type === 'success') {
            premiumSwal.fire({
                title: flash.message,
                icon: "success",
                draggable: true
            });
        } else if (flash.type === 'error') {
            premiumSwal.fire({
                icon: "error",
                title: "Oops...",
                text: flash.message,
                footer: '<a href="#">Why do I have this issue?</a>'
            });
        }
    <?php endif; ?>

    <?php if (isset($errors) && !empty($errors)): ?>
        const errors = <?= json_encode($errors) ?>;
        premiumSwal.fire({
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
            premiumSwal.fire({
                title: sFlash.message,
                icon: "success",
                draggable: true
            });
        } else if (sFlash.type === 'error') {
            premiumSwal.fire({
                icon: "error",
                title: "Oops...",
                text: sFlash.message,
                footer: '<a href="#">Why do I have this issue?</a>'
            });
        }
    <?php endif; ?>
});
</script>
<style>
/* Premium SweetAlert2 Styles */
.premium-swal-popup {
    border-radius: 20px !important;
    padding: 2rem !important;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1) !important;
    font-family: 'Inter', system-ui, -apple-system, sans-serif !important;
}
.premium-swal-title {
    font-size: 1.5rem !important;
    font-weight: 700 !important;
    color: #1e293b !important;
}
.premium-swal-text {
    color: #475569 !important;
    font-size: 1.05rem !important;
}
.premium-swal-confirm {
    background: linear-gradient(135deg, #548CA8, #3b6b85) !important;
    color: white !important;
    border: none !important;
    border-radius: 12px !important;
    padding: 12px 30px !important;
    font-weight: 600 !important;
    font-size: 1.1rem !important;
    cursor: pointer !important;
    transition: all 0.3s ease !important;
    box-shadow: 0 4px 15px rgba(84, 140, 168, 0.3) !important;
    margin: 10px !important;
    outline: none !important;
}
.premium-swal-confirm:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 20px rgba(84, 140, 168, 0.4) !important;
}
.premium-swal-cancel {
    background: #f1f5f9 !important;
    color: #64748b !important;
    border: none !important;
    border-radius: 12px !important;
    padding: 12px 30px !important;
    font-weight: 600 !important;
    font-size: 1.1rem !important;
    cursor: pointer !important;
    transition: all 0.3s ease !important;
    margin: 10px !important;
    outline: none !important;
}
.premium-swal-cancel:hover {
    background: #e2e8f0 !important;
    color: #475569 !important;
}
</style>
</body>
</html>
