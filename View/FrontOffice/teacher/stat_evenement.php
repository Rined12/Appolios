<?php
/**
 * APPOLIOS - Teacher Event Statistics
 */
?>

<div class="dashboard">
    <div class="container admin-dashboard-container" style="max-width: 1400px; width: 100%;">
        <div class="admin-layout">
            <?php $teacherSidebarActive = 'stat_evenements'; require __DIR__ . '/partials/sidebar.php'; ?>

            <div class="admin-main" style="background: transparent; padding: 1rem 0 2rem 0;">
                
                <!-- Action Buttons -->
                <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
                    <a href="javascript:history.back()" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; background: #6c757d;">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" style="transform: rotate(180deg);">
                            <path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/>
                        </svg>
                        Back
                    </a>

                    <a href="<?= APP_ENTRY ?>?url=teacher/export-stats-pdf" target="_blank" style="background: #dc3545; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: background 0.2s;" onmouseover="this.style.background='#c82333'" onmouseout="this.style.background='#dc3545'">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                        Export as PDF
                    </a>
                </div>
                
<style>
    .stat-card {
        background: #ffffff;
        padding: 2rem;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(43, 72, 101, 0.04);
        border: 1px solid rgba(84, 140, 168, 0.1);
        position: relative;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 100%;
        overflow: hidden;
    }
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 4px;
        background: linear-gradient(90deg, #548CA8, #E19864);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 35px rgba(43, 72, 101, 0.12);
        border-color: rgba(84, 140, 168, 0.3);
    }
    .stat-card:hover::before {
        opacity: 1;
    }
    .stat-title {
        color: #2B4865;
        margin-top: 0;
        margin-bottom: 1.5rem;
        font-size: 1.15rem;
        font-weight: 700;
        text-align: center;
        width: 100%;
        padding-bottom: 1rem;
        border-bottom: 1px dashed rgba(84, 140, 168, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    .stat-title i {
        color: #E19864;
    }
    .neo-glass-card {
        transition: all 0.3s ease;
    }
    .neo-glass-card:hover {
        box-shadow: 0 20px 45px rgba(43, 72, 101, 0.12);
    }
    .chart-wrapper {
        width: 100%;
        position: relative;
    }
    .pie-chart-wrapper {
        width: 80%;
        max-width: 350px;
        aspect-ratio: 1;
        position: relative;
        transition: transform 0.3s ease;
    }
    .stat-card:hover .pie-chart-wrapper {
        transform: scale(1.05);
    }
</style>
                <section class="neo-auth-wrap" style="background: transparent; font-family: 'Inter', sans-serif;">
                    
                    <div class="neo-glass-card" style="width: 100%; background: #ffffff; border-radius: 20px; box-shadow: 0 15px 40px rgba(43, 72, 101, 0.08); border: 1px solid rgba(233, 241, 250, 0.8); overflow: hidden; display: flex; flex-direction: column;">
                        
                        <!-- Top Part -->
                        <div class="neo-auth-grid" style="display: grid; grid-template-columns: 1.2fr 1fr; border-bottom: 1px solid #eef2f6;">
                            
                            <div style="padding: 3.5rem; background: #fcfcfc; position: relative; overflow: hidden; display: flex; flex-direction: column; justify-content: center;">
                                <div style="position: absolute; top: -50px; left: -50px; width: 200px; height: 200px; background: #e9f1fa; border-radius: 50%; z-index: 0; opacity: 0.7; transition: transform 0.5s ease;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'"></div>
                                <div style="position: absolute; bottom: 10%; right: -30px; width: 150px; height: 150px; background: #e1edf7; border-radius: 50%; z-index: 0; opacity: 0.5; transition: transform 0.5s ease;" onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform='scale(1)'"></div>
                                
                                <div style="position: relative; z-index: 2;">
                                    <h2 style="font-size: 2.8rem; font-weight: 800; color: #2B4865; line-height: 1.15; margin: 0 0 1rem 0; letter-spacing: -0.02em;">
                                        My Event<br><span style="color: #548CA8;">Statistics</span>
                                    </h2>
                                    <p style="color: #64748b; font-size: 1.1rem; line-height: 1.6; margin: 0 0 2rem 0; max-width: 90%;">
                                        Visualize comprehensive data and analytics about your events, user participation rates, and popular session types to optimize future events.
                                    </p>
                                </div>
                            </div>

                            <div style="padding: 2rem; display: flex; align-items: center; justify-content: center; position: relative; background: #fff; overflow: hidden;">
                                <div style="position: absolute; top: 15%; right: 10%; width: 70%; height: 70%; background: #E19864; border-radius: 30px; transform: rotate(5deg); z-index: 0; opacity: 0.15; transition: transform 0.5s ease;"></div>
                                <div style="position: absolute; bottom: 5%; left: 5%; width: 50%; height: 50%; background: #548CA8; border-radius: 30px; transform: rotate(-8deg); z-index: 0; opacity: 0.1; transition: transform 0.5s ease;"></div>
                                
                                <div style="position: relative; z-index: 1; display: flex; flex-direction: column; gap: 1.5rem; width: 100%; max-width: 280px;">
                                    <?php 
                                    $totalE = isset($eventStats) ? count($eventStats) : 0;
                                    $totalP = 0;
                                    if(isset($eventStats)) {
                                        foreach($eventStats as $es) { $totalP += (int)$es['participant_count']; }
                                    }
                                    ?>
                                    <!-- Widget 1: Total Events -->
                                    <div style="background: rgba(255, 255, 255, 0.85); padding: 1.5rem; border-radius: 16px; box-shadow: 0 10px 25px rgba(43, 72, 101, 0.1); display: flex; align-items: center; gap: 15px; transform: translateX(-15px); backdrop-filter: blur(10px); border: 1px solid rgba(84, 140, 168, 0.3); transition: transform 0.3s ease;" onmouseover="this.style.transform='translateX(-15px) translateY(-5px)'" onmouseout="this.style.transform='translateX(-15px) translateY(0)'">
                                        <div style="width: 55px; height: 55px; border-radius: 14px; background: linear-gradient(135deg, #548CA8, #2B4865); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.5rem; box-shadow: 0 5px 15px rgba(84, 140, 168, 0.3);">
                                            <i class="fas fa-calendar-check"></i>
                                        </div>
                                        <div>
                                            <div style="font-size: 2rem; font-weight: 800; color: #2B4865; line-height: 1.1;"><?= $totalE ?></div>
                                            <div style="font-size: 0.95rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Total Events</div>
                                        </div>
                                    </div>

                                    <!-- Widget 2: Total Participants -->
                                    <div style="background: rgba(255, 255, 255, 0.85); padding: 1.5rem; border-radius: 16px; box-shadow: 0 10px 25px rgba(43, 72, 101, 0.1); display: flex; align-items: center; gap: 15px; transform: translateX(15px); backdrop-filter: blur(10px); border: 1px solid rgba(225, 152, 100, 0.3); transition: transform 0.3s ease;" onmouseover="this.style.transform='translateX(15px) translateY(-5px)'" onmouseout="this.style.transform='translateX(15px) translateY(0)'">
                                        <div style="width: 55px; height: 55px; border-radius: 14px; background: linear-gradient(135deg, #E19864, #c97d4b); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.5rem; box-shadow: 0 5px 15px rgba(225, 152, 100, 0.3);">
                                            <i class="fas fa-users"></i>
                                        </div>
                                        <div>
                                            <div style="font-size: 2rem; font-weight: 800; color: #2B4865; line-height: 1.1;"><?= $totalP ?></div>
                                            <div style="font-size: 0.95rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Total Participants</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Statistics Charts Section -->
                        <div style="padding: 3rem 3.5rem; background: #ffffff;">
                            
                            <h3 style="margin: 0 0 2.5rem 0; color: #2B4865; font-size: 1.8rem; font-weight: 800; display: flex; align-items: center; gap: 12px;">
                                <i class="fas fa-chart-line" style="color: #548CA8;"></i> Performance & Engagement Overview
                            </h3>

                            <!-- Chart.js Setup -->
                            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2.5rem; margin-bottom: 2.5rem;">
                                <!-- Grouped Bar Chart -->
                                <div class="stat-card">
                                    <h4 class="stat-title"><i class="fas fa-users"></i> Events vs Participations</h4>
                                    <div class="chart-wrapper">
                                        <canvas id="mixedChart"></canvas>
                                    </div>
                                </div>

                                <!-- Status Chart -->
                                <div class="stat-card">
                                    <h4 class="stat-title"><i class="fas fa-tasks"></i> Participation Status</h4>
                                    <div class="chart-wrapper">
                                        <canvas id="statusChart"></canvas>
                                    </div>
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2.5rem;">
                                <!-- Participations per Type Pie Chart -->
                                <div class="stat-card">
                                    <h4 class="stat-title"><i class="fas fa-chart-pie"></i> Participations par Type d'Événement</h4>
                                    <div class="pie-chart-wrapper">
                                        <canvas id="eventParticipationPieChart"></canvas>
                                    </div>
                                </div>
                                
                                <!-- Optional pie chart for types -->
                                <div class="stat-card">
                                    <h4 class="stat-title"><i class="fas fa-layer-group"></i> Event Types Distribution</h4>
                                    <div class="pie-chart-wrapper">
                                        <canvas id="typePieChart"></canvas>
                                    </div>
                                </div>
                            </div>

<?php 
// Convert PHP data to Javascript variables
$eventLabels = [];
$capaciteData = [];
$participantData = [];
$refusedData = [];
$pendingData = [];

foreach ($eventStats as $stat) {
    // truncate long titles
    $title = strlen($stat['title']) > 15 ? substr($stat['title'], 0, 15) . '...' : $stat['title'];
    $eventLabels[] = $title;
    $capaciteData[] = (int)$stat['capacite_max'];
    $participantData[] = (int)$stat['participant_count'];
    $refusedData[] = isset($stat['refused_count']) ? (int)$stat['refused_count'] : 0;
    $pendingData[] = isset($stat['pending_count']) ? (int)$stat['pending_count'] : 0;
}

$typeLabels = [];
$typeData = [];
foreach ($typeStats as $ts) {
    $typeLabels[] = ucfirst($ts['type']);
    $typeData[] = (int)$ts['count'];
}

$partTypeLabels = [];
$partTypeData = [];
if (!empty($participantTypeStats)) {
    foreach ($participantTypeStats as $pts) {
        $partTypeLabels[] = ucfirst($pts['type']);
        $partTypeData[] = (int)$pts['participant_count'];
    }
}
?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Global Chart Defaults
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#64748b';
    Chart.defaults.scale.grid.color = 'rgba(233, 241, 250, 0.6)';

    const labels = <?= json_encode($eventLabels); ?>;
    const capaciteMax = <?= json_encode($capaciteData); ?>;
    const participants = <?= json_encode($participantData); ?>;
    const refused = <?= json_encode($refusedData); ?>;
    const pending = <?= json_encode($pendingData); ?>;
    
    const typeLabels = <?= json_encode($typeLabels); ?>;
    const typeData = <?= json_encode($typeData); ?>;
    
    const partTypeLabels = <?= json_encode($partTypeLabels); ?>;
    const partTypeData = <?= json_encode($partTypeData); ?>;

    const modernPalette = ['#2B4865', '#548CA8', '#E19864', '#91B3C6', '#F2C4A7', '#475569'];

    // 1. Grouped Bar Chart (Events vs Participation)
    const ctxMixed = document.getElementById('mixedChart').getContext('2d');
    let barGradient = ctxMixed.createLinearGradient(0, 0, 0, 400);
    barGradient.addColorStop(0, 'rgba(84, 140, 168, 0.9)');
    barGradient.addColorStop(1, 'rgba(43, 72, 101, 0.8)');

    new Chart(ctxMixed, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Participants',
                backgroundColor: barGradient,
                hoverBackgroundColor: '#E19864',
                data: participants,
                borderRadius: 6,
                borderWidth: 0,
                maxBarThickness: 40
            }]
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            scales: {
                y: {
                    beginAtZero: true,
                    border: { display: false },
                    grid: { color: 'rgba(233, 241, 250, 0.8)' }
                },
                x: {
                    border: { display: false },
                    grid: { display: false }
                }
            },
            plugins: {
                legend: { 
                    position: 'top',
                    labels: { font: { weight: '600' }, usePointStyle: true, pointStyle: 'circle' }
                },
                tooltip: {
                    backgroundColor: 'rgba(43, 72, 101, 0.9)',
                    titleFont: { size: 13, family: "'Inter', sans-serif" },
                    bodyFont: { size: 13, family: "'Inter', sans-serif" },
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: false
                }
            }
        }
    });

    // 2. Status Chart (Smooth Line Chart for Accepted, Pending, Refused)
    const ctxStatus = document.getElementById('statusChart').getContext('2d');
    new Chart(ctxStatus, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Acceptés',
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                data: participants,
                fill: true,
                tension: 0.4,
                borderWidth: 2,
                pointRadius: 4,
                pointBackgroundColor: '#28a745'
            }, {
                label: 'En attente',
                borderColor: '#ffc107',
                backgroundColor: 'rgba(255, 193, 7, 0.1)',
                data: pending,
                fill: true,
                tension: 0.4,
                borderWidth: 2,
                pointRadius: 4,
                pointBackgroundColor: '#ffc107'
            }, {
                label: 'Refusés',
                borderColor: '#dc3545',
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                data: refused,
                fill: true,
                tension: 0.4,
                borderWidth: 2,
                pointRadius: 4,
                pointBackgroundColor: '#dc3545'
            }]
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            scales: {
                y: {
                    beginAtZero: true,
                    border: { display: false },
                    grid: { color: 'rgba(233, 241, 250, 0.8)' }
                },
                x: {
                    border: { display: false },
                    grid: { display: false }
                }
            },
            plugins: {
                legend: { 
                    position: 'top',
                    labels: { font: { weight: '600' }, usePointStyle: true, pointStyle: 'circle' }
                },
                tooltip: {
                    backgroundColor: 'rgba(43, 72, 101, 0.9)',
                    titleFont: { size: 13, family: "'Inter', sans-serif" },
                    bodyFont: { size: 13, family: "'Inter', sans-serif" },
                    padding: 12,
                    cornerRadius: 8
                }
            }
        }
    });



    // 3. Participations per Event Type Polar Area Chart
    const ctxEventPart = document.getElementById('eventParticipationPieChart').getContext('2d');
    new Chart(ctxEventPart, {
        type: 'polarArea',
        data: {
            labels: partTypeLabels.length > 0 ? partTypeLabels : ['Aucune Donnée'],
            datasets: [{
                data: partTypeData.length > 0 ? partTypeData : [1],
                backgroundColor: partTypeLabels.length > 0 ? modernPalette.map(color => color + 'CC') : ['#e2e8f0'],
                borderWidth: 1,
                borderColor: '#ffffff',
                hoverOffset: 5
            }]
        },
        options: {
            responsive: true,
            scales: {
                r: {
                    ticks: { display: false },
                    grid: { color: 'rgba(233, 241, 250, 0.8)' }
                }
            },
            plugins: {
                legend: { 
                    position: 'bottom',
                    labels: { padding: 20, font: { weight: '500' }, usePointStyle: true, pointStyle: 'circle' }
                },
                tooltip: {
                    backgroundColor: 'rgba(43, 72, 101, 0.9)',
                    padding: 12,
                    cornerRadius: 8
                }
            }
        }
    });

    // 4. Event Types Pie Chart
    const ctxPie = document.getElementById('typePieChart').getContext('2d');
    new Chart(ctxPie, {
        type: 'doughnut',
        data: {
            labels: typeLabels,
            datasets: [{
                data: typeData,
                backgroundColor: modernPalette,
                borderWidth: 0,
                hoverOffset: 12
            }]
        },
        options: {
            responsive: true,
            cutout: '65%',
            plugins: {
                legend: { 
                    position: 'bottom',
                    labels: { padding: 20, font: { weight: '500' }, usePointStyle: true, pointStyle: 'circle' }
                },
                tooltip: {
                    backgroundColor: 'rgba(43, 72, 101, 0.9)',
                    padding: 12,
                    cornerRadius: 8
                }
            }
        }
    });
});
</script>

                        </div>

                    </div>
                </section>
            </div>
        </div>
    </div>
</div>