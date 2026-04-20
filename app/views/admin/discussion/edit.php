<?php
/**
 * BackOffice — modifier une discussion (admin)
 * $discussion, $errors[], $old[]
 */
$adminSidebarActive = 'sl-discussions';
?>

<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/../partials/sidebar.php'; ?>

            <div class="admin-main">
                <div class="dashboard-header" style="display:flex;align-items:center;gap:12px;margin-bottom:30px;">
                    <a href="<?= APP_URL ?>/index.php?url=admin/sl-discussions" class="btn btn-outline" style="padding:8px 14px;">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                    </a>
                    <div>
                        <h1 style="margin:0;">Modifier la discussion</h1>
                        <p style="margin:4px 0 0;color:var(--gray-dark);"><?= htmlspecialchars(mb_substr($discussion['titre'], 0, 60)) ?></p>
                    </div>
                </div>

                <?php if(!empty($flash)): ?>
                <div class="flash-message <?= htmlspecialchars($flash['type']) ?>" style="margin-bottom:20px;"><?= htmlspecialchars($flash['message']) ?></div>
                <?php endif; ?>

                <div class="table-container" style="max-width:700px;">
                    <div class="table-header"><h3 style="margin:0;">Modifier le contenu</h3></div>
                    <div style="padding:32px;">

                        <?php if(!empty($errors)): ?>
                        <div class="sl-errors">
                            <ul style="margin:0;padding-left:20px;"><?php foreach($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
                        </div>
                        <?php endif; ?>

                        <div style="background:rgba(99,102,241,0.07);border-radius:8px;padding:14px 18px;margin-bottom:20px;font-size:0.875rem;color:var(--gray-dark);">
                            Auteur : <strong><?= htmlspecialchars($discussion['nom_auteur']) ?></strong> ·
                            Créé le : <?= date('d/m/Y H:i', strtotime($discussion['date_creation'])) ?> ·
                            ❤️ <?= $discussion['nb_likes'] ?> likes
                        </div>

                        <form method="POST" action="<?= APP_URL ?>/index.php?url=admin/sl-discussions/<?= $discussion['id_discussion'] ?>/update" id="form-admin-edit-disc" novalidate>

                            <div class="sl-form-group" id="fg-titre">
                                <label class="sl-label" for="titre">Titre <span style="color:#ef4444;">*</span></label>
                                <input type="text" id="titre" name="titre" class="sl-input"
                                       value="<?= htmlspecialchars($old['titre'] ?? $discussion['titre']) ?>">
                                <div class="sl-field-error" id="err-titre"></div>
                            </div>

                            <div class="sl-form-group" id="fg-contenu" style="margin-top:20px;">
                                <label class="sl-label" for="contenu">Contenu <span style="color:#ef4444;">*</span></label>
                                <textarea id="contenu" name="contenu" rows="8" class="sl-input sl-textarea"><?= htmlspecialchars($old['contenu'] ?? $discussion['contenu']) ?></textarea>
                                <div class="sl-field-error" id="err-contenu"></div>
                            </div>

                            <div style="display:flex;gap:12px;margin-top:28px;flex-wrap:wrap;">
                                <button type="submit" class="btn btn-yellow" id="btn-admin-update-disc" style="padding:12px 28px;">Enregistrer</button>
                                <a href="<?= APP_URL ?>/index.php?url=admin/sl-discussions" class="btn btn-outline" style="padding:12px 24px;">Annuler</a>
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
    var form=document.getElementById('form-admin-edit-disc');
    if(!form) return;
    function showErr(fg,err,msg){document.getElementById(fg).classList.add('sl-has-error');var e=document.getElementById(err);if(e){e.textContent=msg;e.style.display='block';}}
    function clearErr(fg,err){document.getElementById(fg).classList.remove('sl-has-error');var e=document.getElementById(err);if(e){e.textContent='';e.style.display='none';}}
    form.addEventListener('submit',function(ev){
        var ok=true;
        clearErr('fg-titre','err-titre');clearErr('fg-contenu','err-contenu');
        var t=document.getElementById('titre').value.trim();
        var c=document.getElementById('contenu').value.trim();
        if(t.length<5||t.length>200){showErr('fg-titre','err-titre','Le titre doit faire entre 5 et 200 caractères.');ok=false;}
        if(c.length<10){showErr('fg-contenu','err-contenu','Le contenu doit contenir au moins 10 caractères.');ok=false;}
        if(!ok) ev.preventDefault();
    });
})();
</script>
