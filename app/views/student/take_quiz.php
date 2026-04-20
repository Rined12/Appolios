<?php
$studentSidebarActive = 'quiz';
$quizId = (int) ($quiz['id'] ?? 0);
$nq = count($quiz['questions'] ?? []);
$tl = isset($quiz['time_limit_sec']) && (int) $quiz['time_limit_sec'] > 0 ? (int) $quiz['time_limit_sec'] : null;
?>
<div class="dashboard student-learning-page">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main">
                <nav class="student-breadcrumb" aria-label="Fil d’Ariane">
                    <a href="<?= APP_URL ?>/index.php?url=student/quiz">Quiz</a>
                    <span aria-hidden="true"> / </span>
                    <span><?= htmlspecialchars($quiz['title'] ?? 'Quiz') ?></span>
                </nav>
                <h1 style="margin:8px 0 12px;"><?= htmlspecialchars($quiz['title'] ?? 'Quiz') ?></h1>
                <div class="student-quiz-intro">
                    <?php if (!empty($quiz['course_title'] ?? '')): ?>
                        <span><?= htmlspecialchars($quiz['course_title']) ?></span>
                    <?php endif; ?>
                    <?php if (!empty($quiz['chapter_title'] ?? '')): ?>
                        <span> · <?= htmlspecialchars($quiz['chapter_title']) ?></span>
                    <?php endif; ?>
                    <span> · <?= $nq ?> question(s)</span>
                    <span> · <?= htmlspecialchars(Quiz::difficultyLabelFr($quiz['difficulty'] ?? 'beginner')) ?></span>
                    <?php if ($tl): ?>
                        <span> · Temps indicatif : <?= $tl ?> s</span>
                    <?php endif; ?>
                </div>

                <?php if (!empty($flash)): ?>
                    <p class="flash flash-<?= htmlspecialchars($flash['type']) ?>" style="margin:16px 0;"><?= htmlspecialchars($flash['message']) ?></p>
                <?php endif; ?>

                <form method="post" action="<?= APP_URL ?>/index.php?url=student/quiz/<?= $quizId ?>/submit" class="table-container student-panel" style="padding:24px;margin-top:20px;">
                    <?php foreach ($quiz['questions'] as $i => $q): ?>
                        <fieldset class="student-quiz-question">
                            <legend>Question <?= $i + 1 ?> / <?= $nq ?></legend>
                            <p class="student-quiz-qtext"><?= htmlspecialchars($q['question'] ?? '') ?></p>
                            <?php $opts = $q['options'] ?? []; foreach ($opts as $oi => $opt): ?>
                                <label class="student-quiz-option">
                                    <input type="radio" name="answers[<?= $i ?>]" value="<?= $oi ?>" required>
                                    <span><?= htmlspecialchars($opt) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </fieldset>
                    <?php endforeach; ?>
                    <div style="margin-top:8px;display:flex;flex-wrap:wrap;gap:10px;align-items:center;">
                        <button type="submit" class="btn btn-primary">Soumettre mes réponses</button>
                        <a href="<?= APP_URL ?>/index.php?url=student/quiz" class="btn btn-outline">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
