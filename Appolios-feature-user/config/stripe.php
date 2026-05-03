<?php
/**
 * Stripe Payment Configuration
 * 
 * To configure: Set environment variables:
 * - STRIPE_PUBLISHABLE_KEY
 * - STRIPE_SECRET_KEY
 * - STRIPE_WEBHOOK_SECRET (optional)
 */

define('STRIPE_PUBLISHABLE_KEY', getenv('STRIPE_PUBLISHABLE_KEY') ?: '');
define('STRIPE_SECRET_KEY', getenv('STRIPE_SECRET_KEY') ?: '');
define('STRIPE_CURRENCY', 'usd');
define('STRIPE_WEBHOOK_SECRET', getenv('STRIPE_WEBHOOK_SECRET') ?: '');
define('STRIPE_TEST_MODE', true);