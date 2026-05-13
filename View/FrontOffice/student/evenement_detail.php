<?php
/**
 * APPOLIOS - Student Evenement Detail
 */

$studentSidebarActive = 'evenements';
$eventName = ($evenement['titre'] ?? '') ?: ($evenement['title'] ?? 'Evenement');
$statusValue = (string) (($evenement['statut'] ?? '') ?: 'planifie');
$typeValue = (string) (($evenement['type'] ?? '') ?: 'General');
$creatorName = (string) (($evenement['creator_name'] ?? '') ?: 'Unknown');
$creatorRoleRaw = strtolower((string) (($evenement['creator_role'] ?? '') ?: 'admin'));
$creatorRoleLabel = $creatorRoleRaw === 'teacher' ? 'Teacher' : 'Admin';
$rulesCount = count($rules ?? []);
$materielsCount = count($materiels ?? []);
$plansCount = count($plans ?? []);
?>

<div class="dashboard student-events-page">
    <div class="container admin-dashboard-container" style="max-width: 1400px; width: 100%;">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>

            <div class="admin-main" style="background: transparent; padding: 1rem 0 2rem 0;">
                <div class="dashboard-header student-event-detail-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 2rem;">
                    <div>
                        <a href="<?= APP_ENTRY ?>?url=student/evenements" style="display: inline-flex; align-items: center; gap: 6px; color: #64748b; text-decoration: none; font-size: 0.9rem; font-weight: 600; margin-bottom: 0.75rem; transition: color 0.2s;" onmouseover="this.style.color='#1e293b'" onmouseout="this.style.color='#64748b'">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                            Back to Events
                        </a>
                        <h1 style="font-size: 1.8rem; font-weight: 800; color: #1e293b;"><?= htmlspecialchars($eventName) ?></h1>
                        <p style="color: #64748b;"><?= htmlspecialchars($evenement['description'] ?? '') ?></p>
                    </div>
                    <?php if (isset($participationStatus)): ?>
                        <?php if ($participationStatus === 'approved'): ?>
                            <a href="<?= APP_ENTRY ?>?url=student/download-ticket/<?= (int) ($evenement['id'] ?? 0) ?>" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px;">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                </svg>
                                View Ticket
                            </a>
                        <?php elseif ($participationStatus === 'pending'): ?>
                            <span style="background: #fef3c7; color: #92400e; padding: 10px 20px; border-radius: 8px; font-weight: 600;">
                                Participation Pending
                            </span>
                        <?php elseif ($participationStatus === 'rejected'): ?>
                            <span style="background: #fee2e2; color: #991b1b; padding: 10px 20px; border-radius: 8px; font-weight: 600;">
                                Participation Rejected
                            </span>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="<?= APP_ENTRY ?>?url=student/participate/<?= (int) ($evenement['id'] ?? 0) ?>" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px;">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="8.5" cy="7" r="4"></circle>
                                <line x1="20" y1="8" x2="20" y2="14"></line>
                                <line x1="23" y1="11" x2="17" y2="11"></line>
                            </svg>
                            Participate
                        </a>
                    <?php endif; ?>
                </div>

                <div class="student-event-detail-layout">
                    <section class="table-container student-event-info-card">
                        <div class="student-event-info-head">
                            <h3>Event Details</h3>
                            <span class="student-event-status-pill"><?= htmlspecialchars($statusValue) ?></span>
                        </div>

                        <div class="student-event-meta-grid">
                            <article class="student-event-meta-item">
                                <span class="student-event-meta-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M8 2v4"></path>
                                        <path d="M16 2v4"></path>
                                        <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                                        <path d="M3 10h18"></path>
                                    </svg>
                                </span>
                                <div>
                                    <small>Start Date</small>
                                    <strong><?= htmlspecialchars((string) ($evenement['date_debut'] ?? 'N/A')) ?></strong>
                                </div>
                            </article>

                            <article class="student-event-meta-item">
                                <span class="student-event-meta-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M8 2v4"></path>
                                        <path d="M16 2v4"></path>
                                        <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                                        <path d="M3 10h18"></path>
                                    </svg>
                                </span>
                                <div>
                                    <small>End Date</small>
                                    <strong><?= htmlspecialchars((string) ($evenement['date_fin'] ?? 'N/A')) ?></strong>
                                </div>
                            </article>

                            <article class="student-event-meta-item">
                                <span class="student-event-meta-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12 6 12 12 16 14"></polyline>
                                    </svg>
                                </span>
                                <div>
                                    <small>Time</small>
                                    <strong><?= htmlspecialchars((string) (($evenement['heure_debut'] ?? '') ?: 'N/A')) ?></strong>
                                </div>
                            </article>

                            <article class="student-event-meta-item">
                                <span class="student-event-meta-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                        <circle cx="12" cy="10" r="3"></circle>
                                    </svg>
                                </span>
                                <div>
                                    <small>Location</small>
                                    <strong><?= htmlspecialchars((string) (($evenement['lieu'] ?? '') ?: 'N/A')) ?></strong>
                                </div>
                            </article>

                            <article class="student-event-meta-item">
                                <span class="student-event-meta-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="9" cy="7" r="4"></circle>
                                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                    </svg>
                                </span>
                                <div>
                                    <small>Organizer</small>
                                    <strong><?= htmlspecialchars($creatorName) ?></strong>
                                </div>
                            </article>

                            <article class="student-event-meta-item">
                                <span class="student-event-meta-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M4 6h16v12H4z"></path>
                                        <path d="M8 10h8"></path>
                                        <path d="M8 14h5"></path>
                                    </svg>
                                </span>
                                <div>
                                    <small>Type</small>
                                    <strong><?= htmlspecialchars($typeValue) ?></strong>
                                </div>
                            </article>
                        </div>
                    </section>
                </div>

                <section class="student-resource-columns" style="margin-top: 2rem;">
                    <div class="ressource-list-card">
                        <h3 class="student-resource-heading">
                            <span class="student-resource-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M9 11l3 3L22 4"></path>
                                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                                </svg>
                            </span>
                            <span>Rules</span>
                            <span class="student-resource-count"><?= (int) $rulesCount ?></span>
                        </h3>
                        <ul class="ressource-list">
                            <?php if (!empty($rules)): ?>
                                <?php foreach ($rules as $item): ?>
                                    <li>
                                        <strong><?= htmlspecialchars($item['title']) ?></strong>
                                        <?php if (!empty($item['details'])): ?><p><?= htmlspecialchars($item['details']) ?></p><?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="empty">No rules available.</li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <div class="ressource-list-card">
                        <h3 class="student-resource-heading">
                            <span class="student-resource-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                </svg>
                            </span>
                            <span>Materiel</span>
                            <span class="student-resource-count"><?= (int) $materielsCount ?></span>
                        </h3>
                        <ul class="ressource-list">
                            <?php if (!empty($materiels)): ?>
                                <?php foreach ($materiels as $item): ?>
                                    <li>
                                        <strong><?= htmlspecialchars($item['title']) ?></strong>
                                        <?php if (!empty($item['details'])): ?><p><?= htmlspecialchars($item['details']) ?></p><?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="empty">No materiel available.</li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <div class="ressource-list-card">
                        <h3 class="student-resource-heading">
                            <span class="student-resource-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                                    <path d="M16 2v4"></path>
                                    <path d="M8 2v4"></path>
                                    <path d="M3 10h18"></path>
                                </svg>
                            </span>
                            <span>Plan de journee</span>
                            <span class="student-resource-count"><?= (int) $plansCount ?></span>
                        </h3>
                        <ul class="ressource-list">
                            <?php if (!empty($plans)): ?>
                                <?php foreach ($plans as $item): ?>
                                    <li>
                                        <strong><?= htmlspecialchars($item['title']) ?></strong>
                                        <?php if (!empty($item['details'])): ?><p><?= htmlspecialchars($item['details']) ?></p><?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="empty">No plan available.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>