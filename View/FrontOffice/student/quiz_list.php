<?php
$studentSidebarActive = 'quiz';
$quizzes = $quizzes ?? [];

$quizzesByCourse = [];
foreach ($quizzes as $q) {
    $cid = (int) ($q['course_id'] ?? 0);
    if (!isset($quizzesByCourse[$cid])) {
        $quizzesByCourse[$cid] = [
            'course_title' => $q['course_title'] ?? '',
            'rows' => [],
        ];
    }
    $quizzesByCourse[$cid]['rows'][] = $q;
}
ksort($quizzesByCourse, SORT_NUMERIC);
?>
<div class="dashboard student-learning-page">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main">
                <div class="dashboard-header" style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:12px;">
                    <div>
                        <h1>Mes quiz</h1>
                        <p style="color:var(--gray-dark);margin:8px 0 0;max-width:640px;">
                            Quiz des cours où vous êtes inscrit. Chaque tentative est enregistrée dans votre historique.
                        </p>
                    </div>
                    <div style="display:flex;flex-wrap:wrap;gap:8px;">
                        <a href="<?= APP_ENTRY ?>?url=student/quiz-history" class="btn btn-outline">Historique</a>
                        <a href="<?= APP_ENTRY ?>?url=student/chapitres" class="btn btn-outline">Chapitres</a>
                    </div>
                </div>

                <?php if (!empty($flash)): ?>
                    <p class="flash flash-<?= htmlspecialchars($flash['type']) ?>" style="margin:16px 0;"><?= htmlspecialchars($flash['message']) ?></p>
                <?php endif; ?>

                <?php if (!empty($quizzesByCourse)): ?>
                    <?php foreach ($quizzesByCourse as $block): ?>
                        <div class="table-container student-panel" style="margin-top:20px;">
                            <div class="table-header">
                                <h2 style="margin:0;font-size:1.05rem;"><?= htmlspecialchars($block['course_title'] ?? '') ?></h2>
                            </div>
                            <div style="overflow-x:auto;">
                                <table class="data-table student-quiz-table" style="width:100%;">
                                    <thead>
                                        <tr>
                                            <th>Quiz</th>
                                            <th>Chapitre</th>
                                            <th>Infos</th>
                                            <th style="width:1%;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($block['rows'] as $q): ?>
                                            <tr>
                                                <td><strong><?= htmlspecialchars($q['title'] ?? '') ?></strong></td>
                                                <td><?= htmlspecialchars($q['chapter_title'] ?? '') ?></td>
                                                <td>
                                                    <span class="student-pill"><?= (int) ($q['question_count'] ?? 0) ?> question(s)</span>
                                                    <span class="student-pill student-pill--muted"><?= htmlspecialchars(Quiz::difficultyLabelFr($q['difficulty'] ?? 'beginner')) ?></span>
                                                    <?php if (!empty($q['time_limit_sec'])): ?>
                                                        <span class="student-pill student-pill--muted"><?= (int) $q['time_limit_sec'] ?> s max</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a class="btn btn-primary" style="padding:6px 14px;white-space:nowrap;" href="<?= APP_ENTRY ?>?url=student/quiz/<?= (int) $q['id'] ?>">Commencer</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="table-container student-panel" style="margin-top:24px;padding:28px;">
                        <p style="margin:0;color:var(--gray-dark);">Aucun quiz pour l’instant. Inscrivez-vous à un cours qui propose des quiz, ou revenez plus tard.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

