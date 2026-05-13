<?php
$sourceDir = 'c:/xampp/htdocs/touhamvc/View/FrontOffice/teacher';
$destDir = 'c:/xampp/htdocs/Appolios/View/FrontOffice/teacher';

if (!is_dir($destDir)) {
    mkdir($destDir, 0777, true);
}

$filesToCopy = [
    'questions_bank.php',
    'question_form.php',
    'quizzes.php',
    'quiz_form.php',
    'quiz_stats.php',
    'exam_builder.php',
    'remediation_plan.php',
    'remediation_quiz_detail.php'
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
