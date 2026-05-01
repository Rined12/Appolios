<?php
/**
 * APPOLIOS - Student Course View
 */

$studentSidebarActive = 'courses';

$chapters = $course['chapters'] ?? [];
?>

<div class="dashboard student-events-page" style="padding: 0.5rem 0 1rem;">
    <div class="container admin-dashboard-container" style="padding-top: 0;">
        <div class="admin-layout" style="gap: 0.5rem;">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>

            <div class="admin-main" style="padding: 0;">
                <div style="background: #f1f5f9; min-height: 100vh; padding: 1.5rem;">
                    <!-- Top Bar -->
                    <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
                        <div style="flex: 1; min-width: 280px; background: white; border-radius: 12px; padding: 1rem;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                <span style="font-weight: 600;">Progress</span>
                                <span id="progress-text" style="color: #10b981; font-weight: 600;"><?= (int) ($progress ?? 0) ?>%</span>
                            </div>
                            <div style="height: 10px; background: #e2e8f0; border-radius: 5px;">
                                <div id="progress-bar" style="height: 100%; background: #10b981; width: <?= (int) ($progress ?? 0) ?>%;"></div>
                            </div>
                            <p id="lessons-completed" style="margin-top: 0.5rem; font-size: 0.85rem; color: #64748b;"><?= count($completedLessons ?? []) ?> of <?= $course['lesson_count'] ?? 0 ?> lessons</p>
                        </div>
                        
                        <div style="background: white; border-radius: 12px; padding: 1rem; min-width: 180px;">
                            <h4 style="margin: 0 0 0.5rem 0;">Badges</h4>
                            <p style="margin: 0; color: #64748b; font-size: 0.85rem;">Complete lessons to earn badges</p>
                        </div>
                    </div>

                    <!-- Header -->
                    <div style="background: white; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                            <div>
                                <a href="<?= APP_ENTRY ?>?url=student/courses" style="color: #3b82f6; text-decoration: none; font-weight: 600;">&larr; Back</a>
                                <h1 style="margin: 0.5rem 0 0; font-size: 1.75rem; color: #1e293b;"><?= htmlspecialchars($course['title']) ?></h1>
                                <p style="margin: 0.5rem 0 0; color: #64748b;">By <?= htmlspecialchars($course['creator_name'] ?? 'Instructor') ?></p>
                            </div>
                            <?php if ($isEnrolled): ?>
                                <span style="background: #dcfce7; color: #16a34a; padding: 8px 16px; border-radius: 20px; font-weight: 600;">Enrolled</span>
                            <?php else: ?>
                                <a href="<?= APP_ENTRY ?>?url=student/enroll/<?= $course['id'] ?>" style="background: #3b82f6; color: white; padding: 8px 16px; border-radius: 20px; font-weight: 600; text-decoration: none;">Enroll Now</a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Course Info -->
                    <div style="background: white; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem;">
                        <h2 style="margin: 0 0 1rem 0; font-size: 1.2rem; color: #1e293b;"><?= htmlspecialchars($course['description'] ?? '') ?></h2>
                        <?php if (!empty($chapters)): ?>
                            <div style="display: flex; gap: 0.75rem;">
                                <span style="background: #dbeafe; color: #1e40af; padding: 6px 14px; border-radius: 14px; font-size: 0.85rem; font-weight: 600;"><?= count($chapters) ?> Chapters</span>
                                <span style="background: #dcfce7; color: #16a34a; padding: 6px 14px; border-radius: 14px; font-size: 0.85rem; font-weight: 600;"><?= $course['lesson_count'] ?? 0 ?> Lessons</span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Reviews Section -->
                    <?php
                    require_once __DIR__ . '/../../../Model/Review.php';
                    $reviewModel = new Review();
                    $ratingData = $reviewModel->getAverageRating($course['id']);
                    $avgRating = round($ratingData['avg'] ?? 0, 1);
                    $reviewCount = $ratingData['count'] ?? 0;
                    ?>
                    <div style="background: white; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                            <h2 style="margin: 0; font-size: 1.2rem; color: #1e293b;">Reviews (<?= $reviewCount ?>)</h2>
                            <?php if ($isEnrolled && !$hasReview): ?>
                                <button onclick="document.getElementById('review-form').style.display=document.getElementById('review-form').style.display==='none'?'block':'none'" style="background: #3b82f6; color: white; border: none; padding: 8px 16px; border-radius: 8px; cursor: pointer;">Write a Review</button>
                            <?php endif; ?>
                        </div>

                        <!-- Review Form -->
                        <?php if ($isEnrolled && !$hasReview): ?>
                            <div id="review-form" style="display: none; background: #f8fafc; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                                <form onsubmit="submitReview(event, <?= $course['id'] ?>)">
                                    <div style="margin-bottom: 1rem;">
                                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Rating</label>
                                        <div id="star-rating" style="display: flex; gap: 8px; cursor: pointer;">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <svg id="star-<?= $i ?>" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#e2e8f0" stroke-width="2" onclick="setRating(<?= $i ?>)">
                                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                                </svg>
                                            <?php endfor; ?>
                                        </div>
                                        <input type="hidden" id="review-rating" value="0">
                                        <div id="rating-text" style="margin-top: 8px; font-size: 0.9rem; color: #64748b;">Click to rate</div>
                                    </div>
                                    <div style="margin-bottom: 1rem;">
                                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Comment</label>
                                        <textarea id="review-comment" rows="4" style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; resize: vertical;" placeholder="Share your experience with this course..."></textarea>
                                    </div>
                                    <button type="submit" style="background: #10b981; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer;">Submit Review</button>
                                </form>
                            </div>
                        <?php endif; ?>

                        <!-- Average Rating Display -->
                        <?php if ($reviewCount > 0): ?>
                            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; padding: 1rem; background: #f8fafc; border-radius: 8px;">
                                <div style="font-size: 2rem; font-weight: 700; color: #1e293b;"><?= $avgRating ?></div>
                                <div>
                                    <div style="color: #f59e0b;"><?= str_repeat('★', floor($avgRating)) . str_repeat('☆', 5 - floor($avgRating)) ?></div>
                                    <div style="font-size: 0.85rem; color: #64748b;"><?= $reviewCount ?> reviews</div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Reviews List -->
                        <?php if (!empty($reviews)): ?>
                            <?php foreach ($reviews as $review): ?>
                                <div style="border-bottom: 1px solid #e2e8f0; padding: 1rem 0;">
                                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                                        <div style="width: 40px; height: 40px; border-radius: 50%; background: #3b82f6; color: white; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                                            <?= strtoupper(substr($review['user_name'] ?? 'U', 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div style="font-weight: 600; color: #1e293b;"><?= htmlspecialchars($review['user_name'] ?? 'Anonymous') ?></div>
                                            <div style="color: #f59e0b; font-size: 0.85rem;"><?= str_repeat('★', $review['rating']) ?></div>
                                        </div>
                                        <div style="margin-left: auto; font-size: 0.8rem; color: #64748b;"><?= date('M d, Y', strtotime($review['created_at'])) ?></div>
                                    </div>
                                    <?php if (!empty($review['comment'])): ?>
                                        <p style="margin: 0.5rem 0 0; color: #475569; line-height: 1.6;"><?= htmlspecialchars($review['comment']) ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p style="color: #64748b; text-align: center; padding: 2rem;">No reviews yet. Be the first to review this course!</p>
                        <?php endif; ?>
                    </div>

                    <!-- Chapters & Lessons -->
                    <?php if (!empty($chapters) && $isEnrolled): ?>
                        <?php foreach ($chapters as $chapterIndex => $chapter): ?>
                            <div style="background: white; border-radius: 12px; margin-bottom: 1rem; overflow: hidden;">
                                <div onclick="toggleChapter(<?= $chapterIndex ?>)" style="padding: 1.25rem; cursor: pointer; display: flex; justify-content: space-between; align-items: center; background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                                    <h3 style="margin: 0; font-size: 1.1rem; color: #1e293b;">Chapter <?= $chapterIndex + 1 ?>: <?= htmlspecialchars($chapter['title']) ?></h3>
                                    <svg id="chapter-icon-<?= $chapterIndex ?>" style="width: 24px; height: 24px; transition: transform 0.3s;">
                                        <path d="M6 9l6 6 6-6" stroke="#64748b" stroke-width="2" fill="none"/>
                                    </svg>
                                </div>
                                <div id="chapter-content-<?= $chapterIndex ?>" style="display: none; padding: 1rem;">
                                    <?php if (!empty($chapter['description'])): ?>
                                        <p style="margin: 0 0 1rem 0; color: #64748b;"><?= htmlspecialchars($chapter['description']) ?></p>
                                    <?php endif; ?>
                                    
                                    <?php foreach ($chapter['lessons'] as $lessonIndex => $lesson): ?>
                                        <?php 
                                        $completedIds = array_map('intval', ($completedLessons ?? []));
                                        $isCompleted = in_array((int)$lesson['id'], $completedIds);
                                        ?>
                                        <div style="background: #f8fafc; border-radius: 8px; margin-bottom: 0.75rem; overflow: hidden; <?= $isCompleted ? 'border-left: 3px solid #22c55e;' : '' ?>" id="lesson-<?= $chapterIndex ?>-<?= $lessonIndex ?>" data-completed="<?= $isCompleted ? 'true' : 'false' ?>">
                                            <div onclick="toggleAndComplete(<?= $chapterIndex ?>, <?= $lessonIndex ?>, <?= $lesson['id'] ?>, <?= $course['id'] ?>)" style="padding: 1rem; cursor: pointer; display: flex; justify-content: space-between; align-items: center;">
                                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                                    <?php if ($isCompleted): ?>
                                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="#22c55e" stroke="#22c55e" stroke-width="2">
                                                            <path d="M22 11.08V12a10 10 0 11-5.93-9.44"/>
                                                            <polyline points="22 4 12 14.01 9 11.01"/>
                                                        </svg>
                                                    <?php else: ?>
                                                        <span style="width: 18px; height: 18px; border-radius: 50%; background: #e5e7eb; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; color: #64748b;"><?= $lessonIndex + 1 ?></span>
                                                    <?php endif; ?>
                                                    <div>
                                                        <h4 style="margin: 0; font-size: 1rem; color: <?= $isCompleted ? '#16a34a' : '#1e293b' ?>;"><?= htmlspecialchars($lesson['title']) ?></h4>
                                                        <p style="margin: 0.25rem 0 0; font-size: 0.85rem; color: #64748b;"><?= ucfirst($lesson['lesson_type']) ?></p>
                                                    </div>
                                                </div>
                                                <?php if ($isCompleted): ?>
                                                    <span style="background: #dcfce7; color: #16a34a; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">✓ Done</span>
                                                <?php else: ?>
                                                    <svg id="lesson-icon-<?= $chapterIndex ?>-<?= $lessonIndex ?>" style="width: 20px; height: 20px; transition: transform 0.3s;">
                                                        <path d="M6 9l6 6 6-6" stroke="#64748b" stroke-width="2" fill="none"/>
                                                    </svg>
                                                <?php endif; ?>
                                            </div>
                                            <div id="lesson-content-<?= $chapterIndex ?>-<?= $lessonIndex ?>" style="display: none; padding: 1rem; border-top: 1px solid #e2e8f0;">
                                                <?php $text = $lesson['content'] ?? ''; $pdf = $lesson['pdf_path'] ?? ''; $type = $lesson['lesson_type'] ?? 'text'; ?>
                                                
                                                <?php if (!empty($text)): ?>
                                                    <div class="lesson-content" data-lesson-id="<?= $lesson['id'] ?>" data-course-id="<?= $course['id'] ?>" style="margin-bottom: 1rem; line-height: 1.7; color: #334155;">
                                                        <?= nl2br($text) ?>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <?php if ($type === 'both' || $type === 'pdf'): ?>
                                                    <?php if (empty($pdf)): ?>
                                                        <div style="padding: 1rem; background: #fef3c7; border-radius: 8px; text-align: center;">
                                                            <p style="margin: 0; color: #92400e; font-weight: 600;">No PDF attached</p>
                                                        </div>
                                                    <?php else: ?>
                                                        <div>
                                                            <h5 style="margin: 0 0 0.5rem 0; font-weight: 600;">PDF Document:</h5>
                                                            <iframe src="<?= htmlspecialchars($pdf) ?>" style="width: 100%; height: 400px; border: 1px solid #e2e8f0; border-radius: 8px;" frameborder="0"></iframe>
                                                            <a href="<?= htmlspecialchars($pdf) ?>" target="_blank" style="display: inline-block; margin-top: 0.5rem; color: #3b82f6;">Open PDF</a>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleChapter(i) {
    var c = document.getElementById('chapter-content-' + i);
    var ic = document.getElementById('chapter-icon-' + i);
    c.style.display = c.style.display === 'none' ? 'block' : 'none';
    ic.style.transform = c.style.display === 'block' ? 'rotate(180deg)' : 'rotate(0deg)';
}

