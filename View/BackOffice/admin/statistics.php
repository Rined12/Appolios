<?php
/**
 * APPOLIOS - Statistiques Pro (Neo Admin Pro)
 */
?>

<div style="margin-bottom: 2.5rem;">
    <h1 style="font-size: 1.8rem; font-weight: 800; color: #1e293b; margin: 0 0 0.5rem 0;">Statistiques & Analyses</h1>
    <p style="color: #64748b; margin: 0;">Aperçu détaillé des performances, de la croissance et des sanctions.</p>
</div>

<!-- Stats Overview Grid -->
<div class="stats-grid-pro" style="margin-bottom: 2rem;">
    <div class="stat-card-pro">
        <div class="stat-icon-pro" style="background: #f0fdf4; color: #15803d;">
            <i class="bi bi-person-check-fill"></i>
        </div>
        <div>
            <div class="stat-label">Taux d'Inscription</div>
            <div class="stat-value">+12%</div>
        </div>
    </div>
    <div class="stat-card-pro">
        <div class="stat-icon-pro" style="background: #fff1f2; color: #be123c;">
            <i class="bi bi-shield-lock-fill"></i>
        </div>
        <div>
            <div class="stat-label">Sanctions Actives</div>
            <div class="stat-value"><?= ($stats['ban_perm'] ?? 0) + ($stats['ban_10h'] ?? 0) + ($stats['ban_1d'] ?? 0) + ($stats['ban_2h'] ?? 0) ?></div>
        </div>
    </div>
    <div class="stat-card-pro">
        <div class="stat-icon-pro" style="background: #eef2ff; color: #4338ca;">
            <i class="bi bi-activity"></i>
        </div>
        <div>
            <div class="stat-label">Activité Système</div>
            <div class="stat-value">Optimale</div>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1.5fr; gap: 2rem; margin-bottom: 2rem;">
    
    <!-- Sanctions Distribution -->
    <div class="admin-card">
        <h2 class="admin-card-title">Répartition des Sanctions</h2>
        <div style="height: 300px; margin-top: 1rem;">
            <canvas id="banChart"></canvas>
        </div>
        <div style="margin-top: 1.5rem; display: flex; flex-direction: column; gap: 10px;">
             <div style="display:flex; justify-content:space-between; align-items:center; font-size:0.9rem;">
                <span style="color:#64748b;"><span style="display:inline-block; width:10px; height:10px; border-radius:50%; background:#ef4444; margin-right:8px;"></span>Permanent</span>
                <span style="font-weight:700; color:#1e293b;"><?= $stats['ban_perm'] ?? 0 ?></span>
             </div>
             <div style="display:flex; justify-content:space-between; align-items:center; font-size:0.9rem;">
                <span style="color:#64748b;"><span style="display:inline-block; width:10px; height:10px; border-radius:50%; background:#f59e0b; margin-right:8px;"></span>10 Heures</span>
                <span style="font-weight:700; color:#1e293b;"><?= $stats['ban_10h'] ?? 0 ?></span>
             </div>
             <div style="display:flex; justify-content:space-between; align-items:center; font-size:0.9rem;">
                <span style="color:#64748b;"><span style="display:inline-block; width:10px; height:10px; border-radius:50%; background:#3b82f6; margin-right:8px;"></span>1 Jour</span>
                <span style="font-weight:700; color:#1e293b;"><?= $stats['ban_1d'] ?? 0 ?></span>
             </div>
             <div style="display:flex; justify-content:space-between; align-items:center; font-size:0.9rem;">
                <span style="color:#64748b;"><span style="display:inline-block; width:10px; height:10px; border-radius:50%; background:#10b981; margin-right:8px;"></span>2 Heures</span>
                <span style="font-weight:700; color:#1e293b;"><?= $stats['ban_2h'] ?? 0 ?></span>
             </div>
        </div>
    </div>

    <!-- Forecast Growth -->
    <div class="admin-card" style="background: var(--admin-active-gradient); color: white; border: none;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h2 class="admin-card-title" style="color: white;">Prévisions de Croissance</h2>
                <p style="font-size: 0.85rem; opacity: 0.8;">Estimations algorithmiques pour les 7 prochains jours</p>
            </div>
            <div class="admin-badge" style="background: rgba(255,255,255,0.2); color: white;">AI Predicted</div>
        </div>
        <div style="height: 350px;">
            <canvas id="forecastChart"></canvas>
        </div>
    </div>

</div>

<!-- User Distribution -->
<div class="admin-card">
    <h2 class="admin-card-title" style="text-align: center; margin-bottom: 2rem;">Répartition Étudiants vs Enseignants</h2>
    <div style="height: 300px;">
        <canvas id="userDistributionChart"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Doughnut Chart (Bans)
    new Chart(document.getElementById('banChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Permanent', '10 Heures', '1 Jour', '2 Heures'],
            datasets: [{
                data: [<?= $stats['ban_perm'] ?? 0 ?>, <?= $stats['ban_10h'] ?? 0 ?>, <?= $stats['ban_1d'] ?? 0 ?>, <?= $stats['ban_2h'] ?? 0 ?>],
                backgroundColor: ['#ef4444', '#f59e0b', '#3b82f6', '#10b981'],
                borderWidth: 0,
                cutout: '75%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } }
        }
    });

    // 2. Bar Chart (User Distribution)
    new Chart(document.getElementById('userDistributionChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: ['Étudiants', 'Enseignants'],
            datasets: [{
                label: 'Total',
                data: [<?= $totalStudents ?? 0 ?>, <?= $totalTeachers ?? 0 ?>],
                backgroundColor: ['#e19864', '#1e293b'],
                borderRadius: 15,
                barThickness: 60
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                x: { grid: { display: false } }
            }
        }
    });

    // 3. Line Chart (Forecast) - Modifié en Line pour plus d'élégance sur fond dégradé
    new Chart(document.getElementById('forecastChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: [<?php foreach($forecast as $f) echo "'".$f['date']."',"; ?>],
            datasets: [
                {
                    label: 'Étudiants',
                    data: [<?php foreach($forecast as $f) echo $f['students'].","; ?>],
                    borderColor: '#ffffff',
                    backgroundColor: 'rgba(255,255,255,0.1)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#ffffff'
                },
                {
                    label: 'Enseignants',
                    data: [<?php foreach($forecast as $f) echo $f['teachers'].","; ?>],
                    borderColor: '#e19864',
                    backgroundColor: 'rgba(225,152,100,0.2)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#e19864'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { labels: { color: '#ffffff', font: { size: 12, weight: '600' } } },
                tooltip: { backgroundColor: '#1e293b', titleColor: '#ffffff', bodyColor: '#ffffff' }
            },
            scales: {
                y: { 
                    ticks: { color: 'rgba(255,255,255,0.7)' },
                    grid: { color: 'rgba(255,255,255,0.1)' }
                },
                x: { 
                    ticks: { color: 'rgba(255,255,255,0.7)' },
                    grid: { display: false }
                }
            }
        }
    });
});
</script>
