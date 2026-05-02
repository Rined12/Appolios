<?php
/**
 * APPOLIOS - Admin Statistics
 */
?>

<div class="dashboard">
    <div class="container admin-dashboard-container" style="max-width: 1400px; width: 100%;">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>

            <div class="admin-main" style="background: transparent; padding: 1rem 0 2rem 0;">
                <div class="dashboard-header">
                    <h1>Statistiques des Bannissements</h1>
                    <p>Analyse visuelle des sanctions appliquées aux étudiants</p>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 2rem;">
                    <!-- Chart Section -->
                    <div style="background: white; border-radius: 20px; padding: 2rem; box-shadow: 0 10px 30px rgba(43, 72, 101, 0.08); display: flex; flex-direction: column; align-items: center; justify-content: center;">
                        <h3 style="color: #2B4865; margin-bottom: 2rem; font-family: 'Poppins', sans-serif;">Répartition par catégorie</h3>
                        <div style="width: 350px; height: 350px;">
                            <canvas id="banChart"></canvas>
                        </div>
                    </div>

                    <!-- Summary Section -->
                    <div style="background: white; border-radius: 20px; padding: 2rem; box-shadow: 0 10px 30px rgba(43, 72, 101, 0.08);">
                        <h3 style="color: #2B4865; margin-bottom: 1.5rem; font-family: 'Poppins', sans-serif;">Détails des sanctions</h3>
                        
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; border-radius: 12px; background: #fff5f7; border-left: 5px solid #ff809b;">
                                <span style="font-weight: 600; color: #ff809b;">Permanent</span>
                                <span style="font-size: 1.5rem; font-weight: 800; color: #2B4865;"><?= $stats['ban_perm'] ?? 0 ?></span>
                            </div>
                            
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; border-radius: 12px; background: #fffcf0; border-left: 5px solid #ffe080;">
                                <span style="font-weight: 600; color: #d4ac0d;">10 Heures</span>
                                <span style="font-size: 1.5rem; font-weight: 800; color: #2B4865;"><?= $stats['ban_10h'] ?? 0 ?></span>
                            </div>

                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; border-radius: 12px; background: #f0f9ff; border-left: 5px solid #80caff;">
                                <span style="font-weight: 600; color: #3498db;">1 Jour</span>
                                <span style="font-size: 1.5rem; font-weight: 800; color: #2B4865;"><?= $stats['ban_1d'] ?? 0 ?></span>
                            </div>

                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; border-radius: 12px; background: #f0fff4; border-left: 5px solid #80ffd4;">
                                <span style="font-weight: 600; color: #2ecc71;">2 Heures</span>
                                <span style="font-size: 1.5rem; font-weight: 800; color: #2B4865;"><?= $stats['ban_2h'] ?? 0 ?></span>
                            </div>
                        </div>

                        <div style="margin-top: 2rem; padding: 1rem; background: #f8fafc; border-radius: 12px; border: 1px dashed #cbd5e1;">
                            <p style="margin: 0; color: #64748b; font-size: 0.9rem; line-height: 1.5;">
                                <i class="fas fa-info-circle" style="color: #2B4865; margin-right: 8px;"></i>
                                Ces données sont extraites des journaux d'activité (Activity Logs) et reflètent l'historique complet des bannissements effectués sur la plateforme.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- User Distribution Chart -->
                <div style="margin-top: 2rem; background: white; border-radius: 20px; padding: 2rem; box-shadow: 0 10px 30px rgba(43, 72, 101, 0.08);">
                    <h3 style="color: #2B4865; margin-bottom: 2rem; font-family: 'Poppins', sans-serif; text-align: center;">Répartition actuelle (Étudiants vs Enseignants)</h3>
                    <div style="height: 300px; width: 100%;">
                        <canvas id="userDistributionChart"></canvas>
                    </div>
                </div>

                <!-- NEW: 7-Day Forecast Chart -->
                <div style="margin-top: 2rem; background: #2B4865; border-radius: 20px; padding: 2rem; box-shadow: 0 10px 30px rgba(43, 72, 101, 0.15);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                        <div>
                            <h3 style="color: #ffffff; margin: 0; font-family: 'Poppins', sans-serif;">Prévisions de croissance (7 jours)</h3>
                            <p style="color: rgba(255,255,255,0.7); margin: 5px 0 0 0; font-size: 0.9rem;">Estimations basées sur les tendances d'activité hebdomadaires</p>
                        </div>
                        <div style="background: rgba(255,255,255,0.1); padding: 8px 15px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.1);">
                            <span style="color: #80ffd4; font-weight: 700;">● Étudiants</span>
                            <span style="color: #ffe080; font-weight: 700; margin-left: 15px;">● Enseignants</span>
                        </div>
                    </div>
                    <div style="height: 350px; width: 100%;">
                        <canvas id="forecastChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Load Chart.js from CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- DONUT CHART (Bans) ---
        const ctxBan = document.getElementById('banChart').getContext('2d');
        
        const banData = {
            labels: ['Permanent', '10 Heures', '1 Jour', '2 Heures'],
            datasets: [{
                data: [
                    <?= $stats['ban_perm'] ?? 0 ?>, 
                    <?= $stats['ban_10h'] ?? 0 ?>, 
                    <?= $stats['ban_1d'] ?? 0 ?>, 
                    <?= $stats['ban_2h'] ?? 0 ?>
                ],
                backgroundColor: ['#ff809b', '#ffe080', '#80caff', '#80ffd4'],
                borderWidth: 0,
                hoverOffset: 15
            }]
        };

        new Chart(ctxBan, {
            type: 'doughnut',
            data: banData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 20, usePointStyle: true, font: { size: 12, family: "'Inter', sans-serif", weight: '600' } } }
                }
            }
        });

        // --- BAR CHART (Current Distribution) ---
        const ctxUser = document.getElementById('userDistributionChart').getContext('2d');
        new Chart(ctxUser, {
            type: 'bar',
            data: {
                labels: ['Étudiants', 'Enseignants'],
                datasets: [{
                    label: 'Nombre total',
                    data: [<?= $totalStudents ?? 0 ?>, <?= $totalTeachers ?? 0 ?>],
                    backgroundColor: ['#80ffd4', '#ffe080'],
                    borderRadius: 12,
                    barThickness: 80
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { color: '#ff809b', font: { size: 16, weight: '700' } }, grid: { color: 'rgba(0,0,0,0.05)' } },
                    x: { ticks: { color: '#2B4865', font: { size: 15, weight: '700' } }, grid: { display: false } }
                }
            }
        });

        // --- FORECAST CHART (7 Days Predictions) ---
        const ctxForecast = document.getElementById('forecastChart').getContext('2d');
        const forecastData = {
            labels: [<?php foreach($forecast as $f) echo "'".$f['date']."',"; ?>],
            datasets: [
                {
                    label: 'Étudiants',
                    data: [<?php foreach($forecast as $f) echo $f['students'].","; ?>],
                    backgroundColor: '#80ffd4',
                    borderRadius: 5,
                    barPercentage: 0.8,
                    categoryPercentage: 0.6
                },
                {
                    label: 'Enseignants',
                    data: [<?php foreach($forecast as $f) echo $f['teachers'].","; ?>],
                    backgroundColor: '#ffe080',
                    borderRadius: 5,
                    barPercentage: 0.8,
                    categoryPercentage: 0.6
                }
            ]
        };

        new Chart(ctxForecast, {
            type: 'bar',
            data: forecastData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: '#1a2e41',
                        padding: 12
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { color: 'rgba(255,255,255,0.7)', font: { size: 12 } },
                        grid: { color: 'rgba(255,255,255,0.1)' }
                    },
                    x: {
                        ticks: { color: '#ffffff', font: { size: 13, weight: '600' } },
                        grid: { display: false }
                    }
                }
            }
        });
    });
</script>
