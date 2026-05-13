<?php $adminSidebarActive = 'sl-discussions'; ?>
<div class="dashboard student-events-page collab-hub sl-admin--discussions">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/../partials/sidebar.php'; ?>
            <div class="admin-main">
                <?php require __DIR__ . '/../../../FrontOffice/student/partials/collab_hub_styles.php'; ?>
                <style>
                    .sl-admin--discussions .collab-admin-approval-badge {
                        display: inline-flex;
                        align-items: center;
                        padding: 0.32rem 0.65rem;
                        border-radius: 999px;
                        font-size: 0.65rem;
                        font-weight: 800;
                        letter-spacing: 0.04em;
                        text-transform: lowercase;
                        border: 1px solid transparent;
                    }
                    .sl-admin--discussions .collab-admin-approval-badge--approved {
                        background: #d4edda;
                        color: #155724;
                        border-color: rgba(21, 87, 36, 0.2);
                    }
                    .sl-admin--discussions .collab-admin-approval-badge--rejected {
                        background: #f8d7da;
                        color: #721c24;
                        border-color: rgba(114, 28, 36, 0.22);
                    }
                    .sl-admin--discussions .collab-admin-approval-badge--pending {
                        background: #fff3cd;
                        color: #856404;
                        border-color: rgba(133, 100, 4, 0.25);
                    }
                    .sl-admin--discussions .collab-disc-card--sl-pending {
                        border-color: rgba(251, 191, 36, 0.45);
                    }
                    .sl-admin--discussions .collab-disc-card--sl-rejected {
                        border-color: rgba(248, 113, 113, 0.4);
                    }
                    .sl-admin--discussions .collab-disc-card__meta {
                        margin: 0 0 1rem;
                        font-size: 0.78rem;
                        font-weight: 700;
                        color: var(--ch-muted);
                    }
                    .sl-admin--discussions .collab-disc-pill-row {
                        display: flex;
                        flex-wrap: wrap;
                        align-items: center;
                        gap: 0.45rem;
                        margin-bottom: 0.75rem;
                    }
                    .sl-admin--discussions .collab-admin-moderation {
                        display: flex;
                        flex-wrap: wrap;
                        gap: 0.45rem;
                        align-items: center;
                    }
                    .sl-admin--discussions .collab-admin-moderation .btn {
                        font-size: 0.78rem;
                        padding: 0.45rem 0.75rem;
                        border-radius: 10px;
                    }
                </style>

                <div class="header collab-hero">
                    <div class="collab-hero__inner">
                        <div>
                            <div class="collab-eyebrow"><i class="bi bi-chat-dots-fill" aria-hidden="true"></i> Social learning</div>
                            <h1>Discussions</h1>
                            <p>Moderate threads across all groups — approve or reject submissions, edit metadata, and open live chat on approved topics.</p>
                        </div>
                        <div class="collab-hero-actions">
                            <a href="<?= APP_ENTRY ?>?url=admin/sl-discussions/create" class="collab-btn-primary">
                                <i class="bi bi-plus-lg" aria-hidden="true"></i> New discussion
                            </a>
                            <a href="<?= APP_ENTRY ?>?url=admin/sl-groupes" class="collab-btn-ghost">
                                <i class="bi bi-collection" aria-hidden="true"></i> Groups
                            </a>
                        </div>
                    </div>
                </div>

                <h3 class="collab-section-label"><span class="dot" aria-hidden="true"></span> All threads</h3>

                <?php if (!empty($discussions)): ?>
                    <div class="collab-disc-grid">
                        <?php foreach ($discussions as $d): ?>
                            <?php
                                $idDisc = (int) ($d['id_discussion'] ?? $d['id'] ?? 0);
                                $approval = (string) ($d['approval_statut'] ?? $d['approval_status'] ?? 'en_cours');
                                $badgeClass = $approval === 'approuve'
                                    ? 'collab-admin-approval-badge--approved'
                                    : ($approval === 'rejete' ? 'collab-admin-approval-badge--rejected' : 'collab-admin-approval-badge--pending');
                                $cardMod = $approval === 'rejete' ? ' collab-disc-card--sl-rejected' : ($approval === 'en_cours' ? ' collab-disc-card--sl-pending' : '');
                            ?>
                            <div class="article collab-disc-card<?= $cardMod ?>">
                                <div class="collab-disc-pill-row">
                                    <span class="collab-pill collab-pill--group"><i class="bi bi-people-fill" aria-hidden="true"></i> <?= htmlspecialchars((string) ($d['nom_groupe'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></span>
                                    <span class="collab-admin-approval-badge <?= $badgeClass ?>"><?= htmlspecialchars($approval, ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                                <h4 class="collab-disc-card__title"><?= htmlspecialchars((string) ($d['titre'] ?? 'Discussion'), ENT_QUOTES, 'UTF-8') ?></h4>
                                <p class="collab-disc-card__excerpt collab-line-clamp-3"><?= htmlspecialchars((string) ($d['contenu'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                                <p class="collab-disc-card__meta">Author: <?= htmlspecialchars((string) ($d['auteur_name'] ?? 'N/A'), ENT_QUOTES, 'UTF-8') ?></p>
                                <div class="collab-card-actions">
                                    <?php if ($approval === 'approuve'): ?>
                                        <a class="collab-chip-btn collab-chip-btn--live" href="<?= APP_ENTRY ?>?url=admin/sl-discussions/<?= $idDisc ?>/chat">
                                            <i class="bi bi-lightning-charge-fill" aria-hidden="true"></i> Live chat
                                        </a>
                                    <?php endif; ?>
                                    <a class="collab-chip-btn collab-chip-btn--muted" href="<?= APP_ENTRY ?>?url=admin/sl-discussions/<?= $idDisc ?>/edit">
                                        <i class="bi bi-pencil-square" aria-hidden="true"></i> Edit
                                    </a>
                                    <?php if ($approval === 'en_cours'): ?>
                                        <div class="collab-admin-moderation">
                                            <form method="POST" action="<?= APP_ENTRY ?>?url=admin/sl-discussions/<?= $idDisc ?>/approve" style="display:inline;" data-skip-confirm="1" data-sl-action="approve">
                                                <button class="btn btn-primary" type="submit">Approve</button>
                                            </form>
                                            <form method="POST" action="<?= APP_ENTRY ?>?url=admin/sl-discussions/<?= $idDisc ?>/reject" style="display:inline;" data-skip-confirm="1" data-sl-action="reject">
                                                <button class="btn btn-outline" type="submit">Reject</button>
                                            </form>
                                            <form method="POST" action="<?= APP_ENTRY ?>?url=admin/sl-discussions/<?= $idDisc ?>/delete" style="display:inline;" data-skip-confirm="1" data-sl-action="delete">
                                                <button class="btn action-btn danger" type="submit">Delete</button>
                                            </form>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="collab-empty">
                        <div class="collab-empty-icon" aria-hidden="true">💬</div>
                        <h3>No discussions</h3>
                        <p>There are no discussion threads yet. Create one or wait for students to post in approved groups.</p>
                        <div style="margin-top:1.25rem;">
                            <a href="<?= APP_ENTRY ?>?url=admin/sl-discussions/create" class="collab-btn-primary">Create discussion</a>
                        </div>
                    </div>
                <?php endif; ?>

                <?php require __DIR__ . '/../partials/sl_actions_swal.php'; ?>
            </div>
        </div>
    </div>
</div>
