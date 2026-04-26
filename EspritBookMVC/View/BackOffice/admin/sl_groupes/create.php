<?php
$adminSidebarActive = 'sl-groupes';
$old = $old ?? [];
?>
<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/../partials/sidebar.php'; ?>
            <div class="admin-main">
                <div class="dashboard-header"><h1>Creer un groupe</h1></div>
                <div class="form-container" style="max-width:700px;">
                    <form method="POST" action="<?= APP_ENTRY ?>?url=admin/sl-groupes/store" enctype="multipart/form-data" novalidate id="admin-groupe-create-form">
                        <div class="form-group">
                            <label for="nom_groupe">Nom du groupe</label>
                            <input type="text" id="nom_groupe" name="nom_groupe" value="<?= htmlspecialchars($old['nom_groupe'] ?? '') ?>">
                            <div class="field-error" data-error-for="nom_groupe" style="color:#ef4444;"><?= htmlspecialchars($errors['nom_groupe'] ?? '') ?></div>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description"><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
                            <div class="field-error" data-error-for="description" style="color:#ef4444;"><?= htmlspecialchars($errors['description'] ?? '') ?></div>
                        </div>
                        <div class="form-group">
                            <label for="group_photo">Photo du groupe (optionnel)</label>
                            <input type="file" id="group_photo" name="group_photo">
                            <p style="margin:6px 0 0;font-size:13px;color:#64748b;">JPEG, PNG, GIF ou WebP, 2 Mo maximum.</p>
                            <div class="field-error" data-error-for="group_photo" style="color:#ef4444;"><?= htmlspecialchars($errors['group_photo'] ?? '') ?></div>
                        </div>
                        <div class="form-group">
                            <label for="statut">Statut</label>
                            <select id="statut" name="statut">
                                <option value="actif" <?= (($old['statut'] ?? 'actif') === 'actif') ? 'selected' : '' ?>>Actif</option>
                                <option value="archivé" <?= (($old['statut'] ?? '') === 'archivé') ? 'selected' : '' ?>>Archive</option>
                            </select>
                            <div class="field-error" data-error-for="statut" style="color:#ef4444;"><?= htmlspecialchars($errors['statut'] ?? '') ?></div>
                        </div>
                        <div class="form-group">
                            <label for="approval_statut">Approbation</label>
                            <select id="approval_statut" name="approval_statut">
                                <option value="en_cours" <?= (($old['approval_statut'] ?? 'en_cours') === 'en_cours') ? 'selected' : '' ?>>En cours (validation)</option>
                                <option value="en_attente" <?= (($old['approval_statut'] ?? '') === 'en_attente') ? 'selected' : '' ?>>En attente (legacy)</option>
                                <option value="approuve" <?= (($old['approval_statut'] ?? '') === 'approuve') ? 'selected' : '' ?>>Approuve</option>
                                <option value="rejete" <?= (($old['approval_statut'] ?? '') === 'rejete') ? 'selected' : '' ?>>Rejete</option>
                            </select>
                            <div class="field-error" data-error-for="approval_statut" style="color:#ef4444;"><?= htmlspecialchars($errors['approval_statut'] ?? '') ?></div>
                        </div>
                        <button class="btn btn-yellow" type="submit">Enregistrer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('admin-groupe-create-form');
    form.addEventListener('submit', function (event) {
        const nom = form.querySelector('#nom_groupe').value.trim();
        const description = form.querySelector('#description').value.trim();
        let hasError = false;
        const setError = (name, msg) => {
            const node = form.querySelector('[data-error-for="' + name + '"]');
            if (node) node.textContent = msg;
        };
        setError('nom_groupe', '');
        setError('description', '');
        setError('group_photo', '');
        const fileInput = form.querySelector('#group_photo');
        if (fileInput && fileInput.files && fileInput.files.length) {
            const f = fileInput.files[0];
            if (f.size > 2097152) {
                setError('group_photo', 'L image ne doit pas depasser 2 Mo.');
                hasError = true;
            }
        }
        if (nom.length < 3 || nom.length > 100) {
            setError('nom_groupe', 'Le nom doit contenir entre 3 et 100 caracteres.');
            hasError = true;
        }
        if (description.length < 10 || description.length > 3000) {
            setError('description', 'La description doit contenir entre 10 et 3000 caracteres.');
            hasError = true;
        }
        if (hasError) event.preventDefault();
    });
});
</script>
