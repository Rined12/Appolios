<?php
/**
 * APPOLIOS - Teacher Add Evenement (neo theme)
 */

$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);

$minDate = date('Y-m-d', strtotime('+1 day'));

$teacherSidebarActive = 'add-evenement';
?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="<?= APP_URL ?>/View/assets/css/teacher-event-form.css">

<div class="dashboard teacher-event-form-page">
    <div class="container admin-dashboard-container" style="max-width: 1400px; width: 100%;">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>

            <div class="admin-main">
                <div class="teacher-event-form-card">
                    <div class="teacher-event-form-card__head">
                        <a href="<?= APP_ENTRY ?>?url=teacher/evenements">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                            Retour à mes événements
                        </a>
                        <h2>Proposer un <span>événement</span></h2>
                        <p>Votre proposition est envoyée à l’administrateur pour validation avant publication.</p>
                    </div>

                    <div class="teacher-event-form-card__body">
                        <form class="teacher-event-form" action="<?= APP_ENTRY ?>?url=teacher/store-evenement" method="POST" novalidate>
                            <div class="teacher-event-field">
                                <label for="title">Titre <span class="req">*</span></label>
                                <input class="teacher-event-input" type="text" id="title" name="title" placeholder="Ex. : Atelier cartographie des données" value="<?= htmlspecialchars($old['title'] ?? '') ?>">
                                <?php if (isset($errors['title'])): ?>
                                    <div class="teacher-event-field-error"><?= htmlspecialchars($errors['title']) ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="teacher-event-field">
                                <label for="description">Description <span class="req">*</span></label>
                                <textarea class="teacher-event-input" id="description" name="description" placeholder="Public visé, contenu, prérequis…" rows="5"><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
                                <?php if (isset($errors['description'])): ?>
                                    <div class="teacher-event-field-error"><?= htmlspecialchars($errors['description']) ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="teacher-event-row2">
                                <div class="teacher-event-field">
                                    <label for="date_debut">Date de début <span class="req">*</span></label>
                                    <input class="teacher-event-input" type="date" id="date_debut" name="date_debut" min="<?= $minDate ?>" value="<?= htmlspecialchars($old['date_debut'] ?? '') ?>">
                                    <?php if (isset($errors['date_debut'])): ?>
                                        <div class="teacher-event-field-error"><?= htmlspecialchars($errors['date_debut']) ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="teacher-event-field">
                                    <label for="date_fin">Date de fin</label>
                                    <input class="teacher-event-input" type="date" id="date_fin" name="date_fin" value="<?= htmlspecialchars($old['date_fin'] ?? '') ?>">
                                    <?php if (isset($errors['date_fin'])): ?>
                                        <div class="teacher-event-field-error"><?= htmlspecialchars($errors['date_fin']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="teacher-event-row2">
                                <div class="teacher-event-field">
                                    <label for="heure_debut">Heure de début <span class="req">*</span></label>
                                    <input class="teacher-event-input" type="time" id="heure_debut" name="heure_debut" value="<?= htmlspecialchars($old['heure_debut'] ?? '') ?>">
                                    <?php if (isset($errors['heure_debut'])): ?>
                                        <div class="teacher-event-field-error"><?= htmlspecialchars($errors['heure_debut']) ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="teacher-event-field">
                                    <label for="heure_fin">Heure de fin</label>
                                    <input class="teacher-event-input" type="time" id="heure_fin" name="heure_fin" value="<?= htmlspecialchars($old['heure_fin'] ?? '') ?>">
                                    <?php if (isset($errors['heure_fin'])): ?>
                                        <div class="teacher-event-field-error"><?= htmlspecialchars($errors['heure_fin']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="teacher-event-field">
                                <label for="lieu">Lieu <span class="teacher-event-label-hint">(recherche ou clic sur la carte)</span></label>
                                <div class="teacher-event-lieu-wrap">
                                    <svg class="teacher-event-lieu-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.35-4.35"></path></svg>
                                    <input class="teacher-event-input teacher-event-input--padded" type="text" id="lieu" name="lieu" placeholder="Rechercher une adresse…" value="<?= htmlspecialchars($old['lieu'] ?? '') ?>">
                                </div>
                                <div id="teacherAddEventMap" class="teacher-event-map"></div>
                            </div>

                            <div class="teacher-event-row2">
                                <div class="teacher-event-field">
                                    <label for="capacite_max">Capacité max.</label>
                                    <input class="teacher-event-input" type="number" id="capacite_max" name="capacite_max" min="0" placeholder="Ex. : 120" value="<?= htmlspecialchars($old['capacite_max'] ?? '') ?>">
                                    <?php if (isset($errors['capacite_max'])): ?>
                                        <div class="teacher-event-field-error"><?= htmlspecialchars($errors['capacite_max']) ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="teacher-event-field">
                                    <label for="type">Type</label>
                                    <input class="teacher-event-input" type="text" id="type" name="type" placeholder="Conférence, atelier, forum…" value="<?= htmlspecialchars($old['type'] ?? '') ?>">
                                    <?php if (isset($errors['type'])): ?>
                                        <div class="teacher-event-field-error"><?= htmlspecialchars($errors['type']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="teacher-event-field">
                                <label for="statut">Statut affiché</label>
                                <?php $selectedStatut = $old['statut'] ?? 'planifie'; ?>
                                <select class="teacher-event-input" id="statut" name="statut">
                                    <option value="planifie" <?= $selectedStatut === 'planifie' ? 'selected' : '' ?>>Planifié</option>
                                    <option value="en_cours" <?= $selectedStatut === 'en_cours' ? 'selected' : '' ?>>En cours</option>
                                    <option value="termine" <?= $selectedStatut === 'termine' ? 'selected' : '' ?>>Terminé</option>
                                    <option value="annule" <?= $selectedStatut === 'annule' ? 'selected' : '' ?>>Annulé</option>
                                </select>
                                <?php if (isset($errors['statut'])): ?>
                                    <div class="teacher-event-field-error"><?= htmlspecialchars($errors['statut']) ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="teacher-event-actions">
                                <button type="submit" name="action" value="save" class="btn-secondary-lite">Enregistrer la proposition</button>
                                <button type="submit" name="action" value="save_and_resources" class="btn-primary-lite">Enregistrer et ajouter des ressources</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var mapEl = document.getElementById('teacherAddEventMap');
        if (!mapEl || typeof L === 'undefined') return;

        var map = L.map('teacherAddEventMap', {
            center: [36.8065, 10.1815],
            zoom: 12,
            zoomControl: false
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            maxZoom: 19
        }).addTo(map);

        L.control.zoom({ position: 'topright' }).addTo(map);

        var marker;
        var lieuInput = document.getElementById('lieu');

        function setMarker(lat, lon) {
            if (marker) map.removeLayer(marker);
            marker = L.marker([lat, lon]).addTo(map);
        }

        map.on('click', function (e) {
            setMarker(e.latlng.lat, e.latlng.lng);
            fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + encodeURIComponent(e.latlng.lat) + '&lon=' + encodeURIComponent(e.latlng.lng))
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data && data.display_name && lieuInput) {
                        lieuInput.value = data.display_name.split(',')[0];
                    }
                })
                .catch(function () {});
        });

        if (lieuInput) {
            lieuInput.addEventListener('change', function () {
                var query = (lieuInput.value || '').trim();
                if (query.length < 3) return;

                fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(query))
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        if (!data || !data.length) return;
                        var lat = parseFloat(data[0].lat);
                        var lon = parseFloat(data[0].lon);
                        if (Number.isNaN(lat) || Number.isNaN(lon)) return;
                        map.setView([lat, lon], 15);
                        setMarker(lat, lon);
                    })
                    .catch(function () {});
            });
        }
    });
</script>
