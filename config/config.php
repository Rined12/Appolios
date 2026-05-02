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
define('APP_ENTRY', APP_URL . '/Controller/index.php');

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
define('APP_QR_SECRET', 'appolios-qr-secret-change-me');
define('APP_LAN_HOST', '');

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