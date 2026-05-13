<?php
/**
 * APPOLIOS - Student Lesson View
 */

$studentSidebarActive = 'my-courses';
?>

<div class="dashboard student-events-page">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>

            <div class="admin-main">
                <a href="<?= APP_ENTRY ?>?url=student/my-courses" style="color: #548CA8; font-size: 0.9rem; display: inline-block; margin-bottom: 1rem;">← Back to My Courses</a>
                
                <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem;">
                    <div>
                        <h1><?= htmlspecialchars($course['title']) ?></h1>
                        <p style="color: #64748b; margin-top: 0.5rem;">
                            Progress: <strong style="color: <?= $progress >= 100 ? '#10b981' : '#3b82f6' ?>"><?= (int) ($progress ?? 0) ?>%</strong>
                            <?php if($progress >= 100): ?><span style="margin-left:8px">🎉</span><?php endif; ?>
                        </p>
                    </div>
                    <?php if (!empty($progress)): ?>
                    <div style="width: 200px; background: #e2e8f0; border-radius: 99px; height: 12px; overflow: hidden;">
                        <div style="height: 100%; background: linear-gradient(90deg, <?= $progress >= 100 ? '#10b981' : '#3b82f6' ?>, <?= $progress >= 100 ? '#34d399' : '#60a5fa' ?>); width: <?= (int) $progress ?>%; transition: width 0.5s ease;"></div>
                    </div>
                    <?php if($progress >= 100): ?>
                    <div style="background:#dcfce7;color:#16a34a;padding:8px 16px;border-radius:8px;font-size:0.85rem;margin-top:8px">🎊 Congratulations! You completed this course!</div>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>

                <div style="display: grid; grid-template-columns: 1fr; gap: 30px;">
                    <!-- Course Description -->
                    <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 2rem;">
                        <h2 style="margin: 0 0 1rem 0; font-size: 1.3rem;">Course Description</h2>
                        <p style="line-height: 1.8; color: #374151;"><?= nl2br(htmlspecialchars($course['description'])) ?></p>
                    </div>

                    <!-- Chapters & Lessons -->
                    <?php if (!empty($course['chapters'])): ?>
                        <?php foreach ($course['chapters'] as $chapterIndex => $chapter): ?>
                            <div style="background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 1.5rem;">
                                <div onclick="this.nextElementSibling.style.display=this.nextElementSibling.style.display==='none'?'block':'none';this.querySelector('svg').style.transform=this.nextElementSibling.style.display==='none'?'rotate(0deg)':'rotate(180deg)'" style="padding: 1.25rem; background: linear-gradient(135deg, #2B4865, #256359); color: white; cursor: pointer; display: flex; justify-content: space-between; align-items: center;">
                                    <div style="display: flex; align-items: center; gap: 1rem;">
                                        <span style="font-weight: 700; font-size: 1.1rem;">Chapter <?= $chapterIndex + 1 ?>: <?= htmlspecialchars($chapter['title']) ?></span>
                                    </div>
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" style="transition: transform 0.3s;">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                </div>
                                
                                <div style="padding: 0;">
                                    <?php if (!empty($chapter['lessons'])): ?>
                                        <?php foreach ($chapter['lessons'] as $lessonIndex => $lesson): ?>
                                            <?php 
                                            $completedIds = array_map('intval', ($completedLessons ?? []));
                                            $isCompleted = in_array((int)$lesson['id'], $completedIds);
                                            $lessonTypeColor = match($lesson['lesson_type'] ?? 'video') {
                                                'video' => '#3b82f6',
                                                'text' => '#10b981',
                                                'pdf' => '#ef4444',
                                                default => '#3b82f6'
                                            };
                                            ?>
                                            <div class="lesson-item" style="padding: 1.25rem; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center; cursor: pointer; <?= $isCompleted ? 'border-left: 4px solid #22c55e;' : '' ?>" onclick="showLessonModal(<?= $lesson['id'] ?>)">
                                                <div style="display: flex; align-items: center; gap: 1rem;">
                                                    <?php if ($isCompleted): ?>
                                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="#22c55e" stroke="#22c55e" stroke-width="2">
                                                            <path d="M22 11.08V12a10 10 0 11-5.93-9.44"/>
                                                            <polyline points="22 4 12 14.01 9 11.01"/>
                                                        </svg>
                                                    <?php else: ?>
                                                        <span style="width: 20px; height: 20px; border-radius: 50%; background: #e5e7eb; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; color: #64748b;"><?= $lessonIndex + 1 ?></span>
                                                    <?php endif; ?>
                                                    <div>
                                                        <div style="font-weight: 600; color: <?= $isCompleted ? '#16a34a' : '#1e293b' ?>;"><?= htmlspecialchars($lesson['title']) ?></div>
                                                        <span style="font-size: 0.8rem; color: <?= $lessonTypeColor ?>; text-transform: uppercase;"><?= $lesson['lesson_type'] ?? 'video' ?></span>
                                                    </div>
                                                </div>
                                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                    <?php if (!$isCompleted): ?>
                                                        <button onclick="event.stopPropagation(); markLessonComplete(<?= $lesson['id'] ?>, <?= $course['id'] ?>)" style="background: #10b981; color: white; border: none; padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; cursor: pointer;">Complete</button>
                                                    <?php else: ?>
                                                        <span style="background: #dcfce7; color: #16a34a; padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 600;">Completed</span>
                                                    <?php endif; ?>
                                                    <span style="color: #548CA8;">View →</span>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div style="padding: 1.25rem; color: #64748b;">No lessons in this chapter</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="text-align: center; padding: 3rem; background: white; border-radius: 16px;">
                            <p style="color: #64748b;">No chapters available yet.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($progress >= 100): ?>
                    <div style="background: linear-gradient(135deg, #fefce8 0%, #fef3c7 100%); border-radius: 16px; padding: 2rem; text-align: center; border: 2px solid #fde047; margin-top: 2rem;">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">🎓</div>
                        <h2 style="margin: 0; color: #1e293b;">Congratulations!</h2>
                        <p style="margin: 0.5rem 0 0; color: #64748b;">You have completed this course!</p>
                        <a href="<?= APP_ENTRY ?>?url=student/dashboard" style="display: inline-block; margin-top: 1rem; background: #2B4865; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600;">Back to Dashboard</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Lesson Modal -->
