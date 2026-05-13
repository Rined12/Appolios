<?php
require_once __DIR__ . '/config/database.php';
try {
    $db = getConnection();
    
    // Add reset_token and reset_token_expiry to users
    try {
        $db->exec("ALTER TABLE users ADD COLUMN reset_token VARCHAR(255) NULL");
        $db->exec("ALTER TABLE users ADD COLUMN reset_token_expiry DATETIME NULL");
        echo "Added reset_token columns to users.\n";
    } catch (Exception $e) {
        echo "Error adding reset_token (might already exist): " . $e->getMessage() . "\n";
    }

    // Add email to teacher_applications
    try {
        $db->exec("ALTER TABLE teacher_applications ADD COLUMN email VARCHAR(255) NULL");
        echo "Added email column to teacher_applications.\n";
    } catch (Exception $e) {
        echo "Error adding email (might already exist): " . $e->getMessage() . "\n";
    }
    
    echo "Database patch complete.";
} catch (Exception $e) {
    echo "Fatal Error: " . $e->getMessage();
}
