<?php
/**
 * Reset default accounts with password "password" (CLI)
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

$pdo = getConnection();

echo "=== APPOLIOS Account Reset ===\n\n";

try {
    $hash = password_hash('password', PASSWORD_DEFAULT, ['cost' => HASH_COST]);

    echo "Password hash generated: " . substr($hash, 0, 20) . "...\n\n";

    $users = [
        ['Admin User', 'admin@appolios.com', 'admin'],
        ['Teacher Demo', 'teacher@appolios.com', 'teacher'],
        ['Student Demo', 'student@appolios.com', 'student'],
    ];

    foreach ($users as $user) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE email = ?");
        $stmt->execute([$user[1]]);

        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$user[0], $user[1], $hash, $user[2]]);
        echo "✓ Created: {$user[1]} ({$user[2]})\n";
    }

    echo "\n=== ACCOUNTS RESET SUCCESSFULLY ===\n\n";
    echo "Login:\n";
    echo "  admin@appolios.com / password\n";
    echo "  teacher@appolios.com / password\n";
    echo "  student@appolios.com / password\n";

} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
}
