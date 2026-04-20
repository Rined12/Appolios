<?php
/**
 * APPOLIOS - Student Dashboard
 */
?>

<div class="dashboard">
    <div class="container">
        <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
            <div>
                <h1>Welcome, <?= htmlspecialchars($userName) ?>!</h1>
                <p>Continue your learning journey</p>
            </div>
            <a href="<?= APP_URL ?>/index.php?url=logout" class="btn btn-outline" style="border-color: #dc3545; color: #dc3545;">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" style="vertical-align: middle; margin-right: 5px;">
                    <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/>
                </svg>
                Logout
            </a>
        </div>

        <!-- Enrolled Courses -->
        <div class="table-container" style="margin-bottom: 30px;">
            <div class="table-header">
                <h3 style="margin: 0;">My Enrolled Courses</h3>
                <a href="<?= APP_URL ?>/index.php?url=student/my-courses" class="btn btn-outline" style="padding: 8px 16px; font-size: 0.85rem;">View All</a>
            </div>
            <?php if (!empty($enrollments)): ?>
                <div style="padding: 20px;">
                    <div class="cards-grid" style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));">
                        <?php foreach (array_slice($enrollments, 0, 3) as $enrollment): ?>
                            <div class="card">
                                <div class="card-icon" style="width: 60px; height: 60px; margin-bottom: 15px;">
                                    <svg viewBox="0 0 24 24" fill="white">
                                        <path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8 12.5v-9l6 4.5-6 4.5z"/>
                                    </svg>
                                </div>
                                <h3 style="font-size: 1.1rem;"><?= htmlspecialchars($enrollment['title']) ?></h3>
                                <p style="font-size: 0.9rem;"><?= htmlspecialchars(substr($enrollment['description'], 0, 100)) ?>...</p>
                                <div style="margin-top: 15px;">
                                    <div style="background: var(--gray-light); border-radius: 10px; height: 8px; overflow: hidden;">
                                        <div style="background: linear-gradient(90deg, var(--secondary-color), var(--primary-color)); height: 100%; width: <?= $enrollment['progress'] ?? 0 ?>%;"></div>
                                    </div>
                                    <small style="color: var(--gray-dark);">Progress: <?= $enrollment['progress'] ?? 0 ?>%</small>
                                </div>
                                <a href="<?= APP_URL ?>/index.php?url=student/course/<?= $enrollment['course_id'] ?>" class="btn btn-secondary btn-block" style="margin-top: 15px;">Continue Learning</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <div style="padding: 40px; text-align: center;">
                    <p style="color: var(--gray-dark); margin-bottom: 20px;">You haven't enrolled in any courses yet.</p>
                    <a href="<?= APP_URL ?>/index.php?url=courses" class="btn btn-primary">Browse Courses</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Available Courses -->
        <div class="table-container">
            <div class="table-header">
                <h3 style="margin: 0;">Available Courses</h3>
                <a href="<?= APP_URL ?>/index.php?url=courses" class="btn btn-outline" style="padding: 8px 16px; font-size: 0.85rem;">View All</a>
            </div>
            <div style="padding: 20px;">
                <div class="cards-grid" style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));">
                    <?php if (!empty($allCourses)): ?>
                        <?php foreach (array_slice($allCourses, 0, 3) as $course): ?>
                            <div class="card">
                                <div class="card-icon" style="width: 60px; height: 60px; margin-bottom: 15px;">
                                    <svg viewBox="0 0 24 24" fill="white">
                                        <path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8 12.5v-9l6 4.5-6 4.5z"/>
                                    </svg>
                                </div>
                                <h3 style="font-size: 1.1rem;"><?= htmlspecialchars($course['title']) ?></h3>
                                <p style="font-size: 0.9rem;"><?= htmlspecialchars(substr($course['description'], 0, 100)) ?>...</p>
                                <p style="font-size: 0.85rem; color: var(--gray-dark);">By: <?= htmlspecialchars($course['creator_name']) ?></p>
                                <a href="<?= APP_URL ?>/index.php?url=student/course/<?= $course['id'] ?>" class="btn btn-primary btn-block" style="margin-top: 15px;">View Course</a>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="color: var(--gray-dark);">No courses available yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>