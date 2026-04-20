<?php
$title = 'Modifier la discussion — APPOLIOS';
$description = 'Édition de la discussion';
$studentSidebarActive = 'discussions';
$slBase = APP_URL . '/index.php?url=social-learning/';
require __DIR__ . '/../../../partials/header.php';
?>

<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/../../../student/partials/sidebar.php'; ?>

            <div class="admin-main">
                <div class="dashboard-header" style="display:flex;align-items:center;gap:12px;margin-bottom:30px;">
                    <a href="<?= $slBase ?>discussion/show/<?= (int)$discussion['id_discussion'] ?>" class="btn btn-outline" style="padding:8px 14px;">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                    </a>
                    <div>
                        <h1 style="margin:0;font-size:1.6rem;">Modifier la discussion</h1>
                        <?php if (!empty($discussion['nom_groupe'])): ?>
                        <p style="margin:4px 0 0;color:var(--gray-dark);">Groupe : <?= htmlspecialchars($discussion['nom_groupe']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="table-container" style="max-width:700px;">
                    <div class="table-header">
                        <h3 style="margin:0;">Contenu</h3>
                    </div>
                    <div style="padding:32px;">

                        <?php if (!empty($errors)): ?>
                        <div class="sl-errors" style="margin-bottom:20px;">
                            <ul style="margin:0;padding-left:20px;">
                                <?php foreach ($errors as $e): ?>
                                <li><?= htmlspecialchars($e) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>

                        <form method="POST" action="<?= $slBase ?>discussion/update/<?= (int)$discussion['id_discussion'] ?>" id="form-edit-discussion" novalidate>

                            <div class="sl-form-group" id="fg-titre">
                                <label class="sl-label" for="titre">Titre <span style="color:#ef4444;">*</span></label>
                                <input type="text" id="titre" name="titre" class="sl-input" maxlength="200"
                                       value="<?= htmlspecialchars($discussion['titre'] ?? '') ?>" required>
                                <div class="sl-field-error" id="err-titre"></div>
                                <small style="color:var(--gray-dark);margin-top:4px;display:block;">Entre 5 et 200 caractères.</small>
                            </div>

                            <div class="sl-form-group" id="fg-contenu" style="margin-top:20px;">
                                <label class="sl-label" for="contenu">Contenu <span style="color:#ef4444;">*</span></label>
                                <textarea id="contenu" name="contenu" rows="8" class="sl-input sl-textarea" required><?= htmlspecialchars($discussion['contenu'] ?? '') ?></textarea>
                                <div class="sl-field-error" id="err-contenu"></div>
                                <small style="color:var(--gray-dark);margin-top:4px;display:block;">Minimum 10 caractères.</small>
                            </div>

                            <div style="display:flex;gap:12px;margin-top:28px;flex-wrap:wrap;">
                                <button type="submit" class="btn btn-primary" style="padding:12px 28px;">Enregistrer</button>
                                <a href="<?= $slBase ?>discussion/show/<?= (int)$discussion['id_discussion'] ?>" class="btn btn-outline" style="padding:12px 24px;">Annuler</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../../partials/footer.php'; ?>
