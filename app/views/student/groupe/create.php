<?php
/**
 * FrontOffice — formulaire création groupe
 * $errors[], $old[]
 */
$studentSidebarActive = 'groupes';
?>

<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/../../partials/sidebar_student.php'; ?>

            <div class="admin-main">
                <div class="dashboard-header" style="display:flex;align-items:center;gap:12px;margin-bottom:30px;">
                    <a href="<?= APP_URL ?>/index.php?url=<?= $slGroupesUrlPrefix ?>" class="btn btn-outline" style="padding:8px 14px;">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                    </a>
                    <div>
                        <h1 style="margin:0;font-size:1.6rem;">Créer un groupe</h1>
                        <p style="margin:4px 0 0;color:var(--gray-dark);">Lancez votre communauté d'apprentissage</p>
                    </div>
                </div>

                <?php if (!empty($flash)): ?>
                <div class="flash-message <?= htmlspecialchars($flash['type']) ?>" style="margin-bottom:20px;"><?= htmlspecialchars($flash['message']) ?></div>
                <?php endif; ?>

                <div class="flash-message info" style="margin-bottom:20px;">
                    Votre groupe sera enregistré comme <strong>demande</strong> : un administrateur devra l’accepter avant qu’il soit visible pour tous dans le catalogue.
                </div>

                <div class="table-container" style="max-width:700px;">
                    <div class="table-header">
                        <h3 style="margin:0;">Informations du groupe</h3>
                    </div>
                    <div style="padding:32px;">

                        <!-- Erreurs globales -->
                        <?php if (!empty($errors)): ?>
                        <div class="sl-errors" id="server-errors">
                            <ul style="margin:0;padding-left:20px;">
                                <?php foreach ($errors as $e): ?>
                                <li><?= htmlspecialchars($e) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>

                        <form method="POST" action="<?= APP_URL ?>/index.php?url=<?= $slGroupesUrlPrefix ?>/store" id="form-create-groupe" novalidate>

                            <!-- Nom du groupe -->
                            <div class="sl-form-group" id="fg-nom">
                                <label class="sl-label" for="nom_groupe">Nom du groupe <span style="color:#ef4444;">*</span></label>
                                <input type="text" id="nom_groupe" name="nom_groupe"
                                       class="sl-input"
                                       placeholder="Ex : Data Science Avancée"
                                       value="<?= htmlspecialchars($old['nom_groupe'] ?? '') ?>"
                                       autocomplete="off">
                                <div class="sl-field-error" id="err-nom"></div>
                                <small style="color:var(--gray-dark);margin-top:4px;display:block;">Minimum 3 caractères, maximum 100.</small>
                            </div>

                            <!-- Description -->
                            <div class="sl-form-group" id="fg-desc" style="margin-top:20px;">
                                <label class="sl-label" for="description">Description <span style="color:#ef4444;">*</span></label>
                                <textarea id="description" name="description" rows="5"
                                          class="sl-input sl-textarea"
                                          placeholder="Décrivez l'objectif de ce groupe…"><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
                                <div class="sl-field-error" id="err-desc"></div>
                                <small style="color:var(--gray-dark);margin-top:4px;display:block;">Minimum 10 caractères.</small>
                            </div>

                            <div style="display:flex;gap:12px;margin-top:28px;flex-wrap:wrap;">
                                <button type="submit" class="btn btn-primary" id="btn-submit-groupe" style="padding:12px 28px;">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:6px;"><polyline points="20 6 9 17 4 12"/></svg>
                                    Créer le groupe
                                </button>
                                <a href="<?= APP_URL ?>/index.php?url=<?= $slGroupesUrlPrefix ?>" class="btn btn-outline" style="padding:12px 24px;">Annuler</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    'use strict';
    var form = document.getElementById('form-create-groupe');
    if (!form) return;

    function showError(fieldId, errId, msg) {
        var fg  = document.getElementById(fieldId);
        var err = document.getElementById(errId);
        if (fg)  fg.classList.add('sl-has-error');
        if (err) { err.textContent = msg; err.style.display = 'block'; }
    }
    function clearError(fieldId, errId) {
        var fg  = document.getElementById(fieldId);
        var err = document.getElementById(errId);
        if (fg)  fg.classList.remove('sl-has-error');
        if (err) { err.textContent = ''; err.style.display = 'none'; }
    }

    form.addEventListener('submit', function(e) {
        var valid = true;
        clearError('fg-nom', 'err-nom');
        clearError('fg-desc', 'err-desc');

        var nom  = document.getElementById('nom_groupe').value.trim();
        var desc = document.getElementById('description').value.trim();

        if (nom === '') {
            showError('fg-nom', 'err-nom', 'Le nom du groupe est obligatoire.');
            valid = false;
        } else if (nom.length < 3) {
            showError('fg-nom', 'err-nom', 'Le nom doit contenir au moins 3 caractères.');
            valid = false;
        } else if (nom.length > 100) {
            showError('fg-nom', 'err-nom', 'Le nom ne peut pas dépasser 100 caractères.');
            valid = false;
        }

        if (desc === '') {
            showError('fg-desc', 'err-desc', 'La description est obligatoire.');
            valid = false;
        } else if (desc.length < 10) {
            showError('fg-desc', 'err-desc', 'La description doit contenir au moins 10 caractères.');
            valid = false;
        }

        if (!valid) e.preventDefault();
    });

    // Live feedback
    document.getElementById('nom_groupe').addEventListener('input', function() {
        clearError('fg-nom', 'err-nom');
    });
    document.getElementById('description').addEventListener('input', function() {
        clearError('fg-desc', 'err-desc');
    });
})();
</script>
