<?php
/**
 * APPOLIOS - Admin Event Statistics (Teacher-style design)
 */

// Pre-compute JS data safely (PHP 5 compatible)
$eventLabels     = array();
$capaciteData    = array();
$participantData = array();

$eventStatsSafe = isset($eventStats) ? $eventStats : array();
foreach ($eventStatsSafe as $stat) {
    $titleVal = isset($stat['title']) ? $stat['title'] : '';
    $title = strlen($titleVal) > 15 ? substr($titleVal, 0, 15) . '...' : $titleVal;
    $eventLabels[] = $title;
    $capaciteData[] = (int)(isset($stat['capacite_max']) ? $stat['capacite_max'] : 0);
    $participantData[] = (int)(isset($stat['participant_count']) ? $stat['participant_count'] : 0);
}

$typeLabels = array();
$typeData   = array();
$typeStatsSafe = isset($typeStats) ? $typeStats : array();
foreach ($typeStatsSafe as $ts) {
    $typeLabels[] = ucfirst(isset($ts['type']) ? $ts['type'] : 'Autre');
    $typeData[]   = (int)(isset($ts['count']) ? $ts['count'] : 0);
}

$partTypeLabels = array();
$partTypeData   = array();
$participantTypeStatsSafe = isset($participantTypeStats) ? $participantTypeStats : array();
foreach ($participantTypeStatsSafe as $pts) {
    $partTypeLabels[] = ucfirst(isset($pts['type']) ? $pts['type'] : 'Autre');
    $partTypeData[]   = (int)(isset($pts['participant_count']) ? $pts['participant_count'] : 0);
}

$totalE = count($eventStatsSafe);
$totalP = array_sum($participantData);

$statusMap = array('planifie' => 0, 'en_cours' => 0, 'termine' => 0, 'annule' => 0);
$statusStatsSafe = isset($statusStats) ? $statusStats : array();
foreach ($statusStatsSafe as $ss) {
    $stKey = isset($ss['statut']) ? $ss['statut'] : '';
    if ($stKey !== '' && isset($statusMap[$stKey])) {
        $statusMap[$stKey] = (int)(isset($ss['count']) ? $ss['count'] : 0);
    }
}
?>

<!-- Action Buttons -->
<div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
    <a href="<?= APP_ENTRY ?>?url=admin/statistics" style="display: inline-flex; align-items: center; gap: 8px; background: rgba(255, 255, 255, 0.05); color:#cbd5e1; border: 1px solid rgba(255, 255, 255, 0.1); padding:9px 16px; border-radius:8px; text-decoration:none; font-weight:600; font-size:.88rem; transition: all 0.2s;" onmouseover="this.style.background='rgba(255, 255, 255, 0.1)'; this.style.color='#ffffff';" onmouseout="this.style.background='rgba(255, 255, 255, 0.05)'; this.style.color='#cbd5e1';">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" style="transform: rotate(180deg);">
            <path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/>
        </svg>
        Retour
    </a>
</div>

