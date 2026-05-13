<?php
/**
 * APPOLIOS - Student Browse All Courses
 */

$studentSidebarActive = 'courses';
?>

<div class="dashboard student-events-page fade-in">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>

            <div class="admin-main" style="grid-template-rows: min-content; align-items: start; height: auto; align-content: start;">
                <h1 style="margin: 0; padding: 0; font-size: 24px; line-height: 1.1;">Browse Courses</h1>
                <p style="color: #64748b; margin: 0; font-size: 0.85rem;">
                    <?= count($courses) ?> courses available
                    <?php if (!empty($recommendations)): ?>
                        <span style="margin-left: 10px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 2px 10px; border-radius: 12px; font-size: 0.75rem;">
                            🤖 <?= count($recommendations) ?> Recommended
                        </span>
                    <?php endif; ?>
                </p>

                <div style="display:flex;gap:0.5rem;margin-bottom:0.5rem;align-items:center">
                    <input type="text" id="searchInput" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" placeholder="Search courses..." style="flex:1;min-width:200px;padding:8px 12px;border:1px solid #e5e7eb;border-radius:6px;font-size:0.9rem" oninput="filterCoursesStudent(this.value)">
                    <button id="bookmarkFilterBtn" onclick="toggleBookmarkFilter()" style="padding:8px 16px;background:#f1f5f9;border:1px solid #e5e7eb;border-radius:6px;font-size:0.9rem;cursor:pointer;font-weight:600;">
                        🔖 My Bookmarks
                    </button>
                    <button id="aiRecommendCoursesBtn" style="background: linear-gradient(135deg, #FFD700, #FFA500); color: #2B4865; border: none; padding: 8px 16px; border-radius: 6px; font-weight: 800; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 15px rgba(255, 165, 0, 0.3); transition: all 0.3s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(255, 165, 0, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(255, 165, 0, 0.3)'">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path></svg>
                        AI Recommender
                    </button>
                </div>
                <script>
                (function() {
                    var showBookmarksOnly = false;
                    window.filterCoursesStudent = function(value) {
                        var filter = (value || '').toLowerCase();
                        var container = document.getElementById('courseContainer');
                        if (!container) return;
                        var cards = container.children;
                        for (var i = 0; i < cards.length; i++) {
                            var card = cards[i];
                            var text = card.textContent || card.innerText;
                            var isBookmarked = card.classList.contains('bookmarked');
                            if (text.toLowerCase().indexOf(filter) > -1 && (!showBookmarksOnly || isBookmarked)) {
                                card.style.display = '';
                            } else {
                                card.style.display = 'none';
                            }
                        }
                    };
                    window.toggleBookmarkFilter = function() {
                        showBookmarksOnly = !showBookmarksOnly;
                        var btn = document.getElementById('bookmarkFilterBtn');
                        btn.style.background = showBookmarksOnly ? '#667eea' : '#f1f5f9';
                        btn.style.color = showBookmarksOnly ? 'white' : '#1e293b';
                        var searchInput = document.getElementById('searchInput');
                        filterCoursesStudent(searchInput ? searchInput.value : '');
                    };
                })();
                </script>

                <?php 
                $hasCourses = is_array($courses) && count($courses) > 0;
                $hasRecommendations = !empty($recommendations);
                
                if ($hasCourses || $hasRecommendations):
                ?>
                    <div id="courseContainer" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem; margin-top: 0.5rem;">
                        <?php if ($hasRecommendations): ?>
                            <?php foreach ($recommendations as $rec): $isBookmarked = isset($bookmarkedCourseIds[$rec['id']]); ?>
                                <article class="<?= $isBookmarked ? 'bookmarked' : '' ?>" style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(102, 126, 234, 0.25); position: relative;<?= $isBookmarked ? ';border:2px solid #667eea' : '' ?>">
                                    <div style="position: absolute; top: 10px; right: 10px; z-index: 10; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 600; display: flex; align-items: center; gap: 4px;">
                                        <svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                                        AI Pick
                                    </div>
                                    <?php if (!empty($rec['image'])): ?>
                                        <?php 
                                            $imgSrc = $rec['image'];
                                            if (strpos($imgSrc, 'http') !== 0) {
                                                $imgSrc = APP_URL . '/' . ltrim($imgSrc, '/');
                                            }
                                        ?>
                                        <img src="<?= htmlspecialchars($imgSrc) ?>" alt="<?= htmlspecialchars($rec['title']) ?>" style="width: 100%; height: 140px; object-fit: contain; background-color: #f8fafc;">
                                    <?php else: ?>
                                        <?php $randomImgId = $rec['id'] ?? rand(1, 1000); ?>
                                        <img src="https://picsum.photos/seed/<?= $randomImgId ?>/400/140" alt="<?= htmlspecialchars($rec['title']) ?>" style="width: 100%; height: 140px; object-fit: cover;">
                                    <?php endif; ?>
                                    <div style="padding: 1rem;">
                                        <h3 style="margin: 0 0 0.5rem 0; font-size: 1rem; color: #1e293b;"><?= htmlspecialchars($rec['title']) ?></h3>
                                        <p style="margin: 0 0 1rem 0; color: #64748b; font-size: 0.85rem;">
                                            <?= htmlspecialchars(mb_strimwidth($rec['description'] ?? '', 0, 60, '...')) ?>
                                        </p>
                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                            <span style="font-weight: 700; color: <?= ($rec['price'] ?? 0) > 0 ? '#10b981' : '#3b82f6' ?>;">
                                                <?= ($rec['price'] ?? 0) > 0 ? '$' . number_format($rec['price'], 2) : 'Free' ?>
                                            </span>
                                            <span style="font-size: 0.85rem; color: #64748b;">by <?= htmlspecialchars($rec['creator_name'] ?? 'Instructor') ?></span>
                                        </div>
                                        <a href="<?= APP_ENTRY ?>?url=student/course/<?= (int) $rec['id'] ?>" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-weight: 600; display: block; text-align: center; margin-top: 1rem;">View Course</a>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        
                        <?php if ($hasCourses): ?>
                            <?php $recommendedIds = array_column($recommendations ?? [], 'id'); ?>
                            <?php foreach ($courses as $course): 
                                if (in_array($course['id'], $recommendedIds)) continue;
                                $isBookmarked = isset($bookmarkedCourseIds[$course['id']]); ?>
                                <article class="<?= $isBookmarked ? 'bookmarked' : '' ?>" style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.05);<?= $isBookmarked ? ';border:2px solid #667eea' : '' ?>">
                                    <?php if (!empty($course['image'])): ?>
                                        <?php 
                                            $imgSrc = $course['image'];
                                            if (strpos($imgSrc, 'http') !== 0) {
                                                $imgSrc = APP_URL . '/' . ltrim($imgSrc, '/');
                                            }
                                        ?>
                                        <img src="<?= htmlspecialchars($imgSrc) ?>" alt="<?= htmlspecialchars($course['title']) ?>" style="width: 100%; height: 140px; object-fit: contain; background-color: #f8fafc;">
                                    <?php else: ?>
                                        <?php $randomImgId = $course['id'] ?? rand(1, 1000); ?>
                                        <img src="https://picsum.photos/seed/<?= $randomImgId ?>/400/140" alt="<?= htmlspecialchars($course['title']) ?>" style="width: 100%; height: 140px; object-fit: cover;">
                                    <?php endif; ?>
                                    <div style="padding: 1rem;">
                                        <h3 style="margin: 0 0 0.5rem 0; font-size: 1rem; color: #1e293b;"><?= htmlspecialchars($course['title']) ?></h3>
                                        <p style="margin: 0 0 1rem 0; color: #64748b; font-size: 0.85rem;">
                                            <?= htmlspecialchars(mb_strimwidth($course['description'] ?? '', 0, 60, '...')) ?>
                                        </p>
                                        <?php if (in_array($course['id'], $enrolledIds)): ?>
                                            <a href="<?= APP_ENTRY ?>?url=student/course/<?= $course['id'] ?>" style="background: #10b981; color: white; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-weight: 600; display: block; text-align: center;">View Course</a>
                                        <?php else: ?>
                                            <a href="<?= APP_ENTRY ?>?url=student/course/<?= $course['id'] ?>" style="background: #2B4865; color: white; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-weight: 600; display: block; text-align: center;">View Details</a>
                                        <?php endif; ?>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 3rem; background: white; border-radius: 12px;">
                        <h3 style="color: #2B4865;">No Courses Found</h3>
                        <p style="color: #64748b;">Try a different search term</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- AI RECOMMENDATIONS MODAL -->
