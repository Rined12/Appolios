<?php
/**
 * APPOLIOS - Student Badges
 */

$studentSidebarActive = 'badges';
$userName = $_SESSION['user_name'] ?? 'Student';
?>

<div class="dashboard student-learning-page">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>

            <div class="admin-main pro-table-page">
                
                <div class="pro-table-head" style="margin-bottom: 2rem;">
                    <div>
                        <h1 style="color: #f8fafc; font-weight: 800; font-size: 1.8rem; margin-bottom: 5px;">My Badges</h1>
                        <p style="color: #94a3b8; font-size: 0.95rem;">Track your achievements and the badges you've collected throughout your learning journey.</p>
                    </div>
                </div>

                <div class="pro-dashboard-grid" style="display: grid; grid-template-columns: 1fr; gap: 2rem;">
                    <div class="pro-table-card" style="background: rgba(30, 41, 59, 0.6); backdrop-filter: blur(10px); border: 1px solid rgba(148, 163, 184, 0.15); border-radius: 16px; overflow: hidden; padding: 2rem;">
                        <h3 style="margin: 0 0 1.5rem 0; font-size: 1.3rem; color: #f8fafc; font-weight: 800;">Earned Badges</h3>
                        
                        <?php if (!empty($badges)): ?>
                            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1.5rem;">
                                <?php foreach ($badges as $badge): ?>
                                    <div style="border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 1.5rem; text-align: center; background: rgba(255,255,255,0.02); transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                                        <div style="font-size: 3rem; margin-bottom: 1rem;">
                                            <?= htmlspecialchars($badge['badge_icon'] ?? $badge['icon'] ?? '🏆') ?>
                                        </div>
                                        <h4 style="margin: 0 0 0.5rem 0; font-size: 1.1rem; font-weight: 700; color: #f8fafc;"><?= htmlspecialchars($badge['badge_name'] ?? $badge['name'] ?? 'Badge') ?></h4>
                                        <p style="margin: 0 0 1rem 0; font-size: 0.85rem; color: #94a3b8;"><?= htmlspecialchars($badge['badge_description'] ?? $badge['description'] ?? '') ?></p>
                                        <div style="font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase;">
                                            Awarded: <?= date('M d, Y', strtotime($badge['earned_at'] ?? $badge['awarded_at'] ?? time())) ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div style="text-align: center; padding: 3rem 1rem; background: rgba(255,255,255,0.02); border: 2px dashed rgba(255,255,255,0.1); border-radius: 12px;">
                                <p style="color: #94a3b8; margin: 0 0 1rem 0; font-size: 1rem;">You have not earned any badges yet.</p>
                                <p style="color: #64748b; font-size: 0.9rem;">Complete courses and quizzes to earn badges!</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
