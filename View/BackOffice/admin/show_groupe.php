<?php
/**
 * APPOLIOS - Admin Show Group
 */

$g = $groupe ?? [];
$members = $members ?? [];
$groupId = (int) ($g['id_groupe'] ?? 0);
$coverRaw = trim((string) ($g['image_url'] ?? ''));
$cover = '';
if ($coverRaw !== '') {
    $cover = preg_match('~^https?://~i', $coverRaw) ? $coverRaw : (APP_URL . '/' . ltrim($coverRaw, '/'));
}
?>

<div style="margin-bottom: 2.25rem; display:flex; justify-content: space-between; align-items:flex-end; gap: 1rem; flex-wrap: wrap;">
    <div>
        <h1 style="font-size: 1.8rem; font-weight: 900; color: #1e293b; margin: 0 0 0.5rem 0;">Group details</h1>
        <p style="color: #64748b; margin: 0;">Admin view (keeps the admin sidebar).</p>
    </div>
    <div style="display:flex; gap: 10px; flex-wrap: wrap;">
        <a href="<?= APP_ENTRY ?>?url=admin/sl-groupes" class="btn-admin" style="background: white; border: 1px solid #e2e8f0; color: #475569; text-decoration:none;">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <a href="<?= APP_ENTRY ?>?url=admin/edit-groupe/<?= $groupId ?>" class="btn-admin" style="background: white; border: 1px solid #e2e8f0; color: #475569; text-decoration:none;">
            <i class="bi bi-pencil-square"></i> Edit
        </a>
        <a href="<?= APP_ENTRY ?>?url=admin/group-activity-pdf/<?= $groupId ?>" class="btn-admin btn-admin-primary" style="text-decoration:none;">
            <i class="bi bi-file-earmark-pdf-fill"></i> Activity PDF
        </a>
    </div>
</div>

<?php if (!empty($flash)): ?>
    <div class="admin-card" style="padding: 1rem 1.25rem; margin-bottom: 1.25rem; border-left: 4px solid <?= ($flash['type'] ?? '') === 'success' ? '#22c55e' : '#ef4444' ?>;">
        <div style="font-weight: 800; color: #0f172a;">
            <?= htmlspecialchars((string) ($flash['message'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
        </div>
    </div>
<?php endif; ?>

<div class="admin-card" style="padding: 0; overflow: hidden;">
    <div style="height: 200px; background: linear-gradient(145deg, rgba(59,130,246,.16), rgba(34,197,94,.12)); position: relative;">
        <?php if ($cover !== ''): ?>
            <img src="<?= htmlspecialchars($cover, ENT_QUOTES, 'UTF-8') ?>" alt="" style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover;" onerror="this.remove();" />
        <?php endif; ?>
        <div style="position:absolute; inset: 0; background: linear-gradient(180deg, rgba(15,23,42,0) 0%, rgba(15,23,42,.55) 100%);"></div>
        <div style="position:absolute; left: 18px; bottom: 14px; color: white;">
            <div style="font-size: 1.35rem; font-weight: 900; letter-spacing: -0.02em;">
                <?= htmlspecialchars((string) ($g['nom_groupe'] ?? 'Group'), ENT_QUOTES, 'UTF-8') ?>
            </div>
            <div style="opacity: 0.9; font-weight: 800; font-size: 0.9rem;">
                <?= htmlspecialchars((string) ($g['approval_statut'] ?? ''), ENT_QUOTES, 'UTF-8') ?> · <?= htmlspecialchars((string) ($g['statut'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
            </div>
        </div>
    </div>

    <div style="padding: 1.25rem 1.5rem; display:grid; grid-template-columns: 1.6fr 1fr; gap: 1.25rem;">
        <div>
            <h3 style="margin: 0 0 8px 0; font-weight: 900; color:#0f172a;">Description</h3>
            <p style="margin: 0; color:#64748b; line-height: 1.6;">
                <?= nl2br(htmlspecialchars((string) ($g['description'] ?? ''), ENT_QUOTES, 'UTF-8')) ?>
            </p>
        </div>

        <div>
            <h3 style="margin: 0 0 8px 0; font-weight: 900; color:#0f172a;">Members (<?= count($members) ?>)</h3>
            <div style="border:1px solid #e2e8f0; border-radius: 14px; overflow:hidden;">
                <table style="width:100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background:#f8fafc; border-bottom: 1px solid #e2e8f0;">
                            <th style="text-align:left; padding: 10px 12px; color:#475569; font-weight:900; font-size:.85rem;">Name</th>
                            <th style="text-align:left; padding: 10px 12px; color:#475569; font-weight:900; font-size:.85rem;">Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($members)): ?>
                            <?php foreach (array_slice($members, 0, 15) as $m): ?>
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 10px 12px; color:#0f172a; font-weight:800;">
                                        <?= htmlspecialchars((string) ($m['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                                    </td>
                                    <td style="padding: 10px 12px; color:#64748b; font-weight:800;">
                                        <?= htmlspecialchars((string) ($m['role'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="2" style="padding: 14px 12px; color:#64748b;">No members.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
