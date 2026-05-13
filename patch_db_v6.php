<?php
require_once __DIR__ . '/config/database.php';
try {
    $db = getConnection();
    
    try {
        $db->exec("ALTER TABLE teacher_applications ADD COLUMN reviewed_by INT(11) NULL");
        echo "Added reviewed_by.\n";
    } catch (Exception $e) { echo $e->getMessage() . "\n"; }

    try {
        $db->exec("ALTER TABLE teacher_applications ADD COLUMN reviewed_at DATETIME NULL");
        echo "Added reviewed_at.\n";
    } catch (Exception $e) { echo $e->getMessage() . "\n"; }

    try {
        $db->exec("ALTER TABLE teacher_applications CHANGE admin_message admin_notes TEXT NULL");
        echo "Renamed admin_message to admin_notes.\n";
    } catch (Exception $e) {
        try {
            $db->exec("ALTER TABLE teacher_applications ADD COLUMN admin_notes TEXT NULL");
            echo "Added admin_notes column.\n";
        } catch (Exception $e2) {
            echo "admin_notes error: " . $e2->getMessage() . "\n";
        }
    }
    
    echo "Database patch complete.";
} catch (Exception $e) {
    echo "Fatal Error: " . $e->getMessage();
}
