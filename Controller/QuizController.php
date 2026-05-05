<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/AdminController.php';
require_once __DIR__ . '/TeacherController.php';
require_once __DIR__ . '/StudentController.php';

class QuizController extends BaseController
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

    public function quizzes()
    {
        $this->admin()->quizzes();
    }

    public function quizHistory()
    {
        $this->admin()->quizHistory();
    }

    public function quizStats()
    {
        $r = $this->role();
        if ($r === 'admin') {
            $this->admin()->quizStats();
            return;
        }
        if ($r === 'teacher') {
            $this->teacher()->quizStats();
            return;
        }
        $this->redirect('login');
    }

    public function addQuiz()
    {
        $r = $this->role();
        if ($r === 'admin') {
            $this->admin()->addQuiz();
            return;
        }
        if ($r === 'teacher') {
            $this->teacher()->addQuiz();
            return;
        }
        $this->redirect('login');
    }

    public function storeQuiz()
    {
        $r = $this->role();
        if ($r === 'admin') {
            $this->admin()->storeQuiz();
            return;
        }
        if ($r === 'teacher') {
            $this->teacher()->storeQuiz();
            return;
        }
        $this->redirect('login');
    }

    public function editQuiz($id)
    {
        $r = $this->role();
        if ($r === 'admin') {
            $this->admin()->editQuiz($id);
            return;
        }
        if ($r === 'teacher') {
            $this->teacher()->editQuiz($id);
            return;
        }
        $this->redirect('login');
    }

    public function updateQuiz($id)
    {
        $r = $this->role();
        if ($r === 'admin') {
            $this->admin()->updateQuiz($id);
            return;
        }
        if ($r === 'teacher') {
            $this->teacher()->updateQuiz($id);
            return;
        }
        $this->redirect('login');
    }

    public function deleteQuiz($id)
    {
        $r = $this->role();
        if ($r === 'admin') {
            $this->admin()->deleteQuiz($id);
            return;
        }
        if ($r === 'teacher') {
            $this->teacher()->deleteQuiz($id);
            return;
        }
        $this->redirect('login');
    }

    public function approveQuiz($id)
    {
        $this->admin()->approveQuiz($id);
    }

    public function rejectQuiz($id)
    {
        $this->admin()->rejectQuiz($id);
    }

    public function duplicateQuiz($id)
    {
        $this->teacher()->duplicateQuiz($id);
    }

    public function quiz($id = null)
    {
        $r = $this->role();
        if ($r === 'teacher') {
            $this->teacher()->quiz();
            return;
        }
        if ($r === 'student') {
            $this->student()->quiz($id);
            return;
        }
        $this->redirect('login');
    }

    public function takeQuiz($id)
    {
        $this->student()->takeQuiz($id);
    }

    public function submitQuiz($id)
    {
        $this->student()->submitQuiz($id);
    }

    public function toggleFavoriteQuiz($quizId)
    {
        $this->student()->toggleFavoriteQuiz($quizId);
    }

    public function toggleRedoQuiz($quizId)
    {
        $this->student()->toggleRedoQuiz($quizId);
    }

    public function coach()
    {
        $this->student()->coach();
    }
}
