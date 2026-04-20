<?php
/**
 * FrontOffice — créer une discussion
 * $groupe, $errors[], $old[]
 */
$studentSidebarActive = 'groupes';
?>

<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/../../partials/sidebar_student.php'; ?>

            <div class="admin-main">
                <div class="dashboard-header" style="display:flex;align-items:center;gap:12px;margin-bottom:30px;">
                    <a href="<?= APP_URL ?>/index.php?url=<?= $slGroupesUrlPrefix ?>/<?= $groupe['id_groupe'] ?>" class="btn btn-outline" style="padding:8px 14px;">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                    </a>
                    <div>
                        <h1 style="margin:0;font-size:1.6rem;">Nouvelle discussion</h1>
                        <p style="margin:4px 0 0;color:var(--gray-dark);">Dans le groupe : <strong><?= htmlspecialchars($groupe['nom_groupe']) ?></strong></p>
                    </div>
                </div>

                <?php if(!empty($flash)): ?>
                <div class="flash-message <?= htmlspecialchars($flash['type']) ?>" style="margin-bottom:20px;"><?= htmlspecialchars($flash['message']) ?></div>
                <?php endif; ?>

                <div class="flash-message info" style="margin-bottom:20px;">
                    La discussion sera enregistrée comme <strong>demande</strong> : un administrateur devra l’accepter avant qu’elle soit visible par tous les membres du groupe.
                </div>

                <div class="table-container" style="max-width:700px;">
                    <div class="table-header"><h3 style="margin:0;">Démarrer une discussion</h3></div>
                    <div style="padding:32px;">

                        <?php if(!empty($errors)): ?>
                        <div class="sl-errors">
                            <ul style="margin:0;padding-left:20px;">
                                <?php foreach($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>

                        <form method="POST" action="<?= APP_URL ?>/index.php?url=<?= $slGroupesUrlPrefix ?>/<?= $groupe['id_groupe'] ?>/discussions/store" id="form-create-disc" novalidate>

                            <div class="sl-form-group" id="fg-titre">
                                <label class="sl-label" for="titre">Titre <span style="color:#ef4444;">*</span></label>
                                <input type="text" id="titre" name="titre" class="sl-input"
                                       placeholder="Résumez votre sujet en une phrase…"
                                       value="<?= htmlspecialchars($old['titre'] ?? '') ?>">
                                <div class="sl-field-error" id="err-titre"></div>
                                <small style="color:var(--gray-dark);margin-top:4px;display:block;">Minimum 5 caractères, maximum 200.</small>
                            </div>

                            <div class="sl-form-group" id="fg-contenu" style="margin-top:20px;">
                                <label class="sl-label" for="contenu">Contenu <span style="color:#ef4444;">*</span></label>
                                <textarea id="contenu" name="contenu" rows="7" class="sl-input sl-textarea"
                                          placeholder="Développez votre sujet…"><?= htmlspecialchars($old['contenu'] ?? '') ?></textarea>
                                <div class="sl-field-error" id="err-contenu"></div>
                                <small style="color:var(--gray-dark);margin-top:4px;display:block;">Minimum 10 caractères.</small>
                            </div>

                            <div style="display:flex;gap:12px;margin-top:28px;flex-wrap:wrap;">
                                <button type="submit" class="btn btn-primary" id="btn-submit-disc" style="padding:12px 28px;">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:6px;"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                                    Publier la discussion
                                </button>
                                <a href="<?= APP_URL ?>/index.php?url=<?= $slGroupesUrlPrefix ?>/<?= $groupe['id_groupe'] ?>" class="btn btn-outline" style="padding:12px 24px;">Annuler</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function(){
    'use strict';
    var form = document.getElementById('form-create-disc');
    if(!form) return;

    function showErr(fgId, errId, msg){
        document.getElementById(fgId).classList.add('sl-has-error');
        var e = document.getElementById(errId);
        if(e){ e.textContent=msg; e.style.display='block'; }
    }
    function clearErr(fgId, errId){
        document.getElementById(fgId).classList.remove('sl-has-error');
        var e = document.getElementById(errId);
        if(e){ e.textContent=''; e.style.display='none'; }
    }

    form.addEventListener('submit', function(ev){
        var ok = true;
        clearErr('fg-titre','err-titre');
        clearErr('fg-contenu','err-contenu');

        var titre   = document.getElementById('titre').value.trim();
        var contenu = document.getElementById('contenu').value.trim();

        if(titre === ''){
            showErr('fg-titre','err-titre','Le titre est obligatoire.'); ok=false;
        } else if(titre.length < 5){
            showErr('fg-titre','err-titre','Le titre doit contenir au moins 5 caractères.'); ok=false;
        } else if(titre.length > 200){
            showErr('fg-titre','err-titre','Le titre ne peut pas dépasser 200 caractères.'); ok=false;
        }

        if(contenu === ''){
            showErr('fg-contenu','err-contenu','Le contenu est obligatoire.'); ok=false;
        } else if(contenu.length < 10){
            showErr('fg-contenu','err-contenu','Le contenu doit contenir au moins 10 caractères.'); ok=false;
        }

        if(!ok) ev.preventDefault();
    });

    ['titre','contenu'].forEach(function(id){
        var el = document.getElementById(id);
        if(el) el.addEventListener('input', function(){
            clearErr('fg-'+id, 'err-'+id);
        });
    });
})();
</script>
