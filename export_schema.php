<?php
require 'C:\xampp\htdocs\tahahama\config\database.php';

try {
    $db = getConnection();
    $sqlContent = "-- APPOLIOS Database Schema Dump\n";
    $sqlContent .= "-- Generated on " . date('Y-m-d H:i:s') . "\n\n";
    $sqlContent .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

    // Get all tables
    $stmt = $db->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($tables as $table) {
        $sqlContent .= "-- Table structure for table `$table`\n";
        $sqlContent .= "DROP TABLE IF EXISTS `$table`;\n";
        
        $createStmt = $db->query("SHOW CREATE TABLE `$table`");
        $createRow = $createStmt->fetch(PDO::FETCH_ASSOC);
        
        $sqlContent .= $createRow['Create Table'] . ";\n\n";
    }

    $sqlContent .= "SET FOREIGN_KEY_CHECKS=1;\n";

    $desktopPath = 'C:\Users\user\Desktop\appolios_schema.sql';
    file_put_contents($desktopPath, $sqlContent);
    echo "Successfully generated schema at: " . $desktopPath;

} catch(Exception $e) {
    echo "Error: " . $e->getMessage();
}
