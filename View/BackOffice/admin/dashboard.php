<?php
/**
 * APPOLIOS - Admin Dashboard (Pro Neo Theme)
 */
?>

<div class="dashboard">
    <div class="container admin-dashboard-container" style="max-width: 1400px; width: 100%;">
        <div class="admin-layout">
            <?php $adminSidebarActive = 'dashboard';
            require __DIR__ . '/partials/sidebar.php'; ?>

            <div class="admin-main"
                style="background: transparent; padding: 1rem 0 2rem 0; font-family: 'Inter', sans-serif;">

                <!-- Welcome Banner -->
                <div
                    style="background: linear-gradient(135deg, #2B4865 0%, #355C7D 100%); border-radius: 20px; padding: 2.5rem; margin-bottom: 2rem; color: white; position: relative; overflow: hidden; box-shadow: 0 10px 30px rgba(43, 72, 101, 0.15);">
                    <div style="position: absolute; right: -50px; top: -50px; opacity: 0.1; transform: scale(2);">
                        <svg width="200" height="200" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 20v-6M6 20V10M18 20V4" />
                        </svg>
                    </div>
                    <div
                        style="position: absolute; left: -20px; bottom: -50px; width: 150px; height: 150px; background: rgba(255,255,255,0.05); border-radius: 50%;">
                    </div>

                    <div
                        style="position: relative; z-index: 1; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem;">
                        <div>
                            <h1 style="font-size: 2.2rem; font-weight: 800; margin: 0 0 0.5rem 0; color: white;">
                                Overview Dashboard</h1>
                            <p style="font-size: 1.1rem; margin: 0; opacity: 0.9; color: white;">Welcome back,
                                <strong><?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?></strong>. Here is
                                what is happening today.
                            </p>
                        </div>
                        <div style="display:flex;gap:10px;flex-wrap:wrap;justify-content:flex-end;">
                            <button type="button" onclick="openFaceSetupModal()"
                                style="background:rgba(255,255,255,0.12);border:1px solid rgba(255,255,255,0.25);color:white;padding:10px 18px;border-radius:10px;font-weight:600;display:flex;align-items:center;gap:8px;backdrop-filter:blur(5px);transition:all 0.2s;cursor:pointer;"
                                onmouseover="this.style.background='rgba(255,255,255,0.22)'"
                                onmouseout="this.style.background='rgba(255,255,255,0.12)'">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                                Setup Face ID
                            </button>
                            <a href="<?= APP_ENTRY ?>?url=logout"
                                style="background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.2);color:white;text-decoration:none;padding:10px 20px;border-radius:10px;font-weight:600;display:flex;align-items:center;gap:8px;backdrop-filter:blur(5px);transition:all 0.2s;"
                                onmouseover="this.style.background='rgba(255,255,255,0.2)'"
                                onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                    <polyline points="16 17 21 12 16 7"></polyline>
                                    <line x1="21" y1="12" x2="9" y2="12"></line>
                                </svg>
                                Logout
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div
                    style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                    <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.02); border: 1px solid #eef2f6; display: flex; align-items: center; gap: 1.2rem; transition: transform 0.2s;"
                        onmouseover="this.style.transform='translateY(-3px)'"
                        onmouseout="this.style.transform='translateY(0)'">
                        <div
                            style="width: 54px; height: 54px; border-radius: 14px; background: #eff6ff; color: #3b82f6; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </div>
                        <div>
                            <h3
                                style="margin: 0 0 0.2rem 0; font-size: 1.6rem; font-weight: 800; color: #1e293b; line-height: 1;">
                                <?= $totalUsers ?? 0 ?>
                            </h3>
                            <p style="margin: 0; font-size: 0.9rem; color: #64748b; font-weight: 600;">Total Users</p>
                        </div>
                    </div>

                    <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.02); border: 1px solid #eef2f6; display: flex; align-items: center; gap: 1.2rem; transition: transform 0.2s;"
                        onmouseover="this.style.transform='translateY(-3px)'"
                        onmouseout="this.style.transform='translateY(0)'">
                        <div
                            style="width: 54px; height: 54px; border-radius: 14px; background: #fdf4ff; color: #d946ef; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                                <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                            </svg>
                        </div>
                        <div>
                            <h3
                                style="margin: 0 0 0.2rem 0; font-size: 1.6rem; font-weight: 800; color: #1e293b; line-height: 1;">
                                <?= $totalStudents ?? 0 ?>
                            </h3>
                            <p style="margin: 0; font-size: 0.9rem; color: #64748b; font-weight: 600;">Students</p>
                        </div>
                    </div>

                    <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.02); border: 1px solid #eef2f6; display: flex; align-items: center; gap: 1.2rem; transition: transform 0.2s;"
                        onmouseover="this.style.transform='translateY(-3px)'"
                        onmouseout="this.style.transform='translateY(0)'">
                        <div
                            style="width: 54px; height: 54px; border-radius: 14px; background: #f0fdf4; color: #22c55e; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3
                                style="margin: 0 0 0.2rem 0; font-size: 1.6rem; font-weight: 800; color: #1e293b; line-height: 1;">
                                <?= $totalCourses ?? 0 ?>
                            </h3>
                            <p style="margin: 0; font-size: 0.9rem; color: #64748b; font-weight: 600;">Courses</p>
                        </div>
                    </div>

                    <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.02); border: 1px solid #eef2f6; display: flex; align-items: center; gap: 1.2rem; transition: transform 0.2s;"
                        onmouseover="this.style.transform='translateY(-3px)'"
                        onmouseout="this.style.transform='translateY(0)'">
                        <div
                            style="width: 54px; height: 54px; border-radius: 14px; background: #fffbeb; color: #f59e0b; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </div>
                        <div>
                            <h3
                                style="margin: 0 0 0.2rem 0; font-size: 1.6rem; font-weight: 800; color: #1e293b; line-height: 1;">
                                <?= $totalEnrollments ?? 0 ?>
                            </h3>
                            <p style="margin: 0; font-size: 0.9rem; color: #64748b; font-weight: 600;">Enrollments</p>
                        </div>
                    </div>

                    <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.02); border: 1px solid #eef2f6; display: flex; align-items: center; gap: 1.2rem; transition: transform 0.2s;"
                        onmouseover="this.style.transform='translateY(-3px)'"
                        onmouseout="this.style.transform='translateY(0)'">
                        <div
                            style="width: 54px; height: 54px; border-radius: 14px; background: #fef2f2; color: #ef4444; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </div>
                        <div>
                            <h3
                                style="margin: 0 0 0.2rem 0; font-size: 1.6rem; font-weight: 800; color: #1e293b; line-height: 1;">
                                <?= $totalEvenements ?? 0 ?>
                            </h3>
                            <p style="margin: 0; font-size: 0.9rem; color: #64748b; font-weight: 600;">Evenements</p>
                        </div>
                    </div>

                    <a href="<?= APP_ENTRY ?>?url=admin/teacher-applications"
                        style="text-decoration: none; background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.02); border: 1px solid #eef2f6; display: flex; align-items: center; gap: 1.2rem; transition: transform 0.2s;"
                        onmouseover="this.style.transform='translateY(-3px)'"
                        onmouseout="this.style.transform='translateY(0)'">
                        <div
                            style="width: 54px; height: 54px; border-radius: 14px; background: #fff7ed; color: #E19864; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                <polyline points="10 9 9 9 8 9"></polyline>
                            </svg>
                        </div>
                        <div>
                            <h3
                                style="margin: 0 0 0.2rem 0; font-size: 1.6rem; font-weight: 800; color: #1e293b; line-height: 1;">
                                <?= $pendingTeacherApps ?? 0 ?>
                            </h3>
                            <p style="margin: 0; font-size: 0.9rem; color: #64748b; font-weight: 600;">Teacher
                                Applications</p>
                        </div>
                        <?php if (($pendingTeacherApps ?? 0) > 0): ?>
                            <span
                                style="margin-left: auto; background: #E19864; color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">Pending</span>
                        <?php endif; ?>
                    </a>
                </div>

                <div class="pro-dashboard-grid">

                    <!-- LEFT COLUMN -->
                    <div class="pro-main-col" style="display: flex; flex-direction: column; gap: 2rem;">

                        <!-- Recent Evenements Table -->
                        <div
                            style="background: white; border-radius: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.03); border: 1px solid #eef2f6; overflow: hidden;">
                            <div
                                style="padding: 1.5rem 2rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                                <h3 style="margin: 0; font-size: 1.25rem; color: #1e293b; font-weight: 700;">Recent
                                    Evenements</h3>
                                <a href="<?= APP_ENTRY ?>?url=admin/evenements"
                                    style="color: #548CA8; text-decoration: none; font-size: 0.9rem; font-weight: 600; padding: 6px 12px; border-radius: 6px; background: #e9f1fa; transition: background 0.2s;"
                                    onmouseover="this.style.background='#d0e3f5'"
                                    onmouseout="this.style.background='#e9f1fa'">View All</a>
                            </div>
                            <div style="overflow-x: auto;">
                                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                                    <thead>
                                        <tr style="background: #f8fafc;">
                                            <th
                                                style="padding: 1rem 2rem; font-size: 0.85rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e2e8f0;">
                                                Title</th>
                                            <th
                                                style="padding: 1rem 2rem; font-size: 0.85rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e2e8f0;">
                                                Date</th>
                                            <th
                                                style="padding: 1rem 2rem; font-size: 0.85rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e2e8f0;">
                                                Location</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($recentEvenements)): ?>
                                            <?php foreach ($recentEvenements as $evenement): ?>
                                                <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;"
                                                    onmouseover="this.style.background='#f8fafc'"
                                                    onmouseout="this.style.background='transparent'">
                                                    <td style="padding: 1.2rem 2rem; font-weight: 600; color: #334155;">
                                                        <?= htmlspecialchars($evenement['title']) ?>
                                                    </td>
                                                    <td style="padding: 1.2rem 2rem; color: #64748b; font-size: 0.95rem;">
                                                        <?= date('M d, Y H:i', strtotime($evenement['event_date'])) ?>
                                                    </td>
                                                    <td style="padding: 1.2rem 2rem; color: #64748b; font-size: 0.95rem;">
                                                        <?= htmlspecialchars($evenement['location'] ?: 'TBA') ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="3"
                                                    style="text-align: center; padding: 2.5rem; color: #94a3b8;">No events
                                                    yet. <a href="<?= APP_ENTRY ?>?url=admin/add-evenement"
                                                        style="color: #548CA8; text-decoration: underline;">Create your
                                                        first evenement</a></td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Recent Courses Table -->
                        <div
                            style="background: white; border-radius: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.03); border: 1px solid #eef2f6; overflow: hidden;">
                            <div
                                style="padding: 1.5rem 2rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                                <h3 style="margin: 0; font-size: 1.25rem; color: #1e293b; font-weight: 700;">Recent
                                    Courses</h3>
                                <a href="<?= APP_ENTRY ?>?url=admin/courses"
                                    style="color: #548CA8; text-decoration: none; font-size: 0.9rem; font-weight: 600; padding: 6px 12px; border-radius: 6px; background: #e9f1fa; transition: background 0.2s;"
                                    onmouseover="this.style.background='#d0e3f5'"
                                    onmouseout="this.style.background='#e9f1fa'">View All</a>
                            </div>
                            <div style="overflow-x: auto;">
                                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                                    <thead>
                                        <tr style="background: #f8fafc;">
                                            <th
                                                style="padding: 1rem 2rem; font-size: 0.85rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e2e8f0;">
                                                Title</th>
                                            <th
                                                style="padding: 1rem 2rem; font-size: 0.85rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e2e8f0;">
                                                Creator</th>
                                            <th
                                                style="padding: 1rem 2rem; font-size: 0.85rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e2e8f0;">
                                                Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($recentCourses)): ?>
                                            <?php foreach (array_slice($recentCourses, 0, 5) as $course): ?>
                                                <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;"
                                                    onmouseover="this.style.background='#f8fafc'"
                                                    onmouseout="this.style.background='transparent'">
                                                    <td style="padding: 1.2rem 2rem; font-weight: 600; color: #334155;">
                                                        <?= htmlspecialchars($course['title']) ?>
                                                    </td>
                                                    <td style="padding: 1.2rem 2rem; color: #64748b; font-size: 0.95rem;">
                                                        <?= htmlspecialchars($course['creator_name']) ?>
                                                    </td>
                                                    <td style="padding: 1.2rem 2rem; color: #64748b; font-size: 0.95rem;">
                                                        <?= date('M d, Y', strtotime($course['created_at'])) ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="3"
                                                    style="text-align: center; padding: 2.5rem; color: #94a3b8;">No courses
                                                    found. <a href="<?= APP_ENTRY ?>?url=admin/add-course"
                                                        style="color: #548CA8; text-decoration: underline;">Add your first
                                                        course</a></td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>

                    <!-- RIGHT COLUMN -->
                    <div class="pro-side-col" style="display: flex; flex-direction: column; gap: 2rem;">

                        <!-- Module Spotlight Card -->
                        <div
                            style="background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%); border-radius: 20px; padding: 2rem; box-shadow: 0 5px 20px rgba(0,0,0,0.03); border: 1px solid #eef2f6; text-align: center;">
                            <div
                                style="width: 70px; height: 70px; background: #e9f1fa; color: #548CA8; border-radius: 20px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem auto;">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                            </div>
                            <h3 style="margin: 0 0 0.8rem 0; font-size: 1.4rem; color: #1e293b; font-weight: 800;">
                                Module Evenement</h3>
                            <p style="margin: 0 0 1.5rem 0; color: #64748b; line-height: 1.6; font-size: 0.95rem;">
                                Manage upcoming events, schedule dates, centralize event info, and review teacher
                                proposals.</p>
                            <a href="<?= APP_ENTRY ?>?url=admin/evenements"
                                style="display: inline-block; width: 100%; background: #548CA8; color: white; text-decoration: none; padding: 12px; border-radius: 10px; font-weight: 700; transition: all 0.2s; box-shadow: 0 4px 10px rgba(84, 140, 168, 0.2);"
                                onmouseover="this.style.background='#355C7D'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 15px rgba(84, 140, 168, 0.3)'"
                                onmouseout="this.style.background='#548CA8'; this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 10px rgba(84, 140, 168, 0.2)'">Open
                                Module</a>
                        </div>

                        <!-- System Status -->
                        <div
                            style="background: white; border-radius: 20px; padding: 2rem; box-shadow: 0 5px 20px rgba(0,0,0,0.03); border: 1px solid #eef2f6;">
                            <h3 style="margin: 0 0 1.5rem 0; font-size: 1.2rem; color: #1e293b; font-weight: 700;">
                                System Status</h3>
                            <div style="display: flex; flex-direction: column; gap: 1rem;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div
                                            style="width: 10px; height: 10px; background: #22c55e; border-radius: 50%;">
                                        </div>
                                        <span
                                            style="color: #475569; font-weight: 600; font-size: 0.95rem;">Database</span>
                                    </div>
                                    <span style="color: #64748b; font-size: 0.85rem;">Online</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div
                                            style="width: 10px; height: 10px; background: #22c55e; border-radius: 50%;">
                                        </div>
                                        <span style="color: #475569; font-weight: 600; font-size: 0.95rem;">Web
                                            Server</span>
                                    </div>
                                    <span style="color: #64748b; font-size: 0.85rem;">Online</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div
                                            style="width: 10px; height: 10px; background: #f59e0b; border-radius: 50%;">
                                        </div>
                                        <span
                                            style="color: #475569; font-weight: 600; font-size: 0.95rem;">Storage</span>
                                    </div>
                                    <span style="color: #64748b; font-size: 0.85rem;">78% Used</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Face ID Setup Modal -->
