<?php
/**
 * APPOLIOS Base Controller
 * All controllers extend this class
 */

abstract class Controller {
    protected $view;
    protected $model;

    /**
     * Load a model
     * @param string $model - Model name
     * @return object
     */
    public function model($model) {
        $modelFile = __DIR__ . '/../models/' . $model . '.php';

        if (file_exists($modelFile)) {
            require_once $modelFile;
            return new $model();
        }

        throw new Exception("Model '{$model}' not found");
    }

    /**
     * Load a view with data
     * @param string $view - View name
     * @param array $data - Data to pass to view
     */
    public function view($view, $data = []) {
        $viewFile = __DIR__ . '/../views/' . $view . '.php';

        if (file_exists($viewFile)) {
            // Extract data to variables
            extract($data);

            // Include header
            require_once __DIR__ . '/../views/partials/header.php';

            // Include the view
            require_once $viewFile;

            // Include footer
            require_once __DIR__ . '/../views/partials/footer.php';
        } else {
            throw new Exception("View '{$view}' not found");
        }
    }

    /**
     * Check if user is logged in
     * @return bool
     */
    protected function isLoggedIn() {
        return isset($_SESSION['user_id']) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    /**
     * Check if user is admin
     * @return bool
     */
    protected function isAdmin() {
        return $this->isLoggedIn() && $_SESSION['role'] === 'admin';
    }

    /**
     * Préfixe d’URL Social Learning : teacher/groupes ou student/groupes (redirections cohérentes).
     */
    protected function socialLearningGroupesPath(): string {
        if ($this->isLoggedIn() && ($_SESSION['role'] ?? '') === 'teacher') {
            return 'teacher/groupes';
        }
        return 'student/groupes';
    }

    /**
     * Redirect to URL
     * @param string $url
     */
    protected function redirect($url) {
        header('Location: ' . APP_URL . '/index.php?url=' . $url);
        exit();
    }

    /**
     * Sanitize input data
     * @param mixed $data
     * @return mixed
     */
    protected function sanitize($data) {
        if (is_array($data)) {
            return array_map([$this, 'sanitize'], $data);
        }
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Set flash message
     * @param string $type - success, error, warning, info
     * @param string $message
     */
    protected function setFlash($type, $message) {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message
        ];
    }

    /**
     * Get and clear flash message
     * @return array|null
     */
    protected function getFlash() {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }
}