<?php
/**
 * APPOLIOS - Event Statistics Dashboard (Dark Theme)
 */

// Helper function for safe array access
function get($array, $key, $default = 0) {
    return isset($array[$key]) ? $array[$key] : $default;
}

$stats = isset($stats) ? $stats : array();
$eventsByType = isset($eventsByType) ? $eventsByType : array();
$eventsByStatus = isset($eventsByStatus) ? $eventsByStatus : array();
$monthlyEvents = isset($monthlyEvents) ? $monthlyEvents : array();
$capacityStats = isset($capacityStats) ? $capacityStats : array();
$topCreators = isset($topCreators) ? $topCreators : array();
$upcomingEvents = isset($upcomingEvents) ? $upcomingEvents : array();
$participationStats = isset($participationStats) ? $participationStats : array();
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

<div class="admin-main" style="background: #0f172a; min-height: 100vh; padding: 2rem;">
    <div style="max-width: 1400px; margin: 0 auto;">
        
        <div style="margin-bottom: 2.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h1 style="font-size: 2.5rem; font-weight: 800; color: #fff; margin: 0 0 0.5rem 0;">
                        Stat <span style="color: #E19864;">Evenements</span>
                    </h1>
                    <p style="color: #94a3b8; font-size: 1rem; margin: 0;">
                        Comprehensive analytics and insights for your events
                    </p>
                </div>
                <a href="<?= APP_ENTRY ?>?url=event/evenements" 
                    style="background: #1e293b; color: #94a3b8; border: 1px solid #334155; padding: 12px 24px; border-radius: 10px; font-weight: 600; text-decoration: none; transition: all 0.2s; display: inline-flex; align-items: center; gap: 8px;"
                    onmouseover="this.style.background='#334155'; this.style.color='#f1f5f9'" 
                    onmouseout="this.style.background='#1e293b'; this.style.color='#94a3b8'">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    Back to Events
                </a>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; margin-bottom: 2.5rem;">
            <div style="background: linear-gradient(145deg, #1e293b 0%, #0f172a 100%); border: 1px solid #334155; border-radius: 16px; padding: 1.5rem;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                    </div>
                    <div>
                        <div style="color: #94a3b8; font-size: 0.875rem; font-weight: 500;">Total Events</div>
                        <div style="color: #fff; font-size: 2rem; font-weight: 800;"><?= get($stats, 'total', 0) ?></div>
                    </div>
                </div>
            </div>

            <div style="background: linear-gradient(145deg, #1e293b 0%, #0f172a 100%); border: 1px solid #334155; border-radius: 16px; padding: 1.5rem;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                    </div>
                    <div>
                        <div style="color: #94a3b8; font-size: 0.875rem; font-weight: 500;">Approved</div>
                        <div style="color: #fff; font-size: 2rem; font-weight: 800;"><?= get($stats, 'approved', 0) ?></div>
                    </div>
                </div>
            </div>

            <div style="background: linear-gradient(145deg, #1e293b 0%, #0f172a 100%); border: 1px solid #334155; border-radius: 16px; padding: 1.5rem;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    <div>
                        <div style="color: #94a3b8; font-size: 0.875rem; font-weight: 500;">Pending</div>
                        <div style="color: #fff; font-size: 2rem; font-weight: 800;"><?= get($stats, 'pending', 0) ?></div>
                    </div>
                </div>
            </div>

            <div style="background: linear-gradient(145deg, #1e293b 0%, #0f172a 100%); border: 1px solid #334155; border-radius: 16px; padding: 1.5rem;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="15" y1="9" x2="9" y2="15"></line>
                            <line x1="9" y1="9" x2="15" y2="15"></line>
                        </svg>
                    </div>
                    <div>
                        <div style="color: #94a3b8; font-size: 0.875rem; font-weight: 500;">Rejected</div>
                        <div style="color: #fff; font-size: 2rem; font-weight: 800;"><?= get($stats, 'rejected', 0) ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 1.5rem; margin-bottom: 2.5rem;">
            <div style="background: linear-gradient(145deg, #1e293b 0%, #0f172a 100%); border: 1px solid #334155; border-radius: 16px; padding: 1.5rem;">
                <h3 style="color: #fff; font-size: 1.25rem; font-weight: 700; margin: 0 0 1.5rem 0;">Events by Type</h3>
                <canvas id="typeChart" height="200"></canvas>
            </div>

            <div style="background: linear-gradient(145deg, #1e293b 0%, #0f172a 100%); border: 1px solid #334155; border-radius: 16px; padding: 1.5rem;">
                <h3 style="color: #fff; font-size: 1.25rem; font-weight: 700; margin: 0 0 1.5rem 0;">Events by Status</h3>
                <canvas id="statusChart" height="200"></canvas>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 2.5rem;">
            <div style="background: linear-gradient(145deg, #1e293b 0%, #0f172a 100%); border: 1px solid #334155; border-radius: 16px; padding: 1.5rem;">
                <h3 style="color: #fff; font-size: 1.25rem; font-weight: 700; margin: 0 0 1.5rem 0;">Monthly Event Creation Trend</h3>
                <canvas id="monthlyChart" height="150"></canvas>
            </div>

            <div style="background: linear-gradient(145deg, #1e293b 0%, #0f172a 100%); border: 1px solid #334155; border-radius: 16px; padding: 1.5rem;">
                <h3 style="color: #fff; font-size: 1.25rem; font-weight: 700; margin: 0 0 1.5rem 0;">Capacity Overview</h3>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: #0f172a; border-radius: 10px;">
                        <span style="color: #94a3b8; font-size: 0.9rem;">Average</span>
                        <span style="color: #E19864; font-size: 1.25rem; font-weight: 700;"><?= round(get($capacityStats, 'avg_capacity', 0)) ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: #0f172a; border-radius: 10px;">
                        <span style="color: #94a3b8; font-size: 0.9rem;">Total</span>
                        <span style="color: #3b82f6; font-size: 1.25rem; font-weight: 700;"><?= get($capacityStats, 'total_capacity', 0) ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: #0f172a; border-radius: 10px;">
                        <span style="color: #94a3b8; font-size: 0.9rem;">Maximum</span>
                        <span style="color: #10b981; font-size: 1.25rem; font-weight: 700;"><?= get($capacityStats, 'max_capacity', 0) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 1.5rem; margin-bottom: 2.5rem;">
            <div style="background: linear-gradient(145deg, #1e293b 0%, #0f172a 100%); border: 1px solid #334155; border-radius: 16px; padding: 1.5rem;">
                <h3 style="color: #fff; font-size: 1.25rem; font-weight: 700; margin: 0 0 1.5rem 0;">Participation Statistics</h3>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; text-align: center;">
                    <div style="padding: 1rem; background: #0f172a; border-radius: 12px;">
                        <div style="color: #8B5CF6; font-size: 2rem; font-weight: 800;"><?= get($participationStats, 'total_participations', 0) ?></div>
                        <div style="color: #94a3b8; font-size: 0.8rem; margin-top: 0.5rem;">Total Participations</div>
                    </div>
                    <div style="padding: 1rem; background: #0f172a; border-radius: 12px;">
                        <div style="color: #E19864; font-size: 2rem; font-weight: 800;"><?= get($participationStats, 'events_with_participation', 0) ?></div>
                        <div style="color: #94a3b8; font-size: 0.8rem; margin-top: 0.5rem;">Events with Participants</div>
                    </div>
                    <div style="padding: 1rem; background: #0f172a; border-radius: 12px;">
                        <div style="color: #10b981; font-size: 2rem; font-weight: 800;"><?= get($participationStats, 'unique_participants', 0) ?></div>
                        <div style="color: #94a3b8; font-size: 0.8rem; margin-top: 0.5rem;">Unique Participants</div>
                    </div>
                </div>
            </div>

            <div style="background: linear-gradient(145deg, #1e293b 0%, #0f172a 100%); border: 1px solid #334155; border-radius: 16px; padding: 1.5rem;">
                <h3 style="color: #fff; font-size: 1.25rem; font-weight: 700; margin: 0 0 1.5rem 0;">Top Event Creators</h3>
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <?php foreach ($topCreators as $index => $creator): ?>
                    <div style="display: flex; align-items: center; gap: 1rem; padding: 0.75rem; background: #0f172a; border-radius: 10px;">
                        <div style="width: 36px; height: 36px; background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 0.875rem;">
                            <?= $index + 1 ?>
                        </div>
                        <div style="flex: 1;">
                            <div style="color: #f1f5f9; font-weight: 600; font-size: 0.95rem;"><?= htmlspecialchars(get($creator, 'name', 'Unknown')) ?></div>
                            <div style="color: #64748b; font-size: 0.8rem;"><?= htmlspecialchars(get($creator, 'role', 'User')) ?></div>
                        </div>
                        <div style="color: #E19864; font-weight: 700; font-size: 1.1rem;">
                            <?= get($creator, 'event_count', 0) ?> events
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php if (empty($topCreators)): ?>
                    <div style="color: #64748b; text-align: center; padding: 2rem;">No creators found</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div style="background: linear-gradient(145deg, #1e293b 0%, #0f172a 100%); border: 1px solid #334155; border-radius: 16px; padding: 1.5rem;">
            <h3 style="color: #fff; font-size: 1.25rem; font-weight: 700; margin: 0 0 1.5rem 0;">Upcoming Events (Next 30 Days)</h3>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 1px solid #334155;">
                            <th style="text-align: left; padding: 1rem; color: #94a3b8; font-size: 0.875rem; font-weight: 600;">Event</th>
                            <th style="text-align: left; padding: 1rem; color: #94a3b8; font-size: 0.875rem; font-weight: 600;">Date</th>
                            <th style="text-align: left; padding: 1rem; color: #94a3b8; font-size: 0.875rem; font-weight: 600;">Type</th>
                            <th style="text-align: left; padding: 1rem; color: #94a3b8; font-size: 0.875rem; font-weight: 600;">Creator</th>
                            <th style="text-align: left; padding: 1rem; color: #94a3b8; font-size: 0.875rem; font-weight: 600;">Capacity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($upcomingEvents as $event): ?>
                        <tr style="border-bottom: 1px solid #1e293b;">
                            <td style="padding: 1rem; color: #f1f5f9; font-weight: 600;">
                                <?= htmlspecialchars(isset($event['titre']) && $event['titre'] ? $event['titre'] : (isset($event['title']) ? $event['title'] : 'Untitled')) ?>
                            </td>
                            <td style="padding: 1rem; color: #94a3b8;">
                                <?= isset($event['date_debut']) ? htmlspecialchars($event['date_debut']) : 'TBA' ?>
                            </td>
                            <td style="padding: 1rem;">
                                <span style="background: #334155; color: #cbd5e1; padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.8rem; font-weight: 500;">
                                    <?= htmlspecialchars(isset($event['type']) && $event['type'] ? $event['type'] : 'General') ?>
                                </span>
                            </td>
                            <td style="padding: 1rem; color: #94a3b8;">
                                <?= htmlspecialchars(get($event, 'creator_name', 'Unknown')) ?>
                            </td>
                            <td style="padding: 1rem; color: #E19864; font-weight: 600;">
                                <?= get($event, 'capacite_max', 'N/A') ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($upcomingEvents)): ?>
                        <tr>
                            <td colspan="5" style="padding: 2rem; text-align: center; color: #64748b;">
                                No upcoming events in the next 30 days
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    Chart.defaults.color = '#94a3b8';
    Chart.defaults.borderColor = '#334155';

    var typeCtx = document.getElementById('typeChart').getContext('2d');
    new Chart(typeCtx, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode(array_map(function($t) { return isset($t['type']) ? $t['type'] : 'Unknown'; }, $eventsByType)) ?>,
            datasets: [{
                data: <?= json_encode(array_map(function($t) { return isset($t['count']) ? (int)$t['count'] : 0; }, $eventsByType)) ?>,
                backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8B5CF6', '#E19864', '#06b6d4'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'right', labels: { color: '#94a3b8', padding: 15 } }
            }
        }
    });

    var statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'pie',
        data: {
            labels: <?= json_encode(array_map(function($s) { return isset($s['statut']) ? $s['statut'] : 'Unknown'; }, $eventsByStatus)) ?>,
            datasets: [{
                data: <?= json_encode(array_map(function($s) { return isset($s['count']) ? (int)$s['count'] : 0; }, $eventsByStatus)) ?>,
                backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8B5CF6'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'right', labels: { color: '#94a3b8', padding: 15 } }
            }
        }
    });

    var monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode(array_map(function($m) { return isset($m['month']) ? $m['month'] : ''; }, $monthlyEvents)) ?>,
            datasets: [{
                label: 'Events Created',
                data: <?= json_encode(array_map(function($m) { return isset($m['count']) ? (int)$m['count'] : 0; }, $monthlyEvents)) ?>,
                borderColor: '#E19864',
                backgroundColor: 'rgba(225, 152, 100, 0.1)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#E19864',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, ticks: { color: '#94a3b8' }, grid: { color: '#1e293b' } },
                x: { ticks: { color: '#94a3b8' }, grid: { display: false } }
            },
            plugins: { legend: { display: false } }
        }
    });
</script>
