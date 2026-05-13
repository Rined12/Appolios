<?php
/**
 * Group Activity Report — printable layout (creator only). Open via …/groupes/{id}/activity-pdf, use Print / Save as PDF.
 */
$foPrefix = (string) ($foPrefix ?? 'student');
$g = $groupe ?? [];
$groupId = (int) ($g['id_groupe'] ?? 0);
$members = $members ?? [];
$discussions = $discussions ?? [];
$top_discussions = $top_discussions ?? [];
$recent_chat = $recent_chat ?? [];
$stats = $stats ?? ['discussions' => 0, 'live_chat' => 0, 'total_activity_lines' => 0, 'posts' => 0, 'comments' => 0];
$chat_table_ok = (bool) ($chat_table_ok ?? false);
$chat_notice = (string) ($chat_notice ?? '');
$creator_name = (string) ($creator_name ?? '—');
$generated_at = (string) ($generated_at ?? date('Y-m-d H:i'));
$brandingLogo = APP_URL . '/View/assets/images/branding/appolios-hero-logo.png';

$fmtEn = static function (?string $dt): string {
    if ($dt === null || $dt === '') {
        return '—';
    }
    $t = strtotime($dt);

    return $t ? date('M j, Y g:i A', $t) : $dt;
};
$groupName = htmlspecialchars((string) ($g['nom_groupe'] ?? 'Group'), ENT_QUOTES, 'UTF-8');
$discussionCount = count($discussions);
?>

<?php require __DIR__ . '/../partials/collab_layout_start.php'; ?>
<?php require __DIR__ . '/../partials/collab_hub_styles.php'; ?>

