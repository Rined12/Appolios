<?php
/**
 * Modale de confirmation (remplace window.confirm) — inclus une fois avant les scripts en bas de page.
 */
?>
<style>
#site-confirm-modal.site-confirm-modal {
    position: fixed;
    inset: 0;
    z-index: 10050;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
}
#site-confirm-modal.site-confirm-modal[hidden] {
    display: none !important;
}
#site-confirm-modal .site-confirm-backdrop {
    position: absolute;
    inset: 0;
    background: rgba(15, 23, 42, 0.55);
    cursor: default;
}
body.light #site-confirm-modal .site-confirm-backdrop {
    background: rgba(15, 23, 42, 0.45);
}
#site-confirm-modal .site-confirm-dialog {
    position: relative;
    width: 100%;
    max-width: 420px;
    padding: 24px;
    border-radius: 12px;
    background: #ffffff;
    color: #0a1f44;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.25);
    border: 1px solid #e5e7eb;
}
#site-confirm-modal .site-confirm-heading {
    margin: 0 0 12px;
    font-size: 1.15rem;
    font-weight: 600;
}
#site-confirm-modal .site-confirm-text {
    margin: 0 0 20px;
    line-height: 1.5;
    font-size: 0.95rem;
}
#site-confirm-modal .site-confirm-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: flex-end;
}
</style>
<div id="site-confirm-modal" class="site-confirm-modal" hidden aria-hidden="true">
    <div class="site-confirm-backdrop" tabindex="-1"></div>
    <div class="site-confirm-dialog" role="dialog" aria-modal="true" aria-labelledby="site-confirm-heading">
        <h3 id="site-confirm-heading" class="site-confirm-heading">Confirmation</h3>
        <p class="site-confirm-text"></p>
        <div class="site-confirm-actions">
            <button type="button" class="btn btn-outline" id="site-confirm-cancel">Annuler</button>
            <button type="button" class="btn btn-yellow" id="site-confirm-ok">Confirmer</button>
        </div>
    </div>
</div>