<div id="face-setup-modal"
    style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(10,15,30,.85);backdrop-filter:blur(6px);align-items:center;justify-content:center;">
    <div
        style="background:white;border-radius:24px;padding:2rem;max-width:480px;width:95%;text-align:center;box-shadow:0 24px 80px rgba(0,0,0,.4);position:relative;animation:fsSlideUp .3s ease;">
        <button onclick="closeFaceSetupModal()"
            style="position:absolute;top:16px;right:16px;background:#f1f5f9;border:none;width:36px;height:36px;border-radius:50%;cursor:pointer;font-size:1.1rem;color:#64748b;display:flex;align-items:center;justify-content:center;">✕</button>
        <div
            style="width:56px;height:56px;background:linear-gradient(135deg,#2B4865,#548CA8);border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                <circle cx="12" cy="12" r="3" />
            </svg>
        </div>
        <h3 style="margin:0 0 6px;color:#1e293b;font-size:1.3rem;font-weight:800;">Setup Face ID</h3>
        <p id="fsm-status-text" style="margin:0 0 20px;color:#64748b;font-size:.9rem;">Loading face recognition models…
        </p>
        <div
            style="position:relative;display:inline-block;border-radius:16px;overflow:hidden;box-shadow:0 8px 30px rgba(0,0,0,.15);">
            <video id="fsm-video" autoplay muted playsinline width="380" height="285"
                style="display:block;border-radius:16px;background:#0f172a;"></video>
            <canvas id="fsm-canvas" width="380" height="285"
                style="position:absolute;top:0;left:0;border-radius:16px;pointer-events:none;"></canvas>
            <div id="fsm-ring"
                style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:160px;height:160px;border-radius:50%;border:3px solid rgba(84,140,168,.5);animation:fsRingPulse 2s ease-in-out infinite;pointer-events:none;">
            </div>
        </div>
        <div
            style="margin:16px auto 0;max-width:340px;height:6px;background:#e2e8f0;border-radius:3px;overflow:hidden;">
            <div id="fsm-progress"
                style="height:100%;width:0%;background:linear-gradient(90deg,#2B4865,#548CA8);border-radius:3px;transition:width .4s ease;">
            </div>
        </div>
        <div id="fsm-error"
            style="display:none;margin-top:12px;padding:10px 14px;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;color:#b91c1c;font-size:.88rem;">
        </div>
        <div id="fsm-success"
            style="display:none;margin-top:12px;padding:10px 14px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;color:#166534;font-size:.88rem;">
        </div>
        <div style="display:flex;gap:10px;justify-content:center;margin-top:20px;">
            <button id="fsm-capture-btn" onclick="captureAndSave()"
                style="padding:11px 28px;background:linear-gradient(135deg,#2B4865,#548CA8);border:none;border-radius:10px;color:white;font-weight:700;cursor:pointer;font-size:.95rem;transition:all .2s;"
                onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">📸 Capture &amp;
                Save</button>
            <button onclick="closeFaceSetupModal()"
                style="padding:11px 24px;border:1px solid #e2e8f0;border-radius:10px;background:white;color:#64748b;cursor:pointer;font-weight:600;"
                onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">Cancel</button>
        </div>
    </div>
