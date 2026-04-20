<?php
$foPrefix = $foPrefix ?? 'student';
$studentSidebarActive = $studentSidebarActive ?? 'groupes';
$old = $old ?? [];
$formData = [
    'nom_groupe' => $old['nom_groupe'] ?? ($groupe['nom_groupe'] ?? ''),
    'description' => $old['description'] ?? ($groupe['description'] ?? ''),
    'statut' => $old['statut'] ?? ($groupe['statut'] ?? 'actif'),
];
$currentCover = trim((string) ($groupe['image_url'] ?? $groupe['photo'] ?? $groupe['image'] ?? ''));
$approvalState = (string) ($groupe['approval_statut'] ?? '');
$isGroupApproved = $approvalState === 'approuve';
?>
<div class="dashboard student-events-page">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/../partials/group_discussion_sidebar.php'; ?>
            <div class="admin-main">
                <div class="dashboard-header"><h1>Edit Group</h1></div>
                <div class="form-container" style="max-width:700px;">
                    <form method="POST" action="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/groupes/<?= (int) $groupe['id_groupe'] ?>/update" enctype="multipart/form-data" novalidate id="student-groupe-edit-form">
                        <div class="form-group">
                            <label for="nom_groupe">Group Name</label>
                            <input type="text" id="nom_groupe" name="nom_groupe" value="<?= htmlspecialchars($formData['nom_groupe']) ?>">
                            <div class="field-error" data-error-for="nom_groupe" style="color:#ef4444;"><?= htmlspecialchars($errors['nom_groupe'] ?? '') ?></div>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description"><?= htmlspecialchars($formData['description']) ?></textarea>
                            <div class="field-error" data-error-for="description" style="color:#ef4444;"><?= htmlspecialchars($errors['description'] ?? '') ?></div>
                        </div>
                        <?php if ($currentCover !== ''): ?>
                        <div class="form-group">
                            <span style="display:block;margin-bottom:6px;font-weight:600;">Current photo</span>
                            <img src="<?= htmlspecialchars($currentCover) ?>" alt="Current group photo" style="max-width:100%;max-height:180px;border-radius:10px;border:1px solid #e2e8f0;">
                        </div>
                        <?php endif; ?>
                        <div class="form-group">
                            <label for="group_photo"><?= $currentCover !== '' ? 'Replace group photo' : 'Group photo (optional)' ?></label>
                            <input type="file" id="group_photo" name="group_photo">
                            <p style="margin:6px 0 0;font-size:13px;color:#64748b;">JPEG, PNG, GIF or WebP, max 2 MB. Leave empty to keep the current image.</p>
                            <div class="field-error" data-error-for="group_photo" style="color:#ef4444;"><?= htmlspecialchars($errors['group_photo'] ?? '') ?></div>
                        </div>
                        <?php if ($isGroupApproved): ?>
                        <div class="form-group">
                            <label for="statut">Status (active / archived)</label>
                            <select id="statut" name="statut">
                                <option value="actif" <?= ($formData['statut'] === 'actif') ? 'selected' : '' ?>>Active</option>
                                <option value="archivé" <?= ($formData['statut'] === 'archivé') ? 'selected' : '' ?>>Archived</option>
                            </select>
                            <div class="field-error" data-error-for="statut" style="color:#ef4444;"><?= htmlspecialchars($errors['statut'] ?? '') ?></div>
                        </div>
                        <?php else: ?>
                        <input type="hidden" name="statut" value="<?= htmlspecialchars($formData['statut']) ?>">
                        <div class="form-group" style="background:#fff7ed;border:1px solid #fed7aa;border-radius:10px;padding:12px 14px;">
                            <strong style="color:#9a3412;">Approval state</strong>
                            <p style="margin:6px 0 0;font-size:14px;color:#64748b;">
                                <?php if ($approvalState === 'rejete'): ?>
                                    This group was <strong>rejected</strong>. Your updates will be sent again as <strong>in progress (pending approval)</strong>.
                                <?php else: ?>
                                    State: <strong>in progress (pending administrator approval)</strong>. Active / archived options appear here after an admin approves the group.
                                <?php endif; ?>
                            </p>
                        </div>
                        <?php endif; ?>
                        <button class="btn btn-yellow" type="submit">Update Group</button>
                    </form>
                    <p style="margin-top:20px;padding-top:16px;border-top:1px solid #e2e8f0;">
                        <a class="btn action-btn danger" href="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/groupes/<?= (int) $groupe['id_groupe'] ?>/delete" onclick="return confirm('Supprimer ce groupe et toutes ses discussions ? Cette action est definitive.');">Delete group</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('student-groupe-edit-form');
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
                setError('group_photo', 'Image must be 2 MB or smaller.');
                hasError = true;
            }
        }
        if (nom.length === 0) {
            setError('nom_groupe', 'This field cannot be empty.');
            hasError = true;
        } else if (nom.length < 3) {
            setError('nom_groupe', 'Group name must be between 3 and 100 characters.');
            hasError = true;
        } else if (nom.length > 100) {
            setError('nom_groupe', 'Group name must not exceed 100 characters.');
            hasError = true;
        }
        if (description.length === 0) {
            setError('description', 'This field cannot be empty.');
            hasError = true;
        } else if (description.length < 10) {
            setError('description', 'Description must be between 10 and 3000 characters.');
            hasError = true;
        } else if (description.length > 3000) {
            setError('description', 'Description must not exceed 3000 characters.');
            hasError = true;
        }
        if (hasError) event.preventDefault();
    });
});
</script>
