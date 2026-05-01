<?php
/**
 * APPOLIOS - Teacher Dashboard
 */

$teacherSidebarActive = 'courses';
?>

<div class="dashboard">
    <div class="container admin-dashboard-container" style="max-width: 1400px; width: 100%;">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>

            <div class="admin-main" style="background: transparent; padding: 1rem 0 2rem 0; font-family: 'Inter', sans-serif;">
                
                <!-- Welcome Banner -->
                <div style="background: linear-gradient(135deg, #2B4865 0%, #355C7D 100%); border-radius: 20px; padding: 2.5rem; margin-bottom: 2rem; color: white; position: relative; overflow: hidden; box-shadow: 0 10px 30px rgba(43, 72, 101, 0.15);">
                    <div style="position: absolute; right: -50px; top: -50px; opacity: 0.1; transform: scale(2);">
                        <svg width="200" height="200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20v-6M6 20V10M18 20V4"/></svg>
                    </div>
                    <div style="position: absolute; left: -20px; bottom: -50px; width: 150px; height: 150px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                    
                    <div style="position: relative; z-index: 1; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem;">
                        <div>
                            <h1 style="font-size: 2.2rem; font-weight: 800; margin: 0 0 0.5rem 0; color: #ffffff;">Teacher Dashboard</h1>
                            <p style="font-size: 1.1rem; margin: 0; opacity: 0.9; color: #f8fafc;">Welcome, <strong style="color: #ffffff;"><?= htmlspecialchars($userName) ?></strong>. Manage your courses and events here.</p>
                        </div>
                        <a href="<?= APP_ENTRY ?>?url=logout" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; text-decoration: none; padding: 10px 20px; border-radius: 10px; font-weight: 600; display: flex; align-items: center; gap: 8px; backdrop-filter: blur(5px); transition: all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                            Logout
                        </a>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                    <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.02); border: 1px solid #eef2f6; display: flex; align-items: center; gap: 1.2rem; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                        <div style="width: 54px; height: 54px; border-radius: 14px; background: #eff6ff; color: #3b82f6; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                        </div>
                        <div>
                            <h3 style="margin: 0 0 0.2rem 0; font-size: 1.6rem; font-weight: 800; color: #1e293b; line-height: 1;"><?= $stats['total_courses'] ?? 0 ?></h3>
                            <p style="margin: 0; font-size: 0.9rem; color: #64748b; font-weight: 600;">My Courses</p>
                        </div>
                    </div>

                    <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.02); border: 1px solid #eef2f6; display: flex; align-items: center; gap: 1.2rem; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                        <div style="width: 54px; height: 54px; border-radius: 14px; background: #fdf4ff; color: #d946ef; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                        </div>
                        <div>
                            <h3 style="margin: 0 0 0.2rem 0; font-size: 1.6rem; font-weight: 800; color: #1e293b; line-height: 1;"><?= $stats['total_students'] ?? 0 ?></h3>
                            <p style="margin: 0; font-size: 0.9rem; color: #64748b; font-weight: 600;">My Students</p>
                        </div>
                    </div>

                    <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.02); border: 1px solid #eef2f6; display: flex; align-items: center; gap: 1.2rem; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                        <div style="width: 54px; height: 54px; border-radius: 14px; background: #ecfdf5; color: #10b981; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                        </div>
                        <div>
                            <h3 style="margin: 0 0 0.2rem 0; font-size: 1.6rem; font-weight: 800; color: #1e293b; line-height: 1;">$<?= number_format($stats['total_earnings'] ?? 0, 2) ?></h3>
                            <p style="margin: 0; font-size: 0.9rem; color: #64748b; font-weight: 600;">Total Earnings</p>
                        </div>
                    </div>

                    <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.02); border: 1px solid #eef2f6; display: flex; align-items: center; gap: 1.2rem; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                        <div style="width: 54px; height: 54px; border-radius: 14px; background: #fffbeb; color: #f59e0b; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 18 18.18 22.73 12 18.77 5.82 22.73 6 18 2 9.27 8.91 8.26 12 2"/></svg>
                        </div>
                        <div>
                            <h3 style="margin: 0 0 0.2rem 0; font-size: 1.6rem; font-weight: 800; color: #1e293b; line-height: 1;"><?= $stats['avg_rating'] ?? 0 ?>/5</h3>
                            <p style="margin: 0; font-size: 0.9rem; color: #64748b; font-weight: 600;">Avg Rating</p>
                        </div>
                    </div>

                    <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.02); border: 1px solid #eef2f6; display: flex; align-items: center; gap: 1.2rem; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                        <div style="width: 54px; height: 54px; border-radius: 14px; background: #fef2f2; color: #ef4444; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        </div>
                        <div>
                            <h3 style="margin: 0 0 0.2rem 0; font-size: 1.6rem; font-weight: 800; color: #1e293b; line-height: 1;"><?= $stats['total_reviews'] ?? 0 ?></h3>
                            <p style="margin: 0; font-size: 0.9rem; color: #64748b; font-weight: 600;">Reviews</p>
                        </div>
                    </div>

                    <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.02); border: 1px solid #eef2f6; display: flex; align-items: center; gap: 1.2rem; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                        <div style="width: 54px; height: 54px; border-radius: 14px; background: #fef3c7; color: #d97706; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        </div>
                        <div>
                            <h3 style="margin: 0 0 0.2rem 0; font-size: 1.6rem; font-weight: 800; color: #1e293b; line-height: 1;"><?= $stats['pending_courses'] ?? 0 ?></h3>
                            <p style="margin: 0; font-size: 0.9rem; color: #64748b; font-weight: 600;">Pending</p>
                        </div>
                    </div>
                </div>

                <!-- Chart Section -->
                <?php if (!empty($monthlyEnrollments)): ?>
                <div style="background: white; border-radius: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.03); border: 1px solid #eef2f6; padding: 2rem; margin-top: 2rem;">
                    <h3 style="margin: 0 0 1.5rem 0; font-size: 1.3rem; color: #1e293b; font-weight: 800;">Monthly Enrollments</h3>
                    <canvas id="teacherChart" height="100"></canvas>
                </div>
                <?php endif; ?>

                <!-- Course Performance -->
                <?php if (!empty($coursePerformance)): ?>
                <div style="background: white; border-radius: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.03); border: 1px solid #eef2f6; padding: 2rem; margin-top: 2rem;">
                    <h3 style="margin: 0 0 1.5rem 0; font-size: 1.3rem; color: #1e293b; font-weight: 800;">Course Performance</h3>
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 2px solid #e2e8f0;">
                                <th style="text-align: left; padding: 12px; color: #64748b; font-weight: 600;">Course</th>
                                <th style="text-align: center; padding: 12px; color: #64748b; font-weight: 600;">Students</th>
                                <th style="text-align: center; padding: 12px; color: #64748b; font-weight: 600;">Earnings</th>
                                <th style="text-align: center; padding: 12px; color: #64748b; font-weight: 600;">Rating</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($coursePerformance as $perf): ?>
                            <tr style="border-bottom: 1px solid #e2e8f0;">
                                <td style="padding: 12px; font-weight: 600; color: #1e293b;"><?= htmlspecialchars($perf['title']) ?></td>
                                <td style="padding: 12px; text-align: center; color: #3b82f6; font-weight: 600;"><?= $perf['students'] ?? 0 ?></td>
                                <td style="padding: 12px; text-align: center; color: #10b981; font-weight: 600;">$<?= number_format($perf['earnings'] ?? 0, 2) ?></td>
                                <td style="padding: 12px; text-align: center; color: #f59e0b; font-weight: 600;"><?= $perf['avg_rating'] ? round($perf['avg_rating'], 1) . '/5' : '-' ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>

                <div class="pro-dashboard-grid">
                    <!-- MAIN COLUMN -->
                    <div class="pro-main-col" style="display: flex; flex-direction: column; gap: 2rem;">

                        <!-- My Courses -->
                        <div style="background: white; border-radius: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.03); border: 1px solid #eef2f6; overflow: hidden; padding: 2rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                                <h3 style="margin: 0; font-size: 1.3rem; color: #1e293b; font-weight: 800;">My Recent Courses</h3>
                                <a href="<?= APP_ENTRY ?>?url=teacher/courses" style="color: #548CA8; text-decoration: none; font-size: 0.9rem; font-weight: 600; padding: 6px 12px; border-radius: 6px; background: #e9f1fa; transition: background 0.2s;" onmouseover="this.style.background='#d0e3f5'" onmouseout="this.style.background='#e9f1fa'">View All</a>
                            </div>
                            
                            <?php if (!empty($courses)): ?>
                                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1.5rem;">
                                    <?php foreach (array_slice($courses, 0, 3) as $course): ?>
                                        <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 10px 20px rgba(0,0,0,0.05)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                            <div style="width: 48px; height: 48px; background: #eff6ff; color: #3b82f6; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                                            </div>
                                            <h4 style="margin: 0 0 0.5rem 0; font-size: 1.1rem; font-weight: 700; color: #1e293b;"><?= htmlspecialchars($course['title']) ?></h4>
                                            <p style="margin: 0 0 1rem 0; font-size: 0.85rem; color: #64748b; line-height: 1.5; height: 40px; overflow: hidden;"><?= htmlspecialchars(substr($course['description'], 0, 80)) ?>...</p>
                                            
                                            <div style="display: flex; gap: 8px;">
                                                <a href="<?= APP_ENTRY ?>?url=teacher/course/<?= $course['id'] ?>" style="flex: 1; text-align: center; background: #f1f5f9; color: #475569; text-decoration: none; padding: 8px; border-radius: 6px; font-weight: 600; font-size: 0.85rem; transition: all 0.2s;" onmouseover="this.style.background='#e2e8f0'; this.style.color='#1e293b'" onmouseout="this.style.background='#f1f5f9'; this.style.color='#475569'">View</a>
                                                <a href="<?= APP_ENTRY ?>?url=teacher/edit-course/<?= $course['id'] ?>" style="flex: 1; text-align: center; background: #548CA8; color: white; text-decoration: none; padding: 8px; border-radius: 6px; font-weight: 600; font-size: 0.85rem; transition: all 0.2s;" onmouseover="this.style.background='#355C7D'" onmouseout="this.style.background='#548CA8'">Edit</a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div style="text-align: center; padding: 3rem 1rem; background: #f8fafc; border: 2px dashed #e2e8f0; border-radius: 12px;">
                                    <p style="color: #64748b; margin: 0 0 1rem 0; font-size: 1rem;">You haven't created any courses yet.</p>
                                    <a href="<?= APP_ENTRY ?>?url=teacher/add-course" class="pro-action-btn primary">Create Your First Course</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- SIDE COLUMN -->
                    <div class="pro-side-col" style="display: flex; flex-direction: column; gap: 2rem;">
                        <!-- Profile Card -->
                        <div style="background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%); border-radius: 20px; padding: 2rem; box-shadow: 0 5px 20px rgba(0,0,0,0.03); border: 1px solid #eef2f6; text-align: center;">
                            <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #548CA8 0%, #355C7D 100%); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem auto; font-size: 2rem; font-weight: 800; box-shadow: 0 10px 20px rgba(84, 140, 168, 0.2);">
                                <?= strtoupper(substr($userName, 0, 1)) ?>
                            </div>
                            <h3 style="margin: 0 0 0.3rem 0; font-size: 1.4rem; color: #1e293b; font-weight: 800;"><?= htmlspecialchars($userName) ?></h3>
                            <p style="margin: 0 0 1.5rem 0; color: #64748b; font-weight: 600; font-size: 0.95rem;">Teacher / Instructor</p>
                            <a href="<?= APP_ENTRY ?>?url=teacher/profile" style="display: inline-block; width: 100%; background: white; color: #548CA8; text-decoration: none; padding: 12px; border-radius: 10px; font-weight: 700; border: 1.5px solid #eef2f6; transition: all 0.2s;" onmouseover="this.style.background='#f8fafc'; this.style.borderColor='#cbd5e1'" onmouseout="this.style.background='white'; this.style.borderColor='#eef2f6'">Edit Profile</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
    .pro-dashboard-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 2rem;
        align-items: start;
    }

    .pro-action-btn {
        padding: 10px 18px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.95rem;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .pro-action-btn.primary {
        background: #548CA8;
        color: white;
        box-shadow: 0 4px 10px rgba(84, 140, 168, 0.2);
    }
    .pro-action-btn.primary:hover {
        background: #355C7D;
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(84, 140, 168, 0.3);
    }

    .pro-action-btn.outline {
        background: white;
        color: #475569;
        border: 1px solid #cbd5e1;
    }
    .pro-action-btn.outline:hover {
        background: #f8fafc;
        border-color: #94a3b8;
        color: #1e293b;
    }

    @media (max-width: 992px) {
        .pro-dashboard-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<?php if (!empty($monthlyEnrollments)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('teacherChart').getContext('2d');
const labels = <?= json_encode(array_column($monthlyEnrollments, 'month')) ?>;
const data = <?= json_encode(array_column($monthlyEnrollments, 'total')) ?>;

new Chart(ctx, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: 'Enrollments',
            data: data,
            borderColor: '#548CA8',
            backgroundColor: 'rgba(84, 140, 168, 0.1)',
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#548CA8',
            pointRadius: 5,
            pointHoverRadius: 7
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 } }
        }
    }
});
</script>
<?php endif; ?>
