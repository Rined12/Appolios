<?php
$lines = file('c:\xampp\htdocs\touhamvc\Controller\QuestionController.php');
foreach ($lines as $i => $line) {
    if (strpos($line, 'function queryCreateQuestionBankQuestion') !== false) {
        echo "Line " . ($i+1) . ": " . trim($line) . "\n";
    }
}
