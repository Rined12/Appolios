<?php
/**
 * APPOLIOS - Student Badges Page
 */

$studentSidebarActive = 'badges';

require_once 'C:/xampp/htdocs/Appolios-feature-user/Appolios-feature-user/Model/Badge.php';
$badgeModel = new Badge();
$badges = $badgeModel->getByUserId($_SESSION['user_id']);
$iconMap = [
    'trophy' => '🏆',
    'star' => '⭐',
    'medal' => '🎖️',
    'certificate' => '📜',
    'rocket' => '🚀',
    'fire' => '🔥',
    'check' => '✅',
    'crown' => '👑',
    'gem' => '💎',
    'lightning' => '⚡'
];
?>

<style>
.badges-page .admin-layout { gap: 5px !important; }
.badges-page .admin-main { gap: 5px !important; display: block !important; }
.badges-page h1 { margin-bottom: 5px !important; }
.badges-page p { margin-bottom: 10px !important; }
.badges-page .card { min-height: 140px; }
</style>

<div class="dashboard student-events-page badges-page">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>

            <div class="admin-main">
                <h1>My Badges</h1>
                <p>Earn badges by completing lessons and courses</p>

                <?php if (!empty($badges)): ?>
                    <div class="cards-grid">
                        <?php foreach ($badges as $badge): ?>
                            <div class="card">
                                <div style="font-size: 2rem; text-align: center;"><?= $badge['badge_icon'] ?? '🏆' ?></div>
                                <h3 style="text-align: center; margin: 0.5rem 0;"><?= htmlspecialchars($badge['badge_name']) ?></h3>
                                <p style="text-align: center; color: #64748b;"><?= htmlspecialchars($badge['badge_description'] ?? '') ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="card" style="text-align: center; padding: 2rem;">
                        <div style="font-size: 3rem;">🏆</div>
                        <h3>No Badges Yet</h3>
                        <p>Complete lessons and courses to earn badges!</p>
                        <a href="<?= APP_ENTRY ?>?url=student/courses" class="btn btn-primary">Browse Courses</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>