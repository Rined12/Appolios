<?php
/**
 * APPOLIOS - Admin Evenement Requests
 */
?>

<div class="dashboard">
    <div class="container">
        <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
            <div>
                <h1>Teacher Evenement Requests</h1>
                <p>Review and process pending evenement proposals from teachers</p>
            </div>
            <a href="<?= APP_URL ?>/index.php?url=admin/evenements" class="btn btn-outline">Back to Evenements</a>
        </div>

        <div class="table-container">
            <div class="table-header">
                <h3 style="margin: 0;">Pending Requests</h3>
            </div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Titre</th>
                            <th>Teacher</th>
                            <th>Email</th>
                            <th>Date Debut</th>
                            <th>Lieu</th>
                            <th>Submitted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($requests)): ?>
                            <?php foreach ($requests as $event): ?>
                                <tr>
                                    <td><?= (int) $event['id'] ?></td>
                                    <td><?= htmlspecialchars(($event['titre'] ?? '') ?: ($event['title'] ?? '')) ?></td>
                                    <td><?= htmlspecialchars($event['creator_name'] ?? 'Teacher') ?></td>
                                    <td><?= htmlspecialchars($event['creator_email'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars((string) (($event['date_debut'] ?? '') ?: 'N/A')) ?></td>
                                    <td><?= htmlspecialchars((string) (($event['lieu'] ?? '') ?: ($event['location'] ?? 'TBA'))) ?></td>
                                    <td><?= htmlspecialchars((string) ($event['created_at'] ?? 'N/A')) ?></td>
                                    <td style="min-width: 270px;">
                                        <form action="<?= APP_URL ?>/index.php?url=admin/approve-evenement/<?= (int) $event['id'] ?>" method="POST" style="display: inline-block; margin-right: 6px;">
                                            <button type="submit" class="btn btn-secondary action-btn" style="padding: 5px 10px; font-size: 0.8rem;">Approve</button>
                                        </form>
                                        <form action="<?= APP_URL ?>/index.php?url=admin/reject-evenement/<?= (int) $event['id'] ?>" method="POST" style="display: inline-flex; gap: 6px; align-items: center;">
                                            <input type="text" name="rejection_reason" placeholder="Reason (optional)" style="padding: 6px 8px; font-size: 0.8rem; width: 130px;">
                                            <button type="submit" class="btn action-btn danger" style="padding: 5px 10px; font-size: 0.8rem;">Reject</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 30px;">No pending requests at the moment.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
