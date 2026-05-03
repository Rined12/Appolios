<?php

abstract class BaseController
{
    public function __construct() {}

    // Mapping from model names to controller names
    private array $controllerMap = [
        'Course' => 'CourseController',
        'User' => 'UserController',
        'Enrollment' => 'EnrollmentController',
        'Chapter' => 'ChapterController',
        'Lesson' => 'LessonController',
        'Evenement' => 'EvenementController',
        'EvenementRessource' => 'EvenementRessourceController',
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

    public function model(string $model)
    {
        // Check if there's a corresponding controller
        if (isset($this->controllerMap[$model])) {
            $controllerName = $this->controllerMap[$model];
            $controllerFile = __DIR__ . '/' . $controllerName . '.php';
            
            if (file_exists($controllerFile)) {
                require_once $controllerFile;
                return new $controllerName();
            }
        }
        
        // Fall back to original model if controller doesn't exist
        $modelFile = __DIR__ . '/../Model/' . $model . '.php';

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
