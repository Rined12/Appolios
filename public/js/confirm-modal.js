/**
 * APPOLIOS — confirmations dans la page (remplace alert/confirm natifs pour les éléments marqués).
 * Liens : <a href="..." data-confirm="Message">…</a>
 * Formulaires : <button type="submit" data-confirm="Message">…</button>
 */
(function () {
    'use strict';

    function getEls() {
        var modal = document.getElementById('site-confirm-modal');
        if (!modal) {
            return null;
        }
        return {
            modal: modal,
            text: modal.querySelector('.site-confirm-text'),
            ok: document.getElementById('site-confirm-ok'),
            cancel: document.getElementById('site-confirm-cancel'),
            backdrop: modal.querySelector('.site-confirm-backdrop')
        };
    }

    document.addEventListener('DOMContentLoaded', function () {
        var els = getEls();
        var pending = null;

        function closeModal() {
            if (!els) {
                return;
            }
            els.modal.setAttribute('hidden', '');
            els.modal.setAttribute('aria-hidden', 'true');
            pending = null;
        }

        function openModal(message, onConfirm) {
            if (!els || !els.text) {
                if (window.confirm(message)) {
                    onConfirm();
                }
                return;
            }
            els.text.textContent = message;
            els.modal.removeAttribute('hidden');
            els.modal.setAttribute('aria-hidden', 'false');
            pending = onConfirm;
        }

        if (els) {
            els.ok.addEventListener('click', function () {
                var fn = pending;
                closeModal();
                if (typeof fn === 'function') {
                    fn();
                }
            });
            els.cancel.addEventListener('click', closeModal);
            if (els.backdrop) {
                els.backdrop.addEventListener('click', closeModal);
            }
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && els && !els.modal.hasAttribute('hidden')) {
                    closeModal();
                }
            });
        }

        document.body.addEventListener('click', function (e) {
            var link = e.target.closest('a[data-confirm]');
            if (!link || !link.getAttribute('href')) {
                return;
            }
            e.preventDefault();
            var msg = link.getAttribute('data-confirm') || 'Confirmer cette action ?';
            var href = link.getAttribute('href');
            openModal(msg, function () {
                window.location.href = href;
            });
        });

        document.addEventListener('submit', function (e) {
            var form = e.target;
            if (!form || form.tagName !== 'FORM') {
                return;
            }
            var sub = e.submitter;
            if (!sub || !sub.hasAttribute('data-confirm')) {
                return;
            }
            e.preventDefault();
            var msg = sub.getAttribute('data-confirm') || 'Confirmer cette action ?';
            openModal(msg, function () {
                sub.removeAttribute('data-confirm');
                if (form.requestSubmit) {
                    form.requestSubmit(sub);
                } else {
                    form.submit();
                }
            });
        });
    });
})();
