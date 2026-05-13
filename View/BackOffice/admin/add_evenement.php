<?php
/**
 * APPOLIOS - Add Evenement Page (Dark Theme with Map)
 */

$old = isset($_SESSION['old']) ? $_SESSION['old'] : [];
unset($_SESSION['old']);

$minDate = date('Y-m-d', strtotime('+1 day'));
?>



<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<div class="admin-main" style="background: #0f172a; min-height: 100vh; padding: 2rem;">
    <div style="max-width: 800px; margin: 0 auto;">
        
        <!-- Header -->
        <div style="margin-bottom: 2rem;">
            <h1 style="font-size: 2.5rem; font-weight: 800; color: #fff; margin: 0 0 0.5rem 0;">
                Ajouter <span style="color: #E19864;">Evenement</span>
            </h1>
            <p style="color: #94a3b8; font-size: 1rem; margin: 0;">
                Create a new event for your platform by filling out the details below.
            </p>
        </div>
        
        <!-- Form -->
        <form action="<?= APP_ENTRY ?>?url=event/store-evenement" method="POST" style="display: flex; flex-direction: column; gap: 1.5rem;">
            
            <!-- Titre -->
            <div>
                <label style="display: block; color: #cbd5e1; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">
                    Titre <span style="color: #E19864;">*</span>
                </label>
                <input type="text" name="title" placeholder="Enter event title" 
                    value="<?= isset($old['title']) ? htmlspecialchars($old['title']) : '' ?>"
                    style="width: 100%; background: #1e293b; border: 1px solid #334155; border-radius: 10px; padding: 14px 16px; font-size: 0.95rem; color: #f1f5f9; outline: none; transition: all 0.2s;"
                    onfocus="this.style.borderColor='#E19864'; this.style.background='#0f172a'" 
                    onblur="this.style.borderColor='#334155'; this.style.background='#1e293b'">
            </div>
            
            <!-- Description -->
            <div>
                <label style="display: block; color: #cbd5e1; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">
                    Description <span style="color: #E19864;">*</span>
                </label>
                <textarea name="description" placeholder="Describe the event" 
                    style="width: 100%; background: #1e293b; border: 1px solid #334155; border-radius: 10px; padding: 14px 16px; font-size: 0.95rem; color: #f1f5f9; outline: none; min-height: 120px; resize: vertical; transition: all 0.2s;"
                    onfocus="this.style.borderColor='#E19864'; this.style.background='#0f172a'" 
                    onblur="this.style.borderColor='#334155'; this.style.background='#1e293b'"><?= isset($old['description']) ? htmlspecialchars($old['description']) : '' ?></textarea>
            </div>
            
            <!-- Date Row -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; color: #cbd5e1; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">
                        Date Debut <span style="color: #E19864;">*</span>
                    </label>
                    <input type="date" name="date_debut" min="<?= $minDate ?>" 
                        value="<?= isset($old['date_debut']) ? htmlspecialchars($old['date_debut']) : '' ?>"
                        style="width: 100%; background: #1e293b; border: 1px solid #334155; border-radius: 10px; padding: 14px 16px; font-size: 0.95rem; color: #f1f5f9; outline: none; transition: all 0.2s;"
                        onfocus="this.style.borderColor='#E19864'; this.style.background='#0f172a'" 
                        onblur="this.style.borderColor='#334155'; this.style.background='#1e293b'">
                </div>
                <div>
                    <label style="display: block; color: #cbd5e1; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">
                        Date Fin
                    </label>
                    <input type="date" name="date_fin" 
                        value="<?= isset($old['date_fin']) ? htmlspecialchars($old['date_fin']) : '' ?>"
                        style="width: 100%; background: #1e293b; border: 1px solid #334155; border-radius: 10px; padding: 14px 16px; font-size: 0.95rem; color: #f1f5f9; outline: none; transition: all 0.2s;"
                        onfocus="this.style.borderColor='#E19864'; this.style.background='#0f172a'" 
                        onblur="this.style.borderColor='#334155'; this.style.background='#1e293b'">
                </div>
            </div>
            
            <!-- Time Row -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; color: #cbd5e1; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">
                        Heure Debut <span style="color: #E19864;">*</span>
                    </label>
                    <input type="time" name="heure_debut" 
                        value="<?= isset($old['heure_debut']) ? htmlspecialchars($old['heure_debut']) : '' ?>"
                        style="width: 100%; background: #1e293b; border: 1px solid #334155; border-radius: 10px; padding: 14px 16px; font-size: 0.95rem; color: #f1f5f9; outline: none; transition: all 0.2s;"
                        onfocus="this.style.borderColor='#E19864'; this.style.background='#0f172a'" 
                        onblur="this.style.borderColor='#334155'; this.style.background='#1e293b'">
                </div>
                <div>
                    <label style="display: block; color: #cbd5e1; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">
                        Heure Fin
                    </label>
                    <input type="time" name="heure_fin" 
                        value="<?= isset($old['heure_fin']) ? htmlspecialchars($old['heure_fin']) : '' ?>"
                        style="width: 100%; background: #1e293b; border: 1px solid #334155; border-radius: 10px; padding: 14px 16px; font-size: 0.95rem; color: #f1f5f9; outline: none; transition: all 0.2s;"
                        onfocus="this.style.borderColor='#E19864'; this.style.background='#0f172a'" 
                        onblur="this.style.borderColor='#334155'; this.style.background='#1e293b'">
                </div>
            </div>
            
            <!-- Location with Map -->
            <div>
                <label style="display: block; color: #cbd5e1; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">
                    Lieu <span style="color: #64748b; font-weight: 400;">(click on map or search)</span>
                </label>
                <div style="position: relative; margin-bottom: 0.75rem;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%);">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                    <input type="text" id="lieu" name="lieu" placeholder="Search for a place..." 
                        value="<?= isset($old['lieu']) ? htmlspecialchars($old['lieu']) : '' ?>"
                        style="width: 100%; background: #1e293b; border: 1px solid #334155; border-radius: 10px; padding: 14px 16px 14px 44px; font-size: 0.95rem; color: #f1f5f9; outline: none; transition: all 0.2s;"
                        onfocus="this.style.borderColor='#E19864'; this.style.background='#0f172a'" 
                        onblur="this.style.borderColor='#334155'; this.style.background='#1e293b'">
                </div>
                <!-- Map -->
                <div id="map" style="width: 100%; height: 200px; border-radius: 12px; overflow: hidden; border: 1px solid #334155;"></div>
                <input type="hidden" id="latitude" name="latitude">
                <input type="hidden" id="longitude" name="longitude">
            </div>
            
            <!-- Status Message -->
            <div style="background: #064e3b; border: 1px solid #059669; border-radius: 10px; padding: 1rem; display: flex; align-items: center; gap: 0.75rem;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
                <span style="color: #6ee7b7; font-size: 0.9rem;">You can add resources later</span>
            </div>
            
            <!-- Capacity & Type Row -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; color: #cbd5e1; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">
                        Capacite Max
                    </label>
                    <input type="number" name="capacite_max" min="0" placeholder="Ex: 200" 
                        value="<?= isset($old['capacite_max']) ? htmlspecialchars($old['capacite_max']) : '' ?>"
                        style="width: 100%; background: #1e293b; border: 1px solid #334155; border-radius: 10px; padding: 14px 16px; font-size: 0.95rem; color: #f1f5f9; outline: none; transition: all 0.2s;"
                        onfocus="this.style.borderColor='#E19864'; this.style.background='#0f172a'" 
                        onblur="this.style.borderColor='#334155'; this.style.background='#1e293b'">
                </div>
                <div>
                    <label style="display: block; color: #cbd5e1; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">
                        Type
                    </label>
                    <input type="text" name="type" placeholder="conference, workshop..." 
                        value="<?= isset($old['type']) ? htmlspecialchars($old['type']) : '' ?>"
                        style="width: 100%; background: #1e293b; border: 1px solid #334155; border-radius: 10px; padding: 14px 16px; font-size: 0.95rem; color: #f1f5f9; outline: none; transition: all 0.2s;"
                        onfocus="this.style.borderColor='#E19864'; this.style.background='#0f172a'" 
                        onblur="this.style.borderColor='#334155'; this.style.background='#1e293b'">
                </div>
            </div>
            
            <!-- Statut -->
            <div>
                <label style="display: block; color: #cbd5e1; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">
                    Statut
                </label>
                <select name="statut" 
                    style="width: 100%; background: #1e293b; border: 1px solid #334155; border-radius: 10px; padding: 14px 16px; font-size: 0.95rem; color: #f1f5f9; outline: none; transition: all 0.2s;"
                    onfocus="this.style.borderColor='#E19864'; this.style.background='#0f172a'" 
                    onblur="this.style.borderColor='#334155'; this.style.background='#1e293b'">
                    <?php $selectedStatut = isset($old['statut']) ? $old['statut'] : 'planifie'; ?>
                    <option value="planifie" <?= $selectedStatut === 'planifie' ? 'selected' : '' ?>>Planifie</option>
                    <option value="en_cours" <?= $selectedStatut === 'en_cours' ? 'selected' : '' ?>>En cours</option>
                    <option value="termine" <?= $selectedStatut === 'termine' ? 'selected' : '' ?>>Termine</option>
                    <option value="annule" <?= $selectedStatut === 'annule' ? 'selected' : '' ?>>Annule</option>
                </select>
            </div>
            
            <!-- Buttons -->
            <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                <a href="<?= APP_ENTRY ?>?url=event/evenements" 
                    style="flex: 1; background: #1e293b; color: #94a3b8; border: 1px solid #334155; padding: 14px 28px; border-radius: 10px; font-weight: 600; font-size: 1rem; cursor: pointer; transition: all 0.2s; text-decoration: none; text-align: center;"
                    onmouseover="this.style.background='#334155'; this.style.color='#f1f5f9'" 
                    onmouseout="this.style.background='#1e293b'; this.style.color='#94a3b8'">
                    Cancel
                </a>
                <button type="submit" name="action" value="save" 
                    style="flex: 2; background: #E19864; color: #fff; border: none; padding: 14px 28px; border-radius: 10px; font-weight: 600; font-size: 1rem; cursor: pointer; transition: all 0.2s;"
                    onmouseover="this.style.background='#d4864e'; this.style.transform='translateY(-1px)'" 
                    onmouseout="this.style.background='#E19864'; this.style.transform='translateY(0)'">
                    Create Evenement
                </button>
            </div>
            
        </form>
    </div>
