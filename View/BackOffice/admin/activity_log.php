 * APPOLIOS - Historique d'Activité (Neo Admin Pro)
 */
?>

<!-- Leaflet CSS for integrated Map -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<div style="margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1 style="font-size: 1.8rem; font-weight: 800; color: #1e293b; margin: 0 0 0.5rem 0;">Journal d'Activité</h1>
        <p style="color: #64748b; margin: 0;">Historique complet des actions effectuées sur la plateforme.</p>
    </div>
    <div style="display: flex; gap: 12px;">
        <button onclick="toggleFilters()" class="btn-admin" style="background: white; border: 1px solid #e2e8f0; color: #475569;">
            <i class="bi bi-filter"></i> Filtres
        </button>
        <a href="<?= APP_ENTRY ?>?url=admin/activity-log" class="btn-admin" style="background: #f1f5f9; color: #475569;">
            <i class="bi bi-arrow-clockwise"></i> Actualiser
        </a>
    </div>
</div>

<!-- Quick Stats Row -->
<div class="stats-grid-pro" style="margin-bottom: 2rem;">
    <div class="stat-card-pro">
        <div class="stat-icon-pro" style="background: #eef2ff; color: #4338ca;">
            <i class="bi bi-list-task"></i>
        </div>
        <div>
            <div class="stat-label">Total Logs</div>
            <div class="stat-value"><?= $stats['total'] ?? 0 ?></div>
        </div>
    </div>
    <div class="stat-card-pro">
        <div class="stat-icon-pro" style="background: #ecfdf5; color: #059669;">
            <i class="bi bi-box-arrow-in-right"></i>
        </div>
        <div>
            <div class="stat-label">Connexions</div>
            <div class="stat-value"><?= $stats['logins'] ?? 0 ?></div>
        </div>
    </div>
    <div class="stat-card-pro">
        <div class="stat-icon-pro" style="background: #fff7ed; color: #c2410c;">
            <i class="bi bi-person-plus-fill"></i>
        </div>
        <div>
            <div class="stat-label">Inscriptions</div>
            <div class="stat-value"><?= $stats['registers'] ?? 0 ?></div>
        </div>
    </div>
</div>

<!-- Filters Panel -->
<div id="filters-panel" class="admin-card" style="display: none; margin-bottom: 2rem;">
    <form method="GET" action="<?= APP_ENTRY ?>" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; align-items: flex-end;">
        <input type="hidden" name="url" value="admin/activity-log">
        
        <div>
            <label class="admin-label-pro">Type d'activité</label>
            <select name="activity_type" class="admin-select-pro">
                <option value="">Tous les types</option>
                <option value="login" <?= ($filters['activity_type'] ?? '') === 'login' ? 'selected' : '' ?>>Connexion</option>
                <option value="logout" <?= ($filters['activity_type'] ?? '') === 'logout' ? 'selected' : '' ?>>Déconnexion</option>
                <option value="register" <?= ($filters['activity_type'] ?? '') === 'register' ? 'selected' : '' ?>>Inscription</option>
                <option value="ban_user" <?= ($filters['activity_type'] ?? '') === 'ban_user' ? 'selected' : '' ?>>Bannissement</option>
            </select>
        </div>

        <div>
            <label class="admin-label-pro">Date de début</label>
            <input type="date" name="date_from" value="<?= htmlspecialchars($filters['date_from'] ?? '') ?>" class="admin-input-pro">
        </div>

        <div>
            <label class="admin-label-pro">Date de fin</label>
            <input type="date" name="date_to" value="<?= htmlspecialchars($filters['date_to'] ?? '') ?>" class="admin-input-pro">
        </div>

        <div style="display: flex; gap: 8px;">
            <button type="submit" class="btn-admin btn-admin-primary" style="flex: 1; justify-content: center;">Appliquer</button>
            <a href="<?= APP_ENTRY ?>?url=admin/activity-log" class="btn-admin" style="background: #f1f5f9; color: #475569;">Réinitialiser</a>
        </div>
    </form>
