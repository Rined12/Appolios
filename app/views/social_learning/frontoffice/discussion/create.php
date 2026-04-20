<?php
$title = 'Créer une discussion — APPOLIOS';
$description = 'Nouvelle discussion';
$studentSidebarActive = 'discussions';
$slBase = APP_URL . '/index.php?url=social-learning/';
$groupes = $groupes ?? [];
require __DIR__ . '/../../../partials/header.php';
?>

<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/../../../student/partials/sidebar.php'; ?>

            <div class="admin-main">
                <div class="dashboard-header" style="display:flex;align-items:center;gap:12px;margin-bottom:30px;">
                    <a href="<?= $slBase ?>discussion" class="btn btn-outline" style="padding:8px 14px;">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                    </a>
                    <div>
                        <h1 style="margin:0;font-size:1.6rem;">Créer une discussion</h1>
                        <p style="margin:4px 0 0;color:var(--gray-dark);">Choisissez un groupe et lancez la conversation</p>
                    </div>
                </div>

                <div class="flash-message info" style="margin-bottom:20px;">
                    La discussion sera enregistrée comme <strong>demande</strong> : un administrateur devra l’accepter avant qu’elle soit publique dans le fil du groupe.
                </div>

                <div class="table-container" style="max-width:700px;">
                    <div class="table-header">
                        <h3 style="margin:0;">Nouvelle discussion</h3>
                    </div>
                    <div style="padding:32px;">

                        <?php if (!empty($errors)): ?>
                        <div class="sl-errors" style="margin-bottom:20px;">
                            <ul style="margin:0;padding-left:20px;">
                                <?php foreach ($errors as $e): ?>
                                <li><?= htmlspecialchars($e) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>

                        <form id="discussionForm" method="post" action="<?= $slBase ?>discussion/store" novalidate>

                            <div class="sl-form-group" id="fg-groupe">
                                <label class="sl-label" for="id_groupe">Groupe <span style="color:#ef4444;">*</span></label>
                                <select name="id_groupe" id="id_groupe" class="sl-input" required>
                                    <option value="">— Sélectionner un groupe —</option>
                                    <?php foreach ($groupes as $gr): ?>
                                    <option value="<?= (int)$gr['id_groupe'] ?>" <?= (string)($old['id_groupe'] ?? '') === (string)$gr['id_groupe'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($gr['nom_groupe']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="sl-field-error" id="err-groupe"></div>
                            </div>

                            <div class="sl-form-group" id="fg-titre" style="margin-top:20px;">
                                <label class="sl-label" for="titre">Titre <span style="color:#ef4444;">*</span></label>
                                <input type="text" name="titre" id="titre" class="sl-input" maxlength="200" value="<?= htmlspecialchars($old['titre'] ?? '') ?>" placeholder="Sujet de la discussion (5–200 caractères)">
                                <div class="sl-field-error" id="err-titre"></div>
                            </div>

                            <div class="sl-form-group" id="fg-contenu" style="margin-top:20px;">
                                <label class="sl-label" for="contenu">Contenu <span style="color:#ef4444;">*</span></label>
                                <textarea name="contenu" id="contenu" class="sl-input sl-textarea" rows="6" maxlength="20000" placeholder="Votre message (min. 10 caractères)…"><?= htmlspecialchars($old['contenu'] ?? '') ?></textarea>
                                <div class="sl-field-error" id="err-contenu"></div>
                            </div>

                            <div style="display:flex;gap:12px;margin-top:28px;flex-wrap:wrap;">
                                <button type="submit" class="btn btn-primary" style="padding:12px 28px;">Publier</button>
                                <a href="<?= $slBase ?>discussion" class="btn btn-outline" style="padding:12px 24px;">Annuler</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    var form = document.getElementById('discussionForm');
    if (!form) return;
    form.addEventListener('submit', function(e) {
        var g = document.getElementById('id_groupe').value;
        var t = document.getElementById('titre').value.trim();
        var c = document.getElementById('contenu').value.trim();
        if (!g || !t || !c) e.preventDefault();
    });
})();
</script>

<?php require __DIR__ . '/../../../partials/footer.php'; ?>
