<?php
/**
 * APPOLIOS - Admin Users Management
 */
?>

<div class="dashboard">
    <div class="container">
        <div class="dashboard-header">
            <h1>Manage Users</h1>
            <p>View and manage platform users</p>
        </div>

        <div class="table-container">
            <div class="table-header">
                <h3 style="margin: 0;">All Users</h3>
            </div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Registered</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= htmlspecialchars($user['id']) ?></td>
                                    <td><?= htmlspecialchars($user['name']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td>
                                        <span style="padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; background: <?= $user['role'] === 'admin' ? 'var(--yellow)' : 'var(--secondary-color)' ?>; color: white;">
                                            <?= ucfirst(htmlspecialchars($user['role'])) ?>
                                        </span>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                                    <td>
                                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                            <a href="<?= APP_URL ?>/index.php?url=admin/delete-user/<?= $user['id'] ?>" class="btn action-btn danger" style="padding: 5px 10px; font-size: 0.8rem;" data-confirm="Are you sure you want to delete this user?">Delete</a>
                                        <?php else: ?>
                                            <span style="color: var(--gray-dark); font-size: 0.85rem;">Current User</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 30px;">No users found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>