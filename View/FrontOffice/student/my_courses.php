<?php
/**
 * APPOLIOS - Student My Courses Page
 */

$studentSidebarActive = 'my-courses';
?>

<div class="dashboard student-courses-page">
    <div class="container admin-dashboard-container" style="max-width: 1400px; width: 100%;">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>

            <div class="admin-main" style="background: transparent; padding: 1rem 0 2rem 0;">
                <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h1>My Courses</h1>
                        <p>Track your enrolled courses and progress</p>
                    </div>
                    <a href="<?= APP_ENTRY ?>?url=courses" class="btn btn-primary">Browse More Courses</a>
                </div>

        <?php if (!empty($enrollments)): ?>
            <div class="cards-grid" style="grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));">
                <?php foreach ($enrollments as $enrollment): ?>
                    <div class="card">
                        <div class="card-icon" style="width: 60px; height: 60px; margin-bottom: 15px;">
                            <svg viewBox="0 0 24 24" fill="white">
                                <path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8 12.5v-9l6 4.5-6 4.5z"/>
                            </svg>
                        </div>
                        <h3 style="font-size: 1.2rem;"><?= htmlspecialchars($enrollment['title']) ?></h3>
                        <p style="font-size: 0.9rem; color: var(--gray-dark);"><?= htmlspecialchars(substr($enrollment['description'], 0, 120)) ?>...</p>

                        <!-- Progress Bar -->
                        <div style="margin-top: 20px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                <span style="font-size: 0.85rem; color: var(--gray-dark);">Progress</span>
                                <span style="font-size: 0.85rem; font-weight: 600;"><?= $enrollment['progress'] ?? 0 ?>%</span>
                            </div>
                            <div style="background: var(--gray-light); border-radius: 10px; height: 10px; overflow: hidden;">
                                <div style="background: linear-gradient(90deg, var(--secondary-color), var(--primary-color)); height: 100%; width: <?= $enrollment['progress'] ?? 0 ?>%; transition: width 0.3s ease;"></div>
                            </div>
                        </div>

                        <div style="margin-top: 15px; font-size: 0.85rem; color: var(--gray-dark);">
                            Enrolled: <?= date('M d, Y', strtotime($enrollment['enrolled_at'])) ?>
                        </div>

                        <a href="<?= APP_ENTRY ?>?url=student/course/<?= $enrollment['course_id'] ?>" class="btn btn-primary btn-block" style="margin-top: 20px;">
                            Continue Learning
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="table-container">
                <div style="padding: 60px; text-align: center;">
                    <svg viewBox="0 0 24 24" width="80" height="80" fill="var(--secondary-color)" style="opacity: 0.5;">
                        <path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8 12.5v-9l6 4.5-6 4.5z"/>
                    </svg>
                    <h3 style="margin-top: 20px; color: var(--primary-color);">No Courses Yet</h3>
                    <p style="color: var(--gray-dark); margin: 15px 0 25px;">You haven't enrolled in any courses. Start exploring our course catalog!</p>
                    <a href="<?= APP_ENTRY ?>?url=courses" class="btn btn-yellow">Browse Courses</a>
                </div>
            </div>
        <?php endif; ?>
            </div>
        </div>
    </div>
</div>