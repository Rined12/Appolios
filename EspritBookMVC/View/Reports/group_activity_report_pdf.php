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
$groupName = (string) ($groupe['nom_groupe'] ?? 'Group');
$groupId = (int) ($groupe['id_groupe'] ?? 0);
$assetsBase = rtrim(APP_URL, '/') . '/View/assets';
$logoUrl = $assetsBase . '/images/appolios-report-logo.png';
$activityTotal = $total_discussions + $total_chat_messages;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(APP_NAME . ' · ' . $report_title, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="<?= htmlspecialchars($assetsBase . '/vendor/bootstrap-icons/bootstrap-icons.css', ENT_QUOTES, 'UTF-8') ?>">
    <style>
        @page { margin: 14mm 12mm; size: A4 portrait; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --ap-navy: #0f2744;
            --ap-navy-mid: #1a3a5c;
            --ap-teal: #3d7a96;
            --ap-teal-bright: #548ca8;
            --ap-sky: #e8f4fc;
            --ap-ice: #f4f9fc;
            --ap-line: #c5d9e8;
            --ap-text: #0f172a;
            --ap-muted: #5c728a;
            --ap-accent: #0d9488;
        }
        body {
            font-family: 'Segoe UI', 'Inter', system-ui, -apple-system, sans-serif;
            font-size: 11.5px;
            line-height: 1.5;
            color: var(--ap-text);
            background: linear-gradient(165deg, #dfeaf3 0%, #f4f9fc 38%, #ffffff 100%);
            min-height: 100vh;
            padding: 28px 18px 40px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .sheet {
            max-width: 820px;
            margin: 0 auto;
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid var(--ap-line);
            box-shadow: 0 20px 50px rgba(15, 39, 68, 0.12), 0 2px 8px rgba(15, 39, 68, 0.06);
        }
        .report-brand-ribbon {
            height: 5px;
            background: linear-gradient(90deg, var(--ap-navy) 0%, var(--ap-teal-bright) 45%, var(--ap-accent) 100%);
        }
        .report-hero {
            display: grid;
            grid-template-columns: minmax(120px, 220px) 1fr auto;
            gap: 18px 20px;
            align-items: center;
            padding: 22px 26px 20px;
            background: linear-gradient(135deg, var(--ap-navy) 0%, var(--ap-navy-mid) 42%, #234a6e 100%);
            color: #fff;
        }
        .report-hero__logo-wrap {
            background: rgba(255, 255, 255, 0.97);
            border-radius: 12px;
            padding: 8px 12px;
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.22);
            display: flex;
            align-items: center;
            justify-content: center;
            align-self: center;
        }
        .report-hero__logo {
            display: block;
            max-width: 200px;
            max-height: 58px;
            width: auto;
            height: auto;
            object-fit: contain;
        }
        .report-hero__logo-fallback {
            width: 118px;
            height: 44px;
            display: none;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 1.35rem;
            letter-spacing: 0.12em;
            color: #fff;
            border: 2px solid rgba(255,255,255,.35);
            border-radius: 10px;
        }
        .report-hero__titles h1 {
            font-size: 1.35rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            margin-bottom: 4px;
            line-height: 1.2;
        }
        .report-hero__eyebrow {
            font-size: 0.62rem;
            font-weight: 800;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: rgba(255,255,255,.72);
            margin-bottom: 6px;
        }
        .report-hero__sub {
            font-size: 0.82rem;
            color: rgba(255,255,255,.88);
            font-weight: 500;
        }
        .report-hero__meta {
            text-align: right;
            font-size: 0.72rem;
            color: rgba(255,255,255,.78);
            line-height: 1.55;
            border-left: 1px solid rgba(255,255,255,.22);
            padding-left: 18px;
        }
        .report-hero__meta strong {
            display: block;
            color: #fff;
            font-size: 0.78rem;
            margin-bottom: 4px;
        }
        .report-body { padding: 22px 26px 26px; background: linear-gradient(180deg, #fff 0%, var(--ap-ice) 100%); }
        .no-print {
            margin: 0 26px 18px;
            padding-top: 16px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: transform .15s ease, filter .15s ease;
        }
        .btn:hover { transform: translateY(-1px); filter: brightness(1.05); }
        .btn-primary {
            background: linear-gradient(135deg, var(--ap-teal-bright), #3a6d8a);
            color: #fff;
            box-shadow: 0 6px 16px rgba(61, 122, 150, 0.35);
        }
        .btn-muted {
            background: #475569;
            color: #fff;
        }
        .notice-inline {
            font-size: 10.5px;
            color: var(--ap-muted);
            max-width: 420px;
            line-height: 1.45;
        }
        .notice-inline i { color: var(--ap-teal-bright); margin-right: 4px; vertical-align: -2px; }
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
            margin-bottom: 22px;
        }
        @media (max-width: 640px) { .stat-grid { grid-template-columns: 1fr; } }
        .stat-card {
            position: relative;
            border-radius: 14px;
            padding: 14px 14px 14px 16px;
            border: 1px solid var(--ap-line);
            background: linear-gradient(145deg, #fff 0%, var(--ap-sky) 100%);
            overflow: hidden;
        }
        .stat-card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(180deg, var(--ap-teal-bright), var(--ap-accent));
            border-radius: 4px 0 0 4px;
        }
        .stat-card .stat-icon {
            font-size: 1.1rem;
            color: var(--ap-teal-bright);
            margin-bottom: 6px;
        }
        .stat-card .n {
            font-size: 1.65rem;
            font-weight: 900;
            letter-spacing: -0.03em;
            color: var(--ap-navy);
            line-height: 1;
        }
        .stat-card .l {
            font-size: 0.62rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.11em;
            color: var(--ap-muted);
            margin-top: 8px;
        }
        h2 {
            font-size: 0.78rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--ap-navy-mid);
            margin: 24px 0 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        h2::before {
            content: '';
            width: 22px;
            height: 3px;
            border-radius: 2px;
            background: linear-gradient(90deg, var(--ap-teal-bright), var(--ap-accent));
        }
        .info-panel {
            border-radius: 12px;
            border: 1px solid var(--ap-line);
            background: #fff;
            padding: 14px 16px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 130px 1fr;
            gap: 8px 14px;
            font-size: 11.5px;
            align-items: start;
        }
        .info-grid dt {
            color: var(--ap-muted);
            font-weight: 700;
        }
        .info-grid dd { color: var(--ap-text); }
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 8px;
            font-size: 11px;
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid var(--ap-line);
        }
        th {
            background: linear-gradient(180deg, var(--ap-navy-mid) 0%, var(--ap-navy) 100%);
            color: #fff;
            text-align: left;
            padding: 10px 12px;
            font-weight: 700;
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }
        td {
            padding: 9px 12px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
            background: #fff;
        }
        tr:last-child td { border-bottom: none; }
        tr:nth-child(even) td { background: var(--ap-ice); }
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 800;
            background: linear-gradient(135deg, #dbeafe, #e0f2fe);
            color: #0c4a6e;
            border: 1px solid #bae6fd;
        }
        .muted { color: var(--ap-muted); font-size: 11px; }
        .msg-preview { max-height: 3.4em; overflow: hidden; }
        .report-footer {
            margin-top: 28px;
            padding: 18px 26px 22px;
            background: linear-gradient(180deg, var(--ap-ice) 0%, #e2edf5 100%);
            border-top: 1px solid var(--ap-line);
            text-align: center;
        }
        .report-footer__brand {
            font-weight: 900;
            font-size: 0.95rem;
            letter-spacing: 0.18em;
            color: var(--ap-navy);
            margin-bottom: 4px;
        }
        .report-footer__tag {
            font-size: 10px;
            color: var(--ap-muted);
            letter-spacing: 0.04em;
        }
        .report-watermark {
            position: fixed;
            right: 24px;
            bottom: 18px;
            font-size: 9px;
            font-weight: 800;
            letter-spacing: 0.25em;
            color: rgba(15, 39, 68, 0.06);
            transform: rotate(-12deg);
            pointer-events: none;
            z-index: 0;
        }
        @media print {
            body { background: #fff; padding: 0; }
            .no-print { display: none !important; }
            .sheet { box-shadow: none; border-radius: 0; border: none; max-width: none; }
            .report-watermark { display: none; }
        }
        @media (max-width: 720px) {
            .report-hero { grid-template-columns: 1fr; text-align: center; }
            .report-hero__logo-wrap { justify-content: center; }
            .report-hero__meta { text-align: center; border-left: none; padding-left: 0; border-top: 1px solid rgba(255,255,255,.2); padding-top: 14px; }
        }
    </style>
</head>
<body>
<div class="report-watermark" aria-hidden="true"><?= htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8') ?></div>
<div class="sheet">
    <div class="report-brand-ribbon" aria-hidden="true"></div>
    <header class="report-hero">
        <div class="report-hero__logo-wrap">
            <img class="report-hero__logo" src="<?= htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8') ?>" width="200" height="58" onerror="var w=this.closest('.report-hero__logo-wrap'); if(w){w.style.display='none';} var f=this.closest('.report-hero').querySelector('.report-hero__logo-fallback'); if(f){f.style.display='flex';}">
        </div>
        <div class="report-hero__logo-fallback"><?= htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8') ?></div>
        <div class="report-hero__titles">
            <div class="report-hero__eyebrow"><?= htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8') ?> · Social learning intelligence</div>
            <h1><?= htmlspecialchars($report_title, ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="report-hero__sub"><strong><?= htmlspecialchars($groupName, ENT_QUOTES, 'UTF-8') ?></strong> &mdash; generated <?= htmlspecialchars($generated_at, ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <div class="report-hero__meta">
            <strong>Document reference</strong>
            Group #<?= $groupId ?: '—' ?><br>
            <?= htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8') ?> v<?= htmlspecialchars(APP_VERSION, ENT_QUOTES, 'UTF-8') ?>
        </div>
    </header>

    <div class="no-print">
        <button type="button" class="btn btn-primary" onclick="window.print()"><i class="bi bi-printer-fill" aria-hidden="true"></i> Print / Save as PDF</button>
        <a class="btn btn-muted" href="<?= htmlspecialchars($backUrl, ENT_QUOTES, 'UTF-8') ?>"><i class="bi bi-arrow-left" aria-hidden="true"></i> Back</a>
        <?php if (!$chat_table_available): ?>
            <span class="notice-inline"><i class="bi bi-info-circle" aria-hidden="true"></i> Live chat history table not found (start the realtime server once to create <code>discussion_messages</code>). Chat counts show 0.</span>
        <?php endif; ?>
    </div>

    <div class="report-body">
        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-chat-square-text-fill" aria-hidden="true"></i></div>
                <div class="n"><?= $total_discussions ?></div>
                <div class="l">Discussions</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-lightning-charge-fill" aria-hidden="true"></i></div>
                <div class="n"><?= $total_chat_messages ?></div>
                <div class="l">Live chat messages</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-activity" aria-hidden="true"></i></div>
                <div class="n"><?= $activityTotal ?></div>
                <div class="l">Total activity lines</div>
            </div>
        </div>

        <h2>Group overview</h2>
        <div class="info-panel">
            <dl class="info-grid">
                <dt>Name</dt><dd><?= htmlspecialchars((string) ($groupe['nom_groupe'] ?? ''), ENT_QUOTES, 'UTF-8') ?></dd>
                <dt>Description</dt><dd><?= nl2br(htmlspecialchars((string) ($groupe['description'] ?? ''), ENT_QUOTES, 'UTF-8')) ?></dd>
                <dt>Created</dt><dd><?= htmlspecialchars($group_created_label !== '' ? $group_created_label : '—', ENT_QUOTES, 'UTF-8') ?></dd>
                <dt>Status</dt><dd><span class="badge"><?= htmlspecialchars((string) ($groupe['approval_statut'] ?? $groupe['approval_status'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span></dd>
                <dt>Creator</dt><dd><?= htmlspecialchars((string) ($groupe['createur_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></dd>
            </dl>
        </div>

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
        <p class="muted" style="margin-bottom:12px;">Opening post counts as one activity line per thread. Live chat counts rows in <strong>discussion_messages</strong> when the realtime service has created that table.</p>

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
    </div>

    <footer class="report-footer">
        <div class="report-footer__brand"><?= htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8') ?></div>
        <div class="report-footer__tag">Official group activity report &middot; <?= htmlspecialchars($groupName, ENT_QUOTES, 'UTF-8') ?> &middot; <?= htmlspecialchars($generated_at, ENT_QUOTES, 'UTF-8') ?></div>
    </footer>
</div>
</body>
</html>
