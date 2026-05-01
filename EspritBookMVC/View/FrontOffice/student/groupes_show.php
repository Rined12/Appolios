<?php
$studentSidebarActive = 'groupes';
$foPrefix = $foPrefix ?? 'student';
$discussion_cards = $discussion_cards ?? [];
$discussionErrors = $discussionErrors ?? [];
$viewerId = (int) ($_SESSION['user_id'] ?? 0);
$ownerId = (int) ($groupe['id_createur'] ?? $groupe['created_by'] ?? 0);
$isOwner = $viewerId === $ownerId;
$cover = trim((string) ($groupe['image_url'] ?? $groupe['photo'] ?? $groupe['image'] ?? ''));
?>
<div class="dashboard student-events-page collab-hub collab-hub--detail">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main">
                <?php require __DIR__ . '/partials/collab_hub_styles.php'; ?>

                <div class="collab-detail-hero">
                    <div class="collab-detail-banner">
                        <?php if ($cover !== ''): ?>
                            <img src="<?= htmlspecialchars($cover) ?>" alt="<?= htmlspecialchars($groupe['nom_groupe'] ?? 'Group') ?>" loading="lazy" onerror="this.style.display='none';">
                        <?php endif; ?>
                        <div class="collab-detail-banner__inner">
                            <div class="collab-eyebrow" style="opacity:.95;"><i class="bi bi-diagram-3-fill" aria-hidden="true"></i> Group workspace</div>
                            <h1><?= htmlspecialchars($groupe['nom_groupe'] ?? 'Group') ?></h1>
                            <p><?= htmlspecialchars((string) ($groupe['description'] ?? '')) ?></p>
                            <?php if ($isOwner): ?>
                                <div style="margin-top:1rem;">
                                    <a class="collab-btn-primary" href="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/groupes/<?= (int) $groupe['id_groupe'] ?>/edit">
                                        <i class="bi bi-pencil-square" aria-hidden="true"></i> Edit group
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <aside class="collab-detail-sidecard">
                        <h3>Members</h3>
                        <?php if (!empty($membres)): ?>
                            <div class="collab-member-row">
                                <?php foreach ($membres as $m): ?>
                                    <?php
                                        $name = trim((string) ($m['name'] ?? ''));
                                        $ini = $name !== '' ? strtoupper(substr($name, 0, 1)) : '?';
                                    ?>
                                    <div class="collab-member-chip">
                                        <span class="collab-member-avatar"><?= htmlspecialchars($ini) ?></span>
                                        <span><?= htmlspecialchars($name ?: 'Member') ?></span>
                                        <span class="collab-role-tag"><?= htmlspecialchars((string) ($m['role'] ?? '')) ?></span>
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
                    </aside>
                </div>

                <section class="collab-thread-panel">
                    <h3><i class="bi bi-chat-text-fill" style="color:var(--ch-teal-soft);margin-right:.35rem;" aria-hidden="true"></i> Discussions</h3>

                    <?php if ($isOwner): ?>
                        <div class="collab-compose">
                            <div style="font-size:0.78rem;font-weight:800;letter-spacing:0.06em;text-transform:uppercase;color:var(--ch-muted);margin-bottom:0.65rem;">Spin up a new thread</div>
                            <?php if (!empty($discussionErrors)): ?>
                                <div class="collab-alert-soft" style="border-color:rgba(239,68,68,0.35);background:linear-gradient(135deg,#fef2f2,#fff);color:#991b1b;">
                                    <?php foreach ($discussionErrors as $err): ?>
                                        <div><?= htmlspecialchars((string) $err) ?></div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            <form method="POST" action="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/groupes/<?= (int) $groupe['id_groupe'] ?>/discussions/store">
                                <div class="form-group">
                                    <label for="gd_title">Title</label>
                                    <input id="gd_title" type="text" name="titre" placeholder="What should we talk about?" value="<?= htmlspecialchars((string) ($discussionOld['titre'] ?? '')) ?>">
                                </div>
                                <div class="form-group">
                                    <label for="gd_body">First message</label>
                                    <textarea id="gd_body" name="contenu" placeholder="Context, goals, links…"><?= htmlspecialchars((string) ($discussionOld['contenu'] ?? '')) ?></textarea>
                                </div>
                                <button class="collab-btn-primary" type="submit" style="box-shadow:none;"><i class="bi bi-send-fill" aria-hidden="true"></i> Publish discussion</button>
                            </form>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($discussion_cards)): ?>
                        <?php foreach ($discussion_cards as $c): ?>
                            <article class="collab-thread-card">
                                <h4 class="collab-thread-card__title"><?= htmlspecialchars((string) ($c['title'] ?? 'Discussion'), ENT_QUOTES, 'UTF-8') ?></h4>
                                <div class="collab-thread-meta">
                                    <i class="bi bi-person-circle" aria-hidden="true"></i>
                                    <?= htmlspecialchars((string) ($c['author_name'] ?? 'Unknown'), ENT_QUOTES, 'UTF-8') ?>
                                </div>
                                <p><?= htmlspecialchars((string) ($c['excerpt'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                                <div class="collab-card-actions">
                                    <?php if (!empty($c['can_chat'])): ?>
                                        <a class="collab-chip-btn collab-chip-btn--live" href="<?= htmlspecialchars((string) ($c['url_chat'] ?? '#'), ENT_QUOTES, 'UTF-8') ?>">
                                            <i class="bi bi-lightning-charge-fill" aria-hidden="true"></i> Open live chat
                                        </a>
                                    <?php endif; ?>
                                    <?php if (!empty($c['can_delete'])): ?>
                                        <a class="collab-chip-btn collab-chip-btn--danger" href="<?= htmlspecialchars((string) ($c['url_delete'] ?? '#'), ENT_QUOTES, 'UTF-8') ?>" onclick="return confirm('Supprimer cette discussion ?');">
                                            <i class="bi bi-trash" aria-hidden="true"></i> Delete
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="collab-empty" style="padding:2rem 1rem;">
                            <div class="collab-empty-icon" aria-hidden="true">🧵</div>
                            <h3>No threads yet</h3>
                            <p style="max-width:420px;margin-left:auto;margin-right:auto;"><?= $isOwner ? 'Use the composer above to publish the first discussion for this group.' : 'The group owner has not started a conversation — check back soon.' ?></p>
                        </div>
                    <?php endif; ?>
                </section>
            </div>
        </div>
    </div>
</div>
