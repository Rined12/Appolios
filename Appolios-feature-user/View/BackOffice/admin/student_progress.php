<?php
/**
 * APPOLIOS - Admin Student Progress Page
 */

$adminSidebarActive = 'users';
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
                            <h1 style="margin: 0 0 0.5rem 0; font-size: 1.8rem; color: #1e293b; font-weight: 800;">Student Progress</h1>
                            <p style="margin: 0; color: #64748b;">Track all student enrollments and progress across courses</p>
                        </div>
                    </div>
                </div>

                <!-- Search & Filter -->
                <div style="background: white; border-radius: 16px; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 2px 10px rgba(0,0,0,0.03);">
                    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                        <input type="text" id="searchInput" placeholder="Search by student name..." style="flex: 1; min-width: 200px; padding: 12px 16px; border: 1px solid #e2e8f0; border-radius: 8px; outline: none;" onkeyup="filterTable()">
                        <select id="courseFilter" style="padding: 12px 16px; border: 1px solid #e2e8f0; border-radius: 8px; outline: none; min-width: 200px;" onchange="filterTable()">
                            <option value="">All Courses</option>
                            <?php if (!empty($courses)): ?>
                                <?php foreach ($courses as $c): ?>
                                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['title']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <!-- Student Progress Table -->
                <div style="background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.03);">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f8fafc;">
                                <th style="padding: 1rem; text-align: left; font-weight: 600; color: #64748b; font-size: 0.85rem;">Student</th>
                                <th style="padding: 1rem; text-align: left; font-weight: 600; color: #64748b; font-size: 0.85rem;">Course</th>
                                <th style="padding: 1rem; text-align: center; font-weight: 600; color: #64748b; font-size: 0.85rem;">Progress</th>
                                <th style="padding: 1rem; text-align: center; font-weight: 600; color: #64748b; font-size: 0.85rem;">Lessons Completed</th>
                                <th style="padding: 1rem; text-align: center; font-weight: 600; color: #64748b; font-size: 0.85rem;">Status</th>
                                <th style="padding: 1rem; text-align: center; font-weight: 600; color: #64748b; font-size: 0.85rem;">Enrolled</th>
                            </tr>
                        </thead>
                        <tbody id="progressTable">
                            <?php if (!empty($enrollments)): ?>
                                <?php foreach ($enrollments as $enrollment): ?>
                                    <tr class="progress-row" data-name="<?= strtolower($enrollment['student_name'] ?? '') ?>" data-course="<?= $enrollment['course_id'] ?>">
                                        <td style="padding: 1rem; border-bottom: 1px solid #e5e7eb;">
                                            <div style="display: flex; align-items: center; gap: 12px;">
                                                <div style="width: 40px; height: 40px; background: #3b82f6; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                                                    <?= strtoupper(substr($enrollment['student_name'] ?? 'S', 0, 1)) ?>
                                                </div>
                                                <div>
                                                    <p style="margin: 0; font-weight: 600; color: #1e293b;"><?= htmlspecialchars($enrollment['student_name'] ?? 'Unknown') ?></p>
                                                    <p style="margin: 0; font-size: 0.8rem; color: #64748b;"><?= htmlspecialchars($enrollment['email'] ?? '') ?></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="padding: 1rem; border-bottom: 1px solid #e5e7eb;">
                                            <p style="margin: 0; font-weight: 500; color: #1e293b;"><?= htmlspecialchars($enrollment['course_title'] ?? 'Unknown') ?></p>
                                        </td>
                                        <td style="padding: 1rem; border-bottom: 1px solid #e5e7eb; text-align: center;">
                                            <div style="display: inline-flex; align-items: center; gap: 8px;">
                                                <div style="width: 100px; background: #e2e8f0; border-radius: 99px; height: 8px; overflow: hidden;">
                                                    <div style="height: 100%; background: <?= ($enrollment['progress'] ?? 0) >= 100 ? '#10b981' : '#3b82f6' ?>; width: <?= (int) ($enrollment['progress'] ?? 0) ?>%;"></div>
                                                </div>
                                                <span style="font-weight: 600; font-size: 0.9rem;"><?= (int) ($enrollment['progress'] ?? 0) ?>%</span>
                                            </div>
                                        </td>
                                        <td style="padding: 1rem; border-bottom: 1px solid #e5e7eb; text-align: center;">
                                            <?= (int) ($enrollment['completed_lessons'] ?? 0) ?> / <?= (int) ($enrollment['total_lessons'] ?? 0) ?>
                                        </td>
                                        <td style="padding: 1rem; border-bottom: 1px solid #e5e7eb; text-align: center;">
                                            <?php if (($enrollment['progress'] ?? 0) >= 100): ?>
                                                <span style="background: #dcfce7; color: #166534; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">Completed</span>
                                            <?php elseif (($enrollment['progress'] ?? 0) > 0): ?>
                                                <span style="background: #dbeafe; color: #1e40af; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">In Progress</span>
                                            <?php else: ?>
                                                <span style="background: #f1f5f9; color: #64748b; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">Not Started</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="padding: 1rem; border-bottom: 1px solid #e5e7eb; text-align: center; color: #64748b; font-size: 0.9rem;">
                                            <?= date('M d, Y', strtotime($enrollment['enrolled_at'] ?? $enrollment['created_at'])) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="padding: 3rem; text-align: center; color: #64748b;">
                                        No enrollments found.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Summary Stats -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-top: 2rem;">
                    <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 2px 10px rgba(0,0,0,0.05); text-align: center;">
                        <div style="font-size: 2rem; font-weight: 700; color: #3b82f6;"><?= count($enrollments ?? []) ?></div>
                        <div style="color: #64748b; font-size: 0.9rem;">Total Enrollments</div>
                    </div>
                    <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 2px 10px rgba(0,0,0,0.05); text-align: center;">
                        <div style="font-size: 2rem; font-weight: 700; color: #10b981;">
                            <?= count(array_filter($enrollments ?? [], fn($e) => ($e['progress'] ?? 0) >= 100)) ?>
                        </div>
                        <div style="color: #64748b; font-size: 0.9rem;">Completed</div>
                    </div>
                    <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 2px 10px rgba(0,0,0,0.05); text-align: center;">
                        <div style="font-size: 2rem; font-weight: 700; color: #f59e0b;">
                            <?= count(array_filter($enrollments ?? [], fn($e) => ($e['progress'] ?? 0) > 0 && ($e['progress'] ?? 0) < 100)) ?>
                        </div>
                        <div style="color: #64748b; font-size: 0.9rem;">In Progress</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function filterTable() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const courseFilter = document.getElementById('courseFilter').value;
    const rows = document.querySelectorAll('.progress-row');
    
    rows.forEach(row => {
        const name = row.getAttribute('data-name');
        const course = row.getAttribute('data-course');
        
        const matchesSearch = name.includes(search);
        const matchesCourse = !courseFilter || course === courseFilter;
        
        row.style.display = (matchesSearch && matchesCourse) ? '' : 'none';
    });
}
</script>