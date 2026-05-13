<?php
require 'config/database.php';
$db = getConnection();
try {
    $stmt = $db->query("DESCRIBE evenements");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "evenements columns: " . implode(', ', $columns) . "\n";
    
    $stmt = $db->query("DESCRIBE evenement_ressources");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "evenement_ressources columns: " . implode(', ', $columns) . "\n";
    
    $stmt = $db->query("DESCRIBE ressources");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "ressources columns: " . implode(', ', $columns) . "\n";
} catch(Exception $e) {
    echo "Error: " . $e->getMessage();
}
