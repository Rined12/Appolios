<?php
$teacherSidebarActive = 'quiz';
$quiz = isset($quiz) && is_array($quiz) ? $quiz : null;
$summary = isset($summary) && is_array($summary) ? $summary : [];
$attempts = isset($attempts) && is_array($attempts) ? $attempts : [];
?>
<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main pro-table-page">
                <div class="pro-table-head">
                    <div>
                        <h1>Détails rattrapage</h1>
                        <p>Vue avancée d'un quiz: performance, stabilité, tendance et dernières tentatives.</p>
                    </div>
                    <div class="pro-table-actions">
                        <a href="<?= APP_ENTRY ?>?url=teacher-quiz/remediation-plan" class="btn btn-outline">← Retour plan</a>
                        <a href="<?= APP_ENTRY ?>?url=teacher-quiz/quiz" class="btn btn-outline">Retour Quiz</a>
                    </div>
                </div>

                <?php if (!empty($flash)): ?>
                    <p class="flash flash-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></p>
                <?php endif; ?>

                <?php if (!$quiz): ?>
                    <div class="pro-table-card" style="padding: 12px; background: rgba(255,255,255,.03);">
                        <div class="pro-cell-sub">Quiz introuvable.</div>
                    </div>
                <?php else: ?>
                    <div class="pro-table-card" style="padding: 12px; background: rgba(255,255,255,.03); margin-bottom: 12px;">
                        <div style="font-weight: 950; font-size: 18px;">
                            <?= htmlspecialchars((string) ($quiz['title'] ?? '')) ?>
                            <span class="pro-cell-sub">#<?= (int) ($quiz['id'] ?? 0) ?></span>
                        </div>
                        <div class="pro-cell-sub"><?= htmlspecialchars(trim(((string) ($quiz['course_title'] ?? '')) . ' — ' . ((string) ($quiz['chapter_title'] ?? '')))) ?></div>
                        <div class="pro-cell-sub" style="margin-top: 8px; display:grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 8px;">
                            <div style="padding:10px; border: 1px solid rgba(148,163,184,0.14); border-radius: 12px; background: rgba(2,6,23,.18);">
                                <div class="pro-cell-sub">Tentatives</div>
                                <div style="font-weight: 900; font-size: 18px;"><?= (int) ($summary['attempts'] ?? 0) ?></div>
                            </div>
                            <div style="padding:10px; border: 1px solid rgba(148,163,184,0.14); border-radius: 12px; background: rgba(2,6,23,.18);">
                                <div class="pro-cell-sub">Moyenne</div>
                                <div style="font-weight: 900; font-size: 18px;"><?= htmlspecialchars((string) ($summary['avg'] ?? '0')) ?>%</div>
                            </div>
                            <div style="padding:10px; border: 1px solid rgba(148,163,184,0.14); border-radius: 12px; background: rgba(2,6,23,.18);">
                                <div class="pro-cell-sub">Échec</div>
                                <div style="font-weight: 900; font-size: 18px;"><?= htmlspecialchars((string) ($summary['fail_rate'] ?? '0')) ?>%</div>
                            </div>
                            <div style="padding:10px; border: 1px solid rgba(148,163,184,0.14); border-radius: 12px; background: rgba(2,6,23,.18);">
                                <div class="pro-cell-sub">Tendance 7j</div>
                                <div style="font-weight: 900; font-size: 18px;">
                                    <?= htmlspecialchars((string) ($summary['trend_7d'] ?? '0')) ?>%
                                    <span class="pro-cell-sub">(Δ <?= htmlspecialchars((string) ($summary['trend_delta'] ?? '0')) ?>)</span>
                                </div>
                            </div>
                        </div>

                        <div class="pro-cell-sub" style="margin-top: 10px; display:grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 8px;">
                            <div style="padding:10px; border: 1px solid rgba(148,163,184,0.14); border-radius: 12px; background: rgba(2,6,23,.18);">
                                <div class="pro-cell-sub">Réussite</div>
                                <div style="font-weight: 900; font-size: 16px;"><?= htmlspecialchars((string) ($summary['pass_rate'] ?? '0')) ?>%</div>
                            </div>
                            <div style="padding:10px; border: 1px solid rgba(148,163,184,0.14); border-radius: 12px; background: rgba(2,6,23,.18);">
                                <div class="pro-cell-sub">Best / Worst</div>
                                <div style="font-weight: 900; font-size: 16px;"><?= (int) ($summary['best'] ?? 0) ?>% / <?= (int) ($summary['worst'] ?? 0) ?>%</div>
                            </div>
                            <div style="padding:10px; border: 1px solid rgba(148,163,184,0.14); border-radius: 12px; background: rgba(2,6,23,.18);">
                                <div class="pro-cell-sub">Stabilité (σ)</div>
                                <div style="font-weight: 900; font-size: 16px;"><?= htmlspecialchars((string) ($summary['std'] ?? '0')) ?></div>
                            </div>
                        </div>

                        <div style="margin-top: 10px; display:flex; gap: 8px; flex-wrap:wrap;">
                            <a class="btn btn-outline" href="<?= APP_ENTRY ?>?url=teacher-quiz/edit-quiz/<?= (int) ($quiz['id'] ?? 0) ?>">Éditer</a>
                            <a class="btn btn-outline" href="<?= APP_ENTRY ?>?url=teacher-quiz/quiz-stats">Stats</a>
                        </div>
                    </div>

                    <div class="pro-table-card" style="padding: 12px; background: rgba(255,255,255,.03);">
                        <div style="font-weight: 950; margin-bottom: 8px;">Dernières tentatives</div>
                        <?php if (empty($attempts)): ?>
                            <div class="pro-cell-sub">Aucune tentative.</div>
                        <?php else: ?>
                            <div class="pro-table-wrap">
                                <table class="pro-table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Étudiant</th>
                                            <th>Score</th>
                                            <th>%</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($attempts as $a): ?>
                                            <tr>
                                                <td class="pro-cell-sub"><?= htmlspecialchars(substr((string) ($a['submitted_at'] ?? ''), 0, 19)) ?></td>
                                                <td><?= htmlspecialchars((string) ($a['student_name'] ?? '')) ?></td>
                                                <td class="pro-cell-sub"><?= (int) ($a['score'] ?? 0) ?>/<?= (int) ($a['total'] ?? 0) ?></td>
                                                <td><strong><?= (int) ($a['percentage'] ?? 0) ?>%</strong></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
