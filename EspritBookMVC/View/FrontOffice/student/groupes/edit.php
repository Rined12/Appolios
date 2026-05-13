<?php
// Canonical nested view path (replaces flat file; identical markup to former flat sibling).
$studentSidebarActive = 'groupes';
$old = $old ?? [];
$errors = $errors ?? [];
$cover = trim((string) ($groupe['image_url'] ?? $groupe['photo'] ?? $groupe['image'] ?? ''));
$series = $group_activity_series ?? ['labels' => [], 'discussions' => [], 'visitors' => []];
?>
<div class="dashboard student-events-page collab-hub">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/../partials/sidebar.php'; ?>
            <div class="admin-main">
                <?php require __DIR__ . '/../partials/collab_hub_styles.php'; ?>

                <div class="header collab-hero" style="margin-bottom:1.2rem;">
                    <div class="collab-hero__inner">
                        <div>
                            <div class="collab-eyebrow"><i class="bi bi-sliders2-vertical" aria-hidden="true"></i> Group control room</div>
                            <h1>Edit Group</h1>
                            <p>Update your group settings and monitor community activity trends in one workspace.</p>
                        </div>
                        <div class="collab-hero-actions">
                            <a href="<?= APP_ENTRY ?>?url=student/groupes/<?= (int) $groupe['id_groupe'] ?>" class="collab-btn-ghost">
                                <i class="bi bi-arrow-left" aria-hidden="true"></i> Back to group
                            </a>
                        </div>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1.1fr 0.9fr;gap:1.1rem;align-items:start;" class="group-edit-grid">
                    <div class="section collab-form-shell" style="max-width:none;">
                        <form method="POST" action="<?= APP_ENTRY ?>?url=student/groupes/<?= (int) $groupe['id_groupe'] ?>/update" enctype="multipart/form-data">
                            <div class="form-group">
                                <label>Group Name</label>
                                <input type="text" name="nom_groupe" value="<?= htmlspecialchars($old['nom_groupe'] ?? $groupe['nom_groupe']) ?>">
                                <?php if (!empty($errors['nom_groupe'])): ?><div style="color:#dc2626;font-size:.85rem;font-weight:600;margin-top:.35rem;"><?= htmlspecialchars((string) $errors['nom_groupe']) ?></div><?php endif; ?>
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description"><?= htmlspecialchars($old['description'] ?? $groupe['description']) ?></textarea>
                                <?php if (!empty($errors['description'])): ?><div style="color:#dc2626;font-size:.85rem;font-weight:600;margin-top:.35rem;"><?= htmlspecialchars((string) $errors['description']) ?></div><?php endif; ?>
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <?php $selectedStatut = (string) ($old['statut'] ?? $groupe['statut'] ?? 'actif'); ?>
                                <select name="statut">
                                    <option value="actif"<?= $selectedStatut === 'actif' ? ' selected' : '' ?>>Active</option>
                                    <option value="archivé"<?= $selectedStatut === 'archivé' ? ' selected' : '' ?>>Archived</option>
                                </select>
                                <?php if (!empty($errors['statut'])): ?><div style="color:#dc2626;font-size:.85rem;font-weight:600;margin-top:.35rem;"><?= htmlspecialchars((string) $errors['statut']) ?></div><?php endif; ?>
                            </div>
                            <div class="form-group">
                                <label>Replace photo</label>
                                <div class="upload-field">
                                    <input id="group_photo_input" class="upload-field__input" type="file" name="group_photo" accept="image/jpeg,image/png,image/gif,image/webp">
                                    <label for="group_photo_input" class="upload-field__button">
                                        <i class="bi bi-image" aria-hidden="true"></i>
                                        <span>Choose image</span>
                                    </label>
                                    <span id="group_photo_name" class="upload-field__name">No file selected</span>
                                </div>
                                <?php if (!empty($errors['group_photo'])): ?><div style="color:#dc2626;font-size:.85rem;font-weight:600;margin-top:.35rem;"><?= htmlspecialchars((string) $errors['group_photo']) ?></div><?php endif; ?>
                            </div>
                            <?php if ($cover !== ''): ?>
                            <div class="form-group">
                                <label>Current photo</label>
                                <img src="<?= htmlspecialchars($cover) ?>" alt="Current group photo" style="width:100%;max-width:460px;height:190px;object-fit:cover;border-radius:12px;border:1px solid #e2e8f0;" onerror="this.style.display='none';">
                            </div>
                            <?php endif; ?>
                            <button class="collab-btn-primary" type="submit"><i class="bi bi-arrow-repeat" aria-hidden="true"></i> Update Group</button>
                        </form>
                    </div>

                    <div class="aside" style="display:flex;flex-direction:column;gap:1rem;">
                        <div class="section collab-detail-sidecard" style="padding:1.1rem 1.1rem 1.25rem;">
                            <h3 style="margin-bottom:.6rem;">Activity Insights</h3>
                            <p style="margin:0 0 .8rem;color:#64748b;font-size:.88rem;line-height:1.5;">Daily trend for visitors and discussion posts (last <?= count($series['labels']) ?> days).</p>
                            <div style="height:230px;">
                                <div id="groupActivityChart"></div>
                            </div>
                        </div>
                        <div class="section collab-detail-sidecard" style="padding:1rem 1.1rem;">
                            <h3 style="margin-bottom:.55rem;">Quick Notes</h3>
                            <div style="font-size:.85rem;color:#64748b;line-height:1.55;">
                                <div>• Visitor trend is estimated from member activity and daily discussion volume.</div>
                                <div>• Higher peaks usually mean stronger engagement windows.</div>
                                <div>• Keep post frequency consistent for smoother growth.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
@media (max-width: 1080px) {
    .group-edit-grid { grid-template-columns: 1fr !important; }
}

.upload-field {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    flex-wrap: wrap;
    padding: 0.6rem;
    border: 1px solid #dbeafe;
    border-radius: 12px;
    background: linear-gradient(135deg, #f8fbff 0%, #ffffff 100%);
}
.upload-field__input {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    border: 0;
}
.upload-field__button {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    padding: 0.55rem 0.9rem;
    border-radius: 10px;
    border: 1px solid #bfdbfe;
    background: #fff;
    color: #1d4ed8;
    font-weight: 700;
    font-size: 0.85rem;
    cursor: pointer;
    transition: transform .18s ease, box-shadow .2s ease, border-color .2s ease;
}
.upload-field__button:hover {
    transform: translateY(-1px);
    border-color: #93c5fd;
    box-shadow: 0 8px 18px rgba(59,130,246,0.15);
}
.upload-field__name {
    font-size: 0.84rem;
    color: #64748b;
    max-width: 320px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function () {
    var groupPhotoInput = document.getElementById('group_photo_input');
    var groupPhotoName = document.getElementById('group_photo_name');
    if (groupPhotoInput && groupPhotoName) {
        groupPhotoInput.addEventListener('change', function () {
            if (groupPhotoInput.files && groupPhotoInput.files.length > 0) {
                groupPhotoName.textContent = groupPhotoInput.files[0].name;
                return;
            }
            groupPhotoName.textContent = 'No file selected';
        });
    }

    var host = document.getElementById('groupActivityChart');
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
