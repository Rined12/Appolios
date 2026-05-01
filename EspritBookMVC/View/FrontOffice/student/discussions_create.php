<?php
$studentSidebarActive = 'discussions';
$foPrefix = $foPrefix ?? 'student';
$old = $old ?? [];
$errors = $errors ?? [];
$groups = $groups ?? [];
?>
<div class="dashboard student-events-page collab-hub">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main">
                <?php require __DIR__ . '/partials/collab_hub_styles.php'; ?>

                <header class="collab-hero">
                    <div class="collab-hero__inner">
                        <div>
                            <div class="collab-eyebrow"><i class="bi bi-pencil-square" aria-hidden="true"></i> New thread</div>
                            <h1>Create discussion</h1>
                            <p>Anchor a conversation inside an approved group you own — members will see it instantly in their hub.</p>
                        </div>
                        <div class="collab-hero-actions">
                            <a href="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/discussions" class="collab-btn-ghost">
                                <i class="bi bi-arrow-left" aria-hidden="true"></i> Back to list
                            </a>
                        </div>
                    </div>
                </header>

                <div class="collab-form-shell">
                    <?php if (empty($groups)): ?>
                        <div class="collab-alert-soft">
                            <strong style="display:block;margin-bottom:.35rem;">No eligible group yet</strong>
                            You can create discussions only inside groups you created that are already approved by an admin.
                        </div>
                    <?php endif; ?>
                    <form method="POST" action="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/discussions/store">
                        <div class="form-group">
                            <label>Group</label>
                            <select name="id_groupe">
                                <option value="">Select group</option>
                                <?php foreach ($groups as $group): ?>
                                    <option value="<?= (int) $group['id_groupe'] ?>" <?= ((int) ($old['id_groupe'] ?? 0) === (int) $group['id_groupe']) ? 'selected' : '' ?>><?= htmlspecialchars($group['nom_groupe']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (!empty($errors['id_groupe'])): ?>
                                <div style="color:#dc2626;font-size:0.85rem;font-weight:600;margin-top:0.35rem;"><?= htmlspecialchars((string) $errors['id_groupe']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" name="titre" value="<?= htmlspecialchars((string) ($old['titre'] ?? '')) ?>">
                            <?php if (!empty($errors['titre'])): ?>
                                <div style="color:#dc2626;font-size:0.85rem;font-weight:600;margin-top:0.35rem;"><?= htmlspecialchars((string) $errors['titre']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label>Content</label>
                            <textarea name="contenu"><?= htmlspecialchars((string) ($old['contenu'] ?? '')) ?></textarea>
                            <?php if (!empty($errors['contenu'])): ?>
                                <div style="color:#dc2626;font-size:0.85rem;font-weight:600;margin-top:0.35rem;"><?= htmlspecialchars((string) $errors['contenu']) ?></div>
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="collab-btn-primary" <?= empty($groups) ? 'disabled style="opacity:.5;cursor:not-allowed;"' : '' ?>>
                            <i class="bi bi-check-lg" aria-hidden="true"></i> Save discussion
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
