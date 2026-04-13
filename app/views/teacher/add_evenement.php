<?php
/**
 * APPOLIOS - Teacher Add Evenement
 */

$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);

$minDate = date('Y-m-d', strtotime('+1 day'));

$teacherSidebarActive = 'evenements';
?>

<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main">
        <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
            <div>
                <h1>Propose New Evenement</h1>
                <p>Your evenement will be sent to admin review (pending approval)</p>
            </div>
            <a href="<?= APP_URL ?>/index.php?url=teacher/evenements" class="btn btn-outline" style="padding: 10px 20px;">Back to My Evenements</a>
        </div>

        <div class="form-container" style="max-width: 760px;">
            <form action="<?= APP_URL ?>/index.php?url=teacher/store-evenement" method="POST">
                <div class="form-group">
                    <label for="title">Titre *</label>
                    <input type="text" id="title" name="title" placeholder="Enter event title" value="<?= htmlspecialchars($old['title'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="description">Description *</label>
                    <textarea id="description" name="description" placeholder="Describe the event" required><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label for="date_debut">Date Debut *</label>
                    <input type="date" id="date_debut" name="date_debut" min="<?= $minDate ?>" value="<?= htmlspecialchars($old['date_debut'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="date_fin">Date Fin</label>
                    <input type="date" id="date_fin" name="date_fin" value="<?= htmlspecialchars($old['date_fin'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="heure_debut">Heure Debut *</label>
                    <input type="time" id="heure_debut" name="heure_debut" value="<?= htmlspecialchars($old['heure_debut'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="heure_fin">Heure Fin</label>
                    <input type="time" id="heure_fin" name="heure_fin" value="<?= htmlspecialchars($old['heure_fin'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="lieu">Lieu</label>
                    <input type="text" id="lieu" name="lieu" placeholder="Online / Room A / Main Hall" value="<?= htmlspecialchars($old['lieu'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="capacite_max">Capacite Max</label>
                    <input type="number" id="capacite_max" name="capacite_max" min="0" placeholder="Ex: 200" value="<?= htmlspecialchars($old['capacite_max'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="type">Type</label>
                    <input type="text" id="type" name="type" placeholder="conference, workshop..." value="<?= htmlspecialchars($old['type'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="statut">Statut</label>
                    <?php $selectedStatut = $old['statut'] ?? 'planifie'; ?>
                    <select id="statut" name="statut">
                        <option value="planifie" <?= $selectedStatut === 'planifie' ? 'selected' : '' ?>>Planifie</option>
                        <option value="en_cours" <?= $selectedStatut === 'en_cours' ? 'selected' : '' ?>>En cours</option>
                        <option value="termine" <?= $selectedStatut === 'termine' ? 'selected' : '' ?>>Termine</option>
                        <option value="annule" <?= $selectedStatut === 'annule' ? 'selected' : '' ?>>Annule</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-yellow btn-block" style="margin-top: 20px;">Submit For Approval</button>
            </form>
        </div>
            </div>
        </div>
    </div>
</div>
