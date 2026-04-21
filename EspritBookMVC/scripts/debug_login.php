<?php
/**
 * Debug Login - CLI: test and repair accounts
 * Run: php EspritBookMVC/scripts/debug_login.php (from project/repo root)
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

$pdo = getConnection();

echo "=== DEBUG LOGIN APPOLIOS ===\n\n";

try {
    echo "1. UTILISATEURS EXISTANTS:\n";
    echo str_repeat("-", 60) . "\n";
    $stmt = $pdo->query("SELECT id, name, email, role, password FROM users");
    $users = $stmt->fetchAll();

    foreach ($users as $user) {
        echo "ID: {$user['id']}\n";
        echo "Name: {$user['name']}\n";
        echo "Email: {$user['email']}\n";
        echo "Role: {$user['role']}\n";
        echo "Password hash: " . substr($user['password'], 0, 30) . "...\n";
        echo "Hash length: " . strlen($user['password']) . "\n";

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

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
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

    $newHash = password_hash('password', PASSWORD_DEFAULT);
    echo "New password hash for 'password': " . substr($newHash, 0, 30) . "...\n\n";

    $accounts = [
        ['Admin User', 'admin@appolios.com', 'admin'],
        ['Teacher Demo', 'teacher@appolios.com', 'teacher'],
        ['Student Demo', 'student@appolios.com', 'student'],
    ];

    foreach ($accounts as $acc) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE email = ?");
        $stmt->execute([$acc[1]]);

        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$acc[0], $acc[1], $newHash, $acc[2]]);

        echo "✓ Réinitialisé: {$acc[1]} ({$acc[2]})\n";
    }

    echo "\n4. VÉRIFICATION APRÈS RÉINITIALISATION:\n";
    echo str_repeat("-", 60) . "\n";

    foreach ($accounts as $acc) {
        $stmt = $pdo->prepare("SELECT password FROM users WHERE email = ?");
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
