<?php

abstract class BaseController
{
    /**
     * Resolve persistence and app services from aggregate files under Model/: `Repositories.php`, `ApplicationServices.php`.
     * Domain entities live in Model/ root `*Entities.php` files; they are not loaded here. Presentation helpers: `PresentationHelpers.php` (included by controllers when needed).
     */
    public function model(string $model)
    {
        $repoFile = __DIR__ . '/../Model/Repositories.php';
        if (file_exists($repoFile)) {
            require_once $repoFile;
            if (class_exists($model, false)) {
                return new $model();
            }
        }

        $serviceFile = __DIR__ . '/../Model/ApplicationServices.php';
        if (file_exists($serviceFile)) {
            require_once $serviceFile;
            if (class_exists($model, false)) {
                return new $model();
            }
        }

        throw new Exception("Class '{$model}' not found in Model (Repositories or ApplicationServices).");
    }

    protected function sessionService()
    {
        require_once __DIR__ . '/../Model/ApplicationServices.php';

        return new SessionService();
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
     * @return array{url:?string, error:?string}
     */
    protected function handleGroupPhotoUpload(string $fieldName = 'group_photo', array $messages = []): array
    {
        return $this->model('FileUploadService')->handleGroupPhotoUpload($fieldName, $messages);
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
        return $this->model('FileUploadService')->handleChatAttachmentUpload($fieldName);
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
