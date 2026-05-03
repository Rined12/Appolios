<?php
/**
 * APPOLIOS - Student Browse All Courses
 */
?>

<div class="dashboard student-courses-page">
    <div class="container">
        <div class="section student-courses-hero">
            <div class="student-courses-hero-copy">
                <span class="student-courses-kicker">eLearning Platform</span>
                <h1>Smart Learning<br>Deeper & More <span>Amazing</span></h1>
                <p>Learn through practical courses, live mentoring, and project-focused paths built for real progress.</p>
                <div class="student-courses-hero-actions">
                    <a href="<?= APP_ENTRY ?>?url=register" class="student-courses-hero-btn student-courses-hero-btn-primary">Start Free Trial</a>
                    <a href="<?= APP_ENTRY ?>?url=courses" class="student-courses-hero-btn student-courses-hero-btn-ghost">How it Work</a>
                </div>
            </div>

            <div class="student-courses-hero-visual" aria-hidden="true">
                <div class="student-courses-hero-shape student-courses-hero-shape-teal"></div>
                <div class="student-courses-hero-shape student-courses-hero-shape-orange"></div>
                <img src="<?= APP_URL ?>/View/assets/images/instructor/06.jpg" alt="Student hero" class="student-courses-hero-photo">
            </div>
        </div>

        <div class="section student-courses-impact">
            <span class="student-courses-impact-kicker">About Us</span>
            <h2>We are passionate about empowering learners worldwide with high-quality and accessible learning experiences.</h2>
            <div class="student-courses-impact-stats">
                <div class="article">
                    <strong>25+</strong>
                    <p>Years of learning experience</p>
                </div>
                <div class="article">
                    <strong><?= count($courses) ?></strong>
                    <p>Courses available now</p>
                </div>
                <div class="article">
                    <strong><?= count($enrolledIds) ?></strong>
                    <p>Courses enrolled by you</p>
                </div>
            </div>
        </div>

        <div class="dashboard-header student-courses-header">
            <div>
                <span class="student-courses-section-chip">Our Course</span>
                <h3>Explore Our Course</h3>
                <p>Browse and enroll in courses to start learning.</p>
            </div>
            <form class="student-courses-search" action="<?= APP_ENTRY ?>" method="get">
                <input type="hidden" name="url" value="courses">
                <input type="search" name="q" placeholder="Search Courses" aria-label="Search Courses">
                <button type="submit" aria-label="Search"><i class="bi bi-search"></i></button>
            </form>
        </div>

        <?php if (!empty($courses)): ?>
            <div class="cards-grid student-courses-grid">
                <?php foreach ($courses as $course): ?>
                    <div class="article card student-course-card">
                        <div class="card-icon student-course-icon">
                            <svg viewBox="0 0 24 24" fill="white">
                                <path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/>
                            </svg>
                        </div>
                        <h3><?= htmlspecialchars($course['title']) ?></h3>
                        <p class="student-course-author">
                            By <?= htmlspecialchars($course['creator_name']) ?>
                        </p>
                        <?php
                        $courseDescription = (string) ($course['description'] ?? '');
                        $coursePreview = strlen($courseDescription) > 120
                            ? substr($courseDescription, 0, 120) . '...'
                            : $courseDescription;
                        ?>
                        <p class="student-course-description">
                            <?= htmlspecialchars($coursePreview) ?>
                        </p>

                        <div class="student-course-footer">
                            <?php if (in_array($course['id'], $enrolledIds)): ?>
                                <a href="<?= APP_ENTRY ?>?url=student/course/<?= $course['id'] ?>" class="btn btn-secondary btn-block student-course-btn">
                                    Continue Learning →
                                </a>
                                <p class="student-course-enrolled">
                                    ✓ Already enrolled
                                </p>
                            <?php else: ?>
                                <a href="<?= APP_ENTRY ?>?url=student/course/<?= $course['id'] ?>" class="btn btn-primary btn-block student-course-btn">
                                    View Course Details
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="student-courses-empty">
                <div class="student-courses-empty-icon">
                    <svg viewBox="0 0 24 24" width="40" height="40" fill="var(--gray-dark)">
                        <path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/>
                    </svg>
                </div>
                <h3>No Courses Available Yet</h3>
                <p>Check back later for new courses!</p>
            </div>
        <?php endif; ?>
    </div>
</div>
