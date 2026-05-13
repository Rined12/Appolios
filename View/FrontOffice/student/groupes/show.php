<?php
$foPrefix = (string) ($foPrefix ?? 'student');
$g = $groupe ?? [];
$groupId = (int) ($g['id_groupe'] ?? 0);
$isOwner = (bool) ($is_owner_viewer ?? false);
$coverRaw = (string) ($group_cover_url ?? ($g['image_url'] ?? ''));
$cover = '';
if ($coverRaw !== '') {
    $cover = preg_match('~^https?://~i', $coverRaw) ? $coverRaw : (APP_URL . '/' . ltrim($coverRaw, '/'));
}
$groupPosts = $group_posts ?? [];
$memberChips = $member_chips ?? [];
$isGroupMember = (bool) ($is_group_member ?? false);
?>

<?php require __DIR__ . '/../partials/collab_layout_start.php'; ?>
                <?php require __DIR__ . '/../partials/collab_hub_styles.php'; ?>

                <div class="collab-detail-hero">
                    <div class="collab-detail-banner">
                        <?php if ($cover !== ''): ?>
                            <img src="<?= htmlspecialchars($cover, ENT_QUOTES, 'UTF-8') ?>" alt="" loading="lazy" onerror="this.remove();">
                        <?php endif; ?>
                        <div class="collab-detail-banner__inner">
                            <h1><?= htmlspecialchars((string) ($g['nom_groupe'] ?? 'Group'), ENT_QUOTES, 'UTF-8') ?></h1>
                            <p><?= htmlspecialchars((string) ($g['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                            <div style="margin-top:1rem;display:flex;flex-wrap:wrap;gap:.6rem;">
                                <a class="collab-btn-ghost" href="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/groupes"><i class="bi bi-arrow-left"></i> Back</a>
                                <?php if ($isOwner): ?>
                                    <a class="collab-btn-ghost" href="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/groupes/<?= $groupId ?>/activity-pdf" title="Print or save group activity report"><i class="bi bi-printer"></i> Activity report</a>
                                    <a class="collab-btn-primary" href="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/groupes/<?= $groupId ?>/edit"><i class="bi bi-sliders"></i> Manage</a>
                                <?php endif; ?>
                                <a class="collab-btn-ghost" href="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/discussions/create"><i class="bi bi-plus-lg"></i> New discussion</a>
                            </div>
                        </div>
                    </div>
                    <div class="collab-detail-sidecard">
                        <h3>Members</h3>
                        <?php if (!empty($memberChips)): ?>
                            <div class="collab-members-list">
                                <?php foreach (array_slice($memberChips, 0, 8) as $m): ?>
                                    <?php
                                        $mn = (string) ($m['name'] ?? 'User');
                                        $mr = (string) ($m['role'] ?? '');
                                        $initial = $mn !== '' ? mb_strtoupper(mb_substr($mn, 0, 1)) : 'U';
                                    ?>
                                    <div class="collab-member-chip">
                                        <span class="collab-member-avatar" aria-hidden="true"><?= htmlspecialchars($initial, ENT_QUOTES, 'UTF-8') ?></span>
                                        <span class="collab-member-name"><?= htmlspecialchars($mn, ENT_QUOTES, 'UTF-8') ?></span>
                                        <?php if ($mr !== ''): ?>
                                            <span class="collab-member-role"><?= htmlspecialchars(mb_strtoupper($mr), ENT_QUOTES, 'UTF-8') ?></span>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div style="color:#64748b;font-size:.92rem;line-height:1.55;">No members yet.</div>
                        <?php endif; ?>
                        <div style="margin-top:1rem;">
                            <a class="collab-chip-btn collab-chip-btn--muted" href="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/groupes"><i class="bi bi-arrow-left" aria-hidden="true"></i> All groups</a>
                        </div>
                    </div>
                </div>

                <div class="collab-thread-panel" style="margin-top:1.35rem;">
                    <div class="collab-feed-card">
                        <div class="collab-feed-card__head">
                            <span class="collab-feed-icon" aria-hidden="true"><i class="bi bi-journal-richtext"></i></span>
                            <h3 class="collab-feed-title">Group posts</h3>
                        </div>

                        <?php if ($isGroupMember): ?>
                        <div class="collab-composer">
                            <form method="post" action="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/groupes/<?= $groupId ?>/post" enctype="multipart/form-data" class="collab-composer__grid">
                                <?php
                                    $meName = (string) ($_SESSION['user_name'] ?? '');
                                    $meLetter = $meName !== '' ? mb_strtoupper(mb_substr($meName, 0, 1)) : 'U';
                                ?>
                                <div class="collab-avatar" aria-hidden="true"><?= htmlspecialchars($meLetter, ENT_QUOTES, 'UTF-8') ?></div>
                                <div style="min-width:0;">
                                    <h4 class="collab-composer__title">Créer une publication</h4>
                                    <div class="collab-composer__name"><?= htmlspecialchars($meName !== '' ? $meName : 'User', ENT_QUOTES, 'UTF-8') ?></div>
                                    <div style="margin-top:.75rem;">
                                        <textarea id="gp_content" name="content" placeholder="Que voulez-vous dire ?"></textarea>
                                    </div>

                                    <div class="collab-composer__toolbar">
                                        <div class="collab-composer__tools" aria-label="Ajouter à votre publication">
                                            <span style="font-weight:850;color:#334155;font-size:.9rem;">Ajouter à votre publication</span>
                                            <label class="collab-tool-btn" title="Ajouter une image" aria-label="Ajouter une image" style="margin-left:auto;">
                                                <i class="bi bi-image"></i>
                                                <input type="file" name="media" accept="image/*,video/*,audio/*,.pdf,.doc,.docx" style="display:none;" />
                                            </label>
                                            <button type="button" class="collab-tool-btn" title="Video" aria-label="Video" onclick="document.querySelector('input[name=media]') && document.querySelector('input[name=media]').click();">
                                                <i class="bi bi-camera-video"></i>
                                            </button>
                                            <button type="button" class="collab-tool-btn" title="Emoji" aria-label="Emoji" onclick="document.getElementById('gp_content') && document.getElementById('gp_content').focus();">
                                                <i class="bi bi-emoji-smile"></i>
                                            </button>
                                            <button type="button" class="collab-tool-btn" title="More" aria-label="More" onclick="document.querySelector('input[name=media]') && document.querySelector('input[name=media]').click();">
                                                <i class="bi bi-three-dots"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <button type="submit" class="collab-btn-primary collab-publish">Publier</button>
                                </div>
                            </form>
                        </div>
                        <?php else: ?>
                        <div class="collab-composer collab-composer--locked" style="padding:1.35rem 1.25rem;border-radius:16px;background:linear-gradient(135deg, rgba(241,245,249,.95), rgba(226,232,240,.55));border:1px solid rgba(148,163,184,.35);">
                            <div style="display:flex;gap:1rem;align-items:flex-start;flex-wrap:wrap;">
                                <div class="collab-feed-icon" style="font-size:1.75rem;opacity:.9;" aria-hidden="true"><i class="bi bi-lock-fill"></i></div>
                                <div style="flex:1;min-width:220px;">
                                    <h4 class="collab-composer__title" style="margin:0 0 .4rem 0;">Members only</h4>
                                    <p style="margin:0;color:#475569;font-size:.95rem;line-height:1.55;">Join this group to view posts, react, comment, and share updates.</p>
                                    <div style="margin-top:1rem;">
                                        <a class="collab-btn-primary" href="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/groupes/<?= $groupId ?>/join"><i class="bi bi-person-plus" aria-hidden="true"></i> Join group</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($isGroupMember): ?>
                    <?php if (!empty($groupPosts)): ?>
                        <div style="display:grid;gap:1rem;margin-top:1rem;">
                            <?php foreach ($groupPosts as $post): ?>
                                <?php
                                    $postId = (int) ($post['id'] ?? 0);
                                    $postOwner = (string) ($post['user_name'] ?? 'User');
                                    $postContent = (string) ($post['content'] ?? '');
                                    $postMediaUrlRaw = (string) ($post['media_url'] ?? '');
                                    $postMediaKind = (string) ($post['media_kind'] ?? '');
                                    $postMediaUrl = '';
                                    if ($postMediaUrlRaw !== '') {
                                        $postMediaUrl = preg_match('~^https?://~i', $postMediaUrlRaw) ? $postMediaUrlRaw : (APP_URL . '/' . ltrim($postMediaUrlRaw, '/'));
                                    }
                                    $reactions = is_array($post['reactions'] ?? null) ? $post['reactions'] : [];
                                    $viewerReaction = (string) ($post['viewer_reaction'] ?? '');
                                    $comments = is_array($post['comments'] ?? null) ? $post['comments'] : [];
                                    $isPostOwner = (bool) ($post['is_owner_viewer'] ?? false);
                                ?>
                                <article class="collab-post">
                                    <div class="collab-post__meta">
                                        <div>
                                            <div class="collab-post__author"><?= htmlspecialchars($postOwner, ENT_QUOTES, 'UTF-8') ?></div>
                                            <div class="collab-post__time"><?= htmlspecialchars((string) ($post['created_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
                                        </div>
                                        <?php if ($isPostOwner): ?>
                                            <a class="collab-chip-btn collab-chip-btn--danger" href="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/groupes/<?= $groupId ?>/delete-post/<?= $postId ?>" style="height:34px;"><i class="bi bi-trash"></i> Delete</a>
                                        <?php endif; ?>
                                    </div>

                                    <?php if ($postContent !== ''): ?>
                                        <div class="collab-post__content"><?= htmlspecialchars($postContent, ENT_QUOTES, 'UTF-8') ?></div>
                                    <?php endif; ?>

                                    <?php if ($postMediaUrl !== ''): ?>
                                        <div class="collab-post__media">
                                            <?php if ($postMediaKind === 'image'): ?>
                                                <img src="<?= htmlspecialchars($postMediaUrl, ENT_QUOTES, 'UTF-8') ?>" alt="" style="max-width:100%;border-radius:16px;border:1px solid rgba(148,163,184,.35);" loading="lazy" />
                                            <?php elseif ($postMediaKind === 'video'): ?>
                                                <video src="<?= htmlspecialchars($postMediaUrl, ENT_QUOTES, 'UTF-8') ?>" controls style="max-width:100%;border-radius:16px;border:1px solid rgba(148,163,184,.35);"></video>
                                            <?php elseif ($postMediaKind === 'audio'): ?>
                                                <audio src="<?= htmlspecialchars($postMediaUrl, ENT_QUOTES, 'UTF-8') ?>" controls style="width:100%;"></audio>
                                            <?php else: ?>
                                                <a class="collab-chip-btn collab-chip-btn--muted" href="<?= htmlspecialchars($postMediaUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">Open attachment</a>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>

                                    <div class="collab-post__actions">
                                        <form method="post" action="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/groupes/<?= $groupId ?>/react" style="display:inline;">
                                            <input type="hidden" name="post_id" value="<?= $postId ?>" />
                                            <input type="hidden" name="reaction" value="like" />
                                            <button type="submit" class="collab-chip-btn <?= $viewerReaction === 'like' ? 'collab-chip-btn--live' : 'collab-chip-btn--muted' ?>">
                                                <i class="bi bi-hand-thumbs-up"></i> Like (<?= (int) ($reactions['like'] ?? 0) ?>)
                                            </button>
                                        </form>

                                        <form method="post" action="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/groupes/<?= $groupId ?>/react" style="display:inline;">
                                            <input type="hidden" name="post_id" value="<?= $postId ?>" />
                                            <input type="hidden" name="reaction" value="love" />
                                            <button type="submit" class="collab-chip-btn <?= $viewerReaction === 'love' ? 'collab-chip-btn--live' : 'collab-chip-btn--muted' ?>">
                                                <i class="bi bi-heart-fill"></i> Love (<?= (int) ($reactions['love'] ?? 0) ?>)
                                            </button>
                                        </form>

                                        <form method="post" action="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/groupes/<?= $groupId ?>/react" style="display:inline;">
                                            <input type="hidden" name="post_id" value="<?= $postId ?>" />
                                            <input type="hidden" name="reaction" value="wow" />
                                            <button type="submit" class="collab-chip-btn <?= $viewerReaction === 'wow' ? 'collab-chip-btn--live' : 'collab-chip-btn--muted' ?>">
                                                <i class="bi bi-stars"></i> Wow (<?= (int) ($reactions['wow'] ?? 0) ?>)
                                            </button>
                                        </form>
                                    </div>

                                    <div class="collab-post__comments">
                                        <form method="post" action="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/groupes/<?= $groupId ?>/comment" style="display:flex;gap:.6rem;align-items:flex-start;flex-wrap:wrap;">
                                            <input type="hidden" name="post_id" value="<?= $postId ?>" />
                                            <input type="text" name="content" placeholder="Write a comment…" style="flex:1 1 240px;min-width:220px;border-radius:12px;border:1px solid rgba(148,163,184,.35);padding:.6rem .75rem;" />
                                            <button type="submit" class="collab-btn-ghost" style="height:42px;">Comment</button>
                                        </form>

                                        <?php if (!empty($comments)): ?>
                                            <div style="display:grid;gap:.55rem;margin-top:.9rem;">
                                                <?php foreach ($comments as $comment): ?>
                                                    <?php
                                                        $commentId = (int) ($comment['id'] ?? 0);
                                                        $commentOwner = (string) ($comment['user_name'] ?? 'User');
                                                        $commentContent = (string) ($comment['content'] ?? '');
                                                        $commentIsOwner = ((int) ($comment['user_id'] ?? 0)) === (int) ($_SESSION['user_id'] ?? 0);
                                                    ?>
                                                    <div class="collab-comment">
                                                        <div style="flex:1 1 auto;min-width:0;">
                                                            <div class="collab-comment__author">
                                                                <?= htmlspecialchars($commentOwner, ENT_QUOTES, 'UTF-8') ?>
                                                                <span class="collab-comment__time">· <?= htmlspecialchars((string) ($comment['created_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
                                                            </div>
                                                            <div class="collab-comment__text"><?= htmlspecialchars($commentContent, ENT_QUOTES, 'UTF-8') ?></div>
                                                        </div>
                                                        <?php if ($commentIsOwner && $commentId > 0): ?>
                                                            <a class="collab-chip-btn collab-chip-btn--danger" href="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/groupes/<?= $groupId ?>/delete-comment/<?= $commentId ?>" style="height:32px;">Delete</a>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="collab-empty" style="margin-top:1rem;">
                            <div class="collab-empty-icon" aria-hidden="true">📝</div>
                            <h3>No posts yet</h3>
                            <p>Be the first to post in this group.</p>
                        </div>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
<?php require __DIR__ . '/../partials/collab_layout_end.php'; ?>

