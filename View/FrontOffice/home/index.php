<?php
/**
 * APPOLIOS - Home Page (Login Theme - Light Mode - Logo Colors)
 */
?>

<style>
    /* Ensure body uses the light theme with logo colors */
    body {
        background: radial-gradient(1100px 520px at -5% -20%, rgba(84, 140, 168, 0.12), transparent 58%),
            radial-gradient(900px 420px at 100% -18%, rgba(225, 152, 100, 0.1), transparent 60%),
            #f8fafc !important;
        color: #1e293b !important;
    }
    
    .home-lite-page {
        display: none !important;
    }

    .neo-home-hero-wrap {
        min-height: calc(100vh - 80px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
    }

    /* Light Mode Glass Card for Home Page Hero */
    .neo-home-glass {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.8);
        border-radius: 24px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
        width: 100%;
        max-width: 1200px;
        overflow: hidden;
        display: grid;
        grid-template-columns: 1.1fr 1fr;
        gap: 0;
    }

    @media (max-width: 900px) {
        .neo-home-glass {
            grid-template-columns: 1fr;
        }
    }

    .neo-home-content {
        padding: 4rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .neo-home-visual {
        background: linear-gradient(135deg, rgba(84, 140, 168, 0.05) 0%, rgba(225, 152, 100, 0.05) 100%);
        padding: 4rem;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    .neo-home-visual img {
        width: 100%;
        max-width: 450px;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        position: relative;
        z-index: 2;
    }

    .neo-home-visual::before {
        content: '';
        position: absolute;
        width: 300px;
        height: 300px;
        background: #548CA8;
        filter: blur(100px);
        opacity: 0.15;
        border-radius: 50%;
        z-index: 1;
    }

    .neo-section {
        max-width: 1200px;
        margin: 0 auto;
        padding: 6rem 2rem;
    }

    .neo-section-title {
        text-align: center;
        margin-bottom: 4rem;
    }

    .neo-section-title h2 {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 1rem;
        color: #0f172a;
    }

    .neo-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
    }

    /* Light mode stat card */
    .neo-stat-card {
        background: rgba(255, 255, 255, 0.8);
        border: 1px solid rgba(148, 163, 184, 0.2);
        padding: 3rem 2rem;
        border-radius: 20px;
        text-align: center;
        transition: transform 0.3s, box-shadow 0.3s;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.02);
    }

    .neo-stat-card:hover {
        transform: translateY(-5px);
        background: #ffffff;
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.06);
    }

    .neo-course-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 2.5rem;
    }

    /* Light mode course card */
    .neo-course-card {
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid rgba(148, 163, 184, 0.2);
        border-radius: 20px;
        overflow: hidden;
        transition: all 0.3s;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.03);
    }

    .neo-course-card:hover {
        transform: translateY(-10px);
        border-color: rgba(84, 140, 168, 0.3);
        box-shadow: 0 20px 40px rgba(0,0,0,0.08);
    }
    
    .neo-text-muted {
        color: #64748b;
    }
</style>

<div class="neo-home-hero-wrap">
    <div class="neo-home-glass">
        <div class="neo-home-content">
            <span style="align-self: flex-start; margin-bottom: 1.5rem; padding: 6px 12px; border-radius: 8px; background: rgba(84, 140, 168, 0.1); color: #548CA8; border: 1px solid rgba(84, 140, 168, 0.2); font-weight: 700; font-size: 0.85rem;">eLearning Platform</span>
            <h1 style="font-size: clamp(2.5rem, 4vw, 3.5rem); font-weight: 800; line-height: 1.1; margin-bottom: 1.5rem; color: #0f172a;">
                Smart Learning<br>Deeper & More <span style="color: #E19864;">Amazing</span>
            </h1>
            <p class="neo-text-muted" style="font-size: 1.1rem; line-height: 1.6; margin-bottom: 2.5rem;">
                Explore practical courses, mentor support, and project-based journeys designed to help you learn faster and retain more.
            </p>
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <a href="<?= APP_ENTRY ?>?url=register" class="neo-btn neo-btn-primary" style="padding: 12px 24px; font-size: 1rem; background: #548CA8; color: white; border-radius: 8px; text-decoration: none; font-weight: 600; box-shadow: 0 4px 10px rgba(84, 140, 168, 0.3); transition: all 0.2s;" onmouseover="this.style.background='#355C7D'" onmouseout="this.style.background='#548CA8'">Start Free Trial</a>
                <a href="<?= APP_ENTRY ?>?url=courses" class="neo-btn" style="padding: 12px 24px; font-size: 1rem; background: transparent; border: 2px solid #548CA8; color: #548CA8; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.2s;" onmouseover="this.style.background='rgba(84, 140, 168, 0.05)'" onmouseout="this.style.background='transparent'">How it Works</a>
            </div>
        </div>
        <div class="neo-home-visual">
            <img src="<?= APP_URL ?>/View/assets/images/about/13.jpg" alt="Student learning">
        </div>
    </div>
</div>

