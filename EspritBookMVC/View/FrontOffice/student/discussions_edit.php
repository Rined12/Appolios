<?php $studentSidebarActive = 'discussions'; $old = $old ?? []; ?>
<div class="dashboard student-events-page">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main">
                <div class="dashboard-header"><h1>Edit Discussion</h1></div>
                <div class="form-container" style="max-width:760px;">
                    <form method="POST" action="<?= APP_ENTRY ?>?url=student/discussions/<?= (int) ($discussion['id_discussion'] ?? 0) ?>/update">
                        <div class="form-group">
                            <label>Group</label>
                            <select name="id_groupe">
                                <?php foreach (($groups ?? []) as $group): ?>
                                    <option value="<?= (int) $group['id_groupe'] ?>" <?= ((int) ($old['id_groupe'] ?? ($discussion['id_groupe'] ?? 0)) === (int) $group['id_groupe']) ? 'selected' : '' ?>><?= htmlspecialchars($group['nom_groupe']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div style="color:#ef4444;"><?= htmlspecialchars($errors['id_groupe'] ?? '') ?></div>
                        </div>
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" name="titre" value="<?= htmlspecialchars($old['titre'] ?? ($discussion['titre'] ?? '')) ?>">
                            <div style="color:#ef4444;"><?= htmlspecialchars($errors['titre'] ?? '') ?></div>
                        </div>
                        <div class="form-group">
                            <label>Content</label>
                            <textarea name="contenu"><?= htmlspecialchars($old['contenu'] ?? ($discussion['contenu'] ?? '')) ?></textarea>
                            <div style="color:#ef4444;"><?= htmlspecialchars($errors['contenu'] ?? '') ?></div>
                        </div>
                        <button type="submit" class="btn btn-yellow">Update Discussion</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
