<?php

require_once __DIR__ . '/TeacherController.php';

class TeacherQuizController extends TeacherController
{
    protected function redirect(string $url): void
    {
        $u = $url;
        if (strncmp($u, 'teacher/', 8) === 0) {
            $u = 'teacher-quiz/' . substr($u, 8);
        }
        parent::redirect($u);
    }
}
