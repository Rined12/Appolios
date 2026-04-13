<?php
$studentSidebarActive = 'my-courses';
?>
<div class="dashboard student-learning-page">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main">
                <div class="dashboard-header" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:14px;">
                    <div>
                        <h1>Mes cours</h1>
                        <p style="color:var(--gray-dark);margin:8px 0 0;">Accédez aux chapitres, contenus et quiz de chaque cours.</p>
                    </div>
                    <a href="<?= APP_URL ?>/index.php?url=student/courses" class="btn btn-outline">Catalogue</a>
                </div>

                <?php if (!empty($flash)): ?>
                    <p class="flash flash-<?= htmlspecialchars($flash['type']) ?>" style="margin:16px 0;"><?= htmlspecialchars($flash['message']) ?></p>
                <?php endif; ?>

                <?php if (!empty($enrollments)): ?>
                    <div class="cards-grid" style="grid-template-columns:repeat(auto-fill,minmax(300px,1fr));margin-top:24px;">
                        <?php foreach ($enrollments as $enrollment): ?>
                            <?php
                            $desc = (string) ($enrollment['description'] ?? '');
                            $excerpt = $desc !== '' ? (strlen($desc) > 120 ? substr($desc, 0, 120) . '…' : $desc) : '';
                            ?>
                            <article class="card student-course-card" style="display:flex;flex-direction:column;">
                                <div class="card-icon" style="width:56px;height:56px;margin-bottom:12px;">
                                    <svg viewBox="0 0 24 24" fill="white" aria-hidden="true">
                                        <path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8 12.5v-9l6 4.5-6 4.5z"/>
                                    </svg>
                                </div>
                                <h3 style="font-size:1.15rem;margin:0 0 8px;"><?= htmlspecialchars($enrollment['title'] ?? '') ?></h3>
                                <?php if ($excerpt !== ''): ?>
                                    <p style="font-size:0.9rem;color:var(--gray-dark);line-height:1.5;flex:1;margin:0;"><?= htmlspecialchars($excerpt) ?></p>
                                <?php endif; ?>

                                <div style="margin-top:16px;">
                                    <div style="display:flex;justify-content:space-between;margin-bottom:6px;font-size:0.85rem;color:var(--gray-dark);">
                                        <span>Progression</span>
                                        <span style="font-weight:600;"><?= (int) ($enrollment['progress'] ?? 0) ?> %</span>
                                    </div>
                                    <div style="background:var(--gray-light);border-radius:10px;height:8px;overflow:hidden;">
                                        <div style="background:linear-gradient(90deg,var(--secondary-color),var(--primary-color));height:100%;width:<?= min(100, max(0, (int) ($enrollment['progress'] ?? 0))) ?>%;"></div>
                                    </div>
                                </div>

                                <p style="margin:14px 0 0;font-size:0.85rem;color:var(--gray-dark);">
                                    Inscrit le <?= date('d/m/Y', strtotime((string) ($enrollment['enrolled_at'] ?? 'now'))) ?>
                                </p>

                                <a href="<?= APP_URL ?>/index.php?url=student/course/<?= (int) ($enrollment['course_id'] ?? 0) ?>" class="btn btn-primary btn-block" style="margin-top:16px;">Ouvrir le cours</a>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="table-container" style="margin-top:24px;text-align:center;padding:48px 24px;">
                        <h3 style="color:var(--primary-color);margin:0 0 8px;">Aucun cours suivi</h3>
                        <p style="color:var(--gray-dark);margin:0 0 20px;">Parcourez le catalogue et inscrivez-vous pour commencer.</p>
                        <a href="<?= APP_URL ?>/index.php?url=student/courses" class="btn btn-yellow">Voir le catalogue</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
