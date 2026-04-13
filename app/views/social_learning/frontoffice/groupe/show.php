<?php
$title = htmlspecialchars($groupe['nom_groupe'] ?? 'Groupe') . ' — APPOLIOS';
$description = 'Détail du groupe';
$studentSidebarActive = 'groupes';
$slBase = APP_URL . '/index.php?url=social-learning/';
$groupePendingApproval = $groupePendingApproval ?? false;
require __DIR__ . '/../../../partials/header.php';
?>

<div class="dashboard student-events-page">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/../../../student/partials/sidebar.php'; ?>
            <div class="admin-main">
                <?php if (isset($_GET['discussion']) && $_GET['discussion'] === 'request'): ?>
                <div class="flash-message success" style="margin-bottom:16px;">Demande enregistrée : la discussion est en attente de validation par un administrateur.</div>
                <?php endif; ?>

                <div class="dashboard-header" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
                    <div>
                        <h1><?= htmlspecialchars($groupe['nom_groupe']) ?></h1>
                        <p><?= nl2br(htmlspecialchars($groupe['description'])) ?></p>
                    </div>
                    <div>
                        <?php if (!empty($canManage)): ?>
                            <a class="btn btn-sm btn-outline-secondary" href="<?= $slBase ?>groupe/edit/<?= $groupe['id_groupe'] ?>">Modifier</a>
                            <a class="btn btn-sm btn-danger" href="<?= $slBase ?>groupe/delete/<?= $groupe['id_groupe'] ?>" data-confirm="Supprimer ce groupe ?">Supprimer</a>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($groupePendingApproval): ?>
                <div class="flash-message error" style="margin:16px 0;">Ce groupe est en attente d'approbation par un administrateur. Il n'apparaît pas dans le catalogue et les nouvelles discussions sont désactivées jusqu'à validation.</div>
                <?php endif; ?>

                <hr>
                <h4>Discussions</h4>
                <?php if (!$groupePendingApproval): ?>
                <a class="btn btn-sm btn-primary mb-2" href="<?= $slBase ?>discussion/create?id_groupe=<?= (int)$groupe['id_groupe'] ?>">Créer une discussion</a>
                <?php endif; ?>
                <?php foreach ($discussions as $d): ?>
                  <div class="card mb-2" style="border-radius:16px;">
                    <div class="card-body">
                      <h5><?= htmlspecialchars($d['titre']) ?></h5>
                      <p><?= nl2br(htmlspecialchars(mb_substr($d['contenu'] ?? '', 0, 280))) ?><?= mb_strlen($d['contenu'] ?? '') > 280 ? '…' : '' ?></p>
                      <div style="display:flex;flex-wrap:wrap;gap:8px;">
                        <a href="<?= $slBase ?>discussion/show/<?= (int)$d['id_discussion'] ?>" class="btn btn-sm btn-outline-primary">Voir</a>
                        <?php if (!empty($canManage)): ?>
                        <a href="<?= $slBase ?>discussion/edit/<?= (int)$d['id_discussion'] ?>" class="btn btn-sm btn-outline-secondary">Modifier</a>
                        <a href="<?= $slBase ?>discussion/delete/<?= (int)$d['id_discussion'] ?>" class="btn btn-sm btn-outline-danger" data-confirm="Supprimer cette discussion ?">Supprimer</a>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../../partials/footer.php'; ?>
