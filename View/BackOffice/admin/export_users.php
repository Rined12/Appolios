<?php
/**
 * APPOLIOS - Integrated Users Export View
 */
?>

<div class="dashboard">
    <div class="container admin-dashboard-container" style="max-width: 1400px; width: 100%;">
        <div class="admin-layout">
            <?php $adminSidebarActive = 'users'; require __DIR__ . '/partials/sidebar.php'; ?>

            <div class="admin-main" style="background: transparent; padding: 1rem 0 2rem 0;">
                <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <h1>Users Report</h1>
                        <p>Detailed export of all platform users</p>
                    </div>
                    <div class="no-print" style="display: flex; gap: 10px;">
                        <button onclick="window.print()" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px;">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor">
                                <path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/>
                            </svg>
                            Print / Save as PDF
                        </button>
                        <a href="<?= APP_ENTRY ?>?url=admin/users" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; background: #6c757d;">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" style="transform: rotate(180deg);">
                                <path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/>
                            </svg>
                            Back to Users
                        </a>
                    </div>
                </div>

                <div class="report-content" style="background: white; border-radius: 16px; padding: 2rem; box-shadow: 0 4px 20px rgba(0,0,0,0.05); border: 1px solid #eef2f6;">
                    <div style="text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #548CA8;">
                        <h2 style="color: #2B4865; margin-bottom: 5px;">APPOLIOS - Users Management Report</h2>
                        <p style="color: #64748b; font-size: 0.9rem;">Complete list of registered platform users</p>
                    </div>

                    <div style="display: flex; justify-content: space-between; margin-bottom: 20px; color: #64748b; font-size: 0.9rem;">
                        <div><strong>Generated:</strong> <?= date('F d, Y H:i:s') ?></div>
                        <div><strong>Total Users:</strong> <?= count($users) ?></div>
                    </div>

                    <div class="table-responsive">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                                    <th style="padding: 12px; text-align: left; color: #475569; font-weight: 700;">ID</th>
                                    <th style="padding: 12px; text-align: left; color: #475569; font-weight: 700;">Full Name</th>
                                    <th style="padding: 12px; text-align: left; color: #475569; font-weight: 700;">Email Address</th>
                                    <th style="padding: 12px; text-align: left; color: #475569; font-weight: 700;">Role</th>
                                    <th style="padding: 12px; text-align: left; color: #475569; font-weight: 700;">Status</th>
                                    <th style="padding: 12px; text-align: left; color: #475569; font-weight: 700;">Registered</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr style="border-bottom: 1px solid #f1f5f9;">
                                        <td style="padding: 12px; color: #1e293b;"><?= htmlspecialchars($user['id']) ?></td>
                                        <td style="padding: 12px; color: #1e293b; font-weight: 600;"><?= htmlspecialchars($user['name']) ?></td>
                                        <td style="padding: 12px; color: #64748b;"><?= htmlspecialchars($user['email']) ?></td>
                                        <td style="padding: 12px;">
                                            <span style="padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; background: <?= $user['role'] === 'admin' ? '#E19864' : '#548CA8' ?>; color: white;">
                                                <?= ucfirst(htmlspecialchars($user['role'])) ?>
                                            </span>
                                        </td>
                                        <td style="padding: 12px;">
                                            <?php if ($user['is_blocked'] ?? 0): ?>
                                                <span style="padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; background: #dc3545; color: white;">Blocked</span>
                                            <?php else: ?>
                                                <span style="color: #22c55e; font-weight: 600;">Active</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="padding: 12px; color: #64748b;"><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div style="margin-top: 40px; text-align: center; font-size: 0.8rem; color: #94a3b8; border-top: 1px solid #f1f5f9; padding-top: 20px;">
                        <p>APPOLIOS E-Learning Platform - User Management Report</p>
                        <p>This document is confidential and intended for authorized personnel only.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    /* Hide everything except the report content */
    .neo-header, .app-footer, .admin-sidebar, .no-print, .dashboard-header {
        display: none !important;
    }
    
    .admin-main {
        padding: 0 !important;
        margin: 0 !important;
    }
    
    .report-content {
        box-shadow: none !important;
        border: none !important;
        padding: 0 !important;
    }
    
    .admin-layout {
        display: block !important;
    }
    
    body {
        background: white !important;
    }
}
</style>

<script>
    // Optional: auto-trigger print dialog after a short delay
    window.onload = function () {
        // Only if needed, but usually users prefer clicking the button
        // setTimeout(() => window.print(), 1000);
    };
</script>
