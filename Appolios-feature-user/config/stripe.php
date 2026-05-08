<?php
/**
 * Stripe Payment Configuration
 * 
 * Uses keys from config.php (loaded from .env)
 * Get your keys from: https://dashboard.stripe.com/test/apikeys
 */

define('STRIPE_PUBLISHABLE_KEY', defined('STRIPE_PUBLISHABLE_KEY') ? STRIPE_PUBLISHABLE_KEY : '');
define('STRIPE_SECRET_KEY', defined('STRIPE_SECRET_KEY') ? STRIPE_SECRET_KEY : '');
define('STRIPE_CURRENCY', 'usd');
define('STRIPE_WEBHOOK_SECRET', '');
define('STRIPE_TEST_MODE', true);