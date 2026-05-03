<?php
declare(strict_types=1);

require_once __DIR__ . '/SessionEntities.php';
/**
 * HTTP session helpers for flash messages, form errors, and repopulated POST ("old").
 */
class SessionService
{
    /** Controller-facing wrapper (logic formerly on BaseController): persist flash from raw strings. */
    public function flashPersist(string $type, string $message): void
    {
        $this->persistFlash(new FlashMessageEntity($type, $message));
    }

    /** Controller-facing wrapper: consume flash and return legacy view array shape. */
    /** @return array{type: string, message: string}|null */
    public function flashConsumeForView(): ?array
    {
        $entity = $this->takeFlash();

        return $entity === null ? null : ['type' => $entity->getType(), 'message' => $entity->getMessage()];
    }

    /** Controller-facing wrapper: store validation messages from a plain array. */
    /** @param array<string, mixed> $errors */
    public function validationPersist(array $errors): void
    {
        $this->persistValidationMessages(new FormValidationMessagesEntity($errors));
    }

    public function persistFlash(FlashMessageEntity $message): void
    {
        $_SESSION['flash'] = ['type' => $message->getType(), 'message' => $message->getMessage()];
    }

    public function takeFlash(): ?FlashMessageEntity
    {
        if (!isset($_SESSION['flash'])) {
            return null;
        }

        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);

        if (!is_array($flash)) {
            return null;
        }

        return new FlashMessageEntity(
            (string) ($flash['type'] ?? ''),
            (string) ($flash['message'] ?? '')
        );
    }

    public function persistValidationMessages(FormValidationMessagesEntity $entity): void
    {
        $_SESSION['form_errors'] = $entity->getMessages();
    }

    public function takeValidationMessages(): FormValidationMessagesEntity
    {
        $errors = $_SESSION['form_errors'] ?? [];
        unset($_SESSION['form_errors']);

        return new FormValidationMessagesEntity(is_array($errors) ? $errors : []);
    }

    /** Repopulated form values after redirect (controllers consume; views never touch $_SESSION). */
    public function consumeOld(): array
    {
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['old']);

        return is_array($old) ? $old : [];
    }

    /** Inline validation lists stored under $_SESSION['errors'] (e.g. registration). */
    public function pullInlineRegistrationErrors(): array
    {
        $errors = $_SESSION['errors'] ?? [];
        unset($_SESSION['errors']);

        return is_array($errors) ? $errors : [];
    }
}

require_once __DIR__ . '/../config/config.php';

/**
 * Shared file upload handlers for group photos and discussion attachments.
 */
class FileUploadService
{
    private function ensureGroupUploadDir(): void
    {
        if (!is_dir(GROUP_UPLOAD_DIR)) {
            @mkdir(GROUP_UPLOAD_DIR, 0755, true);
        }
    }

    private function ensureChatUploadDir(): void
    {
        if (!is_dir(CHAT_UPLOAD_DIR)) {
            @mkdir(CHAT_UPLOAD_DIR, 0755, true);
        }
    }

