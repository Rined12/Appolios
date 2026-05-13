<?php
require_once __DIR__ . '/config/config.php';

session_name(SESSION_NAME);
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'admin';

$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['objective'] = 'UML heritage, polymorphisme';
$_POST['chapter_id'] = 1;
$_POST['difficulty'] = 'intermediate';
$_POST['count'] = 5;

require_once __DIR__ . '/Controller/QuizController.php';
$controller = new QuizController();
$controller->generateBlueprintAi();
