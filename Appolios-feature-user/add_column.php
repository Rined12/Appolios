<?php
require_once 'config/database.php';
$pdo = getConnection();
$pdo->exec("ALTER TABLE reviews ADD COLUMN is_approved TINYINT(1) DEFAULT 1");
echo "Done";