<?php
/**
 * APPOLIOS - Activity Log / History Page
 */
?>

<div class="dashboard">
    <div class="container admin-dashboard-container" style="max-width: 1400px; width: 100%;">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>

            <div class="admin-main" style="background: transparent; padding: 1rem 0 2rem 0;">
                <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <h1>Activity Log</h1>
                        <p>Track all user activities on the platform</p>
                    </div>
                    <a href="javascript:history.back()" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; background: #6c757d;">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" style="transform: rotate(180deg);">
                            <path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/>
                        </svg>
                        Back
                    </a>
                </div>

                <?php if (isset($_SESSION['flash'])): ?>
                    <div class="alert alert-<?= $_SESSION['flash']['type'] === 'error' ? 'danger' : 'success' ?>" style="margin-bottom: 20px;">
                        <?= htmlspecialchars($_SESSION['flash']['message']) ?>
                    </div>
                    <?php unset($_SESSION['flash']); ?>
                <?php endif; ?>

                <!-- Stats Cards -->
                <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
                    <div class="stat-card" style="background: linear-gradient(135deg, rgba(84, 140, 168, 0.1) 0%, rgba(84, 140, 168, 0.05) 100%); border: 1px solid rgba(84, 140, 168, 0.2); border-radius: 12px; padding: 20px;">
                        <div style="font-size: 2rem; font-weight: 700; color: #548CA8;"><?= $stats['total'] ?? 0 ?></div>
                        <div style="color: #64748b; font-size: 0.9rem;">Total Activities</div>
                    </div>
                    <div class="stat-card" style="background: linear-gradient(135deg, rgba(34, 197, 94, 0.1) 0%, rgba(34, 197, 94, 0.05) 100%); border: 1px solid rgba(34, 197, 94, 0.2); border-radius: 12px; padding: 20px;">
                        <div style="font-size: 2rem; font-weight: 700; color: #22c55e;"><?= $stats['logins'] ?? 0 ?></div>
                        <div style="color: #64748b; font-size: 0.9rem;">Logins</div>
                    </div>
                    <div class="stat-card" style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(239, 68, 68, 0.05) 100%); border: 1px solid rgba(239, 68, 68, 0.2); border-radius: 12px; padding: 20px;">
                        <div style="font-size: 2rem; font-weight: 700; color: #ef4444;"><?= $stats['logouts'] ?? 0 ?></div>
                        <div style="color: #64748b; font-size: 0.9rem;">Logouts</div>
                    </div>
                    <div class="stat-card" style="background: linear-gradient(135deg, rgba(251, 146, 60, 0.1) 0%, rgba(251, 146, 60, 0.05) 100%); border: 1px solid rgba(251, 146, 60, 0.2); border-radius: 12px; padding: 20px;">
                        <div style="font-size: 2rem; font-weight: 700; color: #fb923c;"><?= $stats['registers'] ?? 0 ?></div>
                        <div style="color: #64748b; font-size: 0.9rem;">Registrations</div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="table-container" style="margin-bottom: 20px;">
                    <div class="table-header">
                        <h3 style="margin: 0;">Filters</h3>
                    </div>
                    <form method="GET" action="<?= APP_ENTRY ?>?url=admin/activity-log" style="padding: 20px; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                        <input type="hidden" name="url" value="admin/activity-log">

                        <div>
                            <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #1e293b; font-size: 0.9rem;">Activity Type</label>
                            <select name="activity_type" style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.9rem;">
                                <option value="">All Types</option>
                                <option value="login" <?= ($filters['activity_type'] ?? '') === 'login' ? 'selected' : '' ?>>Login</option>
                                <option value="logout" <?= ($filters['activity_type'] ?? '') === 'logout' ? 'selected' : '' ?>>Logout</option>
                                <option value="register" <?= ($filters['activity_type'] ?? '') === 'register' ? 'selected' : '' ?>>Register</option>
                                <option value="reset_password" <?= ($filters['activity_type'] ?? '') === 'reset_password' ? 'selected' : '' ?>>Reset Password</option>
                            </select>
                        </div>

                        <div>
                            <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #1e293b; font-size: 0.9rem;">Date From</label>
                            <input type="date" name="date_from" value="<?= htmlspecialchars($filters['date_from'] ?? '') ?>" style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.9rem;">
                        </div>

                        <div>
                            <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #1e293b; font-size: 0.9rem;">Date To</label>
                            <input type="date" name="date_to" value="<?= htmlspecialchars($filters['date_to'] ?? '') ?>" style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.9rem;">
                        </div>

                        <div style="display: flex; align-items: flex-end;">
                            <button type="submit" class="btn" style="padding: 10px 20px; background: #548CA8; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
                                Filter
                            </button>
                            <a href="<?= APP_ENTRY ?>?url=admin/activity-log" class="btn" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; margin-left: 10px;">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Activities Table -->
                <div class="table-container">
                    <div class="table-header">
                        <h3 style="margin: 0;">Recent Activities</h3>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Role</th>
                                    <th>Activity</th>
                                    <th>Description</th>
                                    <th>IP Address</th>
                                    <th>Timestamp</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($activities)): ?>
                                    <?php foreach ($activities as $activity): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($activity['id']) ?></td>
                                            <td>
                                                <?php if ($activity['user_name']): ?>
                                                    <strong><?= htmlspecialchars($activity['user_name']) ?></strong><br>
                                                    <small style="color: #64748b;"><?= htmlspecialchars($activity['user_email']) ?></small>
                                                <?php else: ?>
                                                    <span style="color: #94a3b8;">Guest</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge badge-<?= $activity['user_role'] ?? 'secondary' ?>" style="padding: 2px 8px; border-radius: 12px; font-size: 0.75rem; background: <?= $activity['user_role'] === 'admin' ? '#E19864' : ($activity['user_role'] === 'teacher' ? '#548CA8' : '#28a745'); ?>; color: white;">
                                                    <?= ucfirst($activity['user_role'] ?? 'N/A') ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span style="padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 600; background: <?= $activity['activity_type'] === 'login' ? 'rgba(34, 197, 94, 0.1)' : ($activity['activity_type'] === 'logout' ? 'rgba(239, 68, 68, 0.1)' : 'rgba(84, 140, 168, 0.1)'); ?>; color: <?= $activity['activity_type'] === 'login' ? '#22c55e' : ($activity['activity_type'] === 'logout' ? '#ef4444' : '#548CA8'); ?>;">
                                                    <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $activity['activity_type']))) ?>
                                                </span>
                                            </td>
                                            <td style="max-width: 300px;"><?= htmlspecialchars($activity['activity_description']) ?></td>
                                            <td><code style="padding: 2px 6px; background: #f1f5f9; border-radius: 4px; font-size: 0.85rem;"><?= htmlspecialchars($activity['ip_address']) ?></code></td>
                                            <td>
                                                <div style="font-weight: 600;"><?= date('M d, Y', strtotime($activity['created_at'])) ?></div>
                                                <div style="color: #64748b; font-size: 0.85rem;"><?= date('H:i:s', strtotime($activity['created_at'])) ?></div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" style="text-align: center; padding: 40px; color: #64748b;">
                                            <i class="bi bi-inbox" style="font-size: 3rem; display: block; margin-bottom: 10px; color: #cbd5e1;"></i>
                                            No activities found.
                                        </td>
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

<style>
.badge-secondary { background: #6c757d !important; }
</style>
