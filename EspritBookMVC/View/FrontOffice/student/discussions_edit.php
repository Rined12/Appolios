<?php
$studentSidebarActive = 'discussions';
$foPrefix = $foPrefix ?? 'student';
$discussion_edit = $discussion_edit ?? ['discussion_id' => 0, 'update_url' => '#', 'selected_group_id' => 0, 'title_value' => '', 'content_value' => ''];
$edit = $discussion_edit;
$groups = $groups ?? [];
$errors = $errors ?? [];
$discussion_stats = $discussion_stats ?? null;
?>
<div class="dashboard student-events-page collab-hub">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main">
                <?php require __DIR__ . '/partials/collab_hub_styles.php'; ?>

                <header class="collab-hero">
                    <div class="collab-hero__inner">
                        <div>
                            <div class="collab-eyebrow"><i class="bi bi-sliders" aria-hidden="true"></i> Refine thread</div>
                            <h1>Edit discussion</h1>
                            <p>Update the title, body, or hosting group — permissions still follow ownership rules.</p>
                        </div>
                        <div class="collab-hero-actions">
                            <a href="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/discussions" class="collab-btn-ghost">
                                <i class="bi bi-arrow-left" aria-hidden="true"></i> Back
                            </a>
                        </div>
                    </div>
                </header>

                <div class="discussion-edit-grid" style="display:grid;grid-template-columns:1.2fr 0.8fr;gap:1rem;align-items:start;">
                    <div class="collab-form-shell" style="max-width:none;">
                        <form method="POST" action="<?= htmlspecialchars((string) $edit['update_url'], ENT_QUOTES, 'UTF-8') ?>">
                            <div class="form-group">
                                <label>Group</label>
                                <select name="id_groupe">
                                    <?php foreach ($groups as $group): ?>
                                        <option value="<?= (int) $group['id_groupe'] ?>" <?= ((int) $edit['selected_group_id'] === (int) $group['id_groupe']) ? 'selected' : '' ?>><?= htmlspecialchars((string) $group['nom_groupe'], ENT_QUOTES, 'UTF-8') ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (!empty($errors['id_groupe'])): ?>
                                    <div style="color:#dc2626;font-size:0.85rem;font-weight:600;margin-top:0.35rem;"><?= htmlspecialchars((string) $errors['id_groupe'], ENT_QUOTES, 'UTF-8') ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" name="titre" value="<?= htmlspecialchars((string) $edit['title_value'], ENT_QUOTES, 'UTF-8') ?>">
                                <?php if (!empty($errors['titre'])): ?>
                                    <div style="color:#dc2626;font-size:0.85rem;font-weight:600;margin-top:0.35rem;"><?= htmlspecialchars((string) $errors['titre'], ENT_QUOTES, 'UTF-8') ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="form-group">
                                <label>Content</label>
                                <textarea name="contenu"><?= htmlspecialchars((string) $edit['content_value'], ENT_QUOTES, 'UTF-8') ?></textarea>
                                <?php if (!empty($errors['contenu'])): ?>
                                    <div style="color:#dc2626;font-size:0.85rem;font-weight:600;margin-top:0.35rem;"><?= htmlspecialchars((string) $errors['contenu'], ENT_QUOTES, 'UTF-8') ?></div>
                                <?php endif; ?>
                            </div>
                            <button type="submit" class="collab-btn-primary">
                                <i class="bi bi-arrow-repeat" aria-hidden="true"></i> Update discussion
                            </button>
                        </form>
                    </div>

                    <?php if (!empty($discussion_stats)): ?>
                    <aside class="collab-detail-sidecard" style="padding:1rem 1rem 1.1rem;">
                        <h3 style="margin-bottom:.6rem;">Owner Message Stats</h3>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.6rem;margin-bottom:.7rem;">
                            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:.6rem .7rem;">
                                <div style="font-size:.72rem;color:#64748b;font-weight:700;">Group messages</div>
                                <div style="font-size:1.2rem;color:#0f172a;font-weight:800;"><?= (int) ($discussion_stats['total_messages'] ?? 0) ?></div>
                            </div>
                            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:.6rem .7rem;">
                                <div style="font-size:.72rem;color:#64748b;font-weight:700;">Your messages</div>
                                <div style="font-size:1.2rem;color:#0f172a;font-weight:800;"><?= (int) ($discussion_stats['owner_messages'] ?? 0) ?></div>
                            </div>
                            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:.6rem .7rem;">
                                <div style="font-size:.72rem;color:#64748b;font-weight:700;">Current message words</div>
                                <div style="font-size:1.2rem;color:#0f172a;font-weight:800;"><?= (int) ($discussion_stats['current_message_words'] ?? 0) ?></div>
                            </div>
                            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:.6rem .7rem;">
                                <div style="font-size:.72rem;color:#64748b;font-weight:700;">Avg/day</div>
                                <div style="font-size:1.2rem;color:#0f172a;font-weight:800;"><?= htmlspecialchars((string) ($discussion_stats['avg_messages_per_day'] ?? '0')) ?></div>
                            </div>
                        </div>
                        <div style="height:180px;margin-bottom:.7rem;">
                            <canvas id="discussionActivityChart"></canvas>
                        </div>
                        <div style="font-size:.78rem;color:#64748b;padding:.55rem .6rem;border-radius:9px;background:#f8fafc;border:1px dashed #cbd5e1;">
                            Last activity: <strong style="color:#334155;"><?= htmlspecialchars((string) ($discussion_stats['last_activity_label'] ?? 'No activity yet')) ?></strong>
                        </div>
                    </aside>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
@media (max-width: 1080px) {
    .discussion-edit-grid { grid-template-columns: 1fr !important; }
}
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function () {
    var c = document.getElementById('discussionActivityChart');
    if (!c || !(window.Chart && typeof window.Chart === 'function')) { return; }
    var labels = <?= json_encode(array_values($discussion_stats['series_labels'] ?? [])) ?>;
    var groupData = <?= json_encode(array_values($discussion_stats['series_group_messages'] ?? [])) ?>;
    var ownerData = <?= json_encode(array_values($discussion_stats['series_owner_messages'] ?? [])) ?>;
    new window.Chart(c, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Group messages',
                    data: groupData,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59,130,246,0.12)',
                    fill: true,
                    tension: 0.35,
                    borderWidth: 2.2,
                    pointRadius: 2
                },
                {
                    label: 'Your messages',
                    data: ownerData,
                    borderColor: '#22c55e',
                    backgroundColor: 'rgba(34,197,94,0.06)',
                    fill: false,
                    tension: 0.35,
                    borderWidth: 2.2,
                    pointRadius: 2
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        boxWidth: 10,
                        boxHeight: 10,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        color: '#334155',
                        font: { size: 10, weight: '700' }
                    }
                }
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
