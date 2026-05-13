<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/Controller/QuizController.php';

$controller = new QuizController();
$reflection = new ReflectionClass('QuizController');
$method = $reflection->getMethod('callGeminiGenerateText');
$method->setAccessible(true);

try {
    $result = $method->invokeArgs($controller, ["Hello! This is a test."]);
    echo "Result:\n" . $result;
} catch (Throwable $e) {
    echo "Error:\n" . $e->getMessage();
}
