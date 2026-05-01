<?php
/**
 * APPOLIOS - Admin Event Statistics
 */
?>

<div class="dashboard">
    <div class="container admin-dashboard-container" style="max-width: 1400px; width: 100%;">
        <div class="admin-layout">
            <?php $adminSidebarActive = 'stat_evenements'; require __DIR__ . '/partials/sidebar.php'; ?>

            <div class="admin-main" style="background: transparent; padding: 1rem 0 2rem 0;">
                
                <!-- Action Buttons -->
                <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
                    <a href="javascript:history.back()" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; background: #6c757d;">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" style="transform: rotate(180deg);">
                            <path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/>
                        </svg>
                        Back
                    </a>

                    <a href="<?= APP_ENTRY ?>?url=event/export-stats-pdf" target="_blank" style="background: #dc3545; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: background 0.2s;" onmouseover="this.style.background='#c82333'" onmouseout="this.style.background='#dc3545'">
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
                
                <section class="neo-auth-wrap" style="background: transparent; font-family: 'Inter', sans-serif;">
                    
                    <div class="neo-glass-card" style="width: 100%; background: #ffffff; border-radius: 20px; box-shadow: 0 15px 40px rgba(43, 72, 101, 0.08); border: 1px solid rgba(233, 241, 250, 0.8); overflow: hidden; display: flex; flex-direction: column;">
                        
                        <!-- Top Part -->
                        <div class="neo-auth-grid" style="display: grid; grid-template-columns: 1.2fr 1fr; border-bottom: 1px solid #eef2f6;">
                            
                            <div style="padding: 3.5rem; background: #fcfcfc; position: relative; overflow: hidden; display: flex; flex-direction: column; justify-content: center;">
                                <div style="position: absolute; top: -50px; left: -50px; width: 200px; height: 200px; background: #e9f1fa; border-radius: 50%; z-index: 0; opacity: 0.7;"></div>
                                <div style="position: absolute; bottom: 10%; right: -30px; width: 150px; height: 150px; background: #e1edf7; border-radius: 50%; z-index: 0; opacity: 0.5;"></div>
                                
                                <div style="position: relative; z-index: 2;">
                                    <h2 style="font-size: 2.8rem; font-weight: 800; color: #2B4865; line-height: 1.15; margin: 0 0 1rem 0; letter-spacing: -0.02em;">
                                        Event<br><span style="color: #548CA8;">Statistics</span>
                                    </h2>
                                    <p style="color: #64748b; font-size: 1.1rem; line-height: 1.6; margin: 0 0 2rem 0; max-width: 90%;">
                                        Visualize comprehensive data and analytics about your events, user participation rates, and popular session types to optimize future events.
                                    </p>
                                </div>
                            </div>

                            <div style="padding: 2rem; display: flex; align-items: center; justify-content: center; position: relative; background: #fff; overflow: hidden;">
                                <div style="position: absolute; top: 15%; right: 10%; width: 70%; height: 70%; background: #E19864; border-radius: 30px; transform: rotate(5deg); z-index: 0; opacity: 0.15;"></div>
                                <div style="position: absolute; bottom: 5%; left: 5%; width: 50%; height: 50%; background: #548CA8; border-radius: 30px; transform: rotate(-8deg); z-index: 0; opacity: 0.1;"></div>
                                
                                <div style="display: flex; gap: 1.5rem; position: relative; z-index: 1;">
                                    <img src="<?= APP_URL ?>/View/assets/images/event/admin-events-hero.png" alt="Stats" style="width: 220px; height: 300px; object-fit: cover; border-radius: 20px; box-shadow: 0 15px 30px rgba(43, 72, 101, 0.15); border: 5px solid #fff; transform: translateY(-5px);">
                                </div>
                            </div>
                        </div>

                        <!-- Statistics Charts Section -->
                        <div style="padding: 2.5rem 3.5rem; background: #ffffff;">
                            
                            <h3 style="margin: 0 0 2rem 0; color: #2B4865; font-size: 1.6rem; font-weight: 700;">Performance & Engagement Overview</h3>

                            <!-- Chart.js Setup -->
                            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
                                <!-- Grouped Bar Chart -->
                                <div style="background: #f8fafc; padding: 2rem; border-radius: 16px; border: 1px solid #e2e8f0; position: relative;">
                                    <h4 style="color: #475569; margin-top: 0; margin-bottom: 1rem; font-size: 1.1rem; text-align: center;">Events vs Participations</h4>
                                    <canvas id="mixedChart"></canvas>
                                </div>

                                <!-- Line Chart -->
                                <div style="background: #f8fafc; padding: 2rem; border-radius: 16px; border: 1px solid #e2e8f0; position: relative;">
                                    <h4 style="color: #475569; margin-top: 0; margin-bottom: 1rem; font-size: 1.1rem; text-align: center;">Participation vs Capacity</h4>
                                    <canvas id="minMaxChart"></canvas>
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                                <!-- Event vs Participation Pie Chart -->
                                <div style="background: #f8fafc; padding: 2rem; border-radius: 16px; border: 1px solid #e2e8f0; position: relative; display: flex; flex-direction: column; align-items: center;">
                                    <h4 style="color: #475569; margin-top: 0; margin-bottom: 1rem; font-size: 1.1rem; text-align: center;">Events vs Participations (Pie Chart)</h4>
                                    <div style="width: 80%; max-width: 400px; aspect-ratio: 1;">
                                        <canvas id="eventParticipationPieChart"></canvas>
                                    </div>
                                </div>
                                
                                <!-- Optional pie chart for types -->
                                <div style="background: #f8fafc; padding: 2rem; border-radius: 16px; border: 1px solid #e2e8f0; position: relative; display: flex; flex-direction: column; align-items: center;">
                                    <h4 style="color: #475569; margin-top: 0; margin-bottom: 1rem; font-size: 1.1rem; text-align: center;">Event Types Breakdown</h4>
                                    <div style="width: 80%; max-width: 400px; aspect-ratio: 1;">
                                        <canvas id="typePieChart"></canvas>
                                    </div>
                                </div>
                            </div>

