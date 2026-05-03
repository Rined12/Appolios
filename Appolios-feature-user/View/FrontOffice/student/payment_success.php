<?php
/**
 * APPOLIOS - Payment Success Page
 */

$studentSidebarActive = 'courses';
$courseId = $course_id ?? 0;
?>
<style>
.certificates-page .admin-layout { gap: 5px !important; }
.certificates-page .admin-main { gap: 5px !important; display: block !important; }
</style>

<div class="dashboard student-events-page fade-in">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>

            <div class="admin-main" style="text-align: center; padding: 3rem;">
                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 3rem; border-radius: 20px; max-width: 500px; margin: 0 auto;">
                    <div style="font-size: 4rem; margin-bottom: 1rem;">🎉</div>
                    <h1 style="margin: 0 0 1rem 0; font-size: 2rem;">Payment Successful!</h1>
                    <p style="font-size: 1.1rem; opacity: 0.9; margin-bottom: 2rem;">
                        Thank you for your purchase! You are now enrolled in the course.
                    </p>
                    <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                        <a href="<?= APP_ENTRY ?>?url=student/course/<?= $courseId ?>" style="background: white; color: #667eea; padding: 12px 24px; border-radius: 8px; font-weight: 600; text-decoration: none;">Start Learning</a>
                        <a href="<?= APP_ENTRY ?>?url=student/my-courses" style="background: rgba(255,255,255,0.2); color: white; padding: 12px 24px; border-radius: 8px; font-weight: 600; text-decoration: none;">My Courses</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>