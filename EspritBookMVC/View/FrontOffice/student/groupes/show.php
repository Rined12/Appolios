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
$discussions_for_share = $discussions_for_share ?? [];
$group_post_reaction_types = $group_post_reaction_types ?? [];
$wall_composer_name = trim((string) ($wall_composer_name ?? 'Member'));
$wall_composer_initial = (string) ($wall_composer_initial ?? '?');
if ($wall_composer_initial === '' || $wall_composer_initial === '?') {
    $wall_composer_initial = strtoupper(substr($wall_composer_name !== '' ? $wall_composer_name : 'M', 0, 1));
}
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
                    <header class="collab-feed-panel__head">
                        <div class="collab-feed-panel__head-icon" aria-hidden="true"><i class="bi bi-collection-play-fill"></i></div>
                        <div>
                            <h3 class="collab-feed-panel__title">Group feed</h3>
                            <p class="collab-feed-panel__subtitle">Post updates with text or media, react and comment, then share a post into a discussion to open it in live chat.</p>
                        </div>
                    </header>

                    <?php if ($can_post_wall): ?>
                        <div class="collab-feed-composer-card">
                            <div class="collab-feed-composer__head">
                                <div class="collab-feed-composer__avatar" aria-hidden="true"><?= htmlspecialchars($wall_composer_initial, ENT_QUOTES, 'UTF-8') ?></div>
                                <div class="collab-feed-composer__head-text">
                                    <div class="collab-feed-composer__kicker">Create post</div>
                                    <div class="collab-feed-composer__as">Posting as <strong><?= htmlspecialchars($wall_composer_name, ENT_QUOTES, 'UTF-8') ?></strong></div>
                                </div>
                            </div>
                            <?php if (!empty($group_post_error_messages)): ?>
                                <div class="collab-feed-composer__alert collab-alert-soft">
                                    <?php foreach ($group_post_error_messages as $err): ?>
                                        <div><?= htmlspecialchars((string) $err) ?></div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            <form class="collab-feed-composer__form" method="POST" enctype="multipart/form-data" action="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/groupes/<?= (int) $groupe['id_groupe'] ?>/posts/store">
                                <label class="sr-only" for="gp_body">Post text</label>
                                <textarea id="gp_body" class="collab-feed-composer__textarea" name="body" rows="3" placeholder="<?= htmlspecialchars($wall_composer_name, ENT_QUOTES, 'UTF-8') ?>, what do you want to share?"><?= htmlspecialchars((string) ($group_post_old['body'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                                <div class="collab-feed-composer__toolbar">
                                    <label class="collab-feed-composer__attach" aria-label="Add image, video, audio, or document (max 10 MB)">
                                        <span class="collab-feed-composer__attach-icon"><i class="bi bi-images" aria-hidden="true"></i></span>
                                        <span class="collab-feed-composer__attach-text">Photo / video / file</span>
                                        <input type="file" name="attachment" accept="image/*,video/*,audio/*,.pdf,.zip,.doc,.docx,.txt">
                                    </label>
                                    <span class="collab-feed-composer__toolbar-hint">Up to 10 MB · optional if you write a message</span>
                                    <button type="submit" class="collab-feed-composer__submit">
                                        <i class="bi bi-send-fill" aria-hidden="true"></i> Post
                                    </button>
                                </div>
                            </form>
                        </div>
                    <?php elseif ($show_group_join_cta): ?>
                        <div class="collab-feed-join-card">
                            <div class="collab-feed-join-card__icon" aria-hidden="true"><i class="bi bi-person-plus"></i></div>
                            <p class="collab-feed-join-card__text">Join this group to post, comment, react, and share on the wall.</p>
                            <a class="collab-feed-composer__submit collab-feed-join-card__btn" href="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/groupes/<?= (int) $groupe['id_groupe'] ?>/join"><i class="bi bi-box-arrow-in-right" aria-hidden="true"></i> Join group</a>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($group_post_cards)): ?>
                        <?php foreach ($group_post_cards as $pc): ?>
                            <article class="collab-feed-post" id="post-<?= (int) ($pc['id'] ?? 0) ?>">
                                <header class="collab-feed-post__hd">
                                    <div class="collab-feed-post__hd-main">
                                        <span class="collab-feed-post__avatar" aria-hidden="true"><?php $an = (string) ($pc['author_name'] ?? '?'); echo htmlspecialchars($an !== '' ? strtoupper(substr($an, 0, 1)) : '?'); ?></span>
                                        <div class="collab-feed-post__who">
                                            <div class="collab-feed-post__name"><?= htmlspecialchars((string) ($pc['author_name'] ?? 'Member'), ENT_QUOTES, 'UTF-8') ?></div>
                                            <?php if (!empty($pc['created_at'])): ?>
                                                <time class="collab-feed-post__time" datetime=""><?= htmlspecialchars((string) $pc['created_at'], ENT_QUOTES, 'UTF-8') ?></time>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php if (!empty($pc['can_delete'])): ?>
                                        <a class="collab-feed-post__delete" href="<?= htmlspecialchars((string) ($pc['url_delete'] ?? '#'), ENT_QUOTES, 'UTF-8') ?>" title="Delete post"><i class="bi bi-trash3" aria-hidden="true"></i><span class="sr-only">Delete post</span></a>
                                    <?php endif; ?>
                                </header>
                                <?php if (trim((string) ($pc['body'] ?? '')) !== ''): ?>
                                    <div class="collab-feed-post__body"><?= nl2br(htmlspecialchars((string) ($pc['body'] ?? ''), ENT_QUOTES, 'UTF-8')) ?></div>
                                <?php endif; ?>
                                <?php
                                $mUrl = (string) ($pc['media_url'] ?? '');
                                $mType = (string) ($pc['media_type'] ?? '');
                                $mFn = (string) ($pc['media_filename'] ?? '');
                                ?>
                                <?php if ($mUrl !== ''): ?>
                                    <div class="collab-feed-post__media">
                                        <?php if ($mType === 'image'): ?>
                                            <a href="<?= htmlspecialchars($mUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener"><img src="<?= htmlspecialchars($mUrl, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($mFn !== '' ? $mFn : 'Image', ENT_QUOTES, 'UTF-8') ?>" loading="lazy"></a>
                                        <?php elseif ($mType === 'video'): ?>
                                            <video controls preload="metadata" playsinline src="<?= htmlspecialchars($mUrl, ENT_QUOTES, 'UTF-8') ?>"></video>
                                        <?php elseif ($mType === 'audio'): ?>
                                            <audio controls preload="metadata" src="<?= htmlspecialchars($mUrl, ENT_QUOTES, 'UTF-8') ?>"></audio>
                                        <?php else: ?>
                                            <a class="collab-chip-btn collab-chip-btn--muted" href="<?= htmlspecialchars($mUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener"><i class="bi bi-paperclip" aria-hidden="true"></i> <?= htmlspecialchars($mFn !== '' ? $mFn : 'Download file', ENT_QUOTES, 'UTF-8') ?></a>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($pc['can_react'])): ?>
                                    <div class="collab-feed-reactions" aria-label="Reactions">
                                        <span class="collab-feed-reactions__label">React</span>
                                        <?php foreach ($group_post_reaction_types as $rt): ?>
                                            <?php
                                            $rk = (string) ($rt['key'] ?? '');
                                            $em = (string) ($rt['emoji'] ?? '');
                                            $isActive = ($pc['user_reaction'] ?? null) === $rk;
                                            ?>
                                            <form method="POST" action="<?= htmlspecialchars((string) ($pc['url_reaction_store'] ?? '#'), ENT_QUOTES, 'UTF-8') ?>" class="collab-feed-reaction-form">
                                                <input type="hidden" name="reaction" value="<?= htmlspecialchars($rk, ENT_QUOTES, 'UTF-8') ?>">
                                                <button type="submit" class="collab-feed-reaction-btn<?= $isActive ? ' is-active' : '' ?>" title="<?= htmlspecialchars((string) ($rt['label'] ?? $rk), ENT_QUOTES, 'UTF-8') ?>" aria-pressed="<?= $isActive ? 'true' : 'false' ?>">
                                                    <?= htmlspecialchars($em, ENT_QUOTES, 'UTF-8') ?>
                                                    <?php
                                                    $cnt = (int) (($pc['reaction_counts'] ?? [])[$rk] ?? 0);
                                                    if ($cnt > 0):
                                                    ?>
                                                        <span class="collab-feed-reaction-count"><?= (int) $cnt ?></span>
                                                    <?php endif; ?>
                                                </button>
                                            </form>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($pc['comments'])): ?>
                                    <div class="collab-feed-comments">
                                        <?php foreach ($pc['comments'] as $cm): ?>
                                            <div class="collab-feed-comment">
                                                <strong><?= htmlspecialchars((string) ($cm['author_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></strong>
                                                <span class="collab-feed-comment__meta"><?= htmlspecialchars((string) ($cm['created_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
                                                <div><?= nl2br(htmlspecialchars((string) ($cm['body'] ?? ''), ENT_QUOTES, 'UTF-8')) ?></div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($pc['can_comment'])): ?>
                                    <form method="POST" action="<?= htmlspecialchars((string) ($pc['url_comment_store'] ?? '#'), ENT_QUOTES, 'UTF-8') ?>" class="collab-feed-comment-form">
                                        <label class="sr-only" for="cmt-<?= (int) ($pc['id'] ?? 0) ?>">Comment</label>
                                        <textarea id="cmt-<?= (int) ($pc['id'] ?? 0) ?>" name="body" rows="2" maxlength="2000" placeholder="Write a comment…" required></textarea>
                                        <button type="submit" class="collab-chip-btn collab-chip-btn--live">Comment</button>
                                    </form>
                                <?php endif; ?>

                                <?php if (!empty($pc['can_share'])): ?>
                                    <div class="collab-feed-share">
                                        <span class="collab-feed-share__label"><i class="bi bi-share" aria-hidden="true"></i> Share to discussion</span>
                                        <?php if (!empty($discussions_for_share)): ?>
                                            <form method="POST" action="<?= htmlspecialchars((string) ($pc['url_share_store'] ?? '#'), ENT_QUOTES, 'UTF-8') ?>" class="collab-feed-share-form">
                                                <select name="id_discussion" required aria-label="Pick discussion">
                                                    <option value="">Select discussion…</option>
                                                    <?php foreach ($discussions_for_share as $dopt): ?>
                                                        <option value="<?= (int) ($dopt['id'] ?? 0) ?>"><?= htmlspecialchars((string) ($dopt['titre'] ?? 'Discussion'), ENT_QUOTES, 'UTF-8') ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <button type="submit" class="collab-chip-btn collab-chip-btn--muted">Share</button>
                                            </form>
                                        <?php else: ?>
                                            <p class="collab-feed-share__empty">No discussions in this group yet — create one from the Discussions menu.</p>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($pc['shares'])): ?>
                                    <div class="collab-feed-shares-log">
                                        <span class="collab-feed-shares-log__title">Shared to</span>
                                        <ul>
                                            <?php foreach ($pc['shares'] as $sh): ?>
                                                <li>
                                                    <a href="<?= htmlspecialchars((string) ($sh['chat_url'] ?? '#'), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) ($sh['discussion_title'] ?? 'Chat'), ENT_QUOTES, 'UTF-8') ?></a>
                                                    <span class="collab-feed-shares-log__time"><?= htmlspecialchars((string) ($sh['created_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                            </article>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="collab-feed-empty" role="status">
                            <div class="collab-feed-empty__icon" aria-hidden="true"><i class="bi bi-chat-square-text"></i></div>
                            <p class="collab-feed-empty__text"><?= $can_post_wall ? 'No posts yet — be the first to share something with the group.' : 'No posts yet.' ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
