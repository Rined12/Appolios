<?php
/**
 * Stripe Payment Configuration
 * 
 * Test keys for development - replace with live keys in production
 * Get your keys from: https://dashboard.stripe.com/test/apikeys
 */

define('STRIPE_PUBLISHABLE_KEY', getenv('STRIPE_PUBLISHABLE_KEY') ?: '');
define('STRIPE_SECRET_KEY', getenv('STRIPE_SECRET_KEY') ?: '');
define('STRIPE_CURRENCY', 'usd');
define('STRIPE_WEBHOOK_SECRET', '');
define('STRIPE_TEST_MODE', true);