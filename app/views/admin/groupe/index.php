<?php
/**
 * BackOffice — tableau de bord admin groupes
 * $groupes, $totalGroupes, $totalActifs, $totalArchives, $currentPage, $totalPages
 */
$adminSidebarActive = 'sl-groupes';
?>

<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/../partials/sidebar.php'; ?>

            <div class="admin-main">
                <!-- Header -->
                <div class="dashboard-header" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:15px;margin-bottom:30px;">
                    <div>
                        <h1 style="margin:0;">🏫 Gestion des Groupes</h1>
                        <p style="margin:4px 0 0;color:var(--gray-dark);">Social Learning — Administration</p>
                    </div>
                    <a href="<?= APP_URL ?>/index.php?url=admin/sl-groupes/create" class="btn btn-yellow" id="btn-admin-create-groupe">
                        + Nouveau groupe
                    </a>
                </div>

                <!-- Flash -->
                <?php if(!empty($flash)): ?>
                <div class="flash-message <?= htmlspecialchars($flash['type']) ?>" style="margin-bottom:20px;"><?= htmlspecialchars($flash['message']) ?></div>
                <?php endif; ?>

                <!-- Stats -->
                <div class="dashboard-grid" style="margin-bottom:30px;">
                    <div class="stat-card">
                        <div class="stat-icon" style="background:linear-gradient(135deg,#6366f1,#8b5cf6);">
                            <svg viewBox="0 0 24 24" fill="white" width="24" height="24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        </div>
                        <div class="stat-info"><h3><?= $totalGroupes ?></h3><p>Total groupes</p></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background:linear-gradient(135deg,#10b981,#34d399);">
                            <svg viewBox="0 0 24 24" fill="white" width="24" height="24"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        <div class="stat-info"><h3><?= $totalActifs ?></h3><p>Actifs</p></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background:linear-gradient(135deg,#f59e0b,#fbbf24);">
                            <svg viewBox="0 0 24 24" fill="white" width="24" height="24"><path d="M20 9v11a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V9"/><path d="M9 22V12h6v10M2 10.5l10-7 10 7"/></svg>
                        </div>
                        <div class="stat-info"><h3><?= $totalArchives ?></h3><p>Archivés</p></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background:linear-gradient(135deg,#f97316,#fb923c);">
                            <svg viewBox="0 0 24 24" fill="white" width="24" height="24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                        </div>
                        <div class="stat-info"><h3><?= (int)($totalPendingApproval ?? 0) ?></h3><p>En attente</p></div>
                    </div>
                </div>

                <!-- Table -->
                <div class="table-container">
                    <div class="table-header">
                        <h3 style="margin:0;">Liste des groupes</h3>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nom du groupe</th>
                                    <th>Créateur</th>
                                    <th>Statut</th>
                                    <th>Approbation</th>
                                    <th>Date création</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($groupes)): ?>
                                <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--gray-dark);">Aucun groupe trouvé.</td></tr>
                                <?php else: ?>
                                <?php foreach($groupes as $g): ?>
                                <tr>
                                    <td><?= $g['id_groupe'] ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($g['nom_groupe']) ?></strong>
                                        <div style="font-size:0.8rem;color:var(--gray-dark);margin-top:2px;"><?= htmlspecialchars(mb_substr($g['description'], 0, 60)) ?>…</div>
                                    </td>
                                    <td><?= htmlspecialchars($g['nom_createur']) ?></td>
                                    <td>
                                        <span class="sl-badge <?= $g['statut'] === 'actif' ? 'sl-badge-success' : 'sl-badge-warning' ?>">
                                            <?= htmlspecialchars($g['statut']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php $ap = $g['approval_statut'] ?? 'approuve'; ?>
                                        <span class="sl-badge <?= $ap === 'approuve' ? 'sl-badge-success' : ($ap === 'refuse' ? 'sl-badge-warning' : '') ?>" style="<?= $ap === 'en_attente' ? 'background:rgba(249,115,22,0.15);color:#ea580c;' : '' ?>">
                                            <?= $ap === 'en_attente' ? 'En attente' : ($ap === 'refuse' ? 'Refusé' : 'Approuvé') ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($g['date_creation'])) ?></td>
                                    <td>
                                        <div style="display:flex;gap:6px;flex-wrap:wrap;align-items:center;">
                                            <?php if (($g['approval_statut'] ?? 'approuve') === 'en_attente'): ?>
                                            <form method="POST" action="<?= APP_URL ?>/index.php?url=admin/sl-groupes/<?= $g['id_groupe'] ?>/approve" style="display:inline;">
                                                <button type="submit" class="btn btn-primary action-btn" style="padding:5px 10px;font-size:0.8rem;">Approuver</button>
                                            </form>
                                            <form method="POST" action="<?= APP_URL ?>/index.php?url=admin/sl-groupes/<?= $g['id_groupe'] ?>/reject" style="display:inline;">
                                                <button type="submit" class="btn btn-outline action-btn" style="padding:5px 10px;font-size:0.8rem;color:#b91c1c;border-color:#fecaca;">Refuser</button>
                                            </form>
                                            <?php endif; ?>
                                            <a href="<?= APP_URL ?>/index.php?url=admin/sl-groupes/<?= $g['id_groupe'] ?>/edit" class="btn btn-secondary action-btn" style="padding:5px 10px;font-size:0.8rem;" id="btn-admin-edit-<?= $g['id_groupe'] ?>">Modifier</a>
                                            <a href="<?= APP_URL ?>/index.php?url=admin/sl-groupes/<?= $g['id_groupe'] ?>/delete" class="btn action-btn danger" style="padding:5px 10px;font-size:0.8rem;" id="btn-admin-del-<?= $g['id_groupe'] ?>"
                                               data-confirm="Supprimer ce groupe définitivement ?">Supprimer</a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                <?php if($totalPages > 1): ?>
                <div class="sl-pagination" style="margin-top:20px;">
                    <?php for($p=1;$p<=$totalPages;$p++): ?>
                    <a href="?url=admin/sl-groupes&page=<?= $p ?>" class="sl-page-btn <?= $p===$currentPage?'active':'' ?>"><?= $p ?></a>
                    <?php endfor; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
