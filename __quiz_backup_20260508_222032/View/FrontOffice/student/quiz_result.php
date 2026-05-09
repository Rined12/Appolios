<?php
$studentSidebarActive = 'quiz';
$pct = (int) ($percentage ?? 0);
$good = $pct >= 70;
$timedOut = !empty($timed_out);
$recs = isset($recommendations) && is_array($recommendations) ? $recommendations : [];
$rankBefore = isset($rank_before) && is_array($rank_before) ? $rank_before : null;
$rankUpdate = isset($rank_update) && is_array($rank_update) ? $rank_update : null;

$rankProgress = isset($rank_progress) && is_array($rank_progress) ? $rank_progress : null;
$rankSpark = isset($rank_spark) && is_array($rank_spark) ? $rank_spark : [];
$coach = isset($coach) && is_array($coach) ? $coach : null;
$cert = isset($cert) && is_array($cert) ? $cert : null;

$weakChapters = isset($weakChapters) && is_array($weakChapters) ? $weakChapters : [];
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
                        <?php if ($good && !empty($cert['verify_url'])): ?>
                            <a class="btn btn-stats-pro" href="<?= APP_ENTRY ?>?url=student-quiz/cert-qr/<?= (int) ($cert['attempt_id'] ?? 0) ?>">
                                <i class="bi bi-award" aria-hidden="true"></i>
                                Certificat QR
                                <span class="btn-stats-pro-badge">PRO</span>
                            </a>
                        <?php endif; ?>
                        <?php if ($pct < 60): ?>
                            <a href="<?= APP_ENTRY ?>?url=student-quiz/remedial/<?= (int) ($quiz['id'] ?? 0) ?>" class="btn btn-training-pro">
                                <i class="bi bi-lightning-charge" aria-hidden="true"></i>
                                Rattrapage
                                <span class="btn-training-pro-badge">GO</span>
                            </a>
                        <?php endif; ?>
                        <a href="<?= APP_ENTRY ?>?url=student-quiz/quiz" class="btn btn-outline">Liste des quiz</a>
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

                        <?php if (!empty($rankUpdate)): ?>
                            <?php
                                $d = (int) ($rankUpdate['delta'] ?? 0);
                                $sign = $d >= 0 ? '+' : '';
                                $rp = is_array($rankProgress) ? $rankProgress : null;
                                $rpPct = (int) ($rp['pct'] ?? 0);
                                $rpToNext = (int) ($rp['to_next'] ?? 0);
                                $rpNext = (string) ($rp['next_label'] ?? 'Next');
                            ?>
                            <div class="pro-table-card" style="padding: 1rem; margin-top: 12px; background: linear-gradient(135deg, rgba(88, 202, 255, 0.10), rgba(170, 106, 255, 0.10)); border: 1px solid rgba(120, 190, 255, 0.22);">
                                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap: 12px; flex-wrap: wrap;">
                                    <div style="min-width: 260px; flex: 1;">
                                        <div style="font-weight:900; font-size: 1rem; letter-spacing:.2px;">Rank Update</div>
                                        <div style="margin-top:6px; opacity:.95; font-size: 1.05rem;">
                                            <strong><?= htmlspecialchars((string) ($rankUpdate['league'] ?? 'Bronze')) ?> <?= htmlspecialchars((string) ($rankUpdate['division'] ?? 'III')) ?></strong>
                                            <span style="opacity:.9;">· Rating</span>
                                            <strong><?= (int) ($rankUpdate['new_rating'] ?? 0) ?></strong>
                                            <span style="margin-left:10px; font-weight:900; <?= $d >= 0 ? 'color:#22c55e;' : 'color:#ef4444;' ?>">
                                                <?= $sign . (int) $d ?>
                                            </span>
                                        </div>

                                        <div style="margin-top:10px;">
                                            <div style="display:flex; justify-content:space-between; align-items:center; gap: 10px; font-size:.85rem; font-weight:800; opacity:.95;">
                                                <span>Progression vers <?= htmlspecialchars($rpNext) ?></span>
                                                <span><?= (int) $rpPct ?>%</span>
                                            </div>
                                            <div style="margin-top:6px; width: 100%; height: 10px; border-radius: 999px; overflow:hidden; background: rgba(255,255,255,.10); border: 1px solid rgba(255,255,255,.12);">
                                                <div style="height:100%; width: <?= max(0, min(100, $rpPct)) ?>%; background: linear-gradient(90deg, rgba(96, 165, 250, .95), rgba(167, 139, 250, .95));"></div>
                                            </div>
                                            <div style="margin-top:6px; opacity:.92; font-size:.85rem; font-weight:700;">
                                                <?= $rpToNext > 0 ? 'Il te reste ~' . (int) $rpToNext . ' points.' : 'Tu es très proche du palier suivant !' ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div style="display:flex; flex-direction:column; gap:10px; align-items:flex-end;">
                                        <a href="<?= APP_ENTRY ?>?url=student/dashboard" class="btn btn-outline">Voir profil</a>
                                        <?php if (!empty($rankSpark) && count($rankSpark) >= 2): ?>
                                            <?php
                                                $pts = [];
                                                $n = count($rankSpark);
                                                $w = 140;
                                                $h = 40;
                                                for ($i = 0; $i < $n; $i++) {
                                                    $x = (int) round(($w - 2) * ($i / max(1, $n - 1))) + 1;
                                                    $y = (int) round(($h - 2) * (1 - (max(0, min(100, (int) $rankSpark[$i])) / 100))) + 1;
                                                    $pts[] = $x . ',' . $y;
                                                }
                                            ?>
                                            <svg width="<?= (int) $w ?>" height="<?= (int) $h ?>" viewBox="0 0 <?= (int) $w ?> <?= (int) $h ?>" style="display:block;">
                                                <polyline points="<?= htmlspecialchars(implode(' ', $pts)) ?>" fill="none" stroke="rgba(96,165,250,.95)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <div style="font-size:.78rem; opacity:.85; font-weight:700;">Dernières tentatives</div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <?php if (!empty($coach)): ?>
                                    <?php
                                        $tone = (string) ($coach['tone'] ?? 'neutral');
                                        $toneBg = 'rgba(255,255,255,.06)';
                                        $toneBorder = 'rgba(255,255,255,.10)';
                                        if ($tone === 'great') { $toneBg = 'rgba(34,197,94,.12)'; $toneBorder = 'rgba(34,197,94,.22)'; }
                                        elseif ($tone === 'good') { $toneBg = 'rgba(59,130,246,.12)'; $toneBorder = 'rgba(59,130,246,.22)'; }
                                        elseif ($tone === 'soft') { $toneBg = 'rgba(245,158,11,.12)'; $toneBorder = 'rgba(245,158,11,.22)'; }
                                        elseif ($tone === 'warning') { $toneBg = 'rgba(239,68,68,.12)'; $toneBorder = 'rgba(239,68,68,.22)'; }
                                    ?>
                                    <div style="margin-top:12px; padding: 12px; border-radius: 14px; background: <?= $toneBg ?>; border: 1px solid <?= $toneBorder ?>;">
                                        <div style="font-weight:900;">Coach</div>
                                        <div style="margin-top:6px; font-weight:800; opacity:.98;">
                                            <?= htmlspecialchars((string) ($coach['headline'] ?? 'Objectif')) ?>
                                        </div>
                                        <?php if (!empty($coach['meta'])): ?>
                                            <div style="margin-top:6px; opacity:.92; font-weight:700;">
                                                <?= htmlspecialchars((string) $coach['meta']) ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($coach['plan']) && is_array($coach['plan'])): ?>
                                            <div style="margin-top:10px; display:grid; gap:6px;">
                                                <?php foreach ($coach['plan'] as $step): ?>
                                                    <div style="display:flex; gap:8px; align-items:flex-start;">
                                                        <span style="margin-top:2px; width: 8px; height:8px; border-radius:999px; background: rgba(255,255,255,.85);"></span>
                                                        <div style="font-weight:700; opacity:.95;">
                                                            <?= htmlspecialchars((string) $step) ?>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($weakChapters)): ?>
                            <div class="pro-table-card" style="padding: 1rem; margin-top: 12px; background: rgba(255,255,255,.03); border: 1px solid rgba(255,255,255,.10);">
                                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap: 12px; flex-wrap: wrap;">
                                    <div style="min-width: 260px; flex: 1;">
                                        <div style="font-weight:900;">Analyse (chapitres à renforcer)</div>
                                        <div style="margin-top:10px; display:grid; gap:8px;">
                                            <?php foreach ($weakChapters as $wc): ?>
                                                <?php
                                                    $avg = (int) ($wc['avg'] ?? 0);
                                                    $att = (int) ($wc['attempts'] ?? 0);
                                                    $delta = isset($wc['delta']) ? $wc['delta'] : null;
                                                    $lastAt = isset($wc['last_at']) ? (string) ($wc['last_at'] ?? '') : '';
                                                    $prio = (float) ($wc['priority'] ?? 0);
                                                    $prioLabel = 'Moyen';
                                                    $prioBadge = 'pro-badge pro-badge--intermediate';
                                                    if ($prio >= 0.65) { $prioLabel = 'Urgent'; $prioBadge = 'pro-badge pro-badge--advanced'; }
                                                    elseif ($prio <= 0.25) { $prioLabel = 'Faible'; $prioBadge = 'pro-badge pro-badge--beginner'; }
                                                ?>
                                                <div style="display:flex; justify-content:space-between; gap: 10px;">
                                                    <div style="font-weight:800; opacity:.95;">
                                                        <?= htmlspecialchars((string) ($wc['chapter_title'] ?? ('Chapitre #' . (int) ($wc['chapter_id'] ?? 0)))) ?>
                                                        <div style="font-size:.82rem; opacity:.85; font-weight:700;">
                                                            <?= (int) $att ?> tentative(s)
                                                            <?php if ($lastAt !== ''): ?>
                                                                · Dernier: <?= htmlspecialchars(substr($lastAt, 0, 10)) ?>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div style="margin-top:6px; display:flex; flex-wrap:wrap; gap:6px;">
                                                            <span class="<?= $prioBadge ?>"><?= htmlspecialchars($prioLabel) ?></span>
                                                            <?php if ($delta !== null): ?>
                                                                <?php $d = (int) $delta; $sign = $d >= 0 ? '+' : ''; ?>
                                                                <span class="pro-badge" style="background: rgba(148, 163, 184, 0.10); border-color: rgba(148, 163, 184, 0.18); color: rgba(226, 232, 240, 0.85);">
                                                                    Trend <?= htmlspecialchars($sign . (string) $d) ?>
                                                                </span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                    <div style="font-weight:900;">
                                                        <?= (int) $avg ?>%
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>

                                    <div style="min-width: 220px;">
                                        <div style="font-weight:900;">Plan rapide</div>
                                        <div style="margin-top:10px; display:grid; gap:8px;">
                                            <a href="<?= APP_ENTRY ?>?url=student/chapitres" class="btn btn-outline" style="text-decoration:none; text-align:left; white-space:normal;">
                                                <div style="font-weight:900;">Revoir chapitres</div>
                                                <div style="margin-top:4px; font-weight:700; opacity:.9; font-size:.9rem;">Lis le cours puis retente un quiz après 24h.</div>
                                            </a>
                                            <a href="<?= APP_ENTRY ?>?url=student-quiz/quiz&filter=redo" class="btn btn-outline" style="text-decoration:none; text-align:left; white-space:normal;">
                                                <div style="font-weight:900;">À refaire</div>
                                                <div style="margin-top:4px; font-weight:700; opacity:.9; font-size:.9rem;">Refais tes quiz marqués “à refaire”.</div>
                                            </a>
                                            <a href="<?= APP_ENTRY ?>?url=student-quiz/quiz" class="btn btn-primary" style="text-decoration:none; text-align:left; white-space:normal;">
                                                <div style="font-weight:900;">Rejouer maintenant</div>
                                                <div style="margin-top:4px; font-weight:800; opacity:.9; font-size:.9rem;">Choisis un quiz de consolidation ou un challenge.</div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="student-result-actions">
                            <a href="<?= APP_ENTRY ?>?url=student-quiz/quiz" class="btn btn-primary">Autres quiz</a>
                            <a href="<?= APP_ENTRY ?>?url=student-quiz/quiz-history" class="btn btn-outline">Historique</a>
                            <a href="<?= APP_ENTRY ?>?url=student/chapitres" class="btn btn-outline">Revoir les chapitres</a>
                        </div>
                    </div>
                </div>

                <?php if (!empty($recs)): ?>
                    <div class="pro-table-card" style="padding: 1.2rem; margin-top: 16px;">
                        <div class="pro-table-head" style="margin-bottom: 10px;">
                            <div>
                                <h2 style="margin:0;">Recommandations</h2>
                                <p style="margin:6px 0 0; opacity:.9;">Prochains quiz conseillés selon ton résultat.</p>
                            </div>
                        </div>

                        <div class="flash" style="margin: 0 0 12px;">
                            <?php if ($pct < 50): ?>
                                Tu as eu <?= (int) $pct ?>%. Je te propose un quiz de remédiation ciblé + un quiz de consolidation.
                            <?php elseif ($pct < 80): ?>
                                Tu as eu <?= (int) $pct ?>%. Bien joué. On consolide puis on monte en niveau.
                            <?php else: ?>
                                Tu as eu <?= (int) $pct ?>%. Excellent. Je te propose un challenge pour progresser.
                            <?php endif; ?>
                        </div>

                        <div class="pro-grid" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 12px;">
                            <?php foreach ($recs as $i => $r): ?>
                                <?php
                                    $qid = (int) ($r['quiz_id'] ?? 0);
                                    $rt = (string) ($r['title'] ?? '');
                                    $goal = (string) ($r['goal'] ?? 'Suggestion');
                                    $reason = (string) ($r['reason'] ?? '');
                                    $chTitle = (string) ($r['chapter_title'] ?? '');
                                    $diff = (string) ($r['difficulty'] ?? 'beginner');
                                    $isNew = !empty($r['is_new']);
                                    $ins = isset($r['insights']) && is_array($r['insights']) ? $r['insights'] : [];
                                ?>
                                <div class="pro-table-card" style="padding: 1rem; background: rgba(255,255,255,.03);">
                                    <div style="display:flex; align-items:flex-start; justify-content:space-between; gap: 10px;">
                                        <div>
                                            <div style="font-weight:700;"><?= (int) ($i + 1) ?>. <?= htmlspecialchars($goal) ?></div>
                                            <div style="margin-top:4px; font-size: 1rem;"><?= htmlspecialchars($rt) ?></div>
                                            <div style="margin-top:6px; opacity:.9; font-size:.9rem;">
                                                <?= $chTitle !== '' ? htmlspecialchars($chTitle) . ' · ' : '' ?><?= htmlspecialchars(difficulty_label_fr($diff)) ?>
                                            </div>
                                            <div style="margin-top:8px; display:flex; flex-wrap:wrap; gap:6px;">
                                                <span class="pro-tag-chip" style="margin:0;"><?= $isNew ? 'Nouveau' : 'Révision' ?></span>
                                                <?php foreach (array_slice($ins, 0, 3) as $t): ?>
                                                    <span class="pro-tag-chip" style="margin:0; background: rgba(148, 163, 184, 0.10); border-color: rgba(148, 163, 184, 0.18); color: rgba(226, 232, 240, 0.85);">
                                                        <?= htmlspecialchars((string) $t) ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        <a class="btn btn-primary" href="<?= APP_ENTRY ?>?url=student-quiz/quiz/<?= (int) $qid ?>" style="white-space:nowrap;">Commencer</a>
                                    </div>
                                    <?php if ($reason !== ''): ?>
                                        <div style="margin-top:10px; opacity:.92; font-size:.92rem;">
                                            <?= htmlspecialchars($reason) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

