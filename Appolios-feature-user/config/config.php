<?php
/**
 * APPOLIOS Configuration File
 * Main application configuration settings
 */

// Load .env file
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($key, $value) = array_pad(explode('=', $line, 2), 2, '');
        $key = trim($key);
        $value = trim($value);
        if (!defined($key)) {
            define($key, $value);
        }
        $_ENV[$key] = $value;
        putenv("$key=$value");
    }
}

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

// API Keys (load from environment or use empty default)
define('OPENROUTER_API_KEY', $_ENV['OPENROUTER_API_KEY'] ?? getenv('OPENROUTER_API_KEY') ?: '');
define('OPENROUTER_BASE_URL', 'https://openrouter.ai/api/v1');
define('OPENROUTER_MODEL', 'meta-llama/llama-3.1-8b-instruct');

// Stripe
define('STRIPE_SECRET_KEY', $_ENV['STRIPE_SECRET_KEY'] ?? getenv('STRIPE_SECRET_KEY') ?: '');
define('STRIPE_PUBLISHABLE_KEY', $_ENV['STRIPE_PUBLISHABLE_KEY'] ?? getenv('STRIPE_PUBLISHABLE_KEY') ?: '');

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