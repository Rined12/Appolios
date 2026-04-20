<?php
/**
 * APPOLIOS - Add Course Page
 */

$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);
$adminSidebarActive = 'courses';
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
                    <a href="<?= APP_ENTRY ?>?url=admin/courses" class="btn btn-outline" style="padding: 10px 20px;">← Back to Courses</a>
                </div>

                <div class="form-container" style="max-width: 700px;">
                    <form action="<?= APP_ENTRY ?>?url=admin/store-course" method="POST" novalidate id="admin-course-create-form">
                        <div class="form-group">
                            <label for="title">Course Title *</label>
                            <input type="text" id="title" name="title" placeholder="Enter course title" value="<?= htmlspecialchars($old['title'] ?? '') ?>" style="<?= isset($errors['title']) ? 'border-color: #ef4444; background: #fef2f2;' : '' ?>">
                            <div class="field-error" data-error-for="title" style="color: #ef4444; font-size: 0.85rem; font-weight: 600; margin-top: 4px;"><?= htmlspecialchars($errors['title'] ?? '') ?></div>
                        </div>

                        <div class="form-group">
                            <label for="description">Course Description *</label>
                            <textarea id="description" name="description" placeholder="Describe what students will learn in this course" style="<?= isset($errors['description']) ? 'border-color: #ef4444; background: #fef2f2;' : '' ?>"><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
                            <div class="field-error" data-error-for="description" style="color: #ef4444; font-size: 0.85rem; font-weight: 600; margin-top: 4px;"><?= htmlspecialchars($errors['description'] ?? '') ?></div>
                        </div>

                        <div class="form-group">
                            <label for="video_url">Video URL (Optional)</label>
                            <input type="text" id="video_url" name="video_url" placeholder="https://youtube.com/watch?v=..." value="<?= htmlspecialchars($old['video_url'] ?? '') ?>" style="<?= isset($errors['video_url']) ? 'border-color: #ef4444; background: #fef2f2;' : '' ?>">
                            <small style="color: var(--gray-dark); font-size: 0.85rem;">YouTube, Vimeo, or direct video link</small>
                            <div class="field-error" data-error-for="video_url" style="color: #ef4444; font-size: 0.85rem; font-weight: 600; margin-top: 4px;"><?= htmlspecialchars($errors['video_url'] ?? '') ?></div>
                        </div>

                        <button type="submit" class="btn btn-yellow btn-block" style="margin-top: 20px;">Create Course</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('admin-course-create-form');
    if (!form) {
        return;
    }

    const title = form.querySelector('#title');
    const description = form.querySelector('#description');
    const videoUrl = form.querySelector('#video_url');

    const setError = (name, message) => {
        const errorNode = form.querySelector('[data-error-for="' + name + '"]');
        if (errorNode) {
            errorNode.textContent = message;
        }
    };

    form.addEventListener('submit', function (event) {
        setError('title', '');
        setError('description', '');
        setError('video_url', '');

        let hasError = false;
        const titleValue = title.value.trim();
        const descriptionValue = description.value.trim();
        const videoUrlValue = videoUrl.value.trim();

        if (titleValue.length < 3) {
            setError('title', 'Title must contain at least 3 characters.');
            hasError = true;
        } else if (titleValue.length > 120) {
            setError('title', 'Title must not exceed 120 characters.');
            hasError = true;
        }

        if (descriptionValue.length < 10) {
            setError('description', 'Description must contain at least 10 characters.');
            hasError = true;
        } else if (descriptionValue.length > 3000) {
            setError('description', 'Description must not exceed 3000 characters.');
            hasError = true;
        }

        if (videoUrlValue.length > 0) {
            try {
                new URL(videoUrlValue);
            } catch (error) {
                setError('video_url', 'Video URL must be a valid URL.');
                hasError = true;
            }
        }

        if (hasError) {
            event.preventDefault();
        }
    });
});
</script>