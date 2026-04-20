<?php
/**
 * APPOLIOS - Teacher Evenements Management
 */

$teacherSidebarActive = 'evenements';
?>

<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main">
        <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
            <div>
                <h1>My Evenements</h1>
                <p>Create and manage your evenement proposals for admin approval</p>
            </div>
            <div style="display: flex; gap: 10px;">
                <a href="<?= APP_URL ?>/index.php?url=teacher/dashboard" class="btn btn-outline">Back to Dashboard</a>
                <a href="<?= APP_URL ?>/index.php?url=teacher/add-evenement" class="btn btn-yellow">Propose Evenement</a>
            </div>
        </div>

        <div class="table-container">
            <div class="table-header">
                <h3 style="margin: 0;">All My Evenements</h3>
            </div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Titre</th>
                            <th>Date Debut</th>
                            <th>Lieu</th>
                            <th>Approval</th>
                            <th>Ressources</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($evenements)): ?>
                            <?php foreach ($evenements as $evenement): ?>
                                <?php $approval = (string) ($evenement['approval_status'] ?? 'approved'); ?>
                                <tr>
                                    <td><?= (int) $evenement['id'] ?></td>
                                    <td><?= htmlspecialchars(($evenement['titre'] ?? '') ?: ($evenement['title'] ?? '')) ?></td>
                                    <td><?= htmlspecialchars((string) (($evenement['date_debut'] ?? '') ?: 'N/A')) ?></td>
                                    <td><?= htmlspecialchars((string) (($evenement['lieu'] ?? '') ?: ($evenement['location'] ?? 'TBA'))) ?></td>
                                    <td>
                                        <span class="student-event-status" style="text-transform: uppercase; font-size: 0.72rem;">
                                            <?= htmlspecialchars($approval) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?= APP_URL ?>/index.php?url=teacher/evenement-ressources&evenement_id=<?= (int) $evenement['id'] ?>" class="btn btn-secondary action-btn" style="padding: 5px 10px; font-size: 0.8rem;">
                                            <?= (int) ($evenement['resource_count'] ?? 0) ?> items
                                        </a>
                                    </td>
                                    <td>
                                        <a href="<?= APP_URL ?>/index.php?url=teacher/edit-evenement/<?= (int) $evenement['id'] ?>" class="btn btn-primary action-btn" style="padding: 5px 10px; font-size: 0.8rem;">Edit</a>
                                        <?php if ($approval === 'pending'): ?>
                                            <a href="<?= APP_URL ?>/index.php?url=teacher/delete-evenement/<?= (int) $evenement['id'] ?>" class="btn action-btn danger" style="padding: 5px 10px; font-size: 0.8rem;" data-confirm="Delete this pending evenement and all linked resources?">Delete</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 30px;">No evenements yet. <a href="<?= APP_URL ?>/index.php?url=teacher/add-evenement">Propose your first evenement</a></td>
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
