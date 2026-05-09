<?php
/**
 * APPOLIOS - Admin Course Requests
 */
$adminSidebarActive = 'courses';
?>

<div class="dashboard">
    <div class="container admin-dashboard-container" style="max-width: 1400px; width: 100%;">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>

            <div class="admin-main" style="background: transparent; padding: 1rem 0 2rem 0; font-family: 'Inter', sans-serif;">
                
                <!-- Header -->
                <div style="background: white; border-radius: 20px; padding: 2rem; margin-bottom: 2rem; box-shadow: 0 5px 20px rgba(0,0,0,0.03); border: 1px solid #eef2f6;">
                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                        <div>
                            <h1 style="margin: 0 0 0.5rem 0; font-size: 1.8rem; color: #1e293b; font-weight: 800;">Course Requests</h1>
                            <p style="margin: 0; color: #64748b;">Review and approve or reject course submissions</p>
                        </div>
                        <a href="<?= APP_ENTRY ?>?url=admin/dashboard" style="background: #f1f5f9; color: #475569; text-decoration: none; padding: 10px 20px; border-radius: 10px; font-weight: 600; display: flex; align-items: center; gap: 8px; transition: all 0.2s;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                            Back to Dashboard
                        </a>
                    </div>
                </div>

                <!-- Filter Tabs -->
                <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem;">
                    <?php $allActive = ($filterStatus ?? 'all') === 'all'; ?>
                    <?php $pendingActive = ($filterStatus ?? 'all') === 'pending'; ?>
                    <?php $approvedActive = ($filterStatus ?? 'all') === 'approved'; ?>
                    <?php $rejectedActive = ($filterStatus ?? 'all') === 'rejected'; ?>
                    <a href="<?= APP_ENTRY ?>?url=admin/course-requests&status=all" style="padding: 10px 20px; background: <?= $allActive ? '#3b82f6' : 'white' ?>; color: <?= $allActive ? 'white' : '#475569' ?>; text-decoration: none; border-radius: 10px; font-weight: 600; border: 1px solid #e2e8f0;">All (<?= ($pendingCount ?? 0) + ($approvedCount ?? 0) + ($rejectedCount ?? 0) ?>)</a>
                    <a href="<?= APP_ENTRY ?>?url=admin/course-requests&status=pending" style="padding: 10px 20px; background: <?= $pendingActive ? '#3b82f6' : 'white' ?>; color: <?= $pendingActive ? 'white' : '#475569' ?>; text-decoration: none; border-radius: 10px; font-weight: 600; border: 1px solid #e2e8f0;">Pending (<?= $pendingCount ?? 0 ?>)</a>
                    <a href="<?= APP_ENTRY ?>?url=admin/course-requests&status=approved" style="padding: 10px 20px; background: <?= $approvedActive ? '#3b82f6' : 'white' ?>; color: <?= $approvedActive ? 'white' : '#475569' ?>; text-decoration: none; border-radius: 10px; font-weight: 600; border: 1px solid #e2e8f0;">Approved (<?= $approvedCount ?? 0 ?>)</a>
                    <a href="<?= APP_ENTRY ?>?url=admin/course-requests&status=rejected" style="padding: 10px 20px; background: <?= $rejectedActive ? '#3b82f6' : 'white' ?>; color: <?= $rejectedActive ? 'white' : '#475569' ?>; text-decoration: none; border-radius: 10px; font-weight: 600; border: 1px solid #e2e8f0;">Rejected (<?= $rejectedCount ?? 0 ?>)</a>
                </div>

                <!-- Course Cards -->
                <!-- Course Cards -->
                <?php if (!empty($courses)): ?>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 1.5rem;">
                        <?php foreach ($courses as $course): ?>
                            <div style="background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.03); border: 1px solid #eef2f6;">
                                <?php if (!empty($course['image'])): ?>
                                    <img src="<?= htmlspecialchars($course['image']) ?>" alt="<?= htmlspecialchars($course['title']) ?>" style="width: 100%; height: 160px; object-fit: cover;">
                                <?php else: ?>
                                    <?php $randomImgId = rand(1, 1000); ?>
                                    <img src="https://picsum.photos/seed/<?= $randomImgId ?>/400/160" alt="<?= htmlspecialchars($course['title']) ?>" style="width: 100%; height: 160px; object-fit: cover;">
                                <?php endif; ?>
                                
                                <div style="padding: 1.5rem;">
                                    <?php 
                                    $courseStatus = $course['status'] ?? 'pending';
                                    $bgColor = $courseStatus === 'approved' ? '#dcfce7' : ($courseStatus === 'rejected' ? '#fee2e2' : '#fef3c7');
                                    $textColor = $courseStatus === 'approved' ? '#16a34a' : ($courseStatus === 'rejected' ? '#dc2626' : '#d97706');
                                    ?>
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
                                        <span style="background: <?= $bgColor ?>; color: <?= $textColor ?>; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">
                                            <?= htmlspecialchars(ucfirst($course['status'] ?? 'pending')) ?>
                                        </span>
                                        <span style="color: #64748b; font-size: 0.85rem;"><?= htmlspecialchars($course['creator_name'] ?? 'Unknown') ?></span>
                                    </div>
                                    
                                    <h3 style="margin: 0 0 0.5rem 0; font-size: 1.2rem; color: #1e293b; font-weight: 700;"><?= htmlspecialchars($course['title']) ?></h3>
                                    <?php if (!empty($course['course_type'])): ?>
                                        <span style="background: #dbeafe; color: #1e40af; padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.5rem; display: inline-block;">
                                            <?= htmlspecialchars($course['course_type']) ?>
                                        </span>
                                    <?php endif; ?>
                                    <p style="margin: 0 0 1rem 0; color: #64748b; font-size: 0.9rem; line-height: 1.5;"><?= htmlspecialchars(substr($course['description'] ?? '', 0, 100)) ?>...</p>
                                    
                                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 1rem;">
                                        <span style="background: #f1f5f9; color: #475569; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 600;"><?= $course['chapters_count'] ?? 0 ?> Chapters</span>
                                        <span style="background: #f1f5f9; color: #475569; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 600;"><?= $course['lessons_count'] ?? 0 ?> Lessons</span>
                                    </div>
                                    
                                    <?php if (($course['status'] ?? 'pending') === 'pending'): ?>
                                        <div style="display: flex; gap: 0.5rem;">
                                            <button type="button" onclick="openCourseModal(<?= $course['id'] ?>)" style="flex: 1; background: #3b82f6; color: white; padding: 10px; border-radius: 10px; border: none; cursor: pointer; font-weight: 600;">View</button>
                                            <button type="button" onclick="openApprovalModal(<?= $course['id'] ?>, 'approve')" style="flex: 1; background: #22c55e; color: white; padding: 10px; border-radius: 10px; border: none; cursor: pointer; font-weight: 600;">Approve</button>
                                            <button type="button" onclick="openApprovalModal(<?= $course['id'] ?>, 'reject')" style="flex: 1; background: #ef4444; color: white; padding: 10px; border-radius: 10px; border: none; cursor: pointer; font-weight: 600;">Reject</button>
                                        </div>
                                    <?php else: ?>
                                        <?php if (!empty($course['admin_message'])): ?>
                                            <div style="background: #f8fafc; padding: 0.75rem; border-radius: 8px; font-size: 0.85rem;">
                                                <strong style="color: #475569;">Admin Response:</strong>
                                                <p style="margin: 0.25rem 0 0; color: #1e293b;"><?= htmlspecialchars($course['admin_message']) ?></p>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div style="background: white; border-radius: 16px; padding: 3rem; text-align: center; box-shadow: 0 5px 20px rgba(0,0,0,0.03); border: 1px solid #eef2f6;">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 1rem;"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                        <h3 style="margin: 0 0 0.5rem; color: #475569;">No courses found</h3>
                        <p style="margin: 0; color: #94a3b8;">There are no courses in this category.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- View Course Modal -->
