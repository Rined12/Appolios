<?php
/**
 * APPOLIOS - Footer Partial
 * Common footer for all pages
 */
?>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-brand">
                    <h3>APP<span>OLIOS</span></h3>
                    <p>APPOLIOS is a modern online learning platform designed to help students develop skills and access high-quality courses anytime, anywhere.</p>
                    <div class="social-links">
                        <!-- Facebook -->
                        <a href="#" aria-label="Facebook">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
                            </svg>
                        </a>
                        <!-- LinkedIn -->
                        <a href="#" aria-label="LinkedIn">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/>
                                <rect x="2" y="9" width="4" height="12"/>
                                <circle cx="4" cy="4" r="2"/>
                            </svg>
                        </a>
                        <!-- YouTube -->
                        <a href="#" aria-label="YouTube">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"/>
                                <polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <div class="footer-links">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="<?= APP_URL ?>/index.php">Home</a></li>
                        <?php if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']): ?>
                            <li><a href="<?= APP_URL ?>/index.php?url=courses">Courses</a></li>
                        <?php endif; ?>
                        <li><a href="<?= APP_URL ?>/index.php?url=about">About</a></li>
                        <li><a href="<?= APP_URL ?>/index.php?url=contact">Contact</a></li>
                    </ul>
                </div>

                <div class="footer-links">
                    <h4>Legal</h4>
                    <ul>
                        <li><a href="<?= APP_URL ?>/index.php?url=privacy">Privacy Policy</a></li>
                        <li><a href="<?= APP_URL ?>/index.php?url=terms">Terms of Service</a></li>
                    </ul>
                </div>

                <div class="footer-links">
                    <h4>Account</h4>
                    <ul>
                        <li><a href="<?= APP_URL ?>/index.php?url=login">Sign In</a></li>
                        <li><a href="<?= APP_URL ?>/index.php?url=register">Sign Up</a></li>
                        <li><a href="<?= APP_URL ?>/index.php?url=admin/login">Admin Portal</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> APPOLIOS. All rights reserved. E-Learning Platform</p>
            </div>
        </div>
    </footer>

    <?php require __DIR__ . '/confirm_modal.php'; ?>
    <!-- JavaScript (thème clair/sombre : public/js/main.js) -->
    <script src="<?= ASSET_URL ?>/js/confirm-modal.js"></script>
    <script src="<?= ASSET_URL ?>/js/main.js"></script>
</body>
</html>