<?php
/**
 * APPOLIOS - Admin Statistics (Neo Admin Pro)
 * User & ban analytics only — event stats are on the Stat Événements page
 */
?>

<!-- ── Page Title ─────────────────────────────────────────────────────────── -->
<div style="margin-bottom:2.5rem;">
    <h1 style="font-size:1.8rem; font-weight:800; color:#1e293b; margin:0 0 .4rem 0;">Statistiques Utilisateurs</h1>
    <p style="color:#64748b; margin:0;">Aperçu de la croissance, des sanctions et de la répartition des utilisateurs.</p>
</div>

<!-- ── KPI Row ────────────────────────────────────────────────────────────── -->
<div class="stats-grid-pro" style="margin-bottom:2rem;">

    <div class="stat-card-pro">
        <div class="stat-icon-pro" style="background:#f0fdf4;color:#15803d;"><i class="bi bi-person-check-fill"></i></div>
        <div><div class="stat-label">Étudiants</div><div class="stat-value"><?= $totalStudents ?? 0 ?></div></div>
    </div>

    <div class="stat-card-pro">
        <div class="stat-icon-pro" style="background:#fef3c7;color:#b45309;"><i class="bi bi-mortarboard-fill"></i></div>
        <div><div class="stat-label">Enseignants</div><div class="stat-value"><?= $totalTeachers ?? 0 ?></div></div>
    </div>

    <div class="stat-card-pro">
        <div class="stat-icon-pro" style="background:#fee2e2;color:#b91c1c;"><i class="bi bi-shield-lock-fill"></i></div>
        <div>
            <div class="stat-label">Sanctions Actives</div>
            <div class="stat-value"><?= ($stats['ban_perm'] ?? 0) + ($stats['ban_10h'] ?? 0) + ($stats['ban_1d'] ?? 0) + ($stats['ban_2h'] ?? 0) ?></div>
        </div>
    </div>

    <div class="stat-card-pro">
        <div class="stat-icon-pro" style="background:#ede9fe;color:#6d28d9;"><i class="bi bi-people-fill"></i></div>
        <div><div class="stat-label">Total Membres</div><div class="stat-value"><?= ($totalStudents ?? 0) + ($totalTeachers ?? 0) ?></div></div>
    </div>

</div>

<!-- ── Charts Row 1: Sanctions + Forecast ────────────────────────────────── -->
<div style="display:grid; grid-template-columns:1fr 1.5fr; gap:2rem; margin-bottom:2rem;">

    <!-- Sanctions Doughnut -->
    <div class="admin-card">
        <h2 class="admin-card-title">Répartition des Sanctions</h2>
        <div style="height:240px; margin-top:1rem;"><canvas id="banChart"></canvas></div>
        <div style="margin-top:1.2rem; display:flex; flex-direction:column; gap:8px;">
            <?php foreach ([
                ['Permanent', '#ef4444', $stats['ban_perm'] ?? 0],
                ['10 Heures', '#f59e0b', $stats['ban_10h'] ?? 0],
                ['1 Jour',    '#3b82f6', $stats['ban_1d']  ?? 0],
                ['2 Heures',  '#10b981', $stats['ban_2h']  ?? 0],
            ] as [$label, $color, $val]): ?>
            <div style="display:flex; justify-content:space-between; align-items:center; font-size:.88rem;">
                <span style="color:#64748b;">
                    <span style="display:inline-block; width:9px; height:9px; border-radius:50%; background:<?= $color ?>; margin-right:7px;"></span>
                    <?= $label ?>
                </span>
                <span style="font-weight:700; color:#1e293b;"><?= $val ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Forecast Line -->
    <div class="admin-card" style="background:var(--admin-active-gradient); color:white; border:none;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; flex-wrap:wrap; gap:.5rem;">
            <div>
                <h2 class="admin-card-title" style="color:white;">Prévisions de Croissance</h2>
                <p style="font-size:.83rem; opacity:.8; margin:0;">Estimations pour les 7 prochains jours</p>
            </div>
            <span class="admin-badge" style="background:rgba(255,255,255,.2); color:white;">AI Predicted</span>
        </div>
        <div style="height:280px;"><canvas id="forecastChart"></canvas></div>
    </div>

</div>

<!-- ── User Distribution ──────────────────────────────────────────────────── -->
<div class="admin-card" style="margin-bottom:2rem;">
    <h2 class="admin-card-title" style="text-align:center; margin-bottom:1.5rem;">Répartition Étudiants vs Enseignants</h2>
    <div style="height:260px;"><canvas id="userDistributionChart"></canvas></div>
