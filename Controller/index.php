<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

session_name(SESSION_NAME);
session_start();

function toCamelCaseAction(string $value): string
{
    return lcfirst(str_replace(' ', '', ucwords(str_replace('-', ' ', $value))));
}

function resolveRoutePath(): string
{
    $queryRoute = trim((string) ($_GET['url'] ?? ''), '/');
    if ($queryRoute !== '') {
        return $queryRoute;
    }

    $requestPath = parse_url((string) ($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH) ?? '';
    $requestPath = trim((string) $requestPath);

    $scriptDir = str_replace('\\', '/', dirname((string) ($_SERVER['SCRIPT_NAME'] ?? '')));
    $scriptDir = rtrim($scriptDir, '/');

    if ($scriptDir !== '' && $scriptDir !== '/' && strncmp($requestPath, $scriptDir, strlen($scriptDir)) === 0) {
        $requestPath = substr($requestPath, strlen($scriptDir));
    }

    $requestPath = trim($requestPath, '/');

    if ($requestPath === 'index.php') {
        return '';
    }

    if (strncmp($requestPath, 'index.php/', 10) === 0) {
        return substr($requestPath, 10);
    }

    return $requestPath;
}

$routePath = resolveRoutePath();
$segments = $routePath === '' ? [] : explode('/', $routePath);

$controller = 'HomeController';
$action = 'index';
$params = [];

if (!empty($segments)) {
    $first = strtolower($segments[0]);
    $second = $segments[1] ?? '';

    if ($first === 'admin' && strtolower($second) === 'login') {
        $controller = 'AuthController';
        $action = 'adminLogin';
    } elseif (in_array($first, ['login', 'register', 'signup', 'logout', 'authenticate'], true)) {
        $controller = 'AuthController';
        $action = [
            'login' => 'login',
            'register' => 'register',
            'signup' => 'signup',
            'logout' => 'logout',
            'authenticate' => 'authenticate',
        ][$first];
    } elseif (in_array($first, ['admin', 'student', 'teacher', 'auth', 'home', 'book'], true)) {
        $controller = ucfirst($first) . 'Controller';
        $action = $second !== '' ? toCamelCaseAction($second) : ($first === 'auth' ? 'login' : 'index');
        $params = array_slice($segments, 2);
    } else {
        $controller = 'HomeController';
        $action = toCamelCaseAction($first);
        $params = array_slice($segments, 1);
    }
}

$controllerFile = __DIR__ . '/' . $controller . '.php';
if (!file_exists($controllerFile)) {
    $controller = 'HomeController';
    $action = 'notFound';
    $params = [];
    $controllerFile = __DIR__ . '/HomeController.php';
}

require_once __DIR__ . '/BaseController.php';
require_once $controllerFile;

$instance = new $controller();
if (!method_exists($instance, $action)) {
    require_once __DIR__ . '/HomeController.php';
    $instance = new HomeController();
    $action = 'notFound';
    $params = [];
}

call_user_func_array([$instance, $action], $params);
