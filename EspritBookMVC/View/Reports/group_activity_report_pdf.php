<?php
/** @var array<string, mixed> $groupe */
/** @var array<int, array<string, mixed>> $members */
/** @var int $total_discussions */
/** @var int $total_chat_messages */
/** @var int $total_opening_posts */
/** @var array<int, array<string, mixed>> $top_discussions */
/** @var array<int, array<string, mixed>> $recent_messages */
/** @var string $generated_at */
/** @var bool $chat_table_available */
/** @var string $group_created_label */
/** @var string $backUrl */
/** @var string $report_title */

$groupe = $groupe ?? [];
$members = $members ?? [];
$total_discussions = (int) ($total_discussions ?? 0);
$total_chat_messages = (int) ($total_chat_messages ?? 0);
$total_opening_posts = (int) ($total_opening_posts ?? 0);
$top_discussions = $top_discussions ?? [];
$recent_messages = $recent_messages ?? [];
$generated_at = (string) ($generated_at ?? '');
$chat_table_available = (bool) ($chat_table_available ?? false);
$group_created_label = (string) ($group_created_label ?? '');
$backUrl = (string) ($backUrl ?? (APP_ENTRY . '?url=admin/sl-groupes'));
$report_title = (string) ($report_title ?? 'Group Activity Report');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($report_title, ENT_QUOTES, 'UTF-8') ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', system-ui, sans-serif; font-size: 12px; line-height: 1.45; color: #1e293b; padding: 22px; background: #f8fafc; }
        .sheet { max-width: 900px; margin: 0 auto; background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 4px 24px rgba(15,23,42,.06); }
        .hero { border-bottom: 3px solid #548CA8; padding-bottom: 18px; margin-bottom: 20px; }
        .hero h1 { font-size: 22px; color: #2B4865; margin-bottom: 6px; }
        .hero .sub { color: #64748b; font-size: 12px; }
        .meta { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 22px; }
        @media (max-width: 700px) { .meta { grid-template-columns: 1fr; } }
        .stat { background: linear-gradient(135deg, #f0f9ff, #fff); border: 1px solid #bfdbfe; border-radius: 10px; padding: 12px 14px; }
        .stat .n { font-size: 22px; font-weight: 800; color: #1d4ed8; }
        .stat .l { font-size: 11px; color: #64748b; text-transform: uppercase; letter-spacing: .04em; margin-top: 4px; }
        h2 { font-size: 14px; color: #2B4865; margin: 22px 0 10px; padding-bottom: 6px; border-bottom: 1px solid #e2e8f0; }
        .info-grid { display: grid; gap: 8px; font-size: 12px; }
        .info-grid dt { color: #64748b; font-weight: 600; }
        .info-grid dd { color: #0f172a; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 11px; }
        th { background: #548CA8; color: #fff; text-align: left; padding: 8px 10px; font-weight: 600; }
        td { padding: 8px 10px; border-bottom: 1px solid #e2e8f0; vertical-align: top; }
        tr:nth-child(even) td { background: #f8fafc; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 999px; font-size: 10px; font-weight: 700; background: #e0f2fe; color: #0369a1; }
        .muted { color: #64748b; font-size: 11px; }
        .msg-preview { max-height: 3.2em; overflow: hidden; }
        .no-print { margin-bottom: 16px; display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }
        .btn { display: inline-block; padding: 10px 18px; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; }
        .btn-primary { background: #548CA8; color: #fff; }
        .btn-muted { background: #64748b; color: #fff; }
        .footer { margin-top: 24px; padding-top: 14px; border-top: 1px solid #e2e8f0; text-align: center; font-size: 10px; color: #94a3b8; }
        @media print {
            body { background: #fff; padding: 0; }
            .no-print { display: none !important; }
            .sheet { box-shadow: none; border: none; border-radius: 0; max-width: none; }
        }
    </style>
</head>
<body>
<div class="sheet">
    <div class="hero">
        <h1><?= htmlspecialchars($report_title, ENT_QUOTES, 'UTF-8') ?></h1>
        <p class="sub"><?= htmlspecialchars((string) ($groupe['nom_groupe'] ?? 'Group'), ENT_QUOTES, 'UTF-8') ?> &mdash; Generated <?= htmlspecialchars($generated_at, ENT_QUOTES, 'UTF-8') ?></p>
    </div>

    <div class="no-print">
        <button type="button" class="btn btn-primary" onclick="window.print()">Print / Save as PDF</button>
        <a class="btn btn-muted" href="<?= htmlspecialchars($backUrl, ENT_QUOTES, 'UTF-8') ?>">Back</a>
        <?php if (!$chat_table_available): ?>
            <span class="muted">Live chat history table not found (run the realtime server once to create discussion_messages). Chat counts show 0.</span>
        <?php endif; ?>
    </div>

    <div class="meta">
        <div class="stat"><div class="n"><?= $total_discussions ?></div><div class="l">Discussions</div></div>
        <div class="stat"><div class="n"><?= $total_chat_messages ?></div><div class="l">Live chat messages</div></div>
        <div class="stat"><div class="n"><?= $total_discussions + $total_chat_messages ?></div><div class="l">Total activity lines</div></div>
    </div>

    <h2>Group overview</h2>
    <dl class="info-grid" style="grid-template-columns: 140px 1fr;">
        <dt>Name</dt><dd><?= htmlspecialchars((string) ($groupe['nom_groupe'] ?? ''), ENT_QUOTES, 'UTF-8') ?></dd>
        <dt>Description</dt><dd><?= nl2br(htmlspecialchars((string) ($groupe['description'] ?? ''), ENT_QUOTES, 'UTF-8')) ?></dd>
        <dt>Created</dt><dd><?= htmlspecialchars($group_created_label !== '' ? $group_created_label : '—', ENT_QUOTES, 'UTF-8') ?></dd>
        <dt>Status</dt><dd><span class="badge"><?= htmlspecialchars((string) ($groupe['approval_statut'] ?? $groupe['approval_status'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span></dd>
        <dt>Creator</dt><dd><?= htmlspecialchars((string) ($groupe['createur_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></dd>
    </dl>

    <h2>Members</h2>
    <?php if (!empty($members)): ?>
        <table>
            <thead>
                <tr><th>Name</th><th>Email</th><th>Role</th><th>Joined</th></tr>
            </thead>
            <tbody>
                <?php foreach ($members as $m): ?>
                    <tr>
                        <td><?= htmlspecialchars((string) ($m['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) ($m['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) ($m['role'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) ($m['date_adhesion'] ?? $m['joined_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="muted">No members recorded.</p>
    <?php endif; ?>

    <h2>Discussion summary</h2>
    <p class="muted" style="margin-bottom:10px;">Opening post counts as one activity line per thread. Live chat counts rows in <strong>discussion_messages</strong> when available.</p>

    <h2>Top discussions (most active)</h2>
    <?php if (!empty($top_discussions)): ?>
        <table>
            <thead>
                <tr><th>#</th><th>Title</th><th>Chat msgs</th><th>Total lines</th></tr>
            </thead>
            <tbody>
                <?php foreach ($top_discussions as $i => $t): ?>
                    <tr>
                        <td><?= (int) $i + 1 ?></td>
                        <td><?= htmlspecialchars((string) ($t['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= (int) ($t['chat_count'] ?? 0) ?></td>
                        <td><strong><?= (int) ($t['message_count'] ?? 0) ?></strong></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="muted">No discussions in this group yet.</p>
    <?php endif; ?>

    <h2>Recent live chat (last 10)</h2>
    <?php if (!empty($recent_messages)): ?>
        <table>
            <thead>
                <tr><th>When</th><th>Discussion</th><th>Author</th><th>Message</th></tr>
            </thead>
            <tbody>
                <?php foreach ($recent_messages as $rm): ?>
                    <?php
                    $mt = (string) ($rm['message_type'] ?? 'text');
                    $body = (string) ($rm['message'] ?? '');
                    if ($mt !== 'text' && $body === '') {
                        $body = '[' . $mt . '] ' . (string) ($rm['file_name'] ?? '');
                    }
                    ?>
                    <tr>
                        <td><?= htmlspecialchars((string) ($rm['created_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) ($rm['discussion_title'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) ($rm['user_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><div class="msg-preview"><?= nl2br(htmlspecialchars($body, ENT_QUOTES, 'UTF-8')) ?></div></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="muted"><?= $chat_table_available ? 'No chat messages yet for discussions in this group.' : 'Chat persistence unavailable.' ?></p>
    <?php endif; ?>

    <div class="footer">APPOLIOS &mdash; Group activity report</div>
</div>
</body>
</html>
