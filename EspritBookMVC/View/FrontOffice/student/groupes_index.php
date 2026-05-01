<?php
$studentSidebarActive = 'groupes';
$viewerId = (int) ($_SESSION['user_id'] ?? 0);
$listQ = (string) ($listQ ?? '');
$listSort = (string) ($listSort ?? 'name_asc');
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
                            <div class="collab-eyebrow"><i class="bi bi-collection-fill" aria-hidden="true"></i> Study circles</div>
                            <h1>Groups</h1>
                            <p>Discover approved communities, track your pending submissions, and manage spaces you lead.</p>
                        </div>
                        <div class="collab-hero-actions">
                            <a href="<?= APP_ENTRY ?>?url=student/groupes/create" class="collab-btn-primary">
                                <i class="bi bi-plus-lg" aria-hidden="true"></i> Create group
                            </a>
                            <a href="<?= APP_ENTRY ?>?url=student/discussions" class="collab-btn-ghost">
                                <i class="bi bi-chat-square-text" aria-hidden="true"></i> Discussions
                            </a>
                        </div>
                    </div>
                </header>

                <form class="collab-toolbar" method="get" action="<?= APP_ENTRY ?>" novalidate>
                    <input type="hidden" name="url" value="student/groupes">
                    <div style="flex:1 1 320px; min-width:0;">
                        <label for="fo_group_search">Search groups</label>
                        <div class="collab-search-row">
                            <input id="fo_group_search" type="text" name="q" value="<?= htmlspecialchars($listQ) ?>" placeholder="Name or description…" autocomplete="off">
                            <button type="submit" title="Search" aria-label="Search"><i class="bi bi-search" aria-hidden="true"></i></button>
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
                                <?php $cover = trim((string) ($g['image_url'] ?? $g['photo'] ?? $g['image'] ?? '')); ?>
                                <article class="collab-group-card collab-group-card--pending">
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
                                            <a class="collab-chip-btn collab-chip-btn--muted" href="<?= APP_ENTRY ?>?url=student/groupes/<?= (int) $g['id_groupe'] ?>"><i class="bi bi-eye" aria-hidden="true"></i> View</a>
                                            <a class="collab-chip-btn collab-chip-btn--live" href="<?= APP_ENTRY ?>?url=student/groupes/<?= (int) $g['id_groupe'] ?>/edit"><i class="bi bi-pencil-square" aria-hidden="true"></i> Edit</a>
                                        </div>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <h3 class="collab-section-label"><span class="dot" aria-hidden="true"></span> Approved groups</h3>
                <?php if (!empty($groupes)): ?>
                    <div class="collab-group-grid">
                        <?php foreach ($groupes as $g): ?>
                            <?php
                                $cover = trim((string) ($g['image_url'] ?? $g['photo'] ?? $g['image'] ?? ''));
                                $ownerId = (int) ($g['id_createur'] ?? $g['created_by'] ?? 0);
                                $isOwner = $ownerId === $viewerId;
                                $isMember = (bool) ($g['is_member_viewer'] ?? false);
                            ?>
                            <article class="collab-group-card">
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
                                        <a class="collab-chip-btn collab-chip-btn--muted" href="<?= APP_ENTRY ?>?url=student/groupes/<?= (int) $g['id_groupe'] ?>"><i class="bi bi-door-open" aria-hidden="true"></i> Open</a>
                                        <?php if ($isOwner): ?>
                                            <a class="collab-chip-btn collab-chip-btn--muted" href="<?= APP_ENTRY ?>?url=student/groupes/<?= (int) $g['id_groupe'] ?>/edit"><i class="bi bi-sliders" aria-hidden="true"></i> Manage</a>
                                        <?php elseif ($isMember): ?>
                                            <a class="collab-chip-btn collab-chip-btn--danger js-quit-group-link" href="<?= APP_ENTRY ?>?url=student/groupes/<?= (int) $g['id_groupe'] ?>/quit"><i class="bi bi-box-arrow-right" aria-hidden="true"></i> Quit</a>
                                        <?php else: ?>
                                            <a class="collab-chip-btn collab-chip-btn--live" href="<?= APP_ENTRY ?>?url=student/groupes/<?= (int) $g['id_groupe'] ?>/join"><i class="bi bi-person-plus" aria-hidden="true"></i> Join</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="collab-empty">
                        <div class="collab-empty-icon" aria-hidden="true">👥</div>
                        <?php if ($listQueryActive): ?>
                            <h3>No groups match</h3>
                            <p>Adjust your search terms or clear the filter to see the full catalogue.</p>
                        <?php else: ?>
                            <h3>No groups yet</h3>
                            <p>Be the first to propose a circle — admins approve new communities regularly.</p>
                            <div style="margin-top:1.25rem;">
                                <a href="<?= APP_ENTRY ?>?url=student/groupes/create" class="collab-btn-primary">Create a group</a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
