<?php
/**
 * FrontOffice — détail groupe + liste discussions
 * $groupe, $membres, $isMember, $myRole, $discussions, $currentPage, $totalPages
 */
$studentSidebarActive = 'groupes';
$isGroupAdmin = ($myRole === 'admin');
$userId = (int)($_SESSION['user_id'] ?? 0);
$groupePendingApproval = $groupePendingApproval ?? false;
?>

<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/../../partials/sidebar_student.php'; ?>

            <div class="admin-main">
                <!-- Flash -->
                <?php if(!empty($flash)): ?>
                <div class="flash-message <?= htmlspecialchars($flash['type']) ?>" style="margin-bottom:20px;"><?= htmlspecialchars($flash['message']) ?></div>
                <?php endif; ?>

                <!-- Groupe Header Banner -->
                <div class="sl-group-banner" style="margin-bottom:28px;">
                    <div class="sl-group-banner-avatar">
                        <?= strtoupper(mb_substr($groupe['nom_groupe'], 0, 2)) ?>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:6px;">
                            <h1 style="margin:0;font-size:1.6rem;"><?= htmlspecialchars($groupe['nom_groupe']) ?></h1>
                            <span class="sl-badge <?= $groupe['statut'] === 'actif' ? 'sl-badge-success' : 'sl-badge-warning' ?>" style="font-size:0.8rem;">
                                <?= htmlspecialchars($groupe['statut']) ?>
                            </span>
                            <?php if ($isGroupAdmin): ?>
                            <span class="sl-badge" style="background:rgba(99,102,241,0.15);color:#6366f1;font-size:0.8rem;">Admin</span>
                            <?php elseif ($isMember): ?>
                            <span class="sl-badge" style="background:rgba(16,185,129,0.15);color:#10b981;font-size:0.8rem;">Membre</span>
                            <?php endif; ?>
                        </div>
                        <p style="margin:0;color:rgba(255,255,255,0.75);font-size:0.95rem;"><?= htmlspecialchars($groupe['description']) ?></p>
                        <div style="display:flex;gap:20px;margin-top:10px;font-size:0.85rem;color:rgba(255,255,255,0.6);">
                            <span>👤 Créé par <strong style="color:rgba(255,255,255,0.9);"><?= htmlspecialchars($groupe['nom_createur']) ?></strong></span>
                            <span>📅 <?= date('d/m/Y', strtotime($groupe['date_creation'])) ?></span>
                            <span>👥 <?= count($membres) ?> membre(s)</span>
                        </div>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:8px;flex-shrink:0;">
                        <?php if (!$isMember): ?>
                        <a href="<?= APP_URL ?>/index.php?url=<?= $slGroupesUrlPrefix ?>/<?= $groupe['id_groupe'] ?>/join" class="btn btn-primary" id="btn-join-groupe" style="white-space:nowrap;">Rejoindre</a>
                        <?php else: ?>
                            <?php if ($isGroupAdmin || $this_is_platform_admin ?? false): ?>
                            <a href="<?= APP_URL ?>/index.php?url=<?= $slGroupesUrlPrefix ?>/<?= $groupe['id_groupe'] ?>/edit" class="btn btn-secondary" style="white-space:nowrap;" id="btn-edit-groupe">Modifier</a>
                            <a href="<?= APP_URL ?>/index.php?url=<?= $slGroupesUrlPrefix ?>/<?= $groupe['id_groupe'] ?>/delete"
                               class="btn" style="background:#ef4444;color:white;white-space:nowrap;" id="btn-delete-groupe"
                               data-confirm="Supprimer ce groupe et toutes ses discussions ?">Supprimer</a>
                            <?php else: ?>
                            <a href="<?= APP_URL ?>/index.php?url=<?= $slGroupesUrlPrefix ?>/<?= $groupe['id_groupe'] ?>/leave" class="btn btn-outline" style="white-space:nowrap;color:#ef4444;border-color:#ef4444;" id="btn-leave-groupe" data-confirm="Quitter ce groupe ?">Quitter</a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($groupePendingApproval): ?>
                <div class="flash-message error" style="margin-bottom:20px;">Ce groupe est en attente d'approbation par un administrateur. Il n'apparaît pas dans le catalogue public tant qu'il n'est pas validé.</div>
                <?php endif; ?>

                <div style="display:grid;grid-template-columns:1fr 280px;gap:24px;align-items:start;">

                    <!-- Discussions -->
                    <div>
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                            <h2 style="margin:0;font-size:1.2rem;">💬 Discussions</h2>
                            <?php if ($isMember && !$groupePendingApproval): ?>
                            <a href="<?= APP_URL ?>/index.php?url=<?= $slGroupesUrlPrefix ?>/<?= $groupe['id_groupe'] ?>/discussions/create" class="btn btn-primary" style="padding:8px 16px;font-size:0.85rem;" id="btn-new-discussion">
                                + Nouvelle discussion
                            </a>
                            <?php endif; ?>
                        </div>

                        <?php if (empty($discussions)): ?>
                        <div class="table-container" style="padding:40px;text-align:center;">
                            <p style="color:var(--gray-dark);margin-bottom:16px;">Aucune discussion pour l'instant.</p>
                            <?php if ($isMember && !$groupePendingApproval): ?>
                            <a href="<?= APP_URL ?>/index.php?url=<?= $slGroupesUrlPrefix ?>/<?= $groupe['id_groupe'] ?>/discussions/create" class="btn btn-primary">Démarrer une discussion</a>
                            <?php endif; ?>
                        </div>
                        <?php else: ?>
                        <div style="display:flex;flex-direction:column;gap:14px;">
                            <?php foreach ($discussions as $d): ?>
                            <div class="sl-discussion-card" id="discussion-<?= $d['id_discussion'] ?>">
                                <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;flex-wrap:wrap;">
                                    <div style="flex:1;min-width:0;">
                                        <a href="<?= APP_URL ?>/index.php?url=<?= $slGroupesUrlPrefix ?>/<?= $groupe['id_groupe'] ?>/discussions/<?= $d['id_discussion'] ?>" style="text-decoration:none;color:inherit;">
                                            <h3 style="margin:0 0 6px;font-size:1rem;color:var(--primary-color);cursor:pointer;"><?= htmlspecialchars($d['titre']) ?></h3>
                                        </a>
                                        <p style="margin:0;color:var(--gray-dark);font-size:0.88rem;line-height:1.4;">
                                            <?= htmlspecialchars(mb_substr($d['contenu'], 0, 140)) ?><?= mb_strlen($d['contenu']) > 140 ? '…' : '' ?>
                                        </p>
                                    </div>
                                    <div style="display:flex;flex-direction:column;align-items:flex-end;gap:6px;flex-shrink:0;">
                                        <span class="sl-badge" style="background:rgba(14,165,233,0.12);color:#0ea5e9;">
                                            💬 <?= (int)$d['nb_messages'] ?> rép.
                                        </span>
                                        <span class="sl-badge" style="background:rgba(239,68,68,0.1);color:#ef4444;">
                                            ❤️ <?= (int)$d['nb_likes'] ?>
                                        </span>
                                    </div>
                                </div>
                                <div style="display:flex;justify-content:space-between;align-items:center;margin-top:12px;flex-wrap:wrap;gap:8px;">
                                    <small style="color:var(--gray-dark);">
                                        Par <strong><?= htmlspecialchars($d['nom_auteur']) ?></strong> · <?= date('d/m/Y H:i', strtotime($d['date_creation'])) ?>
                                    </small>
                                    <div style="display:flex;gap:8px;">
                                        <a href="<?= APP_URL ?>/index.php?url=<?= $slGroupesUrlPrefix ?>/<?= $groupe['id_groupe'] ?>/discussions/<?= $d['id_discussion'] ?>" class="btn btn-secondary" style="padding:5px 12px;font-size:0.8rem;">Voir</a>
                                        <?php if ($isMember && ((int)$d['id_auteur'] === $userId || $isGroupAdmin)): ?>
                                        <a href="<?= APP_URL ?>/index.php?url=<?= $slGroupesUrlPrefix ?>/<?= $groupe['id_groupe'] ?>/discussions/<?= $d['id_discussion'] ?>/delete"
                                           class="btn" style="padding:5px 12px;font-size:0.8rem;background:#fee2e2;color:#ef4444;"
                                           data-confirm="Supprimer cette discussion ?" id="btn-del-disc-<?= $d['id_discussion'] ?>">Supprimer</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Pagination -->
                        <?php if($totalPages > 1): ?>
                        <div class="sl-pagination" style="margin-top:20px;">
                            <?php for($p=1;$p<=$totalPages;$p++): ?>
                            <a href="?url=<?= $slGroupesUrlPrefix ?>/<?= $groupe['id_groupe'] ?>&page=<?= $p ?>" class="sl-page-btn <?= $p===$currentPage?'active':'' ?>"><?= $p ?></a>
                            <?php endfor; ?>
                        </div>
                        <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Sidebar Membres -->
                    <div>
                        <div class="table-container">
                            <div class="table-header"><h3 style="margin:0;font-size:1rem;">👥 Membres (<?= count($membres) ?>)</h3></div>
                            <div style="padding:16px;max-height:400px;overflow-y:auto;">
                                <?php foreach ($membres as $m): ?>
                                <div style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid var(--gray-light);">
                                    <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#6366f1,#8b5cf6);display:flex;align-items:center;justify-content:center;color:white;font-weight:600;font-size:0.85rem;flex-shrink:0;">
                                        <?= strtoupper(mb_substr($m['name'], 0, 1)) ?>
                                    </div>
                                    <div style="flex:1;min-width:0;">
                                        <div style="font-size:0.9rem;font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars($m['name']) ?></div>
                                        <div style="font-size:0.75rem;color:var(--gray-dark);"><?= $m['role'] === 'admin' ? '⭐ Admin' : 'Membre' ?></div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="margin-top:24px;">
                    <a href="<?= APP_URL ?>/index.php?url=<?= $slGroupesUrlPrefix ?>" class="btn btn-outline">← Retour aux groupes</a>
                </div>
            </div>
        </div>
    </div>
</div>