    /**
     * @return array{url:?string, error:?string}
     */
    public function handleGroupPhotoUpload(string $fieldName = 'group_photo', array $messages = []): array
    {
        $msg = array_merge([
            'upload_failed' => 'File upload failed.',
            'invalid_type' => 'Please upload a JPEG, PNG, GIF, or WebP image.',
            'too_large' => 'Image must be 2 MB or smaller.',
            'save_failed' => 'Could not save the image.',
        ], $messages);

        if (!isset($_FILES[$fieldName]) || !is_array($_FILES[$fieldName])) {
            return ['url' => null, 'error' => null];
        }

        $file = $_FILES[$fieldName];
        $errCode = (int) ($file['error'] ?? UPLOAD_ERR_OK);

        if ($errCode === UPLOAD_ERR_NO_FILE) {
            return ['url' => null, 'error' => null];
        }

        if ($errCode !== UPLOAD_ERR_OK) {
            return ['url' => null, 'error' => $msg['upload_failed']];
        }

        $tmp = (string) ($file['tmp_name'] ?? '');
        if ($tmp === '' || !is_uploaded_file($tmp)) {
            return ['url' => null, 'error' => $msg['upload_failed']];
        }

        if (($file['size'] ?? 0) > GROUP_UPLOAD_MAX_BYTES) {
            return ['url' => null, 'error' => $msg['too_large']];
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($tmp);
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
        if ($mime === false || !isset($allowed[$mime])) {
            return ['url' => null, 'error' => $msg['invalid_type']];
        }

        $this->ensureGroupUploadDir();
        $ext = $allowed[$mime];
        $name = 'g_' . bin2hex(random_bytes(8)) . '.' . $ext;
        $dest = GROUP_UPLOAD_DIR . DIRECTORY_SEPARATOR . $name;

        if (!move_uploaded_file($tmp, $dest)) {
            return ['url' => null, 'error' => $msg['save_failed']];
        }

        $url = rtrim(GROUP_UPLOAD_URL, '/') . '/' . $name;

        return ['url' => $url, 'error' => null];
    }

    /**
     * @return array{ok:bool,error:?string,url:?string,fileName:?string,mime:?string,size:int,messageType:?string}
     */
    public function handleChatAttachmentUpload(string $fieldName = 'attachment'): array
    {
        if (!isset($_FILES[$fieldName]) || !is_array($_FILES[$fieldName])) {
            return ['ok' => false, 'error' => 'Attachment file is required.', 'url' => null, 'fileName' => null, 'mime' => null, 'size' => 0, 'messageType' => null];
        }

        $file = $_FILES[$fieldName];
        $errCode = (int) ($file['error'] ?? UPLOAD_ERR_OK);
        if ($errCode !== UPLOAD_ERR_OK) {
            return ['ok' => false, 'error' => 'Upload failed.', 'url' => null, 'fileName' => null, 'mime' => null, 'size' => 0, 'messageType' => null];
        }

        $tmp = (string) ($file['tmp_name'] ?? '');
        if ($tmp === '' || !is_uploaded_file($tmp)) {
            return ['ok' => false, 'error' => 'Invalid uploaded file.', 'url' => null, 'fileName' => null, 'mime' => null, 'size' => 0, 'messageType' => null];
        }

        $size = (int) ($file['size'] ?? 0);
        if ($size <= 0 || $size > CHAT_UPLOAD_MAX_BYTES) {
            return ['ok' => false, 'error' => 'Attachment exceeds 10 MB maximum size.', 'url' => null, 'fileName' => null, 'mime' => null, 'size' => 0, 'messageType' => null];
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = (string) ($finfo->file($tmp) ?: '');
        $allowed = [
            'image/jpeg' => ['ext' => 'jpg', 'type' => 'image'],
            'image/png' => ['ext' => 'png', 'type' => 'image'],
            'image/gif' => ['ext' => 'gif', 'type' => 'image'],
            'image/webp' => ['ext' => 'webp', 'type' => 'image'],
            'audio/webm' => ['ext' => 'webm', 'type' => 'audio'],
            'audio/ogg' => ['ext' => 'ogg', 'type' => 'audio'],
            'audio/mpeg' => ['ext' => 'mp3', 'type' => 'audio'],
            'audio/mp4' => ['ext' => 'm4a', 'type' => 'audio'],
            'application/pdf' => ['ext' => 'pdf', 'type' => 'file'],
            'application/zip' => ['ext' => 'zip', 'type' => 'file'],
            'application/x-zip-compressed' => ['ext' => 'zip', 'type' => 'file'],
            'application/msword' => ['ext' => 'doc', 'type' => 'file'],
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => ['ext' => 'docx', 'type' => 'file'],
            'text/plain' => ['ext' => 'txt', 'type' => 'file'],
        ];
        if (!isset($allowed[$mime])) {
            return ['ok' => false, 'error' => 'Unsupported attachment format.', 'url' => null, 'fileName' => null, 'mime' => null, 'size' => 0, 'messageType' => null];
        }

        $this->ensureChatUploadDir();
        $safeBaseName = preg_replace('/[^A-Za-z0-9._-]/', '_', (string) ($file['name'] ?? 'attachment'));
        $safeBaseName = trim((string) $safeBaseName);
        if ($safeBaseName === '') {
            $safeBaseName = 'attachment';
        }
        $ext = $allowed[$mime]['ext'];
        $storedName = 'c_' . bin2hex(random_bytes(8)) . '.' . $ext;
        $dest = CHAT_UPLOAD_DIR . DIRECTORY_SEPARATOR . $storedName;
        if (!move_uploaded_file($tmp, $dest)) {
            return ['ok' => false, 'error' => 'Could not save attachment.', 'url' => null, 'fileName' => null, 'mime' => null, 'size' => 0, 'messageType' => null];
        }

        return [
            'ok' => true,
            'error' => null,
            'url' => rtrim(CHAT_UPLOAD_URL, '/') . '/' . $storedName,
            'fileName' => $safeBaseName,
            'mime' => $mime,
            'size' => $size,
            'messageType' => $allowed[$mime]['type'],
        ];
    }
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

/**
 * Account / user table maintenance (used by MaintenanceController + CLI).
 */
class AccountMaintenanceService
{
    public function applyDefaultPasswords123(): string
    {
        $pdo = getConnection();
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

        $out .= "\n[OK] Passwords fixed! You can now login with:\n";
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
        $pdo = getConnection();
        $out = "=== APPOLIOS Teacher Role Setup ===\n\n";

        $out .= "1. Checking current role ENUM values...\n";
        $stmt = $pdo->query("SHOW COLUMNS FROM users WHERE Field = 'role'");
        $column = $stmt->fetch();
        $out .= '   Current: ' . ($column['Type'] ?? 'n/a') . "\n";

        $out .= "\n2. Modifying role column to include 'teacher'...\n";
        $pdo->exec("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'student', 'teacher') DEFAULT 'student'");
        $out .= "   [OK] Column modified successfully!\n";

        $out .= "\n3. Checking for sample teacher account...\n";
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute(['teacher@appolios.com']);

        if (!$stmt->fetch()) {
            $out .= "   Creating sample teacher account...\n";
            $teacherHash = password_hash('teacher123', PASSWORD_DEFAULT, ['cost' => HASH_COST]);
            $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)');
            $stmt->execute(['Teacher Demo', 'teacher@appolios.com', $teacherHash, 'teacher']);
            $out .= "   [OK] Sample teacher created!\n";
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
        $pdo = getConnection();
        $out = "=== REPARATION DES COMPTES APPOLIOS ===\n\n";

        $hash = password_hash($plain, PASSWORD_DEFAULT);
        $out .= 'Nouveau hash: ' . substr($hash, 0, 30) . "...\n\n";

        $pdo->exec('DELETE FROM users');
        $out .= "[OK] Anciens comptes supprimes\n\n";

        $accounts = [
            ['Admin User', 'admin@appolios.com', 'admin'],
            ['Teacher Demo', 'teacher@appolios.com', 'teacher'],
            ['Student Demo', 'student@appolios.com', 'student'],
        ];

        foreach ($accounts as $acc) {
            $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())');
            $stmt->execute([$acc[0], $acc[1], $hash, $acc[2]]);
            $out .= "[OK] Cree: {$acc[1]} ({$acc[2]})\n";
        }

        $out .= "\n=== VERIFICATION ===\n\n";
        $stmt = $pdo->query('SELECT id, name, email, role, password FROM users');
        $users = $stmt->fetchAll();
        foreach ($users as $user) {
            $verify = password_verify($plain, $user['password']);
            $out .= "[OK] {$user['email']}: " . ($verify ? 'OK - Test reussi' : 'ECHEC') . "\n";
        }

        $out .= "\n=== TERMINE ===\n\n";
        $out .= "[OK] TOUS LES COMPTES SONT REPARES\n\n";
        $out .= "Utilise maintenant:\n";
        $out .= "- admin@appolios.com / {$plain}\n";
        $out .= "- teacher@appolios.com / {$plain}\n";
        $out .= "- student@appolios.com / {$plain}\n";

        return $out;
    }

    public function resetThreeDefaultAccounts(string $plain): string
    {
        $pdo = getConnection();
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
            $out .= "[OK] Created: {$user[1]} ({$user[2]})\n";
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
        $pdo = getConnection();
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

        $out .= "\n2. TEST DE VERIFICATION:\n";
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
                $out .= "Email: {$email} | Pass: '{$pass}' => " . ($verify ? '[OK] CORRECT' : '[FAIL] WRONG') . "\n";
            } else {
                $out .= "Email: {$email} => USER NOT FOUND\n";
            }
        }

        $out .= "\n3. REINITIALISATION DES MOTS DE PASSE:\n";
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
            $out .= "[OK] Reinitialise: {$acc[1]} ({$acc[2]})\n";
        }

