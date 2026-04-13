<?php
$title = 'Modifier le groupe — APPOLIOS';
$description = 'Édition du groupe';
$studentSidebarActive = 'groupes';
$slBase = APP_URL . '/index.php?url=social-learning/';
require __DIR__ . '/../../../partials/header.php';
?>

<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/../../../student/partials/sidebar.php'; ?>

            <div class="admin-main">
                <div class="dashboard-header" style="display:flex;align-items:center;gap:12px;margin-bottom:30px;">
                    <a href="<?= $slBase ?>groupe/show/<?= (int)$groupe['id_groupe'] ?>" class="btn btn-outline" style="padding:8px 14px;">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                    </a>
                    <div>
                        <h1 style="margin:0;font-size:1.6rem;">Modifier le groupe</h1>
                        <p style="margin:4px 0 0;color:var(--gray-dark);">Mettez à jour les informations et le statut</p>
                    </div>
                </div>

                <div class="table-container" style="max-width:700px;">
                    <div class="table-header">
                        <h3 style="margin:0;">Informations du groupe</h3>
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

                        <form method="POST" action="<?= $slBase ?>groupe/update/<?= (int)$groupe['id_groupe'] ?>" id="form-edit-groupe" novalidate>

                            <div class="sl-form-group" id="fg-nom">
                                <label class="sl-label" for="nom_groupe">Nom du groupe <span style="color:#ef4444;">*</span></label>
                                <input type="text" id="nom_groupe" name="nom_groupe" class="sl-input" maxlength="100"
                                       value="<?= htmlspecialchars($groupe['nom_groupe'] ?? '') ?>" required>
                                <div class="sl-field-error" id="err-nom"></div>
                                <small style="color:var(--gray-dark);margin-top:4px;display:block;">Entre 3 et 100 caractères.</small>
                            </div>

                            <div class="sl-form-group" id="fg-desc" style="margin-top:20px;">
                                <label class="sl-label" for="description">Description <span style="color:#ef4444;">*</span></label>
                                <textarea id="description" name="description" rows="5" class="sl-input sl-textarea" required><?= htmlspecialchars($groupe['description'] ?? '') ?></textarea>
                                <div class="sl-field-error" id="err-desc"></div>
                            </div>

                            <div class="sl-form-group" style="margin-top:20px;">
                                <label class="sl-label" for="statut">Statut</label>
                                <select name="statut" id="statut" class="sl-input">
                                    <option value="actif" <?= (($groupe['statut'] ?? '') === 'actif') ? 'selected' : '' ?>>Actif</option>
                                    <option value="archivé" <?= (($groupe['statut'] ?? '') === 'archivé') ? 'selected' : '' ?>>Archivé</option>
                                </select>
                            </div>

                            <div style="display:flex;gap:12px;margin-top:28px;flex-wrap:wrap;">
                                <button type="submit" class="btn btn-primary" style="padding:12px 28px;">Enregistrer</button>
                                <a href="<?= $slBase ?>groupe/show/<?= (int)$groupe['id_groupe'] ?>" class="btn btn-outline" style="padding:12px 24px;">Annuler</a>
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
    var form = document.getElementById('form-edit-groupe');
    if (!form) return;
    form.addEventListener('submit', function(e) {
        var nom = document.getElementById('nom_groupe').value.trim();
        var desc = document.getElementById('description').value.trim();
        if (nom.length < 3 || desc.length < 10) e.preventDefault();
    });
})();
</script>

<?php require __DIR__ . '/../../../partials/footer.php'; ?>
