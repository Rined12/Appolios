<?php
/**
 * Update lessons to support text, pdf, and both types
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

$db = getConnection();

echo "Updating lesson_type enum...\n";

try {
    $db->exec("ALTER TABLE lessons MODIFY lesson_type ENUM('video', 'text', 'pdf', 'both') DEFAULT 'text'");
    echo "Enum updated successfully.\n";
} catch (Exception $e) {
    echo "Note: " . $e->getMessage() . "\n";
}

echo "Updating existing lessons to 'text' type...\n";
$db->exec("UPDATE lessons SET lesson_type = 'text' WHERE lesson_type = 'video' AND (content IS NOT NULL AND content != '')");

echo "Done!\n";