        $out .= "\n4. VERIFICATION APRES REINITIALISATION:\n";
        $out .= str_repeat('-', 60) . "\n";
        foreach ($accounts as $acc) {
            $stmt = $pdo->prepare('SELECT password FROM users WHERE email = ?');
            $stmt->execute([$acc[1]]);
            $user = $stmt->fetch();
            $verify = password_verify('password', $user['password']);
            $out .= "{$acc[1]}: " . ($verify ? '[OK] OK' : '[FAIL] FAILED') . "\n";
        }

        $out .= "\n=== TERMINE ===\n";
        $out .= "\n[OK] TOUS LES COMPTES SONT REINITIALISES\n";
        $out .= "\nEmail: admin@appolios.com\n";
        $out .= "Email: teacher@appolios.com\n";
        $out .= "Email: student@appolios.com\n";
        $out .= "\nMot de passe pour tous: password\n";

        return $out;
    }

    public function testAuthRepairDemo(): string
    {
        $pdo = getConnection();
        $out = "=== TEST AUTHENTIFICATION ===\n\n";

        $email = 'admin@appolios.com';
        $password = 'password';
        $out .= "Test: {$email} / {$password}\n\n";

        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            $out .= "[OK] Utilisateur trouve:\n";
            $out .= "  ID: {$user['id']}\n";
            $out .= "  Name: {$user['name']}\n";
            $out .= "  Email: {$user['email']}\n";
            $out .= "  Role: {$user['role']}\n\n";

            $verify = password_verify($password, $user['password']);
            $out .= "[OK] password_verify('{$password}', hash) = " . ($verify ? 'TRUE' : 'FALSE') . "\n\n";

            if (!$verify) {
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
                $stmt->execute([$newHash, $user['id']]);
                $out .= "[OK] Mot de passe mis a jour pour {$email}\n";
            }
        } else {
            $out .= "[FAIL] Utilisateur {$email} non trouve - creation...\n";
            $newHash = password_hash('password', PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())');
            $stmt->execute(['Admin User', $email, $newHash, 'admin']);
            $out .= "  [OK] Cree\n";
        }

        $out .= "\n=== TEST TEACHER ===\n";
        $this->ensureUserPasswordPlain($pdo, 'teacher@appolios.com', 'Teacher Demo', 'teacher', 'password', $out);
        $out .= "\n=== TEST STUDENT ===\n";
        $this->ensureUserPasswordPlain($pdo, 'student@appolios.com', 'Student Demo', 'student', 'password', $out);
        $out .= "\n=== TERMINE ===\n";

        return $out;
    }

    private function ensureUserPasswordPlain(PDO $pdo, string $email, string $name, string $role, string $plain, string &$out): void
    {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        if ($row) {
            $verify = password_verify($plain, $row['password']);
            $out .= "{$email}: " . ($verify ? '[OK] OK' : '[FAIL] ECHEC') . "\n";
            if (!$verify) {
                $newHash = password_hash($plain, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
                $stmt->execute([$newHash, $row['id']]);
                $out .= "  [OK] Repare\n";
            }
        } else {
            $out .= "[FAIL] Non trouve - creation...\n";
            $newHash = password_hash($plain, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())');
            $stmt->execute([$name, $email, $newHash, $role]);
            $out .= "  [OK] Cree\n";
        }
    }
}

