<?php
require_once __DIR__ . '/config/database.php';

$db = getConnection();

// Add sort_order to chapters if missing
try {
    $db->exec("ALTER TABLE chapters ADD COLUMN sort_order INT DEFAULT 0");
    echo "Added sort_order to chapters\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "sort_order already exists in chapters\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

// Add chapter_id to quizzes if missing
try {
    $db->exec("ALTER TABLE quizzes ADD COLUMN chapter_id INT DEFAULT NULL");
    echo "Added chapter_id to quizzes\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "chapter_id already exists in quizzes\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

echo "Done! Chapters and quizzes now have sort_order and chapter_id columns.\n";