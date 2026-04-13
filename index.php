<?php
/**
 * APPOLIOS - E-Learning Platform
 * Main Entry Point (Root)
 */

// Include configuration FIRST
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

// Start session (after config is loaded)
session_name(SESSION_NAME);
session_start();

// Include core files
require_once __DIR__ . '/app/core/Controller.php';
require_once __DIR__ . '/app/core/Model.php';
require_once __DIR__ . '/app/core/Router.php';

// Include routes
$router = require_once __DIR__ . '/routes/web.php';

// Dispatch the router
$router->dispatch();
