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

                <!-- New Chart Section: User Distribution -->
                <div style="margin-top: 2rem; background: white; border-radius: 20px; padding: 2rem; box-shadow: 0 10px 30px rgba(43, 72, 101, 0.08);">
                    <h3 style="color: #2B4865; margin-bottom: 2rem; font-family: 'Poppins', sans-serif; text-align: center;">Répartition des Utilisateurs (Étudiants vs Enseignants)</h3>
                    <div style="height: 300px; width: 100%;">
                        <canvas id="userDistributionChart"></canvas>
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

        // --- BAR CHART (Users) ---
        const ctxUser = document.getElementById('userDistributionChart').getContext('2d');
        
        new Chart(ctxUser, {
            type: 'bar',
            data: {
                labels: ['Étudiants', 'Enseignants'],
                datasets: [{
                    label: 'Nombre total',
                    data: [<?= $totalStudents ?? 0 ?>, <?= $totalTeachers ?? 0 ?>],
                    backgroundColor: [
                        '#80ffd4', // Sarcelle pour étudiants
                        '#ffe080'  // Jaune pour enseignants
                    ],
                    borderRadius: 12,
                    barThickness: 80,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#2B4865',
                        padding: 12,
                        cornerRadius: 8
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#ff809b', // ROSE pour l'axe vertical comme demandé
                            font: { size: 16, weight: '700', family: "'Poppins', sans-serif" },
                            padding: 10
                        },
                        grid: {
                            color: 'rgba(255, 128, 155, 0.1)',
                            drawBorder: false
                        }
                    },
                    x: {
                        ticks: {
                            color: '#2B4865',
                            font: { size: 15, weight: '700', family: "'Poppins', sans-serif" },
                            padding: 10
                        },
                        grid: { display: false }
                    }
                }
            }
        });
    });
</script>
