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

// Default UI language (fr | en | ar) — session override via LanguageController
$rawLocale = trim((string) ($_ENV['DEFAULT_LOCALE'] ?? 'fr'));
define('DEFAULT_LOCALE', in_array($rawLocale, ['fr', 'en', 'ar'], true) ? $rawLocale : 'fr');

// Build APP_URL / APP_ENTRY dynamically so it works on XAMPP subfolders and php -S.
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

// Force a stable entry point: /<app>/Controller/index.php
$scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
if ($scriptName === '') {
    $entryPath = '/Controller/index.php';
} elseif (preg_match('#/Controller/index\\.php$#', $scriptName)) {
    $entryPath = $scriptName;
} else {
    $entryPath = rtrim(str_replace('\\', '/', dirname($scriptName)), '/') . '/Controller/index.php';
}

// App base dir is the parent of /Controller/index.php
$appBaseDir = preg_replace('#/Controller/index\\.php$#', '', $entryPath);
if ($appBaseDir === null) {
    $appBaseDir = '';
}

define('APP_URL', $scheme . '://' . $host . $appBaseDir);
define('APP_ENTRY', $scheme . '://' . $host . $entryPath);

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

// Public contact form: notifications are sent to this inbox (override with CONTACT_INBOX_EMAIL in .env)
define(
    'CONTACT_INBOX_EMAIL',
    !empty(trim((string) ($_ENV['CONTACT_INBOX_EMAIL'] ?? '')))
        ? trim((string) $_ENV['CONTACT_INBOX_EMAIL'])
        : 'appolios2026@gmail.com'
);

// SMTP (PHPMailer). XAMPP's mail() does not relay to Gmail; use SMTP for real delivery.
// Option A: MAIL_HOST, MAIL_USERNAME, MAIL_PASSWORD in .env (optional MAIL_PORT, MAIL_ENCRYPTION).
// Option B: GMAIL_EMAIL + GMAIL_APP_PASSWORD (Google account app password) → uses smtp.gmail.com:587.
$_smtpHost = trim((string) ($_ENV['MAIL_HOST'] ?? ''));
$_smtpUser = trim((string) ($_ENV['MAIL_USERNAME'] ?? ''));
$_smtpPass = trim((string) ($_ENV['MAIL_PASSWORD'] ?? ''));
if ($_smtpHost !== '' && $_smtpUser !== '' && $_smtpPass !== '') {
    // explicit SMTP
} elseif ($_smtpHost === '') {
    $_smtpUser = trim((string) ($_ENV['GMAIL_EMAIL'] ?? ''));
    $_smtpPass = trim((string) ($_ENV['GMAIL_APP_PASSWORD'] ?? ''));
    if ($_smtpUser !== '' && $_smtpPass !== '') {
        $_smtpHost = 'smtp.gmail.com';
    }
}
define(
    'MAIL_USE_SMTP',
    $_smtpHost !== '' && $_smtpUser !== '' && $_smtpPass !== ''
);
if (MAIL_USE_SMTP) {
    define('MAIL_HOST', $_smtpHost);
    define('MAIL_USERNAME', $_smtpUser);
    define('MAIL_PASSWORD', $_smtpPass);
    define('MAIL_PORT', (int) ($_ENV['MAIL_PORT'] ?? 587));
    $_enc = strtolower(trim((string) ($_ENV['MAIL_ENCRYPTION'] ?? 'tls')));
    define('MAIL_ENCRYPTION', $_enc !== '' ? $_enc : 'tls');
}

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
define('RECAPTCHA_VERIFY_URL', 'https://www.google.com/recaptcha/api/siteverify');
define('RECAPTCHA_MIN_SCORE', 0.5);

// Fallback if not defined in credentials.php
if (!defined('RECAPTCHA_SITE_KEY')) {
    define('RECAPTCHA_SITE_KEY', '6LetkuEsAAAAAC4i0WLO_R3JyHfsk3HXj0JiBufp');
} // Minimum score threshold (0.1 - 0.9)

// Google OAuth Configuration
define('GOOGLE_CLIENT_ID', $_ENV['GOOGLE_CLIENT_ID'] ?? 'your_google_client_id_here');
define('GOOGLE_CLIENT_SECRET', $_ENV['GOOGLE_CLIENT_SECRET'] ?? 'your_google_client_secret_here');
define('GOOGLE_REDIRECT_URL', APP_ENTRY . '?url=auth/google-callback');

// Twilio SMS Configuration (from .env file)
define('TWILIO_SID', $_ENV['TWILIO_SID'] ?? '');
define('TWILIO_TOKEN', $_ENV['TWILIO_TOKEN'] ?? '');
define('TWILIO_FROM_NUMBER', $_ENV['TWILIO_FROM_NUMBER'] ?? '');
define('ADMIN_PHONE_NUMBER', $_ENV['ADMIN_PHONE_NUMBER'] ?? '');

// AI API Keys Configuration (from .env file)
define('GEMINI_API_KEY', $_ENV['GEMINI_API_KEY'] ?? '');
define('OPENROUTER_API_KEY', $_ENV['OPENROUTER_API_KEY'] ?? '');

// Stripe Configuration (from .env file)
define('STRIPE_SECRET_KEY', $_ENV['STRIPE_SECRET_KEY'] ?? '');
define('STRIPE_PUBLISHABLE_KEY', $_ENV['STRIPE_PUBLISHABLE_KEY'] ?? '');

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