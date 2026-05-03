<?php
/**
 * APPOLIOS - About Page
 */
?>

<!-- Hero Section -->
<div class="section home-lite-page" style="padding-top: 120px; padding-bottom: 60px;">
    <div class="home-lite-container">
        <div class="home-lite-hero-copy" style="text-align: center; max-width: 800px; margin: 0 auto; margin-bottom: 60px;">
            <span class="home-lite-kicker" style="justify-content: center;">Our Story</span>
            <h1 style="font-size: 3.5rem; line-height: 1.2;">About <span style="color: #E19864;">APPOLIOS</span></h1>
            <p style="font-size: 1.2rem; margin-top: 20px;">Empowering learners worldwide with quality education accessible anytime, anywhere.</p>
        </div>

        <div class="neo-glass-card" style="padding: 40px; margin-bottom: 60px; background: #ffffff; border-radius: 20px; box-shadow: 0 15px 40px rgba(43, 72, 101, 0.08); border: 1px solid rgba(233, 241, 250, 0.8);">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: center;">
                <div>
                    <h2 style="color: #2B4865; font-size: 2.2rem; margin-bottom: 20px; font-weight: 800;">Our Mission</h2>
                    <p style="font-size: 1.1rem; line-height: 1.8; color: #64748b; margin-bottom: 20px;">
                        APPOLIOS is a modern online learning platform designed to provide high-quality education to students around the world.
                        We believe that everyone deserves access to quality education, regardless of their location or background.
                    </p>
                    <p style="font-size: 1.1rem; line-height: 1.8; color: #64748b;">
                        Our platform connects students with expert instructors and provides a structured learning experience
                        that helps you achieve your educational and career goals.
                    </p>
                </div>
                <div style="position: relative; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.1);">
                    <div style="position: absolute; inset: 0; background: linear-gradient(135deg, rgba(225,152,100,0.2) 0%, rgba(43,72,101,0.2) 100%); z-index: 1;"></div>
                    <img src="<?= APP_URL ?>/View/assets/images/about/13.jpg" alt="Our Mission" style="width: 100%; height: 100%; object-fit: cover; display: block; border-radius: 20px;">
                </div>
            </div>
        </div>

        <div style="text-align: center; margin-bottom: 40px;">
            <span class="home-lite-label home-lite-label-orange" style="justify-content: center; margin: 0 auto 15px auto;">Values</span>
            <h2 style="font-size: 2.5rem; color: #2B4865; font-weight: 800;">What drives us every day</h2>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; margin-bottom: 80px;">
            <div class="neo-glass-card" style="background: #fff; padding: 40px 30px; border-radius: 20px; text-align: center; border: 1px solid #eef2f6; transition: transform 0.3s ease; box-shadow: 0 10px 30px rgba(0,0,0,0.03);" onmouseover="this.style.transform='translateY(-10px)'" onmouseout="this.style.transform='translateY(0)'">
                <div style="width: 70px; height: 70px; background: #eef2f6; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px auto; color: #2B4865;">
                    <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                </div>
                <h3 style="font-size: 1.5rem; color: #2B4865; margin-bottom: 15px; font-weight: 700;">Security</h3>
                <p style="color: #64748b; line-height: 1.6;">Your data and privacy are protected with industry-standard security measures.</p>
            </div>

            <div class="neo-glass-card" style="background: #fff; padding: 40px 30px; border-radius: 20px; text-align: center; border: 1px solid #eef2f6; transition: transform 0.3s ease; box-shadow: 0 10px 30px rgba(0,0,0,0.03);" onmouseover="this.style.transform='translateY(-10px)'" onmouseout="this.style.transform='translateY(0)'">
                <div style="width: 70px; height: 70px; background: #fff7ed; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px auto; color: #E19864;">
                    <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                </div>
                <h3 style="font-size: 1.5rem; color: #2B4865; margin-bottom: 15px; font-weight: 700;">Quality</h3>
                <p style="color: #64748b; line-height: 1.6;">All our courses are carefully curated and taught by experienced professionals.</p>
            </div>

            <div class="neo-glass-card" style="background: #fff; padding: 40px 30px; border-radius: 20px; text-align: center; border: 1px solid #eef2f6; transition: transform 0.3s ease; box-shadow: 0 10px 30px rgba(0,0,0,0.03);" onmouseover="this.style.transform='translateY(-10px)'" onmouseout="this.style.transform='translateY(0)'">
                <div style="width: 70px; height: 70px; background: #f0fdf4; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px auto; color: #10b981;">
                    <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                </div>
                <h3 style="font-size: 1.5rem; color: #2B4865; margin-bottom: 15px; font-weight: 700;">Community</h3>
                <p style="color: #64748b; line-height: 1.6;">Join a global community of learners and share knowledge with peers worldwide.</p>
            </div>
        </div>

        <div class="neo-glass-card" style="background: #2B4865; padding: 60px; border-radius: 20px; text-align: center; color: white; position: relative; overflow: hidden;">
            <div style="position: absolute; top: -50px; left: -50px; width: 200px; height: 200px; background: rgba(225, 152, 100, 0.2); border-radius: 50%; z-index: 0;"></div>
            <div style="position: absolute; bottom: -50px; right: -50px; width: 200px; height: 200px; background: rgba(255, 255, 255, 0.1); border-radius: 50%; z-index: 0;"></div>
            
            <div style="position: relative; z-index: 1;">
                <h2 style="font-size: 2.5rem; font-weight: 800; margin-bottom: 20px;">Ready to start learning?</h2>
                <p style="font-size: 1.2rem; color: #cbd5e1; margin-bottom: 30px; max-width: 600px; margin-left: auto; margin-right: auto;">
                    Join thousands of students and start your educational journey with APPOLIOS today.
                </p>
                <a href="<?= APP_ENTRY ?>?url=register" class="home-lite-btn home-lite-btn-primary" style="background: #E19864; color: white; border: none; padding: 15px 30px; font-size: 1.1rem;">Join Now</a>
            </div>
        </div>
    </div>
</div>

<style>
    @media (max-width: 768px) {
        .neo-glass-card > div {
            grid-template-columns: 1fr !important;
        }
    }
</style>