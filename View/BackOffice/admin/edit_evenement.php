<?php
/**
 * APPOLIOS - Edit Evenement Page (neo theme)
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

<section class="neo-auth-wrap" style="background: transparent; font-family: 'Inter', sans-serif;">
                    
                    <div class="neo-glass-card" style="width: 100%; background: #0f172a; border-radius: 20px; box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2); border: 1px solid rgba(255, 255, 255, 0.1); overflow: hidden; display: flex; flex-direction: column; color: #ffffff;">
                        
                        <!-- Header Area -->
                        <div style="padding: 3.5rem; background: rgba(30, 41, 59, 0.3); position: relative; overflow: hidden; border-bottom: 1px solid rgba(255, 255, 255, 0.05);">
                            <div style="position: absolute; top: -50px; left: -50px; width: 200px; height: 200px; background: #e9f1fa; border-radius: 50%; z-index: 0; opacity: 0.7;"></div>
                            <div style="position: absolute; bottom: -50px; right: 10%; width: 300px; height: 300px; background: #fef2f2; border-radius: 50%; z-index: 0; opacity: 0.4;"></div>
                            
                            <div style="position: relative; z-index: 2;">
                                <a href="<?= APP_ENTRY ?>?url=event/evenements" style="display: inline-flex; align-items: center; gap: 8px; font-size: 1.15rem; color: #548CA8; font-weight: 700; text-decoration: none; margin-bottom: 2rem; transition: color 0.2s;" onmouseover="this.style.color='#355C7D'" onmouseout="this.style.color='#548CA8'">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                                    Back to Evenements
                                </a>

                                <h2 style="font-size: 2.8rem; font-weight: 800; color: #ffffff; line-height: 1.15; margin: 0 0 0.5rem 0; letter-spacing: -0.02em;">
                                    Modifier <span style="color: #E19864;">Evenement</span>
                                </h2>
                                <p style="color: #94a3b8; font-size: 1.1rem; line-height: 1.6; margin: 0; max-width: 90%;">
                                    Update the selected event details below.
                                </p>
                            </div>
                        </div>

                        <!-- Content Area: Form -->
                        <div style="padding: 3rem; background: transparent;">
                            <form action="<?= APP_ENTRY ?>?url=event/update-evenement/<?= (int) ($evenement['id'] ?? 0) ?>" method="POST" class="neo-form-grid" novalidate>
                                
                                <div class="neo-form-group col-span-2">
                                    <label for="title">Titre *</label>
                                    <input type="text" id="title" name="title" placeholder="Enter event title" value="<?= htmlspecialchars($form['title']) ?>" class="neo-input <?= isset($errors['title']) ? 'neo-error-input' : '' ?>">
                                    <?php if (isset($errors['title'])): ?>
                                        <div class="neo-error-text"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg><?= htmlspecialchars($errors['title']) ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="neo-form-group col-span-2">
                                    <label for="description">Description *</label>
                                    <textarea id="description" name="description" placeholder="Describe the event" class="neo-input <?= isset($errors['description']) ? 'neo-error-input' : '' ?>" style="min-height: 120px; resize: vertical;"><?= htmlspecialchars($form['description']) ?></textarea>
                                    <?php if (isset($errors['description'])): ?>
                                        <div class="neo-error-text"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg><?= htmlspecialchars($errors['description']) ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="neo-form-group">
                                    <label for="date_debut">Date Debut *</label>
                                    <input type="date" id="date_debut" name="date_debut" min="<?= $minDate ?>" value="<?= htmlspecialchars($form['date_debut']) ?>" class="neo-input <?= isset($errors['date_debut']) ? 'neo-error-input' : '' ?>">
                                    <?php if (isset($errors['date_debut'])): ?>
                                        <div class="neo-error-text"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg><?= htmlspecialchars($errors['date_debut']) ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="neo-form-group">
                                    <label for="date_fin">Date Fin</label>
                                    <input type="date" id="date_fin" name="date_fin" value="<?= htmlspecialchars($form['date_fin']) ?>" class="neo-input <?= isset($errors['date_fin']) ? 'neo-error-input' : '' ?>">
                                    <?php if (isset($errors['date_fin'])): ?>
                                        <div class="neo-error-text"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg><?= htmlspecialchars($errors['date_fin']) ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="neo-form-group">
                                    <label for="heure_debut">Heure Debut *</label>
                                    <input type="time" id="heure_debut" name="heure_debut" value="<?= htmlspecialchars($form['heure_debut']) ?>" class="neo-input <?= isset($errors['heure_debut']) ? 'neo-error-input' : '' ?>">
                                    <?php if (isset($errors['heure_debut'])): ?>
                                        <div class="neo-error-text"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg><?= htmlspecialchars($errors['heure_debut']) ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="neo-form-group">
                                    <label for="heure_fin">Heure Fin</label>
                                    <input type="time" id="heure_fin" name="heure_fin" value="<?= htmlspecialchars($form['heure_fin']) ?>" class="neo-input <?= isset($errors['heure_fin']) ? 'neo-error-input' : '' ?>">
                                    <?php if (isset($errors['heure_fin'])): ?>
                                        <div class="neo-error-text"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg><?= htmlspecialchars($errors['heure_fin']) ?></div>
                                    <?php endif; ?>
                                </div>

                                <!-- Location Picker - spans full width -->
                                <div class="neo-form-group col-span-2" id="location-picker-group">
                                    <label>Lieu <span style="color:#94a3b8; font-weight:500; font-size:0.85rem;">(click on map or search)</span></label>

                                    <!-- Search bar -->
                                    <div style="position: relative; margin-bottom: 10px;">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position:absolute; left:14px; top:14px; z-index:1; pointer-events:none;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                        <input
                                            type="text"
                                            id="location-search"
                                            placeholder="Search for a place..."
                                            autocomplete="off"
                                            class="neo-input"
                                            style="padding-left: 44px;"
                                        >
                                        <div id="nominatim-results"></div>
                                    </div>

                                    <!-- Map container -->
                                    <div id="event-map" style="width:100%; height:320px; border-radius:12px; border:1.5px solid rgba(255, 255, 255, 0.1); overflow:hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.2);"></div>

                                    <!-- Selected address display -->
                                    <div id="selected-address-box" style="display:none; margin-top:10px; background:rgba(22, 163, 74, 0.1); border:1px solid rgba(22, 163, 74, 0.2); border-radius:10px; padding:12px 16px; display:flex; align-items:center; gap:10px;">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                        <span id="selected-address-text" style="color:#15803d; font-weight:600; font-size:0.95rem;"></span>
                                    </div>

                                    <!-- Hidden inputs submitted with form -->
                                    <input type="hidden" id="lieu" name="lieu" value="<?= htmlspecialchars($form['lieu']) ?>">
                                    <input type="hidden" id="map_lat" name="map_lat" value="<?= htmlspecialchars($form['map_lat'] ?? '') ?>">
                                    <input type="hidden" id="map_lng" name="map_lng" value="<?= htmlspecialchars($form['map_lng'] ?? '') ?>">

                                    <?php if (isset($errors['lieu'])): ?>
                                        <div class="neo-error-text"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg><?= htmlspecialchars($errors['lieu']) ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="neo-form-group">
                                    <label for="capacite_max">Capacite Max</label>
                                    <input type="number" id="capacite_max" name="capacite_max" min="0" placeholder="Ex: 200" value="<?= htmlspecialchars((string) $form['capacite_max']) ?>" class="neo-input <?= isset($errors['capacite_max']) ? 'neo-error-input' : '' ?>">
                                    <?php if (isset($errors['capacite_max'])): ?>
                                        <div class="neo-error-text"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg><?= htmlspecialchars($errors['capacite_max']) ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="neo-form-group">
                                    <label for="type">Type</label>
                                    <input type="text" id="type" name="type" placeholder="conference, workshop..." value="<?= htmlspecialchars($form['type']) ?>" class="neo-input <?= isset($errors['type']) ? 'neo-error-input' : '' ?>">
                                    <?php if (isset($errors['type'])): ?>
                                        <div class="neo-error-text"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg><?= htmlspecialchars($errors['type']) ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="neo-form-group">
                                    <label for="statut">Statut</label>
                                    <select id="statut" name="statut" class="neo-input <?= isset($errors['statut']) ? 'neo-error-input' : '' ?>">
                                        <option value="planifie" <?= $form['statut'] === 'planifie' ? 'selected' : '' ?>>Planifie</option>
                                        <option value="en_cours" <?= $form['statut'] === 'en_cours' ? 'selected' : '' ?>>En cours</option>
                                        <option value="termine" <?= $form['statut'] === 'termine' ? 'selected' : '' ?>>Termine</option>
                                        <option value="annule" <?= $form['statut'] === 'annule' ? 'selected' : '' ?>>Annule</option>
                                    </select>
                                    <?php if (isset($errors['statut'])): ?>
                                        <div class="neo-error-text"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg><?= htmlspecialchars($errors['statut']) ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="col-span-2" style="margin-top: 1.5rem;">
                                    <button type="submit" class="neo-submit-btn">Sauvegarder</button>
                                </div>
                            </form>
                        </div>

                    </div>
                </section>
<style>
    .neo-form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }
    .col-span-2 {
        grid-column: span 2;
    }
    .neo-form-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    .neo-form-group label {
        color: #94a3b8;
        font-size: 0.95rem;
        font-weight: 700;
        margin: 0;
    }
    .neo-input {
        width: 100%;
        background: rgba(30, 41, 59, 0.5);
        border: 1.5px solid rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        padding: 12px 16px;
        font-size: 1rem;
        color: #ffffff;
        outline: none;
        transition: all 0.2s ease;
        font-family: inherit;
    }
    .neo-input:focus {
        background: rgba(30, 41, 59, 0.8);
        border-color: #E19864;
        box-shadow: 0 0 0 4px rgba(225, 152, 100, 0.1);
    }
    .neo-error-input {
        border-color: #ef4444 !important;
        background: #fef2f2 !important;
    }
    .neo-error-input:focus {
        box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.15) !important;
    }
    .neo-error-text {
        color: #ef4444;
        font-size: 0.85rem;
        font-weight: 600;
        margin-top: 4px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .neo-error-text svg {
        width: 14px;
        height: 14px;
        stroke-width: 2.5;
    }
    .neo-submit-btn {
        background: #E19864;
        color: #fff;
        border: none;
        width: 100%;
        padding: 16px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 1.1rem;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 4px 15px rgba(225, 152, 100, 0.2);
    }
    .neo-submit-btn:hover {
        box-shadow: 0 8px 25px rgba(225, 152, 100, 0.3);
        transform: translateY(-2px);
    }
    
    @media (max-width: 768px) {
        .neo-form-grid {
            grid-template-columns: 1fr;
        }
        .col-span-2 {
            grid-column: span 1;
        }
    }
</style>

<!-- ===================== Leaflet + OpenStreetMap Location Picker ===================== -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<style>
    /* Autocomplete dropdown */
    #nominatim-results {
        position: absolute;
        top: 100%;
        left: 0; right: 0;
        background: #1e293b;
        border: 1.5px solid rgba(255, 255, 255, 0.1);
        border-top: none;
        border-radius: 0 0 10px 10px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.3);
        z-index: 9999;
        max-height: 220px;
        overflow-y: auto;
        display: none;
    }
    #nominatim-results .nomi-item {
        padding: 10px 14px;
        cursor: pointer;
        font-size: 0.9rem;
        color: #cbd5e1;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        display: flex;
        align-items: center;
        gap: 8px;
        transition: background 0.15s;
    }
    #nominatim-results .nomi-item:last-child { border-bottom: none; }
    #nominatim-results .nomi-item:hover { background: rgba(255, 255, 255, 0.05); color: #ffffff; }
    /* Override Leaflet popup to fit theme */
    .leaflet-popup-content-wrapper {
        border-radius: 10px !important;
        box-shadow: 0 8px 20px rgba(0,0,0,0.12) !important;
        font-family: 'Inter', sans-serif !important;
    }
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    /* ---- Map init ---- */
    var defaultLat = 36.8190, defaultLng = 10.1658, defaultZoom = 12;
    var existingLieu = document.getElementById('lieu').value;

    var map = L.map('event-map', { zoomControl: true }).setView([defaultLat, defaultLng], defaultZoom);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19
    }).addTo(map);

    /* ---- Custom orange marker ---- */
    var orangeIcon = L.divIcon({
        className: '',
        html: '<div style="width:22px;height:22px;background:#E19864;border:3px solid #fff;border-radius:50%;box-shadow:0 2px 8px rgba(225,152,100,0.5);"></div>',
        iconSize: [22, 22],
        iconAnchor: [11, 11]
    });

    var marker = null;

    function placeMarker(lat, lng, address) {
        if (marker) map.removeLayer(marker);
        marker = L.marker([lat, lng], { icon: orangeIcon }).addTo(map);
        document.getElementById('lieu').value    = address;
        document.getElementById('map_lat').value = lat;
        document.getElementById('map_lng').value = lng;
        showAddressBox(address);
    }

    function showAddressBox(address) {
        var box  = document.getElementById('selected-address-box');
        var text = document.getElementById('selected-address-text');
        box.style.display = 'flex';
        text.textContent  = address;
    }

    /* ---- Click on map -> reverse geocode (Nominatim) ---- */
    map.on('click', function(e) {
        var lat = e.latlng.lat, lng = e.latlng.lng;
        fetch('https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=' + lat + '&lon=' + lng, {
            headers: { 'Accept-Language': 'fr' }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            var addr = data.display_name || (lat.toFixed(5) + ', ' + lng.toFixed(5));
            placeMarker(lat, lng, addr);
        })
        .catch(function() {
            placeMarker(lat, lng, lat.toFixed(5) + ', ' + lng.toFixed(5));
        });
    });

    /* ---- Search autocomplete (Nominatim) ---- */
    var searchInput   = document.getElementById('location-search');
    var resultsBox    = document.getElementById('nominatim-results');
    var searchTimeout = null;

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        var q = this.value.trim();
        if (q.length < 3) { resultsBox.style.display = 'none'; return; }

        searchTimeout = setTimeout(function() {
            fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(q) + '&limit=5&addressdetails=1', {
                headers: { 'Accept-Language': 'fr' }
            })
            .then(function(r) { return r.json(); })
            .then(function(items) {
                resultsBox.innerHTML = '';
                if (!items.length) { resultsBox.style.display = 'none'; return; }
                items.forEach(function(item) {
                    var div = document.createElement('div');
                    div.className = 'nomi-item';
                    div.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#E19864" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>' +
                        '<span>' + item.display_name + '</span>';
                    div.addEventListener('click', function() {
                        var lat = parseFloat(item.lat), lng = parseFloat(item.lon);
                        map.setView([lat, lng], 16);
                        placeMarker(lat, lng, item.display_name);
                        searchInput.value = '';
                        resultsBox.style.display = 'none';
                    });
                    resultsBox.appendChild(div);
                });
                resultsBox.style.display = 'block';
            })
            .catch(function() { resultsBox.style.display = 'none'; });
        }, 400); // debounce 400ms
    });

    /* Close dropdown on outside click */
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !resultsBox.contains(e.target)) {
            resultsBox.style.display = 'none';
        }
    });

    /* ---- Pre-load existing lieu (for add with old value) ---- */
    if (existingLieu && existingLieu.trim() !== '') {
        showAddressBox(existingLieu);
        
        // If we have map_lat and map_lng already, we can use them directly
        var existingLat = document.getElementById('map_lat').value;
        var existingLng = document.getElementById('map_lng').value;
        
        if (existingLat && existingLng) {
            var lat = parseFloat(existingLat), lng = parseFloat(existingLng);
            map.setView([lat, lng], 15);
            placeMarker(lat, lng, existingLieu);
        } else {
            // Otherwise search by name
            fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(existingLieu) + '&limit=1')
            .then(function(r) { return r.json(); })
            .then(function(items) {
                if (items && items[0]) {
                    var lat = parseFloat(items[0].lat), lng = parseFloat(items[0].lon);
                    map.setView([lat, lng], 15);
                    placeMarker(lat, lng, existingLieu);
                }
            });
        }
    }
});
</script>
