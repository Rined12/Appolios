<?php
/**
 * APPOLIOS - Admin Courses Management
 */
$adminSidebarActive = 'courses';
?>

<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main">
                <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h1>Manage Courses</h1>
                        <p>Add, edit, or delete courses from the platform</p>
                    </div>
                    <a href="<?= APP_ENTRY ?>?url=admin/add-course" class="btn btn-yellow">Add New Course</a>
                </div>

                <div class="table-container">
                    <div class="table-header">
                        <h3 style="margin: 0;">All Courses</h3>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Created By</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($courses)): ?>
                                    <?php foreach ($courses as $course): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($course['id']) ?></td>
                                            <td><?= htmlspecialchars($course['title']) ?></td>
                                            <td><?= htmlspecialchars(substr($course['description'], 0, 80)) ?>...</td>
                                            <td><?= htmlspecialchars($course['creator_name']) ?></td>
                                            <td><?= date('M d, Y', strtotime($course['created_at'])) ?></td>
                                            <td>
                                                <a href="<?= APP_ENTRY ?>?url=admin/edit-course/<?= $course['id'] ?>" class="btn btn-secondary action-btn" style="padding: 5px 10px; font-size: 0.8rem;">Edit</a>
                                                <a href="<?= APP_ENTRY ?>?url=admin/delete-course/<?= $course['id'] ?>" class="btn action-btn danger" style="padding: 5px 10px; font-size: 0.8rem;" onclick="return confirm('Are you sure you want to delete this course?')">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" style="text-align: center; padding: 30px;">No courses found. <a href="<?= APP_ENTRY ?>?url=admin/add-course">Add your first course</a></td>
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