function toggleAndComplete(ci, li, lid, cid) {
    var c = document.getElementById('lesson-content-' + ci + '-' + li);
    var ic = document.getElementById('lesson-icon-' + ci + '-' + li);
    
    if (c.style.display === 'none') {
        c.style.display = 'block';
        ic.style.transform = 'rotate(180deg)';
    } else {
        c.style.display = 'none';
        ic.style.transform = 'rotate(0deg)';
    }
    
    var lessonDiv = document.getElementById('lesson-' + ci + '-' + li);
    var isCompleted = lessonDiv && lessonDiv.getAttribute('data-completed') === 'true';
    
    if (!isCompleted && lid && cid) {
        markComplete(lid, cid, ci, li);
    }
}

function markComplete(lid, cid, chapterIndex, lessonIndex) {
    var x = new XMLHttpRequest();
    x.open('POST', 'index.php?url=student/completeLesson', true);
    x.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    x.onreadystatechange = function() {
        if (x.readyState === 4 && x.status === 200) {
            try { 
                var d = JSON.parse(x.responseText); 
                if (d.success) { 
                    completedLessons++; 
                    updateProgress();
                    updateLessonUI(chapterIndex, lessonIndex);
                    if (d.progress >= 100) {
                        var msg = '🎓 Congratulations! You completed the course!';
                        if (d.badge && d.badge.name) {
                            msg = d.badge.icon + ' ' + d.badge.name + ' - ' + d.badge.description;
                        }
                        alert(msg);
                    }
                }
            } catch(e) {}
        }
    };
    x.send('lessonId=' + lid + '&courseId=' + cid);
}

