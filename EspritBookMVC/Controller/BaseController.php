<?php

abstract class BaseController
{
    /**
     * Resolve persistence (Repository/), application services (Service/), or legacy Model/ classes.
     */
    public function model(string $model)
    {
        $repositoryFile = __DIR__ . '/../Repository/' . $model . '.php';
        if (file_exists($repositoryFile)) {
            require_once $repositoryFile;
            return new $model();
        }

        $serviceFile = __DIR__ . '/../Service/' . $model . '.php';
        if (file_exists($serviceFile)) {
            require_once $serviceFile;
            return new $model();
        }

        $modelFile = __DIR__ . '/../Model/' . $model . '.php';
        if (file_exists($modelFile)) {
            require_once $modelFile;
            return new $model();
        }

        throw new Exception("Class '{$model}' not found in Repository, Service, or Model.");
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
            $data['errors'] = $this->getErrors();
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
     * @param string $view Path relative to View/ without .php (e.g. "student/ticket")
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

    protected function setFlash(string $type, string $message): void
    {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message,
        ];
    }

    protected function getFlash(): ?array
    {
        if (!isset($_SESSION['flash'])) {
            return null;
        }

        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);

        return $flash;
    }

    protected function setErrors(array $errors): void
    {
        $_SESSION['form_errors'] = $errors;
    }

    protected function getErrors(): array
    {
        $errors = $_SESSION['form_errors'] ?? [];
        unset($_SESSION['form_errors']);
        return $errors;
    }

    /** Repopulated form values after redirect (controllers consume; views never touch $_SESSION). */
    protected function consumeSessionOld(): array
    {
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['old']);

        return is_array($old) ? $old : [];
    }

    /** Inline validation lists stored under $_SESSION['errors'] (e.g. registration). */
    protected function consumeSessionInlineErrors(): array
    {
        $errors = $_SESSION['errors'] ?? [];
        unset($_SESSION['errors']);

        return is_array($errors) ? $errors : [];
    }

    protected function groupeImageUrlFromRow(array $row): string
    {
        $raw = trim((string) ($row['image_url'] ?? $row['photo'] ?? $row['image'] ?? ''));
        if ($raw === '') {
            return '';
        }

        return $raw;
    }

    protected function ensureGroupUploadDir(): void
    {
        if (!is_dir(GROUP_UPLOAD_DIR)) {
            @mkdir(GROUP_UPLOAD_DIR, 0755, true);
        }
    }

    /**
     * @return array{url:?string, error:?string}
     */
    protected function handleGroupPhotoUpload(string $fieldName = 'group_photo', array $messages = []): array
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

    protected function ensureChatUploadDir(): void
    {
        if (!is_dir(CHAT_UPLOAD_DIR)) {
            @mkdir(CHAT_UPLOAD_DIR, 0755, true);
        }
    }

    /**
     * @return array{ok:bool,error:?string,url:?string,fileName:?string,mime:?string,size:int,messageType:?string}
     */
    protected function handleChatAttachmentUpload(string $fieldName = 'attachment'): array
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
