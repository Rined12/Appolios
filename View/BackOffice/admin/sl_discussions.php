<?php
/**
 * APPOLIOS - Social Learning Discussions (Admin)
 */

$rows = $discussions ?? [];
$filterStatus = (string) ($filterStatus ?? 'all');
$pendingCount = (int) ($pendingCount ?? 0);
$approvedCount = (int) ($approvedCount ?? 0);
$rejectedCount = (int) ($rejectedCount ?? 0);
?>

<div style="margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: flex-end; gap: 12px; flex-wrap: wrap;">
    <div>
        <h1 style="font-size: 1.95rem; font-weight: 900; color: #1e293b; margin: 0 0 0.5rem 0; letter-spacing: -0.02em;">Social Learning - Discussions</h1>
        <p style="color: #64748b; margin: 0;">Moderate and manage group discussions (admin layout kept).</p>
    </div>
    <div style="display:flex; gap: 10px; flex-wrap: wrap;">
        <a href="<?= APP_ENTRY ?>?url=admin/create-discussion" class="btn-admin btn-admin-primary" style="text-decoration:none;">
            <i class="bi bi-plus-lg"></i> Create discussion
        </a>
        <a href="<?= APP_ENTRY ?>?url=admin/sl-groupes" class="btn-admin" style="background: white; border: 1px solid #e2e8f0; color: #475569; text-decoration:none;">
            <i class="bi bi-people-fill"></i> Manage groups
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

<div class="admin-card" style="padding: 1.25rem; margin-bottom: 1.5rem;">
    <?php $allActive = $filterStatus === 'all'; ?>
    <?php $pendingActive = $filterStatus === 'en_cours'; ?>
    <?php $approvedActive = $filterStatus === 'approuve'; ?>
    <?php $rejectedActive = $filterStatus === 'rejete'; ?>

    <div style="display:flex; gap: 10px; flex-wrap: wrap;">
        <a href="<?= APP_ENTRY ?>?url=admin/sl-discussions&status=all" style="padding: 10px 18px; background: <?= $allActive ? '#3b82f6' : 'white' ?>; color: <?= $allActive ? 'white' : '#475569' ?>; text-decoration: none; border-radius: 12px; font-weight: 800; border: 1px solid #e2e8f0;">
            All (<?= $pendingCount + $approvedCount + $rejectedCount ?>)
        </a>
        <a href="<?= APP_ENTRY ?>?url=admin/sl-discussions&status=en_cours" style="padding: 10px 18px; background: <?= $pendingActive ? '#3b82f6' : 'white' ?>; color: <?= $pendingActive ? 'white' : '#475569' ?>; text-decoration: none; border-radius: 12px; font-weight: 800; border: 1px solid #e2e8f0;">
            En cours (<?= $pendingCount ?>)
        </a>
        <a href="<?= APP_ENTRY ?>?url=admin/sl-discussions&status=approuve" style="padding: 10px 18px; background: <?= $approvedActive ? '#3b82f6' : 'white' ?>; color: <?= $approvedActive ? 'white' : '#475569' ?>; text-decoration: none; border-radius: 12px; font-weight: 800; border: 1px solid #e2e8f0;">
            Approuvés (<?= $approvedCount ?>)
        </a>
        <a href="<?= APP_ENTRY ?>?url=admin/sl-discussions&status=rejete" style="padding: 10px 18px; background: <?= $rejectedActive ? '#3b82f6' : 'white' ?>; color: <?= $rejectedActive ? 'white' : '#475569' ?>; text-decoration: none; border-radius: 12px; font-weight: 800; border: 1px solid #e2e8f0;">
            Rejetés (<?= $rejectedCount ?>)
        </a>
    </div>
</div>

