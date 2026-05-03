<?php
// Canonical nested view path (replaces flat file; identical markup to former flat sibling).
$studentSidebarActive = 'discussions';
$foPrefix = $foPrefix ?? 'student';
$discussion_cards = $discussion_cards ?? [];
$listQ = (string) ($listQ ?? '');
$listSort = (string) ($listSort ?? 'newest');
$listQueryActive = (bool) ($listQueryActive ?? false);
$searchSuggestions = $search_suggestions ?? [];
?>
<div class="dashboard student-events-page collab-hub">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/../partials/sidebar.php'; ?>
            <div class="admin-main">
                <?php require __DIR__ . '/../partials/collab_hub_styles.php'; ?>

                <div class="header collab-hero">
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
                </div>

                <style>
                    .discussions-toolbar-boost {
                        border: 1px solid rgba(148, 163, 184, 0.25);
                        box-shadow: 0 12px 36px rgba(15, 23, 42, 0.07);
                    }
                    .discussion-search-wrap {
                        position: relative;
                    }
                    .discussion-search-wrap .search-magnifier {
                        position: absolute;
                        left: 12px;
                        top: 50%;
                        transform: translateY(-50%);
                        color: #94a3b8;
                        font-size: 0.95rem;
                        pointer-events: none;
                    }
                    .discussion-search-wrap input {
                        padding-left: 2.25rem !important;
                    }
                    .discussion-suggest-box {
                        position: absolute;
                        left: 0;
                        right: 5.25rem;
                        top: calc(100% + 8px);
                        z-index: 30;
                        border: 1px solid rgba(226, 232, 240, 0.95);
                        border-radius: 12px;
                        background: #ffffff;
                        box-shadow: 0 14px 32px rgba(15, 23, 42, 0.14);
                        overflow: hidden;
                        display: none;
                        max-height: 230px;
                        overflow-y: auto;
                    }
                    .discussion-suggest-item {
                        display: block;
                        width: 100%;
                        border: 0;
                        background: transparent;
                        text-align: left;
                        padding: 0.62rem 0.85rem;
                        font-size: 0.88rem;
                        font-weight: 600;
                        color: #1e293b;
                        cursor: pointer;
                    }
                    .discussion-suggest-item + .discussion-suggest-item {
                        border-top: 1px solid #f1f5f9;
                    }
                    .discussion-suggest-item:hover,
                    .discussion-suggest-item.active {
                        background: #eff6ff;
                        color: #1d4ed8;
                    }
                    .discussion-suggest-empty {
                        padding: 0.6rem 0.85rem;
                        font-size: 0.82rem;
                        color: #94a3b8;
                    }
                </style>
                <form class="collab-toolbar discussions-toolbar-boost" method="get" action="<?= APP_ENTRY ?>" novalidate>
                    <input type="hidden" name="url" value="<?= htmlspecialchars($foPrefix . '/discussions', ENT_QUOTES, 'UTF-8') ?>">
                    <div style="flex:1 1 320px; min-width:0;">
                        <label for="fo_disc_search">Find a thread</label>
                        <div class="collab-search-row discussion-search-wrap">
                            <span class="search-magnifier"><i class="bi bi-search"></i></span>
                            <input id="fo_disc_search" type="text" name="q" value="<?= htmlspecialchars($listQ) ?>" placeholder="Title, message text, group name…" autocomplete="off">
                            <button type="submit" title="Search" aria-label="Search"><i class="bi bi-search" aria-hidden="true"></i></button>
                            <div id="fo_disc_suggest" class="discussion-suggest-box"></div>
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
                            <div class="article collab-disc-card">
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
                            </div>
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
<script>
(function () {
    var input = document.getElementById('fo_disc_search');
    var suggestBox = document.getElementById('fo_disc_suggest');
    if (!input || !suggestBox) { return; }

    var suggestions = <?= json_encode($searchSuggestions) ?> || [];
    var activeIndex = -1;
    var visibleItems = [];

    function closeSuggest() {
        suggestBox.style.display = 'none';
        suggestBox.innerHTML = '';
        activeIndex = -1;
        visibleItems = [];
    }

    function openSuggest() {
        if (visibleItems.length === 0) {
            closeSuggest();
            return;
        }
        suggestBox.style.display = 'block';
    }

    function renderList(filtered) {
        suggestBox.innerHTML = '';
        visibleItems = filtered.slice(0, 7);
        if (visibleItems.length === 0) {
            var empty = document.createElement('div');
            empty.className = 'discussion-suggest-empty';
            empty.textContent = 'No suggestions';
            suggestBox.appendChild(empty);
            suggestBox.style.display = input.value.trim() !== '' ? 'block' : 'none';
            return;
        }

        for (var i = 0; i < visibleItems.length; i++) {
            (function (idx) {
                var item = document.createElement('button');
                item.type = 'button';
                item.className = 'discussion-suggest-item';
                item.textContent = visibleItems[idx];
                item.addEventListener('mouseenter', function () { setActive(idx); });
                item.addEventListener('mousedown', function (e) {
                    e.preventDefault();
                    input.value = visibleItems[idx];
                    input.form.submit();
                });
                suggestBox.appendChild(item);
            })(i);
        }
        openSuggest();
    }

    function setActive(idx) {
        var nodes = suggestBox.querySelectorAll('.discussion-suggest-item');
        for (var i = 0; i < nodes.length; i++) {
            nodes[i].classList.remove('active');
        }
        activeIndex = idx;
        if (nodes[activeIndex]) {
            nodes[activeIndex].classList.add('active');
        }
    }

    input.addEventListener('input', function () {
        var q = input.value.toLowerCase().trim();
        if (q === '') {
            closeSuggest();
            return;
        }
        var filtered = [];
        for (var i = 0; i < suggestions.length; i++) {
            var s = String(suggestions[i] || '');
            if (s.toLowerCase().indexOf(q) !== -1) {
                filtered.push(s);
            }
        }
        renderList(filtered);
        activeIndex = -1;
    });

    input.addEventListener('focus', function () {
        if (input.value.trim() === '') { return; }
        input.dispatchEvent(new Event('input'));
    });

    input.addEventListener('keydown', function (e) {
        if (visibleItems.length === 0 || suggestBox.style.display !== 'block') { return; }
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            setActive(activeIndex < visibleItems.length - 1 ? activeIndex + 1 : 0);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            setActive(activeIndex > 0 ? activeIndex - 1 : visibleItems.length - 1);
        } else if (e.key === 'Enter') {
            if (activeIndex >= 0 && visibleItems[activeIndex]) {
                e.preventDefault();
                input.value = visibleItems[activeIndex];
                input.form.submit();
            }
        } else if (e.key === 'Escape') {
            closeSuggest();
        }
    });

    document.addEventListener('click', function (e) {
        if (!suggestBox.contains(e.target) && e.target !== input) {
            closeSuggest();
        }
    });
})();
</script>
