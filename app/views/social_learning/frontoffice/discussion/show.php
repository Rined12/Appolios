<?php
$title = htmlspecialchars($discussion['titre'] ?? 'Discussion') . ' — APPOLIOS';
$description = 'Discussion';
$studentSidebarActive = 'discussions';
$slBase = APP_URL . '/index.php?url=social-learning/';
$discussionApproved = $discussionApproved ?? true;
require __DIR__ . '/../../../partials/header.php';
?>

<div class="dashboard student-events-page">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/../../../student/partials/sidebar.php'; ?>
            <div class="admin-main">
                <div class="dashboard-header" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
                    <div>
                        <h1><?= htmlspecialchars($discussion['titre']) ?></h1>
                    </div>
                    <div>
                        <?php if (!empty($canManageDiscussion)): ?>
                          <a class="btn btn-sm btn-outline-secondary" href="<?= $slBase ?>discussion/edit/<?= $discussion['id_discussion'] ?>">Modifier</a>
                          <a class="btn btn-sm btn-danger" href="<?= $slBase ?>discussion/delete/<?= $discussion['id_discussion'] ?>" data-confirm="Supprimer la discussion ?">Supprimer</a>
                        <?php endif; ?>
                    </div>
                </div>

                <p><?= nl2br(htmlspecialchars($discussion['contenu'])) ?></p>
                <?php if (!$discussionApproved): ?>
                <div class="flash-message error" style="margin:16px 0;">Cette discussion est en attente de validation par un administrateur. Les réponses ne sont pas encore ouvertes.</div>
                <?php endif; ?>
                <hr>
                <h4>Messages</h4>
                <?php if (!empty($_SESSION['form_errors'])): ?>
                  <div class="flash-message error" style="margin-bottom:16px;"><ul style="margin:0;padding-left:20px;"><?php foreach($_SESSION['form_errors'] as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div>
                  <?php unset($_SESSION['form_errors']); endif; ?>

                <?php foreach ($messages as $m): ?>
                  <div class="border p-2 mb-2" style="border-radius:12px;border-color:var(--border-color, #e5e7eb);">
                    <strong><?= htmlspecialchars($m['nom_auteur'] ?? 'Utilisateur') ?></strong>
                    <div><?= nl2br(htmlspecialchars($m['contenu'])) ?></div>
                    <small style="color:var(--gray-dark);"><?= htmlspecialchars($m['date_envoi'] ?? '') ?></small>
                    <?php
                    $uid = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
                    $canDelMsg = ($uid && (int)$m['id_auteur'] === $uid) || !empty($canManageDiscussion);
                    ?>
                    <?php if ($canDelMsg): ?>
                      <div class="mt-1"><a class="btn btn-sm btn-outline" style="color:#b91c1c;border-color:#fecaca;" href="<?= $slBase ?>message/delete/<?= (int)$m['id_message'] ?>" data-confirm="Supprimer le message ?">Supprimer</a></div>
                    <?php endif; ?>
                  </div>
                <?php endforeach; ?>

                <?php if ($discussionApproved): ?>
                <form id="messageForm" method="post" action="<?= $slBase ?>message/store">
                  <input type="hidden" name="id_discussion" value="<?= (int)$discussion['id_discussion'] ?>">
                  <div class="sl-form-group" style="margin-top:16px;">
                    <label class="sl-label" for="contenu_msg">Répondre</label>
                    <textarea name="contenu" id="contenu_msg" class="sl-input sl-textarea" rows="3" placeholder="Votre message…"><?= htmlspecialchars($_SESSION['old']['contenu'] ?? '') ?></textarea>
                  </div>
                  <button type="submit" class="btn btn-primary" style="margin-top:8px;">Envoyer</button>
                </form>
                <?php endif; ?>
                <?php unset($_SESSION['old']); ?>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../../partials/footer.php'; ?>
