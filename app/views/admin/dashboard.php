<?php
/**
 * APPOLIOS - Admin Dashboard
 */
$adminSidebarActive = 'espace';
?>

<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>

            <div class="admin-main">
                <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                    <div>
                        <h1>Admin Dashboard</h1>
                        <p>Welcome back, <?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?>!</p>
                    </div>
                    <a href="<?= APP_URL ?>/index.php?url=logout" class="btn btn-outline" style="border-color: #dc3545; color: #dc3545;">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" style="vertical-align: middle; margin-right: 5px;">
                            <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/>
                        </svg>
                        Logout
                    </a>
                </div>

                <!-- Statistics Cards -->
                <div class="dashboard-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <svg viewBox="0 0 24 24" fill="white">
                                <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                            </svg>
                        </div>
                        <div class="stat-info">
                            <h3><?= $totalUsers ?? 0 ?></h3>
                            <p>Total Users</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <svg viewBox="0 0 24 24" fill="white">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>
                        </div>
                        <div class="stat-info">
                            <h3><?= $totalStudents ?? 0 ?></h3>
                            <p>Students</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <svg viewBox="0 0 24 24" fill="white">
                                <path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-1 9H9V9h10v2zm-4 4H9v-2h6v2zm4-8H9V5h10v2z"/>
                            </svg>
                        </div>
                        <div class="stat-info">
                            <h3><?= $totalCourses ?? 0 ?></h3>
                            <p>Total Courses</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <svg viewBox="0 0 24 24" fill="white">
                                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                            </svg>
                        </div>
                        <div class="stat-info">
                            <h3><?= $totalEnrollments ?? 0 ?></h3>
                            <p>Enrollments</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <svg viewBox="0 0 24 24" fill="white">
                                <path d="M19 4h-1V2h-2v2H8V2H6v2H5C3.9 4 3 4.9 3 6v13c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 15H5V10h14v9z"/>
                            </svg>
                        </div>
                        <div class="stat-info">
                            <h3><?= $totalEvenements ?? 0 ?></h3>
                            <p>Evenements</p>
                        </div>
                    </div>
                </div>

                <div class="module-card" style="margin-bottom: 30px;">
                    <div>
                        <h3 style="margin-bottom: 8px;">Module 01: Evenement</h3>
                        <p style="margin-bottom: 0;">Manage upcoming events, schedule dates, and centralize event info.</p>
                    </div>
                    <a href="<?= APP_URL ?>/index.php?url=admin/evenements" class="btn btn-yellow">Open Module</a>
                </div>

                <!-- Quick Actions -->
                <div class="table-container" style="margin-bottom: 30px;">
                    <div class="table-header">
                        <h3 style="margin: 0;">Quick Actions</h3>
                    </div>
                    <div style="padding: 20px; display: flex; gap: 15px; flex-wrap: wrap;">
                        <a href="<?= APP_URL ?>/index.php?url=admin/add-evenement" class="btn btn-yellow">Add Evenement</a>
                        <a href="<?= APP_URL ?>/index.php?url=admin/add-course" class="btn btn-yellow">Add New Course</a>
                        <a href="<?= APP_URL ?>/index.php?url=admin/courses" class="btn btn-secondary">Manage Courses</a>
                        <a href="<?= APP_URL ?>/index.php?url=admin/users" class="btn btn-primary">Manage Users</a>
                        <a href="<?= APP_URL ?>/index.php?url=admin/teachers" class="btn btn-primary" style="background: var(--yellow); color: var(--primary-color);">Manage Teachers</a>
                    </div>
                </div>

                <div class="table-container" style="margin-bottom: 30px;">
                    <div class="table-header">
                        <h3 style="margin: 0;">Recent Evenements</h3>
                        <a href="<?= APP_URL ?>/index.php?url=admin/evenements" class="btn btn-outline" style="padding: 8px 16px; font-size: 0.85rem;">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Date</th>
                                    <th>Location</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($recentEvenements)): ?>
                                    <?php foreach ($recentEvenements as $evenement): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($evenement['title']) ?></td>
                                            <td><?= date('M d, Y H:i', strtotime($evenement['event_date'])) ?></td>
                                            <td><?= htmlspecialchars($evenement['location'] ?: 'TBA') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" style="text-align: center; padding: 30px;">No events yet. <a href="<?= APP_URL ?>/index.php?url=admin/add-evenement">Create your first evenement</a></td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recent Courses -->
                <div class="table-container">
                    <div class="table-header">
                        <h3 style="margin: 0;">Recent Courses</h3>
                        <a href="<?= APP_URL ?>/index.php?url=admin/courses" class="btn btn-outline" style="padding: 8px 16px; font-size: 0.85rem;">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Created By</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($recentCourses)): ?>
                                    <?php foreach (array_slice($recentCourses, 0, 5) as $course): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($course['id']) ?></td>
                                            <td><?= htmlspecialchars($course['title']) ?></td>
                                            <td><?= htmlspecialchars($course['creator_name']) ?></td>
                                            <td><?= date('M d, Y', strtotime($course['created_at'])) ?></td>
                                            <td>
                                                <a href="<?= APP_URL ?>/index.php?url=admin/edit-course/<?= $course['id'] ?>" class="btn btn-secondary action-btn" style="padding: 5px 10px; font-size: 0.8rem;">Edit</a>
                                                <a href="<?= APP_URL ?>/index.php?url=admin/delete-course/<?= $course['id'] ?>" class="btn action-btn danger" style="padding: 5px 10px; font-size: 0.8rem;" data-confirm="Are you sure you want to delete this course?">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" style="text-align: center; padding: 30px;">No courses found. <a href="<?= APP_URL ?>/index.php?url=admin/add-course">Add your first course</a></td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>