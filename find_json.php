<?php
$lines = file('c:\xampp\htdocs\touhamvc\Controller\QuizController.php');
foreach ($lines as $i => $line) {
    if (strpos($line, 'tryDecodeRelaxedJson') !== false || strpos($line, 'extractFirstJsonObject') !== false) {
        echo "Line " . ($i+1) . ": " . trim($line) . "\n";
    }
}
