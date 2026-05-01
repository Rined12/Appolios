<?php
/**
 * APPOLIOS - Teacher Course Detail Page
 */

$teacherSidebarActive = 'courses';

// Prepare lesson data for JS
$lessonData = [];
if (!empty($course['chapters'])) {
    foreach ($course['chapters'] as $chapter) {
        if (!empty($chapter['lessons'])) {
            foreach ($chapter['lessons'] as $lesson) {
                $lessonData[$lesson['id']] = $lesson;
            }
        }
    }
}
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

                <!-- Chapters & Lessons -->
                <?php if (!empty($course['chapters'])): ?>
                <div class="table-container" style="margin-bottom: 30px;">
                    <div class="table-header">
                        <h3 style="margin: 0;">Course Content</h3>
                    </div>
                    <div style="padding: 20px;">
                        <?php foreach ($course['chapters'] as $chapterIndex => $chapter): ?>
                            <div style="background: #f8fafc; border-radius: 10px; padding: 20px; margin-bottom: 15px;">
                                <div onclick="toggleChapter(<?= $chapterIndex ?>)" style="cursor: pointer; display: flex; justify-content: space-between; align-items: center;">
                                    <h4 style="margin: 0; color: #1e293b;">Chapter <?= $chapterIndex + 1 ?>: <?= htmlspecialchars($chapter['title']) ?></h4>
                                    <span id="chapter-arrow-<?= $chapterIndex ?>" style="color: #64748b;">▼</span>
                                </div>
                                
                                <div id="chapter-content-<?= $chapterIndex ?>" style="display: none; margin-top: 15px; padding-left: 10px;">
                                    <?php if (!empty($chapter['description'])): ?>
                                        <p style="margin: 0 0 15px 0; color: #64748b; font-size: 0.9rem;"><?= htmlspecialchars($chapter['description']) ?></p>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($chapter['lessons'])): ?>
                                        <p style="font-weight: 600; margin: 0 0 10px 0; color: #64748b;">Lessons:</p>
                                        <?php foreach ($chapter['lessons'] as $lessonIndex => $lesson): ?>
                                            <?php $lessonType = $lesson['lesson_type'] ?? 'video'; ?>
                                            <?php $lessonColor = $lessonType === 'video' ? '#3b82f6' : ($lessonType === 'text' ? '#10b981' : '#ef4444'); ?>
                                            <div onclick="viewLesson(<?= $lesson['id'] ?>)" style="display: flex; align-items: center; gap: 15px; padding: 12px; background: white; border-radius: 8px; margin-bottom: 8px; cursor: pointer;">
                                                <span style="width: 30px; height: 30px; background: <?= $lessonColor ?>; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; font-weight: 600;"><?= $lessonIndex + 1 ?></span>
                                                <div style="flex: 1;">
                                                    <p style="margin: 0; font-weight: 500;"><?= htmlspecialchars($lesson['title']) ?></p>
                                                    <p style="margin: 0; font-size: 0.8rem; color: #64748b;"><?= ucfirst($lessonType) ?></p>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar - Enrolled Students -->
            <div>
                <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 20px;">
                    <h3 style="margin: 0 0 1rem 0; font-size: 1.1rem; color: #1e293b;">Enrolled Students</h3>
                    
                    <?php if (!empty($enrolledStudents)): ?>
                        <div style="max-height: 300px; overflow-y: auto;">
                            <?php foreach ($enrolledStudents as $student): ?>
                                <div style="display: flex; align-items: center; gap: 10px; padding: 10px; border-bottom: 1px solid #e5e7eb;">
                                    <div style="width: 32px; height: 32px; background: #3b82f6; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 600;">
                                        <?= strtoupper(substr($student['name'], 0, 1)) ?>
                                    </div>
                                    <div style="flex: 1;">
                                        <p style="margin: 0; font-size: 0.9rem; font-weight: 500;"><?= htmlspecialchars($student['name']) ?></p>
                                        <p style="margin: 0; font-size: 0.75rem; color: #64748b;">
                                            Progress: <?= (int) ($student['progress'] ?? 0) ?>%
                                        </p>
                                    </div>
                                    <?php if (($student['progress'] ?? 0) >= 100): ?>
                                        <span style="background: #10b981; color: white; padding: 2px 8px; border-radius: 10px; font-size: 0.7rem; font-weight: 600;">Done</span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0; font-size: 0.9rem; color: #64748b;">
                                <strong><?= count($enrolledStudents) ?></strong> student(s) enrolled
                            </p>
                        </div>
                    <?php else: ?>
                        <p style="margin: 0; color: #64748b;">No students enrolled yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
                </div>

                <!-- Chapters & Lessons -->
                <?php if (!empty($course['chapters'])): ?>
                <div class="table-container" style="margin-bottom: 30px;">
                    <div class="table-header">
                        <h3 style="margin: 0;">Course Content</h3>
                    </div>
                    <div style="padding: 20px;">
                        <?php foreach ($course['chapters'] as $chapterIndex => $chapter): ?>
                            <div style="background: #f8fafc; border-radius: 10px; padding: 20px; margin-bottom: 15px;">
                                <div onclick="toggleChapter(<?= $chapterIndex ?>)" style="cursor: pointer; display: flex; justify-content: space-between; align-items: center;">
                                    <h4 style="margin: 0; color: #1e293b;">Chapter <?= $chapterIndex + 1 ?>: <?= htmlspecialchars($chapter['title']) ?></h4>
                                    <span id="chapter-arrow-<?= $chapterIndex ?>" style="color: #64748b;">▼</span>
                                </div>
                                
                                <div id="chapter-content-<?= $chapterIndex ?>" style="display: none; margin-top: 15px; padding-left: 10px;">
                                    <?php if (!empty($chapter['description'])): ?>
                                        <p style="margin: 0 0 15px 0; color: #64748b; font-size: 0.9rem;"><?= htmlspecialchars($chapter['description']) ?></p>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($chapter['lessons'])): ?>
                                        <?php foreach ($chapter['lessons'] as $lessonIndex => $lesson): ?>
                                            <?php $lessonType = $lesson['lesson_type'] ?? 'video'; ?>
                                            <?php $lessonColor = $lessonType === 'video' ? '#3b82f6' : ($lessonType === 'text' ? '#10b981' : '#ef4444'); ?>
                                            <div onclick="viewLesson(<?= $lesson['id'] ?>)" style="display: flex; align-items: center; gap: 15px; padding: 12px; background: white; border-radius: 8px; margin-bottom: 8px; cursor: pointer;">
                                                <span style="width: 30px; height: 30px; background: <?= $lessonColor ?>; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; font-weight: 600;"><?= $lessonIndex + 1 ?></span>
                                                <div style="flex: 1;">
                                                    <p style="margin: 0; font-weight: 500;"><?= htmlspecialchars($lesson['title']) ?></p>
                                                    <p style="margin: 0; font-size: 0.8rem; color: #64748b;"><?= ucfirst($lessonType) ?></p>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <script>
