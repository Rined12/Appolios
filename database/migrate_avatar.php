<?php
/**
 * Avatar table migration script
 * Run this once to create the avatars table
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

try {
    $pdo = getConnection();
    echo "Connected to database.\n";

    // Read and execute the SQL file
    $sqlFile = __DIR__ . '/create_avatar_table.sql';
    if (file_exists($sqlFile)) {
        $sql = file_get_contents($sqlFile);
        $pdo->exec($sql);
        echo "Avatar table created successfully!\n";
    } else {
        echo "SQL file not found.\n";
    }

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
