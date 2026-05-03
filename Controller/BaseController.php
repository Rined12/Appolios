<?php

abstract class BaseController
{
    protected function db(): PDO
    {
        require_once __DIR__ . '/../config/database.php';
        return getConnection();
    }

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
        $helpersFile = __DIR__ . '/../View/helpers.php';

        $useLayout = true;
        if (array_key_exists('_layout', $data) && $data['_layout'] === false) {
            $useLayout = false;
        }

        if (!file_exists($viewFile)) {
            throw new Exception("View '{$view}' not found");
        }

        if ($useLayout && (!file_exists($headerFile) || !file_exists($footerFile))) {
            throw new Exception('Layout files are missing in View/layouts.');
        }

        if (file_exists($helpersFile)) {
            require_once $helpersFile;
        }

        if (!isset($data['errors'])) {
            $data['errors'] = $this->getErrors();
        }

        extract($data);
        if ($useLayout) {
            require $headerFile;
        }
        require $viewFile;
        if ($useLayout) {
            require $footerFile;
        }
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
}
