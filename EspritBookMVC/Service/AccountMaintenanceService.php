<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

/**
 * Account / user table maintenance (used by MaintenanceController + CLI).
 */
class AccountMaintenanceService
{
    private function pdo(): PDO
    {
        return getConnection();
    }

    public function applyDefaultPasswords123(): string
    {
        $pdo = $this->pdo();
        $out = '';

        $adminHash = password_hash('admin123', PASSWORD_DEFAULT, ['cost' => HASH_COST]);
        $studentHash = password_hash('student123', PASSWORD_DEFAULT, ['cost' => HASH_COST]);
        $teacherHash = password_hash('teacher123', PASSWORD_DEFAULT, ['cost' => HASH_COST]);

        $out .= "Admin hash: {$adminHash}\n";
        $out .= "Student hash: {$studentHash}\n";
        $out .= "Teacher hash: {$teacherHash}\n";

        $this->upsertUserPassword($pdo, 'Admin', 'admin@appolios.com', $adminHash, 'admin', $out);
        $this->upsertUserPassword($pdo, 'John Student', 'student@appolios.com', $studentHash, 'student', $out);
        $this->upsertUserPassword($pdo, 'Teacher Demo', 'teacher@appolios.com', $teacherHash, 'teacher', $out);

        $out .= "\n✅ Passwords fixed! You can now login with:\n";
        $out .= "Admin: admin@appolios.com / admin123\n";
        $out .= "Student: student@appolios.com / student123\n";
        $out .= "Teacher: teacher@appolios.com / teacher123\n";

        return $out;
    }