<div id="aiRecommendCoursesModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(8px); z-index: 10001; align-items: center; justify-content: center; font-family: 'Inter', sans-serif; padding: 20px; box-sizing: border-box;">
    <div style="background: white; border-radius: 32px; width: 100%; max-width: 550px; max-height: 95vh; display: flex; flex-direction: column; padding: 30px; box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.25); position: relative; animation: modalPop 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); box-sizing: border-box;">
        <button onclick="closeAiCoursesModal()" style="position: absolute; top: 20px; right: 20px; background: #f1f5f9; border: none; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #64748b; cursor: pointer; transition: all 0.2s; z-index: 2;" onmouseover="this.style.background='#e2e8f0'; this.style.color='#1e293b'" onmouseout="this.style.background='#f1f5f9'; this.style.color='#64748b'">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>

        <div style="text-align: center; margin-bottom: 25px; flex-shrink: 0;">
            <div style="background: linear-gradient(135deg, #FFC107, #FF9800); width: 60px; height: 60px; border-radius: 16px; display: flex; align-items: center; justify-content: center; color: #ffffff; margin: 0 auto 20px; box-shadow: 0 8px 16px rgba(255, 152, 0, 0.25);">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"></path></svg>
            </div>
            <h2 style="margin: 0; font-size: 1.8rem; font-weight: 800; color: #0f172a; letter-spacing: -0.03em;">Smart Recommendations</h2>
            <p style="margin: 8px 0 0; font-size: 1rem; color: #64748b;">AI-tailored courses just for you, <?= htmlspecialchars($_SESSION['user_name'] ?? 'Student') ?>.</p>
        </div>

        <div id="aiCourseRecommendationsList" style="overflow-y: auto; padding-right: 5px; scrollbar-width: thin; flex-grow: 1;">
            <!-- Recommendations will be injected here -->
        </div>

        <div style="margin-top: 25px; padding-top: 15px; border-top: 1px solid #eef2f6; text-align: center; flex-shrink: 0;">
            <p style="font-size: 0.85rem; color: #94a3b8; font-weight: 500; margin: 0;">Powered by Gemini Pro AI • Appolios Intelligent Learning</p>
        </div>
    </div>
