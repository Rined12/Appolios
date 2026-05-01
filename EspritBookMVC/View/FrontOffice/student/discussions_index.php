<?php
$studentSidebarActive = 'discussions';
$foPrefix = $foPrefix ?? 'student';
$discussion_cards = $discussion_cards ?? [];
$listQ = (string) ($listQ ?? '');
$listSort = (string) ($listSort ?? 'newest');
$listQueryActive = (bool) ($listQueryActive ?? false);
?>
<div class="dashboard student-events-page collab-hub">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main">
                <?php require __DIR__ . '/partials/collab_hub_styles.php'; ?>

                <header class="collab-hero">
                    <div class="collab-hero__inner">
                        <div>
                            <div class="collab-eyebrow"><i class="bi bi-chat-dots-fill" aria-hidden="true"></i> Collaboration hub</div>
                            <h1>Discussions</h1>
                            <p>Threads from every group you belong to — search, sort, and jump into live chat in one place.</p>
                        </div>
                        <div class="collab-hero-actions">
                            <a href="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/discussions/create" class="collab-btn-primary">
                                <i class="bi bi-plus-lg" aria-hidden="true"></i> New discussion
                            </a>
                            <a href="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/groupes" class="collab-btn-ghost">
                                <i class="bi bi-people" aria-hidden="true"></i> Browse groups
                            </a>
                        </div>
                    </div>
                </header>

                <form class="collab-toolbar" method="get" action="<?= APP_ENTRY ?>" novalidate>
                    <input type="hidden" name="url" value="<?= htmlspecialchars($foPrefix . '/discussions', ENT_QUOTES, 'UTF-8') ?>">
                    <div style="flex:1 1 320px; min-width:0;">
                        <label for="fo_disc_search">Find a thread</label>
                        <div class="collab-search-row">
                            <input id="fo_disc_search" type="text" name="q" value="<?= htmlspecialchars($listQ) ?>" placeholder="Title, message text, group name…" autocomplete="off">
                            <button type="submit" title="Search" aria-label="Search"><i class="bi bi-search" aria-hidden="true"></i></button>
                        </div>
                    </div>
                    <div style="flex:0 0 auto;">
                        <label for="fo_disc_sort">Order</label>
                        <select id="fo_disc_sort" name="sort" aria-label="Sort discussions" onchange="this.form.submit()">
                            <option value="newest"<?= $listSort === 'newest' ? ' selected' : '' ?>>Newest first</option>
                            <option value="oldest"<?= $listSort === 'oldest' ? ' selected' : '' ?>>Oldest first</option>
                            <option value="title_asc"<?= $listSort === 'title_asc' ? ' selected' : '' ?>>Title (A–Z)</option>
                            <option value="title_desc"<?= $listSort === 'title_desc' ? ' selected' : '' ?>>Title (Z–A)</option>
                            <option value="group_asc"<?= $listSort === 'group_asc' ? ' selected' : '' ?>>Group (A–Z)</option>
                            <option value="group_desc"<?= $listSort === 'group_desc' ? ' selected' : '' ?>>Group (Z–A)</option>
                        </select>
                    </div>
                </form>

                <?php if (!empty($discussion_cards)): ?>
                    <div class="collab-disc-grid">
                        <?php foreach ($discussion_cards as $card): ?>
                            <article class="collab-disc-card">
                                <span class="collab-pill collab-pill--group"><i class="bi bi-people-fill" aria-hidden="true"></i> <?= htmlspecialchars((string) ($card['group_name'] ?? 'N/A'), ENT_QUOTES, 'UTF-8') ?></span>
                                <h4 class="collab-disc-card__title"><?= htmlspecialchars((string) ($card['title'] ?? 'Discussion'), ENT_QUOTES, 'UTF-8') ?></h4>
                                <p class="collab-disc-card__excerpt collab-line-clamp-3"><?= htmlspecialchars((string) ($card['content'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                                <div class="collab-card-actions">
                                    <a class="collab-chip-btn collab-chip-btn--live" href="<?= htmlspecialchars((string) ($card['url_chat'] ?? '#'), ENT_QUOTES, 'UTF-8') ?>">
                                        <i class="bi bi-lightning-charge-fill" aria-hidden="true"></i> Live chat
                                    </a>
                                    <?php if (!empty($card['is_author'])): ?>
                                        <a class="collab-chip-btn collab-chip-btn--muted" href="<?= htmlspecialchars((string) ($card['url_edit'] ?? '#'), ENT_QUOTES, 'UTF-8') ?>">
                                            <i class="bi bi-pencil" aria-hidden="true"></i> Edit
                                        </a>
                                        <a class="collab-chip-btn collab-chip-btn--danger" href="<?= htmlspecialchars((string) ($card['url_delete'] ?? '#'), ENT_QUOTES, 'UTF-8') ?>">
                                            <i class="bi bi-trash" aria-hidden="true"></i> Delete
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="collab-empty">
                        <div class="collab-empty-icon" aria-hidden="true">💬</div>
                        <?php if ($listQueryActive): ?>
                            <h3>No matches</h3>
                            <p>Try another keyword or reset the sort order — your threads might use different wording.</p>
                        <?php else: ?>
                            <h3>Quiet for now</h3>
                            <p>Create a discussion inside one of your approved groups, or join a group first to see activity here.</p>
                            <div style="margin-top:1.25rem;">
                                <a href="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/discussions/create" class="collab-btn-primary">Start a discussion</a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
