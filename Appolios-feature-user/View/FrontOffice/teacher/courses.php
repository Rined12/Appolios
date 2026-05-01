<?php
/**
 * APPOLIOS - Teacher Courses Management
 */

$teacherSidebarActive = 'courses';
?>

<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main">
                <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h1>My Courses</h1>
                        <p>Manage your courses</p>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <a href="<?= APP_ENTRY ?>?url=teacher/evenements" class="btn btn-outline">Evenements</a>
                        <a href="<?= APP_ENTRY ?>?url=teacher/add-course" class="btn btn-yellow">Add New Course</a>
                    </div>
                </div>

                <div class="table-container">
                    <div class="table-header">
                        <h3 style="margin: 0;">All My Courses</h3>
                    </div>
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Type</th>
                                        <th>Content</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($courses)): ?>
                                        <?php foreach ($courses as $course): ?>
                                            <tr>
                                                <td style="font-weight: 600;"><?= htmlspecialchars($course['title']) ?></td>
                                                <td>
                                                    <?php if (!empty($course['course_type'])): ?>
                                                        <span style="background: #e0f2f1; color: #2B4865; padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 600;"><?= htmlspecialchars($course['course_type']) ?></span>
                                                    <?php else: ?>
                                                        <span style="color: #94a3b8; font-size: 0.85rem;">Not set</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($course['chapters'])): ?>
                                                        <span style="color: #64748b;"><?= count($course['chapters']) ?> chapters</span>
                                                    <?php else: ?>
                                                        <span style="color: #94a3b8;">No content</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                            <td>
                                                <?php $status = $course['status'] ?? 'pending'; ?>
                                                <span style="padding: 4px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 600; background: <?= $status === 'approved' ? '#dcfce7' : ($status === 'rejected' ? '#fee2e2' : '#fef3c7') ?>; color: <?= $status === 'approved' ? '#16a34a' : ($status === 'rejected' ? '#dc2626' : '#d97706') ?>;">
                                                    <?= ucfirst($status) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="<?= APP_ENTRY ?>?url=teacher/course/<?= $course['id'] ?>" class="btn btn-secondary action-btn" style="padding: 5px 10px; font-size: 0.8rem;">View</a>
                                                <a href="<?= APP_ENTRY ?>?url=teacher/edit-course/<?= $course['id'] ?>" class="btn btn-primary action-btn" style="padding: 5px 10px; font-size: 0.8rem;">Edit</a>
                                                <a href="<?= APP_ENTRY ?>?url=teacher/delete-course/<?= $course['id'] ?>" class="btn action-btn danger" style="padding: 5px 10px; font-size: 0.8rem;" onclick="return confirm('Are you sure you want to delete this course?')">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" style="text-align: center; padding: 30px;">No courses found. <a href="<?= APP_ENTRY ?>?url=teacher/add-course">Create your first course</a></td>
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