</div>

<style>
@keyframes modalPop {
    from { opacity: 0; transform: scale(0.9) translateY(20px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const aiBtn = document.getElementById('aiRecommendCoursesBtn');
    if (aiBtn) {
        aiBtn.addEventListener('click', function() {
            const btn = this;
            const originalContent = btn.innerHTML;
            
            // Loading State
            btn.disabled = true;
            btn.innerHTML = '<svg class="spin" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="animation: spin 1s linear infinite;"><path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83"></path></svg> Analyzing...';
            btn.style.opacity = '0.8';

            fetch('<?= APP_ENTRY ?>?url=student/recommend-courses')
                .then(response => response.json())
                .then(data => {
                    btn.disabled = false;
                    btn.innerHTML = originalContent;
                    btn.style.opacity = '1';

                    if (data.success) {
                        const container = document.getElementById('aiCourseRecommendationsList');
                        container.innerHTML = '';
                        
                        data.recommendations.forEach(rec => {
                            const card = document.createElement('div');
                            card.className = 'ai-rec-card';
                            card.style.cssText = 'background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; padding: 20px; margin-bottom: 15px; transition: all 0.2s; cursor: pointer;';
                            card.innerHTML = `
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                                    <h4 style="margin: 0; color: #1e293b; font-size: 1.1rem; font-weight: 700;">${rec.title}</h4>
                                    <span style="background: #e0f2fe; color: #0369a1; padding: 4px 10px; border-radius: 50px; font-size: 0.75rem; font-weight: 800;">Recommended</span>
                                </div>
                                <p style="margin: 0 0 15px 0; color: #64748b; font-size: 0.9rem; line-height: 1.5;">${rec.reason}</p>
                                <a href="<?= APP_ENTRY ?>?url=student/course/${rec.id}" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; font-weight: 700; font-size: 0.85rem; padding: 8px 16px; border-radius: 8px; display: inline-flex; align-items: center; gap: 5px;">
                                    View Course Details 
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                                </a>
                            `;
                            card.onmouseover = () => { card.style.borderColor = '#667eea'; card.style.background = '#fff'; card.style.transform = 'translateX(5px)'; };
                            card.onmouseout = () => { card.style.borderColor = '#e2e8f0'; card.style.background = '#f8fafc'; card.style.transform = 'translateX(0)'; };
                            container.appendChild(card);
                        });

                        document.getElementById('aiRecommendCoursesModal').style.display = 'flex';
                    } else {
                        alert('AI Error: ' + (data.message || data.error || 'Could not get recommendations.'));
                    }
                })
                .catch(err => {
                    console.error(err);
                    btn.disabled = false;
                    btn.innerHTML = originalContent;
                    btn.style.opacity = '1';
                    alert('A network error occurred. Please try again.');
                });
        });
    }
});

function closeAiCoursesModal() {
    document.getElementById('aiRecommendCoursesModal').style.display = 'none';
}
</script>