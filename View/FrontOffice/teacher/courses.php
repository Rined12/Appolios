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
            <div class="admin-main" style="grid-template-rows: min-content; align-items: start; height: auto; align-content: start;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <div>
                        <h1 style="margin: 0 0 0.5rem 0; font-size: 24px; line-height: 1.1;">My Courses</h1>
                        <p style="color: #64748b; margin: 0; font-size: 0.85rem;">
                            <?= count($courses ?? []) ?> courses
                        </p>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <a href="<?= APP_ENTRY ?>?url=teacher/add-course" class="btn btn-yellow">Add New Course</a>
                    </div>
                </div>

                <?php if (!empty($courses)): ?>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem;">
                        <?php foreach ($courses as $course): 
                            $status = $course['status'] ?? 'pending';
                            $chaptersCount = !empty($course['chapters']) ? count($course['chapters']) : 0;
                            $lessonsCount = 0;
                            if (!empty($course['chapters'])) {
                                foreach ($course['chapters'] as $ch) {
                                    $lessonsCount += !empty($ch['lessons']) ? count($ch['lessons']) : 0;
                                }
                            }
                        ?>
                            <article style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.05); transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.1)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.05)'">
                                <?php if (!empty($course['image'])): ?>
                                    <img src="<?= htmlspecialchars($course['image']) ?>" alt="<?= htmlspecialchars($course['title']) ?>" style="width: 100%; height: 160px; object-fit: cover;">
                                <?php else: ?>
                                    <?php $randomImgId = rand(1, 1000); ?>
                                    <img src="https://picsum.photos/seed/<?= $randomImgId ?>/400/160" alt="<?= htmlspecialchars($course['title']) ?>" style="width: 100%; height: 160px; object-fit: cover;">
                                <?php endif; ?>
                                <div style="padding: 1rem;">
                                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                                        <h3 style="margin: 0; font-size: 1rem; color: #1e293b; flex: 1;"><?= htmlspecialchars($course['title']) ?></h3>
                                        <span style="padding: 4px 10px; border-radius: 12px; font-size: 0.7rem; font-weight: 600; background: <?= $status === 'approved' ? '#dcfce7' : ($status === 'rejected' ? '#fee2e2' : '#fef3c7') ?>; color: <?= $status === 'approved' ? '#16a34a' : ($status === 'rejected' ? '#dc2626' : '#d97706') ?>;">
                                            <?= ucfirst($status) ?>
                                        </span>
                                    </div>
                                    
                                    <p style="margin: 0 0 1rem 0; color: #64748b; font-size: 0.85rem;">
                                        <?= htmlspecialchars(mb_strimwidth($course['description'] ?? '', 0, 80, '...')) ?>
                                    </p>

                                    <div style="display: flex; gap: 12px; margin-bottom: 1rem; font-size: 0.8rem; color: #64748b;">
                                        <span style="display: flex; align-items: center; gap: 4px;">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                                            <?= $chaptersCount ?> Chapters
                                        </span>
                                        <span style="display: flex; align-items: center; gap: 4px;">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                                            <?= $lessonsCount ?> Lessons
                                        </span>
                                    </div>

                                    <?php if (!empty($course['course_type'])): ?>
                                        <span style="background: #e0f2f1; color: #2B4865; padding: 4px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 600; display: inline-block; margin-bottom: 1rem;">
                                            <?= htmlspecialchars($course['course_type']) ?>
                                        </span>
                                    <?php endif; ?>

                                    <div style="display: flex; gap: 8px; margin-top: 1rem;">
                                        <a href="<?= APP_ENTRY ?>?url=teacher/course/<?= $course['id'] ?>" style="flex: 1; background: #f1f5f9; color: #475569; padding: 8px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.85rem; text-align: center; transition: background 0.2s;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">View</a>
                                        <a href="<?= APP_ENTRY ?>?url=teacher/edit-course/<?= $course['id'] ?>" style="flex: 1; background: #2B4865; color: white; padding: 8px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.85rem; text-align: center; transition: background 0.2s;" onmouseover="this.style.background='#1a3a52'" onmouseout="this.style.background='#2B4865'">Edit</a>
                                        <a href="<?= APP_ENTRY ?>?url=teacher/delete-course/<?= $course['id'] ?>" style="background: #fef2f2; color: #dc2626; padding: 8px 12px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.85rem; transition: background 0.2s;" onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fef2f2'" onclick="return confirm('Delete this course?')">Delete</a>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 4rem; background: white; border-radius: 12px;">
                        <svg width="80" height="80" viewBox="0 0 24 24" fill="#548CA8" style="opacity: 0.3; margin-bottom: 1rem;">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                            <line x1="12" y1="8" x2="12" y2="16"></line>
                            <line x1="8" y1="12" x2="16" y2="12"></line>
                        </svg>
                        <h3 style="color: #548CA8; margin: 0 0 0.5rem 0;">No Courses Yet</h3>
                        <p style="color: #64748b; margin: 0 0 1.5rem 0;">Create your first course to get started</p>
                        <a href="<?= APP_ENTRY ?>?url=teacher/add-course" class="btn btn-yellow">Create First Course</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>