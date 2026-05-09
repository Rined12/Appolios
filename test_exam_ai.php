<?php
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'teacher';
$_SESSION['logged_in'] = true;

$_POST['objective'] = 'sql';
$_POST['difficulty'] = 'intermediate';
$_POST['quiz_count'] = 3;
$_POST['questions_per_quiz'] = 10;
$_POST['chapter_ids'] = [7, 8, 9, 10]; // As seen in screenshot

require_once 'config/config.php';
require_once 'Controller/QuizController.php';

$controller = new QuizController();
$controller->generateExamAi();
