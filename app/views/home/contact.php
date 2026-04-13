<?php
/**
 * APPOLIOS - Contact Page
 */
?>

<section class="hero" style="min-height: auto; padding: 120px 0 60px;">
    <div class="container">
        <div class="hero-text" style="text-align: center; max-width: 800px; margin: 0 auto;">
            <h1>Contact Us</h1>
            <p>Have questions? We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
        </div>
    </div>
</section>

<section class="section" style="padding-top: 40px;">
    <div class="container">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 50px; max-width: 1000px; margin: 0 auto;">
            <!-- Contact Form -->
            <div class="form-container" style="box-shadow: var(--shadow); max-width: none;">
                <h2 style="color: var(--primary-color); margin-bottom: 20px;">Send us a Message</h2>
                <form>
                    <div class="form-group">
                        <label for="name">Your Name</label>
                        <input type="text" id="name" name="name" placeholder="Enter your name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="Enter your email" required>
                    </div>
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" placeholder="What is this about?" required>
                    </div>
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" placeholder="Write your message here..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Send Message</button>
                </form>
            </div>

            <!-- Contact Info -->
            <div>
                <div class="card" style="margin-bottom: 20px;">
                    <h3 style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                        <span style="background: var(--secondary-color); color: white; padding: 10px; border-radius: 50%;">
                            <svg viewBox="0 0 24 24" width="20" height="20" fill="white">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                            </svg>
                        </span>
                        Our Location
                    </h3>
                    <p style="color: var(--gray-dark); margin: 0;">
                        123 Education Street<br>
                        Learning City, ED 12345<br>
                        United States
                    </p>
                </div>

                <div class="card" style="margin-bottom: 20px;">
                    <h3 style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                        <span style="background: var(--secondary-color); color: white; padding: 10px; border-radius: 50%;">
                            <svg viewBox="0 0 24 24" width="20" height="20" fill="white">
                                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                            </svg>
                        </span>
                        Email Us
                    </h3>
                    <p style="color: var(--gray-dark); margin: 0;">
                        General: info@appolios.com<br>
                        Support: support@appolios.com
                    </p>
                </div>

                <div class="card">
                    <h3 style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                        <span style="background: var(--secondary-color); color: white; padding: 10px; border-radius: 50%;">
                            <svg viewBox="0 0 24 24" width="20" height="20" fill="white">
                                <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                            </svg>
                        </span>
                        Call Us
                    </h3>
                    <p style="color: var(--gray-dark); margin: 0;">
                        +1 (555) 123-4567<br>
                        Mon - Fri: 9:00 AM - 5:00 PM
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    @media (max-width: 768px) {
        .container > div {
            grid-template-columns: 1fr !important;
        }
    }
</style>