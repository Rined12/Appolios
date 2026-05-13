<?php
$sourceFile = 'c:\\xampp\\htdocs\\touhamvc\\View\\assets\\css\\mvc-pro.css';
$targetFile = 'c:\\xampp\\htdocs\\Appolios\\View\\assets\\css\\admin-neo.css';

$content = file_get_contents($sourceFile);

// Find the start of .pro-table-page styles
$posStart = strpos($content, '.pro-table-page .pro-stats-grid {');
if ($posStart !== false) {
    // Extract everything from there to the end
    $proCSS = substr($content, $posStart);
    
    // Append to admin-neo.css
    file_put_contents($targetFile, "\n\n/* ======================================================== */\n/* PRO STYLES FOR QUIZ AND QUESTION BANK (IMPORTED) */\n/* ======================================================== */\n\n" . $proCSS, FILE_APPEND);
    
    echo "Successfully appended PRO styles to admin-neo.css!";
} else {
    echo "Could not find starting string in mvc-pro.css";
}