</div>

<!-- ── Event Stats Button ─────────────────────────────────────────────────── -->
<div class="admin-card" style="text-align:center; padding:2.5rem; background:linear-gradient(135deg,rgba(84,140,168,.06),rgba(43,72,101,.04)); border:1px dashed rgba(84,140,168,.3);">
    <i class="bi bi-bar-chart-fill" style="font-size:3rem; color:#548CA8; display:block; margin-bottom:1rem;"></i>
    <h3 style="margin:0 0 .5rem; color:#2B4865; font-weight:700;">Statistiques des Événements</h3>
    <p style="color:#64748b; margin:0 0 1.5rem; font-size:.9rem;">Consultez les données de participation, types et performances de vos événements.</p>
    <a href="<?= APP_ENTRY ?>?url=admin/stat-evenements"
       style="display:inline-flex; align-items:center; gap:10px; background:linear-gradient(135deg,#548CA8,#2B4865);
              color:#fff; padding:12px 28px; border-radius:14px; text-decoration:none; font-weight:700;
              font-size:.95rem; box-shadow:0 6px 18px rgba(84,140,168,.35); transition:transform .2s, box-shadow .2s;"
       onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 10px 26px rgba(84,140,168,.45)'"
       onmouseout="this.style.transform='';this.style.boxShadow='0 6px 18px rgba(84,140,168,.35)'">
        <i class="bi bi-arrow-right-circle-fill"></i> Voir les Statistiques Événements
    </a>
</div>

<!-- ── Chart.js ──────────────────────────────────────────────────────────── -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#64748b';

    // 1. Sanctions Doughnut
    const banTotal = <?= ($stats['ban_perm']??0)+($stats['ban_10h']??0)+($stats['ban_1d']??0)+($stats['ban_2h']??0) ?>;
    new Chart(document.getElementById('banChart'), {
        type: 'doughnut',
        data: {
            labels: ['Permanent','10 Heures','1 Jour','2 Heures'],
            datasets: [{ 
                data: [<?= ($stats['ban_perm']??0) ?>,<?= ($stats['ban_10h']??0) ?>,<?= ($stats['ban_1d']??0) ?>,<?= ($stats['ban_2h']??0) ?>],
                backgroundColor: ['#ef4444','#f59e0b','#3b82f6','#10b981'],
                borderWidth: 0, cutout: '72%'
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${ctx.raw} utilisateur(s)` } }
            }
        }
    });

    // 2. Forecast Line
    new Chart(document.getElementById('forecastChart'), {
        type: 'line',
        data: {
            labels: [<?php foreach($forecast as $f) echo "'".$f['date']."',"; ?>],
            datasets: [
                { label:'Étudiants', data:[<?php foreach($forecast as $f) echo $f['students'].','; ?>],
                  borderColor:'#fff', backgroundColor:'rgba(255,255,255,.15)', fill:true, tension:.4,
                  pointBackgroundColor:'#fff', borderWidth:2, pointRadius:4 },
                { label:'Enseignants', data:[<?php foreach($forecast as $f) echo $f['teachers'].','; ?>],
                  borderColor:'#e19864', backgroundColor:'rgba(225,152,100,.2)', fill:true, tension:.4,
                  pointBackgroundColor:'#e19864', borderWidth:2, pointRadius:4 }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { labels: { color:'#fff', font:{ weight:'600' } } } },
            scales: {
                y: { ticks:{ color:'rgba(255,255,255,.7)' }, grid:{ color:'rgba(255,255,255,.1)' } },
                x: { ticks:{ color:'rgba(255,255,255,.7)' }, grid:{ display:false } }
            }
        }
    });

    // 3. User Distribution Bar
    new Chart(document.getElementById('userDistributionChart'), {
        type: 'bar',
        data: {
            labels: ['Étudiants','Enseignants'],
            datasets: [{ label:'Membres', data:[<?= $totalStudents??0 ?>,<?= $totalTeachers??0 ?>],
                backgroundColor:['#548CA8','#2B4865'], borderRadius:14, barThickness:60 }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend:{ display:false } },
            scales: { y:{ beginAtZero:true, grid:{ color:'#f1f5f9' } }, x:{ grid:{ display:false } } }
        }
    });
});
</script>
