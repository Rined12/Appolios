<?php
require_once __DIR__ . '/config/database.php';
$db = getConnection();
$stmt = $db->query("DESCRIBE teacher_applications");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($rows, JSON_PRETTY_PRINT);
