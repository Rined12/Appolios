<?php
require_once 'config/config.php';
require_once 'Controller/QuizController.php';

$controller = new QuizController();
$reflection = new ReflectionClass(QuizController::class);

$method = $reflection->getMethod('buildGeminiExamPlanPrompt');
$method->setAccessible(true);
$prompt = $method->invoke($controller, [
    'objective' => 'sql',
    'difficulty' => 'intermediate',
    'quizCount' => 3,
    'qPerQuiz' => 10,
    'chapters' => [7 => 'Chapitre 7', 8 => 'Chapitre 8', 9 => 'Chapitre 9']
]);

$methodCall = $reflection->getMethod('callGeminiGenerateText');
$methodCall->setAccessible(true);
$rawText = $methodCall->invoke($controller, $prompt);

echo "LENGTH: " . strlen($rawText) . "\n\n";
echo "RAW:\n" . substr($rawText, 0, 500) . "\n...\n" . substr($rawText, -500);