<div class="group-activity-report">
    <div class="gar-shell">
        <header class="gar-header">
            <div class="gar-header__brand">
                <img class="gar-logo" src="<?= htmlspecialchars($brandingLogo, ENT_QUOTES, 'UTF-8') ?>" alt="APPOLIOS" width="120" height="80" loading="eager">
                <div>
                    <h1 class="gar-title">Group Activity Report</h1>
                    <p class="gar-sub"><?= $groupName ?> — Generated <?= htmlspecialchars($generated_at, ENT_QUOTES, 'UTF-8') ?></p>
                </div>
            </div>
            <hr class="gar-rule">
        </header>

        <div class="gar-toolbar no-print">
            <div class="gar-toolbar__btns">
                <button type="button" class="gar-btn gar-btn--primary" onclick="window.print()">
                    <i class="bi bi-printer-fill" aria-hidden="true"></i> Print / Save as PDF
                </button>
                <a class="gar-btn gar-btn--muted" href="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/groupes/<?= $groupId ?>">
                    <i class="bi bi-arrow-left" aria-hidden="true"></i> Back
                </a>
            </div>
            <?php if ($chat_notice !== ''): ?>
                <p class="gar-toolbar__notice"><?= htmlspecialchars($chat_notice, ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
        </div>

        <section class="gar-cards" aria-label="Summary metrics">
            <div class="gar-card">
                <div class="gar-card__num"><?= (int) ($stats['discussions'] ?? 0) ?></div>
                <div class="gar-card__lbl">Discussions</div>
            </div>
            <div class="gar-card">
                <div class="gar-card__num"><?= (int) ($stats['live_chat'] ?? 0) ?></div>
                <div class="gar-card__lbl">Live chat messages</div>
            </div>
            <div class="gar-card">
                <div class="gar-card__num"><?= (int) ($stats['total_activity_lines'] ?? 0) ?></div>
                <div class="gar-card__lbl">Total activity lines</div>
            </div>
        </section>

        <section class="gar-section">
            <h2 class="gar-h2">Group overview</h2>
            <dl class="gar-dl">
                <div><dt>Name</dt><dd><?= $groupName ?></dd></div>
                <div><dt>Description</dt><dd><?= htmlspecialchars((string) ($g['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></dd></div>
                <div><dt>Created</dt><dd><?= htmlspecialchars($fmtEn((string) ($g['date_creation'] ?? '')), ENT_QUOTES, 'UTF-8') ?></dd></div>
                <div><dt>Status</dt><dd><span class="gar-pill"><?= htmlspecialchars((string) ($g['approval_statut'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span></dd></div>
                <div><dt>Creator</dt><dd><?= htmlspecialchars($creator_name, ENT_QUOTES, 'UTF-8') ?></dd></div>
            </dl>
        </section>

        <section class="gar-section">
            <h2 class="gar-h2">Members</h2>
            <div class="gar-table-wrap">
                <table class="gar-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($members)): ?>
                            <?php foreach ($members as $m): ?>
                                <tr>
                                    <td><?= htmlspecialchars((string) ($m['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars((string) ($m['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars((string) ($m['role'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars((string) ($m['date_adhesion'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="gar-muted">No members.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="gar-section">
            <h2 class="gar-h2">Discussion summary</h2>
            <p class="gar-muted gar-prose">
                Activity lines count discussions in this group, every persisted live chat message stored in <code>discussion_messages</code>,
                each group post, and each comment on a post (<?= (int) ($stats['posts'] ?? 0) ?> posts, <?= (int) ($stats['comments'] ?? 0) ?> comments in the feed).
            </p>

            <h3 class="gar-h3">Top discussions (most active)</h3>
            <?php if ($discussionCount === 0): ?>
                <p class="gar-muted">No discussions in this group yet.</p>
            <?php elseif (!$chat_table_ok): ?>
                <p class="gar-muted">Ranking by live chat volume requires the <code>discussion_messages</code> table.</p>
                <ul class="gar-list">
                    <?php foreach (array_slice($discussions, 0, 8) as $d): ?>
                        <li><?= htmlspecialchars((string) ($d['titre'] ?? ''), ENT_QUOTES, 'UTF-8') ?> <span class="gar-muted">— <?= htmlspecialchars((string) ($d['date_creation'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <div class="gar-table-wrap">
                    <table class="gar-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Chat messages</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($top_discussions as $d): ?>
                                <tr>
                                    <td><?= htmlspecialchars((string) ($d['titre'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= (int) ($d['chat_messages'] ?? 0) ?></td>
                                    <td><?= htmlspecialchars((string) ($d['date_creation'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <h3 class="gar-h3 gar-h3--spaced">Recent live chat (last 10)</h3>
            <?php if (!$chat_table_ok): ?>
                <p class="gar-muted">Chat persistence unavailable.</p>
            <?php elseif (empty($recent_chat)): ?>
                <p class="gar-muted">No persisted messages yet.</p>
            <?php else: ?>
                <ul class="gar-chat-list">
                    <?php foreach ($recent_chat as $row): ?>
                        <?php
                            $snippet = (string) ($row['message'] ?? '');
                            if ($snippet === '' && !empty($row['file_name'])) {
                                $snippet = '[' . (string) ($row['message_type'] ?? 'file') . '] ' . (string) ($row['file_name'] ?? '');
                            }
                            if (mb_strlen($snippet) > 160) {
                                $snippet = mb_substr($snippet, 0, 160) . '…';
                            }
                        ?>
                        <li>
                            <span class="gar-chat-meta"><?= htmlspecialchars((string) ($row['created_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?> · <?= htmlspecialchars((string) ($row['user_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?> · <?= htmlspecialchars((string) ($row['discussion_title'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
                            <span class="gar-chat-msg"><?= htmlspecialchars($snippet, ENT_QUOTES, 'UTF-8') ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>

        <footer class="gar-footer">APPOLIOS — Group activity report</footer>
    </div>
</div>

<style>
.group-activity-report { --gar-bg: #f8fafc; --gar-paper: #ffffff; --gar-text: #0f172a; --gar-muted: #64748b; --gar-accent: #2563eb; --gar-border: #bfdbfe; --gar-thead: #1e3a5f; }
body.dark-mode .group-activity-report { --gar-bg: #0f172a; --gar-paper: #1e293b; --gar-text: #f1f5f9; --gar-muted: #94a3b8; --gar-accent: #60a5fa; --gar-border: #334155; --gar-thead: #0f172a; }
.group-activity-report { background: var(--gar-bg); margin: -0.5rem -1rem 1.5rem; padding: 1.25rem 1rem 2rem; border-radius: 12px; }
.gar-shell { max-width: 900px; margin: 0 auto; background: var(--gar-paper); border: 1px solid var(--gar-border); border-radius: 16px; padding: 1.5rem 1.35rem 1.25rem; box-shadow: 0 4px 24px rgba(15, 23, 42, 0.06); }
body.dark-mode .gar-shell { box-shadow: none; }
.gar-header__brand { display: flex; align-items: center; gap: 1rem; flex-wrap: wrap; }
.gar-logo { height: 52px; width: auto; object-fit: contain; }
.gar-title { font-family: Georgia, 'Times New Roman', serif; font-size: 1.75rem; font-weight: 800; color: #1e3a5f; margin: 0; letter-spacing: -0.02em; }
body.dark-mode .gar-title { color: #e2e8f0; }
.gar-sub { margin: 0.35rem 0 0; color: var(--gar-muted); font-size: 0.95rem; }
.gar-rule { border: none; border-top: 1px solid var(--gar-border); margin: 1rem 0 0; }
.gar-toolbar { display: flex; flex-wrap: wrap; align-items: flex-start; gap: 0.75rem 1.25rem; margin-top: 1rem; margin-bottom: 1.25rem; }
.gar-toolbar__btns { display: flex; flex-wrap: wrap; gap: 0.5rem; }
.gar-toolbar__notice { flex: 1 1 220px; margin: 0; font-size: 0.82rem; color: var(--gar-muted); line-height: 1.45; }
.gar-btn { display: inline-flex; align-items: center; gap: 0.45rem; padding: 0.55rem 1rem; border-radius: 10px; font-weight: 600; font-size: 0.9rem; text-decoration: none; border: none; cursor: pointer; }
.gar-btn--primary { background: #64748b; color: #fff; }
.gar-btn--primary:hover { filter: brightness(1.05); }
.gar-btn--muted { background: #334155; color: #fff; }
.gar-btn--muted:hover { filter: brightness(1.08); }
body.dark-mode .gar-btn--primary { background: #475569; }
body.dark-mode .gar-btn--muted { background: #0f172a; color: #e2e8f0; border: 1px solid #334155; }
.gar-cards { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 0.75rem; margin-bottom: 1.5rem; }
@media (max-width: 640px) { .gar-cards { grid-template-columns: 1fr; } }
.gar-card { border: 1px solid var(--gar-border); border-radius: 12px; padding: 0.85rem 1rem; text-align: center; background: var(--gar-bg); }
.gar-card__num { font-size: 1.65rem; font-weight: 900; color: var(--gar-accent); line-height: 1.1; }
.gar-card__lbl { margin-top: 0.35rem; font-size: 0.72rem; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; color: var(--gar-muted); }
.gar-section { margin-bottom: 1.35rem; padding-bottom: 1.25rem; border-bottom: 1px solid var(--gar-border); }
.gar-section:last-of-type { border-bottom: none; }
.gar-h2 { font-size: 1.1rem; font-weight: 800; color: #1e3a5f; margin: 0 0 0.75rem; }
body.dark-mode .gar-h2 { color: #e2e8f0; }
.gar-h3 { font-size: 0.98rem; font-weight: 800; color: var(--gar-text); margin: 0 0 0.5rem; }
.gar-h3--spaced { margin-top: 1.25rem; }
.gar-dl { margin: 0; display: grid; gap: 0.55rem 1rem; }
.gar-dl > div { display: grid; grid-template-columns: 120px 1fr; gap: 0.5rem; align-items: baseline; }
@media (max-width: 520px) { .gar-dl > div { grid-template-columns: 1fr; } }
.gar-dl dt { margin: 0; font-weight: 700; color: var(--gar-muted); font-size: 0.88rem; }
.gar-dl dd { margin: 0; color: var(--gar-text); font-size: 0.95rem; }
.gar-pill { display: inline-block; padding: 0.15rem 0.55rem; border-radius: 999px; background: #e0f2fe; color: #0369a1; font-size: 0.85rem; font-weight: 700; }
body.dark-mode .gar-pill { background: #1e3a5f; color: #7dd3fc; }
.gar-muted { color: var(--gar-muted); }
.gar-prose { font-size: 0.88rem; line-height: 1.55; margin: 0 0 1rem; }
.gar-prose code { font-size: 0.82em; background: var(--gar-bg); padding: 0.1em 0.35em; border-radius: 4px; }
.gar-table-wrap { border: 1px solid var(--gar-border); border-radius: 12px; overflow: hidden; }
.gar-table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
.gar-table thead tr { background: var(--gar-thead); color: #fff; }
.gar-table th { text-align: left; padding: 0.55rem 0.65rem; font-weight: 800; font-size: 0.8rem; }
.gar-table td { padding: 0.55rem 0.65rem; border-top: 1px solid var(--gar-border); color: var(--gar-text); }
.gar-list { margin: 0.25rem 0 0; padding-left: 1.2rem; color: var(--gar-text); }
.gar-chat-list { list-style: none; margin: 0.5rem 0 0; padding: 0; display: flex; flex-direction: column; gap: 0.65rem; }
.gar-chat-list li { border: 1px solid var(--gar-border); border-radius: 10px; padding: 0.5rem 0.65rem; background: var(--gar-bg); }
.gar-chat-meta { display: block; font-size: 0.78rem; color: var(--gar-muted); margin-bottom: 0.25rem; }
.gar-chat-msg { font-size: 0.88rem; color: var(--gar-text); word-break: break-word; }
.gar-footer { text-align: center; font-size: 0.8rem; color: #94a3b8; margin-top: 0.5rem; padding-top: 0.75rem; }
@media print {
    .no-print, .admin-side-nav, .neo-header, .app-footer, #page-loader { display: none !important; }
    body { background: #fff !important; }
    body.dark-mode { background: #fff !important; }
    .dashboard, .admin-main { padding: 0 !important; margin: 0 !important; }
    .admin-layout { display: block !important; }
    .group-activity-report { margin: 0; padding: 0; background: #fff; }
    body.dark-mode .group-activity-report { --gar-bg: #fff; --gar-paper: #fff; --gar-text: #0f172a; --gar-muted: #64748b; --gar-border: #e2e8f0; --gar-thead: #1e3a5f; }
    .gar-shell { box-shadow: none; border: none; max-width: 100%; }
}
</style>

<?php require __DIR__ . '/../partials/collab_layout_end.php'; ?>
