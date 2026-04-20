<?php
$studentSidebarActive = 'chapitres';
$chaptersByCourse = $chaptersByCourse ?? [];
$quizzesByChapter = $quizzesByChapter ?? [];
?>
<div class="dashboard student-learning-page">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main">
                <div class="dashboard-header" style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:12px;">
                    <div>
                        <h1>Mes chapitres</h1>
                        <p style="color:var(--gray-dark);margin:8px 0 0;max-width:640px;">
                            Contenu des cours auxquels vous êtes inscrit. Déployez un chapitre pour lire la leçon et lancer les quiz associés.
                        </p>
                    </div>
                    <a href="<?= APP_URL ?>/index.php?url=student/my-courses" class="btn btn-outline">Mes cours</a>
                </div>

                <?php if (!empty($flash)): ?>
                    <p class="flash flash-<?= htmlspecialchars($flash['type']) ?>" style="margin:16px 0;"><?= htmlspecialchars($flash['message']) ?></p>
                <?php endif; ?>

                <?php if (!empty($chaptersByCourse)): ?>
                    <?php foreach ($chaptersByCourse as $courseBlock): ?>
                        <div class="table-container student-panel" style="margin-top:22px;">
                            <div class="table-header">
                                <h2 style="margin:0;font-size:1.05rem;"><?= htmlspecialchars($courseBlock['course_title'] ?? 'Cours') ?></h2>
                            </div>
                            <div style="padding:8px 0;">
                                <?php foreach ($courseBlock['chapters'] ?? [] as $ch): ?>
                                    <?php
                                    $chid = (int) ($ch['id'] ?? 0);
                                    $qlist = $quizzesByChapter[$chid] ?? [];
                                    ?>
                                    <details class="student-chapter-details">
                                        <summary>
                                            <span class="student-chapter-order"><?= (int) ($ch['sort_order'] ?? 0) ?></span>
                                            <span class="student-chapter-title"><?= htmlspecialchars($ch['title'] ?? '') ?></span>
                                        </summary>
                                        <div class="student-chapter-body">
                                            <div class="student-chapter-content">
                                                <?= nl2br(htmlspecialchars((string) ($ch['content'] ?? '(Contenu à venir.)'))) ?>
                                            </div>
                                            <?php if (!empty($qlist)): ?>
                                                <div class="student-chapter-quizzes">
                                                    <strong>Quiz à passer</strong>
                                                    <ul class="student-quiz-links">
                                                        <?php foreach ($qlist as $qz): ?>
                                                            <li>
                                                                <a href="<?= APP_URL ?>/index.php?url=student/quiz/<?= (int) $qz['id'] ?>"><?= htmlspecialchars($qz['title'] ?? 'Quiz') ?></a>
                                                                <span class="student-quiz-meta">
                                                                    <?= (int) ($qz['question_count'] ?? 0) ?> q.
                                                                    · <?= htmlspecialchars(Quiz::difficultyLabelFr($qz['difficulty'] ?? 'beginner')) ?>
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
                    <div class="table-container student-panel" style="margin-top:24px;padding:28px;">
                        <p style="margin:0;color:var(--gray-dark);">Vous n’avez pas encore de chapitres visibles. Inscrivez-vous à un cours depuis le <a href="<?= APP_URL ?>/index.php?url=student/courses">catalogue</a>.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
