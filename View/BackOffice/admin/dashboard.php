<?php
/**
 * APPOLIOS - Admin Dashboard (Pro Neo Theme)
 */
?>

<div class="dashboard">
    <div class="container admin-dashboard-container" style="max-width: 1400px; width: 100%;">
        <div class="admin-layout">
            <?php $adminSidebarActive = 'dashboard'; require __DIR__ . '/partials/sidebar.php'; ?>

            <div class="admin-main" style="background: transparent; padding: 1rem 0 2rem 0; font-family: 'Inter', sans-serif;">
                
                <!-- Welcome Banner -->
                <div style="background: linear-gradient(135deg, #2B4865 0%, #355C7D 100%); border-radius: 20px; padding: 2.5rem; margin-bottom: 2rem; color: white; position: relative; overflow: hidden; box-shadow: 0 10px 30px rgba(43, 72, 101, 0.15);">
                    <div style="position: absolute; right: -50px; top: -50px; opacity: 0.1; transform: scale(2);">
                        <svg width="200" height="200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20v-6M6 20V10M18 20V4"/></svg>
                    </div>
                    <div style="position: absolute; left: -20px; bottom: -50px; width: 150px; height: 150px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                    
                    <div style="position: relative; z-index: 1; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem;">
                        <div>
                            <h1 style="font-size: 2.2rem; font-weight: 800; margin: 0 0 0.5rem 0; color: white;">Overview Dashboard</h1>
                            <p style="font-size: 1.1rem; margin: 0; opacity: 0.9; color: white;">Welcome back, <strong><?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?></strong>. Here is what is happening today.</p>
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
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                        </div>
                        <div>
                            <h3 style="margin: 0 0 0.2rem 0; font-size: 1.6rem; font-weight: 800; color: #1e293b; line-height: 1;"><?= $totalUsers ?? 0 ?></h3>
                            <p style="margin: 0; font-size: 0.9rem; color: #64748b; font-weight: 600;">Total Users</p>
                        </div>
                    </div>

                    <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.02); border: 1px solid #eef2f6; display: flex; align-items: center; gap: 1.2rem; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                        <div style="width: 54px; height: 54px; border-radius: 14px; background: #fdf4ff; color: #d946ef; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>
                        </div>
                        <div>
                            <h3 style="margin: 0 0 0.2rem 0; font-size: 1.6rem; font-weight: 800; color: #1e293b; line-height: 1;"><?= $totalStudents ?? 0 ?></h3>
                            <p style="margin: 0; font-size: 0.9rem; color: #64748b; font-weight: 600;">Students</p>
                        </div>
                    </div>

                    <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.02); border: 1px solid #eef2f6; display: flex; align-items: center; gap: 1.2rem; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                        <div style="width: 54px; height: 54px; border-radius: 14px; background: #f0fdf4; color: #22c55e; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                        </div>
                        <div>
                            <h3 style="margin: 0 0 0.2rem 0; font-size: 1.6rem; font-weight: 800; color: #1e293b; line-height: 1;"><?= $totalCourses ?? 0 ?></h3>
                            <p style="margin: 0; font-size: 0.9rem; color: #64748b; font-weight: 600;">Courses</p>
                        </div>
                    </div>

                    <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.02); border: 1px solid #eef2f6; display: flex; align-items: center; gap: 1.2rem; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                        <div style="width: 54px; height: 54px; border-radius: 14px; background: #fffbeb; color: #f59e0b; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        </div>
                        <div>
                            <h3 style="margin: 0 0 0.2rem 0; font-size: 1.6rem; font-weight: 800; color: #1e293b; line-height: 1;"><?= $totalEnrollments ?? 0 ?></h3>
                            <p style="margin: 0; font-size: 0.9rem; color: #64748b; font-weight: 600;">Enrollments</p>
                        </div>
                    </div>

                    <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.02); border: 1px solid #eef2f6; display: flex; align-items: center; gap: 1.2rem; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                        <div style="width: 54px; height: 54px; border-radius: 14px; background: #fef2f2; color: #ef4444; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        </div>
                        <div>
                            <h3 style="margin: 0 0 0.2rem 0; font-size: 1.6rem; font-weight: 800; color: #1e293b; line-height: 1;"><?= $totalEvenements ?? 0 ?></h3>
                            <p style="margin: 0; font-size: 0.9rem; color: #64748b; font-weight: 600;">Evenements</p>
                        </div>
                    </div>

                    <a href="<?= APP_ENTRY ?>?url=admin/teacher-applications" style="text-decoration: none; background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.02); border: 1px solid #eef2f6; display: flex; align-items: center; gap: 1.2rem; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                        <div style="width: 54px; height: 54px; border-radius: 14px; background: #fff7ed; color: #E19864; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                        </div>
                        <div>
                            <h3 style="margin: 0 0 0.2rem 0; font-size: 1.6rem; font-weight: 800; color: #1e293b; line-height: 1;"><?= $pendingTeacherApps ?? 0 ?></h3>
                            <p style="margin: 0; font-size: 0.9rem; color: #64748b; font-weight: 600;">Teacher Applications</p>
                        </div>
                        <?php if (($pendingTeacherApps ?? 0) > 0): ?>
                            <span style="margin-left: auto; background: #E19864; color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">Pending</span>
                        <?php endif; ?>
                    </a>
                </div>

                <div class="pro-dashboard-grid">
                    
                    <!-- LEFT COLUMN -->
                    <div class="pro-main-col" style="display: flex; flex-direction: column; gap: 2rem;">
                        
                        <!-- Recent Evenements Table -->
                        <div style="background: white; border-radius: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.03); border: 1px solid #eef2f6; overflow: hidden;">
                            <div style="padding: 1.5rem 2rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                                <h3 style="margin: 0; font-size: 1.25rem; color: #1e293b; font-weight: 700;">Recent Evenements</h3>
                                <a href="<?= APP_ENTRY ?>?url=event/evenements" style="color: #548CA8; text-decoration: none; font-size: 0.9rem; font-weight: 600; padding: 6px 12px; border-radius: 6px; background: #e9f1fa; transition: background 0.2s;" onmouseover="this.style.background='#d0e3f5'" onmouseout="this.style.background='#e9f1fa'">View All</a>
                            </div>
                            <div style="overflow-x: auto;">
                                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                                    <thead>
                                        <tr style="background: #f8fafc;">
                                            <th style="padding: 1rem 2rem; font-size: 0.85rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e2e8f0;">Title</th>
                                            <th style="padding: 1rem 2rem; font-size: 0.85rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e2e8f0;">Date</th>
                                            <th style="padding: 1rem 2rem; font-size: 0.85rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e2e8f0;">Location</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($recentEvenements)): ?>
                                            <?php foreach ($recentEvenements as $evenement): ?>
                                                <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                                    <td style="padding: 1.2rem 2rem; font-weight: 600; color: #334155;"><?= htmlspecialchars($evenement['title']) ?></td>
                                                    <td style="padding: 1.2rem 2rem; color: #64748b; font-size: 0.95rem;"><?= date('M d, Y H:i', strtotime($evenement['event_date'])) ?></td>
                                                    <td style="padding: 1.2rem 2rem; color: #64748b; font-size: 0.95rem;"><?= htmlspecialchars($evenement['location'] ?: 'TBA') ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="3" style="text-align: center; padding: 2.5rem; color: #94a3b8;">No events yet. <a href="<?= APP_ENTRY ?>?url=event/add-evenement" style="color: #548CA8; text-decoration: underline;">Create your first evenement</a></td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Recent Courses Table -->
                        <div style="background: white; border-radius: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.03); border: 1px solid #eef2f6; overflow: hidden;">
                            <div style="padding: 1.5rem 2rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                                <h3 style="margin: 0; font-size: 1.25rem; color: #1e293b; font-weight: 700;">Recent Courses</h3>
                                <a href="<?= APP_ENTRY ?>?url=admin/courses" style="color: #548CA8; text-decoration: none; font-size: 0.9rem; font-weight: 600; padding: 6px 12px; border-radius: 6px; background: #e9f1fa; transition: background 0.2s;" onmouseover="this.style.background='#d0e3f5'" onmouseout="this.style.background='#e9f1fa'">View All</a>
                            </div>
                            <div style="overflow-x: auto;">
                                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                                    <thead>
                                        <tr style="background: #f8fafc;">
                                            <th style="padding: 1rem 2rem; font-size: 0.85rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e2e8f0;">Title</th>
                                            <th style="padding: 1rem 2rem; font-size: 0.85rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e2e8f0;">Creator</th>
                                            <th style="padding: 1rem 2rem; font-size: 0.85rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e2e8f0;">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($recentCourses)): ?>
                                            <?php foreach (array_slice($recentCourses, 0, 5) as $course): ?>
                                                <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                                    <td style="padding: 1.2rem 2rem; font-weight: 600; color: #334155;"><?= htmlspecialchars($course['title']) ?></td>
                                                    <td style="padding: 1.2rem 2rem; color: #64748b; font-size: 0.95rem;"><?= htmlspecialchars($course['creator_name']) ?></td>
                                                    <td style="padding: 1.2rem 2rem; color: #64748b; font-size: 0.95rem;"><?= date('M d, Y', strtotime($course['created_at'])) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="3" style="text-align: center; padding: 2.5rem; color: #94a3b8;">No courses found. <a href="<?= APP_ENTRY ?>?url=admin/add-course" style="color: #548CA8; text-decoration: underline;">Add your first course</a></td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Event Statistics Table -->
                        <div style="background: white; border-radius: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.03); border: 1px solid #eef2f6; overflow: hidden;">
                            <div style="padding: 1.5rem 2rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                                <h3 style="margin: 0; font-size: 1.25rem; color: #1e293b; font-weight: 700;">Event Statistics (Participants)</h3>
                                <a href="<?= APP_ENTRY ?>?url=event/evenements" style="color: #548CA8; text-decoration: none; font-size: 0.9rem; font-weight: 600; padding: 6px 12px; border-radius: 6px; background: #e9f1fa; transition: background 0.2s;" onmouseover="this.style.background='#d0e3f5'" onmouseout="this.style.background='#e9f1fa'">View All Events</a>
                            </div>
                            <div style="overflow-x: auto;">
                                <?php if (!empty($evenementsStats)): ?>
                                    <?php
                                    $chartLabelsAdmin = [];
                                    $chartDataAdmin = [];
                                    foreach (array_slice($evenementsStats, 0, 10) as $stat) {
                                        $chartLabelsAdmin[] = (strlen($stat['title']) > 20) ? substr($stat['title'], 0, 20) . '...' : $stat['title'];
                                        $chartDataAdmin[] = (int)$stat['participant_count'];
                                    }
                                    ?>
                                    <div style="height: 300px; margin: 1.5rem; padding: 1rem; background: #f8fafc; border-radius: 12px;">
                                        <canvas id="adminEventChart"></canvas>
                                    </div>
                                <?php endif; ?>
                                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                                    <thead>
                                        <tr style="background: #f8fafc;">
                                            <th style="padding: 1rem 2rem; font-size: 0.85rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e2e8f0;">Event Title</th>
                                            <th style="padding: 1rem 2rem; font-size: 0.85rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e2e8f0;">Date</th>
                                            <th style="padding: 1rem 2rem; font-size: 0.85rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e2e8f0; text-align: center;">Participants</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($evenementsStats)): ?>
                                            <?php foreach (array_slice($evenementsStats, 0, 5) as $stat): ?>
                                                <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                                    <td style="padding: 1.2rem 2rem; font-weight: 600; color: #334155;"><?= htmlspecialchars($stat['title']) ?></td>
                                                    <td style="padding: 1.2rem 2rem; color: #64748b; font-size: 0.95rem;"><?= date('M d, Y', strtotime($stat['event_date'])) ?></td>
                                                    <td style="padding: 1.2rem 2rem; text-align: center;">
                                                        <span style="background: #f0fdf4; color: #22c55e; padding: 4px 12px; border-radius: 20px; font-weight: 700; font-size: 0.9rem;">
                                                            <?= (int)$stat['participant_count'] ?> <svg style="display:inline; vertical-align:middle; margin-left:4px;" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="3" style="text-align: center; padding: 2.5rem; color: #94a3b8;">No event statistics available yet.</td>
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
                        <div style="background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%); border-radius: 20px; padding: 2rem; box-shadow: 0 5px 20px rgba(0,0,0,0.03); border: 1px solid #eef2f6; text-align: center;">
                            <div style="width: 70px; height: 70px; background: #e9f1fa; color: #548CA8; border-radius: 20px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem auto;">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            </div>
                            <h3 style="margin: 0 0 0.8rem 0; font-size: 1.4rem; color: #1e293b; font-weight: 800;">Module Evenement</h3>
                            <p style="margin: 0 0 1.5rem 0; color: #64748b; line-height: 1.6; font-size: 0.95rem;">Manage upcoming events, schedule dates, centralize event info, and review teacher proposals.</p>
                            <a href="<?= APP_ENTRY ?>?url=event/evenements" style="display: inline-block; width: 100%; background: #548CA8; color: white; text-decoration: none; padding: 12px; border-radius: 10px; font-weight: 700; transition: all 0.2s; box-shadow: 0 4px 10px rgba(84, 140, 168, 0.2);" onmouseover="this.style.background='#355C7D'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 15px rgba(84, 140, 168, 0.3)'" onmouseout="this.style.background='#548CA8'; this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 10px rgba(84, 140, 168, 0.2)'">Open Module</a>
                        </div>

                        <!-- System Status -->
                        <div style="background: white; border-radius: 20px; padding: 2rem; box-shadow: 0 5px 20px rgba(0,0,0,0.03); border: 1px solid #eef2f6;">
                            <h3 style="margin: 0 0 1.5rem 0; font-size: 1.2rem; color: #1e293b; font-weight: 700;">System Status</h3>
                            <div style="display: flex; flex-direction: column; gap: 1rem;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div style="width: 10px; height: 10px; background: #22c55e; border-radius: 50%;"></div>
                                        <span style="color: #475569; font-weight: 600; font-size: 0.95rem;">Database</span>
                                    </div>
                                    <span style="color: #64748b; font-size: 0.85rem;">Online</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div style="width: 10px; height: 10px; background: #22c55e; border-radius: 50%;"></div>
                                        <span style="color: #475569; font-weight: 600; font-size: 0.95rem;">Web Server</span>
                                    </div>
                                    <span style="color: #64748b; font-size: 0.85rem;">Online</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div style="width: 10px; height: 10px; background: #f59e0b; border-radius: 50%;"></div>
                                        <span style="color: #475569; font-weight: 600; font-size: 0.95rem;">Storage</span>
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('adminEventChart');
    if (ctx) {
        new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: <?= json_encode($chartLabelsAdmin ?? []) ?>,
                datasets: [{
                    label: 'Participants',
                    data: <?= json_encode($chartDataAdmin ?? []) ?>,
                    backgroundColor: 'rgba(84, 140, 168, 0.7)',
                    borderColor: 'rgba(84, 140, 168, 1)',
                    borderWidth: 1,
                    borderRadius: 6,
                    hoverBackgroundColor: 'rgba(53, 92, 125, 0.9)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }
});
</script>