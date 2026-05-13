<?php
/**
 * APPOLIOS - Admin Create Discussion
 */

$groups = $groups ?? [];
?>

<div style="margin-bottom: 2.5rem; display:flex; justify-content: space-between; align-items:flex-end; gap: 1rem; flex-wrap: wrap;">
    <div>
        <h1 style="font-size: 1.8rem; font-weight: 900; color: #1e293b; margin: 0 0 0.5rem 0;">Create discussion</h1>
        <p style="color: #64748b; margin: 0;">Create a Social Learning discussion (admin layout kept).</p>
    </div>
    <div style="display:flex; gap: 10px; flex-wrap: wrap;">
        <a href="<?= APP_ENTRY ?>?url=admin/sl-discussions" class="btn-admin" style="background: white; border: 1px solid #e2e8f0; color: #475569; text-decoration:none;">
            <i class="bi bi-arrow-left"></i> Back
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

<div class="admin-card" style="padding: 1.5rem; max-width: 950px;">
    <?php if (empty($groups)): ?>
        <div style="padding: 1rem 1.25rem; border: 1px dashed #e2e8f0; border-radius: 14px; background: #f8fafc; color: #475569; font-weight: 800;">
            No approved group available. Approve a group first, then create a discussion.
        </div>
    <?php endif; ?>

    <form method="post" action="<?= APP_ENTRY ?>?url=admin/store-discussion" style="display:grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-top: 1rem;">
        <div style="grid-column: 1 / -1;">
            <label style="font-weight: 800; color:#334155; display:block; margin-bottom: 0.4rem;">Group</label>
            <select name="id_groupe" style="width:100%; padding: 12px 14px; border:1px solid #e2e8f0; border-radius: 12px; outline:none; background:white;" <?= empty($groups) ? 'disabled' : '' ?>>
                <option value="">Select group</option>
                <?php foreach ($groups as $g): ?>
                    <option value="<?= (int) $g['id_groupe'] ?>"><?= htmlspecialchars((string) ($g['nom_groupe'] ?? ''), ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="grid-column: 1 / -1;">
            <label style="font-weight: 800; color:#334155; display:block; margin-bottom: 0.4rem;">Title</label>
            <input type="text" name="titre" value="" style="width:100%; padding: 12px 14px; border:1px solid #e2e8f0; border-radius: 12px; outline:none;" />
        </div>

        <div style="grid-column: 1 / -1;">
            <label style="font-weight: 800; color:#334155; display:block; margin-bottom: 0.4rem;">Content</label>
            <textarea name="contenu" rows="6" style="width:100%; padding: 12px 14px; border:1px solid #e2e8f0; border-radius: 12px; outline:none; resize: vertical;"></textarea>
        </div>

        <div>
            <label style="font-weight: 800; color:#334155; display:block; margin-bottom: 0.4rem;">Approval</label>
            <select name="approval_statut" style="width:100%; padding: 12px 14px; border:1px solid #e2e8f0; border-radius: 12px; outline:none; background:white;">
                <option value="approuve" selected>approuve</option>
                <option value="en_cours">en_cours</option>
                <option value="rejete">rejete</option>
            </select>
        </div>

        <div style="display:flex; align-items:flex-end; justify-content:flex-end;">
            <button type="submit" class="btn-admin btn-admin-primary" style="border: none;" <?= empty($groups) ? 'disabled style="opacity:.6;cursor:not-allowed;"' : '' ?>>
                <i class="bi bi-check2-circle"></i> Create
            </button>
        </div>
    </form>
</div>
