<?php
/**
 * APPOLIOS - Home Page (Landing Page)
 */
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <div class="hero-text">
                <h1>Welcome to APPOLIOS E-Learning Platform</h1>
                <p>APPOLIOS is an online learning platform designed to help students develop skills, access high quality courses, and learn anytime anywhere.</p>
                <a href="<?= APP_URL ?>/index.php?url=register" class="btn btn-yellow btn-block" style="max-width: 250px;">Start Learning</a>
            </div>
            <div class="hero-image">
                <div class="hero-illustration">
                    <!-- Education Illustration SVG -->
                    <svg viewBox="0 0 200 200" width="300" height="300" fill="white" opacity="0.9">
                        <!-- Graduation Cap -->
                        <path d="M100 40 L20 80 L100 120 L180 80 Z" fill="currentColor" opacity="0.3"/>
                        <rect x="90" y="80" width="20" height="60" fill="currentColor" opacity="0.3"/>
                        <circle cx="100" cy="140" r="10" fill="currentColor" opacity="0.3"/>
                        <!-- Book -->
                        <rect x="50" y="150" width="100" height="30" rx="3" fill="currentColor" opacity="0.4"/>
                        <line x1="100" y1="150" x2="100" y2="180" stroke="currentColor" stroke-width="2" opacity="0.5"/>
                        <!-- Screen/E-learning -->
                        <rect x="60" y="60" width="80" height="50" rx="5" fill="none" stroke="currentColor" stroke-width="3" opacity="0.5"/>
                        <circle cx="100" cy="85" r="8" fill="currentColor" opacity="0.5"/>
                        <polygon points="97,82 103,85 97,88" fill="white"/>
                    </svg>
                    <p style="margin-top: 20px; opacity: 0.7;">Online Learning Platform</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Why APPOLIOS Section -->
<section class="section why-section">
    <div class="container">
        <div class="section-title">
            <h2>Why APPOLIOS?</h2>
            <p>Discover why thousands of students choose APPOLIOS for their learning journey</p>
        </div>

        <div class="cards-grid">
            <div class="card">
                <div class="card-icon">
                    <svg viewBox="0 0 24 24" fill="white">
                        <path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/>
                        <path d="M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/>
                    </svg>
                </div>
                <h3>Quality Courses</h3>
                <p>Access structured and professional courses designed by industry experts to help you achieve your goals.</p>
            </div>

            <div class="card">
                <div class="card-icon">
                    <svg viewBox="0 0 24 24" fill="white">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 0 1 0-5 2.5 2.5 0 0 1 0 5z"/>
                    </svg>
                </div>
                <h3>Learn Anywhere</h3>
                <p>Study anytime from any device. Our platform is fully responsive and accessible from desktop, tablet, or mobile.</p>
            </div>

            <div class="card">
                <div class="card-icon">
                    <svg viewBox="0 0 24 24" fill="white">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                </div>
                <h3>Expert Teachers</h3>
                <p>Learn from experienced instructors who are passionate about teaching and dedicated to your success.</p>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="section">
    <div class="container">
        <div class="section-title">
            <h2>Platform Features</h2>
            <p>Everything you need for a complete learning experience</p>
        </div>

        <div class="features-grid">
            <div class="feature-item">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="white">
                        <path d="M4 4h16v12H4V4zm2 14h12v2H6v-2z"/>
                    </svg>
                </div>
                <div class="feature-content">
                    <h4>Video Courses</h4>
                    <p>High-quality video content with downloadable resources and captions.</p>
                </div>
            </div>

            <div class="feature-item">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="white">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                    </svg>
                </div>
                <div class="feature-content">
                    <h4>Progress Tracking</h4>
                    <p>Track your learning progress and resume where you left off.</p>
                </div>
            </div>

            <div class="feature-item">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="white">
                        <path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm-2 14l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/>
                    </svg>
                </div>
                <div class="feature-content">
                    <h4>Certificates</h4>
                    <p>Earn certificates upon course completion to showcase your achievements.</p>
                </div>
            </div>

            <div class="feature-item">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="white">
                        <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                    </svg>
                </div>
                <div class="feature-content">
                    <h4>Student Dashboard</h4>
                    <p>Personalized dashboard to manage your courses and track progress.</p>
                </div>
            </div>

            <div class="feature-item">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="white">
                        <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/>
                    </svg>
                </div>
                <div class="feature-content">
                    <h4>Admin Management</h4>
                    <p>Comprehensive admin panel for managing users, courses, and content.</p>
                </div>
            </div>

            <div class="feature-item">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="white">
                        <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/>
                    </svg>
                </div>
                <div class="feature-content">
                    <h4>Secure System</h4>
                    <p>Advanced security measures to protect your data and privacy.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="section" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%); color: white;">
    <div class="container" style="text-align: center;">
        <h2 style="color: white; margin-bottom: 20px;">Ready to Start Learning?</h2>
        <p style="color: rgba(255,255,255,0.9); margin-bottom: 30px; max-width: 600px; margin-left: auto; margin-right: auto;">
            Join thousands of students already learning on APPOLIOS. Create your free account and start your journey today.
        </p>
        <a href="<?= APP_URL ?>/index.php?url=register" class="btn btn-yellow" style="font-size: 1.1rem; padding: 15px 40px;">Get Started for Free</a>
    </div>
</section>