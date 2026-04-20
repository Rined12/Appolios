<?php
/**
 * APPOLIOS - Admin Evenements Management
 */
$adminSidebarActive = 'evenements';
?>

<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>

            <div class="admin-main">
                <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                    <div>
                        <h1>Module Evenement</h1>
                        <p>Create and manage events from your back-office</p>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <a href="<?= APP_URL ?>/index.php?url=admin/dashboard" class="btn btn-outline">Back to Dashboard</a>
                        <a href="<?= APP_URL ?>/index.php?url=admin/evenement-requests" class="btn btn-secondary">Teacher Requests</a>
                        <a href="<?= APP_URL ?>/index.php?url=admin/add-evenement" class="btn btn-yellow">Add Evenement</a>
                    </div>
                </div>

                <div class="table-container">
                    <div class="table-header">
                        <h3 style="margin: 0;">Upcoming Evenements</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="events-wide-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Titre</th>
                                    <th>Description</th>
                                    <th>Date Debut</th>
                                    <th>Date Fin</th>
                                    <th>Heure Debut</th>
                                    <th>Heure Fin</th>
                                    <th>Lieu</th>
                                    <th>Capacite</th>
                                    <th>Type</th>
                                    <th>Statut</th>
                                    <th>Ressource</th>
                                    <th>Created By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($evenements)): ?>
                                    <?php foreach ($evenements as $evenement): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($evenement['id']) ?></td>
                                            <td><?= htmlspecialchars($evenement['titre'] ?: $evenement['title']) ?></td>
                                            <td><?= htmlspecialchars((string) ($evenement['description'] ?? '')) ?></td>
                                            <td>
                                                <?= htmlspecialchars((string) (($evenement['date_debut'] ?? '') ?: (!empty($evenement['event_date']) ? date('Y-m-d', strtotime((string) $evenement['event_date'])) : 'N/A'))) ?>
                                            </td>
                                            <td><?= htmlspecialchars((string) (($evenement['date_fin'] ?? '') ?: 'N/A')) ?></td>
                                            <td>
                                                <?= htmlspecialchars((string) (($evenement['heure_debut'] ?? '') ?: (!empty($evenement['event_date']) ? date('H:i', strtotime((string) $evenement['event_date'])) : 'N/A'))) ?>
                                            </td>
                                            <td><?= htmlspecialchars((string) (($evenement['heure_fin'] ?? '') ?: 'N/A')) ?></td>
                                            <td><?= htmlspecialchars((string) (($evenement['lieu'] ?? '') ?: ($evenement['location'] ?: 'TBA'))) ?></td>
                                            <td><?= htmlspecialchars((string) (($evenement['capacite_max'] ?? '') ?: 'N/A')) ?></td>
                                            <td><?= htmlspecialchars((string) (($evenement['type'] ?? '') ?: 'N/A')) ?></td>
                                            <td><?= htmlspecialchars((string) (($evenement['statut'] ?? '') ?: 'planifie')) ?></td>
                                            <td>
                                                <a href="<?= APP_URL ?>/index.php?url=admin/evenement-ressources&evenement_id=<?= (int) $evenement['id'] ?>" class="btn btn-secondary action-btn" style="padding: 5px 10px; font-size: 0.8rem;">
                                                    <?= (int) ($evenement['resource_count'] ?? 0) ?> items
                                                </a>
                                            </td>
                                            <td>
                                                <?php
                                                $creatorRole = strtolower((string) ($evenement['creator_role'] ?? 'admin'));
                                                $creatorRoleLabel = $creatorRole === 'teacher' ? 'Teacher' : 'Admin';
                                                ?>
                                                <?= htmlspecialchars($evenement['creator_name']) ?>
                                                <span style="display: block; font-size: 0.75rem; opacity: 0.8;"><?= htmlspecialchars($creatorRoleLabel) ?></span>
                                            </td>
                                            <td class="events-actions-cell">
                                                <a href="<?= APP_URL ?>/index.php?url=admin/edit-evenement/<?= (int) $evenement['id'] ?>" class="btn btn-secondary action-btn" style="padding: 5px 10px; font-size: 0.8rem;">Edit</a>
                                                <a href="<?= APP_URL ?>/index.php?url=admin/delete-evenement/<?= (int) $evenement['id'] ?>" class="btn action-btn danger" style="padding: 5px 10px; font-size: 0.8rem;" data-confirm="Delete this evenement and all linked resources?">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="14" style="text-align: center; padding: 30px;">No events found. <a href="<?= APP_URL ?>/index.php?url=admin/add-evenement">Create your first evenement</a></td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
