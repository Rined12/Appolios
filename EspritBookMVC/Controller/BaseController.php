<?php

require_once __DIR__ . '/../Model/SessionEntities.php';

abstract class BaseController
{
    /**
     * Resolve repositories from `Model/Repositories.php` and controller-layer services from `Controller/ApplicationServices.php`.
     * Domain entities: `Model/*Entities.php` (not auto-loaded here). Presenters: `View/PresentationHelpers.php`.
     */
    public function model(string $model)
    {
        $repoFile = __DIR__ . '/../Model/Repositories.php';
        if (file_exists($repoFile)) {
            require_once $repoFile;
            if (class_exists($model, false)) {
                return $this->modelInstantiateWithControllerHint($model);
            }
        }

        $serviceFile = __DIR__ . '/ApplicationServices.php';
        if (file_exists($serviceFile)) {
            require_once $serviceFile;
            if (class_exists($model, false)) {
                return $this->modelInstantiateWithControllerHint($model);
            }
        }

        throw new Exception("Class '{$model}' not found (Model/Repositories or Controller/ApplicationServices).");
    }

    /**
     * When a service constructor type-hints BaseController, pass this instance (logic lives on controllers).
     */
    private function modelInstantiateWithControllerHint(string $model): object
    {
        $ref = new \ReflectionClass($model);
        $ctor = $ref->getConstructor();
        if ($ctor !== null) {
            $params = $ctor->getParameters();
            if (count($params) === 1) {
                $t = $params[0]->getType();
                if ($t instanceof \ReflectionNamedType && !$t->isBuiltin()) {
                    $typeName = $t->getName();
                    if (is_a($typeName, BaseController::class, true)) {
                        return new $model($this);
                    }
                }
            }
        }

        return new $model();
    }

    protected function sessionService()
    {
        require_once __DIR__ . '/ApplicationServices.php';

        return new SessionService($this);
    }

    /**
     * Build view flash array from a FlashMessageEntity (mapping stays in controller layer).
     *
     * @return array{type: string, message: string}|null
     */
    protected function flashMessageToViewArray($flash): ?array
    {
        if ($flash === null || !is_object($flash) || !method_exists($flash, 'getType') || !method_exists($flash, 'getMessage')) {
            return null;
        }

        return ['type' => (string) $flash->getType(), 'message' => (string) $flash->getMessage()];
    }

    public function view(string $view, array $data = []): void
    {
        $viewFile = __DIR__ . '/../View/' . $view . '.php';
        $headerFile = __DIR__ . '/../View/layouts/header.php';
        $footerFile = __DIR__ . '/../View/layouts/footer.php';

        if (!file_exists($viewFile)) {
            throw new Exception("View '{$view}' not found");
        }

        if (!file_exists($headerFile) || !file_exists($footerFile)) {
            throw new Exception('Layout files are missing in View/layouts.');
        }

        if (!isset($data['errors'])) {
            $data['errors'] = $this->sessionService()->takeValidationMessages()->getMessages();
        }

        $data = array_merge($this->layoutPresentationData(), $data);

        extract($data);
        require $headerFile;
        require $viewFile;
        require $footerFile;
    }

    /**
     * Render a single view file with no layout (e.g. printable ticket, raw HTML response).
     *
     * @param string $view Path relative to View/ without .php (e.g. "FrontOffice/student/ticket")
     * @param array<string, mixed> $data Variables extracted for the template
     */
    protected function renderStandaloneView(string $view, array $data = []): void
    {
        $viewFile = __DIR__ . '/../View/' . $view . '.php';

        if (!file_exists($viewFile)) {
            throw new Exception("View '{$view}' not found");
        }

        extract($data, EXTR_SKIP);
        require $viewFile;
        exit;
    }

    /**
     * Layout-only values for the shared header (routing/theme flags). Controllers may override keys.
     *
     * @return array<string, mixed>
     */
    protected function layoutPresentationData(): array
    {
        $currentUrl = trim((string) ($_GET['url'] ?? ''), '/');
        if ($currentUrl === '') {
            $currentUrl = 'home/index';
        }

        $isAuthPage = str_starts_with($currentUrl, 'login')
            || str_starts_with($currentUrl, 'register')
            || str_starts_with($currentUrl, 'admin/login');

        $role = $_SESSION['role'] ?? null;

        $bodyClasses = ['neo-brand'];

        if (str_starts_with($currentUrl, 'student/evenements') || str_starts_with($currentUrl, 'student/evenement')) {
            $bodyClasses[] = 'theme-student-events';
        }

        if (str_starts_with($currentUrl, 'home/index')) {
            $bodyClasses[] = 'theme-home-lite';
        }

        if (str_starts_with($currentUrl, 'admin')) {
            $bodyClasses[] = 'theme-home-lite';
        }

        if (
            str_starts_with($currentUrl, 'student/dashboard')
            || str_starts_with($currentUrl, 'student/course')
            || str_starts_with($currentUrl, 'login')
            || str_starts_with($currentUrl, 'register')
            || (str_starts_with($currentUrl, 'admin') && !str_starts_with($currentUrl, 'admin/login'))
        ) {
            $bodyClasses[] = 'neo-dark-ui';
        }

        if ($isAuthPage) {
            $bodyClasses[] = 'auth-page';
        }

        if (!$isAuthPage && !in_array('theme-home-lite', $bodyClasses, true)) {
            $bodyClasses[] = 'theme-home-lite';
        }

        return [
            'currentUrl' => $currentUrl,
            'isAuthPage' => $isAuthPage,
            'role' => $role,
            'bodyClassAttr' => implode(' ', $bodyClasses),
            'viewerLoggedIn' => !empty($_SESSION['logged_in']),
            'viewerUserId' => (int) ($_SESSION['user_id'] ?? 0),
            'viewerUserName' => (string) ($_SESSION['user_name'] ?? ''),
        ];
    }

