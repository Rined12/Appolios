<?php
/**
 * Test authentication (CLI)
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

$pdo = getConnection();

echo "=== TEST AUTHENTIFICATION ===\n\n";

try {
    $email = 'admin@appolios.com';
    $password = 'password';

    echo "Test: $email / $password\n\n";

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        echo "✓ Utilisateur trouvé:\n";
        echo "  ID: {$user['id']}\n";
        echo "  Name: {$user['name']}\n";
        echo "  Email: {$user['email']}\n";
        echo "  Role: {$user['role']}\n\n";

        $verify = password_verify($password, $user['password']);
        echo "✓ password_verify('$password', hash) = " . ($verify ? 'TRUE' : 'FALSE') . "\n\n";

        if (!$verify) {
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$newHash, $user['id']]);
            echo "✓ Mot de passe mis à jour pour $email\n";
        }
    } else {
        echo "❌ Utilisateur $email non trouvé - création...\n";
        $newHash = password_hash('password', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute(['Admin User', $email, $newHash, 'admin']);
        echo "  ✓ Créé\n";
    }

    echo "\n=== TEST TEACHER ===\n";
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute(['teacher@appolios.com']);
    $teacher = $stmt->fetch();
    if ($teacher) {
        $verify = password_verify('password', $teacher['password']);
        echo "teacher@appolios.com: " . ($verify ? "✓ OK" : "❌ ÉCHEC") . "\n";
        if (!$verify) {
            $newHash = password_hash('password', PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$newHash, $teacher['id']]);
            echo "  ✓ Réparé\n";
        }
    } else {
        echo "❌ Non trouvé - création...\n";
        $newHash = password_hash('password', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute(['Teacher Demo', 'teacher@appolios.com', $newHash, 'teacher']);
        echo "  ✓ Créé\n";
    }

    echo "\n=== TEST STUDENT ===\n";
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute(['student@appolios.com']);
    $student = $stmt->fetch();
    if ($student) {
        $verify = password_verify('password', $student['password']);
        echo "student@appolios.com: " . ($verify ? "✓ OK" : "❌ ÉCHEC") . "\n";
        if (!$verify) {
            $newHash = password_hash('password', PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$newHash, $student['id']]);
            echo "  ✓ Réparé\n";
        }
    } else {
        echo "❌ Non trouvé - création...\n";
        $newHash = password_hash('password', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute(['Student Demo', 'student@appolios.com', $newHash, 'student']);
        echo "  ✓ Créé\n";
    }

    echo "\n=== TERMINÉ ===\n";

} catch (Exception $e) {
    echo "❌ ERREUR: " . $e->getMessage() . "\n";
}
