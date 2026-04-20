<?php
$studentSidebarActive = 'courses';
?>
<div class="dashboard student-learning-page">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main">
                <div class="dashboard-header">
                    <h1>Catalogue des cours</h1>
                    <p style="color:var(--gray-dark);margin:8px 0 0;">Parcourez les cours et inscrivez-vous pour accéder aux chapitres et aux quiz.</p>
                </div>

                <?php if (!empty($flash)): ?>
                    <p class="flash flash-<?= htmlspecialchars($flash['type']) ?>" style="margin:16px 0;"><?= htmlspecialchars($flash['message']) ?></p>
                <?php endif; ?>

                <div class="cards-grid" style="grid-template-columns:repeat(auto-fit,minmax(200px,1fr));margin:24px 0;">
                    <div class="card" style="text-align:center;">
                        <h3 style="font-size:2rem;color:var(--primary-color);margin:0;"><?= count($courses ?? []) ?></h3>
                        <p style="color:var(--gray-dark);margin:0;">Cours proposés</p>
                    </div>
                    <div class="card" style="text-align:center;">
                        <h3 style="font-size:2rem;color:var(--secondary-color);margin:0;"><?= count($enrolledIds ?? []) ?></h3>
                        <p style="color:var(--gray-dark);margin:0;">Mes inscriptions</p>
                    </div>
                </div>

                <?php if (!empty($courses)): ?>
                    <div class="cards-grid" style="grid-template-columns:repeat(auto-fill,minmax(300px,1fr));">
                        <?php foreach ($courses as $course): ?>
                            <?php
                            $desc = (string) ($course['description'] ?? '');
                            $excerpt = $desc !== '' ? (strlen($desc) > 140 ? substr($desc, 0, 140) . '…' : $desc) : '';
                            $enrolled = in_array((int) $course['id'], array_map('intval', $enrolledIds ?? []), true);
                            ?>
                            <article class="card student-course-card" style="display:flex;flex-direction:column;">
                                <div class="card-icon" style="width:56px;height:56px;margin-bottom:12px;">
                                    <svg viewBox="0 0 24 24" fill="white" aria-hidden="true">
                                        <path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/>
                                    </svg>
                                </div>
                                <h3 style="font-size:1.15rem;margin:0 0 8px;"><?= htmlspecialchars($course['title']) ?></h3>
                                <p style="color:var(--gray-dark);font-size:0.9rem;margin:0 0 10px;">
                                    Par <?= htmlspecialchars($course['creator_name'] ?? '') ?>
                                </p>
                                <?php if ($excerpt !== ''): ?>
                                    <p style="font-size:0.9rem;line-height:1.55;flex:1;color:var(--gray-dark);margin:0;"><?= htmlspecialchars($excerpt) ?></p>
                                <?php endif; ?>
                                <div style="margin-top:18px;padding-top:14px;border-top:1px solid var(--gray);">
                                    <?php if ($enrolled): ?>
                                        <a href="<?= APP_URL ?>/index.php?url=student/course/<?= (int) $course['id'] ?>" class="btn btn-secondary btn-block">Continuer le cours</a>
                                        <p style="text-align:center;margin:8px 0 0;font-size:0.85rem;color:var(--secondary-color);">Déjà inscrit</p>
                                    <?php else: ?>
                                        <a href="<?= APP_URL ?>/index.php?url=student/course/<?= (int) $course['id'] ?>" class="btn btn-primary btn-block">Voir le cours</a>
                                    <?php endif; ?>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="table-container" style="text-align:center;padding:48px 20px;">
                        <h3 style="color:var(--primary-color);margin:0 0 8px;">Aucun cours pour le moment</h3>
                        <p style="color:var(--gray-dark);margin:0;">Revenez plus tard ou contactez l’administration.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
