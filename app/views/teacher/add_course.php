<?php
/**
 * APPOLIOS - Teacher Add Course Page
 */

$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);

$teacherSidebarActive = 'courses';
?>

<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main">
        <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1>Add New Course</h1>
                <p>Create a new course for students</p>
            </div>
            <a href="<?= APP_URL ?>/index.php?url=teacher/courses" class="btn btn-outline" style="padding: 10px 20px;">← Back to Courses</a>
        </div>

        <div class="form-container" style="max-width: 700px;">
            <form action="<?= APP_URL ?>/index.php?url=teacher/store-course" method="POST">
                <div class="form-group">
                    <label for="title">Course Title *</label>
                    <input type="text" id="title" name="title" placeholder="Enter course title" value="<?= htmlspecialchars($old['title'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="description">Course Description *</label>
                    <textarea id="description" name="description" placeholder="Describe what students will learn in this course" required><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label for="video_url">Video URL (Optional)</label>
                    <input type="url" id="video_url" name="video_url" placeholder="https://youtube.com/watch?v=..." value="<?= htmlspecialchars($old['video_url'] ?? '') ?>">
                    <small style="color: var(--gray-dark); font-size: 0.85rem;">YouTube, Vimeo, or direct video link</small>
                </div>

                <button type="submit" class="btn btn-yellow btn-block" style="margin-top: 20px;">Create Course</button>
            </form>
        </div>
            </div>
        </div>
    </div>
</div>