</div>

<div class="admin-card" style="padding: 0; overflow: hidden;">
    <div style="overflow-x: auto;">
        <table class="admin-table-pro">
            <thead>
                <tr>
                    <th>Utilisateur</th>
                    <th>Événement</th>
                    <th>Détails</th>
                    <th>Adresse IP</th>
                    <th>Localisation</th>
                    <th>Date & Heure</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($activities)): ?>
                    <?php foreach ($activities as $activity): ?>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <?php if ($activity['user_name']): ?>
                                        <div style="width: 32px; height: 32px; border-radius: 8px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; color: var(--admin-primary);">
                                            <?= strtoupper(substr($activity['user_name'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div style="font-weight: 700; color: #1e293b;"><?= htmlspecialchars($activity['user_name']) ?></div>
                                            <div class="admin-badge" style="padding: 0; background: none; color: #94a3b8; font-size: 0.7rem; font-weight: 500;">
                                                <?= ucfirst($activity['user_role'] ?? 'Guest') ?>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div style="width: 32px; height: 32px; border-radius: 8px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; color: #94a3b8;">
                                            <i class="bi bi-person"></i>
                                        </div>
                                        <span style="color: #94a3b8; font-style: italic; font-size: 0.9rem;">Visiteur</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <?php 
                                $typeColor = 'var(--admin-primary)';
                                if (str_contains($activity['activity_type'], 'login')) $typeColor = '#10b981';
                                if (str_contains($activity['activity_type'], 'logout')) $typeColor = '#ef4444';
                                if (str_contains($activity['activity_type'], 'ban')) $typeColor = '#f59e0b';
                                ?>
                                <span class="admin-badge" style="background: <?= $typeColor ?>15; color: <?= $typeColor ?>; border: 1px solid <?= $typeColor ?>30;">
                                    <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $activity['activity_type']))) ?>
                                </span>
                            </td>
                            <td style="font-size: 0.85rem; color: #64748b; max-width: 250px;"><?= $activity['activity_description'] ?></td>
                            <td><code style="font-size: 0.8rem; background: #f8fafc; padding: 4px 8px; border-radius: 6px; color: #475569; border: 1px solid #e2e8f0;"><?= htmlspecialchars($activity['ip_address']) ?></code></td>
                            <td>
                                <?php 
                                $locParts = explode('|', $activity['location'] ?? '');
                                $locText = $locParts[0] ?: 'Local / Unknown';
                                $countryCode = isset($locParts[1]) ? $locParts[1] : null;
                                ?>
                                <div onclick="openMapModal('<?= addslashes($locText) ?>')" 
                                   class="location-link"
                                   style="cursor: pointer; display: flex; align-items: center; gap: 8px;">
                                    <?php if ($countryCode): ?>
                                        <img src="https://flagcdn.com/w20/<?= $countryCode ?>.png" 
                                             style="width: 18px; border-radius: 3px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);" 
                                             alt="<?= $countryCode ?>">
                                    <?php else: ?>
                                        <i class="bi bi-geo-alt" style="color: #94a3b8;"></i>
                                    <?php endif; ?>
                                    <span class="loc-text"><?= htmlspecialchars($locText) ?></span>
                                </div>
                            </td>
                            <td>
                                <div style="font-weight: 700; color: #1e293b;"><?= date('d M, Y', strtotime($activity['created_at'])) ?></div>
                                <div style="font-size: 0.75rem; color: #94a3b8;"><?= date('H:i:s', strtotime($activity['created_at'])) ?></div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align:center; padding: 4rem; color: #94a3b8;">
                        <i class="bi bi-slash-circle" style="font-size: 2rem; display: block; margin-bottom: 1rem;"></i>
                        Aucune activité enregistrée.
                    </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <div style="padding: 1.5rem 2rem; border-top: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; background: #fcfdfe;">
            <div style="color: #64748b; font-size: 0.85rem; font-weight: 500;">
                Affichage de <strong><?= count($activities) ?></strong> sur <strong><?= $totalActivities ?></strong> logs
            </div>
            
            <div style="display: flex; gap: 8px;">
                <?php 
                $queryParams = $_GET; 
                unset($queryParams['url']); // Remove url as it's handled by APP_ENTRY
                ?>

                <!-- Previous -->
                <?php if ($page > 1): ?>
                    <?php $queryParams['page'] = $page - 1; ?>
                    <a href="<?= APP_ENTRY ?>?url=admin/activity-log&<?= http_build_query($queryParams) ?>" class="page-link-pro">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                <?php endif; ?>

                <!-- Pages -->
                <?php 
                $start = max(1, $page - 2);
                $end = min($totalPages, $page + 2);
                for ($i = $start; $i <= $end; $i++): 
                    $queryParams['page'] = $i;
                ?>
                    <a href="<?= APP_ENTRY ?>?url=admin/activity-log&<?= http_build_query($queryParams) ?>" 
                       class="page-link-pro <?= $i === $page ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <!-- Next -->
                <?php if ($page < $totalPages): ?>
                    <?php $queryParams['page'] = $page + 1; ?>
                    <a href="<?= APP_ENTRY ?>?url=admin/activity-log&<?= http_build_query($queryParams) ?>" class="page-link-pro">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.page-link-pro {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    background: white;
    border: 1px solid #e2e8f0;
    color: #475569;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 700;
    transition: all 0.2s;
}
.page-link-pro:hover {
    border-color: var(--admin-primary);
    color: var(--admin-primary);
    background: #f8fafc;
}
.page-link-pro.active {
    background: var(--admin-primary);
    color: white;
    border-color: var(--admin-primary);
    box-shadow: 0 4px 12px rgba(67, 56, 202, 0.2);
}
.location-link .loc-text {
    color: #475569;
    font-size: 0.85rem;
    transition: all 0.2s;
}
.location-link:hover .loc-text {
    color: var(--admin-primary);
    text-decoration: underline;
}
.location-link:hover img {
    transform: scale(1.1);
}
</style>


