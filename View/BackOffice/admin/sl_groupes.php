<?php
/**
 * APPOLIOS - Social Learning Groups (Admin)
 */

$pendingGroups = $pending_groups ?? [];
$approvedGroups = $approved_groups ?? [];
?>

<div style="margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1 style="font-size: 1.95rem; font-weight: 900; color: #1e293b; margin: 0 0 0.5rem 0; letter-spacing: -0.02em;">Social Learning - Groupes</h1>
        <p style="color: #64748b; margin: 0;">Approve, reject, and manage student communities.</p>
    </div>
    <div style="display:flex; gap: 12px;">
        <a href="<?= APP_ENTRY ?>?url=admin/create-groupe" class="btn-admin btn-admin-primary" style="text-decoration:none;">
            <i class="bi bi-plus-lg"></i> Create Group
        </a>
    </div>
</div>

<div class="admin-card" style="padding: 1.25rem; margin-bottom: 2rem;">
    <div style="display:flex; gap: 12px; align-items:center; flex-wrap: wrap;">
        <span style="background: #fff7ed; color: #b45309; padding: 6px 12px; border-radius: 999px; font-weight: 800; font-size: 0.78rem; letter-spacing: 0.06em; text-transform: uppercase;">
            En cours (<?= count($pendingGroups) ?>)
        </span>
        <span style="background: #ecfdf5; color: #15803d; padding: 6px 12px; border-radius: 999px; font-weight: 800; font-size: 0.78rem; letter-spacing: 0.06em; text-transform: uppercase;">
            Approuvés (<?= count($approvedGroups) ?>)
        </span>
        <span style="color:#94a3b8; font-weight: 700; font-size: 0.9rem;">Tip: approve groups so students can join and create discussions.</span>
    </div>
</div>

