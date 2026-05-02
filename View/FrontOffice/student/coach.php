<?php
$studentSidebarActive = '';

$ret = isset($_GET['return']) ? (string) $_GET['return'] : 'student/quiz';
$ret = trim($ret);
if ($ret === '' || strpos($ret, '://') !== false || strpos($ret, 'javascript:') !== false || strpos($ret, '\\') !== false) {
    $ret = 'student/quiz';
}
if (strpos($ret, '..') !== false || strpos($ret, '<') !== false || strpos($ret, '"') !== false || strpos($ret, "'") !== false) {
    $ret = 'student/quiz';
}

$returnUrl = APP_ENTRY . '?url=' . rawurlencode($ret);

$rank = isset($rank) && is_array($rank) ? $rank : null;
$rankProgress = isset($rankProgress) && is_array($rankProgress) ? $rankProgress : null;
$rankSpark = isset($rankSpark) && is_array($rankSpark) ? $rankSpark : [];

$weakChapters = isset($weakChapters) && is_array($weakChapters) ? $weakChapters : [];
$recommendedQuizzes = isset($recommendedQuizzes) && is_array($recommendedQuizzes) ? $recommendedQuizzes : [];
$actions = isset($actions) && is_array($actions) ? $actions : [];
?>

<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>

            <div class="admin-main pro-table-page">
                <div class="pro-table-head">
                    <div>
                        <h1>Coach</h1>
                        <p>Un coach intelligent (règles) pour t’aider à progresser : rank, objectifs, recommandations.</p>
                    </div>
                    <div class="pro-table-actions">
                        <a href="<?= htmlspecialchars($returnUrl) ?>" class="btn btn-outline">Retour</a>
                        <a href="<?= APP_ENTRY ?>?url=student/quiz" class="btn btn-outline">Liste des quiz</a>
                    </div>
                </div>

                <div class="pro-table-card" style="padding: 1.2rem;">
                    <?php if (!empty($rank)): ?>
                        <?php
                            $rp = is_array($rankProgress) ? $rankProgress : null;
                            $rpPct = (int) ($rp['pct'] ?? 0);
                            $rpToNext = (int) ($rp['to_next'] ?? 0);
                            $rpNext = (string) ($rp['next_label'] ?? 'Next');
                        ?>
                        <div style="margin-bottom: 12px; padding: 12px; border-radius: 16px; background: linear-gradient(135deg, rgba(88, 202, 255, 0.10), rgba(170, 106, 255, 0.10)); border: 1px solid rgba(120, 190, 255, 0.22); display:flex; gap: 12px; align-items:center; flex-wrap: wrap;">
                            <div style="width:42px;height:42px;border-radius:14px; background: rgba(11, 31, 58, 0.92); border: 1px solid rgba(120, 190, 255, 0.25); display:flex;align-items:center;justify-content:center;">
                                <i class="bi bi-trophy" style="color: rgba(170, 220, 255, 0.98); font-size: 1.1rem;"></i>
                            </div>
                            <div style="flex:1; min-width: 260px;">
                                <div style="font-weight:900; letter-spacing:.2px;">Ton Rank</div>
                                <div style="margin-top:4px; font-weight:800; opacity:.95;">
                                    <?= htmlspecialchars((string) ($rank['league'] ?? 'Bronze')) ?> <?= htmlspecialchars((string) ($rank['division'] ?? 'III')) ?>
                                    <span style="opacity:.9;">· Rating</span>
                                    <strong><?= (int) ($rank['rating'] ?? 1000) ?></strong>
                                </div>
                                <div style="margin-top:8px;">
                                    <div style="display:flex; justify-content:space-between; font-size:.82rem; font-weight:900; opacity:.95;">
                                        <span>Progression vers <?= htmlspecialchars($rpNext) ?></span>
                                        <span><?= (int) $rpPct ?>%</span>
                                    </div>
                                    <div style="margin-top:6px; width: 100%; height: 10px; border-radius: 999px; overflow:hidden; background: rgba(255,255,255,.14); border: 1px solid rgba(255,255,255,.14);">
                                        <div style="height:100%; width: <?= max(0, min(100, $rpPct)) ?>%; background: linear-gradient(90deg, rgba(96,165,250,.95), rgba(167,139,250,.95));"></div>
                                    </div>
                                    <div style="margin-top:6px; font-size:.82rem; font-weight:800; opacity:.92;">
                                        <?= $rpToNext > 0 ? '~' . (int) $rpToNext . ' points restants' : 'palier proche' ?>
                                    </div>
                                </div>
                            </div>

                            <?php if (!empty($rankSpark) && count($rankSpark) >= 2): ?>
                                <?php
                                    $pts = [];
                                    $n = count($rankSpark);
                                    $w = 160;
                                    $h = 46;
                                    for ($i = 0; $i < $n; $i++) {
                                        $x = (int) round(($w - 2) * ($i / max(1, $n - 1))) + 1;
                                        $y = (int) round(($h - 2) * (1 - (max(0, min(100, (int) $rankSpark[$i])) / 100))) + 1;
                                        $pts[] = $x . ',' . $y;
                                    }
                                ?>
                                <div style="text-align:right;">
                                    <svg width="<?= (int) $w ?>" height="<?= (int) $h ?>" viewBox="0 0 <?= (int) $w ?> <?= (int) $h ?>" style="display:block;">
                                        <polyline points="<?= htmlspecialchars(implode(' ', $pts)) ?>" fill="none" stroke="rgba(96,165,250,.95)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div style="font-size:.78rem; opacity:.85; font-weight:800;">Dernières tentatives</div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 12px;">
                        <div class="pro-table-card" style="padding: 1rem; background: rgba(255,255,255,.03);">
                            <div style="font-weight:900;">Faiblesses détectées</div>
                            <div style="margin-top:8px; display:grid; gap:8px;">
                                <?php if (empty($weakChapters)): ?>
                                    <div style="opacity:.9; font-weight:700;">Pas assez d'historique pour détecter les chapitres faibles.</div>
                                <?php else: ?>
                                    <?php foreach ($weakChapters as $wc): ?>
                                        <div style="display:flex; justify-content:space-between; gap:10px;">
                                            <div style="font-weight:800; opacity:.95;">
                                                <?= htmlspecialchars((string) ($wc['chapter_title'] ?? ('Chapitre #' . (int) ($wc['chapter_id'] ?? 0)))) ?>
                                                <div style="font-size:.82rem; opacity:.85; font-weight:700;"><?= (int) ($wc['attempts'] ?? 0) ?> tentative(s)</div>
                                            </div>
                                            <div style="font-weight:900;"><?= (int) ($wc['avg'] ?? 0) ?>%</div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="pro-table-card" style="padding: 1rem; background: rgba(255,255,255,.03);">
                            <div style="font-weight:900;">Plan d'entraînement</div>
                            <div style="margin-top:8px; display:grid; gap:8px;">
                                <?php if (empty($actions)): ?>
                                    <div style="opacity:.9; font-weight:700;">Aucune action disponible.</div>
                                <?php else: ?>
                                    <?php foreach ($actions as $a): ?>
                                        <a href="<?= htmlspecialchars((string) ($a['url'] ?? '#')) ?>" class="btn btn-outline" style="text-decoration:none; text-align:left; white-space:normal;">
                                            <div style="font-weight:900;"><?= htmlspecialchars((string) ($a['title'] ?? 'Action')) ?></div>
                                            <div style="margin-top:4px; font-weight:700; opacity:.9; font-size:.9rem;">
                                                <?= htmlspecialchars((string) ($a['text'] ?? '')) ?>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="pro-table-card" style="padding: 1rem; background: rgba(255,255,255,.03);">
                            <div style="font-weight:900;">Quiz recommandés</div>
                            <div style="margin-top:8px; display:grid; gap:8px;">
                                <?php if (empty($recommendedQuizzes)): ?>
                                    <div style="opacity:.9; font-weight:700;">Aucune recommandation pour le moment.</div>
                                <?php else: ?>
                                    <?php foreach ($recommendedQuizzes as $rq): ?>
                                        <a class="btn btn-primary" href="<?= APP_ENTRY ?>?url=student/quiz/<?= (int) ($rq['quiz_id'] ?? 0) ?>" style="text-decoration:none; display:flex; justify-content:space-between; align-items:center; gap:10px; white-space: normal;">
                                            <span style="font-weight:900; text-align:left;">
                                                <?= htmlspecialchars((string) ($rq['title'] ?? 'Quiz')) ?>
                                                <span style="display:block; font-weight:800; opacity:.85; font-size:.82rem;">
                                                    <?= htmlspecialchars((string) ($rq['chapter_title'] ?? '')) ?> · <?= htmlspecialchars(difficulty_label_fr((string) ($rq['difficulty'] ?? 'beginner'))) ?>
                                                </span>
                                                <?php if (!empty($rq['reason'])): ?>
                                                    <span style="display:block; font-weight:800; opacity:.78; font-size:.82rem;">
                                                        <?= htmlspecialchars((string) $rq['reason']) ?>
                                                    </span>
                                                <?php endif; ?>
                                            </span>
                                            <span style="font-weight:900;">▶</span>
                                        </a>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
