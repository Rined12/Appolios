<?php $adminSidebarActive = 'sl-groupes'; ?>
<div class="dashboard student-events-page collab-hub sl-admin--groupes">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/../partials/sidebar.php'; ?>
            <div class="admin-main">
                <?php require __DIR__ . '/../../../FrontOffice/student/partials/collab_hub_styles.php'; ?>
                <style>
                    .sl-admin--groupes .collab-admin-approval-badge {
                        position: absolute;
                        top: 10px;
                        left: 10px;
                        z-index: 3;
                        padding: 0.38rem 0.72rem;
                        border-radius: 999px;
                        font-size: 0.68rem;
                        font-weight: 800;
                        letter-spacing: 0.04em;
                        text-transform: lowercase;
                        border: 1px solid transparent;
                        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.12);
                    }
                    .sl-admin--groupes .collab-admin-approval-badge--approved {
                        background: #d4edda;
                        color: #155724;
                        border-color: rgba(21, 87, 36, 0.2);
                    }
                    .sl-admin--groupes .collab-admin-approval-badge--rejected {
                        background: #f8d7da;
                        color: #721c24;
                        border-color: rgba(114, 28, 36, 0.22);
                    }
                    .sl-admin--groupes .collab-admin-approval-badge--pending {
                        background: #fff3cd;
                        color: #856404;
                        border-color: rgba(133, 100, 4, 0.25);
                    }
                    .sl-admin--groupes .collab-group-card--sl-rejected {
                        border-color: rgba(248, 113, 113, 0.45);
                    }
                    .sl-admin--groupes .collab-group-card--sl-pending {
                        border-color: rgba(251, 191, 36, 0.5);
                    }
                    .sl-admin--groupes .collab-group-card__creator {
                        font-size: 0.78rem;
                        color: var(--ch-muted);
                        margin: 0 0 1rem;
                        font-weight: 600;
                    }
                    .sl-admin--groupes .collab-admin-action-stack {
                        display: flex;
                        flex-direction: column;
                        gap: 0.55rem;
                    }
                    .sl-admin--groupes .collab-admin-pdf-btn {
                        display: inline-flex;
                        align-items: center;
                        justify-content: center;
                        gap: 0.5rem;
                        width: 100%;
                        padding: 0.72rem 1rem;
                        border-radius: 12px;
                        font-weight: 800;
                        font-size: 0.88rem;
                        text-decoration: none;
                        border: none;
                        cursor: pointer;
                        background: linear-gradient(135deg, #1e3a5f 0%, #2b4865 55%, #355c7d 100%);
                        color: #fff !important;
                        box-shadow: 0 10px 28px rgba(30, 58, 95, 0.38);
                        transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
                    }
                    .sl-admin--groupes .collab-admin-pdf-btn:hover {
                        transform: translateY(-2px);
                        filter: brightness(1.05);
                        box-shadow: 0 14px 34px rgba(30, 58, 95, 0.45);
                    }
                    .sl-admin--groupes .collab-admin-secondary-actions {
                        display: flex;
                        flex-wrap: wrap;
                        gap: 0.5rem;
                        align-items: center;
                    }
                    .sl-admin--groupes .collab-admin-edit-btn {
                        display: inline-flex;
                        align-items: center;
                        gap: 0.35rem;
                        padding: 0.5rem 0.95rem;
                        border-radius: 10px;
                        font-size: 0.8rem;
                        font-weight: 700;
                        text-decoration: none;
                        background: linear-gradient(135deg, #548ca8 0%, #3d7a96 100%);
                        color: #fff !important;
                        border: 1px solid rgba(255, 255, 255, 0.2);
                        box-shadow: 0 6px 16px rgba(84, 140, 168, 0.35);
                        transition: transform 0.15s ease, box-shadow 0.2s ease;
                    }
                    .sl-admin--groupes .collab-admin-edit-btn:hover {
                        transform: translateY(-2px);
                        box-shadow: 0 8px 20px rgba(84, 140, 168, 0.42);
                    }
                    .sl-admin--groupes .collab-admin-moderation {
                        display: flex;
                        flex-wrap: wrap;
                        gap: 0.45rem;
                        align-items: center;
                        width: 100%;
                        margin-top: 0.15rem;
                    }
                    .sl-admin--groupes .collab-admin-moderation .btn {
                        font-size: 0.78rem;
                        padding: 0.45rem 0.75rem;
                        border-radius: 10px;
                    }
                </style>

                <div class="header collab-hero">
                    <div class="collab-hero__inner">
                        <div>
                            <div class="collab-eyebrow"><i class="bi bi-shield-lock-fill" aria-hidden="true"></i> Social learning</div>
                            <h1>Groups</h1>
                            <p>Review approval status, open activity reports, and manage every study circle from one place.</p>
                        </div>
                        <div class="collab-hero-actions">
                            <a href="<?= APP_ENTRY ?>?url=admin/sl-groupes/create" class="collab-btn-primary">
                                <i class="bi bi-plus-lg" aria-hidden="true"></i> Create group
                            </a>
                            <a href="<?= APP_ENTRY ?>?url=admin/sl-discussions" class="collab-btn-ghost">
                                <i class="bi bi-chat-square-text" aria-hidden="true"></i> Discussions
                            </a>
                        </div>
                    </div>
                </div>

                <h3 class="collab-section-label"><span class="dot" aria-hidden="true"></span> All groups</h3>

                <?php if (!empty($groupes)): ?>
                    <div class="collab-group-grid">
                        <?php foreach ($groupes as $g): ?>
                            <?php
                                $approval = (string) ($g['approval_statut'] ?? $g['approval_status'] ?? 'en_cours');
                                $badgeClass = $approval === 'approuve'
                                    ? 'collab-admin-approval-badge--approved'
                                    : ($approval === 'rejete' ? 'collab-admin-approval-badge--rejected' : 'collab-admin-approval-badge--pending');
                                $cardMod = $approval === 'rejete' ? ' collab-group-card--sl-rejected' : ($approval === 'en_cours' ? ' collab-group-card--sl-pending' : '');
                                $cover = trim((string) ($g['image_url'] ?? $g['photo'] ?? $g['image'] ?? ''));
                            ?>
                            <div class="article collab-group-card<?= $cardMod ?>">
                                <div class="collab-group-card__media<?= $cover === '' ? ' collab-group-card__media--fallback' : '' ?>">
                                    <span class="collab-admin-approval-badge <?= $badgeClass ?>"><?= htmlspecialchars($approval) ?></span>
                                    <?php if ($cover !== ''): ?>
                                        <img src="<?= htmlspecialchars($cover) ?>" alt="" loading="lazy" onerror="this.closest('.collab-group-card__media').classList.add('collab-group-card__media--fallback'); this.remove();">
                                    <?php else: ?>
                                        <div class="collab-group-card__ph" aria-hidden="true"><i class="bi bi-people-fill"></i></div>
                                    <?php endif; ?>
                                    <div class="collab-group-card__overlay"></div>
                                    <h4 class="collab-group-card__floating-title"><?= htmlspecialchars((string) ($g['nom_groupe'] ?? 'Groupe')) ?></h4>
                                </div>
                                <div class="collab-group-card__body">
                                    <p class="collab-line-clamp-2"><?= htmlspecialchars((string) ($g['description'] ?? '')) ?></p>
                                    <p class="collab-group-card__creator">Creator: <?= htmlspecialchars((string) ($g['createur_name'] ?? 'N/A')) ?></p>
                                    <div class="collab-admin-action-stack">
                                        <a class="collab-admin-pdf-btn" target="_blank" rel="noopener" href="<?= APP_ENTRY ?>?url=admin/sl-groupes/<?= (int) $g['id_groupe'] ?>/activity-pdf">
                                            <i class="bi bi-file-earmark-bar-graph" aria-hidden="true"></i> Activity PDF
                                        </a>
                                        <div class="collab-admin-secondary-actions">
                                            <a class="collab-admin-edit-btn" href="<?= APP_ENTRY ?>?url=admin/sl-groupes/<?= (int) $g['id_groupe'] ?>/edit">
                                                <i class="bi bi-pencil-square" aria-hidden="true"></i> Edit
                                            </a>
                                            <?php if ($approval === 'en_cours'): ?>
                                                <div class="collab-admin-moderation">
                                                    <form method="POST" action="<?= APP_ENTRY ?>?url=admin/sl-groupes/<?= (int) $g['id_groupe'] ?>/approve" style="display:inline;" data-skip-confirm="1" data-sl-action="approve">
                                                        <button class="btn btn-primary" type="submit">Approve</button>
                                                    </form>
                                                    <form method="POST" action="<?= APP_ENTRY ?>?url=admin/sl-groupes/<?= (int) $g['id_groupe'] ?>/reject" style="display:inline;" data-skip-confirm="1" data-sl-action="reject">
                                                        <button class="btn btn-outline" type="submit">Reject</button>
                                                    </form>
                                                    <form method="POST" action="<?= APP_ENTRY ?>?url=admin/sl-groupes/<?= (int) $g['id_groupe'] ?>/delete" style="display:inline;" data-skip-confirm="1" data-sl-action="delete">
                                                        <button class="btn action-btn danger" type="submit">Delete</button>
                                                    </form>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="collab-empty">
                        <div class="collab-empty-icon" aria-hidden="true">👥</div>
                        <h3>No groups</h3>
                        <p>There are no study circles yet. Create one to get started.</p>
                        <div style="margin-top:1.25rem;">
                            <a href="<?= APP_ENTRY ?>?url=admin/sl-groupes/create" class="collab-btn-primary">Create group</a>
                        </div>
                    </div>
                <?php endif; ?>

                <?php require __DIR__ . '/../partials/sl_actions_swal.php'; ?>
            </div>
        </div>
    </div>
</div>
