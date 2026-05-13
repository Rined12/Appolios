<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

$db = getConnection();

// Check quiz_question_bank table
echo "Checking quiz_question_bank table...<br>";
try {
    $cols = $db->query("DESCRIBE quiz_question_bank")->fetchAll(PDO::FETCH_ASSOC);
    echo "Current columns: ";
    foreach ($cols as $c) {
        echo $c['Field'] . ", ";
    }
    echo "<br>";
} catch (Exception $e) {
    echo "Table doesn't exist or error<br>";
}

// Add missing columns
try {
    $db->exec("ALTER TABLE quiz_question_bank ADD COLUMN question_bank_id INT NOT NULL AFTER quiz_id");
    echo "Added question_bank_id column<br>";
} catch (Exception $e) {
    echo "question_bank_id exists or error: " . $e->getMessage() . "<br>";
}

try {
    $db->exec("ALTER TABLE quiz_question_bank ADD COLUMN sort_order INT NOT NULL DEFAULT 0 AFTER question_bank_id");
    echo "Added sort_order column<br>";
} catch (Exception $e) {
    echo "sort_order exists or error: " . $e->getMessage() . "<br>";
}

try {
    $db->exec("ALTER TABLE quiz_question_bank ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER sort_order");
    echo "Added created_at column<br>";
} catch (Exception $e) {
    echo "created_at exists or error: " . $e->getMessage() . "<br>";
}

// Check if table exists
try {
    $db->exec("CREATE TABLE IF NOT EXISTS quiz_question_bank (
        id INT AUTO_INCREMENT PRIMARY KEY,
        quiz_id INT NOT NULL,
        question_bank_id INT NOT NULL,
        sort_order INT NOT NULL DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE,
        FOREIGN KEY (question_bank_id) REFERENCES question_bank(id) ON DELETE CASCADE
    )");
    echo "quiz_question_bank table structure ensured<br>";
} catch (Exception $e) {
    echo "Table check error: " . $e->getMessage() . "<br>";
}

echo "Done!";
?>