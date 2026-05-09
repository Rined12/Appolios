<?php
$studentSidebarActive = 'ranks';
$rankIcons = ['Recruit'=>'⭐','Private'=>'⭐⭐','Corporal'=>'⭐⭐⭐','Sergeant'=>'🔱','Lieutenant'=>'🔱🔱','Captain'=>'🎖️','Major'=>'👑','General'=>'🏆'];
$rankXP = ['Recruit'=>'0+ XP','Private'=>'100+ XP','Corporal'=>'300+ XP','Sergeant'=>'600+ XP','Lieutenant'=>'1000+ XP','Captain'=>'2000+ XP','Major'=>'4000+ XP','General'=>'8000+ XP'];
?>

<div class="dashboard student-events-page">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main">
                <h1>🎖️ Military Ranks</h1>
                <p>Climb the ranks by earning XP!</p>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-top: 1.5rem;">
                    <?php foreach ($rankXP as $rank => $xp): ?>
                    <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #eef2f6; text-align: center;">
                        <div style="font-size: 2.5rem; margin-bottom: 0.5rem;"><?= $rankIcons[$rank] ?></div>
                        <h3 style="margin: 0 0 0.5rem 0; color: #1e293b; font-size: 1.2rem;"><?= $rank ?></h3>
                        <p style="margin: 0; color: #667eea; font-weight: 600;"><?= $xp ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 16px; padding: 2rem; margin-top: 2rem; color: white; text-align: center;">
                    <h2 style="margin: 0 0 1rem 0;">Your Current Rank</h2>
                    <div style="font-size: 4rem; margin-bottom: 1rem;"><?= $levelInfo['icon'] ?? '⭐' ?></div>
                    <h3 style="margin: 0; font-size: 1.8rem;"><?= $levelInfo['name'] ?? 'Recruit' ?></h3>
                    <p style="margin: 0.5rem 0 0 0; font-size: 1.1rem;">Total XP: <?= number_format($totalXP ?? 0) ?></p>
                </div>
            </div>
        </div>
    </div>
</div>