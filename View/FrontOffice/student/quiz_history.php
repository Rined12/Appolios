<?php
$studentSidebarActive = 'quiz';
?>
<div class="dashboard student-learning-page">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main">
                <div class="dashboard-header" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
                    <div>
                        <h1>Historique des quiz</h1>
                        <p style="color:var(--gray-dark);margin:8px 0 0;">Toutes vos tentatives enregistrées.</p>
                    </div>
                    <a href="<?= APP_ENTRY ?>?url=student/quiz" class="btn btn-outline">← Liste des quiz</a>
                </div>
                <?php if (!empty($flash)): ?>
                    <p class="flash flash-<?= htmlspecialchars($flash['type']) ?>" style="margin:16px 0;"><?= htmlspecialchars($flash['message']) ?></p>
                <?php endif; ?>
                <div class="table-container student-panel" style="margin-top:20px;">
                    <?php if (!empty($attempts)): ?>
                        <div style="overflow-x:auto;">
                            <table class="data-table" style="width:100%;">
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
                                        <tr>
                                            <td><?= htmlspecialchars($a['quiz_title'] ?? '') ?></td>
                                            <td><?= (int) $a['score'] ?> / <?= (int) $a['total'] ?></td>
                                            <td><strong><?= (int) $a['percentage'] ?></strong></td>
                                            <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime((string) ($a['submitted_at'] ?? 'now')))) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p style="padding:28px;margin:0;color:var(--gray-dark);">Aucune tentative enregistrée. Passez un quiz depuis la liste pour voir vos résultats ici.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