function toggleChapter(idx) {
    const el = document.getElementById('chapter-content-' + idx);
    const arrow = document.getElementById('chapter-arrow-' + idx);
    if (el.style.display === 'none') {
        el.style.display = 'block';
        arrow.textContent = '▲';
    } else {
        el.style.display = 'none';
        arrow.textContent = '▼';
    }
}
                </script>
                <?php endif; ?>

                <!-- Video Player -->
                <?php if (!empty($course['video_url'])): ?>
                    <div class="table-container">
                        <div class="table-header">
                            <h3 style="margin: 0;">Course Video</h3>
                        </div>
                        <div style="padding: 20px;">
                            <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: var(--border-radius-sm); background: var(--primary-color);">
                                <?php
                                $videoUrl = $course['video_url'];
                                if (strpos($videoUrl, 'youtube.com') !== false || strpos($videoUrl, 'youtu.be') !== false) {
                                    if (strpos($videoUrl, 'youtu.be') !== false) {
                                        $videoId = basename(parse_url($videoUrl, PHP_URL_PATH));
                                    } else {
                                        parse_str(parse_url($videoUrl, PHP_URL_QUERY), $params);
                                        $videoId = $params['v'] ?? '';
                                    }
                                    echo '<iframe style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;" src="https://www.youtube.com/embed/' . htmlspecialchars($videoId) . '" frameborder="0" allowfullscreen></iframe>';
                                } else {
                                    echo '<video controls style="width: 100%;"><source src="' . htmlspecialchars($videoUrl) . '" type="video/mp4">Your browser does not support video playback.</video>';
                                }
                                ?>
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

<!-- Lesson Modal -->
<div id="lessonModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 16px; width: 90%; max-width: 700px; max-height: 80vh; overflow-y: auto;">
        <div id="lessonModalContent" style="padding: 20px;"></div>
    </div>
</div>

<script>
const lessonData = <?= json_encode($lessonData) ?>;

function viewLesson(lessonId) {
    const lesson = lessonData[lessonId];
    if (!lesson) return;
    
    const modal = document.getElementById('lessonModal');
    const content = document.getElementById('lessonModalContent');
    
    let html = '<button onclick="document.getElementById(\'lessonModal\').style.display=\'none\'" style="position: absolute; top: 20px; right: 20px; background: #6b7280; color: white; padding: 8px 16px; border-radius: 8px; border: none; cursor: pointer;">Close</button>';
    html += '<h2 style="margin: 0 0 15px 0; color: #1e293b; padding-right: 50px;">' + lesson.title + '</h2>';
    html += '<div style="background: #f1f5f9; padding: 8px 12px; border-radius: 6px; margin-bottom: 15px; display: inline-block;">';
    html += '<span style="font-size: 0.8rem; color: #64748b;">Type: ' + (lesson.lesson_type || 'video') + '</span>';
    html += '</div>';
    
    if ((lesson.lesson_type || 'video') === 'video' && lesson.video_url) {
        html += '<div style="background: #000; border-radius: 8px; padding: 56.25% 0 0 0; position: relative; margin-bottom: 15px;">';
        html += '<iframe style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;" src="' + lesson.video_url + '" frameborder="0" allowfullscreen></iframe>';
        html += '</div>';
    } else if ((lesson.lesson_type || 'video') === 'text' && lesson.content) {
        html += '<div style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-bottom: 15px;">';
        html += lesson.content;
        html += '</div>';
    } else if ((lesson.lesson_type || 'video') === 'pdf' && lesson.pdf_file) {
        html += '<a href="' + lesson.pdf_file + '" target="_blank" style="display: inline-flex; align-items: center; gap: 8px; background: #ef4444; color: white; padding: 12px 20px; border-radius: 8px; text-decoration: none; font-weight: 600;">';
        html += 'View PDF';
        html += '</a>';
    } else {
        html += '<p style="color: #64748b;">No content available</p>';
    }
    
    content.innerHTML = html;
    modal.style.display = 'flex';
}
</script>
