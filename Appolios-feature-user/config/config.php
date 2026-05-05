<?php
/**
 * APPOLIOS Configuration File
 * Main application configuration settings
 */

// Application Settings
define('APP_NAME', 'APPOLIOS');
define('APP_VERSION', '1.0.0');

// Build APP_URL dynamically so it works on XAMPP subfolders and php -S.
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$scriptDir = rtrim($scriptDir, '/');

// If front controller is under /public or /Controller, expose app root for assets/views URLs.
$appBaseDir = preg_replace('#/(public|Controller)$#', '', $scriptDir) ?? $scriptDir;

define('APP_URL', $scheme . '://' . $host . $appBaseDir);
define('APP_ENTRY', APP_URL . '/index.php');

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'appolios_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Session Configuration
define('SESSION_LIFETIME', 3600); // 1 hour
define('SESSION_NAME', 'APPOLIOS_SESSION');

// Security
define('HASH_COST', 12);

// Debug Mode (Set to false in production)
define('DEBUG_MODE', true);

// API Keys
define('OPENROUTER_API_KEY', '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');
define('OPENROUTER_BASE_URL', 'https://openrouter.ai/api/v1');
define('OPENROUTER_MODEL', 'meta-llama/llama-3.1-8b-instruct');

// Stripe
define('STRIPE_SECRET_KEY', 'sk_test_51TSzdc4BXwgkHYpR2tFlRqJMpyQR2YnNEQzl5MFrYqRGYOVJ0uBDM80N8j4RHfl1fMMWpJb2S5Apye7ysRZipaYs00y7Woc3Lw');
define('STRIPE_PUBLISHABLE_KEY', 'pk_test_51TSzdc4BXwgkHYpRPmNplrXhsqEXTquOrN4gU23D033OfrXCZBv9d7OPhmnMqZYi8XeKlMAC6UXVIcBwApOqNBTa00Knh5852v');

// Timezone
date_default_timezone_set('UTC');

// Error Reporting
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}