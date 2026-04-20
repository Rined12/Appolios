<?php
$studentSidebarActive = 'questions';
$questions = $questions ?? [];
?>
<div class="dashboard student-learning-page">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main">
                <div class="dashboard-header" style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:12px;">
                    <div>
                        <h1>Banque de questions</h1>
                        <p style="color:var(--gray-dark);margin:8px 0 0;max-width:640px;">
                            Entraînez-vous en lisant les énoncés et les propositions (révision). Les quiz notés se passent depuis l’onglet <strong>Quiz</strong> : c’est là que vous soumettez vos réponses pour obtenir un score.
                        </p>
                    </div>
                    <a href="<?= APP_URL ?>/index.php?url=student/quiz" class="btn btn-primary">Aller aux quiz</a>
                </div>

                <?php if (!empty($flash)): ?>
                    <p class="flash flash-<?= htmlspecialchars($flash['type']) ?>" style="margin:16px 0;"><?= htmlspecialchars($flash['message']) ?></p>
                <?php endif; ?>

                <?php if (!empty($questions)): ?>
                    <div class="student-qbank-grid" style="margin-top:22px;display:flex;flex-direction:column;gap:16px;">
                        <?php foreach ($questions as $idx => $q): ?>
                            <details class="table-container student-panel student-qbank-item" <?= $idx === 0 ? 'open' : '' ?>>
                                <summary class="student-qbank-summary">
                                    <span class="student-qbank-num">#<?= (int) ($q['id'] ?? $idx + 1) ?></span>
                                    <span class="student-qbank-head">
                                        <strong><?= htmlspecialchars($q['title'] ?: 'Question') ?></strong>
                                        <span class="student-muted"> · <?= htmlspecialchars($q['author_name'] ?? '') ?></span>
                                        <?php if (!empty($q['difficulty'])): ?>
                                            <span class="student-pill student-pill--muted" style="margin-left:8px;"><?= htmlspecialchars(Quiz::difficultyLabelFr($q['difficulty'])) ?></span>
                                        <?php endif; ?>
                                    </span>
                                </summary>
                                <div style="padding:0 20px 20px;border-top:1px solid var(--gray);">
                                    <p class="student-quiz-qtext" style="margin-top:16px;"><?= htmlspecialchars($q['question_text'] ?? '') ?></p>
                                    <p style="font-size:0.85rem;color:var(--gray-dark);margin:12px 0 8px;">Propositions :</p>
                                    <ol class="student-qbank-options">
                                        <?php foreach ($q['options'] ?? [] as $o): ?>
                                            <li><?= htmlspecialchars($o) ?></li>
                                        <?php endforeach; ?>
                                    </ol>
                                </div>
                            </details>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="table-container student-panel" style="margin-top:24px;padding:28px;">
                        <p style="margin:0;color:var(--gray-dark);">La banque est vide pour le moment.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
