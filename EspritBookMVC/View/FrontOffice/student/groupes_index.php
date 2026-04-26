<?php
$studentSidebarActive = 'groupes';
$viewerId = (int) ($_SESSION['user_id'] ?? 0);
?>
<div class="dashboard student-events-page">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main">
                <style>
                    .group-box-grid {
                        display: grid;
                        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
                        gap: 16px;
                    }
                    .group-box-card {
                        background: #fff;
                        border: 1px solid #e2e8f0;
                        border-radius: 14px;
                        box-shadow: 0 2px 12px rgba(15, 23, 42, 0.06);
                        padding: 14px;
                        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
                    }
                    .group-box-card:hover {
                        transform: translateY(-4px);
                        border-color: #cbd5e1;
                        box-shadow: 0 12px 24px rgba(15, 23, 42, 0.12);
                    }
                    .group-box-card h4 {
                        margin: 0 0 6px 0;
                        color: #1e293b;
                        font-size: 1.05rem;
                    }
                    .group-box-cover {
                        width: 100%;
                        height: 140px;
                        border-radius: 10px;
                        object-fit: cover;
                        border: 1px solid #e2e8f0;
                        margin-bottom: 10px;
                        background: #f8fafc;
                        transition: transform 0.25s ease;
                    }
                    .group-box-card:hover .group-box-cover { transform: scale(1.02); }
                    .group-box-card p {
                        margin: 0 0 12px 0;
                        color: #64748b;
                        min-height: 42px;
                    }
                    .group-box-actions {
                        display: flex;
                        gap: 8px;
                        flex-wrap: wrap;
                    }
                    .group-box-actions .action-btn {
                        transition: transform 0.15s ease, box-shadow 0.2s ease, filter 0.2s ease;
                    }
                    .group-box-actions .action-btn:hover {
                        transform: translateY(-2px);
                        box-shadow: 0 8px 16px rgba(15, 23, 42, 0.12);
                        filter: brightness(1.02);
                    }
                    .group-box-badge {
                        display: inline-block;
                        margin-bottom: 10px;
                        font-size: 12px;
                        padding: 4px 10px;
                        border-radius: 999px;
                        background: #ffedd5;
                        color: #9a3412;
                        font-weight: 700;
                        transition: transform 0.2s ease, box-shadow 0.2s ease;
                    }
                    .group-box-card:hover .group-box-badge {
                        transform: translateY(-1px);
                        box-shadow: 0 4px 10px rgba(154, 52, 18, 0.15);
                    }
                </style>

                <div class="dashboard-header" style="display:flex;justify-content:space-between;align-items:center;">
                    <div><h1>Groups</h1><p>Approved groups and your pending groups.</p></div>
                    <a class="btn btn-yellow" href="<?= APP_ENTRY ?>?url=student/groupes/create">Create Group</a>
                </div>
                <?php if (!empty($mesGroupesEnApprobation)): ?>
                    <h3>My groups pending approval</h3>
                    <div class="group-box-grid" style="margin-bottom:18px;">
                        <?php foreach ($mesGroupesEnApprobation as $g): ?>
                            <?php $cover = trim((string) ($g['image_url'] ?? $g['photo'] ?? $g['image'] ?? '')); ?>
                            <article class="group-box-card">
                                <span class="group-box-badge">Pending Approval</span>
                                <?php if ($cover !== ''): ?>
                                    <img class="group-box-cover" src="<?= htmlspecialchars($cover) ?>" alt="Group photo" onerror="this.style.display='none';">
                                <?php endif; ?>
                                <h4><?= htmlspecialchars($g['nom_groupe']) ?></h4>
                                <p><?= htmlspecialchars((string) ($g['description'] ?? '')) ?></p>
                                <div class="group-box-actions">
                                    <a class="btn btn-secondary action-btn" href="<?= APP_ENTRY ?>?url=student/groupes/<?= (int) $g['id_groupe'] ?>">View</a>
                                    <a class="btn btn-primary action-btn" href="<?= APP_ENTRY ?>?url=student/groupes/<?= (int) $g['id_groupe'] ?>/edit">Edit</a>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <h3>Approved groups</h3>
                <?php if (!empty($groupes)): ?>
                    <div class="group-box-grid">
                        <?php foreach ($groupes as $g): ?>
                            <?php
                                $cover = trim((string) ($g['image_url'] ?? $g['photo'] ?? $g['image'] ?? ''));
                                $ownerId = (int) ($g['id_createur'] ?? $g['created_by'] ?? 0);
                                $isOwner = $ownerId === $viewerId;
                            ?>
                            <article class="group-box-card">
                                <?php if ($cover !== ''): ?>
                                    <img class="group-box-cover" src="<?= htmlspecialchars($cover) ?>" alt="Group photo" onerror="this.style.display='none';">
                                <?php endif; ?>
                                <h4><?= htmlspecialchars($g['nom_groupe']) ?></h4>
                                <p><?= htmlspecialchars((string) ($g['description'] ?? '')) ?></p>
                                <div class="group-box-actions">
                                    <a class="btn btn-secondary action-btn" href="<?= APP_ENTRY ?>?url=student/groupes/<?= (int) $g['id_groupe'] ?>">View</a>
                                    <a class="btn btn-primary action-btn" href="<?= APP_ENTRY ?>?url=student/groupes/<?= (int) $g['id_groupe'] ?>/join">Join</a>
                                    <?php if ($isOwner): ?>
                                        <a class="btn btn-outline action-btn" href="<?= APP_ENTRY ?>?url=student/groupes/<?= (int) $g['id_groupe'] ?>/edit">Edit</a>
                                    <?php endif; ?>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="table-container">No groups available.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
