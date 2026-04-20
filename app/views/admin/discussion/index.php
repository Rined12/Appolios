<?php
/**
 * BackOffice — liste toutes les discussions (admin)
 * $discussions, $totalDiscussions, $currentPage, $totalPages
 */
$adminSidebarActive = 'sl-discussions';
?>

<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/../partials/sidebar.php'; ?>

            <div class="admin-main">
                <div class="dashboard-header" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:15px;margin-bottom:30px;">
                    <div>
                        <h1 style="margin:0;">💬 Gestion des Discussions</h1>
                        <p style="margin:4px 0 0;color:var(--gray-dark);">Social Learning — Administration</p>
                    </div>
                    <div class="stat-card" style="min-width:auto;padding:14px 24px;margin:0;">
                        <div class="stat-info"><h3><?= $totalDiscussions ?></h3><p>Total discussions</p></div>
                    </div>
                    <div class="stat-card" style="min-width:auto;padding:14px 24px;margin:0;">
                        <div class="stat-info"><h3><?= (int)($totalPendingApproval ?? 0) ?></h3><p>En attente</p></div>
                    </div>
                </div>

                <?php if(!empty($flash)): ?>
                <div class="flash-message <?= htmlspecialchars($flash['type']) ?>" style="margin-bottom:20px;"><?= htmlspecialchars($flash['message']) ?></div>
                <?php endif; ?>

                <div class="table-container">
                    <div class="table-header">
                        <h3 style="margin:0;">Toutes les discussions</h3>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Titre</th>
                                    <th>Groupe</th>
                                    <th>Auteur</th>
                                    <th style="text-align:center;">Likes</th>
                                    <th>Approbation</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($discussions)): ?>
                                <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--gray-dark);">Aucune discussion trouvée.</td></tr>
                                <?php else: ?>
                                <?php foreach($discussions as $d): ?>
                                <tr>
                                    <td><?= $d['id_discussion'] ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars(mb_substr($d['titre'], 0, 60)) ?><?= mb_strlen($d['titre']) > 60 ? '…' : '' ?></strong>
                                        <div style="font-size:0.78rem;color:var(--gray-dark);margin-top:2px;"><?= htmlspecialchars(mb_substr($d['contenu'], 0, 80)) ?>…</div>
                                    </td>
                                    <td><?= htmlspecialchars($d['nom_groupe']) ?></td>
                                    <td><?= htmlspecialchars($d['nom_auteur']) ?></td>
                                    <td style="text-align:center;"><span class="sl-badge" style="background:rgba(239,68,68,0.1);color:#ef4444;">❤️ <?= $d['nb_likes'] ?></span></td>
                                    <td>
                                        <?php $ap = $d['approval_statut'] ?? 'approuve'; ?>
                                        <span class="sl-badge <?= $ap === 'approuve' ? 'sl-badge-success' : ($ap === 'refuse' ? 'sl-badge-warning' : '') ?>" style="<?= $ap === 'en_attente' ? 'background:rgba(249,115,22,0.15);color:#ea580c;' : '' ?>">
                                            <?= $ap === 'en_attente' ? 'En attente' : ($ap === 'refuse' ? 'Refusé' : 'Approuvé') ?>
                                        </span>
                                    </td>
                                    <td style="white-space:nowrap;"><?= date('d/m/Y H:i', strtotime($d['date_creation'])) ?></td>
                                    <td>
                                        <div style="display:flex;gap:6px;flex-wrap:wrap;align-items:center;">
                                            <?php if (($d['approval_statut'] ?? 'approuve') === 'en_attente'): ?>
                                            <form method="POST" action="<?= APP_URL ?>/index.php?url=admin/sl-discussions/<?= $d['id_discussion'] ?>/approve" style="display:inline;">
                                                <button type="submit" class="btn btn-primary action-btn" style="padding:5px 10px;font-size:0.8rem;">Approuver</button>
                                            </form>
                                            <form method="POST" action="<?= APP_URL ?>/index.php?url=admin/sl-discussions/<?= $d['id_discussion'] ?>/reject" style="display:inline;">
                                                <button type="submit" class="btn btn-outline action-btn" style="padding:5px 10px;font-size:0.8rem;color:#b91c1c;border-color:#fecaca;">Refuser</button>
                                            </form>
                                            <?php endif; ?>
                                            <a href="<?= APP_URL ?>/index.php?url=admin/sl-discussions/<?= $d['id_discussion'] ?>/edit" class="btn btn-secondary action-btn" style="padding:5px 10px;font-size:0.8rem;" id="btn-admin-edit-disc-<?= $d['id_discussion'] ?>">Modifier</a>
                                            <a href="<?= APP_URL ?>/index.php?url=admin/sl-discussions/<?= $d['id_discussion'] ?>/delete" class="btn action-btn danger" style="padding:5px 10px;font-size:0.8rem;" id="btn-admin-del-disc-<?= $d['id_discussion'] ?>"
                                               data-confirm="Supprimer cette discussion et ses messages ?">Supprimer</a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php if($totalPages > 1): ?>
                <div class="sl-pagination" style="margin-top:20px;">
                    <?php for($p=1;$p<=$totalPages;$p++): ?>
                    <a href="?url=admin/sl-discussions&page=<?= $p ?>" class="sl-page-btn <?= $p===$currentPage?'active':'' ?>"><?= $p ?></a>
                    <?php endfor; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