require_once __DIR__ . '/Repositories.php';

/**
 * Student-facing query helpers for groups/discussions (used by StudentController).
 */
class StudentQueryService
{
    public function approvedOwnedGroupsForUser(GroupeRepository $repo, int $userId): array
    {
        $groups = $repo->fetchByCreator($userId);

        return array_values(array_filter(
            $groups,
            static function (array $g): bool {
                $a = (string) ($g['approval_statut'] ?? $g['approval_status'] ?? '');

                return $a === 'approuve';
            }
        ));
    }

    /** @return mixed */
    public function sortKeyGroupId(array $row)
    {
        return (int) ($row['id_groupe'] ?? $row['id'] ?? 0);
    }

    /** @return mixed */
    public function sortKeyDiscussionId(array $row)
    {
        return (int) ($row['id_discussion'] ?? $row['id'] ?? 0);
    }
}

/**
 * Builds structured data for the group activity print/PDF report (admin + members).
 */
class GroupActivityReportService
{
    /**
     * @return array{
     *   groupe: array<string, mixed>,
     *   members: array<int, array<string, mixed>>,
     *   total_discussions: int,
     *   total_chat_messages: int,
     *   total_opening_posts: int,
     *   top_discussions: array<int, array{id:int, title:string, message_count:int, chat_count:int}>,
     *   recent_messages: array<int, array<string, mixed>>,
     *   generated_at: string,
     *   chat_table_available: bool
     * }
     */
    public function build(int $groupId): array
    {
        $groupeRepository = new GroupeRepository();
        $discussionRepository = new DiscussionRepository();
        $groupe = $groupeRepository->findById($groupId);
        if (!$groupe || !is_array($groupe)) {
            throw new InvalidArgumentException('Group not found');
        }

        $members = $groupeRepository->fetchMembres($groupId);
        $discussions = $discussionRepository->fetchByGroup($groupId);

        $discussionIds = [];
        $idToTitle = [];
        foreach ($discussions as $d) {
            $did = (int) ($d['id_discussion'] ?? $d['id'] ?? 0);
            if ($did > 0) {
                $discussionIds[] = $did;
                $idToTitle[$did] = (string) ($d['titre'] ?? $d['title'] ?? 'Discussion');
            }
        }

        $chatCounts = [];
        $recentMessages = [];
        $chatTableAvailable = false;
        $totalChat = 0;

        if ($discussionIds !== []) {
            $pdo = getConnection();
            $placeholders = implode(',', array_fill(0, count($discussionIds), '?'));
            try {
                $stmt = $pdo->prepare(
                    "SELECT discussion_id, COUNT(*) AS cnt FROM discussion_messages WHERE discussion_id IN ({$placeholders}) GROUP BY discussion_id"
                );
                $stmt->execute($discussionIds);
                foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $chatCounts[(int) $row['discussion_id']] = (int) $row['cnt'];
                }
                $totalChat = array_sum($chatCounts);
                $stmt2 = $pdo->prepare(
                    "SELECT discussion_id, user_name, message, message_type, file_name, created_at
                     FROM discussion_messages
                     WHERE discussion_id IN ({$placeholders})
                     ORDER BY created_at DESC
                     LIMIT 10"
                );
                $stmt2->execute($discussionIds);
                $recentMessages = $stmt2->fetchAll(PDO::FETCH_ASSOC) ?: [];
                $chatTableAvailable = true;
            } catch (Throwable $e) {
                $chatCounts = [];
                $recentMessages = [];
                $totalChat = 0;
                $chatTableAvailable = false;
            }
        }

