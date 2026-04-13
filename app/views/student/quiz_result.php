<?php
$studentSidebarActive = 'quiz';
$pct = (int) ($percentage ?? 0);
$good = $pct >= 70;
?>
<div class="dashboard student-learning-page">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main">
                <h1>Résultat du quiz</h1>
                <p style="color:var(--gray-dark);margin:8px 0 0;"><?= htmlspecialchars($quiz['title'] ?? '') ?></p>

                <div class="student-result-card <?= $good ? 'student-result-card--ok' : 'student-result-card--retry' ?>" style="max-width:440px;margin:24px 0;padding:28px;border-radius:12px;text-align:center;">
                    <p style="margin:0;font-size:0.95rem;color:var(--gray-dark);">Votre score</p>
                    <p style="font-size:2.75rem;font-weight:700;margin:8px 0;line-height:1;"><?= (int) $score ?> <span style="font-size:1.5rem;opacity:0.7;">/</span> <?= (int) $total ?></p>
                    <p style="font-size:1.75rem;font-weight:600;margin:0;color:var(--primary-color);"><?= $pct ?> %</p>
                    <p style="margin:16px 0 0;font-size:0.95rem;">
                        <?= $good
                            ? 'Bravo ! Vous maîtrisez bien ce sujet.'
                            : 'Continuez à réviser les chapitres et réessayez plus tard.' ?>
                    </p>
                </div>

                <div style="display:flex;flex-wrap:wrap;gap:10px;">
                    <a href="<?= APP_URL ?>/index.php?url=student/quiz" class="btn btn-primary">Autres quiz</a>
                    <a href="<?= APP_URL ?>/index.php?url=student/quiz-history" class="btn btn-outline">Historique</a>
                    <a href="<?= APP_URL ?>/index.php?url=student/chapitres" class="btn btn-outline">Revoir les chapitres</a>
                </div>
            </div>
        </div>
    </div>
</div>
