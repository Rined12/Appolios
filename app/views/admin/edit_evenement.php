<?php
/**
 * APPOLIOS - Edit Evenement Page
 */

$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);

$minDate = date('Y-m-d', strtotime('+1 day'));

$form = [
    'title' => $old['title'] ?? ($evenement['titre'] ?? $evenement['title'] ?? ''),
    'description' => $old['description'] ?? ($evenement['description'] ?? ''),
    'date_debut' => $old['date_debut'] ?? ($evenement['date_debut'] ?? ''),
    'date_fin' => $old['date_fin'] ?? ($evenement['date_fin'] ?? ''),
    'heure_debut' => $old['heure_debut'] ?? (isset($evenement['heure_debut']) ? substr((string) $evenement['heure_debut'], 0, 5) : ''),
    'heure_fin' => $old['heure_fin'] ?? (isset($evenement['heure_fin']) ? substr((string) $evenement['heure_fin'], 0, 5) : ''),
    'lieu' => $old['lieu'] ?? (($evenement['lieu'] ?? '') ?: ($evenement['location'] ?? '')),
    'capacite_max' => $old['capacite_max'] ?? ($evenement['capacite_max'] ?? ''),
    'type' => $old['type'] ?? ($evenement['type'] ?? 'general'),
    'statut' => $old['statut'] ?? ($evenement['statut'] ?? 'planifie')
];
?>

<div class="dashboard">
    <div class="container">
        <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
            <div>
                <h1>Edit Evenement</h1>
                <p>Update the selected event details</p>
            </div>
            <a href="<?= APP_URL ?>/index.php?url=admin/evenements" class="btn btn-outline" style="padding: 10px 20px;">Back to Evenements</a>
        </div>

        <div class="form-container" style="max-width: 760px;">
            <form action="<?= APP_URL ?>/index.php?url=admin/update-evenement/<?= (int) ($evenement['id'] ?? 0) ?>" method="POST">
                <div class="form-group">
                    <label for="title">Titre *</label>
                    <input type="text" id="title" name="title" placeholder="Enter event title" value="<?= htmlspecialchars($form['title']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="description">Description *</label>
                    <textarea id="description" name="description" placeholder="Describe the event" required><?= htmlspecialchars($form['description']) ?></textarea>
                </div>

                <div class="form-group">
                    <label for="date_debut">Date Debut *</label>
                    <input type="date" id="date_debut" name="date_debut" min="<?= $minDate ?>" value="<?= htmlspecialchars($form['date_debut']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="date_fin">Date Fin</label>
                    <input type="date" id="date_fin" name="date_fin" value="<?= htmlspecialchars($form['date_fin']) ?>">
                </div>

                <div class="form-group">
                    <label for="heure_debut">Heure Debut *</label>
                    <input type="time" id="heure_debut" name="heure_debut" value="<?= htmlspecialchars($form['heure_debut']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="heure_fin">Heure Fin</label>
                    <input type="time" id="heure_fin" name="heure_fin" value="<?= htmlspecialchars($form['heure_fin']) ?>">
                </div>

                <div class="form-group">
                    <label for="lieu">Lieu</label>
                    <input type="text" id="lieu" name="lieu" placeholder="Online / Room A / Main Hall" value="<?= htmlspecialchars($form['lieu']) ?>">
                </div>

                <div class="form-group">
                    <label for="capacite_max">Capacite Max</label>
                    <input type="number" id="capacite_max" name="capacite_max" min="0" placeholder="Ex: 200" value="<?= htmlspecialchars((string) $form['capacite_max']) ?>">
                </div>

                <div class="form-group">
                    <label for="type">Type</label>
                    <input type="text" id="type" name="type" placeholder="conference, workshop..." value="<?= htmlspecialchars($form['type']) ?>">
                </div>

                <div class="form-group">
                    <label for="statut">Statut</label>
                    <select id="statut" name="statut">
                        <option value="planifie" <?= $form['statut'] === 'planifie' ? 'selected' : '' ?>>Planifie</option>
                        <option value="en_cours" <?= $form['statut'] === 'en_cours' ? 'selected' : '' ?>>En cours</option>
                        <option value="termine" <?= $form['statut'] === 'termine' ? 'selected' : '' ?>>Termine</option>
                        <option value="annule" <?= $form['statut'] === 'annule' ? 'selected' : '' ?>>Annule</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-yellow btn-block" style="margin-top: 20px;">Sauvegarder</button>
            </form>
        </div>
    </div>
</div>