function updateLessonUI(chapterIndex, lessonIndex) {
    var lessonDiv = document.querySelectorAll('#chapter-content-' + chapterIndex + ' > div')[lessonIndex];
    if (!lessonDiv) return;
    
    lessonDiv.style.borderLeft = '3px solid #22c55e';
    lessonDiv.setAttribute('data-completed', 'true');
    
    var iconContainer = lessonDiv.querySelector('div > div:first-child');
    if (iconContainer) {
        iconContainer.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="#22c55e" stroke="#22c55e" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.44"/><polyline points="22 4 12 14.01 9 11.01"/></svg><div><h4 style="margin: 0; font-size: 1rem; color: #16a34a;">' + lessonDiv.querySelector('h4').textContent + '</h4></div>';
    }
    
    var rightSide = lessonDiv.querySelector('div > div:last-child');
    if (rightSide && rightSide.tagName !== 'DIV') {
        rightSide.outerHTML = '<span style="background: #dcfce7; color: #16a34a; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">✓ Done</span>';
    }
}

var totalLessons = <?= $course['lesson_count'] ?? 0 ?>;
var completedLessons = <?= count($completedLessons ?? []) ?>;
var courseId = <?= $course['id'] ?>;

function updateProgress() {
    var p = totalLessons > 0 ? Math.round((completedLessons / totalLessons) * 100) : 0;
    document.getElementById('progress-bar').style.width = p + '%';
    document.getElementById('progress-text').textContent = p + '%';
    document.getElementById('lessons-completed').textContent = completedLessons + ' of ' + totalLessons + ' lessons completed';
}

