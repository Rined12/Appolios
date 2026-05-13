/**
 * Marks sibling “module” groups and blurs non-hovered items when one child is hovered.
 */
(function () {
    'use strict';

    var EXCLUDE =
        'header, footer, .modal, [role="dialog"], .dropdown-menu, .no-module-focus, .admin-sidebar-pro, .student-space-sidebar, nav';

    var GRID_SELECTORS = [
        '.neo-stats-grid',
        '.neo-course-grid',
        '.public-courses-grid',
        '.pro-stats-grid',
        '.pro-kpi-grid',
        '.pro-kpi-grid--blocks',
        '.pro-chart-grid',
        '.collab-group-grid',
        '.collab-disc-grid',
        '.student-events-grid',
        '.sl-group-grid',
        '.sl-disc-grid',
        '.stat-grid-2',
        '.stats-grid-pro',
        '.student-profile-grid',
        '.chat-theme-grid',
        '.approved-group-grid',
        '.q-block-grid',
    ].join(',');

    var MODULE_CHILD_SEL =
        '.stat-card, .neo-stat-card, .neo-course-card, .pro-stat-card, .stat-card-pro, .course-card, .public-course-card, .student-course-card';

    function inExcluded(el) {
        return !!(el.closest && el.closest(EXCLUDE));
    }

    function visibleChildCount(el) {
        var n = 0;
        for (var i = 0; i < el.children.length; i++) {
            var c = el.children[i];
            if (c.nodeType === 1 && !c.matches('script,style,noscript')) {
                n++;
            }
        }
        return n;
    }

    function canCluster(el) {
        if (!el || el.nodeType !== 1) {
            return false;
        }
        if (inExcluded(el)) {
            return false;
        }
        var n = visibleChildCount(el);
        return n >= 2 && n <= 28;
    }

    function tagGrids() {
        var parts = GRID_SELECTORS.split(',');
        for (var p = 0; p < parts.length; p++) {
            var sel = parts[p].trim();
            if (!sel) {
                continue;
            }
            var nodes;
            try {
                nodes = document.querySelectorAll(sel);
            } catch (e) {
                continue;
            }
            for (var i = 0; i < nodes.length; i++) {
                var el = nodes[i];
                if (el.classList.contains('appolios-module-cluster')) {
                    continue;
                }
                if (canCluster(el)) {
                    el.classList.add('appolios-module-cluster');
                }
            }
        }
    }

    function tagBootstrapRows() {
        var rows = document.querySelectorAll('.row');
        for (var r = 0; r < rows.length; r++) {
            var row = rows[r];
            if (row.classList.contains('appolios-module-cluster')) {
                continue;
            }
            if (!canCluster(row) || inExcluded(row)) {
                continue;
            }
            var cols = row.querySelectorAll(':scope > [class*="col"]');
            if (cols.length < 2) {
                continue;
            }
            var hasCard = false;
            for (var i = 0; i < cols.length; i++) {
                if (cols[i].querySelector('.card, .neo-stat-card, .neo-course-card, .stat-card, .pro-stat-card, .stat-card-pro, .public-course-card, .student-course-card')) {
                    hasCard = true;
                    break;
                }
            }
            if (hasCard) {
                row.classList.add('appolios-module-cluster');
            }
        }
    }

    function tagCardParents() {
        var seen = new WeakSet();
        var cards = document.querySelectorAll(MODULE_CHILD_SEL);
        for (var i = 0; i < cards.length; i++) {
            var card = cards[i];
            var parent = card.parentElement;
            if (!parent || seen.has(parent)) {
                continue;
            }
            if (!canCluster(parent) || inExcluded(parent)) {
                continue;
            }
            var count = 0;
            for (var j = 0; j < parent.children.length; j++) {
                var ch = parent.children[j];
                if (ch.nodeType === 1 && ch.matches(MODULE_CHILD_SEL)) {
                    count++;
                }
            }
            if (count >= 2) {
                seen.add(parent);
                parent.classList.add('appolios-module-cluster');
            }
        }
    }

    function wireCluster(cluster) {
        if (cluster.dataset.appoliosModuleFocusWired === '1') {
            return;
        }
        cluster.dataset.appoliosModuleFocusWired = '1';

        var items = [];
        for (var i = 0; i < cluster.children.length; i++) {
            var c = cluster.children[i];
            if (c.nodeType === 1 && !c.matches('script,style,noscript')) {
                items.push(c);
            }
        }
        if (items.length < 2) {
            return;
        }

        cluster.addEventListener('mouseleave', function (e) {
            if (!e.relatedTarget || !cluster.contains(e.relatedTarget)) {
                cluster.classList.remove('appolios-module-cluster--active');
                for (var j = 0; j < items.length; j++) {
                    items[j].classList.remove('appolios-module-cluster__focus');
                }
            }
        });

        for (var k = 0; k < items.length; k++) {
            (function (item) {
                item.addEventListener('mouseenter', function () {
                    cluster.classList.add('appolios-module-cluster--active');
                    for (var j = 0; j < items.length; j++) {
                        items[j].classList.toggle('appolios-module-cluster__focus', items[j] === item);
                    }
                });
            })(items[k]);
        }
    }

    function wireAll() {
        var clusters = document.querySelectorAll('.appolios-module-cluster');
        for (var i = 0; i < clusters.length; i++) {
            wireCluster(clusters[i]);
        }
    }

    function init() {
        tagGrids();
        tagBootstrapRows();
        tagCardParents();
        wireAll();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
