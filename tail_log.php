<?php
$file = 'C:/xampp/php/logs/php_error_log';
if (!file_exists($file)) $file = 'C:/xampp/apache/logs/error.log';
if (file_exists($file)) {
    $lines = file($file);
    echo implode("", array_slice($lines, -50));
} else {
    echo "Log file not found.";
}
