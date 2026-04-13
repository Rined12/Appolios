<?php
/**
 * BackOffice — créer un groupe (admin)
 * $errors[], $old[]
 */
$adminSidebarActive = 'sl-groupes';
?>

<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/../partials/sidebar.php'; ?>

            <div class="admin-main">
                <div class="dashboard-header" style="display:flex;align-items:center;gap:12px;margin-bottom:30px;">
                    <a href="<?= APP_URL ?>/index.php?url=admin/sl-groupes" class="btn btn-outline" style="padding:8px 14px;">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                    </a>
                    <div>
                        <h1 style="margin:0;">Créer un groupe</h1>
                        <p style="margin:4px 0 0;color:var(--gray-dark);">Nouveau groupe Social Learning</p>
                    </div>
                </div>

                <?php if(!empty($flash)): ?>
                <div class="flash-message <?= htmlspecialchars($flash['type']) ?>" style="margin-bottom:20px;"><?= htmlspecialchars($flash['message']) ?></div>
                <?php endif; ?>

                <div class="table-container" style="max-width:700px;">
                    <div class="table-header"><h3 style="margin:0;">Informations du groupe</h3></div>
                    <div style="padding:32px;">

                        <?php if(!empty($errors)): ?>
                        <div class="sl-errors">
                            <ul style="margin:0;padding-left:20px;"><?php foreach($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
                        </div>
                        <?php endif; ?>

                        <form method="POST" action="<?= APP_URL ?>/index.php?url=admin/sl-groupes/store" id="form-admin-create-groupe" novalidate>

                            <div class="sl-form-group" id="fg-nom">
                                <label class="sl-label" for="nom_groupe">Nom du groupe <span style="color:#ef4444;">*</span></label>
                                <input type="text" id="nom_groupe" name="nom_groupe" class="sl-input"
                                       placeholder="Ex : Machine Learning Avancé"
                                       value="<?= htmlspecialchars($old['nom_groupe'] ?? '') ?>">
                                <div class="sl-field-error" id="err-nom"></div>
                            </div>

                            <div class="sl-form-group" id="fg-desc" style="margin-top:20px;">
                                <label class="sl-label" for="description">Description <span style="color:#ef4444;">*</span></label>
                                <textarea id="description" name="description" rows="5" class="sl-input sl-textarea"
                                          placeholder="Décrivez l'objectif de ce groupe…"><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
                                <div class="sl-field-error" id="err-desc"></div>
                            </div>

                            <div style="display:flex;gap:12px;margin-top:28px;flex-wrap:wrap;">
                                <button type="submit" class="btn btn-yellow" id="btn-admin-submit-groupe" style="padding:12px 28px;">Créer le groupe</button>
                                <a href="<?= APP_URL ?>/index.php?url=admin/sl-groupes" class="btn btn-outline" style="padding:12px 24px;">Annuler</a>
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
    var form = document.getElementById('form-admin-create-groupe');
    if(!form) return;
    function showErr(fg,err,msg){ document.getElementById(fg).classList.add('sl-has-error'); var e=document.getElementById(err); if(e){e.textContent=msg;e.style.display='block';} }
    function clearErr(fg,err){ document.getElementById(fg).classList.remove('sl-has-error'); var e=document.getElementById(err); if(e){e.textContent='';e.style.display='none';} }
    form.addEventListener('submit',function(ev){
        var ok=true;
        clearErr('fg-nom','err-nom'); clearErr('fg-desc','err-desc');
        var nom=document.getElementById('nom_groupe').value.trim();
        var desc=document.getElementById('description').value.trim();
        if(nom.length<3){ showErr('fg-nom','err-nom','Le nom doit contenir au moins 3 caractères.'); ok=false; }
        else if(nom.length>100){ showErr('fg-nom','err-nom','Maximum 100 caractères.'); ok=false; }
        if(desc.length<10){ showErr('fg-desc','err-desc','La description doit contenir au moins 10 caractères.'); ok=false; }
        if(!ok) ev.preventDefault();
    });
    ['nom_groupe','description'].forEach(function(id){
        var el=document.getElementById(id);
        if(el) el.addEventListener('input',function(){ clearErr(id==='nom_groupe'?'fg-nom':'fg-desc',id==='nom_groupe'?'err-nom':'err-desc'); });
    });
})();
</script>
