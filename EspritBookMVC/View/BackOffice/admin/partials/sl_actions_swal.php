<?php
/** SweetAlert for SL admin card actions + flash toast after redirect */
$flash = $flash ?? null;
?>
<script>
(function () {
    function confirmSl(kind) {
        if (!(window.Swal && typeof Swal.fire === 'function')) {
            return Promise.resolve(window.confirm(kind === 'delete' ? 'Delete this item?' : 'Continue?'));
        }
        var cfg = {
            approve: {
                title: 'Approve?',
                text: 'This item will be marked as approved.',
                icon: 'question',
                confirmButtonText: 'Yes, approve',
                confirmButtonColor: '#166534'
            },
            reject: {
                title: 'Reject?',
                text: 'This item will be marked as rejected.',
                icon: 'warning',
                confirmButtonText: 'Yes, reject',
                confirmButtonColor: '#d97706'
            },
            delete: {
                title: 'Delete permanently?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                confirmButtonText: 'Yes, delete',
                confirmButtonColor: '#be123c'
            }
        };
        var c = cfg[kind] || cfg.delete;
        return Swal.fire({
            title: c.title,
            text: c.text,
            icon: c.icon,
            showCancelButton: true,
            confirmButtonText: c.confirmButtonText,
            cancelButtonText: 'Cancel',
            cancelButtonColor: '#64748b',
            reverseButtons: true,
            backdrop: 'rgba(15, 23, 42, 0.55)'
        }).then(function (r) {
            return !!(r && r.isConfirmed);
        });
    }

    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!form || form.tagName !== 'FORM') {
            return;
        }
        var kind = form.getAttribute('data-sl-action');
        if (!kind) {
            return;
        }
        if (form.getAttribute('data-sl-submitting') === '1') {
            form.removeAttribute('data-sl-submitting');
            return;
        }
        e.preventDefault();
        confirmSl(kind).then(function (ok) {
            if (ok) {
                form.setAttribute('data-sl-submitting', '1');
                HTMLFormElement.prototype.submit.call(form);
            }
        });
    });

    window.addEventListener('load', function () {
        <?php if (!empty($flash['type'])): ?>
        var t = <?= json_encode((string) ($flash['type'] ?? '')) ?>;
        var m = <?= json_encode((string) ($flash['message'] ?? '')) ?>;
        if (!(window.Swal && typeof Swal.fire === 'function')) {
            return;
        }
        if (t === 'success') {
            Swal.fire({ icon: 'success', title: 'Done', text: m, confirmButtonColor: '#548CA8' });
        } else if (t === 'error') {
            Swal.fire({ icon: 'error', title: 'Something went wrong', text: m, confirmButtonColor: '#548CA8' });
        }
        <?php endif; ?>
    });
})();
</script>
