<?php
/**
 * BackOffice — modifier un groupe (admin)
 * $groupe, $errors[], $old[]
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
                        <h1 style="margin:0;">Modifier le groupe</h1>
                        <p style="margin:4px 0 0;color:var(--gray-dark);"><?= htmlspecialchars($groupe['nom_groupe']) ?></p>
                    </div>
                </div>

                <?php if(!empty($flash)): ?>
                <div class="flash-message <?= htmlspecialchars($flash['type']) ?>" style="margin-bottom:20px;"><?= htmlspecialchars($flash['message']) ?></div>
                <?php endif; ?>

                <div class="table-container" style="max-width:700px;">
                    <div class="table-header"><h3 style="margin:0;">Modifier les informations</h3></div>
                    <div style="padding:32px;">

                        <?php if(!empty($errors)): ?>
                        <div class="sl-errors">
                            <ul style="margin:0;padding-left:20px;"><?php foreach($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
                        </div>
                        <?php endif; ?>

                        <form method="POST" action="<?= APP_URL ?>/index.php?url=admin/sl-groupes/<?= $groupe['id_groupe'] ?>/update" id="form-admin-edit-groupe" novalidate>

                            <div class="sl-form-group" id="fg-nom">
                                <label class="sl-label" for="nom_groupe">Nom du groupe <span style="color:#ef4444;">*</span></label>
                                <input type="text" id="nom_groupe" name="nom_groupe" class="sl-input"
                                       value="<?= htmlspecialchars($old['nom_groupe'] ?? $groupe['nom_groupe']) ?>">
                                <div class="sl-field-error" id="err-nom"></div>
                            </div>

                            <div class="sl-form-group" id="fg-desc" style="margin-top:20px;">
                                <label class="sl-label" for="description">Description <span style="color:#ef4444;">*</span></label>
                                <textarea id="description" name="description" rows="5" class="sl-input sl-textarea"><?= htmlspecialchars($old['description'] ?? $groupe['description']) ?></textarea>
                                <div class="sl-field-error" id="err-desc"></div>
                            </div>

                            <div class="sl-form-group" style="margin-top:20px;">
                                <label class="sl-label" for="statut">Statut</label>
                                <select id="statut" name="statut" class="sl-input">
                                    <?php $st = $old['statut'] ?? $groupe['statut']; ?>
                                    <option value="actif"   <?= $st==='actif'   ?'selected':'' ?>>Actif</option>
                                    <option value="archivé" <?= $st==='archivé' ?'selected':'' ?>>Archivé</option>
                                </select>
                            </div>

                            <div style="display:flex;gap:12px;margin-top:28px;flex-wrap:wrap;">
                                <button type="submit" class="btn btn-yellow" id="btn-admin-update-groupe" style="padding:12px 28px;">Enregistrer</button>
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
    var form=document.getElementById('form-admin-edit-groupe');
    if(!form) return;
    function showErr(fg,err,msg){document.getElementById(fg).classList.add('sl-has-error');var e=document.getElementById(err);if(e){e.textContent=msg;e.style.display='block';}}
    function clearErr(fg,err){document.getElementById(fg).classList.remove('sl-has-error');var e=document.getElementById(err);if(e){e.textContent='';e.style.display='none';}}
    form.addEventListener('submit',function(ev){
        var ok=true;
        clearErr('fg-nom','err-nom');clearErr('fg-desc','err-desc');
        var nom=document.getElementById('nom_groupe').value.trim();
        var desc=document.getElementById('description').value.trim();
        if(nom.length<3){showErr('fg-nom','err-nom','Minimum 3 caractères.');ok=false;}
        else if(nom.length>100){showErr('fg-nom','err-nom','Maximum 100 caractères.');ok=false;}
        if(desc.length<10){showErr('fg-desc','err-desc','Minimum 10 caractères.');ok=false;}
        if(!ok) ev.preventDefault();
    });
})();
</script>
