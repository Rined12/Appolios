<?php $adminSidebarActive = 'sl-groupes'; ?>
<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main">
                <div class="dashboard-header" style="display:flex;justify-content:space-between;align-items:center;gap:12px;">
                    <h1>Social Learning - Groupes</h1>
                    <a class="btn btn-yellow" href="<?= APP_ENTRY ?>?url=admin/sl-groupes/create">Create Group</a>
                </div>
                <style>
                    .sl-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(300px, 1fr)); gap:16px; }
                    .sl-card { background:#fff; border:1px solid #e2e8f0; border-radius:14px; box-shadow:0 2px 12px rgba(15,23,42,.06); padding:14px; transition:transform .2s ease, box-shadow .2s ease, border-color .2s ease; }
                    .sl-card:hover { transform:translateY(-4px); border-color:#cbd5e1; box-shadow:0 12px 24px rgba(15,23,42,.12); }
                    .sl-cover { width:100%; height:140px; border-radius:10px; object-fit:cover; border:1px solid #e2e8f0; margin-bottom:10px; background:#f8fafc; transition:transform .25s ease; }
                    .sl-card:hover .sl-cover { transform:scale(1.02); }
                    .sl-card h3 { margin:0 0 8px 0; color:#1e293b; font-size:1.05rem; }
                    .sl-card p { margin:0 0 12px 0; color:#64748b; min-height:40px; }
                    .sl-chip { display:inline-block; font-size:12px; padding:4px 10px; border-radius:999px; font-weight:700; margin-bottom:10px; transition:transform .2s ease, box-shadow .2s ease; }
                    .sl-card:hover .sl-chip { transform:translateY(-1px); box-shadow:0 4px 10px rgba(71,85,105,.15); }
                    .sl-chip.pending { background:#ffedd5; color:#9a3412; }
                    .sl-chip.approved { background:#dcfce7; color:#166534; }
                    .sl-chip.rejected { background:#fee2e2; color:#991b1b; }
                    .sl-actions { display:flex; gap:8px; flex-wrap:wrap; margin-top:8px; }
                    .sl-actions .action-btn { transition:transform .15s ease, box-shadow .2s ease, filter .2s ease; }
                    .sl-actions .action-btn:hover { transform:translateY(-2px); box-shadow:0 8px 16px rgba(15,23,42,.12); filter:brightness(1.02); }
                </style>
                <?php if (!empty($groupes)): ?>
                    <div class="sl-grid">
                        <?php foreach ($groupes as $g): ?>
                            <?php
                                $approval = (string) ($g['approval_statut'] ?? 'en_cours');
                                $chipClass = $approval === 'approuve' ? 'approved' : ($approval === 'rejete' ? 'rejected' : 'pending');
                                $cover = trim((string) ($g['image_url'] ?? $g['photo'] ?? $g['image'] ?? ''));
                            ?>
                            <article class="sl-card">
                                <span class="sl-chip <?= $chipClass ?>"><?= htmlspecialchars($approval) ?></span>
                                <?php if ($cover !== ''): ?>
                                    <img class="sl-cover" src="<?= htmlspecialchars($cover) ?>" alt="Group image" onerror="this.style.display='none';">
                                <?php endif; ?>
                                <h3><?= htmlspecialchars((string) ($g['nom_groupe'] ?? 'Groupe')) ?></h3>
                                <p><?= htmlspecialchars((string) ($g['description'] ?? '')) ?></p>
                                <div style="font-size:12px;color:#64748b;margin-bottom:10px;">
                                    Creator: <?= htmlspecialchars((string) ($g['createur_name'] ?? 'N/A')) ?>
                                </div>
                                <div class="sl-actions">
                                    <a class="btn btn-secondary action-btn" href="<?= APP_ENTRY ?>?url=admin/sl-groupes/<?= (int) $g['id_groupe'] ?>/edit">Edit</a>
                                    <form method="POST" action="<?= APP_ENTRY ?>?url=admin/sl-groupes/<?= (int) $g['id_groupe'] ?>/approve" style="display:inline;">
                                        <button class="btn btn-primary action-btn" type="submit">Approve</button>
                                    </form>
                                    <form method="POST" action="<?= APP_ENTRY ?>?url=admin/sl-groupes/<?= (int) $g['id_groupe'] ?>/reject" style="display:inline;">
                                        <button class="btn btn-outline action-btn" type="submit">Reject</button>
                                    </form>
                                    <form method="POST" action="<?= APP_ENTRY ?>?url=admin/sl-groupes/<?= (int) $g['id_groupe'] ?>/delete" style="display:inline;">
                                        <button class="btn action-btn danger" type="submit">Delete</button>
                                    </form>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="table-container">Aucun groupe.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