    protected function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id'], $_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    protected function isAdmin(): bool
    {
        return $this->isLoggedIn() && (($_SESSION['role'] ?? '') === 'admin');
    }

    /**
     * URL segment for shared student/teacher pages (groups, discussions).
     */
    protected function frontOfficeRoutePrefix(): string
    {
        return (($_SESSION['role'] ?? '') === 'teacher') ? 'teacher' : 'student';
    }

    protected function redirect(string $url): void
    {
        header('Location: ' . APP_ENTRY . '?url=' . ltrim($url, '/'));
        exit();
    }

    protected function sanitize($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'sanitize'], $data);
        }

        return htmlspecialchars(strip_tags(trim((string) $data)), ENT_QUOTES, 'UTF-8');
    }

    protected function groupeImageUrlFromRow(array $row): string
    {
        $raw = trim((string) ($row['image_url'] ?? $row['photo'] ?? $row['image'] ?? ''));
        if ($raw === '') {
            return '';
        }

        return $raw;
    }

    /**
     * Last-N-days activity trend for group edit analytics (shared student / admin).
     *
     * @param array<int, array<string, mixed>> $discussionRows
     * @param array<int, array<string, mixed>> $memberRows
     * @return array{labels: array<int, string>, discussions: array<int, int>, visitors: array<int, int>}
     */
    protected function buildGroupActivitySeries(int $groupId, array $discussionRows, array $memberRows, int $days = 14): array
    {
        $days = max(7, min(30, $days));
        $dateKeys = [];
        $labels = [];
        $discussionCounts = [];
        $visitors = [];

        $today = new \DateTimeImmutable('today');
        for ($i = $days - 1; $i >= 0; $i--) {
            $d = $today->sub(new \DateInterval('P' . $i . 'D'));
            $key = $d->format('Y-m-d');
            $dateKeys[] = $key;
            $labels[] = $d->format('d M');
            $discussionCounts[$key] = 0;
        }

        foreach ($discussionRows as $row) {
            $raw = (string) ($row['date_creation'] ?? $row['created_at'] ?? '');
            if ($raw === '') {
                continue;
            }
            $ts = strtotime($raw);
            if ($ts === false) {
                continue;
            }
            $k = date('Y-m-d', $ts);
            if (isset($discussionCounts[$k])) {
                $discussionCounts[$k]++;
            }
        }

        $memberBase = max(1, count($memberRows));
        foreach ($dateKeys as $k) {
            $dailyDiscussions = $discussionCounts[$k];
            $weekday = (int) date('N', strtotime($k));
            $weekendBoost = ($weekday >= 6) ? 1 : 0;
            $hashNoise = (int) (crc32((string) $groupId . '|' . $k) % 4);
            $visitorCount = max(2, (int) round($memberBase * 1.2) + ($dailyDiscussions * 3) + $weekendBoost + $hashNoise);
            $visitors[] = $visitorCount;
        }

        return [
            'labels' => $labels,
            'discussions' => array_values($discussionCounts),
            'visitors' => $visitors,
        ];
    }

    /**
     * @return array{url:?string, error:?string}
     */
    protected function handleGroupPhotoUpload(string $fieldName = 'group_photo', array $messages = []): array
    {
        return $this->layerFileUpload_handleGroupPhotoUpload($fieldName, $messages);
    }

    protected function deleteGroupPhotoFileIfManaged(?string $url): void
    {
        if ($url === null || $url === '') {
            return;
        }

        $base = rtrim(GROUP_UPLOAD_URL, '/') . '/';
        if (strncmp($url, $base, strlen($base)) !== 0) {
            return;
        }

        $pathPart = parse_url($url, PHP_URL_PATH);
        $file = $pathPart !== null && $pathPart !== '' ? basename($pathPart) : '';
        if ($file === '' || $file === '.' || $file === '..') {
            return;
        }

        $full = GROUP_UPLOAD_DIR . DIRECTORY_SEPARATOR . $file;
        if (is_file($full)) {
            @unlink($full);
        }
    }

    protected function jsonResponse(array $payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit();
    }

    /**
     * @return array{ok:bool,error:?string,url:?string,fileName:?string,mime:?string,size:int,messageType:?string}
     */
    protected function handleChatAttachmentUpload(string $fieldName = 'attachment'): array
    {
        return $this->layerFileUpload_handleChatAttachmentUpload($fieldName);
    }

    /* ---- Session layer (SessionService in Controller/ApplicationServices.php delegates here) ---- */

    public function layerSession_flashPersist(string $type, string $message): void
    {
        $this->layerSession_persistFlash(new FlashMessageEntity($type, $message));
    }

    /** @return array{type: string, message: string}|null */
    public function layerSession_flashConsumeForView(): ?array
    {
        $entity = $this->layerSession_takeFlash();

        return $entity === null ? null : ['type' => $entity->getType(), 'message' => $entity->getMessage()];
    }

    /** @param array<string, mixed> $errors */
    public function layerSession_validationPersist(array $errors): void
    {
        $this->layerSession_persistValidationMessages(new FormValidationMessagesEntity($errors));
    }

    public function layerSession_persistFlash(FlashMessageEntity $message): void
    {
        $_SESSION['flash'] = ['type' => $message->getType(), 'message' => $message->getMessage()];
    }

    public function layerSession_takeFlash(): ?FlashMessageEntity
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

    public function layerSession_persistValidationMessages(FormValidationMessagesEntity $entity): void
    {
        $_SESSION['form_errors'] = $entity->getMessages();
    }

    public function layerSession_takeValidationMessages(): FormValidationMessagesEntity
    {
        $errors = $_SESSION['form_errors'] ?? [];
        unset($_SESSION['form_errors']);

        return new FormValidationMessagesEntity(is_array($errors) ? $errors : []);
    }

    public function layerSession_consumeOld(): array
    {
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['old']);

        return is_array($old) ? $old : [];
    }

    /** @return array<int|string, mixed> */
    public function layerSession_pullInlineRegistrationErrors(): array
    {
        $errors = $_SESSION['errors'] ?? [];
        unset($_SESSION['errors']);

        return is_array($errors) ? $errors : [];
    }

    /* ---- File upload layer (BaseController; used via protected helpers) ---- */

    private function layerFileUpload_ensureGroupUploadDir(): void
    {
        require_once __DIR__ . '/../config/config.php';
        if (!is_dir(GROUP_UPLOAD_DIR)) {
            @mkdir(GROUP_UPLOAD_DIR, 0755, true);
        }
    }

    private function layerFileUpload_ensureChatUploadDir(): void
    {
        require_once __DIR__ . '/../config/config.php';
        if (!is_dir(CHAT_UPLOAD_DIR)) {
            @mkdir(CHAT_UPLOAD_DIR, 0755, true);
        }
    }

    /**
     * @return array{url:?string, error:?string}
     */
    public function layerFileUpload_handleGroupPhotoUpload(string $fieldName = 'group_photo', array $messages = []): array
    {
        require_once __DIR__ . '/../config/config.php';
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

        $this->layerFileUpload_ensureGroupUploadDir();
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
    public function layerFileUpload_handleChatAttachmentUpload(string $fieldName = 'attachment'): array
    {
        require_once __DIR__ . '/../config/config.php';
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
            'video/mp4' => ['ext' => 'mp4', 'type' => 'video'],
            'audio/webm' => ['ext' => 'webm', 'type' => 'audio'],
            // MediaRecorder / libmagic often label opus-in-webm as video (same container as audio-only WebM).
            'video/webm' => ['ext' => 'webm', 'type' => 'audio'],
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
            $head = @file_get_contents($tmp, false, null, 0, 4);
            if ($head === "\x1a\x45\xdf\xa3") {
                $mime = 'audio/webm';
            }
        }
        if (!isset($allowed[$mime])) {
            return ['ok' => false, 'error' => 'Unsupported attachment format.', 'url' => null, 'fileName' => null, 'mime' => null, 'size' => 0, 'messageType' => null];
        }

        $this->layerFileUpload_ensureChatUploadDir();
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

    /**
     * Group activity report payload (implementation moved from Model GroupActivityReportService).
     *
     * @return array<string, mixed>
     */
    public function implGroupActivityReportBuild(int $groupId): array
    {
        require_once __DIR__ . '/../config/database.php';
        $repoFile = __DIR__ . '/../Model/Repositories.php';
        if (!file_exists($repoFile)) {
            throw new InvalidArgumentException('Repositories not available');
        }
        require_once $repoFile;
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

    /**
     * Generate a random temporary password
     * @param int $length
     * @return string
     */
    protected function generateTempPassword(int $length = 10): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $password = '';
        $maxIndex = strlen($chars) - 1;

        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, $maxIndex)];
        }

        return $password;
    }
}
