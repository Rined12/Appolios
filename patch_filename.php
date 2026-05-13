<?php
require 'config/database.php';
$db = getConnection();
try {
    $db->exec("ALTER TABLE avatars ADD COLUMN filename VARCHAR(255) NULL");
    echo "Success: Added filename to avatars";
} catch(Exception $e) {
    echo "Error: " . $e->getMessage();
}
