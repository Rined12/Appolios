<?php $adminSidebarActive = 'sl-groupes'; $old = $old ?? []; $errors = $errors ?? []; ?>
<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main">
                <div class="dashboard-header"><h1>Create Group</h1></div>
                <div class="table-container" style="max-width:780px;">
                    <form method="POST" action="<?= APP_ENTRY ?>?url=admin/sl-groupes/store" novalidate style="padding:18px;">
                        <div style="margin-bottom:12px;">
                            <label>Group Name</label>
                            <input type="text" name="nom_groupe" value="<?= htmlspecialchars((string) ($old['nom_groupe'] ?? '')) ?>" data-js-required="1" style="width:100%;">
                            <?php if (!empty($errors['nom_groupe'])): ?><small style="color:#b91c1c;"><?= htmlspecialchars((string) $errors['nom_groupe']) ?></small><?php endif; ?>
                        </div>
                        <div style="margin-bottom:12px;">
                            <label>Description</label>
                            <textarea name="description" rows="5" data-js-required="1" style="width:100%;"><?= htmlspecialchars((string) ($old['description'] ?? '')) ?></textarea>
                            <?php if (!empty($errors['description'])): ?><small style="color:#b91c1c;"><?= htmlspecialchars((string) $errors['description']) ?></small><?php endif; ?>
                        </div>
                        <div style="margin-bottom:16px;">
                            <label>Approval Status</label>
                            <?php $s = (string) ($old['approval_statut'] ?? 'en_cours'); ?>
                            <select name="approval_statut" style="width:100%;">
                                <option value="en_cours" <?= $s === 'en_cours' ? 'selected' : '' ?>>en_cours</option>
                                <option value="approuve" <?= $s === 'approuve' ? 'selected' : '' ?>>approuve</option>
                                <option value="rejete" <?= $s === 'rejete' ? 'selected' : '' ?>>rejete</option>
                            </select>
                            <?php if (!empty($errors['approval_statut'])): ?><small style="color:#b91c1c;"><?= htmlspecialchars((string) $errors['approval_statut']) ?></small><?php endif; ?>
                        </div>
                        <button class="btn btn-primary" type="submit">Save Group</button>
                        <a class="btn btn-outline" href="<?= APP_ENTRY ?>?url=admin/sl-groupes">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
