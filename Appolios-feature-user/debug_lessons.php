<?php
require_once 'C:/xampp/htdocs/Appolios-feature-user/Appolios-feature-user/config/database.php';
$db = getConnection();

// Lessons for chapter 53 (HTML Fundamentals)
$lessons = $db->query("SELECT * FROM lessons WHERE chapter_id = 53 ORDER BY lesson_order")->fetchAll(PDO::FETCH_ASSOC);
echo "=== LESSONS for chapter 53 (HTML Fundamentals) ===\n";
foreach($lessons as $l) {
    echo "Lesson: '{$l['title']}' (type={$l['lesson_type']})\n";
}
?>