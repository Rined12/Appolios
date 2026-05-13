<?php
/**
 * APPOLIOS - Student My Courses Page
 */

$studentSidebarActive = 'my-courses';
?>

<div class="dashboard student-events-page fade-in">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>

            <div class="admin-main" style="grid-template-rows: min-content; align-items: start; height: auto; align-content: start;">
                <h1 style="margin: 0 0 0.5rem 0; font-size: 24px; line-height: 1.1;">My Courses</h1>
                <p style="color: #64748b; margin: 0; font-size: 0.85rem;">
                    <?= count($enrollments) ?> courses enrolled
                    <?php if (!empty($recommendations)): ?>
                        <span style="margin-left: 10px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 2px 10px; border-radius: 12px; font-size: 0.75rem;">
                            🤖 <?= count($recommendations) ?> Recommended
                        </span>
                    <?php endif; ?>
                </p>

                <?php if (!empty($enrollments)): ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem; margin-top: 1rem;">
                <?php foreach ($enrollments as $enrollment): ?>
                    <article style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                        <?php if (!empty($enrollment['image'])): ?>
                            <img src="<?= htmlspecialchars($enrollment['image']) ?>" alt="<?= htmlspecialchars($enrollment['title']) ?>" style="width: 100%; height: 140px; object-fit: cover;">
                        <?php else: ?>
                            <?php $randomImgId = rand(1, 1000); ?>
                            <img src="https://picsum.photos/seed/<?= $randomImgId ?>/400/140" alt="<?= htmlspecialchars($enrollment['title']) ?>" style="width: 100%; height: 140px; object-fit: cover;">
                        <?php endif; ?>
                        <div style="padding: 1rem;">
                            <h3 style="margin: 0 0 0.5rem 0; font-size: 1rem; color: #1e293b;"><?= htmlspecialchars($enrollment['title']) ?></h3>
                            <p style="margin: 0 0 1rem 0; color: #64748b; font-size: 0.85rem;">
                                <?= htmlspecialchars(mb_strimwidth($enrollment['description'] ?? '', 0, 60, '...')) ?>
                            </p>
                            <div style="margin: 0.5rem 0;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                                    <span style="font-size: 0.8rem; font-weight: 600; color: #64748b;">Progress</span>
                                    <span style="font-size: 0.8rem; font-weight: 700; color: #3b82f6;"><?= (int) ($enrollment['progress'] ?? 0) ?>%</span>
                                </div>
                                <div style="width: 100%; background: #e2e8f0; border-radius: 99px; height: 6px; overflow: hidden;">
                                    <div style="height: 100%; background: linear-gradient(90deg, #3b82f6, #60a5fa); width: <?= (int) ($enrollment['progress'] ?? 0) ?>%;"></div>
                                </div>
                            </div>
                            <a href="<?= APP_ENTRY ?>?url=student/course/<?= $enrollment['course_id'] ?>" style="background: #10b981; color: white; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-weight: 600; display: block; text-align: center; margin-top: 1rem;">Continue Learning</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 3rem; background: white; border-radius: 12px;">
                <svg viewBox="0 0 24 24" width="80" height="80" fill="#548CA8" style="opacity: 0.5;">
                    <path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8 12.5v-9l6 4.5-6 4.5z"/>
                </svg>
                <h3 style="margin-top: 20px; color: #548CA8;">No Courses Yet</h3>
                <p style="color: #64748b; margin: 15px 0 25px;">You haven't enrolled in any courses. Start exploring!</p>
                <a href="<?= APP_ENTRY ?>?url=courses" style="background: #548CA8; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600;">Browse Courses</a>
            </div>
        <?php endif; ?>
            </div>
        </div>
    </div>
</div>