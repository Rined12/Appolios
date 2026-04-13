<?php
/**
 * APPOLIOS - Teacher Evenement Resources Workspace
 */

$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);
$selectedEvenementId = (int) ($selectedEvenementId ?? 0);
$selectedEvenementTitle = $selectedEvenement['title'] ?? $selectedEvenement['titre'] ?? '';
$editResource = $editResource ?? null;

$teacherSidebarActive = 'evenements';
?>

<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main">
        <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
            <div>
                <h1>Ressources Evenement</h1>
                <p>Manage rules, materiel, and day plans for your evenement</p>
                <p style="margin-bottom: 0;"><strong>Evenement:</strong> <?= htmlspecialchars($selectedEvenementTitle) ?></p>
            </div>
            <div style="display: flex; gap: 10px;">
                <a href="<?= APP_URL ?>/index.php?url=teacher/evenements" class="btn btn-outline">Back to My Evenements</a>
            </div>
        </div>

        <div class="ressource-workspace">
            <div class="ressource-forms-col">
                <div class="ressource-form-card">
                    <h3>Ajouter Rule</h3>
                    <form action="<?= APP_URL ?>/index.php?url=teacher/store-evenement-ressource" method="POST" class="resource-create-form" data-type-label="Rule">
                        <input type="hidden" name="type" value="rule">
                        <input type="hidden" name="evenement_id" value="<?= (int) $selectedEvenementId ?>">
                        <div class="form-group">
                            <label for="rule_title">Rule Title *</label>
                            <input type="text" id="rule_title" name="title" placeholder="Example: Respect start time" value="<?= htmlspecialchars(($old['type'] ?? '') === 'rule' ? ($old['title'] ?? '') : '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="rule_details">Rule Details</label>
                            <textarea id="rule_details" name="details" placeholder="Explain this rule clearly"><?= htmlspecialchars(($old['type'] ?? '') === 'rule' ? ($old['details'] ?? '') : '') ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Rule</button>
                    </form>
                </div>

                <div class="ressource-form-card">
                    <h3>Ajouter Materiel</h3>
                    <form action="<?= APP_URL ?>/index.php?url=teacher/store-evenement-ressource" method="POST" class="resource-create-form" data-type-label="Materiel">
                        <input type="hidden" name="type" value="materiel">
                        <input type="hidden" name="evenement_id" value="<?= (int) $selectedEvenementId ?>">
                        <div class="form-group">
                            <label for="material_title">Materiel Name *</label>
                            <input type="text" id="material_title" name="title" placeholder="Example: Projector" value="<?= htmlspecialchars(($old['type'] ?? '') === 'materiel' ? ($old['title'] ?? '') : '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="material_details">Materiel Details</label>
                            <textarea id="material_details" name="details" placeholder="Quantity, room, notes..."><?= htmlspecialchars(($old['type'] ?? '') === 'materiel' ? ($old['details'] ?? '') : '') ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Materiel</button>
                    </form>
                </div>

                <div class="ressource-form-card">
                    <h3>Ajouter Plan (Journee)</h3>
                    <form action="<?= APP_URL ?>/index.php?url=teacher/store-evenement-ressource" method="POST" class="resource-create-form" data-type-label="Plan">
                        <input type="hidden" name="type" value="plan">
                        <input type="hidden" name="evenement_id" value="<?= (int) $selectedEvenementId ?>">
                        <div class="form-group">
                            <label for="plan_title">Plan Item *</label>
                            <input type="text" id="plan_title" name="title" placeholder="Example: 09:00 - Opening" value="<?= htmlspecialchars(($old['type'] ?? '') === 'plan' ? ($old['title'] ?? '') : '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="plan_details">Plan Details</label>
                            <textarea id="plan_details" name="details" placeholder="Speaker, duration, objectives..."><?= htmlspecialchars(($old['type'] ?? '') === 'plan' ? ($old['details'] ?? '') : '') ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Plan</button>
                    </form>
                </div>
            </div>

            <div class="ressource-lists-col">
                <?php
                $groups = [
                    'Rules' => ['key' => 'rules', 'type' => 'rule'],
                    'Materiels' => ['key' => 'materials', 'type' => 'materiel'],
                    'Plans' => ['key' => 'plans', 'type' => 'plan']
                ];
                ?>
                <?php foreach ($groups as $groupTitle => $groupConfig): ?>
                    <div class="ressource-list-card">
                        <h3>Liste <?= htmlspecialchars($groupTitle) ?></h3>
                        <ul class="ressource-list">
                            <?php $items = ${$groupConfig['key']} ?? []; ?>
                            <?php if (!empty($items)): ?>
                                <?php foreach ($items as $item): ?>
                                    <li>
                                        <?php if ($editResource && (int) $editResource['id'] === (int) $item['id']): ?>
                                            <form action="<?= APP_URL ?>/index.php?url=teacher/update-evenement-ressource/<?= (int) $item['id'] ?>" method="POST">
                                                <input type="hidden" name="evenement_id" value="<?= (int) $selectedEvenementId ?>">
                                                <div class="form-group" style="margin-bottom: 8px;">
                                                    <input type="text" name="title" value="<?= htmlspecialchars($item['title']) ?>" required>
                                                </div>
                                                <div class="form-group" style="margin-bottom: 8px;">
                                                    <textarea name="details" placeholder="Details..."><?= htmlspecialchars($item['details'] ?? '') ?></textarea>
                                                </div>
                                                <button type="submit" class="btn btn-primary action-btn">Sauvegarder</button>
                                                <a href="<?= APP_URL ?>/index.php?url=teacher/evenement-ressources&evenement_id=<?= (int) $selectedEvenementId ?>" class="btn btn-outline action-btn">Annuler</a>
                                            </form>
                                        <?php else: ?>
                                            <strong><?= htmlspecialchars($item['title']) ?></strong>
                                            <?php if (!empty($item['details'])): ?><p><?= htmlspecialchars($item['details']) ?></p><?php endif; ?>
                                            <div class="ressource-actions">
                                                <a href="<?= APP_URL ?>/index.php?url=teacher/evenement-ressources&evenement_id=<?= (int) $selectedEvenementId ?>&edit_id=<?= (int) $item['id'] ?>" class="btn btn-secondary action-btn">Edit</a>
                                                <form action="<?= APP_URL ?>/index.php?url=teacher/delete-evenement-ressource/<?= (int) $item['id'] ?>" method="POST" style="display: inline-block;">
                                                    <input type="hidden" name="evenement_id" value="<?= (int) $selectedEvenementId ?>">
                                                    <button type="submit" class="btn action-btn danger" data-confirm="Delete this resource item?">Delete</button>
                                                </form>
                                            </div>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="empty">No <?= strtolower($groupTitle) ?> added yet.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="ressource-save-all">
            <button type="button" id="saveAllTeacherResourcesBtn" class="btn btn-yellow">Sauvegarder Tout</button>
        </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const saveBtn = document.getElementById('saveAllTeacherResourcesBtn');
    if (!saveBtn) {
        return;
    }

    saveBtn.addEventListener('click', async function () {
        const forms = Array.from(document.querySelectorAll('.resource-create-form'));
        const validForms = forms.filter(function (form) {
            const titleInput = form.querySelector('input[name="title"]');
            return titleInput && titleInput.value.trim() !== '';
        });
        const existingRightListItems = document.querySelectorAll('.ressource-list li:not(.empty)');

        if (validForms.length === 0) {
            if (existingRightListItems.length > 0) {
                alert('Resources are already present in the right list and considered saved.');
                window.location.href = '<?= APP_URL ?>/index.php?url=teacher/evenements';
                return;
            }

            alert('No resources found. Add at least one resource first.');
            return;
        }

        saveBtn.disabled = true;
        saveBtn.textContent = 'Sauvegarde en cours...';

        let savedCount = 0;
        const errors = [];

        for (const form of validForms) {
            const data = new FormData(form);
            data.append('batch_mode', '1');

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: data,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const result = await response.json();
                if (result.success && result.verified_in_right_list === true) {
                    savedCount += 1;
                } else {
                    errors.push(result.message || ('Verification failed for ' + (form.dataset.typeLabel || 'resource')));
                }
            } catch (err) {
                errors.push('Network error while saving resources.');
            }
        }

        if (errors.length > 0) {
            alert('Saved: ' + savedCount + '\nErrors:\n- ' + errors.join('\n- '));
        } else {
            alert('All resources saved successfully (' + savedCount + ').');
        }

        window.location.href = '<?= APP_URL ?>/index.php?url=teacher/evenements';
    });
})();
</script>
