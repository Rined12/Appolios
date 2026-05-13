
<?php
// Canonical nested view path (replaces flat file; identical markup to former flat sibling).
$foPrefix = (string) ($foPrefix ?? 'student');
$viewer_user_id = (int) ($viewer_user_id ?? 0);
$listQ = (string) ($listQ ?? '');
$listSort = (string) ($listSort ?? 'name_asc');
$listQueryActive = (bool) ($listQueryActive ?? false);
$groupSearchSuggestionItems = $group_search_suggestion_items ?? [];
?>
<?php require __DIR__ . '/../partials/collab_layout_start.php'; ?>
                <?php require __DIR__ . '/../partials/collab_hub_styles.php'; ?>

                <div class="header collab-hero">
                    <div class="collab-hero__inner">
                        <div>
                            <div class="collab-eyebrow"><i class="bi bi-briefcase-fill" aria-hidden="true"></i> Study circles</div>
                            <h1>Groups</h1>
                            <p>Discover approved communities, track your pending submissions, and manage spaces you lead.</p>
                        </div>
                        <div class="collab-hero-actions">
                            <a href="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/groupes/create" class="collab-btn-primary">
                                <i class="bi bi-plus-lg" aria-hidden="true"></i> Create group
                            </a>
                            <a href="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/discussions" class="collab-btn-ghost">
                                <i class="bi bi-chat-square-text" aria-hidden="true"></i> Discussions
                            </a>
                        </div>
                    </div>
                </div>

                <style>
                    .group-search-wrap .search-magnifier {
                        position: absolute;
                        left: 12px;
                        top: 50%;
                        transform: translateY(-50%);
                        color: #94a3b8;
                        font-size: 0.95rem;
                        pointer-events: none;
                        z-index: 2;
                    }
                    .group-search-wrap.collab-search-row input[type="text"] {
                        padding-left: 2.25rem !important;
                    }
                </style>

                <form class="collab-toolbar" method="get" action="<?= APP_ENTRY ?>" novalidate>
                    <input type="hidden" name="url" value="<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/groupes">
                    <div style="flex:1 1 320px; min-width:0;">
                        <label for="fo_group_search">Search groups</label>
                        <div class="collab-search-row group-search-wrap hub-omnibox-wrap">
                            <span class="search-magnifier"><i class="bi bi-search"></i></span>
                            <input id="fo_group_search" type="text" name="q" value="<?= htmlspecialchars($listQ) ?>" placeholder="Search groups — names, descriptions…" autocomplete="off">
                            <button type="submit" title="Search" aria-label="Search"><i class="bi bi-search" aria-hidden="true"></i></button>
                            <div id="fo_group_suggest" class="hub-omnibox-dropdown" role="listbox" aria-label="Group suggestions"></div>
                        </div>
                    </div>
                    <div style="flex:0 0 auto;">
                        <label for="fo_group_sort">Sort</label>
                        <select id="fo_group_sort" name="sort" aria-label="Sort groups" onchange="this.form.submit()">
                            <option value="name_asc"<?= $listSort === 'name_asc' ? ' selected' : '' ?>>Name (A–Z)</option>
                            <option value="name_desc"<?= $listSort === 'name_desc' ? ' selected' : '' ?>>Name (Z–A)</option>
                            <option value="newest"<?= $listSort === 'newest' ? ' selected' : '' ?>>Newest first</option>
                            <option value="oldest"<?= $listSort === 'oldest' ? ' selected' : '' ?>>Oldest first</option>
                        </select>
                    </div>
                </form>

                <?php if (!empty($mesGroupesEnApprobation)): ?>
                    <div class="collab-pending-block">
                        <h3 class="collab-section-label"><span class="dot" aria-hidden="true"></span> Awaiting approval</h3>
                        <div class="collab-group-grid">
                            <?php foreach ($mesGroupesEnApprobation as $g): ?>
                                <?php $cover = (string) ($g['cover_url'] ?? ''); ?>
                                <div class="article collab-group-card collab-group-card--pending">
                                    <div class="collab-group-card__media<?= $cover === '' ? ' collab-group-card__media--fallback' : '' ?>">
                                        <span class="collab-group-card__pending-tag">Awaiting approval</span>
                                        <?php if ($cover !== ''): ?>
                                            <img src="<?= htmlspecialchars($cover) ?>" alt="" loading="lazy" onerror="this.closest('.collab-group-card__media').classList.add('collab-group-card__media--fallback'); this.remove();">
                                        <?php else: ?>
                                            <div class="collab-group-card__ph" aria-hidden="true"><i class="bi bi-hourglass-split"></i></div>
                                        <?php endif; ?>
                                        <div class="collab-group-card__overlay"></div>
                                        <h4 class="collab-group-card__floating-title"><?= htmlspecialchars($g['nom_groupe']) ?></h4>
                                    </div>
                                    <div class="collab-group-card__body">
                                        <p class="collab-line-clamp-2"><?= htmlspecialchars((string) ($g['description'] ?? '')) ?></p>
                                        <div class="collab-card-actions">
                                            <a class="collab-chip-btn collab-chip-btn--muted" href="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/groupes/<?= (int) $g['id_groupe'] ?>"><i class="bi bi-eye" aria-hidden="true"></i> View</a>
                                            <a class="collab-chip-btn collab-chip-btn--muted" href="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/groupes/<?= (int) $g['id_groupe'] ?>/activity-pdf" title="Print or save group activity report"><i class="bi bi-printer" aria-hidden="true"></i> Activity report</a>
                                            <a class="collab-chip-btn collab-chip-btn--live" href="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/groupes/<?= (int) $g['id_groupe'] ?>/edit"><i class="bi bi-pencil-square" aria-hidden="true"></i> Edit</a>
                                            <a class="collab-chip-btn collab-chip-btn--danger" href="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/groupes/<?= (int) $g['id_groupe'] ?>/delete"><i class="bi bi-trash" aria-hidden="true"></i> Delete</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <h3 class="collab-section-label"><span class="dot" aria-hidden="true"></span> Approved groups</h3>
                <?php if (!empty($groupes)): ?>
                    <div class="collab-group-grid approved-group-grid">
                        <?php foreach ($groupes as $g): ?>
                            <?php
                                $cover = (string) ($g['cover_url'] ?? '');
                                $isOwner = (bool) ($g['is_owner_viewer'] ?? false);
                                $isMember = (bool) ($g['is_member_viewer'] ?? false);
                            ?>
                            <div class="article collab-group-card">
                                <div class="collab-group-card__media<?= $cover === '' ? ' collab-group-card__media--fallback' : '' ?>">
                                    <?php if ($cover !== ''): ?>
                                        <img src="<?= htmlspecialchars($cover) ?>" alt="" loading="lazy" onerror="this.closest('.collab-group-card__media').classList.add('collab-group-card__media--fallback'); this.remove();">
                                    <?php else: ?>
                                        <div class="collab-group-card__ph" aria-hidden="true"><i class="bi bi-people-fill"></i></div>
                                    <?php endif; ?>
                                    <div class="collab-group-card__overlay"></div>
                                    <h4 class="collab-group-card__floating-title"><?= htmlspecialchars($g['nom_groupe']) ?></h4>
                                </div>
                                <div class="collab-group-card__body">
                                    <p class="collab-line-clamp-2"><?= htmlspecialchars((string) ($g['description'] ?? '')) ?></p>
                                    <div class="collab-card-actions">
                                        <a class="collab-chip-btn collab-chip-btn--muted" href="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/groupes/<?= (int) $g['id_groupe'] ?>"><i class="bi bi-door-open" aria-hidden="true"></i> Open</a>
                                        <?php if ($isOwner): ?>
                                            <a class="collab-chip-btn collab-chip-btn--muted" href="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/groupes/<?= (int) $g['id_groupe'] ?>/activity-pdf" title="Print or save group activity report"><i class="bi bi-printer" aria-hidden="true"></i> Activity report</a>
                                            <a class="collab-chip-btn collab-chip-btn--muted" href="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/groupes/<?= (int) $g['id_groupe'] ?>/edit"><i class="bi bi-sliders" aria-hidden="true"></i> Manage</a>
                                            <a class="collab-chip-btn collab-chip-btn--danger" href="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/groupes/<?= (int) $g['id_groupe'] ?>/delete"><i class="bi bi-trash" aria-hidden="true"></i> Delete</a>
                                        <?php elseif ($isMember): ?>
                                            <a class="collab-chip-btn collab-chip-btn--danger js-quit-group-link" href="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/groupes/<?= (int) $g['id_groupe'] ?>/quit"><i class="bi bi-box-arrow-right" aria-hidden="true"></i> Quit</a>
                                        <?php else: ?>
                                            <a class="collab-chip-btn collab-chip-btn--live" href="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/groupes/<?= (int) $g['id_groupe'] ?>/join"><i class="bi bi-person-plus" aria-hidden="true"></i> Join</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="collab-empty">
                        <div class="collab-empty-icon--glyph" aria-hidden="true"><i class="bi bi-people"></i></div>
                        <?php if ($listQueryActive): ?>
                            <h3>No groups match</h3>
                            <p>Adjust your search terms or clear the filter to see the full catalogue.</p>
                        <?php else: ?>
                            <h3>No groups yet</h3>
                            <p>Be the first to propose a circle — admins approve new communities regularly.</p>
                            <div style="margin-top:1.25rem;">
                                <a href="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/groupes/create" class="collab-btn-primary">Create a group</a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
<?php require __DIR__ . '/../partials/collab_layout_end.php'; ?>
<script>
(function () {
    var input = document.getElementById('fo_group_search');
    var suggestBox = document.getElementById('fo_group_suggest');
    if (!input || !suggestBox) { return; }

    var items = <?= json_encode($groupSearchSuggestionItems, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?> || [];
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
            empty.textContent = 'No matching groups';
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
                iconWrap.className = 'hub-omnibox-icon hub-omnibox-icon--group';
                iconWrap.innerHTML = '<i class="bi bi-people-fill" aria-hidden="true"></i>';

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
                badge.textContent = 'Group';

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
        if (String(input.value || '').trim() === '') {
            closeSuggest();
            return;
        }
        renderList(filterItems(input.value));
        activeIndex = -1;
    }

    input.addEventListener('input', refresh);
    input.addEventListener('focus', refresh);

    input.addEventListener('blur', function () {
        window.setTimeout(function () {
            if (document.activeElement !== input && !suggestBox.contains(document.activeElement)) {
                closeSuggest();
            }
        }, 180);
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

