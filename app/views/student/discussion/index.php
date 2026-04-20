<?php
/**
 * FrontOffice — liste discussions d'un groupe
 * $groupe, $discussions, $isMember, $currentPage, $totalPages
 */
$studentSidebarActive = 'groupes';
$userId = (int)($_SESSION['user_id'] ?? 0);
?>

<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/../../partials/sidebar_student.php'; ?>

            <div class="admin-main">
                <!-- Header -->
                <div class="dashboard-header" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:15px;margin-bottom:30px;">
                    <div>
                        <a href="<?= APP_URL ?>/index.php?url=<?= $slGroupesUrlPrefix ?>/<?= $groupe['id_groupe'] ?>" style="font-size:0.85rem;color:var(--gray-dark);text-decoration:none;">
                            ← <?= htmlspecialchars($groupe['nom_groupe']) ?>
                        </a>
                        <h1 style="margin:8px 0 4px;font-size:1.6rem;">💬 Discussions</h1>
                        <p style="margin:0;color:var(--gray-dark);"><?= $totalPages * 10 ?> discussion(s) dans ce groupe</p>
                    </div>
                    <?php if($isMember): ?>
                    <a href="<?= APP_URL ?>/index.php?url=<?= $slGroupesUrlPrefix ?>/<?= $groupe['id_groupe'] ?>/discussions/create" class="btn btn-primary" id="btn-new-disc">
                        + Nouvelle discussion
                    </a>
                    <?php endif; ?>
                </div>

                <?php if(!empty($flash)): ?>
                <div class="flash-message <?= htmlspecialchars($flash['type']) ?>" style="margin-bottom:20px;"><?= htmlspecialchars($flash['message']) ?></div>
                <?php endif; ?>

                <?php if(empty($discussions)): ?>
                <div class="table-container" style="padding:60px;text-align:center;">
                    <svg viewBox="0 0 24 24" width="64" height="64" fill="none" stroke="#ccc" stroke-width="1.5" style="margin-bottom:20px;"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    <p style="color:var(--gray-dark);margin-bottom:20px;">Aucune discussion encore dans ce groupe.</p>
                    <?php if($isMember): ?>
                    <a href="<?= APP_URL ?>/index.php?url=<?= $slGroupesUrlPrefix ?>/<?= $groupe['id_groupe'] ?>/discussions/create" class="btn btn-primary">Démarrer la première discussion</a>
                    <?php endif; ?>
                </div>
                <?php else: ?>
                <div style="display:flex;flex-direction:column;gap:14px;margin-bottom:24px;">
                    <?php foreach($discussions as $d): ?>
                    <div class="sl-discussion-card" id="disc-<?= $d['id_discussion'] ?>">
                        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;flex-wrap:wrap;">
                            <div style="flex:1;min-width:0;">
                                <a href="<?= APP_URL ?>/index.php?url=<?= $slGroupesUrlPrefix ?>/<?= $groupe['id_groupe'] ?>/discussions/<?= $d['id_discussion'] ?>" style="text-decoration:none;">
                                    <h3 style="margin:0 0 6px;font-size:1rem;color:var(--primary-color);"><?= htmlspecialchars($d['titre']) ?></h3>
                                </a>
                                <p style="margin:0;color:var(--gray-dark);font-size:0.88rem;line-height:1.4;">
                                    <?= htmlspecialchars(mb_substr($d['contenu'], 0, 160)) ?><?= mb_strlen($d['contenu']) > 160 ? '…' : '' ?>
                                </p>
                            </div>
                            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:6px;">
                                <span class="sl-badge" style="background:rgba(14,165,233,0.12);color:#0ea5e9;">💬 <?= $d['nb_messages'] ?></span>
                                <span class="sl-badge" style="background:rgba(239,68,68,0.1);color:#ef4444;">❤️ <?= $d['nb_likes'] ?></span>
                            </div>
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-top:12px;flex-wrap:wrap;gap:8px;">
                            <small style="color:var(--gray-dark);">Par <strong><?= htmlspecialchars($d['nom_auteur']) ?></strong> · <?= date('d/m/Y H:i', strtotime($d['date_creation'])) ?></small>
                            <a href="<?= APP_URL ?>/index.php?url=<?= $slGroupesUrlPrefix ?>/<?= $groupe['id_groupe'] ?>/discussions/<?= $d['id_discussion'] ?>" class="btn btn-secondary" style="padding:5px 14px;font-size:0.82rem;">Lire →</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <?php if($totalPages > 1): ?>
                <div class="sl-pagination">
                    <?php for($p=1;$p<=$totalPages;$p++): ?>
                    <a href="?url=<?= $slGroupesUrlPrefix ?>/<?= $groupe['id_groupe'] ?>/discussions&page=<?= $p ?>" class="sl-page-btn <?= $p===$currentPage?'active':'' ?>"><?= $p ?></a>
                    <?php endfor; ?>
                </div>
                <?php endif; ?>
                <?php endif; ?>

                <div style="margin-top:24px;">
                    <a href="<?= APP_URL ?>/index.php?url=<?= $slGroupesUrlPrefix ?>/<?= $groupe['id_groupe'] ?>" class="btn btn-outline">← Retour au groupe</a>
                </div>
            </div>
        </div>
    </div>
</div>
