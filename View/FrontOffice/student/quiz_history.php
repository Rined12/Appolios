<?php
$studentSidebarActive = 'quiz';
?>
<style>
.student-quiz-browse-page .student-quiz-history-page h1 {
    font-size: 1.75rem;
    font-weight: 800;
    color: #1e293b;
    margin: 0 0 0.35rem 0;
}
.student-quiz-browse-page .student-quiz-history-page > p {
    color: #64748b;
    font-size: 0.95rem;
    margin: 0 0 1rem 0;
}
.student-quiz-browse-page .student-quiz-history-page .qh-back {
    display: inline-flex;
    align-items: center;
    padding: 8px 14px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    background: #fff;
    color: #475569;
    font-weight: 600;
    text-decoration: none;
}
.student-quiz-browse-page .student-quiz-history-page .qh-back:hover {
    background: #f8fafc;
    color: #1e293b;
}
.student-quiz-browse-page .student-quiz-history-page .pro-table-card {
    background: #fff;
    border: 1px solid #eef2f6;
    border-radius: 16px;
    box-shadow: 0 4px 15px rgba(15, 23, 42, 0.06);
    overflow: hidden;
}
.student-quiz-browse-page .student-quiz-history-page .student-history-table th {
    background: #f8fafc;
    color: #64748b;
    font-weight: 700;
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    border-bottom: 1px solid #e2e8f0;
    padding: 12px 16px;
}
.student-quiz-browse-page .student-quiz-history-page .student-history-table td {
    color: #334155;
    border-bottom: 1px solid #f1f5f9;
    padding: 14px 16px;
    font-size: 0.92rem;
}
.student-quiz-browse-page .student-quiz-history-page .student-history-table tbody tr:hover { background: #fafbfc; }
.student-quiz-browse-page .student-quiz-history-page .student-history-score { font-weight: 700; color: #1e293b; }
.student-quiz-browse-page .student-quiz-history-page .student-history-pill {
    display: inline-block;
    padding: 0.25rem 0.6rem;
    border-radius: 999px;
    font-weight: 800;
    font-size: 0.8rem;
}
.student-quiz-browse-page .student-quiz-history-page .student-history-pill--high { background: #ecfdf5; color: #047857; }
.student-quiz-browse-page .student-quiz-history-page .student-history-pill--mid { background: #fffbeb; color: #b45309; }
.student-quiz-browse-page .student-quiz-history-page .student-history-pill--low { background: #fef2f2; color: #b91c1c; }
.student-quiz-browse-page .student-quiz-history-page .qh-empty {
    padding: 1.25rem 1.35rem;
    margin: 0;
    color: #64748b;
    line-height: 1.55;
}
</style>
<div class="dashboard student-events-page student-quiz-browse-page">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main student-quiz-history-page">
                <h1>Historique des quiz</h1>
                <p>Toutes vos tentatives enregistrées.</p>
                <div style="margin-bottom: 1rem;">
                    <a href="<?= APP_ENTRY ?>?url=student-quiz/quiz" class="qh-back">← Retour aux quiz</a>
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
                        <p class="qh-empty">Aucune tentative enregistrée. Passez un quiz depuis la liste pour voir vos résultats ici.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
