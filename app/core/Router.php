<?php
/**
 * APPOLIOS Router Class
 * Handles URL routing and dispatches to controllers
 */

class Router {
    protected $routes = [];
    protected $notFoundController = 'HomeController';
    protected $notFoundAction = 'notFound';

    /**
     * Add a route
     * @param string $url - URL pattern
     * @param string $controller - Controller name
     * @param string $action - Method name
     * @param string $method - HTTP method (GET, POST, etc.)
     */
    public function add($url, $controller, $action, $method = 'GET') {
        $this->routes[] = [
            'url' => $url,
            'controller' => $controller,
            'action' => $action,
            'method' => $method
        ];
    }

    /**
     * Parse the current URL
     * @return string
     */
    protected function parseUrl() {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            return $url;
        }
        return '';
    }

    /**
     * Dispatch the route
     */
    public function dispatch() {
        $url = $this->parseUrl();
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        // Check for matching route
        foreach ($this->routes as $route) {
            $pattern = $this->convertToRegex($route['url']);

            if (preg_match($pattern, $url, $matches) && $route['method'] === $requestMethod) {
                // Remove the full match from matches
                array_shift($matches);

                // Call the controller action with parameters
                $this->callAction($route['controller'], $route['action'], $matches);
                return;
            }
        }

        // No route found, show 404
        $this->callAction($this->notFoundController, $this->notFoundAction, []);
    }

    /**
     * Convert route pattern to regex
     * @param string $url
     * @return string
     */
    protected function convertToRegex($url) {
        // Escape special regex characters
        $pattern = preg_replace('/\//', '\/', $url);

        // Convert {param} to capture groups
        $pattern = preg_replace('/\{([a-zA-Z0-9]+)\}/', '([^\/]+)', $pattern);

        // Add start and end anchors
        $pattern = '/^' . $pattern . '$/';

        return $pattern;
    }

    /**
     * Call controller action
     * @param string $controller
     * @param string $action
     * @param array $params
     */
    protected function callAction($controller, $action, $params) {
        $controllerFile = __DIR__ . '/../controllers/' . $controller . '.php';

        if (file_exists($controllerFile)) {
            require_once $controllerFile;

            if (class_exists($controller)) {
                $controllerInstance = new $controller();

                if (method_exists($controllerInstance, $action)) {
                    call_user_func_array([$controllerInstance, $action], $params);
                    return;
                }
            }
        }

        // Controller or action not found
        http_response_code(404);
        echo "<h1>404 - Page Not Found</h1>";
        echo "<p>The requested page could not be found.</p>";
    }
}