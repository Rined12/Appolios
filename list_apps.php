<?php
require_once __DIR__ . '/config/database.php';
try {
    $db = getConnection();
    $stmt = $db->query("SELECT * FROM teacher_applications");
    $apps = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Total rows: " . count($apps) . "\n";
    foreach ($apps as $app) {
        print_r($app);
    }
} catch (Exception $e) {
    echo "Fatal Error: " . $e->getMessage();
}
