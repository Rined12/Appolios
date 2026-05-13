</div>

<div class="app-footer footer">
    <div class="container app-footer-inner">
        <p>© <?= date('Y') ?> APPOLIOS. Built for professional e-learning workflows.</p>
    </div>
</div>

<script src="<?= APP_URL ?>/View/assets/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?= APP_URL ?>/View/assets/js/functions.js"></script>
<script src="<?= APP_URL ?>/View/assets/js/main.js"></script>
<script src="<?= APP_URL ?>/View/assets/js/custom-form-validation.js"></script>
<script>
(function () {
    function getActionKind(url) {
        if (!url) {
            return '';
        }
        var lower = String(url).toLowerCase();
        if (lower.indexOf('/delete') !== -1 || lower.indexOf('delete-') !== -1) {
            return 'delete';
        }
        if (lower.indexOf('/quit') !== -1) {
            return 'quit';
        }
        if (lower.indexOf('/update') !== -1 || lower.indexOf('update-') !== -1) {
            return 'update';
        }
        return '';
    }

    function getDialogCopy(kind) {
        if (kind === 'delete') {
            return {
                title: 'Delete this item?',
                text: 'This action may be permanent. Please confirm.',
                confirmText: 'Yes, delete',
                color: '#be123c'
            };
        }
        if (kind === 'quit') {
            return {
                title: 'Quit this group?',
                text: 'You can join again later if needed.',
                confirmText: 'Yes, quit',
                color: '#be123c'
            };
        }
        return {
            title: 'Confirm update?',
            text: 'Your changes will be saved.',
            confirmText: 'Yes, update',
            color: '#0f766e'
        };
    }

    function confirmWithSweetAlert(kind, onConfirm) {
        var copy = getDialogCopy(kind);
        if (!(window.Swal && typeof window.Swal.fire === 'function')) {
            if (window.confirm(copy.title)) {
                onConfirm();
            }
            return;
        }

        window.Swal.fire({
            title: copy.title,
            text: copy.text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: copy.confirmText,
            cancelButtonText: 'Cancel',
            confirmButtonColor: copy.color,
            cancelButtonColor: '#355c7d',
            reverseButtons: true,
            backdrop: 'rgba(15, 23, 42, 0.55)'
        }).then(function (result) {
            if (result && result.isConfirmed) {
                onConfirm();
            }
        });
    }

    document.addEventListener('click', function (event) {
        var link = event.target && event.target.closest ? event.target.closest('a[href]') : null;
        if (!link) {
            return;
        }
        if (link.hasAttribute('data-skip-confirm')) {
            return;
        }
        var href = link.getAttribute('href');
        if (!href || href.charAt(0) === '#') {
            return;
        }
        var kind = getActionKind(href);
        if (!kind) {
            return;
        }
        event.preventDefault();
        confirmWithSweetAlert(kind, function () {
            window.location.href = href;
        });
    });

    document.addEventListener('submit', function (event) {
        var form = event.target;
        if (!form || form.hasAttribute('data-skip-confirm')) {
            return;
        }
        var action = form.getAttribute('action') || '';
        var kind = getActionKind(action);
        if (!kind) {
            return;
        }
        event.preventDefault();
        confirmWithSweetAlert(kind, function () {
            form.setAttribute('data-skip-confirm', '1');
            form.submit();
        });
    });
})();
</script>
</body>
</html>
