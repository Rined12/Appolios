<?php
/**
 * APPOLIOS - Add Course Page
 */

$old = $old ?? [];
?>

<div class="dashboard student-events-page">
    <div class="container admin-dashboard-container" style="max-width: 1400px; width: 100%;">
        <div class="admin-layout">
            <?php $adminSidebarActive = 'add-course'; require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main">
        <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1>Add New Course</h1>
                <p>Create a new course for the platform</p>
            </div>
            <div style="display: flex; gap: 10px;">
                <a href="javascript:history.back()" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; background: #6c757d;">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" style="transform: rotate(180deg);">
                        <path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/>
                    </svg>
                    Back
                </a>
            </div>
        </div>

        <div class="form-container" style="max-width: 700px;">
            <form action="<?= APP_ENTRY ?>?url=admin/store-course" method="POST" novalidate>
                <div class="form-group">
                    <label for="title">Course Title *</label>
                    <input type="text" id="title" name="title" placeholder="Enter course title" value="<?= htmlspecialchars($old['title'] ?? '') ?>" style="<?= isset($errors['title']) ? 'border-color: #ef4444; background: #fef2f2;' : '' ?>">
                    <?php if (isset($errors['title'])): ?>
                        <div style="color: #ef4444; font-size: 0.85rem; font-weight: 600; margin-top: 4px; display: flex; align-items: center; gap: 6px;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                            <?= htmlspecialchars($errors['title']) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="description">Course Description *</label>
                    <textarea id="description" name="description" placeholder="Describe what students will learn in this course" style="<?= isset($errors['description']) ? 'border-color: #ef4444; background: #fef2f2;' : '' ?>"><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
                    <?php if (isset($errors['description'])): ?>
                        <div style="color: #ef4444; font-size: 0.85rem; font-weight: 600; margin-top: 4px; display: flex; align-items: center; gap: 6px;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                            <?= htmlspecialchars($errors['description']) ?>
                        </div>
                    <?php endif; ?>
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