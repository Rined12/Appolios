<?php
$teacherSidebarActive = 'quiz';
$items = isset($items) && is_array($items) ? $items : [];
$flash = $flash ?? null;
?>
<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main pro-table-page">
                <div class="pro-table-head">
                    <div>
                        <h1>Plan de rattrapage (Smart)</h1>
                        <p>Recommandations automatiques basées sur les tentatives et la difficulté observée.</p>
                    </div>
                    <div class="pro-table-actions">
                        <a href="<?= APP_ENTRY ?>?url=teacher-quiz/quiz" class="btn btn-outline">Retour Quiz</a>
                    </div>
                </div>

                <?php if (!empty($flash)): ?>
                    <p class="flash flash-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></p>
                <?php endif; ?>

                <div class="pro-table-card" style="padding: 12px; background: rgba(255,255,255,.03);">
                    <?php if (empty($items)): ?>
                        <div class="pro-cell-sub">Aucune donnée.</div>
                    <?php else: ?>
                        <?php foreach ($items as $it): ?>
                            <?php
                                $level = (string) ($it['level'] ?? 'LOW');
                                $badge = 'pro-badge';
                                if ($level === 'HIGH') $badge .= ' pro-badge--advanced';
                                elseif ($level === 'MEDIUM') $badge .= ' pro-badge--intermediate';
                                else $badge .= ' pro-badge--beginner';
                                $recs = isset($it['recommendations']) && is_array($it['recommendations']) ? $it['recommendations'] : [];
                            ?>
                            <div style="padding: 12px; border: 1px solid rgba(148,163,184,0.14); border-radius: 12px; margin-bottom: 10px; background: rgba(2,6,23,.20);">
                                <div style="display:flex; justify-content:space-between; gap: 10px; align-items:flex-start; flex-wrap:wrap;">
                                    <div>
                                        <div style="font-weight: 900;">
                                            <?= htmlspecialchars((string) ($it['title'] ?? '')) ?>
                                            <span class="pro-cell-sub">#<?= (int) ($it['id'] ?? 0) ?></span>
                                        </div>
                                        <?php if (!empty($it['sub'])): ?>
                                            <div class="pro-cell-sub"><?= htmlspecialchars((string) $it['sub']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div style="text-align:right; min-width: 220px;">
                                        <span class="<?= htmlspecialchars($badge) ?>"><?= htmlspecialchars($level) ?></span>
                                        <div class="pro-cell-sub">
                                            Impact: <?= (int) ($it['score'] ?? 0) ?>/100 · <?= (int) ($it['attempts'] ?? 0) ?> tentatives · <?= (int) round((float) ($it['avg'] ?? 0)) ?>%
                                        </div>
                                        <div class="pro-cell-sub" style="margin-top: 6px; display:grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 6px; text-align:left;">
                                            <div style="opacity:.95;">Réussite: <strong><?= htmlspecialchars((string) ($it['pass_rate'] ?? '0')) ?>%</strong></div>
                                            <div style="opacity:.95;">Échec: <strong><?= htmlspecialchars((string) ($it['fail_rate'] ?? '0')) ?>%</strong></div>
                                            <div style="opacity:.95;">Best/Worst: <strong><?= (int) ($it['best'] ?? 0) ?>%</strong> / <strong><?= (int) ($it['worst'] ?? 0) ?>%</strong></div>
                                            <div style="opacity:.95;">Stabilité (σ): <strong><?= htmlspecialchars((string) ($it['std'] ?? '0')) ?></strong></div>
                                            <div style="opacity:.95;">Moy 7j: <strong><?= htmlspecialchars((string) ($it['trend_7d'] ?? '0')) ?>%</strong></div>
                                            <div style="opacity:.95;">Δ 7j: <strong><?= htmlspecialchars((string) ($it['trend_delta'] ?? '0')) ?></strong></div>
                                        </div>
                                        <div style="margin-top: 6px; display:flex; gap: 8px; justify-content:flex-end; flex-wrap:wrap;">
                                            <a class="btn btn-outline" style="padding: 6px 10px;" href="<?= APP_ENTRY ?>?url=teacher-quiz/edit-quiz/<?= (int) ($it['id'] ?? 0) ?>">Éditer</a>
                                            <a class="btn btn-outline" style="padding: 6px 10px;" href="<?= APP_ENTRY ?>?url=teacher-quiz/quiz-stats">Stats</a>
                                        </div>
                                    </div>
                                </div>

                                <?php if (!empty($recs)): ?>
                                    <ul style="margin: 8px 0 0; padding-left: 18px;">
                                        <?php foreach ($recs as $r): ?>
                                            <li class="pro-cell-sub"><?= htmlspecialchars((string) $r) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <div class="pro-cell-sub" style="margin-top: 8px;">Aucune recommandation.</div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
