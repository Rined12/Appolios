<?php
$foPrefix = $foPrefix ?? 'student';
$studentSidebarActive = $studentSidebarActive ?? 'discussions';
?>

<div class="dashboard student-events-page">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/../partials/group_discussion_sidebar.php'; ?>
            <div class="admin-main">
                <div class="dashboard-header" style="display:flex;justify-content:space-between;align-items:center;gap:12px;">
                    <div>
                    <h1>Discussions</h1>
                    <p>Start and follow group discussions from one place.</p>
                    </div>
                    <a href="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/discussions/create" class="btn btn-yellow">Create Discussion</a>
                </div>

                <?php if (!empty($discussions)): ?>
                    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px;">
                        <?php foreach ($discussions as $discussion): ?>
                            <article style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:14px;">
                                <h3 style="margin:0 0 6px 0;color:#1e293b;"><?= htmlspecialchars($discussion['titre'] ?? 'Discussion') ?></h3>
                                <p style="margin:0 0 10px 0;color:#64748b;min-height:42px;"><?= htmlspecialchars(substr((string) ($discussion['contenu'] ?? ''), 0, 180)) ?></p>
                                <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:10px;">
                                    <?php
                                        $dap = (string) ($discussion['approval_statut'] ?? $discussion['approval_status'] ?? 'approuve');
                                        $dapLabel = $dap === 'approuve' ? 'Approved' : ($dap === 'rejete' ? 'Rejected' : 'In progress (admin)');
                                        $dapBg = $dap === 'approuve' ? '#dcfce7' : ($dap === 'rejete' ? '#fee2e2' : '#ffedd5');
                                        $dapColor = $dap === 'approuve' ? '#166534' : ($dap === 'rejete' ? '#991b1b' : '#9a3412');
                                    ?>
                                    <span style="font-size:12px;padding:4px 8px;border-radius:999px;background:<?= $dapBg ?>;color:<?= $dapColor ?>;"><?= htmlspecialchars($dapLabel) ?></span>
                                    <span style="font-size:12px;padding:4px 8px;border-radius:999px;background:#f1f5f9;color:#475569;">Group: <?= htmlspecialchars($discussion['nom_groupe'] ?? 'N/A') ?></span>
                                </div>
                                <div style="display:flex;gap:8px;">
                                    <a href="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/discussions/<?= (int) ($discussion['id_discussion'] ?? 0) ?>/edit" class="btn btn-secondary action-btn">Edit</a>
                                    <a href="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/discussions/<?= (int) ($discussion['id_discussion'] ?? 0) ?>/delete" class="btn action-btn danger" onclick="return confirm('Supprimer cette discussion ?')">Delete</a>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="table-container" style="padding: 20px;">
                        <p style="margin: 0; color: #475569;">No discussions yet. Create your first discussion.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
