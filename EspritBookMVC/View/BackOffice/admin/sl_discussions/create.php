<?php $adminSidebarActive = 'sl-discussions'; $old = $old ?? []; $errors = $errors ?? []; $groups = $groups ?? []; ?>
<div class="dashboard student-events-page collab-hub sl-admin--discussions">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/../partials/sidebar.php'; ?>
            <div class="admin-main">
                <?php require __DIR__ . '/../../../FrontOffice/student/partials/collab_hub_styles.php'; ?>
                <style>
                    .sl-admin--discussions .collab-form-shell .form-group { margin-bottom: 1.05rem; }
                    .sl-admin--discussions .collab-form-shell select,
                    .sl-admin--discussions .collab-form-shell input[type="text"],
                    .sl-admin--discussions .collab-form-shell textarea { width: 100%; box-sizing: border-box; }
                </style>

                <div class="header collab-hero">
                    <div class="collab-hero__inner">
                        <div>
                            <div class="collab-eyebrow"><i class="bi bi-pencil-square" aria-hidden="true"></i> Admin</div>
                            <h1>Create discussion</h1>
                            <p>Attach a thread to an approved group and set its moderation state.</p>
                        </div>
                        <div class="collab-hero-actions">
                            <a href="<?= APP_ENTRY ?>?url=admin/sl-discussions" class="collab-btn-ghost">
                                <i class="bi bi-arrow-left" aria-hidden="true"></i> Back to list
                            </a>
                        </div>
                    </div>
                </div>

                <div class="collab-form-shell">
                    <form method="POST" action="<?= APP_ENTRY ?>?url=admin/sl-discussions/store" novalidate>
                        <div class="form-group">
                            <label>Group (approved)</label>
                            <?php $selectedGroup = (int) ($old['id_groupe'] ?? 0); ?>
                            <select name="id_groupe" data-js-required="1">
                                <option value="">Select group</option>
                                <?php foreach ($groups as $group): ?>
                                    <option value="<?= (int) ($group['id_groupe'] ?? 0) ?>" <?= $selectedGroup === (int) ($group['id_groupe'] ?? 0) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars((string) ($group['nom_groupe'] ?? '')) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (!empty($errors['id_groupe'])): ?>
                                <div style="color:#dc2626;font-size:0.85rem;font-weight:600;margin-top:0.35rem;"><?= htmlspecialchars((string) $errors['id_groupe']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" name="titre" value="<?= htmlspecialchars((string) ($old['titre'] ?? '')) ?>" data-js-required="1">
                            <?php if (!empty($errors['titre'])): ?>
                                <div style="color:#dc2626;font-size:0.85rem;font-weight:600;margin-top:0.35rem;"><?= htmlspecialchars((string) $errors['titre']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label>Content</label>
                            <textarea name="contenu" rows="6" data-js-required="1"><?= htmlspecialchars((string) ($old['contenu'] ?? '')) ?></textarea>
                            <?php if (!empty($errors['contenu'])): ?>
                                <div style="color:#dc2626;font-size:0.85rem;font-weight:600;margin-top:0.35rem;"><?= htmlspecialchars((string) $errors['contenu']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label>Approval status</label>
                            <?php $s = (string) ($old['approval_statut'] ?? 'en_cours'); ?>
                            <select name="approval_statut">
                                <option value="en_cours" <?= $s === 'en_cours' ? 'selected' : '' ?>>en_cours</option>
                                <option value="approuve" <?= $s === 'approuve' ? 'selected' : '' ?>>approuve</option>
                                <option value="rejete" <?= $s === 'rejete' ? 'selected' : '' ?>>rejete</option>
                            </select>
                            <?php if (!empty($errors['approval_statut'])): ?>
                                <div style="color:#dc2626;font-size:0.85rem;font-weight:600;margin-top:0.35rem;"><?= htmlspecialchars((string) $errors['approval_statut']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div style="display:flex;flex-wrap:wrap;gap:0.65rem;align-items:center;margin-top:0.5rem;">
                            <button class="collab-btn-primary" type="submit">
                                <i class="bi bi-check-lg" aria-hidden="true"></i> Save discussion
                            </button>
                            <a class="collab-chip-btn collab-chip-btn--muted" href="<?= APP_ENTRY ?>?url=admin/sl-discussions">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
