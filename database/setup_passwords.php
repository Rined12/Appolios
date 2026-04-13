<?php
/**
 * APPOLIOS - Password Setup Script
 *
 * Run this script ONCE after importing the database to set correct password hashes.
 * Access via browser: http://localhost/projetWeb/APPOLIOS/database/setup_passwords.php
 *
 * DELETE THIS FILE after running!
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'appolios_db');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    // Generate password hashes
    $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $studentPassword = password_hash('student123', PASSWORD_DEFAULT);

    // Update passwords
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->execute([$adminPassword, 'admin@appolios.com']);
    $stmt->execute([$studentPassword, 'student@appolios.com']);

    echo "<h1 style='color: green;'>Passwords set successfully!</h1>";
    echo "<p><strong>Admin Login:</strong><br>Email: admin@appolios.com<br>Password: admin123</p>";
    echo "<p><strong>Student Login:</strong><br>Email: student@appolios.com<br>Password: student123</p>";
    echo "<p style='color: red; font-weight: bold;'>IMPORTANT: Delete this file now!</p>";

} catch (PDOException $e) {
    echo "<h1 style='color: red;'>Database Error:</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<p>Make sure you have imported the database schema first.</p>";
}
?>