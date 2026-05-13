<?php
$sourceDir = 'c:/xampp/htdocs/touhamvc/View/FrontOffice/teacher/partials';
$destDir = 'c:/xampp/htdocs/Appolios/View/FrontOffice/teacher/partials';

if (!is_dir($destDir)) {
    mkdir($destDir, 0777, true);
}

$filesToCopy = [
    'sidebar.php'
];

foreach ($filesToCopy as $file) {
    $src = "$sourceDir/$file";
    $dst = "$destDir/$file";
    if (file_exists($src)) {
        copy($src, $dst);
        echo "Copied $file\n";
    } else {
        echo "Source file $file not found.\n";
    }
}
echo "Done.";
