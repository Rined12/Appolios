<?php $adminSidebarActive = 'sl-discussions'; ?>

<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/../partials/sidebar.php'; ?>
            <div class="admin-main">
                <div class="dashboard-header">
                    <h1>Social Learning - Discussions</h1>
                    <p>Valider les discussions creees par les etudiants (statut en cours).</p>
                </div>

                <style>
                    .disc-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(320px,1fr)); gap:18px; }
                    .disc-card { background:#fff; border:1px solid #e2e8f0; border-radius:14px; overflow:hidden; box-shadow:0 2px 10px rgba(2,6,23,0.06); padding:14px; }
                    .disc-meta { display:flex; gap:8px; flex-wrap:wrap; margin:8px 0 10px; }
                    .disc-tag { font-size:12px; padding:4px 8px; border-radius:999px; background:#f1f5f9; color:#334155; }
                    .disc-actions { display:flex; gap:8px; flex-wrap:wrap; margin-top:12px; }
                </style>

                <?php if (!empty($discussions)): ?>
                    <div class="disc-grid">
                        <?php foreach ($discussions as $d): ?>
                            <?php
                                $ap = (string) ($d['approval_statut'] ?? $d['approval_status'] ?? 'approuve');
                                $idDisc = (int) ($d['id_discussion'] ?? $d['id'] ?? 0);
                            ?>
                            <article class="disc-card">
                                <h3 style="margin:0 0 8px 0;"><?= htmlspecialchars((string) ($d['titre'] ?? 'Discussion')) ?></h3>
                                <p style="margin:0;color:#475569;font-size:14px;min-height:42px;"><?= htmlspecialchars(substr((string) ($d['contenu'] ?? ''), 0, 200)) ?></p>
                                <div class="disc-meta">
                                    <span class="disc-tag">#<?= $idDisc ?></span>
                                    <span class="disc-tag">Groupe: <?= htmlspecialchars((string) ($d['nom_groupe'] ?? '—')) ?></span>
                                    <span class="disc-tag">Auteur: <?= htmlspecialchars((string) ($d['auteur_name'] ?? '—')) ?></span>
                                    <span class="disc-tag">Approbation: <?= htmlspecialchars($ap) ?></span>
                                </div>
                                <div class="disc-actions">
                                    <form method="POST" action="<?= APP_ENTRY ?>?url=admin/sl-discussions/<?= $idDisc ?>/approve" style="display:inline;">
                                        <button class="btn btn-primary action-btn" type="submit">Approuver</button>
                                    </form>
                                    <form method="POST" action="<?= APP_ENTRY ?>?url=admin/sl-discussions/<?= $idDisc ?>/reject" style="display:inline;">
                                        <button class="btn btn-outline action-btn" type="submit">Rejeter</button>
                                    </form>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="table-container" style="padding:24px;">Aucune discussion.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
