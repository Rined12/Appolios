<?php $studentSidebarActive = 'discussions'; $old = $old ?? []; ?>
<div class="dashboard student-events-page">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main">
                <div class="dashboard-header"><h1>Create Discussion</h1></div>
                <div class="form-container" style="max-width:760px;">
                    <?php if (empty($groups)): ?>
                        <div class="table-container" style="margin-bottom:12px;background:#fff7ed;border:1px solid #fed7aa;">
                            You can create discussions only inside groups you created and that are approved.
                        </div>
                    <?php endif; ?>
                    <form method="POST" action="<?= APP_ENTRY ?>?url=student/discussions/store">
                        <div class="form-group">
                            <label>Group</label>
                            <select name="id_groupe">
                                <option value="">Select group</option>
                                <?php foreach (($groups ?? []) as $group): ?>
                                    <option value="<?= (int) $group['id_groupe'] ?>" <?= ((int) ($old['id_groupe'] ?? 0) === (int) $group['id_groupe']) ? 'selected' : '' ?>><?= htmlspecialchars($group['nom_groupe']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div style="color:#ef4444;"><?= htmlspecialchars($errors['id_groupe'] ?? '') ?></div>
                        </div>
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" name="titre" value="<?= htmlspecialchars($old['titre'] ?? '') ?>">
                            <div style="color:#ef4444;"><?= htmlspecialchars($errors['titre'] ?? '') ?></div>
                        </div>
                        <div class="form-group">
                            <label>Content</label>
                            <textarea name="contenu"><?= htmlspecialchars($old['contenu'] ?? '') ?></textarea>
                            <div style="color:#ef4444;"><?= htmlspecialchars($errors['contenu'] ?? '') ?></div>
                        </div>
                        <button type="submit" class="btn btn-yellow">Save Discussion</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
