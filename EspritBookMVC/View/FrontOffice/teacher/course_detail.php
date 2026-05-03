<?php
/**
 * APPOLIOS - Teacher Course Detail Page
 */

$teacherSidebarActive = 'courses';
?>

<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main">
        <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <a href="<?= APP_ENTRY ?>?url=teacher/courses" style="color: var(--secondary-color); font-size: 0.9rem;">← Back to Courses</a>
                <h1 style="margin-top: 10px;"><?= htmlspecialchars($course['title']) ?></h1>
            </div>
            <div style="display: flex; gap: 10px;">
                <a href="<?= APP_ENTRY ?>?url=teacher/edit-course/<?= $course['id'] ?>" class="btn btn-secondary">Edit Course</a>
                <a href="<?= APP_ENTRY ?>?url=teacher/delete-course/<?= $course['id'] ?>" class="btn action-btn danger" onclick="return confirm('Are you sure you want to delete this course?')">Delete</a>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; margin-top: 30px;">
            <!-- Main Content -->
            <div>
                <div class="table-container" style="margin-bottom: 30px;">
                    <div style="padding: 30px;">
                        <h2>Course Description</h2>
                        <p style="line-height: 1.8;"><?= nl2br(htmlspecialchars($course['description'])) ?></p>
                    </div>
                </div>

                <!-- Video Player -->
                <?php $course_video_payload = $course_video_payload ?? ['type' => 'none']; ?>
                <?php if (($course_video_payload['type'] ?? '') !== 'none'): ?>
                    <div class="table-container">
                        <div class="table-header">
                            <h3 style="margin: 0;">Course Video</h3>
                        </div>
                        <div style="padding: 20px;">
                            <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: var(--border-radius-sm); background: var(--primary-color);">
                                <?php if (($course_video_payload['type'] ?? '') === 'youtube'): ?>
                                    <iframe style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;" src="<?= htmlspecialchars((string) ($course_video_payload['embed_url'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" frameborder="0" allowfullscreen></iframe>
                                <?php elseif (($course_video_payload['type'] ?? '') === 'mp4'): ?>
                                    <?php $videoUrl = htmlspecialchars((string) ($course_video_payload['src'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                    <object data="<?= $videoUrl ?>" type="video/mp4" width="640" height="360">
                                        <param name="src" value="<?= $videoUrl ?>">
                                        <param name="autoplay" value="false">
                                        <param name="controls" value="true">
                                        <a href="<?= $videoUrl ?>">Download video</a>
                                    </object>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar - Enrolled Students -->
            <div>
                <div class="table-container">
                    <div class="table-header">
                        <h3 style="margin: 0;">Enrolled Students (<?= count($students) ?>)</h3>
                    </div>
                    <div style="padding: 20px;">
                        <?php if (!empty($students)): ?>
                            <div style="max-height: 400px; overflow-y: auto;">
                                <?php foreach ($students as $student): ?>
                                    <div style="display: flex; align-items: center; gap: 15px; padding: 15px; border-bottom: 1px solid var(--gray);">
                                        <div style="width: 40px; height: 40px; background: var(--secondary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                            <?= strtoupper(substr($student['name'], 0, 1)) ?>
                                        </div>
                                        <div style="flex: 1;">
                                            <p style="margin: 0; font-weight: 500;"><?= htmlspecialchars($student['name']) ?></p>
                                            <p style="margin: 0; font-size: 0.85rem; color: var(--gray-dark);"><?= htmlspecialchars($student['email']) ?></p>
                                        </div>
                                        <div style="text-align: right;">
                                            <p style="margin: 0; font-size: 0.85rem; font-weight: 600;"><?= $student['progress'] ?>%</p>
                                            <p style="margin: 0; font-size: 0.75rem; color: var(--gray-dark);">progress</p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p style="text-align: center; color: var(--gray-dark); padding: 20px;">No students enrolled yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
            </div>
        </div>
    </div>
</div>
