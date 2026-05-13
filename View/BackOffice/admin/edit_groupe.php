<?php
/**
 * APPOLIOS - Admin Edit Group
 */

$g = $groupe ?? [];
$groupId = (int) ($g['id_groupe'] ?? 0);
$coverRaw = trim((string) ($g['image_url'] ?? ''));
$cover = '';
if ($coverRaw !== '') {
    $cover = preg_match('~^https?://~i', $coverRaw) ? $coverRaw : (APP_URL . '/' . ltrim($coverRaw, '/'));
}
?>

<div style="margin-bottom: 2.5rem; display:flex; justify-content: space-between; align-items:flex-end; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.8rem; font-weight: 900; color: #1e293b; margin: 0 0 0.5rem 0;">Edit group</h1>
        <p style="color: #64748b; margin: 0;">Update group details, approval status, and cover.</p>
    </div>
    <div style="display:flex; gap: 10px;">
        <a href="<?= APP_ENTRY ?>?url=admin/sl-groupes" class="btn-admin" style="background: white; border: 1px solid #e2e8f0; color: #475569; text-decoration:none;">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <a href="<?= APP_ENTRY ?>?url=admin/group-activity-pdf/<?= $groupId ?>" class="btn-admin" style="background: white; border: 1px solid #e2e8f0; color: #475569; text-decoration:none;">
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

<div class="admin-card" style="padding: 1.5rem; max-width: 900px;">
    <form method="post" action="<?= APP_ENTRY ?>?url=admin/update-groupe/<?= $groupId ?>" enctype="multipart/form-data" style="display:grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
        <div style="grid-column: 1 / -1; display:flex; gap: 1rem; align-items:center; flex-wrap: wrap;">
            <div style="width: 120px; height: 78px; border-radius: 14px; overflow:hidden; border: 1px solid #e2e8f0; background: #f8fafc; display:flex; align-items:center; justify-content:center;">
                <?php if ($cover !== ''): ?>
                    <img src="<?= htmlspecialchars($cover, ENT_QUOTES, 'UTF-8') ?>" alt="" style="width:100%; height:100%; object-fit:cover;" onerror="this.remove();" />
                <?php else: ?>
                    <span style="color:#94a3b8; font-weight:800;">No cover</span>
                <?php endif; ?>
            </div>
            <div style="flex: 1 1 auto; min-width: 220px;">
                <label style="font-weight: 800; color:#334155; display:block; margin-bottom: 0.4rem;">Cover photo</label>
                <input type="file" name="group_photo" accept="image/jpeg,image/png,image/gif,image/webp" style="width:100%;" />
                <div style="color:#94a3b8; font-size: 0.85rem; margin-top: 0.25rem;">JPEG/PNG/GIF/WebP · max 2MB</div>
            </div>
        </div>

        <div style="grid-column: 1 / -1;">
            <label style="font-weight: 800; color:#334155; display:block; margin-bottom: 0.4rem;">Group name</label>
            <input type="text" name="nom_groupe" value="<?= htmlspecialchars((string) ($g['nom_groupe'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" style="width:100%; padding: 12px 14px; border:1px solid #e2e8f0; border-radius: 12px; outline:none;" />
        </div>

        <div style="grid-column: 1 / -1;">
            <label style="font-weight: 800; color:#334155; display:block; margin-bottom: 0.4rem;">Description</label>
            <textarea name="description" rows="4" style="width:100%; padding: 12px 14px; border:1px solid #e2e8f0; border-radius: 12px; outline:none; resize: vertical;"><?= htmlspecialchars((string) ($g['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <div>
            <label style="font-weight: 800; color:#334155; display:block; margin-bottom: 0.4rem;">Status</label>
            <select name="statut" style="width:100%; padding: 12px 14px; border:1px solid #e2e8f0; border-radius: 12px; outline:none; background:white;">
                <?php $st = (string) ($g['statut'] ?? 'actif'); ?>
                <option value="actif" <?= $st === 'actif' ? 'selected' : '' ?>>actif</option>
                <option value="archivé" <?= $st === 'archivé' ? 'selected' : '' ?>>archivé</option>
            </select>
        </div>

        <div>
            <label style="font-weight: 800; color:#334155; display:block; margin-bottom: 0.4rem;">Approval</label>
            <select name="approval_statut" style="width:100%; padding: 12px 14px; border:1px solid #e2e8f0; border-radius: 12px; outline:none; background:white;">
                <?php $ap = (string) ($g['approval_statut'] ?? 'en_cours'); ?>
                <option value="en_cours" <?= $ap === 'en_cours' ? 'selected' : '' ?>>en_cours</option>
                <option value="approuve" <?= $ap === 'approuve' ? 'selected' : '' ?>>approuve</option>
                <option value="rejete" <?= $ap === 'rejete' ? 'selected' : '' ?>>rejete</option>
            </select>
        </div>

        <div style="grid-column: 1 / -1; display:flex; gap: 10px; justify-content:flex-end; padding-top: 0.25rem;">
            <button type="submit" class="btn-admin btn-admin-primary" style="border: none;">
                <i class="bi bi-check2-circle"></i> Save
            </button>
        </div>
    </form>
</div>
