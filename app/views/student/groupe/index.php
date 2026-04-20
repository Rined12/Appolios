<?php
/**
 * FrontOffice — liste des groupes
 * $groupes, $currentPage, $totalPages
 */
$studentSidebarActive = 'groupes';
?>

<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/../../partials/sidebar_student.php'; ?>

            <div class="admin-main">
                <!-- Header -->
                <div class="dashboard-header" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:15px;margin-bottom:30px;">
                    <div>
                        <h1 style="margin:0;font-size:1.8rem;">
                            <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:8px;color:var(--secondary-color)"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            Groupes d'apprentissage
                        </h1>
                        <p style="margin:5px 0 0;color:var(--gray-dark);">Rejoignez un groupe et apprenez ensemble. Les nouveaux groupes sont des <strong>demandes</strong> validées par un administrateur.</p>
                    </div>
                    <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                    <a href="<?= APP_URL ?>/index.php?url=<?= $slGroupesUrlPrefix ?>/create" class="btn btn-primary" id="btn-create-groupe">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:6px;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Créer un groupe
                    </a>
                    <?php endif; ?>
                </div>

                <!-- Flash -->
                <?php if(!empty($flash)): ?>
                <div class="flash-message <?= htmlspecialchars($flash['type']) ?>" style="margin-bottom:20px;">
                    <?= htmlspecialchars($flash['message']) ?>
                </div>
                <?php endif; ?>

                <!-- Stat Cards -->
                <div class="dashboard-grid" style="margin-bottom:30px;">
                    <div class="stat-card">
                        <div class="stat-icon" style="background:linear-gradient(135deg,#6366f1,#8b5cf6);">
                            <svg viewBox="0 0 24 24" fill="white" width="24" height="24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        </div>
                        <div class="stat-info"><h3><?= count($groupes) ?></h3><p>Groupes affichés</p></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background:linear-gradient(135deg,#0ea5e9,#38bdf8);">
                            <svg viewBox="0 0 24 24" fill="white" width="24" height="24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        </div>
                        <div class="stat-info"><h3><?= $totalPages * 10 ?>+</h3><p>Total groupes</p></div>
                    </div>
                </div>

                <!-- Groupes Grid -->
                <?php if (empty($groupes)): ?>
                <div class="table-container" style="padding:60px;text-align:center;">
                    <svg viewBox="0 0 24 24" width="64" height="64" fill="none" stroke="#ccc" stroke-width="1.5" style="margin-bottom:20px;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    <p style="color:var(--gray-dark);font-size:1.1rem;margin-bottom:20px;">Aucun groupe n'existe encore.</p>
                    <a href="<?= APP_URL ?>/index.php?url=<?= $slGroupesUrlPrefix ?>/create" class="btn btn-primary">Créer le premier groupe</a>
                </div>
                <?php else: ?>
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:24px;margin-bottom:30px;">
                    <?php foreach ($groupes as $g): ?>
                    <div class="sl-card">
                        <div class="sl-card-header">
                            <div class="sl-card-avatar">
                                <?= strtoupper(mb_substr($g['nom_groupe'], 0, 2)) ?>
                            </div>
                            <div style="flex:1;min-width:0;">
                                <h3 style="margin:0;font-size:1rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                    <?= htmlspecialchars($g['nom_groupe']) ?>
                                </h3>
                                <small style="color:var(--gray-dark);">Par <?= htmlspecialchars($g['nom_createur']) ?></small>
                            </div>
                            <span class="sl-badge <?= $g['statut'] === 'actif' ? 'sl-badge-success' : 'sl-badge-warning' ?>">
                                <?= htmlspecialchars($g['statut']) ?>
                            </span>
                        </div>
                        <p style="color:var(--gray-dark);font-size:0.9rem;margin:12px 0;line-height:1.5;">
                            <?= htmlspecialchars(mb_substr($g['description'], 0, 100)) ?><?= mb_strlen($g['description']) > 100 ? '…' : '' ?>
                        </p>
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;font-size:0.85rem;color:var(--gray-dark);">
                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            <?= date('d/m/Y', strtotime($g['date_creation'])) ?>
                        </div>
                        <div style="display:flex;gap:8px;flex-wrap:wrap;">
                            <a href="<?= APP_URL ?>/index.php?url=<?= $slGroupesUrlPrefix ?>/<?= $g['id_groupe'] ?>" class="btn btn-secondary" style="flex:1;text-align:center;padding:8px 12px;font-size:0.85rem;" id="btn-voir-groupe-<?= $g['id_groupe'] ?>">
                                Voir le groupe
                            </a>
                            <a href="<?= APP_URL ?>/index.php?url=<?= $slGroupesUrlPrefix ?>/<?= $g['id_groupe'] ?>/join" class="btn btn-primary" style="flex:1;text-align:center;padding:8px 12px;font-size:0.85rem;" id="btn-join-<?= $g['id_groupe'] ?>">
                                Rejoindre
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <div class="sl-pagination">
                    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                    <a href="?url=<?= $slGroupesUrlPrefix ?>&page=<?= $p ?>" class="sl-page-btn <?= $p === $currentPage ? 'active' : '' ?>"><?= $p ?></a>
                    <?php endfor; ?>
                </div>
                <?php endif; ?>
                <?php endif; ?>

            </div><!-- /admin-main -->
        </div><!-- /admin-layout -->
    </div>
</div>
