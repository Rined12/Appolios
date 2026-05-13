<?php
require_once __DIR__ . '/config/database.php';

$db = getConnection();

// Add is_favorite to quizzes if missing
try {
    $db->exec("ALTER TABLE quizzes ADD COLUMN is_favorite TINYINT(1) DEFAULT 0");
    echo "Added is_favorite to quizzes\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "is_favorite already exists in quizzes\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

// Add difficulty to quizzes if missing
try {
    $db->exec("ALTER TABLE quizzes ADD COLUMN difficulty VARCHAR(50) DEFAULT 'medium'");
    echo "Added difficulty to quizzes\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "difficulty already exists in quizzes\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

// Add status to quizzes if missing
try {
    $db->exec("ALTER TABLE quizzes ADD COLUMN status VARCHAR(20) DEFAULT 'draft'");
    echo "Added status to quizzes\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "status already exists in quizzes\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

// Add tags to quizzes if missing
try {
    $db->exec("ALTER TABLE quizzes ADD COLUMN tags VARCHAR(255) DEFAULT ''");
    echo "Added tags to quizzes\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "tags already exists in quizzes\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

echo "Done!\n";