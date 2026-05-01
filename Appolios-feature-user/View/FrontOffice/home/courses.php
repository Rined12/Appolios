<?php
/**
 * APPOLIOS - Public Courses Page
 */
?>

<section class="hero" style="min-height: auto; padding: 120px 0 60px;">
    <div class="container">
        <div class="hero-text" style="text-align: center; max-width: 800px; margin: 0 auto;">
            <h1>Explore Our Courses</h1>
            <p>Discover a wide range of courses designed to help you develop new skills and advance your career.</p>
        </div>
    </div>
</section>

<section class="section" style="padding-top: 20px;">
    <div class="container">
        <!-- Search & Filter Bar -->
        <div style="background: white; border-radius: 16px; padding: 1.5rem; margin-bottom: 2rem; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
            <form method="GET" action="" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center;">
                <input type="hidden" name="url" value="home/courses">
                <div style="flex: 2; min-width: 250px;">
                    <input type="text" name="search" placeholder="Search courses..." 
                           value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                           style="width: 100%; padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 1rem;">
                </div>
                <div style="flex: 1; min-width: 150px;">
                    <select name="filter" style="width: 100%; padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 1rem; background: white;">
                        <option value="">All Courses</option>
                        <option value="free" <?= ($_GET['filter'] ?? '') === 'free' ? 'selected' : '' ?>>Free Courses</option>
                        <option value="paid" <?= ($_GET['filter'] ?? '') === 'paid' ? 'selected' : '' ?>>Paid Courses</option>
                    </select>
                </div>
                <button type="submit" style="background: #548CA8; color: white; padding: 12px 24px; border: none; border-radius: 10px; font-weight: 600; cursor: pointer;">Search</button>
                <?php if (!empty($_GET['search']) || !empty($_GET['filter'])): ?>
                    <a href="<?= APP_ENTRY ?>?url=home/courses" style="color: #64748b; text-decoration: underline; padding: 12px;">Clear</a>
                <?php endif; ?>
            </form>
        </div>

        <?php if (!empty($_GET['search']) || !empty($_GET['filter'])): ?>
            <p style="margin-bottom: 1rem; color: #64748b;">Showing results 
                <?php if (!empty($_GET['search'])): ?>"<?= htmlspecialchars($_GET['search']) ?>"<?php endif; ?>
                <?php if (!empty($_GET['filter'])): ?>
                    (<?= $_GET['filter'] === 'free' ? 'Free' : 'Paid' ?> only)
                <?php endif; ?>
            </p>
        <?php endif; ?>
        
        <?php if (!empty($courses)): ?>
            <div class="cards-grid" style="grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));">
                <?php foreach ($courses as $course): ?>
                    <div class="course-card">
                        <?php if (!empty($course['image'])): ?>
                            <img src="<?= htmlspecialchars($course['image']) ?>" alt="<?= htmlspecialchars($course['title']) ?>" style="width: 100%; height: 180px; object-fit: cover;">
                        <?php else: ?>
                            <?php $randomImgId = rand(1, 1000); ?>
                            <img src="https://picsum.photos/seed/<?= $randomImgId ?>/400/180" alt="<?= htmlspecialchars($course['title']) ?>" style="width: 100%; height: 180px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="course-content">
                            <div style="display: flex; justify-content: space-between; align-items: start;">
                                <h3 style="flex: 1;"><?= htmlspecialchars($course['title']) ?></h3>
                                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                                    <button onclick="toggleBookmark(<?= $course['id'] ?>)" style="background: none; border: none; cursor: pointer; padding: 4px;" title="Bookmark">
                                        <svg id="bookmark-icon-<?= $course['id'] ?>" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="<?= (isset($bookmarkedCourses) && in_array($course['id'], $bookmarkedCourses)) ? '#f59e0b' : '#64748b' ?>" stroke-width="2">
                                            <path d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2z"/>
                                        </svg>
                                    </button>
                                <?php endif; ?>
                            </div>
                            <p><?= htmlspecialchars(substr($course['description'], 0, 150)) ?>...</p>
                            <div class="course-meta">
                                <span>By: <?= htmlspecialchars($course['creator_name']) ?></span>
                                <span>
                                    <?php if (($course['price'] ?? 0) > 0): ?>
                                        <strong style="color: #22c55e;">$<?= number_format($course['price'], 2) ?></strong>
                                    <?php else: ?>
                                        <strong style="color: #3b82f6;">Free</strong>
                                    <?php endif; ?>
                                </span>
                            </div>
                            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                                <a href="<?= APP_ENTRY ?>?url=student/course/<?= $course['id'] ?>" class="btn btn-primary btn-block" style="margin-top: 15px;">View Course</a>
                            <?php else: ?>
                                <a href="<?= APP_ENTRY ?>?url=login" class="btn btn-secondary btn-block" style="margin-top: 15px;">Login to Enroll</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="table-container" style="max-width: 600px; margin: 0 auto;">
                <div style="padding: 60px; text-align: center;">
                    <svg viewBox="0 0 24 24" width="80" height="80" fill="var(--secondary-color)" style="opacity: 0.5;">
                        <path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8 12.5v-9l6 4.5-6 4.5z"/>
                    </svg>
                    <h3 style="margin-top: 20px; color: var(--primary-color);">No Courses Found</h3>
                    <p style="color: var(--gray-dark); margin-top: 10px;">Try adjusting your search or filter criteria.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
function toggleBookmark(courseId) {
    fetch('<?= APP_ENTRY ?>?url=student/toggle-bookmark', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'course_id=' + courseId
    }).then(r => r.json()).then(data => {
        if (data.success) {
            var icon = document.getElementById('bookmark-icon-' + courseId);
            icon.setAttribute('fill', data.bookmarked ? '#f59e0b' : 'none');
            icon.setAttribute('stroke', data.bookmarked ? '#f59e0b' : '#64748b');
        } else {
            alert(data.message || 'Error');
        }
    }).catch(() => alert('Error toggling bookmark'));
}
</script>