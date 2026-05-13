<?php
require_once __DIR__ . '/config/database.php';
try {
    $db = getConnection();
    $stmt = $db->query("DESCRIBE teacher_applications");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Columns in teacher_applications:\n";
    foreach ($columns as $col) {
        echo $col['Field'] . "\n";
    }
} catch (Exception $e) {
    echo "Fatal Error: " . $e->getMessage();
}
