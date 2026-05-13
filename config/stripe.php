<?php
/**
 * Stripe Payment Configuration
 * 
 * Uses keys from config.php (loaded from .env)
 * Get your keys from: https://dashboard.stripe.com/test/apikeys
 */

if (!defined('STRIPE_PUBLISHABLE_KEY')) {
    define('STRIPE_PUBLISHABLE_KEY', $_ENV['STRIPE_PUBLISHABLE_KEY'] ?? '');
}
if (!defined('STRIPE_SECRET_KEY')) {
    define('STRIPE_SECRET_KEY', $_ENV['STRIPE_SECRET_KEY'] ?? '');
}
define('STRIPE_CURRENCY', 'usd');
define('STRIPE_WEBHOOK_SECRET', '');
define('STRIPE_TEST_MODE', true);