<div id="courseModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 20px; width: 90%; max-width: 800px; max-height: 90vh; overflow-y: auto; margin: 2rem;">
        <div style="padding: 1.5rem 2rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
            <h2 style="margin: 0; color: #1e293b; font-size: 1.5rem;">Course Details</h2>
            <button type="button" onclick="closeCourseModal()" style="background: none; border: none; cursor: pointer; padding: 0.5rem;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>
        <div id="courseModalContent" style="padding: 2rem;">
            <!-- Content loaded via AJAX -->
        </div>
    </div>
</div>

<!-- Approval Modal -->
<div id="approvalModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 20px; width: 90%; max-width: 500px;">
        <div style="padding: 1.5rem 2rem; border-bottom: 1px solid #e2e8f0;">
            <h2 id="approvalModalTitle" style="margin: 0; color: #1e293b; font-size: 1.5rem;">Approve Course</h2>
        </div>
        <form id="approvalForm" method="POST" style="padding: 2rem;" onsubmit="submitApproval(event)">
            <input type="hidden" name="course_id" id="approvalCourseId">
            <input type="hidden" name="action" id="approvalAction">
            
            <div class="form-group">
                <label>Message (optional)</label>
                <textarea name="admin_message" rows="4" placeholder="Add a message for the teacher..." style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 10px; font-family: inherit; resize: vertical;"></textarea>
            </div>
            
            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                <button type="button" onclick="closeApprovalModal()" style="background: #f1f5f9; color: #475569; padding: 10px 20px; border-radius: 10px; border: none; cursor: pointer; font-weight: 600;">Cancel</button>
                <button type="submit" id="approvalSubmitBtn" style="background: #22c55e; color: white; padding: 10px 20px; border-radius: 10px; border: none; cursor: pointer; font-weight: 600;">Confirm</button>
            </div>
        </form>
    </div>
