<?php
require_once 'C:/xampp/htdocs/Appolios-feature-user/Appolios-feature-user/config/database.php';
$db = getConnection();

// Check ALL lessons for course 9 (join to get chapter titles)
$lessons = $db->query("
    SELECT l.id, l.title, l.chapter_id, c.title as chapter_title, c.course_id 
    FROM lessons l 
    JOIN chapters c ON l.chapter_id = c.id 
    WHERE c.course_id = 9 
    ORDER BY c.chapter_order, l.lesson_order
")->fetchAll(PDO::FETCH_ASSOC);

echo "=== ALL LESSONS for course 9 ===\n";
foreach($lessons as $l) {
    echo "Lesson: '{$l['title']}' | Chapter: '{$l['chapter_title']}' (chapter_id={$l['chapter_id']})\n";
}

// Check for duplicates
echo "\n=== Chapters ===\n";
$chapters = $db->query("SELECT id, title FROM chapters WHERE course_id = 9 ORDER BY chapter_order")->fetchAll(PDO::FETCH_ASSOC);
foreach($chapters as $c) {
    echo "Chapter: '{$c['title']}' (id={$c['id']})\n";
}
?>