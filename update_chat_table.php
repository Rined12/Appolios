<?php
require_once 'config/database.php';

$pdo = getConnection();

$sql = "
ALTER TABLE `discussion_messages` 
ADD COLUMN IF NOT EXISTS `room` varchar(255) DEFAULT NULL AFTER `discussion_id`,
ADD COLUMN IF NOT EXISTS `file_url` varchar(500) DEFAULT NULL AFTER `message_type`;
";

try {
    $pdo->exec($sql);
    echo "Discussion messages table updated successfully!\n";
} catch (PDOException $e) {
    echo "Error updating table: " . $e->getMessage() . "\n";
}