<style>
.sl-disc-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(290px,1fr));gap:1.25rem;align-items:stretch;}
.sl-disc-card{background:#fff;border:1px solid #e2e8f0;border-radius:18px;box-shadow:0 12px 30px rgba(15,23,42,.06);overflow:hidden;display:flex;flex-direction:column;}
.sl-disc-head{padding:1rem 1rem .85rem;border-bottom:1px solid #f1f5f9;display:flex;align-items:flex-start;justify-content:space-between;gap:10px;}
.sl-disc-badge{background:#0f172a;color:#fff;border-radius:999px;padding:4px 10px;font-weight:900;font-size:.72rem;letter-spacing:.1em;text-transform:uppercase;}
.sl-disc-body{padding:1rem;display:flex;flex-direction:column;gap:.7rem;flex:1;}
.sl-disc-title{margin:0;font-weight:900;color:#0f172a;font-size:1.02rem;letter-spacing:-.01em;}
.sl-disc-meta{margin:0;color:#64748b;font-weight:800;font-size:.85rem;}
.sl-disc-text{margin:0;color:#64748b;font-size:.92rem;line-height:1.55;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;}
.sl-actions{margin-top:auto;display:flex;gap:.6rem;flex-wrap:wrap;}
.sl-btn{display:inline-flex;align-items:center;justify-content:center;gap:.35rem;height:38px;padding:0 .85rem;border-radius:12px;border:1px solid transparent;font-weight:800;font-size:.82rem;text-decoration:none;cursor:pointer;}
.sl-btn-primary{background:linear-gradient(135deg,#548ca8,#355c7d);color:#fff;}
.sl-btn-ghost{background:#f8fafc;border-color:#e2e8f0;color:#334155;}
.sl-btn-danger{background:#fff1f2;border-color:#fecdd3;color:#be123c;}
</style>

<?php if (!empty($rows)): ?>
    <div class="sl-disc-grid">
        <?php foreach ($rows as $d): ?>
            <?php
                $id = (int) ($d['id_discussion'] ?? 0);
                $approval = (string) ($d['approval_statut'] ?? 'approuve');
                $badgeStyle = 'background: rgba(15,23,42,.72);';
                if ($approval === 'approuve') $badgeStyle = 'background: rgba(22, 163, 74, 0.92);';
                if ($approval === 'rejete') $badgeStyle = 'background: rgba(239, 68, 68, 0.92);';
                if ($approval === 'en_cours') $badgeStyle = 'background: rgba(234, 179, 8, 0.92);';
            ?>
            <div class="sl-disc-card">
                <div class="sl-disc-head">
                    <span class="sl-disc-badge" style="<?= $badgeStyle ?>"><?= htmlspecialchars($approval, ENT_QUOTES, 'UTF-8') ?></span>
                    <span style="color:#94a3b8;font-weight:900;font-size:.85rem;white-space:nowrap;">
                        <?= htmlspecialchars((string) ($d['date_creation'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                    </span>
                </div>
                <div class="sl-disc-body">
                    <h3 class="sl-disc-title"><?= htmlspecialchars((string) ($d['titre'] ?? 'Discussion'), ENT_QUOTES, 'UTF-8') ?></h3>
                    <p class="sl-disc-meta">
                        Groupe: <?= htmlspecialchars((string) ($d['nom_groupe'] ?? '-'), ENT_QUOTES, 'UTF-8') ?><br>
                        Auteur: <?= htmlspecialchars((string) ($d['auteur_name'] ?? '-'), ENT_QUOTES, 'UTF-8') ?>
                    </p>
                    <p class="sl-disc-text"><?= htmlspecialchars((string) ($d['contenu'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>

                    <div class="sl-actions">
                        <?php if ($approval === 'approuve'): ?>
                            <a class="sl-btn sl-btn-ghost" href="<?= APP_ENTRY ?>?url=admin/chat-discussion/<?= $id ?>"><i class="bi bi-chat-dots-fill"></i> Live chat</a>
                        <?php endif; ?>
                        <form method="post" action="<?= APP_ENTRY ?>?url=admin/approve-discussion/<?= $id ?>" style="display:inline;">
                            <button type="submit" class="sl-btn sl-btn-primary"><i class="bi bi-check2"></i> Approve</button>
                        </form>
                        <form method="post" action="<?= APP_ENTRY ?>?url=admin/reject-discussion/<?= $id ?>" style="display:inline;">
                            <button type="submit" class="sl-btn sl-btn-ghost"><i class="bi bi-x"></i> Reject</button>
                        </form>
                        <form method="post" action="<?= APP_ENTRY ?>?url=admin/delete-discussion/<?= $id ?>" style="display:inline;" onsubmit="return confirm('Delete this discussion?');">
                            <button type="submit" class="sl-btn sl-btn-danger"><i class="bi bi-trash"></i> Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="admin-card" style="padding: 2rem; text-align:center; color:#64748b;">No discussions.</div>
<?php endif; ?>