</div>

<script>
function openCourseModal(courseId) {
    document.getElementById('courseModalContent').innerHTML = '<p style="text-align: center; color: #64748b;">Loading course details...</p>';
    document.getElementById('courseModal').style.display = 'flex';

    fetch('<?= APP_ENTRY ?>?url=admin/get-course-content/' + courseId)
    .then(r => r.text())
    .then(html => {
        document.getElementById('courseModalContent').innerHTML = html;
    })
    .catch(err => {
        document.getElementById('courseModalContent').innerHTML = '<p style="color: #ef4444;">Error loading course</p>';
    });
}

function closeCourseModal() {
    document.getElementById('courseModal').style.display = 'none';
}

function openApprovalModal(courseId, action) {
    document.getElementById('approvalCourseId').value = courseId;
    document.getElementById('approvalAction').value = action;
    document.getElementById('approvalModalTitle').textContent = action === 'approve' ? 'Approve Course' : 'Reject Course';
    document.getElementById('approvalSubmitBtn').style.background = action === 'approve' ? '#22c55e' : '#ef4444';
    document.getElementById('approvalModal').style.display = 'flex';
}

function submitApproval(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    const submitBtn = document.getElementById('approvalSubmitBtn');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Processing...';
    submitBtn.disabled = true;
    
    fetch('<?= APP_ENTRY ?>?url=admin/approve-course', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
        
        if (data.success) {
            closeApprovalModal();
            location.reload();
        } else {
            alert(data.message || 'Failed to process course');
        }
    })
    .catch(error => {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
        alert('Error: ' + error.message);
    });
}

function closeApprovalModal() {
    document.getElementById('approvalModal').style.display = 'none';
}

// Close modals on background click
document.getElementById('courseModal').addEventListener('click', function(e) {
    if (e.target === this) closeCourseModal();
});
document.getElementById('approvalModal').addEventListener('click', function(e) {
    if (e.target === this) closeApprovalModal();
});
</script>