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

                <div style="display:flex;gap:0.5rem;margin-bottom:0.5rem">
                    <input type="text" id="searchInput" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" placeholder="Search courses..." style="flex:1;min-width:200px;padding:8px 12px;border:1px solid #e5e7eb;border-radius:6px;font-size:0.9rem" oninput="filterCourses(this.value)">
                </div>
                <script>
                function filterCourses(value) {
                    var filter = value.toLowerCase();
                    var container = document.getElementById('courseContainer');
                    if (!container) return;
                    var cards = container.children;
                    for (var i = 0; i < cards.length; i++) {
                        var card = cards[i];
                        var text = card.textContent || card.innerText;
                        if (text.toLowerCase().indexOf(filter) > -1) {
                            card.style.display = '';
                        } else {
                            card.style.display = 'none';
                        }
                    }
                }
                </script>

                <?php 
                $hasCourses = is_array($courses) && count($courses) > 0;
                $hasRecommendations = !empty($recommendations);
                
                if ($hasCourses || $hasRecommendations):
                ?>
                    <div id="courseContainer" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem; margin-top: 0.5rem;">
                        <!-- AI Recommended Courses (highlighted) -->
                        <?php if ($hasRecommendations): ?>
                            <?php foreach ($recommendations as $rec): ?>
                                <article style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(102, 126, 234, 0.25); position: relative;">
                                    <!-- AI Badge -->
                                    <div style="position: absolute; top: 10px; right: 10px; z-index: 10; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 600; display: flex; align-items: center; gap: 4px;">
                                        <svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                                        AI Pick
                                    </div>
                                    <?php if (!empty($rec['image'])): ?>
                                        <img src="<?= htmlspecialchars($rec['image']) ?>" alt="<?= htmlspecialchars($rec['title']) ?>" style="width: 100%; height: 140px; object-fit: cover;">
                                    <?php else: ?>
                                        <?php $randomImgId = rand(1, 1000); ?>
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
                        
                        <!-- Regular Courses -->
                        <?php if ($hasCourses): ?>
                            <?php foreach ($courses as $course): ?>
                                <article style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                                    <?php if (!empty($course['image'])): ?>
                                        <img src="<?= htmlspecialchars($course['image']) ?>" alt="<?= htmlspecialchars($course['title']) ?>" style="width: 100%; height: 140px; object-fit: cover;">
                                    <?php else: ?>
                                        <?php $randomImgId = rand(1, 1000); ?>
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