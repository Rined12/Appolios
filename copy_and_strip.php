<?php
$filesToCopy = [
    'quiz_stats.php',
    'risk_queue.php',
    'quiz_history.php',
    'ai_risk_review.php'
];

$sourceDir = 'c:\\xampp\\htdocs\\touhamvc\\View\\BackOffice\\admin\\';
$destDir = 'c:\\xampp\\htdocs\\Appolios\\View\\BackOffice\\admin\\';

foreach ($filesToCopy as $file) {
    if (file_exists($sourceDir . $file)) {
        copy($sourceDir . $file, $destDir . $file);
        echo "Copied $file\n";
    }
}

$filesToStrip = [
    'quiz_form.php',
    'question_form.php',
    'quiz_stats.php',
    'risk_queue.php',
    'quiz_history.php',
    'ai_risk_review.php'
];

foreach ($filesToStrip as $file) {
    $path = $destDir . $file;
    if (file_exists($path)) {
        $content = file_get_contents($path);
        
        // Remove opening wrappers
        $content = preg_replace('/<div class="dashboard">\s*<div class="container admin-dashboard-container">\s*<div class="admin-layout">\s*<\?php require __DIR__ \. \'\/partials\/sidebar\.php\'; \?>/s', '', $content);
        
        // Sometimes the class is just container
        $content = preg_replace('/<div class="dashboard">\s*<div class="container">\s*<div class="admin-layout">\s*<\?php require __DIR__ \. \'\/partials\/sidebar\.php\'; \?>/s', '', $content);
        
        // Change div class="admin-main pro-table-page" to just "pro-table-page" but we can just leave admin-main if we want.
        // Actually, let's just make it `<div class="pro-table-page" style="padding: 0;">`
        $content = preg_replace('/<div class="admin-main pro-table-page([^"]*)">/', '<div class="pro-table-page$1" style="padding: 0;">', $content);
        
        // Also risk_queue might not have pro-table-page. Let's replace `<div class="admin-main">` if present
        $content = preg_replace('/<div class="admin-main">/', '<div style="padding: 0;">', $content);
        
        // Remove trailing </div></div></div>
        $content = preg_replace('/<\/div>\s*<\/div>\s*<\/div>\s*<\/div>\s*<\/div>(\s*<script>|\s*$)/', '</div></div>$1', $content);
        $content = preg_replace('/<\/div>\s*<\/div>\s*<\/div>\s*<\/div>(\s*<script>|\s*$)/', '</div>$1', $content);
        
        // Update sidebar active state in quiz_form.php and others
        if ($file === 'quiz_form.php' || $file === 'quiz_stats.php' || $file === 'risk_queue.php' || $file === 'quiz_history.php' || $file === 'ai_risk_review.php') {
            $content = str_replace('$adminSidebarActive = \'quiz\';', '$adminSidebarActive = \'quizzes\';', $content);
        } elseif ($file === 'question_form.php') {
            $content = str_replace('$adminSidebarActive = \'questions\';', '$adminSidebarActive = \'questions_bank\';', $content);
        }
        
        file_put_contents($path, $content);
        echo "Stripped wrappers from $file\n";
    }
}
