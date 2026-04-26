<?php
$foPrefix = $foPrefix ?? 'student';
$studentSidebarActive = $studentSidebarActive ?? 'groupes';
$mesGroupesEnApprobation = $mesGroupesEnApprobation ?? [];
?>
<div class="dashboard student-events-page">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/../partials/group_discussion_sidebar.php'; ?>
            <div class="admin-main">
                <div class="dashboard-header" style="display:flex;justify-content:space-between;align-items:center;">
                    <div>
                        <h1>Groups</h1>
                        <p>Discover approved groups and join your community.</p>
                    </div>
                    <a class="btn btn-yellow" href="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/groupes/create">Create Group</a>
                </div>

                <style>
                    .student-group-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(300px, 1fr)); gap:18px; }
                    .student-group-card { background:#fff; border:1px solid #e2e8f0; border-radius:14px; overflow:hidden; box-shadow:0 2px 12px rgba(15,23,42,0.05); }
                    .student-group-cover { height:145px; background:linear-gradient(135deg,#dbeafe,#e2e8f0); display:flex; align-items:center; justify-content:center; color:#334155; font-weight:700; }
                    .student-group-content { padding:14px; }
                    .student-group-meta { margin:8px 0 10px; display:flex; flex-wrap:wrap; gap:8px; }
                    .student-group-chip { font-size:12px; padding:4px 8px; border-radius:999px; background:#f1f5f9; color:#475569; }
                    .student-group-chip--pending { background:#ffedd5; color:#9a3412; }
                    .student-group-actions { display:flex; gap:8px; }
                </style>

                <?php if (!empty($mesGroupesEnApprobation)): ?>
                    <div style="margin-bottom:28px;">
                        <h2 style="margin:0 0 8px 0;font-size:1.15rem;color:#1e293b;">My groups pending approval</h2>
                        <p style="margin:0 0 14px 0;color:#64748b;font-size:14px;">These groups are <strong>in progress (pending administrator approval)</strong> until an admin approves or rejects them.</p>
                        <div class="student-group-grid">
                            <?php foreach ($mesGroupesEnApprobation as $g): ?>
                                <?php $cover = trim((string) ($g['image_url'] ?? $g['photo'] ?? $g['image'] ?? '')); ?>
                                <?php $ap = (string) ($g['approval_statut'] ?? $g['approval_status'] ?? ''); ?>
                                <article class="student-group-card" style="border-color:#fed7aa;">
                                    <div class="student-group-cover" style="<?= $cover !== '' ? 'padding:0;' : 'background:linear-gradient(135deg,#ffedd5,#fef3c7);' ?>">
                                        <?php if ($cover !== ''): ?>
                                            <img src="<?= htmlspecialchars($cover) ?>" alt="" style="width:100%;height:100%;object-fit:cover;">
                                        <?php else: ?>
                                            Pending
                                        <?php endif; ?>
                                    </div>
                                    <div class="student-group-content">
                                        <h3 style="margin:0; font-size:1.1rem; color:#1e293b;"><?= htmlspecialchars($g['nom_groupe']) ?></h3>
                                        <p style="margin:6px 0 0; color:#64748b; min-height:42px;"><?= htmlspecialchars(substr((string) ($g['description'] ?? ''), 0, 120)) ?></p>
                                        <div class="student-group-meta">
                                            <span class="student-group-chip student-group-chip--pending"><?= $ap === 'rejete' ? 'Rejected — you can edit' : 'In progress (approval)' ?></span>
                                        </div>
                                        <div class="student-group-actions">
                                            <a class="btn btn-secondary action-btn" href="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/groupes/<?= (int) $g['id_groupe'] ?>">View</a>
                                            <a class="btn btn-primary action-btn" href="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/groupes/<?= (int) $g['id_groupe'] ?>/edit">Edit</a>
                                            <a class="btn action-btn danger" href="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/groupes/<?= (int) $g['id_groupe'] ?>/delete" onclick="return confirm('Supprimer ce groupe et toutes ses discussions ?');">Delete</a>
                                        </div>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <h2 style="margin:0 0 8px 0;font-size:1.15rem;color:#1e293b;">Approved groups</h2>
                <p style="margin:0 0 16px 0;color:#64748b;font-size:14px;">Only groups validated by an administrator appear here for everyone.</p>

                <?php if (!empty($groupes)): ?>
                    <div class="student-group-grid">
                        <?php foreach ($groupes as $g): ?>
                            <?php $cover = trim((string) ($g['image_url'] ?? $g['photo'] ?? $g['image'] ?? '')); ?>
                            <article class="student-group-card">
                                <div class="student-group-cover" style="<?= $cover !== '' ? 'padding:0;' : '' ?>">
                                    <?php if ($cover !== ''): ?>
                                        <img src="<?= htmlspecialchars($cover) ?>" alt="" style="width:100%;height:100%;object-fit:cover;">
                                    <?php else: ?>
                                        Group Image
                                    <?php endif; ?>
                                </div>
                                <div class="student-group-content">
                                    <h3 style="margin:0; font-size:1.1rem; color:#1e293b;"><?= htmlspecialchars($g['nom_groupe']) ?></h3>
                                    <p style="margin:6px 0 0; color:#64748b; min-height:42px;"><?= htmlspecialchars(substr((string) ($g['description'] ?? ''), 0, 120)) ?></p>
                                    <?php $apPub = (string) ($g['approval_statut'] ?? $g['approval_status'] ?? ''); ?>
                                    <div class="student-group-meta">
                                        <span class="student-group-chip">Creator: <?= htmlspecialchars($g['createur_name'] ?? 'N/A') ?></span>
                                        <?php if ($apPub === 'approuve'): ?>
                                        <span class="student-group-chip" style="background:#dcfce7;color:#166534;">Approved</span>
                                        <?php else: ?>
                                        <span class="student-group-chip" style="background:#ffedd5;color:#9a3412;"><?= htmlspecialchars($apPub ?: '—') ?></span>
                                        <?php endif; ?>
                                        <span class="student-group-chip">Status: <?= htmlspecialchars((string) ($g['statut'] ?? '')) ?></span>
                                    </div>
                                    <div class="student-group-actions">
                                        <a class="btn btn-secondary action-btn" href="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/groupes/<?= (int) $g['id_groupe'] ?>">View</a>
                                        <a class="btn btn-primary action-btn" href="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/groupes/<?= (int) $g['id_groupe'] ?>/join">Join</a>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="table-container" style="text-align:center;padding:30px;">No groups available.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