</div>

<script>
    // Initialize map
    var map = L.map('map', {
        center: [36.8065, 10.1815], // Tunis coordinates
        zoom: 13,
        zoomControl: false
    });
    
    // Add dark theme tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19
    }).addTo(map);
    
    // Add zoom control to top right
    L.control.zoom({
        position: 'topright'
    }).addTo(map);
    
    var marker;
    
    // Click on map to set location
    map.on('click', function(e) {
        if (marker) {
            map.removeLayer(marker);
        }
        marker = L.marker(e.latlng).addTo(map);
        document.getElementById('latitude').value = e.latlng.lat;
        document.getElementById('longitude').value = e.latlng.lng;
        
        // Reverse geocoding - get address from coordinates
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${e.latlng.lat}&lon=${e.latlng.lng}`)
            .then(response => response.json())
            .then(data => {
                if (data.display_name) {
                    document.getElementById('lieu').value = data.display_name.split(',')[0];
                }
            })
            .catch(() => {});
    });
    
    // Search functionality
    document.getElementById('lieu').addEventListener('change', function() {
        var query = this.value;
        if (query.length > 2) {
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.length > 0) {
                        var lat = parseFloat(data[0].lat);
                        var lon = parseFloat(data[0].lon);
                        map.setView([lat, lon], 15);
                        
                        if (marker) {
                            map.removeLayer(marker);
                        }
                        marker = L.marker([lat, lon]).addTo(map);
                        document.getElementById('latitude').value = lat;
                        document.getElementById('longitude').value = lon;
                    }
                })
                .catch(() => {});
        }
    });
</script>
