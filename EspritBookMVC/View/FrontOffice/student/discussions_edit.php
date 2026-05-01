<?php
$studentSidebarActive = 'discussions';
$foPrefix = $foPrefix ?? 'student';
$discussion_edit = $discussion_edit ?? ['discussion_id' => 0, 'update_url' => '#', 'selected_group_id' => 0, 'title_value' => '', 'content_value' => ''];
$edit = $discussion_edit;
$groups = $groups ?? [];
$errors = $errors ?? [];
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
                            <div class="collab-eyebrow"><i class="bi bi-sliders" aria-hidden="true"></i> Refine thread</div>
                            <h1>Edit discussion</h1>
                            <p>Update the title, body, or hosting group — permissions still follow ownership rules.</p>
                        </div>
                        <div class="collab-hero-actions">
                            <a href="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/discussions" class="collab-btn-ghost">
                                <i class="bi bi-arrow-left" aria-hidden="true"></i> Back
                            </a>
                        </div>
                    </div>
                </header>

                <div class="collab-form-shell">
                    <form method="POST" action="<?= htmlspecialchars((string) $edit['update_url'], ENT_QUOTES, 'UTF-8') ?>">
                        <div class="form-group">
                            <label>Group</label>
                            <select name="id_groupe">
                                <?php foreach ($groups as $group): ?>
                                    <option value="<?= (int) $group['id_groupe'] ?>" <?= ((int) $edit['selected_group_id'] === (int) $group['id_groupe']) ? 'selected' : '' ?>><?= htmlspecialchars((string) $group['nom_groupe'], ENT_QUOTES, 'UTF-8') ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (!empty($errors['id_groupe'])): ?>
                                <div style="color:#dc2626;font-size:0.85rem;font-weight:600;margin-top:0.35rem;"><?= htmlspecialchars((string) $errors['id_groupe'], ENT_QUOTES, 'UTF-8') ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" name="titre" value="<?= htmlspecialchars((string) $edit['title_value'], ENT_QUOTES, 'UTF-8') ?>">
                            <?php if (!empty($errors['titre'])): ?>
                                <div style="color:#dc2626;font-size:0.85rem;font-weight:600;margin-top:0.35rem;"><?= htmlspecialchars((string) $errors['titre'], ENT_QUOTES, 'UTF-8') ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label>Content</label>
                            <textarea name="contenu"><?= htmlspecialchars((string) $edit['content_value'], ENT_QUOTES, 'UTF-8') ?></textarea>
                            <?php if (!empty($errors['contenu'])): ?>
                                <div style="color:#dc2626;font-size:0.85rem;font-weight:600;margin-top:0.35rem;"><?= htmlspecialchars((string) $errors['contenu'], ENT_QUOTES, 'UTF-8') ?></div>
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="collab-btn-primary">
                            <i class="bi bi-arrow-repeat" aria-hidden="true"></i> Update discussion
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
