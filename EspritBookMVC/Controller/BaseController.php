<?php

abstract class BaseController
{
    public function model(string $model)
    {
        $modelFile = __DIR__ . '/../Model/' . $model . '.php';

        if (!file_exists($modelFile)) {
            throw new Exception("Model '{$model}' not found");
        }

        require_once $modelFile;
        return new $model();
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

        extract($data);
        require $headerFile;
        require $viewFile;
        require $footerFile;
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
     * URL segment for shared student/teacher pages (groups, discussions): "student" or "teacher".
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

    protected function groupeImageUrlFromRow(array $row): string
    {
        return trim((string) ($row['image_url'] ?? $row['photo'] ?? $row['image'] ?? ''));
    }

    protected function ensureGroupUploadDir(): void
    {
        if (!is_dir(GROUP_UPLOAD_DIR)) {
            @mkdir(GROUP_UPLOAD_DIR, 0755, true);
        }
    }

    /**
     * @param array<string,string> $messages Optional French (or other) overrides for error strings.
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
}