<style>
.sl-group-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1.25rem;align-items:stretch;}
.sl-group-card{background:#fff;border:1px solid #e2e8f0;border-radius:18px;box-shadow:0 12px 30px rgba(15,23,42,.06);overflow:hidden;display:flex;flex-direction:column;}
.sl-group-media{height:150px;position:relative;background:linear-gradient(145deg, rgba(59,130,246,.14), rgba(34,197,94,.12));}
.sl-group-media img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;}
.sl-group-badge{position:absolute;top:12px;left:12px;background:rgba(15,23,42,.72);color:#fff;border:1px solid rgba(255,255,255,.2);padding:4px 10px;border-radius:999px;font-weight:900;font-size:.72rem;letter-spacing:.1em;text-transform:uppercase;z-index:2;}
.sl-group-body{padding:1rem 1rem 1.05rem;display:flex;flex-direction:column;gap:.85rem;flex:1 1 auto;min-height:0;}
.sl-group-title{margin:0;font-weight:900;color:#0f172a;font-size:1.05rem;letter-spacing:-.01em;}
.sl-group-desc{margin:0;color:#64748b;font-size:.92rem;line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.sl-actions{margin-top:auto;display:flex;gap:.6rem;flex-wrap:wrap;}
.sl-btn{display:inline-flex;align-items:center;justify-content:center;gap:.35rem;height:38px;padding:0 .85rem;border-radius:12px;border:1px solid transparent;font-weight:800;font-size:.82rem;text-decoration:none;cursor:pointer;}
.sl-btn-primary{background:linear-gradient(135deg,#548ca8,#355c7d);color:#fff;}
.sl-btn-ghost{background:#f8fafc;border-color:#e2e8f0;color:#334155;}
.sl-btn-danger{background:#fff1f2;border-color:#fecdd3;color:#be123c;}
</style>

<h2 style="margin: 0 0 1rem 0; font-size: 1.2rem; font-weight: 900; color: #0f172a;">En attente d'approbation</h2>
<?php if (!empty($pendingGroups)): ?>
    <div class="sl-group-grid" style="margin-bottom: 2.2rem;">
        <?php foreach ($pendingGroups as $g): ?>
            <?php
                $gid = (int) ($g['id_groupe'] ?? 0);
                $cover = (string) ($g['cover_url'] ?? '');
                $name = (string) ($g['nom_groupe'] ?? 'Group');
                $desc = (string) ($g['description'] ?? '');
            ?>
            <div class="sl-group-card">
                <div class="sl-group-media">
                    <span class="sl-group-badge">en_cours</span>
                    <?php if ($cover !== ''): ?>
                        <img src="<?= htmlspecialchars($cover, ENT_QUOTES, 'UTF-8') ?>" alt="" loading="lazy" onerror="this.remove();">
                    <?php endif; ?>
                </div>
                <div class="sl-group-body">
                    <h3 class="sl-group-title"><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?></h3>
                    <p class="sl-group-desc"><?= htmlspecialchars($desc, ENT_QUOTES, 'UTF-8') ?></p>
                    <div class="sl-actions">
                        <a class="sl-btn sl-btn-ghost" href="<?= APP_ENTRY ?>?url=admin/show-groupe/<?= $gid ?>"><i class="bi bi-box-arrow-up-right"></i> Open</a>
                        <a class="sl-btn sl-btn-ghost" href="<?= APP_ENTRY ?>?url=admin/group-activity-pdf/<?= $gid ?>"><i class="bi bi-file-earmark-pdf-fill"></i> Activity PDF</a>
                        <a class="sl-btn sl-btn-ghost" href="<?= APP_ENTRY ?>?url=admin/edit-groupe/<?= $gid ?>"><i class="bi bi-pencil-square"></i> Edit</a>
                        <form method="post" action="<?= APP_ENTRY ?>?url=admin/approve-groupe/<?= $gid ?>" style="display:inline;">
                            <button type="submit" class="sl-btn sl-btn-primary"><i class="bi bi-check2"></i> Approve</button>
                        </form>
                        <form method="post" action="<?= APP_ENTRY ?>?url=admin/reject-groupe/<?= $gid ?>" style="display:inline;">
                            <button type="submit" class="sl-btn sl-btn-ghost"><i class="bi bi-x"></i> Reject</button>
                        </form>
                        <form method="post" action="<?= APP_ENTRY ?>?url=admin/delete-groupe/<?= $gid ?>" style="display:inline;" onsubmit="return confirm('Delete this group?');">
                            <button type="submit" class="sl-btn sl-btn-danger"><i class="bi bi-trash"></i> Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="admin-card" style="padding: 2rem; text-align:center; color:#64748b;">No pending groups.</div>
<?php endif; ?>

<h2 style="margin: 0 0 1rem 0; font-size: 1.2rem; font-weight: 900; color: #0f172a;">Groupes approuvés</h2>
<?php if (!empty($approvedGroups)): ?>
    <div class="sl-group-grid">
        <?php foreach ($approvedGroups as $g): ?>
            <?php
                $gid = (int) ($g['id_groupe'] ?? 0);
                $cover = (string) ($g['cover_url'] ?? '');
                $name = (string) ($g['nom_groupe'] ?? 'Group');
                $desc = (string) ($g['description'] ?? '');
            ?>
            <div class="sl-group-card">
                <div class="sl-group-media">
                    <span class="sl-group-badge" style="background: rgba(22, 163, 74, 0.9); border-color: rgba(255,255,255,0.25);">approuve</span>
                    <?php if ($cover !== ''): ?>
                        <img src="<?= htmlspecialchars($cover, ENT_QUOTES, 'UTF-8') ?>" alt="" loading="lazy" onerror="this.remove();">
                    <?php endif; ?>
                </div>
                <div class="sl-group-body">
                    <h3 class="sl-group-title"><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?></h3>
                    <p class="sl-group-desc"><?= htmlspecialchars($desc, ENT_QUOTES, 'UTF-8') ?></p>
                    <div class="sl-actions">
                        <a class="sl-btn sl-btn-ghost" href="<?= APP_ENTRY ?>?url=admin/show-groupe/<?= $gid ?>"><i class="bi bi-box-arrow-up-right"></i> Open</a>
                        <a class="sl-btn sl-btn-ghost" href="<?= APP_ENTRY ?>?url=admin/group-activity-pdf/<?= $gid ?>"><i class="bi bi-file-earmark-pdf-fill"></i> Activity PDF</a>
                        <a class="sl-btn sl-btn-ghost" href="<?= APP_ENTRY ?>?url=admin/edit-groupe/<?= $gid ?>"><i class="bi bi-pencil-square"></i> Edit</a>
                        <form method="post" action="<?= APP_ENTRY ?>?url=admin/delete-groupe/<?= $gid ?>" style="display:inline;" onsubmit="return confirm('Delete this group?');">
                            <button type="submit" class="sl-btn sl-btn-danger"><i class="bi bi-trash"></i> Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="admin-card" style="padding: 2rem; text-align:center; color:#64748b;">No approved groups.</div>
<?php endif; ?>
