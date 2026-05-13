<?php
/**
 * APPOLIOS - Group Activity Report (Printable)
 */

$g = $groupe ?? [];
$stats = $stats ?? ['members' => 0, 'posts' => 0, 'comments' => 0, 'reactions' => 0, 'discussions' => 0];
$members = $members ?? [];
$posts = $posts ?? [];
$discussions = $discussions ?? [];

$groupId = (int) ($g['id_groupe'] ?? 0);
?>

<div class="dashboard" style="background: #f8fafc; min-height: 100vh; padding: 2rem;">
    <div class="container" style="max-width: 1200px; margin: 0 auto;">
        <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem; gap: 12px;">
            <div>
                <h1 style="font-size: 2rem; font-weight: 900; color: #1e293b; margin: 0;">Group Activity Report</h1>
                <p style="color: #64748b; margin: 6px 0 0 0;">Printable activity snapshot for one Social Learning group.</p>
            </div>
            <div class="no-print" style="display: flex; gap: 10px; flex-wrap: wrap; justify-content: flex-end;">
                <button onclick="window.print()" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px; background: #4338ca; color: white; border: none; padding: 10px 20px; border-radius: 10px; cursor: pointer;">
                    <i class="bi bi-printer-fill"></i> Print / Save as PDF
                </button>
                <a href="<?= APP_ENTRY ?>?url=admin/edit-groupe/<?= $groupId ?>" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; background: #64748b; color: white; text-decoration: none; padding: 10px 20px; border-radius: 10px;">
                    <i class="bi bi-pencil-square"></i> Edit group
                </a>
                <a href="<?= APP_ENTRY ?>?url=admin/sl-groupes" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; background: #0f172a; color: white; text-decoration: none; padding: 10px 20px; border-radius: 10px;">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div class="report-content" style="background: white; border-radius: 16px; padding: 2rem; box-shadow: 0 4px 20px rgba(0,0,0,0.05); border: 1px solid #eef2f6;">
            <div style="text-align: center; margin-bottom: 26px; padding-bottom: 18px; border-bottom: 2px solid #4338ca;">
                <h2 style="color: #1e293b; margin: 0 0 6px 0;">APPOLIOS - Social Learning</h2>
                <div style="color: #64748b; font-size: 0.95rem;">Group: <strong><?= htmlspecialchars((string) ($g['nom_groupe'] ?? 'Group'), ENT_QUOTES, 'UTF-8') ?></strong> (ID <?= $groupId ?>)</div>
            </div>

            <div style="display: flex; justify-content: space-between; margin-bottom: 20px; color: #64748b; font-size: 0.9rem; flex-wrap: wrap; gap: 10px;">
                <div><strong>Generated:</strong> <?= date('F d, Y H:i:s') ?></div>
                <div><strong>Approval:</strong> <?= htmlspecialchars((string) ($g['approval_statut'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
            </div>

            <div style="display:grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 12px; margin-bottom: 22px;">
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius: 14px; padding: 12px 14px;">
                    <div style="color:#64748b; font-weight:800; font-size:.75rem; letter-spacing:.1em; text-transform:uppercase;">Members</div>
                    <div style="font-size: 1.35rem; font-weight: 900; color:#0f172a;"><?= (int) ($stats['members'] ?? 0) ?></div>
                </div>
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius: 14px; padding: 12px 14px;">
                    <div style="color:#64748b; font-weight:800; font-size:.75rem; letter-spacing:.1em; text-transform:uppercase;">Posts</div>
                    <div style="font-size: 1.35rem; font-weight: 900; color:#0f172a;"><?= (int) ($stats['posts'] ?? 0) ?></div>
                </div>
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius: 14px; padding: 12px 14px;">
                    <div style="color:#64748b; font-weight:800; font-size:.75rem; letter-spacing:.1em; text-transform:uppercase;">Comments</div>
                    <div style="font-size: 1.35rem; font-weight: 900; color:#0f172a;"><?= (int) ($stats['comments'] ?? 0) ?></div>
                </div>
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius: 14px; padding: 12px 14px;">
                    <div style="color:#64748b; font-weight:800; font-size:.75rem; letter-spacing:.1em; text-transform:uppercase;">Reactions</div>
                    <div style="font-size: 1.35rem; font-weight: 900; color:#0f172a;"><?= (int) ($stats['reactions'] ?? 0) ?></div>
                </div>
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius: 14px; padding: 12px 14px;">
                    <div style="color:#64748b; font-weight:800; font-size:.75rem; letter-spacing:.1em; text-transform:uppercase;">Discussions</div>
                    <div style="font-size: 1.35rem; font-weight: 900; color:#0f172a;"><?= (int) ($stats['discussions'] ?? 0) ?></div>
                </div>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 18px; align-items: start;">
                <div>
                    <h3 style="margin: 0 0 10px 0; font-size: 1.05rem; font-weight: 900; color:#0f172a;">Members</h3>
                    <div style="border:1px solid #e2e8f0; border-radius: 14px; overflow:hidden;">
                        <table style="width:100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background:#f8fafc; border-bottom: 1px solid #e2e8f0;">
                                    <th style="text-align:left; padding: 10px 12px; color:#475569; font-weight:800; font-size:.85rem;">Name</th>
                                    <th style="text-align:left; padding: 10px 12px; color:#475569; font-weight:800; font-size:.85rem;">Role</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($members)): ?>
                                    <?php foreach ($members as $m): ?>
                                        <tr style="border-bottom: 1px solid #f1f5f9;">
                                            <td style="padding: 10px 12px; color:#0f172a; font-weight:700;"><?= htmlspecialchars((string) ($m['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                            <td style="padding: 10px 12px; color:#64748b; font-weight:700;"><?= htmlspecialchars((string) ($m['role'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="2" style="padding: 14px 12px; color:#64748b;">No members.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div>
                    <h3 style="margin: 0 0 10px 0; font-size: 1.05rem; font-weight: 900; color:#0f172a;">Discussions</h3>
                    <div style="border:1px solid #e2e8f0; border-radius: 14px; overflow:hidden;">
                        <table style="width:100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background:#f8fafc; border-bottom: 1px solid #e2e8f0;">
                                    <th style="text-align:left; padding: 10px 12px; color:#475569; font-weight:800; font-size:.85rem;">Title</th>
                                    <th style="text-align:left; padding: 10px 12px; color:#475569; font-weight:800; font-size:.85rem;">Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($discussions)): ?>
                                    <?php foreach ($discussions as $d): ?>
                                        <tr style="border-bottom: 1px solid #f1f5f9;">
                                            <td style="padding: 10px 12px; color:#0f172a; font-weight:700;"><?= htmlspecialchars((string) ($d['titre'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                            <td style="padding: 10px 12px; color:#64748b;"><?= htmlspecialchars((string) ($d['date_creation'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="2" style="padding: 14px 12px; color:#64748b;">No discussions.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div style="margin-top: 22px;">
                <h3 style="margin: 0 0 10px 0; font-size: 1.05rem; font-weight: 900; color:#0f172a;">Recent posts</h3>
                <div style="border:1px solid #e2e8f0; border-radius: 14px; overflow:hidden;">
                    <table style="width:100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background:#f8fafc; border-bottom: 1px solid #e2e8f0;">
                                <th style="text-align:left; padding: 10px 12px; color:#475569; font-weight:800; font-size:.85rem;">Author</th>
                                <th style="text-align:left; padding: 10px 12px; color:#475569; font-weight:800; font-size:.85rem;">Content</th>
                                <th style="text-align:left; padding: 10px 12px; color:#475569; font-weight:800; font-size:.85rem;">Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($posts)): ?>
                                <?php foreach (array_slice($posts, 0, 25) as $p): ?>
                                    <tr style="border-bottom: 1px solid #f1f5f9;">
                                        <td style="padding: 10px 12px; color:#0f172a; font-weight:700; white-space: nowrap;"><?= htmlspecialchars((string) ($p['user_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                        <td style="padding: 10px 12px; color:#64748b;">
                                            <?php $c = trim((string) ($p['content'] ?? '')); ?>
                                            <?= htmlspecialchars(mb_strlen($c) > 140 ? (mb_substr($c, 0, 140) . '…') : $c, ENT_QUOTES, 'UTF-8') ?>
                                        </td>
                                        <td style="padding: 10px 12px; color:#64748b; white-space: nowrap;"><?= htmlspecialchars((string) ($p['created_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="3" style="padding: 14px 12px; color:#64748b;">No posts.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div style="margin-top: 34px; text-align: center; font-size: 0.8rem; color: #94a3b8; border-top: 1px solid #f1f5f9; padding-top: 18px;">
                <p style="margin:0;">APPOLIOS E-Learning Platform - Social Learning Report</p>
                <p style="margin:6px 0 0 0;">This report is intended for authorized administrators only.</p>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print, .admin-sidebar-pro, .admin-page-container > *:not(.dashboard), .bg-shape { display: none !important; }
    body { background: white !important; }
    .dashboard { padding: 0 !important; }
}
</style>
