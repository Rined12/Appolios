<?php
/**
 * Setup Script - Add Teacher Role and Create Sample Teacher
 * Run this script to update the database structure
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

echo "=== APPOLIOS Teacher Role Setup ===\n\n";

try {
    $db = Database::getInstance();
    
    // 1. Check current ENUM values
    echo "1. Checking current role ENUM values...\n";
    $stmt = $db->query("SHOW COLUMNS FROM users WHERE Field = 'role'");
    $column = $stmt->fetch();
    echo "   Current: " . $column['Type'] . "\n";
    
    // 2. Modify the ENUM to include 'teacher'
    echo "\n2. Modifying role column to include 'teacher'...\n";
    $db->exec("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'student', 'teacher') DEFAULT 'student'");
    echo "   ✓ Column modified successfully!\n";
    
    // 3. Check if sample teacher exists
    echo "\n3. Checking for sample teacher account...\n";
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute(['teacher@appolios.com']);
    
    if (!$stmt->fetch()) {
        echo "   Creating sample teacher account...\n";
        
        // Generate correct password hash
        $teacherHash = password_hash('teacher123', PASSWORD_DEFAULT, ['cost' => HASH_COST]);
        
        $stmt = $db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute(['Teacher Demo', 'teacher@appolios.com', $teacherHash, 'teacher']);
        
        echo "   ✓ Sample teacher created!\n";
        echo "   Email: teacher@appolios.com\n";
        echo "   Password: teacher123\n";
    } else {
        echo "   Sample teacher already exists.\n";
    }
    
    // 4. Verify the setup
    echo "\n4. Verifying setup...\n";
    $stmt = $db->query("SELECT COUNT(*) as count FROM users WHERE role = 'teacher'");
    $result = $stmt->fetch();
    echo "   Total teachers: " . $result['count'] . "\n";
    
    echo "\n=== Setup Complete! ===\n";
    echo "\nYou can now:\n";
    echo "1. Login as teacher: teacher@appolios.com / teacher123\n";
    echo "2. Admin can create more teachers at: Admin Dashboard > Manage Teachers\n";
    echo "3. Teachers can create and manage their own courses\n";
    
} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Please check your database configuration.\n";
}
