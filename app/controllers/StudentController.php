<?php
/**
 * APPOLIOS Student Controller
 * Handles student dashboard and course enrollment
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Course.php';
require_once __DIR__ . '/../models/Enrollment.php';
require_once __DIR__ . '/../models/Evenement.php';
require_once __DIR__ . '/../models/EvenementRessource.php';

class StudentController extends Controller {

    /**
     * Student dashboard
     */
    public function dashboard() {
        // Check if logged in
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Please login to access your dashboard.');
            $this->redirect('login');
            return;
        }

        $evenementModel = $this->model('Evenement');
        $evenements = $evenementModel->findApprovedUpcoming();

        $data = [
            'title' => 'My Dashboard - APPOLIOS',
            'description' => 'Student evenement dashboard',
            'userName' => $_SESSION['user_name'],
            'evenements' => $evenements,
            'flash' => $this->getFlash()
        ];

        $this->view('student/evenements', $data);
    }

    /**
     * Student evenements catalog page
     */
    public function evenements() {
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Please login to access events.');
            $this->redirect('login');
            return;
        }

        $evenementModel = $this->model('Evenement');

        $data = [
            'title' => 'Evenements - APPOLIOS',
            'description' => 'Browse upcoming evenements',
            'userName' => $_SESSION['user_name'],
            'evenements' => $evenementModel->findApprovedUpcoming(),
            'flash' => $this->getFlash()
        ];

        $this->view('student/evenements', $data);
    }

    /**
     * Student evenement detail page with resources
     */
    public function evenementDetail($id) {
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Please login to view evenement details.');
            $this->redirect('login');
            return;
        }

        $evenementModel = $this->model('Evenement');
        $ressourceModel = $this->model('EvenementRessource');

        $evenement = $evenementModel->findByIdWithCreator($id);
        if (!$evenement) {
            $this->setFlash('error', 'Evenement not found.');
            $this->redirect('student/evenements');
            return;
        }

        if (($evenement['approval_status'] ?? 'approved') !== 'approved') {
            $this->setFlash('error', 'This evenement is not available yet.');
            $this->redirect('student/evenements');
            return;
        }

        $grouped = $ressourceModel->getGroupedByEvenement($id);

        $data = [
            'title' => (($evenement['titre'] ?? '') ?: ($evenement['title'] ?? 'Evenement')) . ' - APPOLIOS',
            'description' => $evenement['description'] ?? 'Evenement details',
            'evenement' => $evenement,
            'rules' => $grouped['rules'],
            'materiels' => $grouped['materiels'],
            'plans' => $grouped['plans'],
            'flash' => $this->getFlash()
        ];

        $this->view('student/evenement_detail', $data);
    }

    /**
     * Browse all available courses (for students)
     */
    public function courses() {
        // Check if logged in
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Please login to browse courses.');
            $this->redirect('login');
            return;
        }

        // Only students can access this page
        if ($_SESSION['role'] !== 'student') {
            $this->setFlash('error', 'Access denied.');
            $this->redirect('login');
            return;
        }

        $courseModel = $this->model('Course');
        $enrollmentModel = $this->model('Enrollment');

        // Get all courses
        $allCourses = $courseModel->getAllWithCreator();

        // Get enrolled course IDs to mark them
        $enrollments = $enrollmentModel->getUserEnrollments($_SESSION['user_id']);
        $enrolledIds = array_column($enrollments, 'course_id');

        $data = [
            'title' => 'Catalogue des cours - APPOLIOS',
            'description' => 'Parcourir et s’inscrire aux cours',
            'courses' => $allCourses,
            'enrolledIds' => $enrolledIds,
            'flash' => $this->getFlash()
        ];

        $this->view('student/courses', $data);
    }

    /**
     * View course details
     */
    public function viewCourse($id) {
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Please login to view courses.');
            $this->redirect('login');
            return;
        }

        $courseModel = $this->model('Course');
        $enrollmentModel = $this->model('Enrollment');

        $course = $courseModel->getWithCreator($id);

        if (!$course) {
            $this->setFlash('error', 'Course not found.');
            $this->redirect('student/dashboard');
            return;
        }

        $isEnrolled = $enrollmentModel->isEnrolled($_SESSION['user_id'], $id);

        $chapters = [];
        $quizzesByChapter = [];
        $courseQuizCount = 0;
        if ($isEnrolled) {
            $chapterModel = $this->model('Chapter');
            $chapters = $chapterModel->getByCourseId((int) $id);
            $quizModel = $this->model('Quiz');
            foreach ($quizModel->getForEnrolledStudent((int) $_SESSION['user_id']) as $qz) {
                if ((int) ($qz['course_id'] ?? 0) !== (int) $id) {
                    continue;
                }
                $courseQuizCount++;
                $chid = (int) ($qz['chapter_id'] ?? 0);
                if ($chid > 0) {
                    if (!isset($quizzesByChapter[$chid])) {
                        $quizzesByChapter[$chid] = [];
                    }
                    $quizzesByChapter[$chid][] = $qz;
                }
            }
        }

        $data = [
            'title' => $course['title'] . ' - APPOLIOS',
            'description' => $course['description'],
            'course' => $course,
            'isEnrolled' => $isEnrolled,
            'chapters' => $chapters,
            'quizzesByChapter' => $quizzesByChapter,
            'courseQuizCount' => $courseQuizCount,
            'studentSidebarActive' => $isEnrolled ? 'my-courses' : 'courses',
            'flash' => $this->getFlash()
        ];

        $this->view('student/course', $data);
    }

    /**
     * Enroll in a course
     */
    public function enroll($id) {
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Please login to enroll in courses.');
            $this->redirect('login');
            return;
        }

        $enrollmentModel = $this->model('Enrollment');

        // Check if already enrolled
        if ($enrollmentModel->isEnrolled($_SESSION['user_id'], $id)) {
            $this->setFlash('info', 'You are already enrolled in this course.');
            $this->redirect('student/course/' . $id);
            return;
        }

        // Enroll user
        if ($enrollmentModel->enroll($_SESSION['user_id'], $id)) {
            $this->setFlash('success', 'Successfully enrolled in the course!');
        } else {
            $this->setFlash('error', 'Failed to enroll. Please try again.');
        }

        $this->redirect('student/course/' . $id);
    }

    /**
     * My courses page
     */
    public function myCourses() {
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Please login to view your courses.');
            $this->redirect('login');
            return;
        }

        $enrollmentModel = $this->model('Enrollment');
        $enrollments = $enrollmentModel->getUserEnrollments($_SESSION['user_id']);

        $data = [
            'title' => 'Mes cours - APPOLIOS',
            'description' => 'Vos cours et accès aux chapitres et quiz',
            'enrollments' => $enrollments,
            'flash' => $this->getFlash()
        ];

        $this->view('student/my_courses', $data);
    }

    /**
     * Student profile page
     */
    public function profile() {
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Please login to view your profile.');
            $this->redirect('login');
            return;
        }

        require_once __DIR__ . '/../models/User.php';
        $userModel = $this->model('User');
        $user = $userModel->findById($_SESSION['user_id']);

        $data = [
            'title' => 'My Profile - APPOLIOS',
            'description' => 'Student profile',
            'user' => $user,
            'flash' => $this->getFlash()
        ];

        $this->view('student/profile', $data);
    }

    /**
     * Hub « Mon espace » avec accès cours, chapitres, quiz, etc.
     */
    public function espace() {
        if (!$this->requireStudentRole()) {
            return;
        }
        $evenementModel = $this->model('Evenement');
        $enrollmentModel = $this->model('Enrollment');
        $quizModel = $this->model('Quiz');
        $uid = (int) $_SESSION['user_id'];
        // protect against missing tables or DB errors to avoid fatal exception
        try {
            $chapters = $quizModel->getChaptersForEnrolledStudent($uid);
            $quizzes = $quizModel->getForEnrolledStudent($uid);
            $chapterCount = count($chapters);
            $quizCount = count($quizzes);
            $flash = $this->getFlash();
        } catch (PDOException $e) {
            // database schema may be missing - surface a friendly message
            $chapterCount = 0;
            $quizCount = 0;
            $flash = $this->getFlash();
            $flash['error'][] = 'Database schema error: ' . $e->getMessage();
            $flash['error'][] = 'Try importing the project SQL (see APPOLIOS/database/appolios_db.sql)';
        }

        $this->view('student/espace', [
            'title' => 'Mon espace - APPOLIOS',
            'userName' => $_SESSION['user_name'],
            'evenements' => $evenementModel->findApprovedUpcoming(),
            'enrollmentCount' => count($enrollmentModel->getUserEnrollments($uid)),
            'chapterCount' => $chapterCount,
            'quizCount' => $quizCount,
            'flash' => $flash,
        ]);
    }

    public function chapitres() {
        if (!$this->requireStudentRole()) {
            return;
        }
        $quizModel = $this->model('Quiz');
        $uid = (int) $_SESSION['user_id'];
        try {
            $chapters = $quizModel->getChaptersForEnrolledStudent($uid);
            $quizzes = $quizModel->getForEnrolledStudent($uid);
        } catch (PDOException $e) {
            $this->setFlash('error', 'Database schema error: ' . $e->getMessage());
            $chapters = [];
            $quizzes = [];
        }
        $quizzesByChapter = [];
        foreach ($quizzes as $qz) {
            $chid = (int) ($qz['chapter_id'] ?? 0);
            if ($chid > 0) {
                if (!isset($quizzesByChapter[$chid])) {
                    $quizzesByChapter[$chid] = [];
                }
                $quizzesByChapter[$chid][] = $qz;
            }
        }
        $chaptersByCourse = [];
        foreach ($chapters as $ch) {
            $coid = (int) ($ch['course_id'] ?? 0);
            if (!isset($chaptersByCourse[$coid])) {
                $chaptersByCourse[$coid] = [
                    'course_title' => $ch['course_title'] ?? '',
                    'chapters' => [],
                ];
            }
            $chaptersByCourse[$coid]['chapters'][] = $ch;
        }
        ksort($chaptersByCourse, SORT_NUMERIC);
        $this->view('student/chapitres', [
            'title' => 'Mes chapitres - APPOLIOS',
            'chaptersByCourse' => $chaptersByCourse,
            'quizzesByChapter' => $quizzesByChapter,
            'flash' => $this->getFlash(),
        ]);
    }

    public function quizList() {
        if (!$this->requireStudentRole()) {
            return;
        }
        $quizModel = $this->model('Quiz');
        $this->view('student/quiz_list', [
            'title' => 'Quiz - APPOLIOS',
            'quizzes' => $quizModel->getForEnrolledStudent((int) $_SESSION['user_id']),
            'flash' => $this->getFlash(),
        ]);
    }

    public function takeQuiz($id) {
        if (!$this->requireStudentRole()) {
            return;
        }
        $quizModel = $this->model('Quiz');
        $quiz = $quizModel->findWithChapterCourse((int) $id);
        if (!$quiz) {
            $this->setFlash('error', 'Quiz introuvable.');
            $this->redirect('student/quiz');
            return;
        }
        $enrollmentModel = $this->model('Enrollment');
        if (!$enrollmentModel->isEnrolled((int) $_SESSION['user_id'], (int) $quiz['course_id'])) {
            $this->setFlash('error', 'Vous devez être inscrit au cours pour passer ce quiz.');
            $this->redirect('student/quiz');
            return;
        }
        if (empty($quiz['questions'])) {
            $this->setFlash('error', 'Ce quiz ne contient pas encore de questions.');
            $this->redirect('student/quiz');
            return;
        }
        $this->view('student/take_quiz', [
            'title' => ($quiz['title'] ?? 'Quiz') . ' - APPOLIOS',
            'quiz' => $quiz,
            'flash' => $this->getFlash(),
        ]);
    }

    public function submitQuiz($id) {
        if (!$this->requireStudentRole()) {
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('student/quiz/' . (int) $id);
            return;
        }
        $quizModel = $this->model('Quiz');
        $quiz = $quizModel->findWithChapterCourse((int) $id);
        if (!$quiz) {
            $this->setFlash('error', 'Quiz introuvable.');
            $this->redirect('student/quiz');
            return;
        }
        $enrollmentModel = $this->model('Enrollment');
        if (!$enrollmentModel->isEnrolled((int) $_SESSION['user_id'], (int) $quiz['course_id'])) {
            $this->setFlash('error', 'Accès refusé.');
            $this->redirect('student/quiz');
            return;
        }
        $questions = $quiz['questions'] ?? [];
        $answers = $_POST['answers'] ?? [];
        if (!is_array($answers)) {
            $answers = [];
        }
        $score = 0;
        foreach ($questions as $i => $q) {
            $expected = isset($q['correctAnswer']) ? (int) $q['correctAnswer'] : 0;
            $given = isset($answers[$i]) ? (int) $answers[$i] : -1;
            if ($given === $expected) {
                $score++;
            }
        }
        $total = count($questions);
        $percentage = $total > 0 ? (int) round(($score / $total) * 100) : 0;
        $attemptModel = $this->model('QuizAttempt');
        $attemptModel->record((int) $_SESSION['user_id'], (int) $id, $score, $total, $percentage);
        $this->view('student/quiz_result', [
            'title' => 'Résultat du quiz - APPOLIOS',
            'quiz' => $quiz,
            'score' => $score,
            'total' => $total,
            'percentage' => $percentage,
        ]);
    }

    public function questionsBank() {
        if (!$this->requireStudentRole()) {
            return;
        }
        $this->model('Quiz');
        $qb = $this->model('QuestionBank');
        $this->view('student/questions_bank', [
            'title' => 'Banque de questions - APPOLIOS',
            'questions' => $qb->getAllReadable(),
            'flash' => $this->getFlash(),
        ]);
    }

    public function quizHistory() {
        if (!$this->requireStudentRole()) {
            return;
        }
        $attemptModel = $this->model('QuizAttempt');
        $this->view('student/quiz_history', [
            'title' => 'Historique des quiz - APPOLIOS',
            'attempts' => $attemptModel->getByUserWithQuizTitles((int) $_SESSION['user_id']),
            'flash' => $this->getFlash(),
        ]);
    }

    /**
     * @return bool true si étudiant connecté
     */
    private function requireStudentRole() {
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Connexion requise.');
            $this->redirect('login');
            return false;
        }
        if (($_SESSION['role'] ?? '') !== 'student') {
            $this->setFlash('error', 'Espace réservé aux étudiants.');
            $this->redirect('login');
            return false;
        }
        return true;
    }
}