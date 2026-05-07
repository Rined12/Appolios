<?php
/**
 * APPOLIOS - Carte Interactive d'Activité
 */
?>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #activity-map {
        height: 600px;
        width: 100%;
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        border: 1px solid #e2e8f0;
        z-index: 1;
    }
    .map-popup .popup-title {
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 4px;
        display: block;
    }
    .map-popup .popup-desc {
        font-size: 0.8rem;
        color: #64748b;
    }
    .map-popup .popup-time {
        font-size: 0.7rem;
        color: #94a3b8;
        margin-top: 6px;
        display: block;
    }
</style>

<div style="margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1 style="font-size: 1.8rem; font-weight: 800; color: #1e293b; margin: 0 0 0.5rem 0;">Carte d'Activité Mondiale</h1>
        <p style="color: #64748b; margin: 0;">Visualisation en temps réel de la provenance de vos utilisateurs.</p>
    </div>
    <div style="display: flex; gap: 12px;">
        <a href="<?= APP_ENTRY ?>?url=admin/activity-log" class="btn-admin" style="background: white; border: 1px solid #e2e8f0; color: #475569;">
            <i class="bi bi-list-task"></i> Voir la Liste
        </a>
    </div>
</div>

<div class="admin-card" style="padding: 1.5rem;">
    <div id="activity-map"></div>
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize the map
        const map = L.map('activity-map').setView([20, 0], 2);

        // Add Light style tiles (OpenStreetMap)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Markers Data
        const activities = [
            <?php foreach ($activities as $activity): 
                $locParts = explode('|', $activity['location'] ?? '');
                if (count($locParts) >= 4):
                    $lat = $locParts[2];
                    $lon = $locParts[3];
                    if (!empty($lat) && !empty($lon)):
            ?>
            {
                lat: <?= $lat ?>,
                lon: <?= $lon ?>,
                user: "<?= htmlspecialchars($activity['user_name'] ?? 'Visiteur') ?>",
                type: "<?= htmlspecialchars($activity['activity_type']) ?>",
                desc: "<?= htmlspecialchars($activity['activity_description']) ?>",
                time: "<?= date('d M, H:i', strtotime($activity['created_at'])) ?>",
                color: "<?= str_contains($activity['activity_type'], 'login') ? '#10b981' : (str_contains($activity['activity_type'], 'logout') ? '#ef4444' : '#4338ca') ?>"
            },
            <?php 
                    endif;
                endif;
            endforeach; ?>
        ];

        // Add markers to map
        activities.forEach(function(act) {
            const circle = L.circleMarker([act.lat, act.lon], {
                color: act.color,
                fillColor: act.color,
                fillOpacity: 0.5,
                radius: 8
            }).addTo(map);

            circle.bindPopup(`
                <div class="map-popup">
                    <span class="popup-title">${act.user} - ${act.type}</span>
                    <div class="popup-desc">${act.desc}</div>
                    <span class="popup-time"><i class="bi bi-clock"></i> ${act.time}</span>
                </div>
            `);
        });

        // If there are markers, fit the map to show them all
        if (activities.length > 0) {
            const group = new L.featureGroup(activities.map(a => L.marker([a.lat, a.lon])));
            map.fitBounds(group.getBounds().pad(0.1));
        }
    });
</script>
