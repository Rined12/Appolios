<?php
/**
 * Fix Accounts - reset all accounts (CLI)
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

$pdo = getConnection();

echo "=== RÉPARATION DES COMPTES APPOLIOS ===\n\n";

try {
    $hash = password_hash('password', PASSWORD_DEFAULT);

    echo "Nouveau hash: " . substr($hash, 0, 30) . "...\n\n";

    $pdo->exec("DELETE FROM users");
    echo "✓ Anciens comptes supprimés\n\n";

    $accounts = [
        ['Admin User', 'admin@appolios.com', 'admin', 'password'],
        ['Teacher Demo', 'teacher@appolios.com', 'teacher', 'password'],
        ['Student Demo', 'student@appolios.com', 'student', 'password'],
    ];

    foreach ($accounts as $acc) {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$acc[0], $acc[1], $hash, $acc[2]]);
        echo "✓ Créé: {$acc[1]} ({$acc[2]})\n";
    }

    echo "\n=== VÉRIFICATION ===\n\n";
    $stmt = $pdo->query("SELECT id, name, email, role, password FROM users");
    $users = $stmt->fetchAll();

    foreach ($users as $user) {
        $verify = password_verify('password', $user['password']);
        echo "✓ {$user['email']}: " . ($verify ? "OK - Test réussi" : "ÉCHEC") . "\n";
    }

    echo "\n=== TERMINÉ ===\n\n";
    echo "✅ TOUS LES COMPTES SONT RÉPARÉS\n\n";
    echo "Utilise maintenant:\n";
    echo "• admin@appolios.com / password\n";
    echo "• teacher@appolios.com / password\n";
    echo "• student@appolios.com / password\n";

} catch (Exception $e) {
    echo "❌ ERREUR: " . $e->getMessage() . "\n";
}
