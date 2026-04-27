<?php
/**
 * Migration script to add avatar column to users table
 * Run this once to enable profile picture uploads
 */

require_once __DIR__ . '/../config/database.php';

try {
    $db = getConnection();

    // Check if column already exists
    $stmt = $db->query("SHOW COLUMNS FROM users LIKE 'avatar'");
    if ($stmt->rowCount() > 0) {
        echo "Column 'avatar' already exists in users table.\n";
    } else {
        // Add avatar column
        $db->exec("ALTER TABLE users ADD COLUMN avatar VARCHAR(255) NULL DEFAULT NULL AFTER face_descriptor");
        echo "Column 'avatar' added successfully to users table.\n";
    }

    // Create uploads directory
    $uploadDir = __DIR__ . '/../uploads/avatars/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
        echo "Upload directory created: {$uploadDir}\n";
    }

    echo "Migration completed successfully!\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
