<?php
/**
 * APPOLIOS - Contact Page
 */
?>

<section class="home-lite-page" style="padding-top: 120px; padding-bottom: 60px;">
    <div class="home-lite-container">
        <div class="home-lite-hero-copy" style="text-align: center; max-width: 800px; margin: 0 auto; margin-bottom: 60px;">
            <span class="home-lite-kicker" style="justify-content: center;">Get in Touch</span>
            <h1 style="font-size: 3.5rem; line-height: 1.2;">Contact <span style="color: #E19864;">Us</span></h1>
            <p style="font-size: 1.2rem; margin-top: 20px;">Have questions? We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
        </div>

        <?php if (!empty($flash_banner)): ?>
            <div style="max-width: 1100px; margin: 0 auto 30px auto; padding: 15px 20px; border-radius: 10px; font-weight: 500; <?= $flash_banner['inner_style'] ?>">
                <?= htmlspecialchars($flash_banner['message']) ?>
            </div>
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 50px; max-width: 1100px; margin: 0 auto;">
            <!-- Contact Form -->
            <div class="neo-glass-card" style="background: #ffffff; padding: 40px; border-radius: 20px; box-shadow: 0 15px 40px rgba(43, 72, 101, 0.08); border: 1px solid rgba(233, 241, 250, 0.8);">
                <h2 style="color: #2B4865; font-size: 2rem; margin-bottom: 30px; font-weight: 800;">Send a Message</h2>
                <form action="<?= APP_ENTRY ?>?url=submit-contact" method="POST" style="display: flex; flex-direction: column; gap: 20px;" novalidate>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <label for="name" style="display: block; color: #475569; font-weight: 700; margin-bottom: 8px;">Your Name *</label>
                            <input type="text" id="name" name="name" placeholder="John Doe" data-js-required="1" style="width: 100%; background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 12px 16px; font-size: 1rem; color: #1e293b; outline: none; transition: all 0.2s ease;">
                        </div>
                        <div>
                            <label for="email" style="display: block; color: #475569; font-weight: 700; margin-bottom: 8px;">Email Address *</label>
                            <input type="text" id="email" name="email" placeholder="john@example.com" data-js-required="1" style="width: 100%; background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 12px 16px; font-size: 1rem; color: #1e293b; outline: none; transition: all 0.2s ease;">
                        </div>
                    </div>
                    <div>
                        <label for="subject" style="display: block; color: #475569; font-weight: 700; margin-bottom: 8px;">Subject *</label>
                        <input type="text" id="subject" name="subject" placeholder="How can we help?" data-js-required="1" style="width: 100%; background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 12px 16px; font-size: 1rem; color: #1e293b; outline: none; transition: all 0.2s ease;">
                    </div>
                    <div>
                        <label for="message" style="display: block; color: #475569; font-weight: 700; margin-bottom: 8px;">Message *</label>
                        <textarea id="message" name="message" placeholder="Write your message here..." data-js-required="1" style="width: 100%; background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 12px 16px; font-size: 1rem; color: #1e293b; outline: none; transition: all 0.2s ease; min-height: 150px; resize: vertical;"></textarea>
                    </div>
                    <button type="submit" style="background: #E19864; color: #fff; border: none; width: 100%; padding: 16px; border-radius: 10px; font-weight: 700; font-size: 1.1rem; cursor: pointer; transition: all 0.2s ease; margin-top: 10px;">Send Message</button>
                </form>
            </div>

            <!-- Contact Info -->
            <div style="display: flex; flex-direction: column; gap: 25px;">
                <div class="neo-glass-card" style="background: #fff; padding: 30px; border-radius: 20px; border: 1px solid #eef2f6; box-shadow: 0 10px 30px rgba(0,0,0,0.03); display: flex; align-items: flex-start; gap: 20px;">
                    <div style="width: 50px; height: 50px; background: #eef2f6; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #2B4865; flex-shrink: 0;">
                        <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    </div>
                    <div>
                        <h3 style="font-size: 1.3rem; color: #2B4865; margin-bottom: 8px; font-weight: 700;">Our Location</h3>
                        <p style="color: #64748b; line-height: 1.6; margin: 0;">123 Education Street<br>Learning City, ED 12345<br>United States</p>
                    </div>
                </div>

                <div class="neo-glass-card" style="background: #fff; padding: 30px; border-radius: 20px; border: 1px solid #eef2f6; box-shadow: 0 10px 30px rgba(0,0,0,0.03); display: flex; align-items: flex-start; gap: 20px;">
                    <div style="width: 50px; height: 50px; background: #fff7ed; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #E19864; flex-shrink: 0;">
                        <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                    </div>
                    <div>
                        <h3 style="font-size: 1.3rem; color: #2B4865; margin-bottom: 8px; font-weight: 700;">Email Us</h3>
                        <p style="color: #64748b; line-height: 1.6; margin: 0;">General: info@appolios.com<br>Support: support@appolios.com</p>
                    </div>
                </div>

                <div class="neo-glass-card" style="background: #fff; padding: 30px; border-radius: 20px; border: 1px solid #eef2f6; box-shadow: 0 10px 30px rgba(0,0,0,0.03); display: flex; align-items: flex-start; gap: 20px;">
                    <div style="width: 50px; height: 50px; background: #f0fdf4; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #10b981; flex-shrink: 0;">
                        <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                    </div>
                    <div>
                        <h3 style="font-size: 1.3rem; color: #2B4865; margin-bottom: 8px; font-weight: 700;">Call Us</h3>
                        <p style="color: #64748b; line-height: 1.6; margin: 0;">+1 (555) 123-4567<br>Mon - Fri: 9:00 AM - 5:00 PM</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    input:focus, textarea:focus {
        border-color: #E19864 !important;
        box-shadow: 0 0 0 4px rgba(225, 152, 100, 0.1) !important;
    }
    button[type="submit"]:hover {
        box-shadow: 0 8px 25px rgba(225, 152, 100, 0.3);
        transform: translateY(-2px);
    }
    @media (max-width: 900px) {
        .home-lite-container > div:nth-child(2) {
            grid-template-columns: 1fr !important;
        }
        form > div:first-child {
            grid-template-columns: 1fr !important;
        }
    }
</style>