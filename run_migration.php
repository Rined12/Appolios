<?php
require_once __DIR__ . '/Controller/QuestionController.php';
$ref = new ReflectionClass('QuestionController');
$m = $ref->getMethod('questions');
echo "questions starts at line " . $m->getStartLine();
