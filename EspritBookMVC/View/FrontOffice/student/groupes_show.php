<?php
$studentSidebarActive = 'groupes';
$viewerId = (int) ($_SESSION['user_id'] ?? 0);
$ownerId = (int) ($groupe['id_createur'] ?? $groupe['created_by'] ?? 0);
$isOwner = $viewerId === $ownerId;
$cover = trim((string) ($groupe['image_url'] ?? $groupe['photo'] ?? $groupe['image'] ?? ''));
?>
<div class="dashboard student-events-page">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main">
                <div class="dashboard-header" style="display:flex;justify-content:space-between;">
                    <div>
                        <h1><?= htmlspecialchars($groupe['nom_groupe'] ?? 'Group') ?></h1>
                        <p><?= htmlspecialchars((string) ($groupe['description'] ?? '')) ?></p>
                    </div>
                    <?php if ($isOwner): ?>
                        <a class="btn btn-primary" href="<?= APP_ENTRY ?>?url=student/groupes/<?= (int) $groupe['id_groupe'] ?>/edit">Edit</a>
                    <?php endif; ?>
                </div>

                <?php if ($cover !== ''): ?>
                    <div style="margin-bottom:14px;">
                        <img src="<?= htmlspecialchars($cover) ?>" alt="Group photo" style="width:100%;max-width:600px;height:240px;object-fit:cover;border-radius:12px;border:1px solid #e2e8f0;" onerror="this.style.display='none';">
                    </div>
                <?php endif; ?>

                <div class="table-container" style="padding:20px 24px;">
                    <h3 style="margin:0 0 14px 0;">Members</h3>
                    <?php if (!empty($membres)): foreach ($membres as $m): ?>
                        <div style="padding:8px 0;border-bottom:1px solid #f1f5f9;">
                            <?= htmlspecialchars($m['name']) ?> - <?= htmlspecialchars($m['role']) ?>
                        </div>
                    <?php endforeach; else: ?>
                        <div>No members yet.</div>
                    <?php endif; ?>
                </div>

                <div class="table-container" style="margin-top:14px;padding:20px 24px;">
                    <h3 style="margin:0 0 14px 0;">Discussions</h3>
                    <?php if ($isOwner): ?>
                        <form method="POST" action="<?= APP_ENTRY ?>?url=student/groupes/<?= (int) $groupe['id_groupe'] ?>/discussions/store" style="margin-bottom:14px;">
                            <div class="form-group"><input type="text" name="titre" placeholder="Discussion title"></div>
                            <div class="form-group"><textarea name="contenu" placeholder="Discussion content"></textarea></div>
                            <button class="btn btn-yellow" type="submit">Create Discussion</button>
                        </form>
                    <?php endif; ?>
                    <?php if (!empty($discussions)): foreach ($discussions as $d): ?>
                        <div style="margin-top:10px;padding:10px;border:1px solid #e2e8f0;border-radius:10px;">
                            <strong><?= htmlspecialchars($d['titre'] ?? 'Discussion') ?></strong>
                            <p><?= htmlspecialchars((string) ($d['contenu'] ?? '')) ?></p>
                        </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
