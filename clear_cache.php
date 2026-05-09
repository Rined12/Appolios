<?php
session_start();
unset($_SESSION['gemini_cache']);

$dir = sys_get_temp_dir();
$files = glob($dir . DIRECTORY_SEPARATOR . 'appolios_gemini_*.txt');
$count = 0;
if (is_array($files)) {
    foreach ($files as $f) {
        @unlink($f);
        $count++;
    }
}
echo "Cleared $count cached files.\n";
