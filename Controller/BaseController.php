<?php

require_once __DIR__ . '/../config/database.php';

if (!function_exists('difficulty_label_fr')) {
    function difficulty_label_fr(string $code): string {
        $map = [
            'beginner' => 'Débutant',
            'intermediate' => 'Intermédiaire',
            'advanced' => 'Avancé',
        ];
        return $map[$code] ?? $code;
    }
}

abstract class BaseController
{
    public function __construct()
    {
    }

    protected function jsonResponse($data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit();
    }

    /** Same as Appolios-COUR-QUIZ: DB-layer classes live under Controller/ with *Controller names. */
    private array $controllerMap = [
        'Course' => 'CourseController',
        'User' => 'UserController',
        'Enrollment' => 'EnrollmentController',
        'Chapter' => 'ChapterController',
        'Lesson' => 'LessonController',
        'Evenement' => 'EvenementController',
        'EvenementRessource' => 'EvenementRessourceController',
        'Groupe' => 'GroupeController',
        'Discussion' => 'DiscussionController',
        'GroupPost' => 'GroupPostController',
        'GroupPostComment' => 'GroupPostCommentController',
        'GroupPostReaction' => 'GroupPostReactionController',
        'Badge' => 'BadgeController',
        'Category' => 'CategoryController',
        'Certificate' => 'CertificateController',
        'Review' => 'ReviewController',
        'Notification' => 'NotificationController',
        'TeacherApplication' => 'TeacherApplicationController',
        'ContactMessage' => 'ContactMessageController',
        'LessonProgress' => 'LessonProgressController',
        'CourseBookmark' => 'CourseBookmarkController',
        'CourseBadge' => 'CourseBadgeController',
        'UserXP' => 'UserXPController',
        'Payment' => 'PaymentController',
    ];

    protected function getDb()
    {
        return getConnection();
    }

    public function model(string $model)
    {
        if (isset($this->controllerMap[$model])) {
            $controllerName = $this->controllerMap[$model];
            $controllerFile = __DIR__ . '/' . $controllerName . '.php';

            if (file_exists($controllerFile)) {
                require_once $controllerFile;

                return new $controllerName();
            }
        }

        $groupModels = ['GroupPost' => true, 'GroupPostComment' => true, 'GroupPostReaction' => true];
        $modelFileBasename = isset($groupModels[$model]) ? 'Groupe' : $model;
        $modelFile = __DIR__ . '/../Model/' . $modelFileBasename . '.php';

        if (!file_exists($modelFile)) {
            throw new Exception("Model '{$model}' not found");
        }

        require_once $modelFile;

        $reflection = new ReflectionClass($model);
        if ($reflection->isAbstract()) {
            throw new Exception("Cannot instantiate abstract class {$model}");
        }

        return new $model();
    }

    public function view(string $view, array $data = []): void
    {
        $viewFile = __DIR__ . '/../View/' . $view . '.php';

        $isAdmin = $this->isAdmin();
        $isAdminView = str_contains($view, 'BackOffice/admin');
        $isProfileView = ($view === 'FrontOffice/student/profile');

        if ($isAdminView || ($isAdmin && $isProfileView)) {
            $headerFile = __DIR__ . '/../View/BackOffice/admin/partials/admin_header.php';
            $footerFile = __DIR__ . '/../View/BackOffice/admin/partials/admin_footer.php';

            if ($isProfileView) {
                $data['adminSidebarActive'] = 'profile';
            }
        } else {
            $headerFile = __DIR__ . '/../View/layouts/header.php';
            $footerFile = __DIR__ . '/../View/layouts/footer.php';
        }

        if (!file_exists($viewFile)) {
            throw new Exception("View '{$view}' not found");
        }

        if (!file_exists($headerFile) || !file_exists($footerFile)) {
            throw new Exception('Layout files are missing.');
        }

        if (!isset($data['errors'])) {
            $data['errors'] = $this->getErrors();
        }

        require_once __DIR__ . '/../Model/LanguageModel.php';
        $languageModel = new LanguageModel();
        $data['lang'] = $data['lang'] ?? $languageModel->getTranslations($languageModel->getCurrentLang());
        $data['currentLang'] = $data['currentLang'] ?? $languageModel->getCurrentLang();
        $data['availableLangs'] = $data['availableLangs'] ?? $languageModel->getAvailableLanguages();

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

    public function redirect(string $url): void
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

    public function setFlash(string $type, string $message): void
    {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message,
        ];
    }

    public function getFlash(): ?array
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

    /**
     * Save a single PHP upload to a directory (CollabHubDelegate, chat attachments, group covers, etc.).
     *
     * @return array{ok: bool, fileName?: string, originalName?: string, mime?: string, error?: string}
     */
    public function storeUploadedFile(array $file, string $targetDir): array
    {
        $err = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
        if ($err !== UPLOAD_ERR_OK) {
            return ['ok' => false, 'error' => 'Upload failed.'];
        }
        $tmp = (string) ($file['tmp_name'] ?? '');
        $orig = (string) ($file['name'] ?? 'file');
        if ($tmp === '' || !is_uploaded_file($tmp)) {
            return ['ok' => false, 'error' => 'Invalid upload.'];
        }
        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0775, true);
        }
        $ext = pathinfo($orig, PATHINFO_EXTENSION);
        $safeExt = $ext !== '' ? preg_replace('/[^a-zA-Z0-9]/', '', $ext) : '';
        $base = bin2hex(random_bytes(16));
        $fileName = $safeExt !== '' ? ($base . '.' . $safeExt) : $base;
        $dest = rtrim($targetDir, '/\\') . DIRECTORY_SEPARATOR . $fileName;
        if (!move_uploaded_file($tmp, $dest)) {
            return ['ok' => false, 'error' => 'Could not save file.'];
        }
        $mime = function_exists('mime_content_type') ? (string) @mime_content_type($dest) : '';
        return ['ok' => true, 'fileName' => $fileName, 'originalName' => $orig, 'mime' => $mime];
    }
}
