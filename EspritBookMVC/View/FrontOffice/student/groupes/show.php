<?php
// Canonical nested view path (replaces flat file; identical markup to former flat sibling).
$foPrefix = $foPrefix ?? 'student';
$member_chips = $member_chips ?? [];
$group_cover_url = (string) ($group_cover_url ?? '');
$is_owner_viewer = (bool) ($is_owner_viewer ?? false);
$can_post_wall = (bool) ($can_post_wall ?? false);
$show_group_join_cta = (bool) ($show_group_join_cta ?? false);
$group_post_cards = $group_post_cards ?? [];
$group_post_error_messages = $group_post_error_messages ?? [];
$group_post_old = $group_post_old ?? [];
?>
<div class="dashboard student-events-page collab-hub collab-hub--detail">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/../partials/sidebar.php'; ?>
            <div class="admin-main">
                <?php require __DIR__ . '/../partials/collab_hub_styles.php'; ?>

                <div class="collab-detail-hero">
                    <div class="collab-detail-banner">
                        <?php if ($group_cover_url !== ''): ?>
                            <img src="<?= htmlspecialchars($group_cover_url) ?>" alt="<?= htmlspecialchars($groupe['nom_groupe'] ?? 'Group') ?>" loading="lazy" onerror="this.style.display='none';">
                        <?php endif; ?>
                        <div class="collab-detail-banner__inner">
                            <div class="collab-eyebrow" style="opacity:.95;"><i class="bi bi-diagram-3-fill" aria-hidden="true"></i> Group workspace</div>
                            <h1><?= htmlspecialchars($groupe['nom_groupe'] ?? 'Group') ?></h1>
                            <p><?= htmlspecialchars((string) ($groupe['description'] ?? '')) ?></p>
                            <div style="margin-top:1rem;display:flex;flex-wrap:wrap;gap:0.5rem;">
                                <?php if ($is_owner_viewer): ?>
                                    <a class="collab-btn-primary" target="_blank" rel="noopener" href="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/groupes/<?= (int) $groupe['id_groupe'] ?>/activity-report">
                                        <i class="bi bi-file-earmark-bar-graph" aria-hidden="true"></i> Activity report (PDF)
                                    </a>
                                    <a class="collab-btn-primary" href="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/groupes/<?= (int) $groupe['id_groupe'] ?>/edit" style="background:linear-gradient(135deg,#475569,#334155);">
                                        <i class="bi bi-pencil-square" aria-hidden="true"></i> Edit group
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="aside collab-detail-sidecard">
                        <h3>Members</h3>
                        <?php if (!empty($member_chips)): ?>
                            <div class="collab-member-row">
                                <?php foreach ($member_chips as $chip): ?>
                                    <div class="collab-member-chip">
                                        <span class="collab-member-avatar"><?= htmlspecialchars((string) ($chip['avatar_initial'] ?? '?')) ?></span>
                                        <span><?= htmlspecialchars((string) ($chip['display_name'] ?? 'Member')) ?></span>
                                        <span class="collab-role-tag"><?= htmlspecialchars((string) ($chip['role_label'] ?? '')) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p style="margin:0;color:var(--ch-muted);font-size:0.9rem;">No members yet.</p>
                        <?php endif; ?>
                        <div style="margin-top:1.25rem;">
                            <a href="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/groupes" class="collab-chip-btn collab-chip-btn--muted">
                                <i class="bi bi-arrow-left" aria-hidden="true"></i> All groups
                            </a>
                        </div>
                    </div>
                </div>

                <div class="section collab-thread-panel collab-feed-panel">
                    <h3><i class="bi bi-columns-gap" style="color:var(--ch-teal-soft);margin-right:.35rem;" aria-hidden="true"></i> Group feed</h3>
                    <p style="margin:0 0 1rem;font-size:0.9rem;color:var(--ch-muted);max-width:52rem;">Share updates with everyone in this group. Posts appear here in reverse order (newest first).</p>

                    <?php if ($can_post_wall): ?>
                        <div class="collab-compose collab-feed-compose">
                            <div style="font-size:0.78rem;font-weight:800;letter-spacing:0.06em;text-transform:uppercase;color:var(--ch-muted);margin-bottom:0.65rem;">Write a post</div>
                            <?php if (!empty($group_post_error_messages)): ?>
                                <div class="collab-alert-soft" style="border-color:rgba(239,68,68,0.35);background:linear-gradient(135deg,#fef2f2,#fff);color:#991b1b;">
                                    <?php foreach ($group_post_error_messages as $err): ?>
                                        <div><?= htmlspecialchars((string) $err) ?></div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            <form method="POST" action="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/groupes/<?= (int) $groupe['id_groupe'] ?>/posts/store">
                                <div class="form-group">
                                    <label for="gp_body">What is on your mind?</label>
                                    <textarea id="gp_body" name="body" rows="4" placeholder="Share an update, link, or question…"><?= htmlspecialchars((string) ($group_post_old['body'] ?? '')) ?></textarea>
                                </div>
                                <button class="collab-btn-primary" type="submit" style="box-shadow:none;"><i class="bi bi-send-fill" aria-hidden="true"></i> Post</button>
                            </form>
                        </div>
                    <?php elseif ($show_group_join_cta): ?>
                        <div class="collab-empty collab-feed-join-hint" style="padding:1.25rem 1rem;margin-bottom:1rem;">
                            <p style="margin:0 0 0.75rem;color:var(--ch-muted);">Join this group to post updates on the wall.</p>
                            <a class="collab-btn-primary" href="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/groupes/<?= (int) $groupe['id_groupe'] ?>/join"><i class="bi bi-person-plus" aria-hidden="true"></i> Join group</a>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($group_post_cards)): ?>
                        <?php foreach ($group_post_cards as $pc): ?>
                            <div class="article collab-thread-card collab-feed-post">
                                <div class="collab-thread-meta collab-feed-post__meta">
                                    <span class="collab-feed-post__avatar" aria-hidden="true"><?php $an = (string) ($pc['author_name'] ?? '?'); echo htmlspecialchars($an !== '' ? strtoupper(substr($an, 0, 1)) : '?'); ?></span>
                                    <span><strong><?= htmlspecialchars((string) ($pc['author_name'] ?? 'Member'), ENT_QUOTES, 'UTF-8') ?></strong></span>
                                    <?php if (!empty($pc['created_at'])): ?>
                                        <span style="opacity:.65;font-size:0.82rem;"> · <?= htmlspecialchars((string) $pc['created_at'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="collab-feed-post__body"><?= nl2br(htmlspecialchars((string) ($pc['body'] ?? ''), ENT_QUOTES, 'UTF-8')) ?></div>
                                <?php if (!empty($pc['can_delete'])): ?>
                                    <div class="collab-card-actions" style="margin-top:0.75rem;">
                                        <a class="collab-chip-btn collab-chip-btn--danger" href="<?= htmlspecialchars((string) ($pc['url_delete'] ?? '#'), ENT_QUOTES, 'UTF-8') ?>">
                                            <i class="bi bi-trash" aria-hidden="true"></i> Delete
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="collab-empty collab-feed-empty" style="padding:1.5rem 1rem;margin-bottom:0.5rem;">
                            <p style="margin:0;color:var(--ch-muted);font-size:0.92rem;"><?= $can_post_wall ? 'No posts yet — be the first to share something with the group.' : 'No posts yet.' ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
