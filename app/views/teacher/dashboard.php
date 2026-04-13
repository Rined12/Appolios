<?php
/**
 * APPOLIOS - Teacher Dashboard
 */

$teacherSidebarActive = 'espace';
?>

<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main">
        <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
            <div>
                <h1>Welcome, <?= htmlspecialchars($userName) ?>!</h1>
                <p>Manage your courses and students</p>
            </div>
            <a href="<?= APP_URL ?>/index.php?url=logout" class="btn btn-outline" style="border-color: #dc3545; color: #dc3545;">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" style="vertical-align: middle; margin-right: 5px;">
                    <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/>
                </svg>
                Logout
            </a>
        </div>

        <!-- Stats Cards -->
        <div class="cards-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-bottom: 30px;">
            <div class="card" style="text-align: center;">
                <div class="card-icon" style="width: 50px; height: 50px; margin: 0 auto 15px;">
                    <svg viewBox="0 0 24 24" fill="white">
                        <path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/>
                    </svg>
                </div>
                <h3 style="font-size: 2rem; color: var(--primary-color);"><?= $stats['total_courses'] ?></h3>
                <p style="color: var(--gray-dark);">My Courses</p>
            </div>

            <div class="card" style="text-align: center;">
                <div class="card-icon" style="width: 50px; height: 50px; margin: 0 auto 15px;">
                    <svg viewBox="0 0 24 24" fill="white">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                </div>
                <h3 style="font-size: 2rem; color: var(--primary-color);"><?= $stats['total_students'] ?></h3>
                <p style="color: var(--gray-dark);">My Students</p>
            </div>

            <div class="card" style="text-align: center;">
                <div class="card-icon" style="width: 50px; height: 50px; margin: 0 auto 15px;">
                    <svg viewBox="0 0 24 24" fill="white">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                    </svg>
                </div>
                <h3 style="font-size: 2rem; color: var(--primary-color);"><?= $stats['active_enrollments'] ?></h3>
                <p style="color: var(--gray-dark);">Active Enrollments</p>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="table-container" style="margin-bottom: 30px;">
            <div class="table-header">
                <h3 style="margin: 0;">Quick Actions</h3>
            </div>
            <div style="padding: 20px; display: flex; gap: 15px; flex-wrap: wrap;">
                <a href="<?= APP_URL ?>/index.php?url=teacher/add-course" class="btn btn-yellow">Add New Course</a>
                <a href="<?= APP_URL ?>/index.php?url=teacher/courses" class="btn btn-secondary">View My Courses</a>
                <a href="<?= APP_URL ?>/index.php?url=teacher/evenements" class="btn btn-primary">Manage Evenements</a>
                <a href="<?= APP_URL ?>/index.php?url=teacher/profile" class="btn btn-primary">My Profile</a>
            </div>
        </div>

        <!-- My Courses -->
        <div class="table-container">
            <div class="table-header">
                <h3 style="margin: 0;">My Recent Courses</h3>
                <a href="<?= APP_URL ?>/index.php?url=teacher/courses" class="btn btn-outline" style="padding: 8px 16px; font-size: 0.85rem;">View All</a>
            </div>
            <div style="padding: 20px;">
                <?php if (!empty($courses)): ?>
                    <div class="cards-grid" style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));">
                        <?php foreach (array_slice($courses, 0, 3) as $course): ?>
                            <div class="card">
                                <div class="card-icon" style="width: 60px; height: 60px; margin-bottom: 15px;">
                                    <svg viewBox="0 0 24 24" fill="white">
                                        <path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/>
                                    </svg>
                                </div>
                                <h3 style="font-size: 1.1rem;"><?= htmlspecialchars($course['title']) ?></h3>
                                <p style="font-size: 0.9rem;"><?= htmlspecialchars(substr($course['description'], 0, 100)) ?>...</p>
                                <p style="font-size: 0.85rem; color: var(--gray-dark);">Created: <?= date('M d, Y', strtotime($course['created_at'])) ?></p>
                                <div style="display: flex; gap: 10px; margin-top: 15px;">
                                    <a href="<?= APP_URL ?>/index.php?url=teacher/course/<?= $course['id'] ?>" class="btn btn-secondary" style="flex: 1; padding: 8px;">View</a>
                                    <a href="<?= APP_URL ?>/index.php?url=teacher/edit-course/<?= $course['id'] ?>" class="btn btn-primary" style="flex: 1; padding: 8px;">Edit</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 40px;">
                        <p style="color: var(--gray-dark); margin-bottom: 20px;">You haven't created any courses yet.</p>
                        <a href="<?= APP_URL ?>/index.php?url=teacher/add-course" class="btn btn-yellow">Create Your First Course</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
            </div>
        </div>
    </div>
</div>
