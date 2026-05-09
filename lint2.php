<?php
$file = 'c:\xampp\htdocs\touhamvc\Controller\TeacherController.php';
$output = shell_exec('php -l ' . escapeshellarg($file));
echo "Shell exec output: " . $output . "\n";
echo "File size: " . filesize($file) . " bytes\n";
echo "File lines: " . count(file($file)) . "\n";
