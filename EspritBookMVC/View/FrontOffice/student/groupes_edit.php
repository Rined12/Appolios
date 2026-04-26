<?php $studentSidebarActive = 'groupes'; $old = $old ?? []; ?>
<?php $cover = trim((string) ($groupe['image_url'] ?? $groupe['photo'] ?? $groupe['image'] ?? '')); ?>
<div class="dashboard student-events-page">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main">
                <div class="dashboard-header"><h1>Edit Group</h1></div>
                <div class="form-container" style="max-width:700px;">
                    <form method="POST" action="<?= APP_ENTRY ?>?url=student/groupes/<?= (int) $groupe['id_groupe'] ?>/update" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>Group Name</label>
                            <input type="text" name="nom_groupe" value="<?= htmlspecialchars($old['nom_groupe'] ?? $groupe['nom_groupe']) ?>">
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description"><?= htmlspecialchars($old['description'] ?? $groupe['description']) ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="statut">
                                <option value="actif">Active</option>
                                <option value="archivé">Archived</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Replace photo</label>
                            <input type="file" name="group_photo" accept="image/jpeg,image/png,image/gif,image/webp">
                        </div>
                        <?php if ($cover !== ''): ?>
                        <div class="form-group">
                            <label>Current photo</label>
                            <img src="<?= htmlspecialchars($cover) ?>" alt="Current group photo" style="width:100%;max-width:420px;height:180px;object-fit:cover;border-radius:10px;border:1px solid #e2e8f0;" onerror="this.style.display='none';">
                        </div>
                        <?php endif; ?>
                        <button class="btn btn-yellow" type="submit">Update Group</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
