<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

try {
    $pdo = getConnection();
    echo "Connected to DB.<br>";
    
    // 1. Add face_descriptor
    $sql1 = "ALTER TABLE users ADD COLUMN IF NOT EXISTS face_descriptor LONGTEXT NULL DEFAULT NULL COMMENT 'JSON-encoded 128-float face descriptor for Face ID login'";
    $pdo->exec($sql1);
    echo "Added face_descriptor to users.<br>";
    
    $sql2 = "ALTER TABLE teacher_applications ADD COLUMN IF NOT EXISTS face_descriptor LONGTEXT NULL DEFAULT NULL COMMENT 'JSON-encoded 128-float face descriptor captured at registration'";
    $pdo->exec($sql2);
    echo "Added face_descriptor to teacher_applications.<br>";

    // 2. Add avatar
    $sql3 = "ALTER TABLE users ADD COLUMN IF NOT EXISTS avatar VARCHAR(255) NULL DEFAULT NULL AFTER face_descriptor";
    $pdo->exec($sql3);
    echo "Added avatar to users.<br>";

    echo "DONE.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
