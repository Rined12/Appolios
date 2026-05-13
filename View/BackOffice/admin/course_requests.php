<?php
/**
 * APPOLIOS - Admin Course Requests
 */
$adminSidebarActive = 'course-requests';
?>

<div class="dashboard">
    <div class="admin-page-container">
            
            <div class="admin-page-content">
                
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
                                            <a href="<?= APP_ENTRY ?>?url=admin/view-course/<?= $course['id'] ?>" style="flex: 1; background: #3b82f6; color: white; padding: 10px; border-radius: 10px; border: none; cursor: pointer; font-weight: 600; text-align: center; text-decoration: none;">View</a>
                                            <a href="<?= APP_ENTRY ?>?url=admin/approve-course/<?= $course['id'] ?>" style="flex: 1; background: #22c55e; color: white; padding: 10px; border-radius: 10px; border: none; cursor: pointer; font-weight: 600; text-align: center; text-decoration: none;">Approve</a>
                                            <a href="<?= APP_ENTRY ?>?url=admin/reject-course/<?= $course['id'] ?>" onclick="return confirm('Are you sure?')" style="flex: 1; background: #ef4444; color: white; padding: 10px; border-radius: 10px; border: none; cursor: pointer; font-weight: 600; text-align: center; text-decoration: none;">Reject</a>
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