.swal2-popup.appolios-quit-popup {
    border-radius: 22px !important;
    padding: 1.65rem 1.5rem 1.35rem !important;
    border: 1px solid rgba(84, 140, 168, 0.28) !important;
    box-shadow: 0 26px 60px rgba(15, 23, 42, 0.34) !important;
}
.swal2-title.appolios-quit-title {
    font-size: 2rem !important;
    font-weight: 900 !important;
    letter-spacing: -0.02em !important;
    color: #1e293b !important;
    margin-top: 0.2rem !important;
}
.swal2-html-container.appolios-quit-text {
    font-size: 1.05rem !important;
    line-height: 1.6 !important;
    color: #64748b !important;
    margin: 0.45rem 0 0.7rem !important;
}
.swal2-icon.swal2-warning {
    border-color: rgba(225, 152, 100, 0.8) !important;
    color: #e19864 !important;
}
.swal2-actions.appolios-quit-actions {
    gap: 0.7rem !important;
    margin-top: 1.05rem !important;
}
.swal2-styled.appolios-btn-cancel,
.swal2-styled.appolios-btn-confirm {
    min-width: 128px !important;
    border-radius: 12px !important;
    font-weight: 800 !important;
    font-size: 1rem !important;
    padding: 0.7rem 1rem !important;
    border: 0 !important;
    transition: transform 0.18s ease, box-shadow 0.2s ease, filter 0.2s ease !important;
}
.swal2-styled.appolios-btn-cancel {
    background: linear-gradient(135deg, #2b4865 0%, #355c7d 100%) !important;
    box-shadow: 0 10px 24px rgba(43, 72, 101, 0.26) !important;
}
.swal2-styled.appolios-btn-confirm {
    background: linear-gradient(135deg, #be123c 0%, #9f1239 100%) !important;
    box-shadow: 0 10px 24px rgba(190, 18, 60, 0.3) !important;
}
.swal2-styled.appolios-btn-cancel:hover,
.swal2-styled.appolios-btn-confirm:hover {
    transform: translateY(-2px) !important;
    filter: brightness(1.04) !important;
}
</style>
<script>
(function () {
    function bindQuitGroupLinks() {
        var links = document.querySelectorAll('.js-quit-group-link');
        if (!links || !links.length) {
            return;
        }

        for (var i = 0; i < links.length; i++) {
            links[i].addEventListener('click', function (event) {
                event.preventDefault();
                var targetUrl = this.getAttribute('href');
                if (!targetUrl) {
                    return;
                }

                if (window.Swal && typeof window.Swal.fire === 'function') {
                    window.Swal.fire({
                        title: 'Quit group?',
                        text: 'You will leave this group and can join again later.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, quit',
                        cancelButtonText: 'Cancel',
                        reverseButtons: true,
                        backdrop: 'rgba(15, 23, 42, 0.55)',
                        background: '#ffffff',
                        showClass: {
                            popup: 'swal2-show'
                        },
                        hideClass: {
                            popup: 'swal2-hide'
                        },
                        customClass: {
                            popup: 'appolios-quit-popup',
                            title: 'appolios-quit-title',
                            htmlContainer: 'appolios-quit-text',
                            actions: 'appolios-quit-actions',
                            confirmButton: 'appolios-btn-confirm',
                            cancelButton: 'appolios-btn-cancel'
                        },
                        buttonsStyling: true
                    }).then(function (result) {
                        if (result && result.isConfirmed) {
                            window.location.href = targetUrl;
                        }
                    });
                    return;
                }

                if (window.confirm('Quit this group?')) {
                    window.location.href = targetUrl;
                }
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bindQuitGroupLinks);
    } else {
        bindQuitGroupLinks();
    }
})();
</script>
