<?php

require_once __DIR__ . '/StudentController.php';

class StudentQuizController extends StudentController
{
    protected function redirect(string $url): void
    {
        $u = $url;
        if (strncmp($u, 'student/', 8) === 0) {
            $u = 'student-quiz/' . substr($u, 8);
        }
        parent::redirect($u);
    }
}
