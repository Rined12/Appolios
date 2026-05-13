<?php
$targetFile = 'c:\\xampp\\htdocs\\Appolios\\View\\assets\\css\\admin-neo.css';
$content = file_get_contents($targetFile);
$content = str_replace('body.theme-quiz-pro', '.admin-body', $content);
file_put_contents($targetFile, $content);
echo "Successfully replaced body.theme-quiz-pro with .admin-body in admin-neo.css!";
