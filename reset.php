<?php
/**
 * Reset Passwords Script - Create/Reset all default accounts
 */

require_once 'config/config.php';
require_once 'config/database.php';

echo "=== APPOLIOS Account Reset ===\n\n";

try {
    $db = Database::getInstance();
    
    // Hachage du mot de passe "password"
    $hash = password_hash('password', PASSWORD_DEFAULT, ['cost' => HASH_COST]);
    
    echo "Password hash generated: " . substr($hash, 0, 20) . "...\n\n";
    
    // Créer les 3 comptes
    $users = [
        ['Admin User', 'admin@appolios.com', 'admin'],
        ['Teacher Demo', 'teacher@appolios.com', 'teacher'],
        ['Student Demo', 'student@appolios.com', 'student']
    ];
    
    foreach ($users as $user) {
        // Supprimer l'ancien compte s'il existe
        $stmt = $db->prepare("DELETE FROM users WHERE email = ?");
        $stmt->execute([$user[1]]);
        
        // Créer le nouveau compte
        $stmt = $db->prepare("INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$user[0], $user[1], $hash, $user[2]]);
        echo "✓ Created: {$user[1]} ({$user[2]})\n";
    }
    
    echo "\n=== ACCOUNTS RESET SUCCESSFULLY ===\n\n";
    echo "You can now login with:\n\n";
    echo "╔════════════════════════════════════════════════════════╗\n";
    echo "║  ROLE      │  EMAIL                    │  PASSWORD   ║\n";
    echo "╠════════════════════════════════════════════════════════╣\n";
    echo "║  Admin     │  admin@appolios.com       │  password   ║\n";
    echo "║  Teacher   │  teacher@appolios.com     │  password   ║\n";
    echo "║  Student   │  student@appolios.com     │  password   ║\n";
    echo "╚════════════════════════════════════════════════════════╝\n\n";
    echo "Test now: http://localhost/projetWeb/APPOLIOS\n";
    
} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Please check your database configuration in config/database.php\n";
}
