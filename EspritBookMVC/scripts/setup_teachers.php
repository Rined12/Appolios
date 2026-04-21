<?php
/**
 * Add teacher role ENUM and sample teacher (CLI)
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

$pdo = getConnection();

echo "=== APPOLIOS Teacher Role Setup ===\n\n";

try {
    echo "1. Checking current role ENUM values...\n";
    $stmt = $pdo->query("SHOW COLUMNS FROM users WHERE Field = 'role'");
    $column = $stmt->fetch();
    echo "   Current: " . $column['Type'] . "\n";

    echo "\n2. Modifying role column to include 'teacher'...\n";
    $pdo->exec("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'student', 'teacher') DEFAULT 'student'");
    echo "   ✓ Column modified successfully!\n";

    echo "\n3. Checking for sample teacher account...\n";
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute(['teacher@appolios.com']);

    if (!$stmt->fetch()) {
        echo "   Creating sample teacher account...\n";
        $teacherHash = password_hash('teacher123', PASSWORD_DEFAULT, ['cost' => HASH_COST]);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute(['Teacher Demo', 'teacher@appolios.com', $teacherHash, 'teacher']);
        echo "   ✓ Sample teacher created!\n";
        echo "   Email: teacher@appolios.com\n";
        echo "   Password: teacher123\n";
    } else {
        echo "   Sample teacher already exists.\n";
    }

    echo "\n4. Verifying setup...\n";
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'teacher'");
    $result = $stmt->fetch();
    echo "   Total teachers: " . $result['count'] . "\n";

    echo "\n=== Setup Complete! ===\n";

} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
}
