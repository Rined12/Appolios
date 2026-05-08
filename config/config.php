<?php
/**
 * APPOLIOS Configuration File
 * Main application configuration settings
 */

// Load environment variables from .env file
function loadEnv($path) {
    if (!file_exists($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '#') === 0) continue; // Skip comments
        if (strpos($line, '=') === false) continue;
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        if (!empty($key) && !isset($_ENV[$key])) {
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}
loadEnv(__DIR__ . '/../.env');

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
define('APP_ENTRY', APP_URL . '/Controller/index.php');

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'appolios_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Mail Configuration (used by MailService)
// Change MAIL_FROM_EMAIL to a real address for production.
define('MAIL_FROM_EMAIL', 'jilenibenhamouda@gmail.com');
define('MAIL_FROM_NAME', 'APPOLIOS Platform');

// Session Configuration
define('SESSION_LIFETIME', 3600); // 1 hour
define('SESSION_NAME', 'APPOLIOS_SESSION');

// Security
define('HASH_COST', 12);

// API Credentials (Loaded from a separate file ignored by Git)
if (file_exists(__DIR__ . '/credentials.php')) {
    require_once __DIR__ . '/credentials.php';
}

// Google reCAPTCHA Configuration
if (!defined('RECAPTCHA_SITE_KEY')) define('RECAPTCHA_SITE_KEY', $_ENV['RECAPTCHA_SITE_KEY'] ?? '');
if (!defined('RECAPTCHA_SECRET_KEY')) define('RECAPTCHA_SECRET_KEY', $_ENV['RECAPTCHA_SECRET_KEY'] ?? '');
define('RECAPTCHA_VERIFY_URL', 'https://www.google.com/recaptcha/api/siteverify');
define('RECAPTCHA_MIN_SCORE', 0.5);

// Gemini AI Configuration
if (!defined('GEMINI_API_KEY')) define('GEMINI_API_KEY', $_ENV['GEMINI_API_KEY'] ?? '');

// Google OAuth Configuration
if (!defined('GOOGLE_CLIENT_ID')) define('GOOGLE_CLIENT_ID', $_ENV['GOOGLE_CLIENT_ID'] ?? '');
if (!defined('GOOGLE_CLIENT_SECRET')) define('GOOGLE_CLIENT_SECRET', $_ENV['GOOGLE_CLIENT_SECRET'] ?? '');
define('GOOGLE_REDIRECT_URL', APP_ENTRY . '?url=auth/google-callback');

// Twilio SMS Configuration (from .env file)
define('TWILIO_SID', $_ENV['TWILIO_SID'] ?? '');
define('TWILIO_TOKEN', $_ENV['TWILIO_TOKEN'] ?? '');
define('TWILIO_FROM_NUMBER', $_ENV['TWILIO_FROM_NUMBER'] ?? '');
define('ADMIN_PHONE_NUMBER', $_ENV['ADMIN_PHONE_NUMBER'] ?? '');

// Debug Mode (Set to false in production)
define('DEBUG_MODE', true);

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