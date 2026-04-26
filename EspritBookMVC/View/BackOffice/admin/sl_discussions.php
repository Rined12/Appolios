<?php $adminSidebarActive = 'sl-discussions'; ?>
<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main">
                <div class="dashboard-header" style="display:flex;justify-content:space-between;align-items:center;gap:12px;">
                    <h1>Social Learning - Discussions</h1>
                    <a class="btn btn-yellow" href="<?= APP_ENTRY ?>?url=admin/sl-discussions/create">Create Discussion</a>
                </div>
                <style>
                    .sl-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(300px, 1fr)); gap:16px; }
                    .sl-card { background:#fff; border:1px solid #e2e8f0; border-radius:14px; box-shadow:0 2px 12px rgba(15,23,42,.06); padding:14px; transition:transform .2s ease, box-shadow .2s ease, border-color .2s ease; }
                    .sl-card:hover { transform:translateY(-4px); border-color:#cbd5e1; box-shadow:0 12px 24px rgba(15,23,42,.12); }
                    .sl-card h3 { margin:0 0 8px 0; color:#1e293b; font-size:1.05rem; }
                    .sl-card p { margin:0 0 12px 0; color:#64748b; min-height:40px; }
                    .sl-chip { display:inline-block; font-size:12px; padding:4px 10px; border-radius:999px; font-weight:700; margin-bottom:10px; background:#f1f5f9; color:#475569; transition:transform .2s ease, box-shadow .2s ease; }
                    .sl-card:hover .sl-chip { transform:translateY(-1px); box-shadow:0 4px 10px rgba(71,85,105,.15); }
                    .sl-chip.pending { background:#ffedd5; color:#9a3412; }
                    .sl-chip.approved { background:#dcfce7; color:#166534; }
                    .sl-chip.rejected { background:#fee2e2; color:#991b1b; }
                    .sl-actions { display:flex; gap:8px; flex-wrap:wrap; margin-top:8px; }
                    .sl-actions .action-btn { transition:transform .15s ease, box-shadow .2s ease, filter .2s ease; }
                    .sl-actions .action-btn:hover { transform:translateY(-2px); box-shadow:0 8px 16px rgba(15,23,42,.12); filter:brightness(1.02); }
                </style>
                <?php if (!empty($discussions)): ?>
                    <div class="sl-grid">
                        <?php foreach ($discussions as $d): ?>
                            <?php
                                $idDisc = (int) ($d['id_discussion'] ?? $d['id'] ?? 0);
                                $approval = (string) ($d['approval_statut'] ?? $d['approval_status'] ?? 'en_cours');
                                $chipClass = $approval === 'approuve' ? 'approved' : ($approval === 'rejete' ? 'rejected' : 'pending');
                            ?>
                            <article class="sl-card">
                                <span class="sl-chip">Group: <?= htmlspecialchars((string) ($d['nom_groupe'] ?? '—')) ?></span>
                                <span class="sl-chip <?= $chipClass ?>" style="margin-left:6px;"><?= htmlspecialchars($approval) ?></span>
                                <h3><?= htmlspecialchars((string) ($d['titre'] ?? 'Discussion')) ?></h3>
                                <p><?= htmlspecialchars((string) ($d['contenu'] ?? '')) ?></p>
                                <div style="font-size:12px;color:#64748b;margin-bottom:10px;">
                                    Author: <?= htmlspecialchars((string) ($d['auteur_name'] ?? 'N/A')) ?>
                                </div>
                                <div class="sl-actions">
                                    <a class="btn btn-secondary action-btn" href="<?= APP_ENTRY ?>?url=admin/sl-discussions/<?= $idDisc ?>/edit">Edit</a>
                                    <form method="POST" action="<?= APP_ENTRY ?>?url=admin/sl-discussions/<?= $idDisc ?>/approve" style="display:inline;">
                                        <button class="btn btn-primary action-btn" type="submit">Approve</button>
                                    </form>
                                    <form method="POST" action="<?= APP_ENTRY ?>?url=admin/sl-discussions/<?= $idDisc ?>/reject" style="display:inline;">
                                        <button class="btn btn-outline action-btn" type="submit">Reject</button>
                                    </form>
                                    <form method="POST" action="<?= APP_ENTRY ?>?url=admin/sl-discussions/<?= $idDisc ?>/delete" style="display:inline;">
                                        <button class="btn action-btn danger" type="submit">Delete</button>
                                    </form>
                                    <?php if ($approval === 'approuve'): ?>
                                        <a class="btn btn-secondary action-btn" href="<?= APP_ENTRY ?>?url=admin/sl-discussions/<?= $idDisc ?>/chat">Live Chat</a>
                                    <?php endif; ?>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="table-container">Aucune discussion.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
