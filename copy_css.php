<?php
$sourceDir = 'C:\xampp\htdocs\touhamvc\View\assets\css\\';
$targetDir = 'C:\xampp\htdocs\Appolios\View\assets\css\\';

$files = ['mvc-pro.css', 'neo-ui.css', 'appolios.css', 'dark-mode.css', 'style.css'];

foreach ($files as $file) {
    if (file_exists($sourceDir . $file)) {
        copy($sourceDir . $file, $targetDir . $file);
        echo "Copied $file<br>";
    } else {
        echo "Missing $file<br>";
    }
}
echo "Done.";