function submitReview(e, courseId) {
    e.preventDefault();
    var rating = document.getElementById('review-rating').value;
    var comment = document.getElementById('review-comment').value;
    if (rating === '0' || rating === 0) {
        alert('Please select a rating by clicking on stars');
        return;
    }
    var x = new XMLHttpRequest();
    x.open('POST', 'index.php?url=student/review/' + courseId, true);
    x.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    x.onreadystatechange = function() {
        if (x.readyState === 4 && x.status === 200) {
            try { 
                var d = JSON.parse(x.responseText); 
                if (d.success) {
                    alert('Review submitted successfully!');
                    location.reload();
                } else {
                    alert(d.message || 'Error submitting review');
                }
            } catch(e) { alert('Error submitting review'); }
        }
    };
    x.send('rating=' + rating + '&comment=' + encodeURIComponent(comment));
}

function setRating(rating) {
    document.getElementById('review-rating').value = rating;
    var text = '';
    var textEl = document.getElementById('rating-text');
    switch(rating) {
        case 1: text = '1 Star - Very Poor'; break;
        case 2: text = '2 Stars - Poor'; break;
        case 3: text = '3 Stars - Average'; break;
        case 4: text = '4 Stars - Good'; break;
        case 5: text = '5 Stars - Excellent'; break;
    }
    textEl.textContent = text;
    textEl.style.color = '#f59e0b';
    for (var i = 1; i <= 5; i++) {
        var star = document.getElementById('star-' + i);
        if (i <= rating) {
            star.setAttribute('fill', '#f59e0b');
            star.setAttribute('stroke', '#f59e0b');
        } else {
            star.setAttribute('fill', 'none');
            star.setAttribute('stroke', '#e2e8f0');
        }
    }
}
</script>