<?php
/**
 * APPOLIOS - Student My Courses Page
 */
$studentSidebarActive = 'my-courses';
?>

<div class="dashboard student-learning-page">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>

            <div class="admin-main">
                <div class="dashboard-header" style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:12px;">
                    <div>
                        <h1>Mes cours</h1>
                        <p>Retrouvez tous vos cours inscrits et continuez votre apprentissage.</p>
                    </div>
                    <a href="<?= APP_ENTRY ?>?url=student/courses" class="btn btn-primary">Voir plus de cours</a>
                </div>

                <?php if (!empty($enrollments)): ?>
                    <div class="student-cards-grid">
                        <?php foreach ($enrollments as $enrollment): ?>
                            <?php $progress = max(0, min(100, (int) ($enrollment['progress'] ?? 0))); ?>
                            <article class="student-course-card">
                                <div style="width:56px;height:56px;border-radius:14px;background:linear-gradient(135deg,#e9f3ff 0%,#d7ebff 100%);color:#2d6aa6;display:flex;align-items:center;justify-content:center;margin-bottom:14px;">
                                    <svg viewBox="0 0 24 24" width="28" height="28" fill="currentColor">
                                        <path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8 12.5v-9l6 4.5-6 4.5z"/>
                                    </svg>
                                </div>
                                <h3 style="font-size:1.2rem;"><?= htmlspecialchars($enrollment['title']) ?></h3>
                                <p style="font-size:0.94rem;color:#64748b;margin:10px 0 16px;">
                                    <?= htmlspecialchars(mb_strimwidth((string) ($enrollment['description'] ?? ''), 0, 130, '...')) ?>
                                </p>

                                <div style="margin-bottom:16px;">
                                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                                        <span style="font-size:0.88rem;color:#64748b;font-weight:600;">Progression</span>
                                        <span style="font-size:0.88rem;font-weight:700;color:#1e3a6d;"><?= $progress ?>%</span>
                                    </div>
                                    <div style="background:#e2e8f0;border-radius:999px;height:10px;overflow:hidden;">
                                        <div style="background:linear-gradient(90deg,#4f8ecf,#6ab5f0);height:100%;width:<?= $progress ?>%;"></div>
                                    </div>
                                </div>

                                <div class="student-soft-box" style="margin-bottom:16px;font-size:0.88rem;color:#475569;">
                                    Inscrit le <?= date('d/m/Y', strtotime((string) $enrollment['enrolled_at'])) ?>
                                </div>

                                <a href="<?= APP_ENTRY ?>?url=student/course/<?= (int) $enrollment['course_id'] ?>" class="btn btn-primary btn-block">
                                    Continuer
                                </a>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="student-page-card" style="text-align:center;">
                        <h3 style="margin-bottom:12px;">Aucun cours pour le moment</h3>
                        <p style="color:#64748b;margin:0 0 18px;">Vous n’êtes inscrit à aucun cours. Parcourez le catalogue pour commencer.</p>
                        <a href="<?= APP_ENTRY ?>?url=student/courses" class="btn btn-primary">Parcourir les cours</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>