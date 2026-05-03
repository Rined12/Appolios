<?php $adminSidebarActive = 'sl-discussions'; $old = $old ?? []; $errors = $errors ?? []; $groups = $groups ?? []; ?>
<div class="dashboard student-events-page">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/../partials/sidebar.php'; ?>
            <div class="admin-main">
                <div class="dashboard-header"><h1>Create Discussion</h1></div>
                <div class="table-container" style="max-width:780px;">
                    <form method="POST" action="<?= APP_ENTRY ?>?url=admin/sl-discussions/store" novalidate style="padding:18px;">
                        <div style="margin-bottom:12px;">
                            <label>Group (approved)</label>
                            <?php $selectedGroup = (int) ($old['id_groupe'] ?? 0); ?>
                            <select name="id_groupe" data-js-required="1" style="width:100%;">
                                <option value="">Select group</option>
                                <?php foreach ($groups as $group): ?>
                                    <option value="<?= (int) ($group['id_groupe'] ?? 0) ?>" <?= $selectedGroup === (int) ($group['id_groupe'] ?? 0) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars((string) ($group['nom_groupe'] ?? '')) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (!empty($errors['id_groupe'])): ?><small style="color:#b91c1c;"><?= htmlspecialchars((string) $errors['id_groupe']) ?></small><?php endif; ?>
                        </div>
                        <div style="margin-bottom:12px;">
                            <label>Title</label>
                            <input type="text" name="titre" value="<?= htmlspecialchars((string) ($old['titre'] ?? '')) ?>" data-js-required="1" style="width:100%;">
                            <?php if (!empty($errors['titre'])): ?><small style="color:#b91c1c;"><?= htmlspecialchars((string) $errors['titre']) ?></small><?php endif; ?>
                        </div>
                        <div style="margin-bottom:12px;">
                            <label>Content</label>
                            <textarea name="contenu" rows="6" data-js-required="1" style="width:100%;"><?= htmlspecialchars((string) ($old['contenu'] ?? '')) ?></textarea>
                            <?php if (!empty($errors['contenu'])): ?><small style="color:#b91c1c;"><?= htmlspecialchars((string) $errors['contenu']) ?></small><?php endif; ?>
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
                        <button class="btn btn-primary" type="submit">Save Discussion</button>
                        <a class="btn btn-outline" href="<?= APP_ENTRY ?>?url=admin/sl-discussions">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
