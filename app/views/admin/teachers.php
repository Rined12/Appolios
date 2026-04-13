<?php
/**
 * APPOLIOS - Admin Teachers Management
 */
?>

<div class="dashboard">
    <div class="container">
        <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1>Manage Teachers</h1>
                <p>View and manage teacher accounts</p>
            </div>
            <a href="<?= APP_URL ?>/index.php?url=admin/add-teacher" class="btn btn-yellow">Add New Teacher</a>
        </div>

        <div class="table-container">
            <div class="table-header">
                <h3 style="margin: 0;">All Teachers</h3>
            </div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Registered</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($teachers)): ?>
                            <?php foreach ($teachers as $teacher): ?>
                                <tr>
                                    <td><?= htmlspecialchars($teacher['id']) ?></td>
                                    <td><?= htmlspecialchars($teacher['name']) ?></td>
                                    <td><?= htmlspecialchars($teacher['email']) ?></td>
                                    <td><?= date('M d, Y', strtotime($teacher['created_at'])) ?></td>
                                    <td>
                                        <a href="<?= APP_URL ?>/index.php?url=admin/delete-user/<?= $teacher['id'] ?>" class="btn action-btn danger" style="padding: 5px 10px; font-size: 0.8rem;" data-confirm="Are you sure you want to delete this teacher?">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 30px;">No teachers found. <a href="<?= APP_URL ?>/index.php?url=admin/add-teacher">Add your first teacher</a></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