<script>
function toggleFilters() {
    const p = document.getElementById('filters-panel');
    p.style.display = p.style.display === 'none' ? 'block' : 'none';
}

function openMapModal(location) {
    if (location === 'Local / Unknown') return;
    
    Swal.fire({
        title: `<i class="bi bi-geo-alt-fill" style="color: #ef4444;"></i> ${location}`,
        html: `<div id="modal-map" style="width: 100%; height: 450px; border-radius: 12px; border: 1px solid #e2e8f0;"></div>`,
        width: '850px',
        showCloseButton: true,
        showConfirmButton: false,
        padding: '1.5rem',
        background: '#ffffff',
        backdrop: `rgba(15, 23, 42, 0.75)`,
        didOpen: () => {
            // Geocode the location text using Nominatim (Free)
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(location)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        const lat = data[0].lat;
                        const lon = data[0].lon;
                        
                        const map = L.map('modal-map').setView([lat, lon], 13);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; OpenStreetMap contributors'
                        }).addTo(map);
                        
                        L.marker([lat, lon]).addTo(map)
                            .bindPopup(`<b>${location}</b>`)
                            .openPopup();
                    } else {
                        document.getElementById('modal-map').innerHTML = '<div style="padding: 2rem; text-align: center; color: #64748b;">Impossible de charger la carte pour cette adresse.</div>';
                    }
                })
                .catch(() => {
                    document.getElementById('modal-map').innerHTML = '<div style="padding: 2rem; text-align: center; color: #64748b;">Erreur de connexion au service de carte.</div>';
                });
        }
    });
}
</script>

<style>
.admin-label-pro { display: block; font-size: 0.85rem; font-weight: 700; color: #475569; margin-bottom: 8px; }
.admin-input-pro, .admin-select-pro { width: 100%; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 0.9rem; background: white; outline: none; }
.admin-input-pro:focus, .admin-select-pro:focus { border-color: var(--admin-primary); }
</style>
