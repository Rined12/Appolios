<?php
/**
 * APPOLIOS - Admin Teacher Applications Management
 */
?>

<div class="dashboard">
    <div class="container admin-dashboard-container" style="max-width: 1400px; width: 100%;">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>

            <div class="admin-main" style="background: transparent; padding: 1rem 0 2rem 0;">
                <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <h1>Teacher Applications</h1>
                        <p>Review and approve teacher registration requests</p>
                    </div>
                    <a href="javascript:history.back()" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; background: #6c757d;">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" style="transform: rotate(180deg);">
                            <path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/>
                        </svg>
                        Back
                    </a>
                </div>


        <!-- Stats Cards -->
        <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
            <div class="stat-card" style="background: linear-gradient(135deg, rgba(84, 140, 168, 0.1) 0%, rgba(84, 140, 168, 0.05) 100%); border: 1px solid rgba(84, 140, 168, 0.2); border-radius: 12px; padding: 20px;">
                <div style="font-size: 2rem; font-weight: 700; color: #548CA8;"><?= $pendingCount ?? 0 ?></div>
                <div style="color: #64748b; font-size: 0.9rem;">Pending Applications</div>
            </div>
        </div>

        <!-- Applications Table -->
        <div class="table-container">
            <div class="table-header">
                <h3 style="margin: 0;">Pending Teacher Applications</h3>
            </div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>CV File</th>
                            <th>Submitted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($applications)): ?>
                            <?php foreach ($applications as $app): ?>
                                <tr>
                                    <td><?= htmlspecialchars($app['id']) ?></td>
                                    <td><?= htmlspecialchars($app['name']) ?></td>
                                    <td><?= htmlspecialchars($app['email']) ?></td>
                                    <td>
                                        <a href="<?= APP_URL ?>/<?= htmlspecialchars($app['cv_path']) ?>" target="_blank" class="btn action-btn" style="padding: 5px 10px; font-size: 0.8rem; background: #548CA8; color: white;">
                                            <i class="bi bi-file-earmark-pdf"></i> View CV
                                        </a>
                                    </td>
                                    <td><?= date('M d, Y H:i', strtotime($app['created_at'])) ?></td>
                                    <td>
                                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                            <button type="button" class="btn action-btn success" style="padding: 5px 12px; font-size: 0.8rem;" onclick="openApproveModal(<?= $app['id'] ?>, '<?= htmlspecialchars($app['name']) ?>')">
                                                <i class="bi bi-check-lg"></i> Approve
                                            </button>
                                            <button type="button" class="btn action-btn danger" style="padding: 5px 12px; font-size: 0.8rem;" onclick="openRejectModal(<?= $app['id'] ?>, '<?= htmlspecialchars($app['name']) ?>')">
                                                <i class="bi bi-x-lg"></i> Reject
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 40px; color: #64748b;">
                                    <i class="bi bi-inbox" style="font-size: 3rem; display: block; margin-bottom: 10px; color: #cbd5e1;"></i>
                                    No pending applications found.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div id="approveModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="modal-content" style="background: white; border-radius: 16px; padding: 30px; max-width: 500px; width: 90%; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
        <h3 style="margin-top: 0; color: #0f172a;">Approve Application</h3>
        <p style="color: #64748b;">Are you sure you want to approve <strong id="approveTeacherName"></strong> as a teacher?</p>
        
        <form action="<?= APP_ENTRY ?>?url=admin/approve-teacher" method="POST">
            <input type="hidden" id="approveAppId" name="application_id">
            
            <div class="neo-field" style="margin-bottom: 20px;">
                <label for="approve_notes">Admin Notes (optional)</label>
                <textarea id="approve_notes" name="admin_notes" rows="3" style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; resize: vertical;"></textarea>
            </div>
            
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" class="btn" style="padding: 10px 20px; background: #e2e8f0; color: #475569; border: none; border-radius: 8px; cursor: pointer;" onclick="closeModal('approveModal')">Cancel</button>
                <button type="submit" class="btn" style="padding: 10px 20px; background: linear-gradient(135deg, #548CA8 0%, #355C7D 100%); color: white; border: none; border-radius: 8px; cursor: pointer;">
                    <i class="bi bi-check-lg"></i> Approve
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="modal-content" style="background: white; border-radius: 16px; padding: 30px; max-width: 500px; width: 90%; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
        <h3 style="margin-top: 0; color: #dc3545;">Reject Application</h3>
        <p style="color: #64748b;">Are you sure you want to reject <strong id="rejectTeacherName"></strong>'s application?</p>
        
        <form action="<?= APP_ENTRY ?>?url=admin/reject-teacher" method="POST">
            <input type="hidden" id="rejectAppId" name="application_id">
            
            <div class="neo-field" style="margin-bottom: 20px;">
                <label for="reject_notes">Rejection Reason (required)</label>
                <textarea id="reject_notes" name="admin_notes" rows="3" required style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; resize: vertical;"></textarea>
            </div>
            
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" class="btn" style="padding: 10px 20px; background: #e2e8f0; color: #475569; border: none; border-radius: 8px; cursor: pointer;" onclick="closeModal('rejectModal')">Cancel</button>
                <button type="submit" class="btn" style="padding: 10px 20px; background: #dc3545; color: white; border: none; border-radius: 8px; cursor: pointer;">
                    <i class="bi bi-x-lg"></i> Reject
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openApproveModal(appId, teacherName) {
    document.getElementById('approveAppId').value = appId;
    document.getElementById('approveTeacherName').textContent = teacherName;
    document.getElementById('approveModal').style.display = 'flex';
}

function openRejectModal(appId, teacherName) {
    document.getElementById('rejectAppId').value = appId;
    document.getElementById('rejectTeacherName').textContent = teacherName;
    document.getElementById('rejectModal').style.display = 'flex';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}
</script>
            </div>
        </div>
    </div>
</div>
