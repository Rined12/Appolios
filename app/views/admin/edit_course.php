<?php
/**
 * APPOLIOS - Edit Course Page
 */
?>

<div class="dashboard">
    <div class="container">
        <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1>Edit Course</h1>
                <p>Update course information</p>
            </div>
            <a href="<?= APP_URL ?>/index.php?url=admin/courses" class="btn btn-outline" style="padding: 10px 20px;">← Back to Courses</a>
        </div>

        <div class="form-container" style="max-width: 700px;">
            <form action="<?= APP_URL ?>/index.php?url=admin/update-course/<?= $course['id'] ?>" method="POST">
                <div class="form-group">
                    <label for="title">Course Title *</label>
                    <input type="text" id="title" name="title" placeholder="Enter course title" value="<?= htmlspecialchars($course['title']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="description">Course Description *</label>
                    <textarea id="description" name="description" placeholder="Describe what students will learn in this course" required><?= htmlspecialchars($course['description']) ?></textarea>
                </div>

                <div class="form-group">
                    <label for="video_url">Video URL (Optional)</label>
                    <input type="url" id="video_url" name="video_url" placeholder="https://youtube.com/watch?v=..." value="<?= htmlspecialchars($course['video_url'] ?? '') ?>">
                    <small style="color: var(--gray-dark); font-size: 0.85rem;">YouTube, Vimeo, or direct video link</small>
                </div>

                <button type="submit" class="btn btn-yellow btn-block" style="margin-top: 20px;">Update Course</button>
            </form>
        </div>
    </div>
</div>