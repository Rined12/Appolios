<?php
/**
 * APPOLIOS - Teacher Manage Discussions
 */
$teacherSidebarActive = 'sl-discussions';
$rows = $discussions ?? [];
?>

<div class="dashboard" style="padding: 2rem;">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main">
                <div style="margin-bottom: 2rem; display:flex; justify-content: space-between; align-items:flex-end; gap: 12px; flex-wrap: wrap;">
                    <div>
                        <h1 style="margin:0 0 .35rem 0; font-size: 1.8rem; font-weight: 900; color:#1e293b;">Manage discussions</h1>
                        <p style="margin:0; color:#64748b;">Your discussions created inside groups.</p>
                    </div>
                    <div style="display:flex; gap: 10px; flex-wrap: wrap;">
                        <a class="btn-admin btn-admin-primary" href="<?= APP_ENTRY ?>?url=student/discussions/create" style="text-decoration:none;">
                            <i class="bi bi-plus-lg"></i> Create discussion
                        </a>
                    </div>
                </div>

                <?php if (!empty($flash)): ?>
                    <div class="admin-card" style="padding: 1rem 1.25rem; margin-bottom: 1.25rem; border-left: 4px solid <?= ($flash['type'] ?? '') === 'success' ? '#22c55e' : '#ef4444' ?>;">
                        <div style="font-weight: 800; color: #0f172a;">
                            <?= htmlspecialchars((string) ($flash['message'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.25rem;">
                    <?php foreach ($rows as $d): ?>
                        <?php
                            $id = (int) ($d['id_discussion'] ?? 0);
                            $approval = (string) ($d['approval_statut'] ?? 'approuve');
                        ?>
                        <div class="admin-card" style="padding: 1.25rem;">
                            <div style="display:flex; justify-content: space-between; gap: 10px; align-items:flex-start;">
                                <div>
                                    <div style="font-weight: 900; color:#0f172a; font-size: 1.05rem;">
                                        <?= htmlspecialchars((string) ($d['titre'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                    <div style="color:#64748b; font-weight: 800; font-size: .85rem; margin-top: .2rem;">
                                        <?= htmlspecialchars((string) ($d['nom_groupe'] ?? ''), ENT_QUOTES, 'UTF-8') ?> · <?= htmlspecialchars($approval, ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                </div>
                                <a class="btn-admin" style="background:white;border:1px solid #e2e8f0;color:#475569;text-decoration:none;" href="<?= APP_ENTRY ?>?url=student/discussions/chat/<?= $id ?>">
                                    Live chat
                                </a>
                            </div>
                            <p style="margin:.75rem 0 0 0; color:#64748b; line-height:1.5;">
                                <?= htmlspecialchars((string) ($d['contenu'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($rows)): ?>
                        <div class="admin-card" style="padding: 2rem; text-align:center; color:#64748b; grid-column: 1 / -1;">No discussions yet.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
