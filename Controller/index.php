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
    // 1. Priorité au paramètre ?url= (utilisé par .htaccess)
    $queryRoute = trim((string) ($_GET['url'] ?? ''), '/');
    if ($queryRoute !== '') {
        return $queryRoute;
    }

    // 2. Fallback sur PATH_INFO (ex: index.php/controller/action)
    if (!empty($_SERVER['PATH_INFO'])) {
        return trim($_SERVER['PATH_INFO'], '/');
    }

    // 3. Détection via REQUEST_URI (pour serveur interne PHP ou Nginx)
    $requestPath = parse_url((string) ($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH) ?? '';
    $requestPath = trim((string) $requestPath);

    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $scriptDir = str_replace('\\', '/', dirname($scriptName));
    $scriptDir = rtrim($scriptDir, '/');

    // Retirer le dossier de base si présent
    if ($scriptDir !== '' && $scriptDir !== '/' && strpos($requestPath, $scriptDir) === 0) {
        $requestPath = substr($requestPath, strlen($scriptDir));
    }

    $requestPath = trim($requestPath, '/');

    // Retirer index.php si présent au début
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

    if (in_array($first, ['login', 'register', 'signup', 'logout', 'authenticate', 'admin'], true) && strtolower($second) === 'login') {
        $controller = 'AuthController';
        $action = 'login';
    } elseif ($first === 'auth' && $second === 'face-login-admin') {
        $controller = 'AuthController';
        $action = 'faceLoginAdmin';
    } elseif ($first === 'auth' && $second === 'save-face-descriptor') {
        $controller = 'AuthController';
        $action = 'saveFaceDescriptor';
    } elseif ($first === 'auth' && $second === 'check-face-unique') {
        $controller = 'AuthController';
        $action = 'checkFaceUnique';
    } elseif ($first === 'auth' && $second === 'face-login') {
        $controller = 'AuthController';
        $action = 'faceLogin';
    } elseif (in_array($first, ['login', 'register', 'signup', 'logout', 'authenticate'], true)) {
        $controller = 'AuthController';
        $actionMap = [
            'login' => 'login',
            'register' => 'register',
            'signup' => 'signup',
            'logout' => 'logout',
            'authenticate' => 'authenticate',
        ];
        $action = $actionMap[$first] ?? 'login';
    } elseif ($first === 'forgot-password') {
        $controller = 'AuthController';
        $action = 'forgotPassword';
    } elseif ($first === 'request-password-reset') {
        $controller = 'AuthController';
        $action = 'requestPasswordReset';
    } elseif ($first === 'verify-reset-code') {
        $controller = 'AuthController';
        $action = 'verifyResetCode';
    } elseif ($first === 'reset-password') {
        $controller = 'AuthController';
        $action = 'resetPassword';
    } elseif ($first === 'process-reset-password') {
        $controller = 'AuthController';
        $action = 'processResetPassword';
    } elseif (in_array($first, ['admin', 'student', 'teacher', 'auth', 'home', 'event', 'ressource'], true)) {
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
