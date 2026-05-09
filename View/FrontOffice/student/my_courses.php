<?php
/**
 * APPOLIOS - Student My Courses Page
 */

$studentSidebarActive = 'my-courses';
?>

<div class="dashboard student-events-page">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>

            <div class="admin-main">
                <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                    <div>
                        <h1>My Courses</h1>
                        <p>Track your enrolled courses and progress</p>
                    </div>
                    <a href="<?= APP_ENTRY ?>?url=student/courses" class="btn btn-primary">Browse More Courses</a>
                </div>

        <?php if (!empty($enrollments)): ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem;">
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

                            <!-- Progress Bar -->
                            <div style="margin-bottom: 1rem;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                    <span style="font-size: 0.85rem; color: #64748b;">Progress</span>
                                    <span style="font-size: 0.85rem; font-weight: 600;"><?= $enrollment['progress'] ?? 0 ?>%</span>
                                </div>
                                <div style="background: #e5e7eb; border-radius: 10px; height: 10px; overflow: hidden;">
                                    <div style="background: linear-gradient(90deg, #667eea, #764ba2); height: 100%; width: <?= $enrollment['progress'] ?? 0 ?>%; transition: width 0.3s ease;"></div>
                                </div>
                            </div>

                            <div style="font-size: 0.85rem; color: #64748b; margin-bottom: 1rem;">
                                Enrolled: <?= date('M d, Y', strtotime($enrollment['enrolled_at'])) ?>
                            </div>

                            <a href="<?= APP_ENTRY ?>?url=student/course/<?= $enrollment['course_id'] ?>" style="background: #10b981; color: white; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-weight: 600; display: block; text-align: center;">
                                Continue Learning
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 3rem; background: white; border-radius: 12px;">
                <h3 style="color: #2B4865;">No Courses Yet</h3>
                <p style="color: #64748b;">You haven't enrolled in any courses. Start exploring our course catalog!</p>
                <a href="<?= APP_ENTRY ?>?url=student/courses" style="background: #2B4865; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 600;">Browse Courses</a>
            </div>
        <?php endif; ?>
        

            </div>
        </div>
    </div>
</div>