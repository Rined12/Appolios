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

<section class="section" style="padding-top: 40px;">
    <div class="container">
        <?php if (!empty($courses)): ?>
            <div class="cards-grid" style="grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));">
                <?php foreach ($courses as $course): ?>
                    <div class="course-card">
                        <div class="course-image">
                            <svg viewBox="0 0 24 24" fill="white" opacity="0.9">
                                <path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8 12.5v-9l6 4.5-6 4.5z"/>
                            </svg>
                        </div>
                        <div class="course-content">
                            <h3><?= htmlspecialchars($course['title']) ?></h3>
                            <p><?= htmlspecialchars(substr($course['description'], 0, 150)) ?>...</p>
                            <div class="course-meta">
                                <span>By: <?= htmlspecialchars($course['creator_name']) ?></span>
                                <span><?= date('M Y', strtotime($course['created_at'])) ?></span>
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
                    <h3 style="margin-top: 20px; color: var(--primary-color);">No Courses Available</h3>
                    <p style="color: var(--gray-dark); margin-top: 10px;">New courses are coming soon! Check back later.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>