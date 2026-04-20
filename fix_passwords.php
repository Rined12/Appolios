<?php
/**
 * Fix default passwords
 * Run this once to set correct passwords
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

// Generate correct password hashes
$adminHash = password_hash('admin123', PASSWORD_DEFAULT, ['cost' => HASH_COST]);
$studentHash = password_hash('student123', PASSWORD_DEFAULT, ['cost' => HASH_COST]);
$teacherHash = password_hash('teacher123', PASSWORD_DEFAULT, ['cost' => HASH_COST]);

echo "Admin hash: " . $adminHash . "\n";
echo "Student hash: " . $studentHash . "\n";
echo "Teacher hash: " . $teacherHash . "\n";

// Update database
try {
    $db = Database::getInstance()->getConnection();
    
    // Ensure admin exists, then update password
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute(['admin@appolios.com']);
    if (!$stmt->fetch()) {
        $stmt = $db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute(['Admin', 'admin@appolios.com', $adminHash, 'admin']);
        echo "Admin account created!\n";
    } else {
        $stmt = $db->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->execute([$adminHash, 'admin@appolios.com']);
        echo "Admin password updated!\n";
    }

    // Ensure student exists, then update password
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute(['student@appolios.com']);
    if (!$stmt->fetch()) {
        $stmt = $db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute(['John Student', 'student@appolios.com', $studentHash, 'student']);
        echo "Student account created!\n";
    } else {
        $stmt = $db->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->execute([$studentHash, 'student@appolios.com']);
        echo "Student password updated!\n";
    }
    
    // Check if teacher exists, if not create it
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute(['teacher@appolios.com']);
    if (!$stmt->fetch()) {
        $stmt = $db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute(['Teacher Demo', 'teacher@appolios.com', $teacherHash, 'teacher']);
        echo "Teacher account created!\n";
    } else {
        $stmt = $db->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->execute([$teacherHash, 'teacher@appolios.com']);
        echo "Teacher password updated!\n";
    }
    
    echo "\n✅ Passwords fixed! You can now login with:\n";
    echo "Admin: admin@appolios.com / admin123\n";
    echo "Student: student@appolios.com / student123\n";
    echo "Teacher: teacher@appolios.com / teacher123\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
