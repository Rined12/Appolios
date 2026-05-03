<?php
// Canonical nested view path (replaces flat file; identical markup to former flat sibling).
$old = $old ?? [];
$errors = $errors ?? [];
?>
<div class="dashboard student-events-page">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/../partials/sidebar.php'; ?>
            <div class="admin-main">
                <div class="dashboard-header"><h1>Create Group</h1></div>
                <div class="form-container" style="max-width:700px;">
                    <form method="POST" action="<?= APP_ENTRY ?>?url=student/groupes/store" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>Group Name</label>
                            <input type="text" name="nom_groupe" value="<?= htmlspecialchars($old['nom_groupe'] ?? '') ?>">
                            <div style="color:#ef4444;"><?= htmlspecialchars($errors['nom_groupe'] ?? '') ?></div>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description"><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
                            <div style="color:#ef4444;"><?= htmlspecialchars($errors['description'] ?? '') ?></div>
                        </div>
                        <div class="form-group">
                            <label>Group photo (optional)</label>
                            <input type="file" name="group_photo" accept="image/jpeg,image/png,image/gif,image/webp">
                            <div style="color:#ef4444;"><?= htmlspecialchars($errors['group_photo'] ?? '') ?></div>
                        </div>
                        <button class="btn btn-yellow" type="submit">Save Group</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
