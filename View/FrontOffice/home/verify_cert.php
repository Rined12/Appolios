<?php
$ok = !empty($ok);
$reason = (string) ($reason ?? '');
$attempt = isset($attempt) && is_array($attempt) ? $attempt : null;
?>
<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <div class="admin-main pro-table-page" style="margin-left:0;">
                <div class="pro-table-head">
                    <div>
                        <h1>Vérification certificat</h1>
                        <p><?= htmlspecialchars($reason) ?></p>
                    </div>
                    <div class="pro-table-actions">
                        <a href="<?= APP_URL ?>" class="btn btn-outline">Accueil</a>
                    </div>
                </div>

                <div class="pro-stats-grid" style="grid-template-columns: minmax(0, 1fr);">
                    <div class="pro-stat-card" style="background: <?= $ok ? 'linear-gradient(135deg, rgba(34,197,94,0.12), rgba(96,165,250,0.08))' : 'linear-gradient(135deg, rgba(239,68,68,0.12), rgba(167,139,250,0.06))' ?>; border-color: <?= $ok ? 'rgba(34,197,94,0.22)' : 'rgba(239,68,68,0.22)' ?>;">
                        <div class="pro-stat-top">
                            <div class="pro-stat-title"><?= $ok ? 'Authentique' : 'Invalide' ?></div>
                            <div class="pro-stat-icon"><i class="bi <?= $ok ? 'bi-patch-check' : 'bi-exclamation-triangle' ?>"></i></div>
                        </div>
                        <div class="pro-stat-value"><?= $ok ? 'OK' : 'KO' ?></div>
                        <div class="pro-stat-sub"><?= htmlspecialchars($reason) ?></div>
                    </div>
                </div>

                <?php if ($ok && $attempt): ?>
                    <div class="pro-table-card" style="margin-top: 14px; padding: 14px;">
                        <div style="display:grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px;">
                            <div>
                                <div style="opacity:.75; font-weight:900;">Étudiant</div>
                                <div style="margin-top:4px; font-weight:1000; font-size:1.05rem;">
                                    <?= htmlspecialchars((string) ($attempt['student_name'] ?? '')) ?>
                                </div>
                            </div>
                            <div>
                                <div style="opacity:.75; font-weight:900;">Quiz</div>
                                <div style="margin-top:4px; font-weight:1000; font-size:1.05rem;">
                                    <?= htmlspecialchars((string) ($attempt['quiz_title'] ?? '')) ?>
                                </div>
                            </div>
                            <div>
                                <div style="opacity:.75; font-weight:900;">Score</div>
                                <div style="margin-top:4px; font-weight:1000; font-size:1.05rem;">
                                    <?= (int) ($attempt['score'] ?? 0) ?> / <?= (int) ($attempt['total'] ?? 0) ?>
                                    <span style="opacity:.85;">·</span>
                                    <?= (int) ($attempt['percentage'] ?? 0) ?>%
                                </div>
                            </div>
                            <div>
                                <div style="opacity:.75; font-weight:900;">Date</div>
                                <div style="margin-top:4px; font-weight:1000; font-size:1.05rem;">
                                    <?= htmlspecialchars(substr((string) ($attempt['submitted_at'] ?? ''), 0, 19)) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="pro-table-card" style="margin-top: 14px; padding: 14px; opacity:.9;">
                    <div style="font-weight: 950;">Conseil</div>
                    <div style="margin-top: 8px; font-weight: 750; opacity:.85;">
                        Si tu scans depuis un téléphone, assure-toi que le lien ne contient pas <strong>localhost</strong>.
                        Utilise l'IP de ton PC (même Wi‑Fi) ou un domaine.
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
