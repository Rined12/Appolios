<?php
/**
 * Debug Login - Test et réparation des comptes
 */

require_once 'config/config.php';
require_once 'config/database.php';

echo "=== DEBUG LOGIN APPOLIOS ===\n\n";

try {
    $db = Database::getInstance();
    
    // Afficher tous les utilisateurs
    echo "1. UTILISATEURS EXISTANTS:\n";
    echo str_repeat("-", 60) . "\n";
    $stmt = $db->query("SELECT id, name, email, role, password FROM users");
    $users = $stmt->fetchAll();
    
    foreach ($users as $user) {
        echo "ID: {$user['id']}\n";
        echo "Name: {$user['name']}\n";
        echo "Email: {$user['email']}\n";
        echo "Role: {$user['role']}\n";
        echo "Password hash: " . substr($user['password'], 0, 30) . "...\n";
        echo "Hash length: " . strlen($user['password']) . "\n";
        
        // Vérifier si le hash est valide
        $info = password_get_info($user['password']);
        echo "Hash algo: " . ($info['algoName'] ?: 'INVALID/OLD HASH') . "\n";
        echo str_repeat("-", 40) . "\n";
    }
    
    echo "\n2. TEST DE VÉRIFICATION:\n";
    echo str_repeat("-", 60) . "\n";
    
    $testPasswords = [
        ['admin@appolios.com', 'password'],
        ['student@appolios.com', 'password'],
        ['teacher@appolios.com', 'password'],
        ['admin@appolios.com', 'admin123'],
        ['student@appolios.com', 'student123'],
    ];
    
    foreach ($testPasswords as $test) {
        $email = $test[0];
        $pass = $test[1];
        
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user) {
            $verify = password_verify($pass, $user['password']);
            echo "Email: $email | Pass: '$pass' => " . ($verify ? "✅ CORRECT" : "❌ WRONG") . "\n";
        } else {
            echo "Email: $email => USER NOT FOUND\n";
        }
    }
    
    echo "\n3. RÉINITIALISATION DES MOTS DE PASSE:\n";
    echo str_repeat("-", 60) . "\n";
    
    // Générer un nouveau hash
    $newHash = password_hash('password', PASSWORD_DEFAULT);
    echo "New password hash for 'password': " . substr($newHash, 0, 30) . "...\n\n";
    
    // Mettre à jour tous les comptes
    $accounts = [
        ['Admin User', 'admin@appolios.com', 'admin'],
        ['Teacher Demo', 'teacher@appolios.com', 'teacher'],
        ['Student Demo', 'student@appolios.com', 'student']
    ];
    
    foreach ($accounts as $acc) {
        // Supprimer l'ancien
        $stmt = $db->prepare("DELETE FROM users WHERE email = ?");
        $stmt->execute([$acc[1]]);
        
        // Créer le nouveau
        $stmt = $db->prepare("INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$acc[0], $acc[1], $newHash, $acc[2]]);
        
        echo "✓ Réinitialisé: {$acc[1]} ({$acc[2]})\n";
    }
    
    echo "\n4. VÉRIFICATION APRÈS RÉINITIALISATION:\n";
    echo str_repeat("-", 60) . "\n";
    
    foreach ($accounts as $acc) {
        $stmt = $db->prepare("SELECT password FROM users WHERE email = ?");
        $stmt->execute([$acc[1]]);
        $user = $stmt->fetch();
        
        $verify = password_verify('password', $user['password']);
        echo "{$acc[1]}: " . ($verify ? "✅ OK" : "❌ FAILED") . "\n";
    }
    
    echo "\n=== TERMINÉ ===\n";
    echo "\n✅ TOUS LES COMPTES SONT RÉINITIALISÉS\n";
    echo "\nEmail: admin@appolios.com\n";
    echo "Email: teacher@appolios.com\n";
    echo "Email: student@appolios.com\n";
    echo "\nMot de passe pour tous: password\n";
    
} catch (Exception $e) {
    echo "\n❌ ERREUR: " . $e->getMessage() . "\n";
}
