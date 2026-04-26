<?php
$studentSidebarActive = 'discussions';
$currentUserId = (int) ($currentUserId ?? ($_SESSION['user_id'] ?? 0));
?>
<div class="dashboard student-events-page">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main">
                <style>
                    .discussion-box-grid {
                        display: grid;
                        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
                        gap: 16px;
                    }
                    .discussion-box-card {
                        background: #fff;
                        border: 1px solid #e2e8f0;
                        border-radius: 14px;
                        box-shadow: 0 2px 12px rgba(15, 23, 42, 0.06);
                        padding: 14px;
                        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
                    }
                    .discussion-box-card:hover {
                        transform: translateY(-4px);
                        border-color: #cbd5e1;
                        box-shadow: 0 12px 24px rgba(15, 23, 42, 0.12);
                    }
                    .discussion-box-card h4 {
                        margin: 0 0 6px 0;
                        color: #1e293b;
                        font-size: 1.05rem;
                    }
                    .discussion-box-card p {
                        margin: 0 0 12px 0;
                        color: #64748b;
                        min-height: 42px;
                    }
                    .discussion-box-meta {
                        display: inline-block;
                        margin-bottom: 10px;
                        font-size: 12px;
                        padding: 4px 10px;
                        border-radius: 999px;
                        background: #f1f5f9;
                        color: #475569;
                        font-weight: 700;
                        transition: transform 0.2s ease, box-shadow 0.2s ease;
                    }
                    .discussion-box-card:hover .discussion-box-meta {
                        transform: translateY(-1px);
                        box-shadow: 0 4px 10px rgba(71, 85, 105, 0.15);
                    }
                    .discussion-box-actions {
                        display: flex;
                        gap: 8px;
                        flex-wrap: wrap;
                    }
                    .discussion-box-actions .action-btn {
                        transition: transform 0.15s ease, box-shadow 0.2s ease, filter 0.2s ease;
                    }
                    .discussion-box-actions .action-btn:hover {
                        transform: translateY(-2px);
                        box-shadow: 0 8px 16px rgba(15, 23, 42, 0.12);
                        filter: brightness(1.02);
                    }
                </style>
                <div class="dashboard-header" style="display:flex;justify-content:space-between;align-items:center;">
                    <div><h1>Discussions</h1><p>Discussions from groups you joined.</p></div>
                    <a href="<?= APP_ENTRY ?>?url=student/discussions/create" class="btn btn-yellow">Create Discussion</a>
                </div>
                <?php if (!empty($discussions)): ?>
                    <div class="discussion-box-grid">
                        <?php foreach ($discussions as $d): ?>
                            <?php
                                $status = (string) ($d['approval_statut'] ?? $d['approval_status'] ?? 'approuve');
                                $statusLabel = $status === 'approuve' ? 'Approved' : ($status === 'rejete' ? 'Rejected' : 'Pending approval');
                                $statusBg = $status === 'approuve' ? '#dcfce7' : ($status === 'rejete' ? '#fee2e2' : '#ffedd5');
                                $statusColor = $status === 'approuve' ? '#166534' : ($status === 'rejete' ? '#991b1b' : '#9a3412');
                                $authorId = (int) ($d['id_auteur'] ?? $d['created_by'] ?? 0);
                                $isAuthor = $authorId === $currentUserId;
                            ?>
                            <article class="discussion-box-card">
                                <span class="discussion-box-meta">Group: <?= htmlspecialchars((string) ($d['nom_groupe'] ?? 'N/A')) ?></span>
                                <span class="discussion-box-meta" style="background:<?= $statusBg ?>;color:<?= $statusColor ?>;margin-left:6px;"><?= htmlspecialchars($statusLabel) ?></span>
                                <h4><?= htmlspecialchars($d['titre'] ?? 'Discussion') ?></h4>
                                <p><?= htmlspecialchars((string) ($d['contenu'] ?? '')) ?></p>
                                <div class="discussion-box-actions">
                                    <?php if ($status === 'approuve'): ?>
                                        <a class="btn btn-primary action-btn" href="<?= APP_ENTRY ?>?url=student/discussions/<?= (int) ($d['id_discussion'] ?? 0) ?>/chat">Live Chat</a>
                                        <?php if ($isAuthor): ?>
                                            <a class="btn btn-secondary action-btn" href="<?= APP_ENTRY ?>?url=student/discussions/<?= (int) ($d['id_discussion'] ?? 0) ?>/edit">Edit</a>
                                            <a class="btn action-btn danger" href="<?= APP_ENTRY ?>?url=student/discussions/<?= (int) ($d['id_discussion'] ?? 0) ?>/delete">Delete</a>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php if ($isAuthor): ?>
                                            <span style="font-size:13px;color:#64748b;">Wait for admin approval to use this discussion.</span>
                                        <?php else: ?>
                                            <span style="font-size:13px;color:#64748b;">Only approved discussions are visible for interaction.</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="table-container">No discussions yet.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