<div class="neo-section">
    <div class="neo-section-title">
        <span style="background: rgba(225, 152, 100, 0.1); color: #E19864; border: 1px solid rgba(225, 152, 100, 0.2); padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 1rem; display: inline-block;">About Us</span>
        <h2>Empowering Learners Worldwide</h2>
        <p class="neo-text-muted" style="max-width: 600px; margin: 0 auto; font-size: 1.1rem;">We are passionate about providing high-quality, accessible and engaging education for everyone.</p>
    </div>
    
    <div class="neo-stats-grid">
        <div class="neo-stat-card">
            <div style="font-size: 3.5rem; font-weight: 800; color: #548CA8; margin-bottom: 0.5rem; line-height: 1;">25+</div>
            <div class="neo-text-muted" style="font-size: 1.1rem; font-weight: 600;">Years Experience</div>
        </div>
        <div class="neo-stat-card">
            <div style="font-size: 3.5rem; font-weight: 800; color: #E19864; margin-bottom: 0.5rem; line-height: 1;">56k</div>
            <div class="neo-text-muted" style="font-size: 1.1rem; font-weight: 600;">Students Enrolled</div>
        </div>
        <div class="neo-stat-card">
            <div style="font-size: 3.5rem; font-weight: 800; color: #548CA8; margin-bottom: 0.5rem; line-height: 1;">170+</div>
            <div class="neo-text-muted" style="font-size: 1.1rem; font-weight: 600;">Expert Mentors</div>
        </div>
    </div>
</div>

<div class="neo-section" style="padding-top: 0;">
    <div class="neo-section-title">
        <span style="background: rgba(84, 140, 168, 0.1); color: #548CA8; border: 1px solid rgba(84, 140, 168, 0.2); padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 1rem; display: inline-block;">Our Courses</span>
        <h2>Explore Top Courses</h2>
    </div>

    <div class="neo-course-grid">
        <div class="neo-course-card">
            <div style="height: 200px; position: relative;">
                <img src="<?= APP_URL ?>/View/assets/images/courses/4by3/01.jpg" style="width: 100%; height: 100%; object-fit: cover;" alt="Course">
                <div style="position: absolute; top: 15px; right: 15px; background: rgba(255,255,255,0.9); backdrop-filter: blur(4px); padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 700; color: #0f172a; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">Development</div>
            </div>
            <div style="padding: 2rem;">
                <h3 style="font-size: 1.25rem; margin-bottom: 1rem; color: #0f172a;">Frontend Development Mastery</h3>
                <p class="neo-text-muted" style="margin-bottom: 1.5rem; font-size: 0.95rem; line-height: 1.5;">Master modern HTML, CSS, and JavaScript through real UI building projects.</p>
                <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid rgba(148, 163, 184, 0.2); padding-top: 1.5rem;">
                    <span style="color: #E19864; font-weight: 800;">Free</span>
                    <a href="<?= APP_ENTRY ?>?url=courses" style="color: #548CA8; text-decoration: none; font-weight: 700; font-size: 0.9rem;">View Details &rarr;</a>
                </div>
            </div>
        </div>

        <div class="neo-course-card">
            <div style="height: 200px; position: relative;">
                <img src="<?= APP_URL ?>/View/assets/images/courses/4by3/03.jpg" style="width: 100%; height: 100%; object-fit: cover;" alt="Course">
                <div style="position: absolute; top: 15px; right: 15px; background: rgba(255,255,255,0.9); backdrop-filter: blur(4px); padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 700; color: #0f172a; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">Data Science</div>
            </div>
            <div style="padding: 2rem;">
                <h3 style="font-size: 1.25rem; margin-bottom: 1rem; color: #0f172a;">Data Analysis Foundations</h3>
                <p class="neo-text-muted" style="margin-bottom: 1.5rem; font-size: 0.95rem; line-height: 1.5;">Learn dashboards, metrics and storytelling techniques for impactful decisions.</p>
                <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid rgba(148, 163, 184, 0.2); padding-top: 1.5rem;">
                    <span style="color: #E19864; font-weight: 800;">Free</span>
                    <a href="<?= APP_ENTRY ?>?url=courses" style="color: #548CA8; text-decoration: none; font-weight: 700; font-size: 0.9rem;">View Details &rarr;</a>
                </div>
            </div>
        </div>

        <div class="neo-course-card">
            <div style="height: 200px; position: relative;">
                <img src="<?= APP_URL ?>/View/assets/images/courses/4by3/06.jpg" style="width: 100%; height: 100%; object-fit: cover;" alt="Course">
                <div style="position: absolute; top: 15px; right: 15px; background: rgba(255,255,255,0.9); backdrop-filter: blur(4px); padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 700; color: #0f172a; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">Productivity</div>
            </div>
            <div style="padding: 2rem;">
                <h3 style="font-size: 1.25rem; margin-bottom: 1rem; color: #0f172a;">Productive Learning Strategy</h3>
                <p class="neo-text-muted" style="margin-bottom: 1.5rem; font-size: 0.95rem; line-height: 1.5;">Build routines, focus systems, and practical methods for long-term growth.</p>
                <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid rgba(148, 163, 184, 0.2); padding-top: 1.5rem;">
                    <span style="color: #E19864; font-weight: 800;">Free</span>
                    <a href="<?= APP_ENTRY ?>?url=courses" style="color: #548CA8; text-decoration: none; font-weight: 700; font-size: 0.9rem;">View Details &rarr;</a>
                </div>
            </div>
        </div>
    </div>
    
    <div style="text-align: center; margin-top: 4rem;">
        <a href="<?= APP_ENTRY ?>?url=courses" class="neo-btn" style="background: white; border: 1px solid rgba(84, 140, 168, 0.5); color: #548CA8; padding: 12px 30px; border-radius: 8px; font-weight: 700; text-decoration: none; box-shadow: 0 4px 10px rgba(0,0,0,0.05); transition: all 0.2s;" onmouseover="this.style.background='rgba(84, 140, 168, 0.05)'" onmouseout="this.style.background='white'">View All Courses</a>
    </div>
</div>
