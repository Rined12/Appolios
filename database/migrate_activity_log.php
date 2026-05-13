<?php
/**
 * Create activity_log table (once per database).
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

try {
    $pdo = getConnection();
    echo "Connected to database.\n";

    $sqlFile = __DIR__ . '/add_activity_log.sql';
    if (!is_file($sqlFile)) {
        echo "SQL file not found: {$sqlFile}\n";
        exit(1);
    }

    $sql = file_get_contents($sqlFile);
    $pdo->exec($sql);
    echo "activity_log table created successfully (or already exists).\n";
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
    exit(1);
}
