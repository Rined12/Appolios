<?php
require_once __DIR__ . '/config/database.php';
try {
    $db = getConnection();
    $stmt = $db->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Columns in users:\n";
    foreach ($columns as $col) {
        echo $col['Field'] . " - " . $col['Type'] . " - " . $col['Null'] . " - " . $col['Default'] . "\n";
    }
} catch (Exception $e) {
    echo "Fatal Error: " . $e->getMessage();
}
