<?php $adminSidebarActive = 'sl-groupes'; ?>

<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/../partials/sidebar.php'; ?>
            <div class="admin-main">
                <div class="dashboard-header" style="display:flex;justify-content:space-between;align-items:center;">
                    <div>
                        <h1>Social Learning - Groupes</h1>
                        <p>Validation et administration des groupes</p>
                    </div>
                    <a class="btn btn-yellow" href="<?= APP_ENTRY ?>?url=admin/sl-groupes/create">Nouveau groupe</a>
                </div>

                <style>
                    .groupes-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(320px,1fr)); gap:18px; }
                    .groupe-card { background:#fff; border:1px solid #e2e8f0; border-radius:14px; overflow:hidden; box-shadow:0 2px 10px rgba(2,6,23,0.06); }
                    .groupe-card-image { height:150px; background:linear-gradient(135deg,#dbeafe,#e2e8f0); display:flex; align-items:center; justify-content:center; color:#1e3a8a; font-weight:700; }
                    .groupe-card-body { padding:14px; }
                    .groupe-meta { display:flex; gap:8px; flex-wrap:wrap; margin:8px 0 10px; }
                    .groupe-tag { font-size:12px; padding:4px 8px; border-radius:999px; background:#f1f5f9; color:#334155; }
                    .groupe-actions { display:flex; gap:8px; flex-wrap:wrap; margin-top:12px; }
                </style>

                <?php if (!empty($groupes)): ?>
                    <div class="groupes-grid">
                        <?php foreach ($groupes as $g): ?>
                            <?php
                                $approval = (string) ($g['approval_statut'] ?? 'en_cours');
                                $statut = (string) ($g['statut'] ?? 'actif');
                                $picture = trim((string) ($g['image_url'] ?? $g['photo'] ?? $g['image'] ?? ''));
                            ?>
                            <article class="groupe-card">
                                <?php if ($picture !== ''): ?>
                                    <img src="<?= htmlspecialchars($picture) ?>" alt="Image groupe" class="groupe-card-image" style="width:100%;object-fit:cover;">
                                <?php else: ?>
                                    <div class="groupe-card-image">Image du groupe</div>
                                <?php endif; ?>
                                <div class="groupe-card-body">
                                    <h3 style="margin:0 0 8px 0;"><?= htmlspecialchars((string) ($g['nom_groupe'] ?? 'Groupe')) ?></h3>
                                    <p style="margin:0;color:#475569;font-size:14px;min-height:42px;"><?= htmlspecialchars((string) ($g['description'] ?? '')) ?></p>
                                    <div class="groupe-meta">
                                        <span class="groupe-tag">#<?= (int) ($g['id_groupe'] ?? 0) ?></span>
                                        <span class="groupe-tag">Createur: <?= htmlspecialchars((string) ($g['createur_name'] ?? 'N/A')) ?></span>
                                        <span class="groupe-tag">Approbation: <?= htmlspecialchars($approval) ?></span>
                                        <span class="groupe-tag">Statut: <?= htmlspecialchars($statut) ?></span>
                                    </div>
                                    <div class="groupe-actions">
                                        <a class="btn btn-secondary action-btn" href="<?= APP_ENTRY ?>?url=admin/sl-groupes/<?= (int) $g['id_groupe'] ?>/edit">Edit</a>
                                        <a class="btn action-btn danger" href="<?= APP_ENTRY ?>?url=admin/sl-groupes/<?= (int) $g['id_groupe'] ?>/delete" onclick="return confirm('Confirmer la suppression ?')">Delete</a>
                                        <form method="POST" action="<?= APP_ENTRY ?>?url=admin/sl-groupes/<?= (int) $g['id_groupe'] ?>/approve" style="display:inline;">
                                            <button class="btn btn-primary action-btn" type="submit">Approve</button>
                                        </form>
                                        <form method="POST" action="<?= APP_ENTRY ?>?url=admin/sl-groupes/<?= (int) $g['id_groupe'] ?>/reject" style="display:inline;">
                                            <button class="btn btn-outline action-btn" type="submit">Reject</button>
                                        </form>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="table-container" style="padding:24px;">Aucun groupe.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
