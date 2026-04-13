<?php
/**
 * Test Authentication
 */

require_once 'config/config.php';
require_once 'config/database.php';

echo "=== TEST AUTHENTIFICATION ===\n\n";

try {
    $db = Database::getInstance()->getConnection();
    
    // Test avec admin
    $email = 'admin@appolios.com';
    $password = 'password';
    
    echo "Test: $email / $password\n\n";
    
    // Récupérer l'utilisateur
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user) {
        echo "✓ Utilisateur trouvé:\n";
        echo "  ID: {$user['id']}\n";
        echo "  Name: {$user['name']}\n";
        echo "  Email: {$user['email']}\n";
        echo "  Role: {$user['role']}\n";
        echo "  Password hash: {$user['password']}\n";
        echo "  Hash length: " . strlen($user['password']) . "\n\n";
        
        // Vérifier le hash
        $info = password_get_info($user['password']);
        echo "Info du hash:\n";
        print_r($info);
        
        // Vérifier le mot de passe
        $verify = password_verify($password, $user['password']);
        echo "\n✓ password_verify('$password', hash) = " . ($verify ? 'TRUE' : 'FALSE') . "\n\n";
        
        if (!$verify) {
            echo "❌ Le mot de passe ne correspond pas!\n\n";
            
            // Créer un nouveau hash correct
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            echo "Nouveau hash correct: $newHash\n\n";
            
            // Mettre à jour
            $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$newHash, $user['id']]);
            echo "✓ Mot de passe mis à jour pour $email\n";
            
            // Vérifier
            $stmt = $db->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$user['id']]);
            $updated = $stmt->fetch();
            $verify2 = password_verify($password, $updated['password']);
            echo "✓ Vérification après mise à jour: " . ($verify2 ? 'OK' : 'ÉCHEC') . "\n";
        }
    } else {
        echo "❌ Utilisateur $email non trouvé - création...\n";
        $newHash = password_hash('password', PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute(['Admin User', $email, $newHash, 'admin']);
        echo "  ✓ Créé\n";
    }
    
    // Même test pour teacher et student
    echo "\n=== TEST TEACHER ===\n";
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute(['teacher@appolios.com']);
    $teacher = $stmt->fetch();
    if ($teacher) {
        $verify = password_verify('password', $teacher['password']);
        echo "teacher@appolios.com: " . ($verify ? "✓ OK" : "❌ ÉCHEC") . "\n";
        if (!$verify) {
            $newHash = password_hash('password', PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$newHash, $teacher['id']]);
            echo "  ✓ Réparé\n";
        }
    } else {
        echo "❌ Non trouvé - création...\n";
        $newHash = password_hash('password', PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute(['Teacher Demo', 'teacher@appolios.com', $newHash, 'teacher']);
        echo "  ✓ Créé\n";
    }
    
    echo "\n=== TEST STUDENT ===\n";
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute(['student@appolios.com']);
    $student = $stmt->fetch();
    if ($student) {
        $verify = password_verify('password', $student['password']);
        echo "student@appolios.com: " . ($verify ? "✓ OK" : "❌ ÉCHEC") . "\n";
        if (!$verify) {
            $newHash = password_hash('password', PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$newHash, $student['id']]);
            echo "  ✓ Réparé\n";
        }
    } else {
        echo "❌ Non trouvé - création...\n";
        $newHash = password_hash('password', PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute(['Student Demo', 'student@appolios.com', $newHash, 'student']);
        echo "  ✓ Créé\n";
    }
    
    echo "\n=== TERMINÉ ===\n";
    
} catch (Exception $e) {
    echo "❌ ERREUR: " . $e->getMessage() . "\n";
}
