<?php
$studentSidebarActive = 'quiz';
?>
<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main pro-table-page">
                <div class="pro-table-head">
                    <div>
                        <h1>Historique des quiz</h1>
                        <p>Toutes vos tentatives enregistrées.</p>
                    </div>
                    <div class="pro-table-actions">
                        <a href="<?= APP_ENTRY ?>?url=student/quiz" class="btn btn-outline">← Liste des quiz</a>
                    </div>
                </div>
                <?php if (!empty($flash)): ?>
                    <p class="flash flash-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></p>
                <?php endif; ?>
                <div class="pro-table-card">
                    <?php if (!empty($attempts)): ?>
                        <div class="pro-table-wrap">
                            <table class="pro-table student-history-table" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th>Quiz</th>
                                        <th>Score</th>
                                        <th>%</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($attempts as $a): ?>
                                        <?php
                                            $p = (int) ($a['percentage'] ?? 0);
                                            $pill = 'student-history-pill';
                                            if ($p >= 70) { $pill .= ' student-history-pill--high'; }
                                            elseif ($p >= 40) { $pill .= ' student-history-pill--mid'; }
                                            else { $pill .= ' student-history-pill--low'; }
                                        ?>
                                        <tr>
                                            <td><?= htmlspecialchars($a['quiz_title'] ?? '') ?></td>
                                            <td><span class="student-history-score"><?= (int) $a['score'] ?> / <?= (int) $a['total'] ?></span></td>
                                            <td><span class="<?= $pill ?>"><?= $p ?>%</span></td>
                                            <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime((string) ($a['submitted_at'] ?? 'now')))) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p style="padding: 1.2rem;margin:0;color: rgba(226, 232, 240, 0.7);">Aucune tentative enregistrée. Passez un quiz depuis la liste pour voir vos résultats ici.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

