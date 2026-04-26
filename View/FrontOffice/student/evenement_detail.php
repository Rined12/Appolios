<?php
/**
 * APPOLIOS - Student Evenement Detail
 */

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
    <div class="container">
        <div class="dashboard-header student-event-detail-header">
            <div>
                <h1><?= htmlspecialchars($eventName) ?></h1>
                <p><?= htmlspecialchars($evenement['description'] ?? '') ?></p>
            </div>
            <a href="<?= APP_ENTRY ?>?url=student/evenements" class="btn btn-outline">Back to Evenements</a>
        </div>

        <div class="student-event-detail-layout">
            <section class="table-container student-event-info-card">
                <div class="student-event-info-head">
                    <h3>Evenement Details</h3>
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
                            <small>Date debut</small>
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
                            <small>Date fin</small>
                            <strong><?= htmlspecialchars((string) (($evenement['date_fin'] ?? '') ?: 'N/A')) ?></strong>
                        </div>
                    </article>

                    <article class="student-event-meta-item">
                        <span class="student-event-meta-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="9"></circle>
                                <path d="M12 7v5l3 2"></path>
                            </svg>
                        </span>
                        <div>
                            <small>Heure debut</small>
                            <strong><?= htmlspecialchars((string) (($evenement['heure_debut'] ?? '') ?: 'N/A')) ?></strong>
                        </div>
                    </article>

                    <article class="student-event-meta-item">
                        <span class="student-event-meta-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="9"></circle>
                                <path d="M12 7v5l3 2"></path>
                            </svg>
                        </span>
                        <div>
                            <small>Heure fin</small>
                            <strong><?= htmlspecialchars((string) (($evenement['heure_fin'] ?? '') ?: 'N/A')) ?></strong>
                        </div>
                    </article>

                    <article class="student-event-meta-item">
                        <span class="student-event-meta-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 21s-7-4.35-7-10a7 7 0 0 1 14 0c0 5.65-7 10-7 10z"></path>
                                <circle cx="12" cy="11" r="2.5"></circle>
                            </svg>
                        </span>
                        <div>
                            <small>Lieu</small>
                            <strong><?= htmlspecialchars((string) (($evenement['lieu'] ?? '') ?: ($evenement['location'] ?? 'TBA'))) ?></strong>
                        </div>
                    </article>

                    <article class="student-event-meta-item">
                        <span class="student-event-meta-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 7h16"></path>
                                <path d="M4 12h16"></path>
                                <path d="M4 17h10"></path>
                            </svg>
                        </span>
                        <div>
                            <small>Capacite max</small>
                            <strong><?= htmlspecialchars((string) (($evenement['capacite_max'] ?? '') ?: 'N/A')) ?></strong>
                        </div>
                    </article>

                    <article class="student-event-meta-item">
                        <span class="student-event-meta-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 19h16"></path>
                                <path d="M7 19V8"></path>
                                <path d="M12 19V5"></path>
                                <path d="M17 19v-9"></path>
                            </svg>
                        </span>
                        <div>
                            <small>Type</small>
                            <strong><?= htmlspecialchars($typeValue) ?></strong>
                        </div>
                    </article>

                    <article class="student-event-meta-item">
                        <span class="student-event-meta-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 12l2 2 4-4"></path>
                                <circle cx="12" cy="12" r="9"></circle>
                            </svg>
                        </span>
                        <div>
                            <small>Statut</small>
                            <strong><?= htmlspecialchars($statusValue) ?></strong>
                        </div>
                    </article>

                    <article class="student-event-meta-item">
                        <span class="student-event-meta-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </span>
                        <div>
                            <small>Propose par</small>
                            <strong><?= htmlspecialchars($creatorName . ' (' . $creatorRoleLabel . ')') ?></strong>
                        </div>
                    </article>
                </div>

                <!-- Participation Action -->
                <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #eef2f6;">
                    <?php if (!$participation): ?>
                        <form method="POST" action="<?= APP_ENTRY ?>?url=student/participate/<?= (int)$evenement['id'] ?>">
                            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px; font-size: 1.1rem; background: linear-gradient(135deg, #548CA8, #2B4865); border: none;">
                                ✦ Participer à cet événement
                            </button>
                        </form>
                    <?php else: ?>
                        <div style="display: flex; align-items: center; justify-content: space-between; background: #f8fafc; padding: 1.5rem; border-radius: 12px; border: 1px solid #e2e8f0;">
                            <div>
                                <p style="margin: 0; font-size: 0.9rem; color: #64748b; font-weight: 600;">Statut de participation</p>
                                <h4 style="margin: 5px 0 0 0; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                                    <?php 
                                        $pStatus = $participation['details'] ?? 'pending';
                                        if ($pStatus === 'approved'): 
                                    ?>
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        <span style="color: #166534;">Approuvée</span>
                                    <?php elseif ($pStatus === 'rejected'): ?>
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                        <span style="color: #991b1b;">Refusée</span>
                                    <?php else: ?>
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="3"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                        <span style="color: #854d0e;">En attente d'approbation</span>
                                    <?php endif; ?>
                                </h4>
                            </div>
                            <?php if ($pStatus === 'pending'): ?>
                                <form method="POST" action="<?= APP_ENTRY ?>?url=student/cancel-participation/<?= (int)$evenement['id'] ?>">
                                    <button type="submit" class="btn btn-outline" style="color: #ef4444; border-color: #fecaca;" onclick="return confirm('Annuler votre demande ?')">Annuler</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <section class="student-resource-columns">
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
