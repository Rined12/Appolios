<?php
/**
 * FrontOffice — détail discussion + messages
 * $groupe, $discussion, $messages, $isMember, $myRole, $errors[], $currentPage, $totalPages
 */
$studentSidebarActive = 'groupes';
$userId = (int)($_SESSION['user_id'] ?? 0);
$isGroupAdmin = ($myRole === 'admin');
$discussionApproved = $discussionApproved ?? true;
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

                <?php if (!$discussionApproved): ?>
                <div class="flash-message error" style="margin-bottom:20px;">Cette discussion est en attente de validation par un administrateur. Les réponses et certains actions seront disponibles après approbation.</div>
                <?php endif; ?>

                <!-- Discussion Header -->
                <div class="sl-disc-header" style="margin-bottom:28px;">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:12px;margin-bottom:12px;">
                        <div style="flex:1;min-width:0;">
                            <a href="<?= APP_URL ?>/index.php?url=<?= $slGroupesUrlPrefix ?>/<?= $groupe['id_groupe'] ?>" style="font-size:0.85rem;color:var(--gray-dark);text-decoration:none;display:inline-flex;align-items:center;gap:4px;margin-bottom:8px;">
                                ← <?= htmlspecialchars($groupe['nom_groupe']) ?>
                            </a>
                            <h1 style="margin:0;font-size:1.5rem;line-height:1.3;"><?= htmlspecialchars($discussion['titre']) ?></h1>
                        </div>
                        <div style="display:flex;gap:8px;flex-shrink:0;flex-wrap:wrap;align-items:flex-start;">
                            <?php if ($discussionApproved): ?>
                            <a href="<?= APP_URL ?>/index.php?url=<?= $slGroupesUrlPrefix ?>/<?= $groupe['id_groupe'] ?>/discussions/<?= $discussion['id_discussion'] ?>/like" class="btn btn-outline" style="padding:6px 14px;font-size:0.85rem;" id="btn-like-disc">❤️ <?= $discussion['nb_likes'] ?></a>
                            <?php endif; ?>
                            <?php if($discussionApproved && ((int)$discussion['id_auteur'] === $userId || $isGroupAdmin)): ?>
                            <a href="<?= APP_URL ?>/index.php?url=<?= $slGroupesUrlPrefix ?>/<?= $groupe['id_groupe'] ?>/discussions/<?= $discussion['id_discussion'] ?>/delete"
                               class="btn" style="padding:6px 14px;font-size:0.85rem;background:#fee2e2;color:#ef4444;" id="btn-del-disc"
                               data-confirm="Supprimer cette discussion et tous ses messages ?">🗑 Supprimer</a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Discussion contenu -->
                    <div class="sl-disc-body">
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
                            <div class="sl-avatar-sm"><?= strtoupper(mb_substr($discussion['nom_auteur'], 0, 1)) ?></div>
                            <div>
                                <strong><?= htmlspecialchars($discussion['nom_auteur']) ?></strong>
                                <small style="color:var(--gray-dark);margin-left:8px;"><?= date('d/m/Y à H:i', strtotime($discussion['date_creation'])) ?></small>
                            </div>
                        </div>
                        <p style="margin:0;line-height:1.7;white-space:pre-wrap;"><?= htmlspecialchars($discussion['contenu']) ?></p>
                    </div>

                    <!-- Edit inline (auteur ou admin du groupe) -->
                    <?php if($discussionApproved && ((int)$discussion['id_auteur'] === $userId || $isGroupAdmin)): ?>
                    <details style="margin-top:12px;">
                        <summary style="cursor:pointer;color:var(--secondary-color);font-size:0.85rem;font-weight:600;list-style:none;">✏ Modifier cette discussion</summary>
                        <form method="POST" action="<?= APP_URL ?>/index.php?url=<?= $slGroupesUrlPrefix ?>/<?= $groupe['id_groupe'] ?>/discussions/<?= $discussion['id_discussion'] ?>/update" style="margin-top:16px;" id="form-edit-disc" novalidate>
                            <?php if(!empty($errors)): ?>
                            <div class="sl-errors" style="margin-bottom:12px;">
                                <ul style="margin:0;padding-left:20px;"><?php foreach($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
                            </div>
                            <?php endif; ?>
                            <div class="sl-form-group" id="fg-edit-titre">
                                <label class="sl-label" for="edit_titre">Titre</label>
                                <input type="text" id="edit_titre" name="titre" class="sl-input" value="<?= htmlspecialchars($discussion['titre']) ?>">
                                <div class="sl-field-error" id="err-edit-titre"></div>
                            </div>
                            <div class="sl-form-group" style="margin-top:14px;" id="fg-edit-contenu">
                                <label class="sl-label" for="edit_contenu">Contenu</label>
                                <textarea id="edit_contenu" name="contenu" rows="5" class="sl-input sl-textarea"><?= htmlspecialchars($discussion['contenu']) ?></textarea>
                                <div class="sl-field-error" id="err-edit-contenu"></div>
                            </div>
                            <button type="submit" class="btn btn-primary" id="btn-update-disc" style="margin-top:14px;padding:10px 24px;">Enregistrer</button>
                        </form>
                    </details>
                    <?php endif; ?>
                </div>

                <!-- Messages -->
                <?php if ($discussionApproved): ?>
                <div style="margin-bottom:24px;">
                    <h2 style="font-size:1.1rem;margin-bottom:16px;">💬 Réponses (<?= count($messages) ?>)</h2>

                    <?php foreach($messages as $m): ?>
                    <div class="sl-message" id="msg-<?= $m['id_message'] ?>">
                        <div class="sl-avatar-sm"><?= strtoupper(mb_substr($m['nom_auteur'], 0, 1)) ?></div>
                        <div style="flex:1;min-width:0;">
                            <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;margin-bottom:6px;">
                                <div>
                                    <strong><?= htmlspecialchars($m['nom_auteur']) ?></strong>
                                    <small style="color:var(--gray-dark);margin-left:8px;"><?= date('d/m/Y H:i', strtotime($m['date_envoi'])) ?></small>
                                </div>
                                <?php if((int)$m['id_auteur'] === $userId || $isGroupAdmin): ?>
                                <a href="<?= APP_URL ?>/index.php?url=<?= $slGroupesUrlPrefix ?>/<?= $groupe['id_groupe'] ?>/discussions/<?= $discussion['id_discussion'] ?>/messages/<?= $m['id_message'] ?>/delete"
                                   class="sl-del-msg" data-confirm="Supprimer ce message ?" id="btn-del-msg-<?= $m['id_message'] ?>">🗑</a>
                                <?php endif; ?>
                            </div>
                            <p style="margin:0;line-height:1.6;white-space:pre-wrap;"><?= htmlspecialchars($m['contenu']) ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <!-- Pagination messages -->
                    <?php if($totalPages > 1): ?>
                    <div class="sl-pagination" style="margin-top:16px;">
                        <?php for($p=1;$p<=$totalPages;$p++): ?>
                        <a href="?url=<?= $slGroupesUrlPrefix ?>/<?= $groupe['id_groupe'] ?>/discussions/<?= $discussion['id_discussion'] ?>&page=<?= $p ?>" class="sl-page-btn <?= $p===$currentPage?'active':'' ?>"><?= $p ?></a>
                        <?php endfor; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- Formulaire réponse -->
                <?php if($discussionApproved && $isMember): ?>
                <div class="table-container" style="margin-bottom:24px;">
                    <div class="table-header"><h3 style="margin:0;">✍ Votre réponse</h3></div>
                    <div style="padding:24px;">
                        <?php $msgErreurs = $errors ?? []; ?>
                        <?php if(!empty($msgErreurs)): ?>
                        <div class="sl-errors" style="margin-bottom:14px;">
                            <ul style="margin:0;padding-left:20px;"><?php foreach($msgErreurs as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
                        </div>
                        <?php endif; ?>

                        <form method="POST" action="<?= APP_URL ?>/index.php?url=<?= $slGroupesUrlPrefix ?>/<?= $groupe['id_groupe'] ?>/discussions/<?= $discussion['id_discussion'] ?>/messages/store" id="form-reply" novalidate>
                            <div class="sl-form-group" id="fg-reply">
                                <label class="sl-label" for="reply_contenu">Message <span style="color:#ef4444;">*</span></label>
                                <textarea id="reply_contenu" name="contenu" rows="4" class="sl-input sl-textarea"
                                          placeholder="Écrivez votre réponse…"><?= htmlspecialchars($msgContenu ?? '') ?></textarea>
                                <div class="sl-field-error" id="err-reply"></div>
                            </div>
                            <div style="margin-top:14px;">
                                <button type="submit" class="btn btn-primary" id="btn-send-reply" style="padding:10px 24px;">
                                    <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:6px;"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                                    Envoyer
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php elseif ($discussionApproved): ?>
                <div class="table-container" style="padding:24px;text-align:center;margin-bottom:24px;">
                    <p style="color:var(--gray-dark);">Vous devez rejoindre le groupe pour répondre.</p>
                    <a href="<?= APP_URL ?>/index.php?url=<?= $slGroupesUrlPrefix ?>/<?= $groupe['id_groupe'] ?>/join" class="btn btn-primary" style="margin-top:10px;">Rejoindre le groupe</a>
                </div>
                <?php endif; ?>

                <a href="<?= APP_URL ?>/index.php?url=<?= $slGroupesUrlPrefix ?>/<?= $groupe['id_groupe'] ?>" class="btn btn-outline">← Retour au groupe</a>
            </div>
        </div>
    </div>
</div>

<script>
(function(){
    'use strict';
    // Validation du formulaire de réponse
    var replyForm = document.getElementById('form-reply');
    if(replyForm){
        replyForm.addEventListener('submit', function(ev){
            var fg  = document.getElementById('fg-reply');
            var err = document.getElementById('err-reply');
            var val = document.getElementById('reply_contenu').value.trim();
            if(fg) fg.classList.remove('sl-has-error');
            if(err){ err.textContent=''; err.style.display='none'; }

            if(val === ''){
                if(fg) fg.classList.add('sl-has-error');
                if(err){ err.textContent='Le message est obligatoire.'; err.style.display='block'; }
                ev.preventDefault(); return;
            }
            if(val.length < 10){
                if(fg) fg.classList.add('sl-has-error');
                if(err){ err.textContent='Le message doit contenir au moins 10 caractères.'; err.style.display='block'; }
                ev.preventDefault();
            }
        });
    }

    // Validation formulaire édition discussion
    var editForm = document.getElementById('form-edit-disc');
    if(editForm){
        editForm.addEventListener('submit', function(ev){
            var ok = true;
            ['titre','contenu'].forEach(function(field){
                var fg  = document.getElementById('fg-edit-'+field);
                var err = document.getElementById('err-edit-'+field);
                var val = document.getElementById('edit_'+field).value.trim();
                if(fg) fg.classList.remove('sl-has-error');
                if(err){ err.textContent=''; err.style.display='none'; }
                var min = field === 'titre' ? 5 : 10;
                if(val.length < min){
                    if(fg) fg.classList.add('sl-has-error');
                    if(err){ err.textContent='Minimum ' + min + ' caractères requis.'; err.style.display='block'; }
                    ok = false;
                }
            });
            if(!ok) ev.preventDefault();
        });
    }

    // Auto-scroll vers le bas des messages
    var lastMsg = document.querySelector('.sl-message:last-child');
    if(lastMsg) lastMsg.scrollIntoView({behavior:'smooth', block:'nearest'});
})();
</script>
