<?php
$studentSidebarActive = 'chapitres';
$chaptersByCourse = $chaptersByCourse ?? [];
$quizzesByChapter = $quizzesByChapter ?? [];
?>
<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main pro-table-page">
                <div class="pro-table-head">
                    <div>
                        <h1>Mes chapitres</h1>
                        <p>Contenu des cours auxquels vous êtes inscrit. Déployez un chapitre pour lire la leçon et lancer les quiz associés.</p>
                    </div>
                    <div class="pro-table-actions">
                        <a href="<?= APP_ENTRY ?>?url=student/my-courses" class="btn btn-outline">Mes cours</a>
                    </div>
                </div>

                <?php if (!empty($flash)): ?>
                    <p class="flash flash-<?= htmlspecialchars($flash['type']) ?>" style="margin:16px 0;"><?= htmlspecialchars($flash['message']) ?></p>
                <?php endif; ?>

                <?php if (!empty($chaptersByCourse)): ?>
                    <?php foreach ($chaptersByCourse as $courseBlock): ?>
                        <div class="pro-table-card" style="margin-top: 16px;">
                            <div class="pro-course-head">
                                <h2><?= htmlspecialchars($courseBlock['course_title'] ?? 'Cours') ?></h2>
                            </div>
                            <div style="padding: 0.4rem 0.95rem 1rem;">
                                <?php foreach ($courseBlock['chapters'] ?? [] as $ch): ?>
                                    <?php
                                    $chid = (int) ($ch['id'] ?? 0);
                                    $qlist = $quizzesByChapter[$chid] ?? [];
                                    ?>
                                    <details class="pro-chapter-details">
                                        <summary>
                                            <span class="pro-chapter-order"><?= (int) ($ch['sort_order'] ?? 0) ?></span>
                                            <span class="pro-chapter-title"><?= htmlspecialchars($ch['title'] ?? '') ?></span>
                                        </summary>
                                        <div class="pro-chapter-body">
                                            <div class="pro-chapter-content">
                                                <?= nl2br(htmlspecialchars((string) ($ch['content'] ?? '(Contenu à venir.)'))) ?>
                                            </div>
                                            <?php if (!empty($qlist)): ?>
                                                <div class="pro-chapter-quizzes">
                                                    <strong>Quiz à passer</strong>
                                                    <ul class="pro-quiz-links">
                                                        <?php foreach ($qlist as $qz): ?>
                                                            <li>
                                                                <a href="<?= APP_ENTRY ?>?url=student-quiz/quiz/<?= (int) $qz['id'] ?>"><?= htmlspecialchars($qz['title'] ?? 'Quiz') ?></a>
                                                                <span class="pro-quiz-meta">
                                                                    <?= (int) ($qz['question_count'] ?? 0) ?> q.
                                                                    · <?= htmlspecialchars(difficulty_label_fr((string) ($qz['difficulty'] ?? 'beginner'))) ?>
                                                                </span>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </details>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="pro-table-card" style="padding: 1.2rem;">
                        <p style="margin:0;color: rgba(226, 232, 240, 0.7);">Vous n’avez pas encore de chapitres visibles. Inscrivez-vous à un cours depuis le <a href="<?= APP_ENTRY ?>?url=student/courses">catalogue</a>.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

