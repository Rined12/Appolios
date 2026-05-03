<?php
$adminSidebarActive = 'sl-groupes';
$old = $old ?? [];
$errors = $errors ?? [];
$g = $groupe ?? [];
$gid = (int) ($g['id_groupe'] ?? 0);
$series = $group_activity_series ?? ['labels' => [], 'discussions' => [], 'visitors' => []];
$stats = $group_editor_stats ?? ['members' => 0, 'discussions' => 0, 'chat_messages' => 0];
$cover = trim((string) ($g['image_url'] ?? $g['photo'] ?? $g['image'] ?? ''));
$creatorName = trim((string) ($g['createur_name'] ?? ''));
$approvalCur = (string) ($old['approval_statut'] ?? ($g['approval_statut'] ?? 'en_cours'));
?>
<div class="dashboard student-events-page collab-hub sl-admin--groupes-edit">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/../partials/sidebar.php'; ?>
            <div class="admin-main">
                <?php require __DIR__ . '/../../../FrontOffice/student/partials/collab_hub_styles.php'; ?>
                <style>
                    .sl-admin--groupes-edit .sl-admin-edit-stats {
                        display: flex;
                        flex-wrap: wrap;
                        gap: 0.65rem;
                        margin: 0 0 1.15rem;
                    }
                    .sl-admin--groupes-edit .sl-admin-edit-stat {
                        flex: 1 1 120px;
                        min-width: 0;
                        padding: 0.72rem 0.95rem;
                        border-radius: 14px;
                        border: 1px solid rgba(148, 163, 184, 0.35);
                        background: linear-gradient(145deg, #f8fafc 0%, #ffffff 55%, #f1f5f9 100%);
                        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
                    }
                    .sl-admin--groupes-edit .sl-admin-edit-stat__val {
                        display: block;
                        font-size: 1.45rem;
                        font-weight: 800;
                        letter-spacing: -0.02em;
                        color: #0f172a;
                        line-height: 1.1;
                    }
                    .sl-admin--groupes-edit .sl-admin-edit-stat__lab {
                        display: flex;
                        align-items: center;
                        gap: 0.35rem;
                        margin-top: 0.25rem;
                        font-size: 0.72rem;
                        font-weight: 700;
                        text-transform: uppercase;
                        letter-spacing: 0.06em;
                        color: #64748b;
                    }
                    .sl-admin--groupes-edit .sl-admin-edit-stat__lab i {
                        font-size: 0.95rem;
                        color: #3b82f6;
                    }
                    .sl-admin--groupes-edit .group-edit-grid-admin {
                        display: grid;
                        grid-template-columns: 1.1fr 0.9fr;
                        gap: 1.1rem;
                        align-items: start;
                    }
                    @media (max-width: 1080px) {
                        .sl-admin--groupes-edit .group-edit-grid-admin {
                            grid-template-columns: 1fr;
                        }
                    }
                </style>

                <div class="header collab-hero" style="margin-bottom:1rem;">
                    <div class="collab-hero__inner">
                        <div>
                            <div class="collab-eyebrow"><i class="bi bi-shield-lock-fill" aria-hidden="true"></i> Admin · Social learning</div>
                            <h1>Edit group</h1>
                            <p>Update metadata and approval, review live engagement, and export an activity PDF when needed.</p>
                            <?php if ($creatorName !== ''): ?>
                                <p style="margin:0.5rem 0 0;font-size:0.88rem;color:var(--ch-muted);font-weight:600;">
                                    <i class="bi bi-person-badge" aria-hidden="true"></i> Creator: <?= htmlspecialchars($creatorName, ENT_QUOTES, 'UTF-8') ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        <div class="collab-hero-actions">
                            <a href="<?= APP_ENTRY ?>?url=admin/sl-groupes" class="collab-btn-ghost">
                                <i class="bi bi-arrow-left" aria-hidden="true"></i> All groups
                            </a>
                            <a href="<?= APP_ENTRY ?>?url=admin/sl-groupes/<?= $gid ?>/activity-pdf" class="collab-btn-primary" target="_blank" rel="noopener" style="background:linear-gradient(135deg,#1e3a5f,#355c7d);">
                                <i class="bi bi-file-earmark-bar-graph" aria-hidden="true"></i> Activity PDF
                            </a>
                        </div>
                    </div>
                </div>

                <div class="sl-admin-edit-stats" role="region" aria-label="Group statistics">
                    <div class="sl-admin-edit-stat">
                        <span class="sl-admin-edit-stat__val"><?= (int) $stats['members'] ?></span>
                        <span class="sl-admin-edit-stat__lab"><i class="bi bi-people-fill" aria-hidden="true"></i> Members</span>
                    </div>
                    <div class="sl-admin-edit-stat">
                        <span class="sl-admin-edit-stat__val"><?= (int) $stats['discussions'] ?></span>
                        <span class="sl-admin-edit-stat__lab"><i class="bi bi-chat-text-fill" aria-hidden="true"></i> Discussions</span>
                    </div>
                    <div class="sl-admin-edit-stat">
                        <span class="sl-admin-edit-stat__val"><?= (int) $stats['chat_messages'] ?></span>
                        <span class="sl-admin-edit-stat__lab"><i class="bi bi-lightning-charge-fill" aria-hidden="true"></i> Chat messages</span>
                    </div>
                </div>

                <div class="group-edit-grid-admin">
                    <div class="section collab-form-shell" style="max-width:none;">
                        <?php if ($cover !== ''): ?>
                            <div style="margin-bottom:1rem;border-radius:14px;overflow:hidden;border:1px solid #e2e8f0;max-height:200px;">
                                <img src="<?= htmlspecialchars($cover, ENT_QUOTES, 'UTF-8') ?>" alt="" style="width:100%;height:200px;object-fit:cover;display:block;" loading="lazy" onerror="this.parentElement.style.display='none';">
                            </div>
                        <?php endif; ?>
                        <form method="POST" action="<?= APP_ENTRY ?>?url=admin/sl-groupes/<?= $gid ?>/update" novalidate>
                            <div class="form-group">
                                <label for="adm_g_nom">Group name</label>
                                <input id="adm_g_nom" type="text" name="nom_groupe" value="<?= htmlspecialchars((string) ($old['nom_groupe'] ?? ($g['nom_groupe'] ?? '')), ENT_QUOTES, 'UTF-8') ?>" data-js-required="1">
                                <?php if (!empty($errors['nom_groupe'])): ?>
                                    <div style="color:#dc2626;font-size:.85rem;font-weight:600;margin-top:.35rem;"><?= htmlspecialchars((string) $errors['nom_groupe'], ENT_QUOTES, 'UTF-8') ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="form-group">
                                <label for="adm_g_desc">Description</label>
                                <textarea id="adm_g_desc" name="description" rows="5" data-js-required="1"><?= htmlspecialchars((string) ($old['description'] ?? ($g['description'] ?? '')), ENT_QUOTES, 'UTF-8') ?></textarea>
                                <?php if (!empty($errors['description'])): ?>
                                    <div style="color:#dc2626;font-size:.85rem;font-weight:600;margin-top:.35rem;"><?= htmlspecialchars((string) $errors['description'], ENT_QUOTES, 'UTF-8') ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="form-group">
                                <label for="adm_g_appr">Approval status</label>
                                <?php $s = (string) ($old['approval_statut'] ?? $approvalCur); ?>
                                <select id="adm_g_appr" name="approval_statut">
                                    <option value="en_cours" <?= $s === 'en_cours' ? 'selected' : '' ?>>en_cours</option>
                                    <option value="approuve" <?= $s === 'approuve' ? 'selected' : '' ?>>approuve</option>
                                    <option value="rejete" <?= $s === 'rejete' ? 'selected' : '' ?>>rejete</option>
                                </select>
                                <?php if (!empty($errors['approval_statut'])): ?>
                                    <div style="color:#dc2626;font-size:.85rem;font-weight:600;margin-top:.35rem;"><?= htmlspecialchars((string) $errors['approval_statut'], ENT_QUOTES, 'UTF-8') ?></div>
                                <?php endif; ?>
                            </div>
                            <div style="display:flex;flex-wrap:wrap;gap:0.65rem;margin-top:0.5rem;">
                                <button class="collab-btn-primary" type="submit"><i class="bi bi-arrow-repeat" aria-hidden="true"></i> Update group</button>
                                <a class="collab-btn-ghost" href="<?= APP_ENTRY ?>?url=admin/sl-groupes">Cancel</a>
                            </div>
                        </form>
                    </div>

                    <div class="aside" style="display:flex;flex-direction:column;gap:1rem;">
                        <div class="section collab-detail-sidecard" style="padding:1.1rem 1.1rem 1.25rem;">
                            <h3 style="margin-bottom:.6rem;">Activity insights</h3>
                            <p style="margin:0 0 .8rem;color:#64748b;font-size:.88rem;line-height:1.5;">Daily trend for visitors and discussion posts (last <?= count($series['labels']) ?> days) — same signal as the student edit workspace.</p>
                            <div style="height:230px;">
                                <div id="groupActivityChartAdmin"></div>
                            </div>
                        </div>
                        <div class="section collab-detail-sidecard" style="padding:1rem 1.1rem;">
                            <h3 style="margin-bottom:.55rem;">Moderation notes</h3>
                            <div style="font-size:.85rem;color:#64748b;line-height:1.55;">
                                <div>• Visitor trend is estimated from member count and daily discussion volume.</div>
                                <div>• Chat messages require the realtime server to have persisted history.</div>
                                <div>• Use the PDF export for a formal snapshot of threads and chat totals.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function () {
    var host = document.getElementById('groupActivityChartAdmin');
    if (!host || !(window.Chart && typeof window.Chart === 'function')) { return; }
    var el = document.createElement('canvas');
    el.style.width = '100%';
    el.style.height = '100%';
    host.appendChild(el);
    var labels = <?= json_encode(array_values($series['labels'])) ?>;
    var discussions = <?= json_encode(array_values($series['discussions'])) ?>;
    var visitors = <?= json_encode(array_values($series['visitors'])) ?>;

    new window.Chart(el, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Visitors',
                    data: visitors,
                    borderColor: '#22c55e',
                    backgroundColor: 'rgba(34,197,94,0.12)',
                    pointBackgroundColor: '#16a34a',
                    pointRadius: 2.8,
                    pointHoverRadius: 4,
                    borderWidth: 2.4,
                    fill: true,
                    tension: 0.35
                },
                {
                    label: 'Discussion posts',
                    data: discussions,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59,130,246,0.08)',
                    pointBackgroundColor: '#2563eb',
                    pointRadius: 2.6,
                    pointHoverRadius: 4,
                    borderWidth: 2.1,
                    fill: false,
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: {
                    labels: { boxWidth: 10, boxHeight: 10, usePointStyle: true, pointStyle: 'circle', color: '#334155', font: { size: 11, weight: '700' } }
                },
                tooltip: { backgroundColor: 'rgba(15,23,42,.92)', titleColor: '#fff', bodyColor: '#e2e8f0', padding: 10, cornerRadius: 10 }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: '#64748b', maxRotation: 0, autoSkip: true, font: { size: 10 } }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(148,163,184,.2)' },
                    ticks: { color: '#64748b', precision: 0, font: { size: 10 } }
                }
            }
        }
    });
})();
</script>
