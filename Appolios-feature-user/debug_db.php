<?php
require_once 'C:/xampp/htdocs/Appolios-feature-user/Appolios-feature-user/config/database.php';
$db = getConnection();

// Get course 9 chapters
$chapters = $db->query("SELECT * FROM chapters WHERE course_id = 9 ORDER BY chapter_order")->fetchAll(PDO::FETCH_ASSOC);
echo "=== CHAPTERS for course 9 ===\n";
foreach($chapters as $c) {
    echo "Chapter {$c['chapter_order']}: ID={$c['id']} Title='{$c['title']}'\n";
}

echo "\n=== Check: any lessons with 'HTML Fundamentals' title? ===\n";
$lessons = $db->query("SELECT l.*, c.title as chapter_title FROM lessons l JOIN chapters c ON l.chapter_id = c.id WHERE l.title LIKE '%HTML%' OR l.title LIKE '%Fundamental%'")->fetchAll(PDO::FETCH_ASSOC);
foreach($lessons as $l) {
    echo "Lesson: '{$l['title']}' in Chapter: '{$l['chapter_title']}'\n";
}
?>