<?php
/**
 * APPOLIOS - 404 Error Page
 */
?>

<div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);">
    <div style="text-align: center; padding: 40px; color: white;">
        <h1 style="font-size: 8rem; margin: 0; opacity: 0.8;">404</h1>
        <h2 style="font-size: 2rem; margin: 20px 0;">Page Not Found</h2>
        <p style="font-size: 1.1rem; opacity: 0.9; margin-bottom: 30px;">
            The page you are looking for does not exist or has been moved.
        </p>
        <a href="<?= APP_URL ?>" class="btn btn-yellow" style="padding: 15px 30px; font-size: 1rem;">
            Go Back Home
        </a>
    </div>
</div>