        foreach ($recentMessages as &$m) {
            $did = (int) ($m['discussion_id'] ?? 0);
            $m['discussion_title'] = $idToTitle[$did] ?? ('#' . $did);
        }
        unset($m);

        $top = [];
        foreach ($discussions as $d) {
            $did = (int) ($d['id_discussion'] ?? $d['id'] ?? 0);
            if ($did <= 0) {
                continue;
            }
            $chat = $chatCounts[$did] ?? 0;
            $opening = 1;
            $top[] = [
                'id' => $did,
                'title' => (string) ($d['titre'] ?? $d['title'] ?? ''),
                'message_count' => $chat + $opening,
                'chat_count' => $chat,
            ];
        }
        usort($top, static function (array $a, array $b): int {
            return $b['message_count'] <=> $a['message_count'];
        });
        $top = array_slice($top, 0, 5);

        $createdRaw = (string) ($groupe['date_creation'] ?? $groupe['created_at'] ?? '');
        $createdLabel = $createdRaw;
        if ($createdRaw !== '') {
            $ts = strtotime($createdRaw);
            if ($ts !== false) {
                $createdLabel = date('M j, Y g:i A', $ts);
            }
        }

        return [
            'groupe' => $groupe,
            'members' => $members,
            'total_discussions' => count($discussions),
            'total_chat_messages' => $totalChat,
            'total_opening_posts' => count($discussions),
            'top_discussions' => $top,
            'recent_messages' => $recentMessages,
            'generated_at' => date('Y-m-d H:i'),
            'chat_table_available' => $chatTableAvailable,
            'group_created_label' => $createdLabel,
        ];
    }
}
