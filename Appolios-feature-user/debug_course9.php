<?php
require_once 'C:/xampp/htdocs/Appolios-feature-user/Appolios-feature-user/config/database.php';
$pdo = getConnection();
$chapters = $pdo->query("SELECT * FROM chapters WHERE course_id = 9 ORDER BY chapter_order")->fetchAll(PDO::FETCH_ASSOC);
echo "=== CHAPTERS ===\n";
foreach($chapters as $c) {
    echo "Chapter {$c['chapter_order']}: {$c['title']} (id={$c['id']})\n";
}
foreach($chapters as $c) {
    $lessons = $pdo->query("SELECT * FROM lessons WHERE chapter_id = {$c['id']} ORDER BY lesson_order")->fetchAll(PDO::FETCH_ASSOC);
    echo "\nLessons for chapter {$c['id']}:\n";
    foreach($lessons as $l) {
        echo "  - {$l['title']} (type={$l['lesson_type']})\n";
    }
}
?>