</div>

<style>
    @keyframes fsSlideUp {
        from {
            opacity: 0;
            transform: translateY(30px) scale(.97)
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1)
        }
    }

    @keyframes fsRingPulse {

        0%,
        100% {
            transform: translate(-50%, -50%) scale(.95);
            opacity: .5
        }

        50% {
            transform: translate(-50%, -50%) scale(1.05);
            opacity: 1
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script>
    (function () {
        const MODELS = 'https://cdn.jsdelivr.net/gh/justadudewhohacks/face-api.js@0.22.2/weights';
        const SAVE_URL = '<?= APP_ENTRY ?>?url=auth/save-face-descriptor';
        let stream = null, loaded = false, detecting = false, lastDescriptor = null;

        function el(id) { return document.getElementById(id); }
        function setStatus(t, p) { el('fsm-status-text').textContent = t; if (p !== undefined) el('fsm-progress').style.width = p + '%'; }
        function showErr(m) { el('fsm-error').style.display = 'block'; el('fsm-error').textContent = m; el('fsm-success').style.display = 'none'; }
        function showOk(m) { el('fsm-success').style.display = 'block'; el('fsm-success').textContent = m; el('fsm-error').style.display = 'none'; }
        function hideMessages() { el('fsm-error').style.display = 'none'; el('fsm-success').style.display = 'none'; }

        async function loadModels() {
            if (loaded) return;
            await Promise.all([
                faceapi.nets.tinyFaceDetector.loadFromUri(MODELS),
                faceapi.nets.faceLandmark68TinyNet.loadFromUri(MODELS),
                faceapi.nets.faceRecognitionNet.loadFromUri(MODELS)
            ]);
            loaded = true;
        }

        window.openFaceSetupModal = async function () {
            el('face-setup-modal').style.display = 'flex';
            hideMessages(); lastDescriptor = null;
            setStatus('Loading face recognition models…', 5);
            try {
                await loadModels();
                setStatus('Starting camera…', 20);
                stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user', width: 380, height: 285 } });
                const v = el('fsm-video'); v.srcObject = stream; await v.play();
                setStatus('Position your face in the ring, then click Capture.', 40);
                startPreview();
            } catch (e) { setStatus('Error', 0); showErr('Error: ' + e.message); }
        };

        function startPreview() {
            detecting = false;
            const v = el('fsm-video'), c = el('fsm-canvas'), ctx = c.getContext('2d');
            const opts = new faceapi.TinyFaceDetectorOptions({ inputSize: 320, scoreThreshold: .5 });
            const previewLoop = setInterval(async () => {
                if (detecting || v.readyState < 2) return;
                detecting = true;
                ctx.clearRect(0, 0, c.width, c.height);
                try {
                    const r = await faceapi.detectSingleFace(v, opts).withFaceLandmarks(true).withFaceDescriptor();
                    if (r) {
                        const dims = faceapi.matchDimensions(c, v, true);
                        faceapi.draw.drawDetections(c, faceapi.resizeResults(r, dims));
                        el('fsm-ring').style.borderColor = 'rgba(34,197,94,.8)';
                        lastDescriptor = Array.from(r.descriptor);
                        setStatus('Face detected ✓ — click Capture & Save when ready.', 60);
                    } else {
                        el('fsm-ring').style.borderColor = 'rgba(84,140,168,.5)';
                        lastDescriptor = null;
                        setStatus('No face detected — position your face in the ring.', 40);
                    }
                } catch (e) { }
                detecting = false;
            }, 800);
            window._fsmPreviewLoop = previewLoop;
        }

        window.captureAndSave = async function () {
            if (!lastDescriptor) { showErr('No face detected yet. Please position your face first.'); return; }
            hideMessages();
            setStatus('Saving Face ID…', 85);
            el('fsm-capture-btn').disabled = true;
            try {
                const res = await fetch(SAVE_URL, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ descriptor: lastDescriptor }) });
                const d = await res.json();
                if (d.success) {
                    setStatus('Face ID saved successfully!', 100);
                    el('fsm-ring').style.borderColor = 'rgba(34,197,94,1)';
                    showOk('✓ ' + d.message + ' You can now log in using Face ID.');
                } else {
                    setStatus('Failed to save.', 0);
                    showErr(d.message || 'Failed to save Face ID.');
                }
            } catch (e) { showErr('Network error: ' + e.message); }
            el('fsm-capture-btn').disabled = false;
        };

        function stopCam() {
            if (window._fsmPreviewLoop) { clearInterval(window._fsmPreviewLoop); window._fsmPreviewLoop = null; }
            if (stream) { stream.getTracks().forEach(t => t.stop()); stream = null; }
            const v = el('fsm-video'); if (v) v.srcObject = null;
            detecting = false; lastDescriptor = null;
        }

        window.closeFaceSetupModal = function () {
            stopCam();
            el('face-setup-modal').style.display = 'none';
            hideMessages();
            setStatus('Loading face recognition models…', 0);
            el('fsm-progress').style.width = '0%';
            el('fsm-ring').style.borderColor = 'rgba(84,140,168,.5)';
        };
    })();
</script>

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