<style>
    .stat-card {
        background: rgba(30, 41, 59, 0.5);
        padding: 2rem;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.05);
        position: relative;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
        width: 100%;
        min-width: 0;
        overflow: hidden;
        box-sizing: border-box;
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
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
        border-color: rgba(225, 152, 100, 0.3);
    }
    .stat-card:hover::before { opacity: 1; }
    .stat-title {
        color: #ffffff;
        margin-top: 0;
        margin-bottom: 1.5rem;
        font-size: 1.05rem;
        font-weight: 700;
        text-align: center;
        width: 100%;
        padding-bottom: 1rem;
        border-bottom: 1px dashed rgba(255, 255, 255, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        flex-shrink: 0;
    }
    .stat-title i { color: #E19864; }
    .neo-glass-card { transition: all 0.3s ease; }
    .neo-glass-card:hover { box-shadow: 0 20px 45px rgba(0, 0, 0, 0.3); }
    .chart-wrapper {
        width: 100%;
        height: 280px;
        position: relative;
        min-width: 0;
        overflow: hidden;
    }
    .chart-wrapper canvas {
        max-width: 100% !important;
    }
    .pie-chart-wrapper {
        width: 80%; max-width: 280px; height: 260px;
        position: relative; transition: transform 0.3s ease;
        flex-shrink: 0;
    }
    .stat-card:hover .pie-chart-wrapper { transform: scale(1.03); }
    .stat-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2.5rem;
        min-width: 0;
    }
</style>

<section class="neo-auth-wrap" style="background: transparent; font-family: 'Inter', sans-serif;">

    <div class="neo-glass-card" style="width: 100%; background: #0f172a; border-radius: 20px; box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2); border: 1px solid rgba(255, 255, 255, 0.1); overflow: hidden; display: flex; flex-direction: column;">

        <!-- Top Part: Hero -->
        <div class="neo-auth-grid" style="display: grid; grid-template-columns: 1.2fr 1fr; border-bottom: 1px solid rgba(255, 255, 255, 0.05);">

            <!-- Left: Title & Description -->
            <div style="padding: 3.5rem; background: rgba(30, 41, 59, 0.3); position: relative; overflow: hidden; display: flex; flex-direction: column; justify-content: center;">
                <div style="position: absolute; top: -50px; left: -50px; width: 200px; height: 200px; background: #e9f1fa; border-radius: 50%; z-index: 0; opacity: 0.7; transition: transform 0.5s ease;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'"></div>
                <div style="position: absolute; bottom: 10%; right: -30px; width: 150px; height: 150px; background: #e1edf7; border-radius: 50%; z-index: 0; opacity: 0.5; transition: transform 0.5s ease;" onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform='scale(1)'"></div>

                <div style="position: relative; z-index: 2;">
                    <h2 style="font-size: 2.8rem; font-weight: 800; color: #ffffff; line-height: 1.15; margin: 0 0 1rem 0; letter-spacing: -0.02em;">
                        Event<br><span style="color: #E19864;">Statistics</span>
                    </h2>
                    <p style="color: #94a3b8; font-size: 1.1rem; line-height: 1.6; margin: 0 0 2rem 0; max-width: 90%;">
                        Visualize comprehensive data and analytics about your events, participation rates, and popular session types to optimize future events.
                    </p>
                </div>
            </div>

            <!-- Right: KPI Widgets -->
            <div style="padding: 2rem; display: flex; align-items: center; justify-content: center; position: relative; background: transparent; overflow: hidden;">
                <div style="position: absolute; top: 15%; right: 10%; width: 70%; height: 70%; background: #E19864; border-radius: 30px; transform: rotate(5deg); z-index: 0; opacity: 0.15;"></div>
                <div style="position: absolute; bottom: 5%; left: 5%; width: 50%; height: 50%; background: #548CA8; border-radius: 30px; transform: rotate(-8deg); z-index: 0; opacity: 0.1;"></div>

                <div style="position: relative; z-index: 1; display: flex; flex-direction: column; gap: 1.5rem; width: 100%; max-width: 280px;">

                    <!-- Widget 1: Total Events -->
                    <div style="background: rgba(30, 41, 59, 0.8); padding: 1.5rem; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,.2); display: flex; align-items: center; gap: 15px; transform: translateX(-15px); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,.05); transition: transform 0.3s ease;" onmouseover="this.style.transform='translateX(-15px) translateY(-5px)'; this.style.borderColor='rgba(225, 152, 100, 0.3)';" onmouseout="this.style.transform='translateX(-15px) translateY(0)'; this.style.borderColor='rgba(255,255,255,.05)';" >
                        <div style="width: 55px; height: 55px; border-radius: 14px; background: linear-gradient(135deg, #E19864, #d9804b); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.5rem; box-shadow: 0 5px 15px rgba(225,152,100,.3);">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div>
                            <div style="font-size: 2rem; font-weight: 800; color: #ffffff; line-height: 1.1;"><?= $totalE ?></div>
                            <div style="font-size: 0.95rem; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Total Events</div>
                        </div>
                    </div>

                    <!-- Widget 2: Total Participants -->
                    <div style="background: rgba(30, 41, 59, 0.8); padding: 1.5rem; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,.2); display: flex; align-items: center; gap: 15px; transform: translateX(15px); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,.05); transition: transform 0.3s ease;" onmouseover="this.style.transform='translateX(15px) translateY(-5px)'; this.style.borderColor='rgba(225, 152, 100, 0.3)';" onmouseout="this.style.transform='translateX(15px) translateY(0)'; this.style.borderColor='rgba(255,255,255,.05)';" >
                        <div style="width: 55px; height: 55px; border-radius: 14px; background: linear-gradient(135deg, #E19864, #c97d4b); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.5rem; box-shadow: 0 5px 15px rgba(225,152,100,.3);">
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <div style="font-size: 2rem; font-weight: 800; color: #ffffff; line-height: 1.1;"><?= $totalP ?></div>
                            <div style="font-size: 0.95rem; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Total Participants</div>
                        </div>
                    </div>

                    <!-- Widget 3: Terminés -->
                    <div style="background: rgba(30, 41, 59, 0.8); padding: 1.5rem; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,.2); display: flex; align-items: center; gap: 15px; transform: translateX(-15px); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,.05); transition: transform 0.3s ease;" onmouseover="this.style.transform='translateX(-15px) translateY(-5px)'; this.style.borderColor='rgba(225, 152, 100, 0.3)';" onmouseout="this.style.transform='translateX(-15px) translateY(0)'; this.style.borderColor='rgba(255,255,255,.05)';" >
                        <div style="width: 55px; height: 55px; border-radius: 14px; background: linear-gradient(135deg, #10b981, #059669); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.5rem; box-shadow: 0 5px 15px rgba(16,185,129,.3);">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div>
                            <div style="font-size: 2rem; font-weight: 800; color: #ffffff; line-height: 1.1;"><?= $statusMap['termine'] ?></div>
                            <div style="font-size: 0.95rem; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Terminés</div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div style="padding: 3rem 3.5rem; background: transparent;">

            <h3 style="margin: 0 0 2.5rem 0; color: #ffffff; font-size: 1.8rem; font-weight: 800; display: flex; align-items: center; gap: 12px;">
                <i class="fas fa-chart-line" style="color: #E19864;"></i> Performance &amp; Engagement Overview
            </h3>

            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

            <div class="stat-grid-2" style="margin-bottom: 2.5rem;">
                <div class="stat-card" style="min-height:380px;">
                    <h4 class="stat-title"><i class="fas fa-users"></i> Events vs Participations</h4>
                    <div class="chart-wrapper"><canvas id="mixedChart"></canvas></div>
                </div>

                <div class="stat-card" style="min-height:380px;">
                    <h4 class="stat-title"><i class="fas fa-tasks"></i> Statut des Événements</h4>
                    <div style="display:flex; align-items:center; justify-content:center; flex:1; width:100%;">
                        <div style="width:240px; height:240px; position:relative;">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="stat-grid-2">
                <div class="stat-card" style="min-height:380px;">
                    <h4 class="stat-title"><i class="fas fa-chart-pie"></i> Participations par Type d'Événement</h4>
                    <div style="display:flex; align-items:center; justify-content:center; flex:1; width:100%;">
                        <div style="width:240px; height:240px; position:relative;">
                            <canvas id="eventParticipationPieChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="stat-card" style="min-height:380px;">
                    <h4 class="stat-title"><i class="fas fa-layer-group"></i> Event Types Distribution</h4>
                    <div style="display:flex; align-items:center; justify-content:center; flex:1; width:100%;">
                        <div style="width:240px; height:240px; position:relative;">
                            <canvas id="typePieChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

<?php
$jsEventLabels  = json_encode($eventLabels);
$jsParticipants = json_encode($participantData);
$jsCapacite     = json_encode($capaciteData);
$jsTypeLabels   = json_encode($typeLabels);
$jsTypeData     = json_encode($typeData);
$jsPartLabels   = json_encode($partTypeLabels);
$jsPartData     = json_encode($partTypeData);
?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#94a3b8';
    Chart.defaults.scale.grid.color = 'rgba(255, 255, 255, 0.05)';

    const labels      = <?= $jsEventLabels ?>;
    const participants = <?= $jsParticipants ?>;
    const capaciteMax  = <?= $jsCapacite ?>;
    const typeLabels   = <?= $jsTypeLabels ?>;
    const typeData     = <?= $jsTypeData ?>;
    const partLabels   = <?= $jsPartLabels ?>;
    const partData     = <?= $jsPartData ?>;

    const modernPalette = ['#2B4865', '#548CA8', '#E19864', '#91B3C6', '#F2C4A7', '#475569'];

    const ctxMixed = document.getElementById('mixedChart').getContext('2d');
    let barGradient = ctxMixed.createLinearGradient(0, 0, 0, 300);
    barGradient.addColorStop(0, 'rgba(225, 152, 100, 0.9)');
    barGradient.addColorStop(1, 'rgba(225, 152, 100, 0.5)');

    new Chart(ctxMixed, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Participants',
                backgroundColor: barGradient,
                hoverBackgroundColor: '#E19864',
                data: participants,
                borderRadius: 6, borderWidth: 0, maxBarThickness: 40
            }, {
                label: 'Capacité Max',
                backgroundColor: 'rgba(255, 255, 255, 0.05)',
                data: capaciteMax,
                borderRadius: 6, borderWidth: 0, maxBarThickness: 40
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            scales: {
                y: { beginAtZero: true, border: { display: false }, grid: { color: 'rgba(255, 255, 255, 0.05)' } },
                x: { border: { display: false }, grid: { display: false } }
            },
            plugins: {
                legend: { position: 'top', labels: { font: { weight: '600' }, usePointStyle: true, pointStyle: 'circle' } },
                tooltip: { backgroundColor: 'rgba(15, 23, 42, 0.9)', padding: 12, cornerRadius: 8, titleColor: '#ffffff', bodyColor: '#cbd5e1' }
            }
        }
    });

    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: ['Planifié', 'En cours', 'Terminé', 'Annulé'],
            datasets: [{
                data: [<?= $statusMap['planifie'] ?>, <?= $statusMap['en_cours'] ?>, <?= $statusMap['termine'] ?>, <?= $statusMap['annule'] ?>],
                backgroundColor: ['#f59e0b', '#10b981', '#3b82f6', '#ef4444'],
                borderWidth: 0, hoverOffset: 12
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: { position: 'bottom', labels: { padding: 16, font: { weight: '500' }, usePointStyle: true, pointStyle: 'circle' } },
                tooltip: { backgroundColor: 'rgba(15, 23, 42, 0.9)', padding: 12, cornerRadius: 8, titleColor: '#ffffff', bodyColor: '#cbd5e1' }
            }
        }
    });

    new Chart(document.getElementById('eventParticipationPieChart'), {
        type: 'polarArea',
        data: {
            labels: partLabels.length > 0 ? partLabels : ['Aucune Donnée'],
            datasets: [{
                data: partData.length > 0 ? partData : [1],
                backgroundColor: partLabels.length > 0 ? modernPalette.map(c => c + 'CC') : ['#e2e8f0'],
                borderWidth: 1, borderColor: '#ffffff', hoverOffset: 5
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            scales: { r: { ticks: { display: false }, grid: { color: 'rgba(233,241,250,.8)' } } },
            plugins: {
                legend: { position: 'bottom', labels: { padding: 16, font: { weight: '500' }, usePointStyle: true, pointStyle: 'circle' } },
                tooltip: { backgroundColor: 'rgba(15, 23, 42, 0.9)', padding: 12, cornerRadius: 8, titleColor: '#ffffff', bodyColor: '#cbd5e1' }
            }
        }
    });

    new Chart(document.getElementById('typePieChart'), {
        type: 'doughnut',
        data: {
            labels: typeLabels,
            datasets: [{
                data: typeData,
                backgroundColor: modernPalette,
                borderWidth: 0, hoverOffset: 12
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: { position: 'bottom', labels: { padding: 16, font: { weight: '500' }, usePointStyle: true, pointStyle: 'circle' } },
                tooltip: { backgroundColor: 'rgba(15, 23, 42, 0.9)', padding: 12, cornerRadius: 8, titleColor: '#ffffff', bodyColor: '#cbd5e1' }
            }
        }
    });
});
</script>

        </div>
    </div>
</section>