<?php 
// Convert PHP data to Javascript variables
$eventLabels = [];
$capaciteData = [];
$participantData = [];

foreach ($eventStats as $stat) {
    // truncate long titles
    $title = strlen($stat['title']) > 15 ? substr($stat['title'], 0, 15) . '...' : $stat['title'];
    $eventLabels[] = $title;
    $capaciteData[] = (int)$stat['capacite_max'];
    $participantData[] = (int)$stat['participant_count'];
}

$typeLabels = [];
$typeData = [];
foreach ($typeStats as $ts) {
    $typeLabels[] = ucfirst($ts['type']);
    $typeData[] = (int)$ts['count'];
}
?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const labels = <?= json_encode($eventLabels); ?>;
    const capaciteMax = <?= json_encode($capaciteData); ?>;
    const participants = <?= json_encode($participantData); ?>;
    
    const typeLabels = <?= json_encode($typeLabels); ?>;
    const typeData = <?= json_encode($typeData); ?>;

    // 1. Grouped Bar Chart (Events vs Participation)
    const ctxMixed = document.getElementById('mixedChart').getContext('2d');
    new Chart(ctxMixed, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Participants',
                backgroundColor: '#FFB6D5', // Dataset 1 (pink bars)
                data: participants
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f5f9' }
                },
                x: {
                    grid: { display: false }
                }
            },
            plugins: {
                legend: { position: 'top' }
            }
        }
    });

    // 2. Line Chart
    const ctxLine = document.getElementById('minMaxChart').getContext('2d');
    new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Max Capacity',
                data: capaciteMax,
                borderColor: '#FF6384', // Pink line
                backgroundColor: '#FF6384',
                borderWidth: 2,
                pointBackgroundColor: '#FF6384',
                pointRadius: 4,
                tension: 0,
                fill: false
            }, {
                label: 'Participants',
                data: participants,
                borderColor: '#36A2EB', // Blue line
                backgroundColor: '#36A2EB',
                borderWidth: 2,
                pointBackgroundColor: '#36A2EB',
                pointRadius: 4,
                tension: 0,
                fill: false
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    min: -10,
                    max: 50,
                    ticks: {
                        stepSize: 10
                    },
                    grid: { color: '#f1f5f9' }
                },
                x: {
                    grid: { display: false }
                }
            },
            plugins: {
                legend: { position: 'top' }
            }
        }
    });

    // 3. Event vs Participation Pie Chart
    const totalEvents = labels.length;
    const totalParticipants = participants.reduce((sum, val) => sum + val, 0);

    const ctxEventPart = document.getElementById('eventParticipationPieChart').getContext('2d');
    new Chart(ctxEventPart, {
        type: 'pie',
        data: {
            labels: ['Total Events', 'Total Participants'],
            datasets: [{
                data: [totalEvents, totalParticipants],
                backgroundColor: ['#4bc0c0', '#ff9f40'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed !== null) {
                                label += context.parsed;
                            }
                            return label;
                        }
                    }
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
                backgroundColor: ['#548CA8', '#E19864', '#FFB6C1', '#2B4865', '#a8b8d8', '#f8b4a6'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
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