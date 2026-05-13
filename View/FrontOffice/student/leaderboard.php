<?php
$studentSidebarActive = 'leaderboard';
?>
<div class="dashboard student-events-page">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main" style="grid-template-rows: min-content; align-items: start; height: auto; align-content: start;">
                <h1 style="margin: 0 0 0.5rem 0; font-size: 24px;">Leaderboard</h1>
                <p style="color: #64748b; margin: 0 0 1.5rem 0;">Top students by XP</p>

                <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 2rem; margin-top: 2rem; align-items: flex-end;">
                    <?php $rank = 1; foreach ($leaderboard as $student): ?>
                        <?php 
                        $isTop3 = $rank <= 3;
                        $bg = 'white';
                        $icon = '';
                        $size = '220px';
                        $textColor = '#1e293b';
                        $xpColor = '#64748b';
                        
                        if ($rank === 1) {
                            $bg = 'linear-gradient(135deg, #FFD700 0%, #FDB931 100%)';
                            $icon = '🏆 1st';
                            $size = '260px';
                            $textColor = '#fff';
                            $xpColor = 'rgba(255,255,255,0.9)';
                        } elseif ($rank === 2) {
                            $bg = 'linear-gradient(135deg, #E2E2E2 0%, #C9D6FF 100%)';
                            $icon = '🥈 2nd';
                            $size = '240px';
                        } elseif ($rank === 3) {
                            $bg = 'linear-gradient(135deg, #F3A183 0%, #EC6F66 100%)';
                            $icon = '🥉 3rd';
                            $size = '230px';
                            $textColor = '#fff';
                            $xpColor = 'rgba(255,255,255,0.9)';
                        }
                        ?>
                        <div style="width: <?= $size ?>; background: <?= $bg ?>; border-radius: 16px; padding: 2rem 1.5rem; display: flex; flex-direction: column; align-items: center; gap: 1rem; box-shadow: 0 10px 25px rgba(0,0,0,0.1); position: relative; <?= $rank === 1 ? 'z-index: 10;' : '' ?>">
                            <?php if ($isTop3): ?>
                                <div style="position: absolute; top: -15px; background: white; padding: 6px 16px; border-radius: 20px; font-weight: 800; font-size: 1.1rem; box-shadow: 0 4px 10px rgba(0,0,0,0.15); color: #1e293b;"><?= $icon ?></div>
                            <?php else: ?>
                                <div style="position: absolute; top: 15px; left: 15px; font-weight: 800; color: #94a3b8; font-size: 1.2rem;">#<?= $rank ?></div>
                            <?php endif; ?>
                            
                            <div style="width: 70px; height: 70px; border-radius: 50%; background: <?= $isTop3 ? 'rgba(255,255,255,0.3)' : '#f1f5f9' ?>; color: <?= $textColor ?>; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 2rem; border: 2px solid <?= $isTop3 ? 'rgba(255,255,255,0.5)' : '#e2e8f0' ?>;">
                                <?= strtoupper(substr($student['name'], 0, 1)) ?>
                            </div>
                            
                            <div style="text-align: center;">
                                <div style="font-weight: 700; color: <?= $textColor ?>; font-size: 1.2rem; margin-bottom: 0.25rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 180px;"><?= htmlspecialchars($student['name']) ?></div>
                                <div style="font-size: 1.1rem; font-weight: 800; color: <?= $xpColor ?>;">
                                    <?= number_format((int) ($student['xp'] ?? 0)) ?> XP
                                </div>
                            </div>
                            
                            <?php if ($student['id'] == $_SESSION['user_id']): ?>
                                <span style="background: <?= $isTop3 ? 'rgba(255,255,255,0.25)' : '#dcfce7' ?>; color: <?= $isTop3 ? '#fff' : '#16a34a' ?>; padding: 4px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 700; border: 1px solid <?= $isTop3 ? 'rgba(255,255,255,0.5)' : '#bbf7d0' ?>;">You</span>
                            <?php endif; ?>
                        </div>
                    <?php $rank++; endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>