<?php
/**
 * APPOLIOS - Admin Manage Courses
 */
$adminSidebarActive = 'manage-courses';
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
                            <h1 style="margin: 0 0 0.5rem 0; font-size: 1.8rem; color: #1e293b; font-weight: 800;">Manage Courses</h1>
                            <p style="margin: 0; color: #64748b;">Edit or delete courses from the platform</p>
                        </div>
                        <a href="<?= APP_ENTRY ?>?url=admin/dashboard" style="background: #f1f5f9; color: #475569; text-decoration: none; padding: 10px 20px; border-radius: 10px; font-weight: 600; display: flex; align-items: center; gap: 8px; transition: all 0.2s;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                            Back to Dashboard
                        </a>
                    </div>
                </div>
                
                <!-- Statistics Panel with Circular Charts -->
                <div style="display: flex; flex-wrap: wrap; gap: 1.5rem; margin-bottom: 2rem;">
                    <?php $total = $totalCourses ?? 0; $pending = $pendingCount ?? 0; $approved = $approvedCount ?? 0; $rejected = $rejectedCount ?? 0; $maxVal = max(1, $total, $pending, $approved, $rejected); $radius = 30; $circumference = 2 * 3.14159 * $radius; $earnings = $totalEarnings ?? 0; ?>
                    
                    <div class="stat-card" style="flex: 1; min-width: 150px; background: linear-gradient(135deg, #f8fafc, #e2e8f0); border: 1px solid #cbd5e1; border-radius: 16px; padding: 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.05); text-align: center; transition: all 0.3s ease; cursor: pointer;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 15px rgba(0,0,0,0.1)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px rgba(0,0,0,0.05)'">
                        <svg width="80" height="80" viewBox="0 0 100 100" style="margin: 0 auto;">
                            <circle cx="50" cy="50" r="<?= $radius ?>" fill="none" stroke="#e5e7eb" stroke-width="8"/>
                            <circle cx="50" cy="50" r="<?= $radius ?>" fill="none" stroke="#1e293b" stroke-width="8"
                                    stroke-linecap="round" stroke-dasharray="<?= ($total / $maxVal) * $circumference ?> <?= $circumference ?>" 
                                    stroke-dashoffset="0" transform="rotate(-90 50 50)"
                                    style="transition: stroke-dasharray 0.8s ease;">
                                    <animate attributeName="stroke-dasharray" from="0 <?= $circumference ?>" to="<?= ($total / $maxVal) * $circumference ?> <?= $circumference ?>" dur="0.8s" fill="freeze"/>
                            </circle>
                        </svg>
                        <h4 style="margin: 0.5rem 0 0; font-size: 1.1rem; color: #1e293b; font-weight: 700;"><?= number_format($total) ?></h4>
                        <p style="margin: 0; font-size: 0.85rem; color: #64748b;">Total Courses</p>
                    </div>

                    <div class="stat-card" style="flex: 1; min-width: 150px; background: linear-gradient(135deg, #ecfdf5, #d1fae5); border: 1px solid #34d399; border-radius: 16px; padding: 1rem; box-shadow: 0 4px 6px rgba(16,185,129,0.1); text-align: center; transition: all 0.3s ease; cursor: pointer;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 15px rgba(16,185,129,0.2)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px rgba(16,185,129,0.1)'">
                        <svg width="80" height="80" viewBox="0 0 100 100" style="margin: 0 auto;">
                            <circle cx="50" cy="50" r="<?= $radius ?>" fill="none" stroke="#d1fae5" stroke-width="8"/>
                            <circle cx="50" cy="50" r="<?= $radius ?>" fill="none" stroke="#059669" stroke-width="8"
                                    stroke-linecap="round" stroke-dasharray="<?= min($circumference, ($earnings > 0 ? 80 : 0)) ?> <?= $circumference ?>" 
                                    stroke-dashoffset="0" transform="rotate(-90 50 50)"
                                    style="transition: stroke-dasharray 0.8s ease;">
                                    <animate attributeName="stroke-dasharray" from="0 <?= $circumference ?>" to="<?= min($circumference, ($earnings > 0 ? 80 : 0)) ?> <?= $circumference ?>" dur="0.8s" fill="freeze"/>
                            </circle>
                        </svg>
                        <h4 style="margin: 0.5rem 0 0; font-size: 1.1rem; color: #065f46; font-weight: 700;">$<?= number_format($earnings, 2) ?></h4>
                        <p style="margin: 0; font-size: 0.85rem; color: #047857;">Total Earnings</p>
                    </div>

                    <div class="stat-card" style="flex: 1; min-width: 150px; background: linear-gradient(135deg, #fffbeb, #fef3c7); border: 1px solid #fcd34d; border-radius: 16px; padding: 1rem; box-shadow: 0 4px 6px rgba(217,119,6,0.1); text-align: center; transition: all 0.3s ease; cursor: pointer;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 15px rgba(217,119,6,0.2)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px rgba(217,119,6,0.1)'">
                        <svg width="80" height="80" viewBox="0 0 100 100" style="margin: 0 auto;">
                            <circle cx="50" cy="50" r="<?= $radius ?>" fill="none" stroke="#fef3c7" stroke-width="8"/>
                            <circle cx="50" cy="50" r="<?= $radius ?>" fill="none" stroke="#d97706" stroke-width="8"
                                    stroke-linecap="round" stroke-dasharray="<?= ($pending / $maxVal) * $circumference ?> <?= $circumference ?>" 
                                    stroke-dashoffset="0" transform="rotate(-90 50 50)"
                                    style="transition: stroke-dasharray 0.8s ease;">
                                    <animate attributeName="stroke-dasharray" from="0 <?= $circumference ?>" to="<?= ($pending / $maxVal) * $circumference ?> <?= $circumference ?>" dur="0.8s" fill="freeze"/>
                            </circle>
                        </svg>
                        <h4 style="margin: 0.5rem 0 0; font-size: 1.1rem; color: #b45309; font-weight: 700;"><?= number_format($pending) ?></h4>
                        <p style="margin: 0; font-size: 0.85rem; color: #92400e;">Pending</p>
                    </div>

                    <div class="stat-card" style="flex: 1; min-width: 150px; background: linear-gradient(135deg, #ecfdf5, #d1fae5); border: 1px solid #34d399; border-radius: 16px; padding: 1rem; box-shadow: 0 4px 6px rgba(16,185,129,0.1); text-align: center; transition: all 0.3s ease; cursor: pointer;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 15px rgba(16,185,129,0.2)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px rgba(16,185,129,0.1)'">
                        <svg width="80" height="80" viewBox="0 0 100 100" style="margin: 0 auto;">
                            <circle cx="50" cy="50" r="<?= $radius ?>" fill="none" stroke="#d1fae5" stroke-width="8"/>
                            <circle cx="50" cy="50" r="<?= $radius ?>" fill="none" stroke="#059669" stroke-width="8"
                                    stroke-linecap="round" stroke-dasharray="<?= ($approved / $maxVal) * $circumference ?> <?= $circumference ?>" 
                                    stroke-dashoffset="0" transform="rotate(-90 50 50)"
                                    style="transition: stroke-dasharray 0.8s ease;">
                                    <animate attributeName="stroke-dasharray" from="0 <?= $circumference ?>" to="<?= ($approved / $maxVal) * $circumference ?> <?= $circumference ?>" dur="0.8s" fill="freeze"/>
                            </circle>
                        </svg>
                        <h4 style="margin: 0.5rem 0 0; font-size: 1.1rem; color: #065f46; font-weight: 700;"><?= number_format($approved) ?></h4>
                        <p style="margin: 0; font-size: 0.85rem; color: #047857;">Approved</p>
                    </div>

                    <div class="stat-card" style="flex: 1; min-width: 150px; background: linear-gradient(135deg, #fef2f2, #fecaca); border: 1px solid #f87171; border-radius: 16px; padding: 1rem; box-shadow: 0 4px 6px rgba(220,38,38,0.1); text-align: center; transition: all 0.3s ease; cursor: pointer;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 15px rgba(220,38,38,0.2)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px rgba(220,38,38,0.1)'">
                        <svg width="80" height="80" viewBox="0 0 100 100" style="margin: 0 auto;">
                            <circle cx="50" cy="50" r="<?= $radius ?>" fill="none" stroke="#fca5a5" stroke-width="8"/>
                            <circle cx="50" cy="50" r="<?= $radius ?>" fill="none" stroke="#dc2626" stroke-width="8"
                                    stroke-linecap="round" stroke-dasharray="<?= ($rejected / $maxVal) * $circumference ?> <?= $circumference ?>" 
                                    stroke-dashoffset="0" transform="rotate(-90 50 50)"
                                    style="transition: stroke-dasharray 0.8s ease;">
                                    <animate attributeName="stroke-dasharray" from="0 <?= $circumference ?>" to="<?= ($rejected / $maxVal) * $circumference ?> <?= $circumference ?>" dur="0.8s" fill="freeze"/>
                            </circle>
                        </svg>
                        <h4 style="margin: 0.5rem 0 0; font-size: 1.1rem; color: #991b1b; font-weight: 700;"><?= number_format($rejected) ?></h4>
                        <p style="margin: 0; font-size: 0.85rem; color: #b91c1c;">Rejected</p>
                    </div>
                </div>
                <!-- Search Bar -->
                <div style="background: white; border-radius: 20px; padding: 1.5rem; margin-bottom: 2rem; box-shadow: 0 5px 20px rgba(0,0,0,0.03); border: 1px solid #eef2f6;">
                    <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                        <div style="flex: 1; min-width: 300px; position: relative;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%);">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="M21 21l-4.35-4.35"></path>
                            </svg>
                            <input type="text" id="searchInput" placeholder="Search courses by name, type, or creator..." 
                                   style="width: 100%; padding: 14px 14px 14px 45px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 1rem; transition: all 0.3s; background: #f8fafc;" />
                        </div>
                        <div style="position: relative;">
                            <select id="searchBy" style="padding: 14px 40px 14px 16px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 1rem; background: white; cursor: pointer; min-width: 150px; appearance: none; -webkit-appearance: none; -moz-appearance: none; transition: all 0.3s; background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"12\" height=\"12\" viewBox=\"0 0 12 12\"><path fill=\"%2394a3b8\" d=\"M6 9L1 4h10z\"/></svg>'); background-repeat: no-repeat; background-position: right 15px center;"
                                    onchange="filterCourses()">
                                <option value="all">All Fields</option>
                                <option value="name">Course Name</option>
                                <option value="type">Course Type</option>
                                <option value="creator">Creator</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Course Cards -->
                <?php if (!empty($courses)): ?>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 1.5rem;" id="coursesContainer">
                        <?php foreach ($courses as $course): ?>
                            <div style="background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.03); border: 1px solid #eef2f6;" class="course-card">
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
                                        <span style="color: #64748b; font-size: 0.85rem;" class="creator-name"><?= htmlspecialchars($course['creator_name'] ?? 'Unknown') ?></span>
                                    </div>
                                    
                                    <h3 style="margin: 0 0 0.5rem 0; font-size: 1.2rem; color: #1e293b; font-weight: 700;"><?= htmlspecialchars($course['title']) ?></h3>
                                    
                                    <?php if (!empty($course['course_type'])): ?>
                                        <span style="background: #dbeafe; color: #1e40af; padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.5rem; display: inline-block;" class="course-type-badge">
                                            <?= htmlspecialchars($course['course_type']) ?>
                                        </span>
                                    <?php endif; ?>
                                    
                                    <p style="margin: 0 0 1rem 0; color: #64748b; font-size: 0.9rem; line-height: 1.5;"><?= htmlspecialchars(substr($course['description'] ?? '', 0, 100)) ?>...</p>
                                    
                                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 1rem;">
                                        <span style="background: #f1f5f9; color: #475569; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 600;"><?= $course['chapters_count'] ?? 0 ?> Chapters</span>
                                        <span style="background: #f1f5f9; color: #475569; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 600;"><?= $course['lessons_count'] ?? 0 ?> Lessons</span>
                                        <span style="background: <?= ($course['price'] ?? 0) > 0 ? '#fef3c7' : '#dcfce7'; ?>; color: <?= ($course['price'] ?? 0) > 0 ? '#d97706' : '#16a34a'; ?>; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 600;">
                                            <?= ($course['price'] ?? 0) > 0 ? '$' . number_format($course['price'], 2) : 'Free' ?>
                                        </span>
                                    </div>
                                    
                                    <div style="display: flex; gap: 0.5rem;">
                                        <a href="<?= APP_ENTRY ?>?url=admin/view-course/<?= $course['id'] ?>" style="flex: 1; background: #10b981; color: white; padding: 10px; border-radius: 10px; border: none; cursor: pointer; font-weight: 600; text-align: center; text-decoration: none;">View</a>
                                        <?php if (($course['created_by'] ?? 0) == ($_SESSION['user_id'] ?? 0)): ?>
                                            <a href="<?= APP_ENTRY ?>?url=admin/edit-course/<?= $course['id'] ?>" style="flex: 1; background: #3b82f6; color: white; padding: 10px; border-radius: 10px; border: none; cursor: pointer; font-weight: 600; text-align: center; text-decoration: none;">Edit</a>
                                            <a href="<?= APP_ENTRY ?>?url=admin/delete-course/<?= $course['id'] ?>" style="flex: 1; background: #ef4444; color: white; padding: 10px; border-radius: 10px; border: none; cursor: pointer; font-weight: 600; text-align: center; text-decoration: none;" onclick="return confirm('Are you sure you want to delete this course?')">Delete</a>
                                        <?php else: ?>
                                            <span style="flex: 2; color: #94a3b8; font-size: 0.85rem; padding: 10px; text-align: center;">Not your course</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div style="background: white; border-radius: 16px; padding: 3rem; text-align: center; box-shadow: 0 5px 20px rgba(0,0,0,0.03); border: 1px solid #eef2f6;" id="noResults">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 1rem;"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                        <h3 style="margin: 0 0 0.5rem; color: #475569;">No courses found</h3>
                        <p style="margin: 0; color: #94a3b8;">No courses match your search.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    'use strict';
    var searchInput = document.getElementById('searchInput');
    var searchBy = document.getElementById('searchBy');
    
    if (!searchInput || !searchBy) {
        console.error('Search elements not found');
        return;
    }
    
    function filterCourses() {
        var term = searchInput.value.toLowerCase().trim();
        var field = searchBy.value;
        var cards = document.querySelectorAll('.course-card');
        var visible = 0;
        
        for (var i = 0; i < cards.length; i++) {
            var card = cards[i];
            var show = false;
            
            if (term === '') {
                show = true;
            } else if (field === 'all') {
                show = card.textContent.toLowerCase().indexOf(term) !== -1;
            } else if (field === 'name') {
                var titleEl = card.querySelector('h3');
                show = titleEl && titleEl.textContent.toLowerCase().indexOf(term) !== -1;
            } else if (field === 'type') {
                var typeBadge = card.querySelector('.course-type-badge');
                show = typeBadge && typeBadge.textContent.toLowerCase().indexOf(term) !== -1;
            } else if (field === 'creator') {
                var creatorEl = card.querySelector('.creator-name');
                show = creatorEl && creatorEl.textContent.toLowerCase().indexOf(term) !== -1;
            }
            
            card.style.display = show ? '' : 'none';
            if (show) visible++;
        }
        
        var noResults = document.getElementById('noResults');
        var container = document.getElementById('coursesContainer');
        
        if (visible === 0 && cards.length > 0) {
            if (!noResults && container) {
                noResults = document.createElement('div');
                noResults.id = 'noResults';
                noResults.innerHTML = '<div style="background: white; border-radius: 16px; padding: 3rem; text-align: center;"><h3 style="margin: 0 0 0.5rem; color: #475569;">No courses found</h3><p style="margin: 0; color: #94a3b8;">No courses match your search.</p></div>';
                container.appendChild(noResults);
            }
        } else if (noResults) {
            noResults.remove();
        }
    }
    
    searchInput.addEventListener('input', filterCourses);
    searchBy.addEventListener('change', filterCourses);
})();
</script>