    private function upsertUserPassword(PDO $pdo, string $name, string $email, string $hash, string $role, string &$out): void
    {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if (!$stmt->fetch()) {
            $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)');
            $stmt->execute([$name, $email, $hash, $role]);
            $out .= "Created: {$email}\n";
        } else {
            $stmt = $pdo->prepare('UPDATE users SET password = ? WHERE email = ?');
            $stmt->execute([$hash, $email]);
            $out .= "Password updated: {$email}\n";
        }
    }

    public function ensureTeacherRoleAndSample(): string
    {
        $pdo = $this->pdo();
        $out = "=== APPOLIOS Teacher Role Setup ===\n\n";

        $out .= "1. Checking current role ENUM values...\n";
        $stmt = $pdo->query("SHOW COLUMNS FROM users WHERE Field = 'role'");
        $column = $stmt->fetch();
        $out .= '   Current: ' . ($column['Type'] ?? 'n/a') . "\n";

        $out .= "\n2. Modifying role column to include 'teacher'...\n";
        $pdo->exec("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'student', 'teacher') DEFAULT 'student'");
        $out .= "   ✓ Column modified successfully!\n";

        $out .= "\n3. Checking for sample teacher account...\n";
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute(['teacher@appolios.com']);

        if (!$stmt->fetch()) {
            $out .= "   Creating sample teacher account...\n";
            $teacherHash = password_hash('teacher123', PASSWORD_DEFAULT, ['cost' => HASH_COST]);
            $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)');
            $stmt->execute(['Teacher Demo', 'teacher@appolios.com', $teacherHash, 'teacher']);
            $out .= "   ✓ Sample teacher created!\n";
            $out .= "   Email: teacher@appolios.com\n";
            $out .= "   Password: teacher123\n";
        } else {
            $out .= "   Sample teacher already exists.\n";
        }

        $out .= "\n4. Verifying setup...\n";
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'teacher'");
        $result = $stmt->fetch();
        $out .= '   Total teachers: ' . (int) ($result['count'] ?? 0) . "\n";
        $out .= "\n=== Setup Complete! ===\n";

        return $out;
    }

    public function recreateAllAccountsPassword(string $plain): string
    {
        $pdo = $this->pdo();
        $out = "=== RÉPARATION DES COMPTES APPOLIOS ===\n\n";

        $hash = password_hash($plain, PASSWORD_DEFAULT);
        $out .= 'Nouveau hash: ' . substr($hash, 0, 30) . "...\n\n";

        $pdo->exec('DELETE FROM users');
        $out .= "✓ Anciens comptes supprimés\n\n";

        $accounts = [
            ['Admin User', 'admin@appolios.com', 'admin'],
            ['Teacher Demo', 'teacher@appolios.com', 'teacher'],
            ['Student Demo', 'student@appolios.com', 'student'],
        ];

        foreach ($accounts as $acc) {
            $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())');
            $stmt->execute([$acc[0], $acc[1], $hash, $acc[2]]);
            $out .= "✓ Créé: {$acc[1]} ({$acc[2]})\n";
        }

        $out .= "\n=== VÉRIFICATION ===\n\n";
        $stmt = $pdo->query('SELECT id, name, email, role, password FROM users');
        $users = $stmt->fetchAll();
        foreach ($users as $user) {
            $verify = password_verify($plain, $user['password']);
            $out .= "✓ {$user['email']}: " . ($verify ? 'OK - Test réussi' : 'ÉCHEC') . "\n";
        }

        $out .= "\n=== TERMINÉ ===\n\n";
        $out .= "✅ TOUS LES COMPTES SONT RÉPARÉS\n\n";
        $out .= "Utilise maintenant:\n";
        $out .= "• admin@appolios.com / {$plain}\n";
        $out .= "• teacher@appolios.com / {$plain}\n";
        $out .= "• student@appolios.com / {$plain}\n";

        return $out;
    }

    public function resetThreeDefaultAccounts(string $plain): string
    {
        $pdo = $this->pdo();
        $out = "=== APPOLIOS Account Reset ===\n\n";

        $hash = password_hash($plain, PASSWORD_DEFAULT, ['cost' => HASH_COST]);
        $out .= 'Password hash generated: ' . substr($hash, 0, 20) . "...\n\n";

        $users = [
            ['Admin User', 'admin@appolios.com', 'admin'],
            ['Teacher Demo', 'teacher@appolios.com', 'teacher'],
            ['Student Demo', 'student@appolios.com', 'student'],
        ];

        foreach ($users as $user) {
            $stmt = $pdo->prepare('DELETE FROM users WHERE email = ?');
            $stmt->execute([$user[1]]);
            $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())');
            $stmt->execute([$user[0], $user[1], $hash, $user[2]]);
            $out .= "✓ Created: {$user[1]} ({$user[2]})\n";
        }

        $out .= "\n=== ACCOUNTS RESET SUCCESSFULLY ===\n\n";
        $out .= "Login:\n";
        $out .= "  admin@appolios.com / {$plain}\n";
        $out .= "  teacher@appolios.com / {$plain}\n";
        $out .= "  student@appolios.com / {$plain}\n";

        return $out;
    }

    public function debugLoginDumpAndReinit(): string
    {
        $pdo = $this->pdo();
        $out = "=== DEBUG LOGIN APPOLIOS ===\n\n";

        $out .= "1. UTILISATEURS EXISTANTS:\n";
        $out .= str_repeat('-', 60) . "\n";
        $stmt = $pdo->query('SELECT id, name, email, role, password FROM users');
        $users = $stmt->fetchAll();

        foreach ($users as $user) {
            $out .= "ID: {$user['id']}\n";
            $out .= "Name: {$user['name']}\n";
            $out .= "Email: {$user['email']}\n";
            $out .= "Role: {$user['role']}\n";
            $out .= 'Password hash: ' . substr($user['password'], 0, 30) . "...\n";
            $out .= 'Hash length: ' . strlen($user['password']) . "\n";
            $info = password_get_info($user['password']);
            $out .= 'Hash algo: ' . ($info['algoName'] ?: 'INVALID/OLD HASH') . "\n";
            $out .= str_repeat('-', 40) . "\n";
        }

        $out .= "\n2. TEST DE VÉRIFICATION:\n";
        $out .= str_repeat('-', 60) . "\n";

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
            $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            if ($user) {
                $verify = password_verify($pass, $user['password']);
                $out .= "Email: {$email} | Pass: '{$pass}' => " . ($verify ? '✅ CORRECT' : '❌ WRONG') . "\n";
            } else {
                $out .= "Email: {$email} => USER NOT FOUND\n";
            }
        }

        $out .= "\n3. RÉINITIALISATION DES MOTS DE PASSE:\n";
        $out .= str_repeat('-', 60) . "\n";
        $newHash = password_hash('password', PASSWORD_DEFAULT);
        $out .= 'New password hash for \'password\': ' . substr($newHash, 0, 30) . "...\n\n";

        $accounts = [
            ['Admin User', 'admin@appolios.com', 'admin'],
            ['Teacher Demo', 'teacher@appolios.com', 'teacher'],
            ['Student Demo', 'student@appolios.com', 'student'],
        ];

        foreach ($accounts as $acc) {
            $stmt = $pdo->prepare('DELETE FROM users WHERE email = ?');
            $stmt->execute([$acc[1]]);
            $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())');
            $stmt->execute([$acc[0], $acc[1], $newHash, $acc[2]]);
            $out .= "✓ Réinitialisé: {$acc[1]} ({$acc[2]})\n";
        }

        $out .= "\n4. VÉRIFICATION APRÈS RÉINITIALISATION:\n";
        $out .= str_repeat('-', 60) . "\n";
        foreach ($accounts as $acc) {
            $stmt = $pdo->prepare('SELECT password FROM users WHERE email = ?');
            $stmt->execute([$acc[1]]);
            $user = $stmt->fetch();
            $verify = password_verify('password', $user['password']);
            $out .= "{$acc[1]}: " . ($verify ? '✅ OK' : '❌ FAILED') . "\n";
        }

        $out .= "\n=== TERMINÉ ===\n";
        $out .= "\n✅ TOUS LES COMPTES SONT RÉINITIALISÉS\n";
        $out .= "\nEmail: admin@appolios.com\n";
        $out .= "Email: teacher@appolios.com\n";
        $out .= "Email: student@appolios.com\n";
        $out .= "\nMot de passe pour tous: password\n";

        return $out;
    }

    public function testAuthRepairDemo(): string
    {
        $pdo = $this->pdo();
        $out = "=== TEST AUTHENTIFICATION ===\n\n";

        $email = 'admin@appolios.com';
        $password = 'password';
        $out .= "Test: {$email} / {$password}\n\n";

        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            $out .= "✓ Utilisateur trouvé:\n";
            $out .= "  ID: {$user['id']}\n";
            $out .= "  Name: {$user['name']}\n";
            $out .= "  Email: {$user['email']}\n";
            $out .= "  Role: {$user['role']}\n\n";

            $verify = password_verify($password, $user['password']);
            $out .= "✓ password_verify('{$password}', hash) = " . ($verify ? 'TRUE' : 'FALSE') . "\n\n";

            if (!$verify) {
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
                $stmt->execute([$newHash, $user['id']]);
                $out .= "✓ Mot de passe mis à jour pour {$email}\n";
            }
        } else {
            $out .= "❌ Utilisateur {$email} non trouvé - création...\n";
            $newHash = password_hash('password', PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())');
            $stmt->execute(['Admin User', $email, $newHash, 'admin']);
            $out .= "  ✓ Créé\n";
        }

        $out .= "\n=== TEST TEACHER ===\n";
        $this->ensureUserPasswordPlain($pdo, 'teacher@appolios.com', 'Teacher Demo', 'teacher', 'password', $out);
        $out .= "\n=== TEST STUDENT ===\n";
        $this->ensureUserPasswordPlain($pdo, 'student@appolios.com', 'Student Demo', 'student', 'password', $out);
        $out .= "\n=== TERMINÉ ===\n";

        return $out;
    }

    private function ensureUserPasswordPlain(PDO $pdo, string $email, string $name, string $role, string $plain, string &$out): void
    {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        if ($row) {
            $verify = password_verify($plain, $row['password']);
            $out .= "{$email}: " . ($verify ? '✓ OK' : '❌ ÉCHEC') . "\n";
            if (!$verify) {
                $newHash = password_hash($plain, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
                $stmt->execute([$newHash, $row['id']]);
                $out .= "  ✓ Réparé\n";
            }
        } else {
            $out .= "❌ Non trouvé - création...\n";
            $newHash = password_hash($plain, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())');
            $stmt->execute([$name, $email, $newHash, $role]);
            $out .= "  ✓ Créé\n";
        }
    }
}
