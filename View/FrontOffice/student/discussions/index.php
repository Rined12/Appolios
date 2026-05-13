<?php
$foPrefix = $foPrefix ?? 'student';
$discussion_cards = $discussion_cards ?? [];
$listQ = (string) ($listQ ?? '');
$listSort = (string) ($listSort ?? 'newest');
$listQueryActive = (bool) ($listQueryActive ?? false);
$searchSuggestionItems = $search_suggestion_items ?? [];
?>
<?php require __DIR__ . '/../partials/collab_layout_start.php'; ?>
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
                    .discussion-search-wrap .search-magnifier {
                        position: absolute;
                        left: 12px;
                        top: 50%;
                        transform: translateY(-50%);
                        color: #94a3b8;
                        font-size: 0.95rem;
                        pointer-events: none;
                        z-index: 2;
                    }
                    .discussion-search-wrap input {
                        padding-left: 2.25rem !important;
                    }
                </style>
                <form class="collab-toolbar" method="get" action="<?= APP_ENTRY ?>" novalidate>
                    <input type="hidden" name="url" value="<?= htmlspecialchars($foPrefix . '/discussions', ENT_QUOTES, 'UTF-8') ?>">
                    <div style="flex:1 1 320px; min-width:0;">
                        <label for="fo_disc_search">Find a thread</label>
                        <div class="collab-search-row discussion-search-wrap hub-omnibox-wrap">
                            <span class="search-magnifier"><i class="bi bi-search"></i></span>
                            <input id="fo_disc_search" type="text" name="q" value="<?= htmlspecialchars($listQ) ?>" placeholder="Search discussions — titles, groups, messages…" autocomplete="off">
                            <button type="submit" title="Search" aria-label="Search"><i class="bi bi-search" aria-hidden="true"></i></button>
                            <div id="fo_disc_suggest" class="hub-omnibox-dropdown" role="listbox" aria-label="Discussion suggestions"></div>
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
                        <div class="collab-empty-icon--glyph collab-empty-icon--discussions" aria-hidden="true"><i class="bi bi-chat-dots-fill"></i></div>
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
<?php require __DIR__ . '/../partials/collab_layout_end.php'; ?>
<script>
(function () {
    var input = document.getElementById('fo_disc_search');
    var suggestBox = document.getElementById('fo_disc_suggest');
    if (!input || !suggestBox) { return; }

    var items = <?= json_encode($searchSuggestionItems, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?> || [];
    var activeIndex = -1;
    var visibleItems = [];

    function closeSuggest() {
        suggestBox.style.display = 'none';
        suggestBox.innerHTML = '';
        activeIndex = -1;
        visibleItems = [];
    }

    function filterItems(q) {
        var ql = q.toLowerCase().trim();
        if (ql === '') {
            return [];
        }
        var out = [];
        for (var i = 0; i < items.length; i++) {
            var it = items[i];
            var primary = String((it && it.primary) || '').toLowerCase().trim();
            if (primary.indexOf(ql) === 0) {
                out.push(it);
            }
        }
        return out;
    }

    function go(item) {
        if (!item || !item.url) { return; }
        window.location.href = String(item.url);
    }

    function renderList(filtered) {
        suggestBox.innerHTML = '';
        visibleItems = filtered.slice(0, 10);
        if (visibleItems.length === 0) {
            var empty = document.createElement('div');
            empty.className = 'hub-omnibox-empty';
            empty.textContent = 'No matching discussions';
            suggestBox.appendChild(empty);
            suggestBox.style.display = 'block';
            return;
        }

        for (var i = 0; i < visibleItems.length; i++) {
            (function (idx) {
                var row = visibleItems[idx];
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'hub-omnibox-item';
                btn.setAttribute('role', 'option');

                var iconWrap = document.createElement('span');
                iconWrap.className = 'hub-omnibox-icon';
                iconWrap.innerHTML = '<i class="bi bi-chat-dots-fill" aria-hidden="true"></i>';

                var body = document.createElement('span');
                body.className = 'hub-omnibox-body';
                var p = document.createElement('span');
                p.className = 'hub-omnibox-primary';
                p.textContent = String(row.primary || '');
                var s = document.createElement('span');
                s.className = 'hub-omnibox-secondary';
                s.textContent = String(row.secondary || '');
                body.appendChild(p);
                body.appendChild(s);

                var badge = document.createElement('span');
                badge.className = 'hub-omnibox-badge';
                badge.textContent = 'Live chat';

                btn.appendChild(iconWrap);
                btn.appendChild(body);
                btn.appendChild(badge);

                btn.addEventListener('mouseenter', function () { setActive(idx); });
                btn.addEventListener('mousedown', function (e) {
                    e.preventDefault();
                    go(row);
                });
                suggestBox.appendChild(btn);
            })(i);
        }
        suggestBox.style.display = 'block';
    }

    function setActive(idx) {
        var nodes = suggestBox.querySelectorAll('.hub-omnibox-item');
        for (var i = 0; i < nodes.length; i++) {
            nodes[i].classList.remove('active');
        }
        activeIndex = idx;
        if (nodes[activeIndex]) {
            nodes[activeIndex].classList.add('active');
        }
    }

    function refresh() {
        var q = input.value;
        if (String(q || '').trim() === '') {
            closeSuggest();
            return;
        }
        renderList(filterItems(q));
        activeIndex = -1;
    }

    input.addEventListener('input', function () {
        refresh();
    });

    input.addEventListener('focus', function () {
        refresh();
    });

    input.addEventListener('blur', function () {
        window.setTimeout(function () {
            if (document.activeElement !== input && !suggestBox.contains(document.activeElement)) {
                closeSuggest();
            }
        }, 180);
    });

    input.addEventListener('keydown', function (e) {
        var itemNodes = suggestBox.querySelectorAll('.hub-omnibox-item');
        if (itemNodes.length === 0 || suggestBox.style.display !== 'block') { return; }
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            setActive(activeIndex < visibleItems.length - 1 ? activeIndex + 1 : 0);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            setActive(activeIndex > 0 ? activeIndex - 1 : visibleItems.length - 1);
        } else if (e.key === 'Enter') {
            if (activeIndex >= 0 && visibleItems[activeIndex]) {
                e.preventDefault();
                go(visibleItems[activeIndex]);
            }
        } else if (e.key === 'Escape') {
            closeSuggest();
        }
    });

    document.addEventListener('click', function (e) {
        var wrap = input.closest('.hub-omnibox-wrap');
        if (wrap && !wrap.contains(e.target)) {
            closeSuggest();
        }
    });
})();
</script>

