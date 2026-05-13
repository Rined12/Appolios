<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

$db = getConnection();
try {
    $db->exec("ALTER TABLE question_bank ADD COLUMN created_by INT NOT NULL DEFAULT 1 AFTER difficulty");
    echo "Added created_by column to question_bank<br>";
} catch (Exception $e) {
    echo "Column exists or error: " . $e->getMessage() . "<br>";
}

try {
    $db->exec("ALTER TABLE question_bank ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER created_by");
    echo "Added created_at column to question_bank<br>";
} catch (Exception $e) {
    echo "created_at exists or error: " . $e->getMessage() . "<br>";
}

try {
    $db->exec("ALTER TABLE question_bank ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at");
    echo "Added updated_at column to question_bank<br>";
} catch (Exception $e) {
    echo "updated_at exists or error: " . $e->getMessage() . "<br>";
}

echo "Done!";
?>