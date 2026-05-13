<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

$db = getConnection();

// Add missing columns first
try {
    $db->exec("ALTER TABLE categories ADD COLUMN types TEXT DEFAULT NULL AFTER description");
    echo "Added 'types' column<br>";
} catch (Exception $e) {
    echo "types column already exists or error: " . $e->getMessage() . "<br>";
}

try {
    $db->exec("ALTER TABLE categories ADD COLUMN icon VARCHAR(50) DEFAULT 'folder' AFTER types");
    echo "Added 'icon' column<br>";
} catch (Exception $e) {
    echo "icon column already exists or error: " . $e->getMessage() . "<br>";
}

$categories = [
    ['Programming', 'Learn coding and software development', 'Web Development,Mobile Development,Desktop Development,Data Science,DevOps', 'code'],
    ['Design', 'Learn UI/UX and graphic design', 'Graphic Design,UI/UX Design,Web Design,Animation,Photography', 'palette'],
    ['Marketing', 'Learn digital marketing and business', 'Digital Marketing,SEO,Social Media,Content Marketing,Email Marketing', 'trending-up'],
    ['Business', 'Learn entrepreneurship and management', 'Entrepreneurship,Management,Finance,Accounting,Leadership', 'briefcase'],
    ['Languages', 'Learn new languages', 'English,Spanish,French,German,Chinese', 'globe'],
];

foreach ($categories as $cat) {
    $stmt = $db->prepare("INSERT INTO categories (name, description, types, icon) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE types=VALUES(types), icon=VALUES(icon)");
    $stmt->execute($cat);
}

echo "Sample categories added successfully!";
?>