<div id="lessonModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 1000; align-items: center; justify-content: center;" onclick="if(event.target===this)this.style.display='none'">
    <div style="background: white; border-radius: 16px; width: 90%; max-width: 800px; max-height: 90vh; overflow-y: auto; padding: 2rem; position: relative;">
        <button onclick="document.getElementById('lessonModal').style.display='none'" style="position: absolute; top: 1rem; right: 1rem; background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #64748b;">✕</button>
        <div id="lessonContent"></div>
    </div>
</div>

<script>
const lessonData = <?= json_encode($lessonData ?? []) ?>;
const completedLessons = <?= json_encode(array_values($completedLessons ?? [])) ?>;

function showLessonModal(lessonId) {
    const lesson = lessonData[lessonId];
    if (!lesson) return;
    
    const modal = document.getElementById('lessonModal');
    const content = document.getElementById('lessonContent');
    const isCompleted = completedLessons.includes(lessonId);
    
    let html = '<h2 style="margin: 0 0 1rem 0;">' + lesson.title + '</h2>';
    
    if (isCompleted) {
        html += '<div style="background: #dcfce7; color: #16a34a; padding: 12px 16px; border-radius: 8px; margin-bottom: 1rem; font-weight: 600; display: flex; align-items: center; gap: 8px;">';
        html += '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.44"/><polyline points="22 4 12 14.01 9 11.01"/></svg>';
        html += 'You have completed this lesson</div>';
    } else {
        html += '<span style="display: inline-block; background: #e5e7eb; padding: 4px 12px; border-radius: 20px; font-size: 0.85rem; margin-bottom: 1rem;">' + (lesson.lesson_type || 'video') + '</span>';
    }
    
    if (lesson.lesson_type === 'video' && lesson.video_url) {
        html += '<div style="background: #000; border-radius: 8px; overflow: hidden; margin-bottom: 1rem;"><iframe width="100%" height="400" src="' + lesson.video_url.replace('watch?v=', 'embed/') + '" frameborder="0" allowfullscreen></iframe></div>';
    } else if (lesson.lesson_type === 'text' && lesson.content) {
        html += '<div style="line-height: 1.8; padding: 1rem; background: #f8fafc; border-radius: 8px; margin-bottom: 1rem;">' + lesson.content + '</div>';
    } else if (lesson.lesson_type === 'pdf' && lesson.pdf_file) {
        html += '<a href="<?= APP_URL ?>/' + lesson.pdf_file + '" target="_blank" style="display: inline-flex; align-items: center; gap: 8px; background: #ef4444; color: white; padding: 12px 20px; border-radius: 8px; text-decoration: none; font-weight: 600;">';
        html += '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>';
        html += 'View PDF Document</a>';
    }
    
    if (isCompleted) {
        html += '<div style="margin-top: 1rem; background: #dcfce7; color: #16a34a; padding: 12px 20px; border-radius: 8px; font-weight: 600;">✓ Lesson Completed</div>';
    } else {
        html += '<button onclick="markComplete(' + lessonId + ')" style="margin-top: 1rem; background: #10b981; color: white; padding: 12px 24px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600; font-size: 1rem;">Mark as Complete</button>';
    }
    
    content.innerHTML = html;
    modal.style.display = 'flex';
}

function markLessonComplete(lessonId, courseId) {
    fetch('<?= APP_ENTRY ?>?url=student/mark-lesson-complete', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'lesson_id=' + lessonId + '&course_id=' + courseId
    }).then(r => r.json()).then(data => {
        if (data.success) {
            if (data.progress >= 100) {
                alert('Congratulations! You completed the course! 🎓');
            }
            location.reload();
        } else {
            alert(data.message || 'Error marking complete');
        }
    });
}

function markComplete(lessonId) {
    fetch('<?= APP_ENTRY ?>?url=student/mark-lesson-complete', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'lesson_id=' + lessonId
    }).then(r => r.json()).then(data => {
        if (data.success) {
            if (data.progress >= 100) {
                alert('Congratulations! You completed the course! 🎓');
            }
            location.reload();
        } else {
            alert(data.message || 'Error marking complete');
        }
    });
}
</script>