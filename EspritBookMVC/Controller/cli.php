<?php

/**
 * CLI entry for maintenance tasks (delegates to MaintenanceController).
 *
 * From repository root:
 *   php EspritBookMVC/Controller/cli.php fix-passwords
 *   php EspritBookMVC/Controller/cli.php help
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('CLI only');
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/MaintenanceController.php';

$command = $argv[1] ?? 'help';

$map = [
    'fix-passwords' => 'fixPasswords',
    'setup-teachers' => 'setupTeachers',
    'fix-accounts' => 'fixAccounts',
    'reset' => 'resetAccounts',
    'debug-login' => 'debugLogin',
    'test-auth' => 'testAuth',
    'help' => 'help',
];

$action = $map[$command] ?? null;
if ($action === null) {
    echo "Unknown command: {$command}\n\n";
    $command = 'help';
    $action = 'help';
}

$controller = new MaintenanceController();
$controller->$action();
