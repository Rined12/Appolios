<?php
$desktopDir = 'C:\\Users\\user\\Desktop\\tahahama\\Model\\';
$htdocsDir = 'c:\\xampp\\htdocs\\tahahama\\Model\\';

$files = scandir($htdocsDir);
foreach ($files as $file) {
    if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
        $path = $htdocsDir . $file;
        if (filesize($path) === 0) {
            $srcPath = $desktopDir . $file;
            if (file_exists($srcPath)) {
                copy($srcPath, $path);
                echo "Restored: " . $file . "\n";
            } else {
                echo "Source not found for: " . $file . "\n";
            }
        }
    }
}
