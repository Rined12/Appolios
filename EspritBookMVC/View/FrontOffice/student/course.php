<?php
/**
 * APPOLIOS - Student Course View (Premium Neo Design)
 */
?>

<div class="section neo-page">
    <div class="neo-container neo-main-stack">
        <div class="header neo-glass-card neo-toolbar">
            <div>
                <p style="margin: 0 0 0.4rem;"><a class="neo-btn neo-btn-outline" href="<?= APP_ENTRY ?>?url=student/dashboard"><i class="bi bi-arrow-left"></i>Back to Dashboard</a></p>
                <h1><?= htmlspecialchars($course['title']) ?></h1>
                <p>By <?= htmlspecialchars($course['creator_name'] ?? 'Instructor') ?></p>
            </div>
            <?php if ($isEnrolled): ?>
                <span class="neo-badge success" style="font-size: 0.82rem;">Enrolled</span>
            <?php endif; ?>
        </div>

        <div class="neo-split">
            <div class="neo-main-stack">
                <div class="section neo-glass-card" style="padding: 1rem;">
                    <div class="neo-section-title">
                        <h2 style="font-size: 1.25rem;">Course Overview</h2>
                    </div>
                    <p class="neo-muted" style="margin-top: 0.55rem; line-height: 1.7;"><?= nl2br(htmlspecialchars((string) ($course['description'] ?? 'Course content will be updated soon.'))) ?></p>
                </div>

                <div class="section neo-glass-card" style="padding: 1rem;">
                    <div class="neo-section-title" style="margin-bottom: 0.75rem;">
                        <h2 style="font-size: 1.25rem;">Video Lesson</h2>
                    </div>
                    <div class="neo-video-frame">
                        <?php $cv = $course_video_payload ?? ['type' => 'none']; ?>
                        <?php if (($cv['type'] ?? '') === 'youtube'): ?>
                            <iframe src="<?= htmlspecialchars((string) ($cv['embed_url'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" allowfullscreen></iframe>
                        <?php elseif (($cv['type'] ?? '') === 'mp4'): ?>
                            <?php $videoUrl = htmlspecialchars((string) ($cv['src'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                            <object data="<?= $videoUrl ?>" type="video/mp4" width="640" height="360">
                                <param name="src" value="<?= $videoUrl ?>">
                                <param name="autoplay" value="false">
                                <param name="controls" value="true">
                                <a href="<?= $videoUrl ?>">Download video</a>
                            </object>
                        <?php else: ?>
                            <div style="height: 360px; display: grid; place-items: center; color: #94a3b8;">Video coming soon.</div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="neo-split" style="grid-template-columns: 1fr 1fr;">
                    <div class="section neo-glass-card neo-comments">
                        <h3 style="margin: 0 0 0.65rem; font-size: 1.1rem; font-family: 'Poppins', 'Inter', sans-serif;">Comments</h3>
                        <div class="article neo-comment-item">
                            <strong style="font-size: 0.88rem; color: #e2e8f0;">Nour</strong>
                            <p class="neo-muted" style="margin: 0.25rem 0 0; font-size: 0.88rem;">Excellent explanation of architecture patterns.</p>
                        </div>
                        <div class="article neo-comment-item">
                            <strong style="font-size: 0.88rem; color: #e2e8f0;">Yassine</strong>
                            <p class="neo-muted" style="margin: 0.25rem 0 0; font-size: 0.88rem;">Loved the practical examples and pacing.</p>
                        </div>
                    </div>
                    <div class="section neo-glass-card neo-notes">
                        <h3 style="margin: 0 0 0.65rem; font-size: 1.1rem; font-family: 'Poppins', 'Inter', sans-serif;">Personal Notes</h3>
                        <textarea placeholder="Write your learning notes here..."></textarea>
                    </div>
                </div>
            </div>

            <div class="aside neo-main-stack">
                <div class="section neo-glass-card neo-profile-card">
                    <h3 style="margin: 0; font-size: 1.14rem; font-family: 'Poppins', 'Inter', sans-serif;">Progress Tracking</h3>
                    <div class="neo-progress-wrap" style="margin-top: 0.8rem;">
                        <div class="neo-progress-track"><div class="neo-progress-value" style="width: <?= $isEnrolled ? 65 : 15 ?>%;"></div></div>
                        <div class="neo-progress-meta"><?= $isEnrolled ? '65% completed' : 'Enroll to start progress' ?></div>
                    </div>
                    <?php if ($isEnrolled): ?>
                        <a class="neo-btn neo-btn-primary" style="margin-top: 0.9rem; width: 100%;" href="#">Continue Lesson</a>
                    <?php else: ?>
                        <a class="neo-btn neo-btn-warning" style="margin-top: 0.9rem; width: 100%;" href="<?= APP_ENTRY ?>?url=student/enroll/<?= (int) $course['id'] ?>">Enroll Now</a>
                    <?php endif; ?>
                </div>

                <div class="section neo-glass-card neo-profile-card">
                    <h3 style="margin: 0; font-size: 1.14rem; font-family: 'Poppins', 'Inter', sans-serif;">Achievements & Badges</h3>
                    <p class="neo-muted" style="margin: 0.35rem 0 0;">Earn badges by completing lessons and quizzes.</p>
                    <div class="neo-badges">
                        <span class="neo-badge primary">Course Explorer</span>
                        <span class="neo-badge success">Quick Starter</span>
                        <span class="neo-badge warning">Top Performer</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>