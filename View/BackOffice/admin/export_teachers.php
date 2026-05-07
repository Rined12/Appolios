<?php
/**
 * APPOLIOS - Integrated Teachers Export View
 */
?>

<div class="dashboard" style="background: #f8fafc; min-height: 100vh; padding: 2rem;">
    <div class="container" style="max-width: 1200px; margin: 0 auto;">
        <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem;">
            <div>
                <h1 style="font-size: 2rem; font-weight: 800; color: #1e293b; margin: 0;">Teachers Report</h1>
                <p style="color: #64748b; margin: 5px 0 0 0;">Detailed export of all platform teachers</p>
            </div>
                    <div class="no-print" style="display: flex; gap: 10px;">
                        <button onclick="window.print()" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px; background: #4338ca; color: white; border: none; padding: 10px 20px; border-radius: 10px; cursor: pointer;">
                            <i class="bi bi-printer-fill"></i> Print / Save as PDF
                        </button>
                        <a href="<?= APP_ENTRY ?>?url=admin/teachers" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; background: #64748b; color: white; text-decoration: none; padding: 10px 20px; border-radius: 10px;">
                            <i class="bi bi-arrow-left"></i> Back to Teachers
                        </a>
                    </div>
                </div>

                <div class="report-content" style="background: white; border-radius: 16px; padding: 2rem; box-shadow: 0 4px 20px rgba(0,0,0,0.05); border: 1px solid #eef2f6; margin-top: 2rem;">
                    <div style="text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #4338ca;">
                        <h2 style="color: #1e293b; margin-bottom: 5px;">APPOLIOS - Teachers Management Report</h2>
                        <p style="color: #64748b; font-size: 0.9rem;">Complete list of faculty members</p>
                    </div>

                    <div style="display: flex; justify-content: space-between; margin-bottom: 20px; color: #64748b; font-size: 0.9rem;">
                        <div><strong>Generated:</strong> <?= date('F d, Y H:i:s') ?></div>
                        <div><strong>Total Teachers:</strong> <?= count($teachers) ?></div>
                    </div>

                    <div class="table-responsive">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                                    <th style="padding: 12px; text-align: left; color: #475569; font-weight: 700;">ID</th>
                                    <th style="padding: 12px; text-align: left; color: #475569; font-weight: 700;">Teacher Name</th>
                                    <th style="padding: 12px; text-align: left; color: #475569; font-weight: 700;">Email Address</th>
                                    <th style="padding: 12px; text-align: left; color: #475569; font-weight: 700;">Joined Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($teachers as $teacher): ?>
                                    <tr style="border-bottom: 1px solid #f1f5f9;">
                                        <td style="padding: 12px; color: #1e293b;"><?= htmlspecialchars($teacher['id']) ?></td>
                                        <td style="padding: 12px; color: #1e293b; font-weight: 600;"><?= htmlspecialchars($teacher['name']) ?></td>
                                        <td style="padding: 12px; color: #64748b;"><?= htmlspecialchars($teacher['email']) ?></td>
                                        <td style="padding: 12px; color: #64748b;"><?= date('M d, Y', strtotime($teacher['created_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div style="margin-top: 40px; text-align: center; font-size: 0.8rem; color: #94a3b8; border-top: 1px solid #f1f5f9; padding-top: 20px;">
                        <p>APPOLIOS E-Learning Platform - Teacher Management Report</p>
                        <p>This document is confidential and intended for authorized personnel only.</p>
                    </div>
    </div>
</div>


<style>
@media print {
    .sidebar, .no-print, .dashboard-header, .admin-sidebar-pro, .bg-shape {
        display: none !important;
    }
    
    .admin-main, .admin-content-pro {
        padding: 0 !important;
        margin: 0 !important;
    }
    
    .report-content {
        box-shadow: none !important;
        border: none !important;
        padding: 0 !important;
        margin-top: 0 !important;
    }
    
    .admin-layout, .admin-page-container {
        display: block !important;
    }
    
    body {
        background: white !important;
    }
}
</style>
