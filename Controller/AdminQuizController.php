<?php

require_once __DIR__ . '/AdminController.php';

class AdminQuizController extends AdminController
{
    protected function redirect(string $url): void
    {
        $u = $url;
        if (strncmp($u, 'admin/', 6) === 0) {
            $u = 'admin-quiz/' . substr($u, 6);
        }
        parent::redirect($u);
    }
}
