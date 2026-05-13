<?php
require_once __DIR__ . '/config/database.php';
try {
    $db = getConnection();
    
    try {
        $db->exec("ALTER TABLE teacher_applications MODIFY COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
        echo "Modified created_at.\n";
    } catch (Exception $e) { echo $e->getMessage() . "\n"; }

    try {
        $db->exec("ALTER TABLE teacher_applications MODIFY COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
        echo "Modified updated_at.\n";
    } catch (Exception $e) { echo $e->getMessage() . "\n"; }
    
    echo "Database patch complete.";
} catch (Exception $e) {
    echo "Fatal Error: " . $e->getMessage();
}
