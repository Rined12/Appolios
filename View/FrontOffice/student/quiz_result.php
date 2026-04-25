<?php
$studentSidebarActive = 'quiz';
$pct = (int) ($percentage ?? 0);
$good = $pct >= 70;
$timedOut = !empty($timed_out);
?>
<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main pro-table-page">
                <div class="pro-table-head">
                    <div>
                        <h1>Résultat du quiz</h1>
                        <p><?= htmlspecialchars($quiz['title'] ?? '') ?></p>
                    </div>
                    <div class="pro-table-actions">
                        <a href="<?= APP_ENTRY ?>?url=student/quiz" class="btn btn-outline">Liste des quiz</a>
                    </div>
                </div>

                <?php if ($timedOut): ?>
                    <div class="flash flash-error" style="margin:16px 0;">
                        Temps dépassé : votre tentative a été enregistrée automatiquement.
                    </div>
                <?php endif; ?>

                <div class="pro-table-card" style="padding: 1.2rem;">
                    <div class="student-result-wrap" style="margin-top:0;">
                        <div class="student-result-card <?= $good ? 'student-result-card--ok' : 'student-result-card--retry' ?>">
                            <p class="student-result-kicker">Votre score</p>
                            <p class="student-result-score"><?= (int) $score ?> <span>/</span> <?= (int) $total ?></p>
                            <p class="student-result-percent"><?= $pct ?> %</p>
                            <div class="student-result-bar" aria-hidden="true"><span style="width: <?= max(0, min(100, $pct)) ?>%;"></span></div>
                            <p class="student-result-msg">
                                <?= $good ? 'Bravo ! Vous maîtrisez bien ce sujet.' : 'Continuez à réviser les chapitres et réessayez plus tard.' ?>
                            </p>
                        </div>

                        <div class="student-result-actions">
                            <a href="<?= APP_ENTRY ?>?url=student/quiz" class="btn btn-primary">Autres quiz</a>
                            <a href="<?= APP_ENTRY ?>?url=student/quiz-history" class="btn btn-outline">Historique</a>
                            <a href="<?= APP_ENTRY ?>?url=student/chapitres" class="btn btn-outline">Revoir les chapitres</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

