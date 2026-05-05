<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/AdminController.php';
require_once __DIR__ . '/TeacherController.php';
require_once __DIR__ . '/StudentController.php';

class QuestionController extends BaseController
{
    private function role(): string
    {
        return (string) ($_SESSION['role'] ?? '');
    }

    private function admin(): AdminController
    {
        return new AdminController();
    }

    private function teacher(): TeacherController
    {
        return new TeacherController();
    }

    private function student(): StudentController
    {
        return new StudentController();
    }

    public function questions()
    {
        $r = $this->role();
        if ($r === 'admin') {
            $this->admin()->questions();
            return;
        }
        if ($r === 'teacher') {
            $this->teacher()->questions();
            return;
        }
        $this->redirect('login');
    }

    public function addQuestion()
    {
        $r = $this->role();
        if ($r === 'admin') {
            $this->admin()->addQuestion();
            return;
        }
        if ($r === 'teacher') {
            $this->teacher()->addQuestion();
            return;
        }
        $this->redirect('login');
    }

    public function storeQuestion()
    {
        $r = $this->role();
        if ($r === 'admin') {
            $this->admin()->storeQuestion();
            return;
        }
        if ($r === 'teacher') {
            $this->teacher()->storeQuestion();
            return;
        }
        $this->redirect('login');
    }

    public function editQuestion($id)
    {
        $r = $this->role();
        if ($r === 'admin') {
            $this->admin()->editQuestion($id);
            return;
        }
        if ($r === 'teacher') {
            $this->teacher()->editQuestion($id);
            return;
        }
        $this->redirect('login');
    }

    public function updateQuestion($id)
    {
        $r = $this->role();
        if ($r === 'admin') {
            $this->admin()->updateQuestion($id);
            return;
        }
        if ($r === 'teacher') {
            $this->teacher()->updateQuestion($id);
            return;
        }
        $this->redirect('login');
    }

    public function questionsBank()
    {
        $this->student()->questionsBank();
    }

    public function training()
    {
        $this->student()->training();
    }

    public function questionsBankDifficulty()
    {
        $this->student()->questionsBankDifficulty();
    }
}
