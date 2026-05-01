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

            <div class="admin-main">
                
                <!-- Welcome Banner -->
                <section class="student-events-hero-top" style="margin-bottom: 2rem;">
                    <div class="student-events-hero-copy">
                        <span class="student-events-hero-kicker">Student Space</span>
                        <h1>Dashboard</h1>
                        <p>Welcome back, <strong><?= htmlspecialchars($userName) ?></strong>. Track your progress and keep learning.</p>
                        
                        <div style="margin-top: 1.5rem;">
                            <a href="<?= APP_ENTRY ?>?url=student/courses" class="student-courses-hero-btn student-courses-hero-btn-primary" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; font-size: 0.95rem; border-radius: 8px; text-decoration: none;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                                Explore Courses
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

                <!-- Advanced Statistics with Charts -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                    <?php $enrolled = $enrolledCount ?? 0; $completed = $completedCount ?? 0; $inProgress = $inProgressCount ?? 0; $avgProg = $averageProgress ?? 0; $radius = 35; $circumference = 2 * 3.14159 * $radius; ?>
                    
                    <!-- Progress Chart -->
                    <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.02); border: 1px solid #eef2f6;">
                        <h3 style="margin: 0 0 1rem 0; font-size: 1.1rem; color: #1e293b; font-weight: 700;">Overall Progress</h3>
                        <div style="display: flex; align-items: center; justify-content: center; gap: 2rem;">
                            <svg width="100" height="100" viewBox="0 0 100 100">
                                <circle cx="50" cy="50" r="<?= $radius ?>" fill="none" stroke="#e5e7eb" stroke-width="8"/>
                                <circle cx="50" cy="50" r="<?= $radius ?>" fill="none" stroke="#3b82f6" stroke-width="8"
                                    stroke-linecap="round" stroke-dasharray="<?= ($avgProg / 100) * $circumference ?> <?= $circumference ?>" 
                                    stroke-dashoffset="0" transform="rotate(-90 50 50)"
                                    style="transition: stroke-dasharray 0.8s ease;"/>
                            </svg>
                            <div>
                                <p style="font-size: 2rem; font-weight: 800; color: #1e293b; margin: 0;"><?= number_format($avgProg, 1) ?>%</p>
                                <p style="margin: 0; color: #64748b; font-size: 0.9rem;">Average Progress</p>
                            </div>
                        </div>
                    </div>

                    <!-- Enrollment Status Chart -->
                    <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.02); border: 1px solid #eef2f6;">
                        <h3 style="margin: 0 0 1rem 0; font-size: 1.1rem; color: #1e293b; font-weight: 700;">Course Status</h3>
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; background: #eff6ff; border-radius: 8px;">
                                <span style="color: #3b82f6; font-weight: 600;">Enrolled</span>
                                <span style="font-size: 1.25rem; font-weight: 800; color: #1e293b;"><?= $enrolled ?></span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; background: #fef3c7; border-radius: 8px;">
                                <span style="color: #d97706; font-weight: 600;">In Progress</span>
                                <span style="font-size: 1.25rem; font-weight: 800; color: #1e293b;"><?= $inProgress ?></span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; background: #dcfce7; border-radius: 8px;">
                                <span style="color: #16a34a; font-weight: 600;">Completed</span>
                                <span style="font-size: 1.25rem; font-weight: 800; color: #1e293b;"><?= $completed ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.02); border: 1px solid #eef2f6;">
                        <h3 style="margin: 0 0 1rem 0; font-size: 1.1rem; color: #1e293b; font-weight: 700;">Recent Enrollments</h3>
                        <?php if (!empty($progressDetails)): ?>
                            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                                <?php foreach (array_slice($progressDetails, 0, 3) as $detail): ?>
                                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem; border-bottom: 1px solid #f1f5f9;">
                                        <div>
                                            <p style="margin: 0; font-weight: 600; color: #1e293b; font-size: 0.9rem;"><?= htmlspecialchars(substr($detail['title'], 0, 25)) ?>...</p>
                                            <p style="margin: 0; color: #94a3b8; font-size: 0.75rem;"><?= date('M d, Y', strtotime($detail['enrolled_at'])) ?></p>
                                        </div>
                                        <span style="background: <?= $detail['progress'] == 100 ? '#dcfce7' : ($detail['progress'] > 0 ? '#fef3c7' : '#f1f5f9') ?>; color: <?= $detail['progress'] == 100 ? '#16a34a' : ($detail['progress'] > 0 ? '#d97706' : '#64748b') ?>; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: 600;">
                                            <?= $detail['progress'] ?>%
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p style="color: #94a3b8; text-align: center;">No enrollments yet</p>
                        <?php endif; ?>
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
                            
                            <?php 
                            $inProgressEnrollments = array_filter($enrollments ?? [], function($e) {
                                return ($e['progress'] ?? 0) < 100;
                            });
                            ?>
                            <?php if (!empty($inProgressEnrollments)): ?>
                                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1.5rem;">
                                    <?php foreach (array_slice($inProgressEnrollments, 0, 3) as $enrollment): ?>
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

                        <!-- AI Course Recommendations -->
                        <?php if (!empty($recommendations)): ?>
                        <div style="background: white; border-radius: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.03); border: 1px solid #eef2f6; overflow: hidden; padding: 2rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                                <div>
                                    <h3 style="margin: 0; font-size: 1.3rem; color: #1e293b; font-weight: 800;">🤖 Recommended For You</h3>
                                    <p style="margin: 0.2rem 0 0; font-size: 0.9rem; color: #64748b;">AI-powered suggestions based on your learning.</p>
                                </div>
                            </div>
                            
                            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1.5rem;">
                                <?php foreach ($recommendations as $rec): ?>
                                    <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 10px 20px rgba(0,0,0,0.05)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                        <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                                        </div>
                                        <h4 style="margin: 0 0 0.5rem 0; font-size: 1.1rem; font-weight: 700; color: #1e293b;"><?= htmlspecialchars($rec['title']) ?></h4>
                                        <p style="margin: 0 0 0.5rem 0; font-size: 0.8rem; color: #64748b;"><?= htmlspecialchars(substr($rec['description'] ?? '', 0, 60)) ?>...</p>
                                        
                                        <div style="margin: 1rem 0; display: flex; justify-content: space-between; align-items: center;">
                                            <span style="font-size: 0.9rem; font-weight: 700; color: <?= ($rec['price'] ?? 0) > 0 ? '#10b981' : '#3b82f6' ?>;">
                                                <?= ($rec['price'] ?? 0) > 0 ? '$' . number_format($rec['price'], 2) : 'Free' ?>
                                            </span>
                                            <span style="font-size: 0.75rem; color: #64748b;">by <?= htmlspecialchars($rec['creator_name'] ?? 'Instructor') ?></span>
                                        </div>
                                        
                                        <a href="<?= APP_ENTRY ?>?url=student/course/<?= (int) $rec['id'] ?>" style="display: block; text-align: center; background: #667eea; color: white; text-decoration: none; padding: 10px; border-radius: 8px; font-weight: 600; font-size: 0.9rem; transition: all 0.2s;" onmouseover="this.style.background='#5a67d8'" onmouseout="this.style.background='#667eea'">View Course</a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- My Badges -->
                        <?php
                        $completedCourses = 0;
                        $badges = $badges ?? [];
                        
                        // Also include dynamically calculated badges
                        if (!empty($enrollments)) {
                            foreach ($enrollments as $e) {
                                if (($e['progress'] ?? 0) >= 100) {
                                    $completedCourses++;
                                }
                            }
                        }
                        
                        // Check for enroll badge (not in DB yet)
                        if ($enrollmentCount >= 1) {
                            $hasEnrolledBadge = false;
                            foreach ($badges as $b) {
                                if (($b['badge_name'] ?? $b['name'] ?? '') === 'Enrolled') $hasEnrolledBadge = true;
                            }
                            if (!$hasEnrolledBadge) {
                                $badges[] = ['name' => 'Enrolled', 'icon' => '✅', 'desc' => 'Enroll in a course'];
                            }
                        }
                        
                        // Calculate badges from enrollments for display
                        if ($completedCourses >= 1 && empty(array_filter($badges, fn($b) => ($b['badge_name'] ?? $b['name'] ?? '') === 'First Step'))) {
                            $badges[] = ['name' => 'First Step', 'icon' => '🎯', 'desc' => 'Complete your first course'];
                        }
                        if ($completedCourses >= 3 && empty(array_filter($badges, fn($b) => ($b['badge_name'] ?? $b['name'] ?? '') === 'Dedicated Learner'))) {
                            $badges[] = ['name' => 'Dedicated Learner', 'icon' => '📚', 'desc' => 'Complete 3 courses'];
                        }
                        if ($completedCourses >= 5 && empty(array_filter($badges, fn($b) => ($b['badge_name'] ?? $b['name'] ?? '') === 'Knowledge Seeker'))) {
                            $badges[] = ['name' => 'Knowledge Seeker', 'icon' => '🏆', 'desc' => 'Complete 5 courses'];
                        }
                        if ($avgProgress >= 50 && empty(array_filter($badges, fn($b) => ($b['badge_name'] ?? $b['name'] ?? '') === 'Half Way'))) {
                            $badges[] = ['name' => 'Half Way', 'icon' => '🚀', 'desc' => 'Reach 50% average progress'];
                        }
                        ?>
                        <div style="background: white; border-radius: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.03); border: 1px solid #eef2f6; overflow: hidden; padding: 2rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                                <h3 style="margin: 0; font-size: 1.3rem; color: #1e293b; font-weight: 800;">My Achievements</h3>
                                <a href="<?= APP_ENTRY ?>?url=student/evenements" style="color: #548CA8; text-decoration: none; font-size: 0.85rem; font-weight: 600;">View Events →</a>
                            </div>
                            
                            <div style="display: flex; flex-wrap: wrap; gap: 1rem; justify-content: center;">
                                <?php if (!empty($badges)): ?>
                                    <?php foreach ($badges as $badge): ?>
                                        <div style="text-align: center; padding: 1rem; background: linear-gradient(135deg, #fefce8 0%, #fef3c7 100%); border-radius: 12px; min-width: 100px; border: 1px solid #fde047;">
                                            <div style="font-size: 2rem; margin-bottom: 0.5rem;"><?= $badge['icon'] ?? $badge['badge_icon'] ?? '🎖️' ?></div>
                                            <div style="font-size: 0.85rem; font-weight: 700; color: #1e293b;"><?= htmlspecialchars($badge['name'] ?? $badge['badge_name'] ?? '') ?></div>
                                            <div style="font-size: 0.75rem; color: #64748b;"><?= htmlspecialchars($badge['desc'] ?? $badge['badge_description'] ?? '') ?></div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div style="text-align: center; padding: 2rem; background: #f8fafc; border-radius: 12px; width: 100%;">
                                        <div style="font-size: 2rem; margin-bottom: 0.5rem;">🎖️</div>
                                        <p style="color: #64748b; margin: 0;">Start completing courses to earn badges!</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($completedCourses > 0): ?>
                                <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #eef2f6;">
                                    <div style="display: flex; align-items: center; gap: 1rem; justify-content: center;">
                                        <span style="font-size: 1.5rem;">🎓</span>
                                        <span style="font-weight: 700; color: #1e293b;"><?= $completedCourses ?> Course<?= $completedCourses > 1 ? 's' : '' ?> Completed!</span>
                                    </div>
                                </div>
                            <?php endif; ?>
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
                            <p style="margin: 0 0 1.5rem 0; color: #E19864; font-weight: 700; font-size: 1.1rem;">
                                <?= ($levelInfo['icon'] ?? '⭐') ?> <?= $levelInfo['level'] ?? 'Private' ?>
                            </p>
                            <p style="margin: 0 0 1rem 0; color: #64748b; font-size: 0.85rem;">
                                Total XP: <strong style="color: #1e293b;"><?= (int) ($totalXP ?? 0) ?></strong>
                            </p>
                            
                            <div style="margin-bottom: 1.5rem; text-align: left;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                                    <span style="font-size: 0.8rem; font-weight: 600; color: #64748b;">
                                        <?= $levelInfo['next_level'] ? 'XP to ' . $levelInfo['next_level'] : 'Max Level!' ?>
                                    </span>
                                    <span style="font-size: 0.8rem; font-weight: 700; color: #E19864;">
                                        <?= $levelInfo['xp_to_next'] > 0 ? $levelInfo['xp_to_next'] . ' XP' : '---' ?>
                                    </span>
                                </div>
                                <div style="width: 100%; background: #e2e8f0; border-radius: 99px; height: 8px; overflow: hidden;">
                                    <div style="height: 100%; background: linear-gradient(90deg, #E19864, #f9b384); width: <?= $levelInfo['progress'] ?? 0 ?>%;"></div>
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