<?php
/**
 * APPOLIOS - Student Dashboard
 */

$studentSidebarActive = 'dashboard';
$enrollmentCount = count($enrollments ?? []);
$availableCount = count($allCourses ?? []);
$avgProgress = 0;

if (!empty($enrollments)) {
    $sum = 0;
    foreach ($enrollments as $enrollment) {
        $sum += (int) ($enrollment['progress'] ?? 0);
    }
    $avgProgress = (int) round($sum / count($enrollments));
}
?>

<div class="dashboard student-events-page">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>

            <div class="admin-main" style="background: transparent; padding: 1rem 0 2rem 0;">
                
                <!-- Welcome Banner -->
                <section class="student-events-hero-top" style="margin-bottom: 2rem;">
                    <div class="student-events-hero-copy">
                        <span class="student-events-hero-kicker">Student Space</span>
                        <h1>Dashboard</h1>
                        <p>Welcome back, <strong><?= htmlspecialchars($userName) ?></strong>. Track your progress and keep learning.</p>
                        
                        <div style="margin-top: 1.5rem;">
                            <a href="<?= APP_ENTRY ?>?url=logout" class="student-courses-hero-btn student-courses-hero-btn-primary" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; font-size: 0.95rem; border-radius: 8px; text-decoration: none;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                                Logout
                            </a>
                        </div>
                    </div>

                    <div class="student-events-hero-media" aria-hidden="true">
                        <article class="student-events-visual-card student-events-visual-card-main">
                            <img src="<?= APP_URL ?>/View/assets/images/about/06.jpg" alt="Student studying" class="student-events-visual-img">
                        </article>
                        <article class="student-events-visual-card student-events-visual-card-sub">
                            <img src="<?= APP_URL ?>/View/assets/images/about/09.jpg" alt="Online learning" class="student-events-visual-img">
                        </article>
                    </div>
                </section>

                <!-- Stats Grid -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                    <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.02); border: 1px solid #eef2f6; display: flex; align-items: center; gap: 1.2rem; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                        <div style="width: 54px; height: 54px; border-radius: 14px; background: #eff6ff; color: #3b82f6; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                        </div>
                        <div>
                            <h3 style="margin: 0 0 0.2rem 0; font-size: 1.6rem; font-weight: 800; color: #1e293b; line-height: 1;"><?= (int) $enrollmentCount ?></h3>
                            <p style="margin: 0; font-size: 0.9rem; color: #64748b; font-weight: 600;">Enrolled Courses</p>
                        </div>
                    </div>

                    <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.02); border: 1px solid #eef2f6; display: flex; align-items: center; gap: 1.2rem; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                        <div style="width: 54px; height: 54px; border-radius: 14px; background: #fdf4ff; color: #d946ef; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        </div>
                        <div>
                            <h3 style="margin: 0 0 0.2rem 0; font-size: 1.6rem; font-weight: 800; color: #1e293b; line-height: 1;"><?= (int) $avgProgress ?>%</h3>
                            <p style="margin: 0; font-size: 0.9rem; color: #64748b; font-weight: 600;">Average Progress</p>
                        </div>
                    </div>

                    <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.02); border: 1px solid #eef2f6; display: flex; align-items: center; gap: 1.2rem; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                        <div style="width: 54px; height: 54px; border-radius: 14px; background: #fffbeb; color: #f59e0b; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        </div>
                        <div>
                            <h3 style="margin: 0 0 0.2rem 0; font-size: 1.6rem; font-weight: 800; color: #1e293b; line-height: 1;"><?= (int) $availableCount ?></h3>
                            <p style="margin: 0; font-size: 0.9rem; color: #64748b; font-weight: 600;">Available Courses</p>
                        </div>
                    </div>
                </div>

                <div class="pro-dashboard-grid">
                    <!-- MAIN COLUMN -->
                    <div class="pro-main-col" style="display: flex; flex-direction: column; gap: 2rem;">
                        
                        <!-- Continue Learning -->
                        <div style="background: white; border-radius: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.03); border: 1px solid #eef2f6; overflow: hidden; padding: 2rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                                <div>
                                    <h3 style="margin: 0; font-size: 1.3rem; color: #1e293b; font-weight: 800;">Continue Learning</h3>
                                    <p style="margin: 0.2rem 0 0; font-size: 0.9rem; color: #64748b;">Jump back into your most recent courses.</p>
                                </div>
                                <a href="<?= APP_ENTRY ?>?url=student/my-courses" style="color: #548CA8; text-decoration: none; font-size: 0.9rem; font-weight: 600; padding: 6px 12px; border-radius: 6px; background: #e9f1fa; transition: background 0.2s;" onmouseover="this.style.background='#d0e3f5'" onmouseout="this.style.background='#e9f1fa'">View All</a>
                            </div>
                            
                            <?php if (!empty($enrollments)): ?>
                                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1.5rem;">
                                    <?php foreach (array_slice($enrollments, 0, 3) as $enrollment): ?>
                                        <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 10px 20px rgba(0,0,0,0.05)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                            <div style="width: 48px; height: 48px; background: #eff6ff; color: #3b82f6; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polygon points="10 8 16 12 10 16 10 8"></polygon></svg>
                                            </div>
                                            <h4 style="margin: 0 0 0.5rem 0; font-size: 1.1rem; font-weight: 700; color: #1e293b;"><?= htmlspecialchars($enrollment['title']) ?></h4>
                                            
                                            <div style="margin: 1rem 0;">
                                                <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                                                    <span style="font-size: 0.8rem; font-weight: 600; color: #64748b;">Progress</span>
                                                    <span style="font-size: 0.8rem; font-weight: 700; color: #3b82f6;"><?= (int) ($enrollment['progress'] ?? 0) ?>%</span>
                                                </div>
                                                <div style="width: 100%; background: #e2e8f0; border-radius: 99px; height: 6px; overflow: hidden;">
                                                    <div style="height: 100%; background: linear-gradient(90deg, #3b82f6, #60a5fa); width: <?= (int) ($enrollment['progress'] ?? 0) ?>%;"></div>
                                                </div>
                                            </div>
                                            
                                            <a href="<?= APP_ENTRY ?>?url=student/course/<?= (int) $enrollment['course_id'] ?>" style="display: block; text-align: center; background: #f1f5f9; color: #475569; text-decoration: none; padding: 10px; border-radius: 8px; font-weight: 600; font-size: 0.9rem; transition: all 0.2s;" onmouseover="this.style.background='#548CA8'; this.style.color='white'" onmouseout="this.style.background='#f1f5f9'; this.style.color='#475569'">Continue Lesson</a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div style="text-align: center; padding: 3rem 1rem; background: #f8fafc; border: 2px dashed #e2e8f0; border-radius: 12px;">
                                    <p style="color: #64748b; margin: 0 0 1rem 0; font-size: 1rem;">You are not enrolled in any courses yet.</p>
                                    <a href="<?= APP_ENTRY ?>?url=courses" class="pro-action-btn primary">Browse Courses</a>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Recommended Courses -->
                        <div style="background: white; border-radius: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.03); border: 1px solid #eef2f6; overflow: hidden; padding: 2rem;">
                            <h3 style="margin: 0 0 1.5rem 0; font-size: 1.3rem; color: #1e293b; font-weight: 800;">Recommended For You</h3>
                            
                            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1.5rem;">
                                <?php if (!empty($allCourses)): ?>
                                    <?php foreach (array_slice($allCourses, 0, 2) as $course): ?>
                                        <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                                            <div style="width: 48px; height: 48px; background: #fff7ed; color: #ea580c; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                            </div>
                                            <h4 style="margin: 0 0 0.5rem 0; font-size: 1.1rem; font-weight: 700; color: #1e293b;"><?= htmlspecialchars($course['title']) ?></h4>
                                            <p style="margin: 0 0 1rem 0; font-size: 0.85rem; color: #64748b;">By <?= htmlspecialchars($course['creator_name'] ?? 'Instructor') ?></p>
                                            
                                            <a href="<?= APP_ENTRY ?>?url=student/course/<?= (int) $course['id'] ?>" style="display: block; text-align: center; background: white; color: #548CA8; text-decoration: none; padding: 8px; border-radius: 8px; font-weight: 600; font-size: 0.9rem; border: 1px solid #548CA8; transition: all 0.2s;" onmouseover="this.style.background='#548CA8'; this.style.color='white'" onmouseout="this.style.background='white'; this.style.color='#548CA8'">View Details</a>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p style="color: #64748b; font-size: 0.9rem;">No recommendations available right now.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- SIDE COLUMN -->
                    <div class="pro-side-col" style="display: flex; flex-direction: column; gap: 2rem;">
                        <!-- Profile Level Card -->
                        <div style="background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%); border-radius: 20px; padding: 2rem; box-shadow: 0 5px 20px rgba(0,0,0,0.03); border: 1px solid #eef2f6; text-align: center;">
                            <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #E19864 0%, #C87B46 100%); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem auto; font-size: 2rem; font-weight: 800; box-shadow: 0 10px 20px rgba(225, 152, 100, 0.2);">
                                <?= strtoupper(substr($userName, 0, 1)) ?>
                            </div>
                            <h3 style="margin: 0 0 0.3rem 0; font-size: 1.4rem; color: #1e293b; font-weight: 800;"><?= htmlspecialchars($userName) ?></h3>
                            <p style="margin: 0 0 1.5rem 0; color: #64748b; font-weight: 600; font-size: 0.95rem;">Student Level <?= max(1, (int) floor(($avgProgress + 20) / 15)) ?></p>
                            
                            <div style="margin-bottom: 1.5rem; text-align: left;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                                    <span style="font-size: 0.8rem; font-weight: 600; color: #64748b;">XP to Next Level</span>
                                    <span style="font-size: 0.8rem; font-weight: 700; color: #E19864;"><?= max(120, $avgProgress * 20) ?> XP</span>
                                </div>
                                <div style="width: 100%; background: #e2e8f0; border-radius: 99px; height: 8px; overflow: hidden;">
                                    <div style="height: 100%; background: linear-gradient(90deg, #E19864, #f9b384); width: <?= max(25, min(95, $avgProgress + 12)) ?>%;"></div>
                                </div>
                            </div>

                            <a href="<?= APP_ENTRY ?>?url=student/profile" style="display: inline-block; width: 100%; background: white; color: #E19864; text-decoration: none; padding: 12px; border-radius: 10px; font-weight: 700; border: 1.5px solid #eef2f6; transition: all 0.2s;" onmouseover="this.style.background='#fff7ed'; this.style.borderColor='#f9b384'" onmouseout="this.style.background='white'; this.style.borderColor='#eef2f6'">Edit Profile</a>
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

    @media (max-width: 992px) {
        .pro-dashboard-grid {
            grid-template-columns: 1fr;
        }
    }
</style>