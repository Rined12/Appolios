<?php
require_once __DIR__ . '/config/database.php';
try {
    $db = getConnection();
    
    try {
        $db->exec("ALTER TABLE teacher_applications MODIFY COLUMN user_id INT(11) NULL");
        echo "Modified user_id to be nullable.\n";
    } catch (Exception $e) { echo $e->getMessage() . "\n"; }

    try {
        $db->exec("ALTER TABLE teacher_applications MODIFY COLUMN motivation TEXT NULL");
        echo "Modified motivation to be nullable.\n";
    } catch (Exception $e) { echo $e->getMessage() . "\n"; }
    
    echo "Database patch complete.";
} catch (Exception $e) {
    echo "Fatal Error: " . $e->getMessage();
}
