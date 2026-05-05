<?php
/**
 * APPOLIOS - Student Leaderboard Page
 */
$studentSidebarActive = 'leaderboard';
?>

<style>
.leaderboard-page .admin-layout { gap: 5px !important; }
.leaderboard-page .admin-main { gap: 5px !important; display: block !important; }
.leaderboard-page h1 { margin-bottom: 5px !important; }
.leaderboard-page p { margin-bottom: 10px !important; }
</style>

<div class="dashboard student-events-page leaderboard-page">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <div>
                        <h1>🏆 Leaderboard</h1>
                        <p>Top students with the most XP!</p>
                    </div>
                    <button onclick="showRanksModal()" style="background: #667eea; color: white; padding: 10px 20px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">View All Ranks</button>
                </div>
                <script>
                function showRanksModal() {
                    Swal.fire({
                        title: '🎖️ Military Ranks',
                        html: `<div style="text-align:left;max-height:400px;overflow-y:auto;">
                            <table style="width:100%;border-collapse:collapse;">
                                <tr style="background:#f1f5f9;"><th style="padding:8px;">Rank</th><th style="padding:8px;">XP Required</th></tr>
                                <tr><td style="padding:8px;">⭐ Recruit</td><td style="padding:8px;">0+ XP</td></tr>
                                <tr style="background:#f8fafc"><td style="padding:8px;">⭐⭐ Private</td><td style="padding:8px;">100+ XP</td></tr>
                                <tr><td style="padding:8px;">⭐⭐⭐ Corporal</td><td style="padding:8px;">300+ XP</td></tr>
                                <tr style="background:#f8fafc"><td style="padding:8px;">🔱 Sergeant</td><td style="padding:8px;">600+ XP</td></tr>
                                <tr><td style="padding:8px;">🔱🔱 Lieutenant</td><td style="padding:8px;">1000+ XP</td></tr>
                                <tr style="background:#f8fafc"><td style="padding:8px;">🎖️ Captain</td><td style="padding:8px;">2000+ XP</td></tr>
                                <tr><td style="padding:8px;">👑 Major</td><td style="padding:8px;">4000+ XP</td></tr>
                                <tr style="background:#f8fafc"><td style="padding:8px;">🏆 General</td><td style="padding:8px;">8000+ XP</td></tr>
                            </table>
                        </div>`,
                        width: '400px',
                        confirmButtonColor: '#667eea'
                    });
                }
                </script>
                <div style="background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #eef2f6;">
                    <div style="display: grid; grid-template-columns: 50px 1fr 100px 150px; padding: 1rem 1.5rem; background: #667eea; color: white; font-weight: 700;">
                        <div>#</div>
                        <div>Student</div>
                        <div>XP</div>
                        <div>Level</div>
                    </div>
<?php 
                    $rank = 1;
                    if (empty($leaderboard)) {
                        $leaderboard = [
                            ['name' => 'John Student', 'xp' => 505],
                            ['name' => 'Sarah Williams', 'xp' => 8500],
                        ];
                    }
                    foreach ($leaderboard as $user) {
                        $xp = intval($user['xp'] ?? 0);
                        $bg = ($user['name'] ?? '') === ($userName ?? '') ? '#fef3c7' : ($rank <= 3 ? '#f8fafc' : 'white');
                        $border = $rank === 1 ? '2px solid #fbbf24' : ($rank === 2 ? '2px solid #94a3b8' : ($rank === 3 ? '2px solid #cd7f32' : 'none'));
                    ?>
                    <div style="display: grid; grid-template-columns: 50px 1fr 100px 150px; padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9; background: <?= $bg ?>; align-items: center; border-left: <?= $border ?>;">
                        <div style="font-size: 1.5rem;">
                            <?php if ($rank === 1) echo '🥇'; elseif ($rank === 2) echo '🥈'; elseif ($rank === 3) echo '🥉'; else echo $rank; ?>
                        </div>
                        <div style="font-weight: 600; color: #1e293b;"><?= htmlspecialchars($user['name']) ?></div>
                        <div style="font-weight: 700; color: #667eea;"><?= number_format($xp) ?> XP</div>
                        <div style="font-size: 0.9rem; font-weight: 600; color: #1e293b;">
                            <?php 
                            if ($xp >= 8000) echo '🏆 General';
                            elseif ($xp >= 4000) echo '👑 Major';
                            elseif ($xp >= 2000) echo '🎖️ Captain';
                            elseif ($xp >= 1000) echo '🔱 Lieutenant';
                            elseif ($xp >= 500) echo '⭐⭐ Sergeant';
                            elseif ($xp >= 300) echo '⭐ Corporal';
                            elseif ($xp >= 100) echo '✨ Private';
                            else echo '🎯 Recruit';
                            ?>
                        </div>
                    </div>
                    <?php $rank++; } ?>
                </div>
            </div>
        </div>
    </div>
</div>