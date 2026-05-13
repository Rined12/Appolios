<?php
require_once __DIR__ . '/config/database.php';
try {
    $db = getConnection();
    
    try {
        $db->exec("ALTER TABLE teacher_applications ADD COLUMN name VARCHAR(255) NULL");
        echo "Added name column.\n";
    } catch (Exception $e) { echo $e->getMessage() . "\n"; }

    try {
        $db->exec("ALTER TABLE teacher_applications ADD COLUMN password VARCHAR(255) NULL");
        echo "Added password column.\n";
    } catch (Exception $e) { echo $e->getMessage() . "\n"; }

    try {
        $db->exec("ALTER TABLE teacher_applications ADD COLUMN cv_filename VARCHAR(255) NULL");
        echo "Added cv_filename column.\n";
    } catch (Exception $e) { echo $e->getMessage() . "\n"; }
    
    echo "Database patch complete.";
} catch (Exception $e) {
    echo "Fatal Error: " . $e->getMessage();
}
