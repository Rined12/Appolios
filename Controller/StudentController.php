<?php
/**
 * APPOLIOS Student Controller
 * Handles student dashboard and course enrollment
 */

require_once __DIR__ . '/../Controller/BaseController.php';
require_once __DIR__ . '/../Model/Course.php';
require_once __DIR__ . '/../Model/Enrollment.php';
require_once __DIR__ . '/../Model/Evenement.php';
require_once __DIR__ . '/../Model/EvenementRessource.php';
require_once __DIR__ . '/../Model/Chapter.php';
require_once __DIR__ . '/QuizQuestionValidation.php';

class StudentController extends BaseController {

    /**
     * Route alias for /student/evenement/{id}
     */
    public function evenement($id) {
        $this->evenementDetail($id);
    }

    /**
     * Route alias for /student/course/{id}
     */
    public function course($id) {
        $this->viewCourse($id);
    }

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

        $this->view('FrontOffice/student/evenements', $data);
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

        $this->view('FrontOffice/student/evenements', $data);
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

        $this->view('FrontOffice/student/evenement_detail', $data);
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
            'title' => 'Browse Courses - APPOLIOS',
            'description' => 'Explore all available courses',
            'courses' => $allCourses,
            'enrolledIds' => $enrolledIds,
            'flash' => $this->getFlash()
        ];

        $this->view('FrontOffice/student/courses', $data);
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

        $courseId = (int) $id;
        $userId = (int) $_SESSION['user_id'];
        $isEnrolled = $enrollmentModel->isEnrolled($userId, $courseId);

        $courseQuizCount = 0;
        $attemptedCourseQuizCount = 0;
        $courseProgress = 0;
        if ($isEnrolled) {
            $quizService = $this->service('QuizService');
            $enrolledQuizzes = $quizService->getQuizzesForEnrolledStudent($userId);
            $courseQuizIds = [];
            foreach ($enrolledQuizzes as $quizRow) {
                if ((int) ($quizRow['course_id'] ?? 0) === $courseId) {
                    $courseQuizIds[(int) ($quizRow['id'] ?? 0)] = true;
                }
            }

            $courseQuizCount = count($courseQuizIds);
            if ($courseQuizCount > 0) {
                $attempts = $quizService->getAttemptsByUser($userId);
                $attemptedQuizIds = [];
                foreach ($attempts as $attempt) {
                    $attemptQuizId = (int) ($attempt['quiz_id'] ?? 0);
                    if (isset($courseQuizIds[$attemptQuizId])) {
                        $attemptedQuizIds[$attemptQuizId] = true;
                    }
                }
                $attemptedCourseQuizCount = count($attemptedQuizIds);
                $courseProgress = (int) round(($attemptedCourseQuizCount / $courseQuizCount) * 100);
            }
        }

        $data = [
            'title' => $course['title'] . ' - APPOLIOS',
            'description' => $course['description'],
            'course' => $course,
            'isEnrolled' => $isEnrolled,
            'courseQuizCount' => $courseQuizCount,
            'attemptedCourseQuizCount' => $attemptedCourseQuizCount,
            'courseProgress' => $courseProgress,
            'flash' => $this->getFlash()
        ];

        $this->view('FrontOffice/student/course', $data);
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
            'title' => 'My Courses - APPOLIOS',
            'description' => 'Your enrolled courses',
            'enrollments' => $enrollments,
            'flash' => $this->getFlash()
        ];

        $this->view('FrontOffice/student/my_courses', $data);
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

        require_once __DIR__ . '/../Model/User.php';
        $userModel = $this->model('User');
        $user = $userModel->findById($_SESSION['user_id']);

        $data = [
            'title' => 'My Profile - APPOLIOS',
            'description' => 'Student profile',
            'user' => $user,
            'flash' => $this->getFlash()
        ];

        $this->view('FrontOffice/student/profile', $data);
    }

    /* ---------- Chapitres / Quiz (étudiant) ---------- */

    public function chapitres() {
        if (!$this->requireStudentRole()) {
            return;
        }

        $quizService = $this->service('QuizService');
        $uid = (int) $_SESSION['user_id'];
        $chapters = $quizService->getChaptersForEnrolledStudent($uid);
        $quizzes = $quizService->getQuizzesForEnrolledStudent($uid);

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

        $this->view('FrontOffice/student/chapitres', [
            'title' => 'Mes chapitres - ' . APP_NAME,
            'chaptersByCourse' => $chaptersByCourse,
            'quizzesByChapter' => $quizzesByChapter,
            'flash' => $this->getFlash(),
        ]);
    }

    /**
     * /student/quiz  (liste)
     * /student/quiz/{id} (passer un quiz)
     */
    public function quiz($id = null) {
        if ($id !== null && $id !== '') {
            $this->takeQuiz((int) $id);
            return;
        }
        if (!$this->requireStudentRole()) {
            return;
        }

        $quizService = $this->service('QuizService');
        $this->view('FrontOffice/student/quiz_list', [
            'title' => 'Mes quiz - ' . APP_NAME,
            'quizzes' => $quizService->getQuizzesForEnrolledStudent((int) $_SESSION['user_id']),
            'flash' => $this->getFlash(),
        ]);
    }

    public function takeQuiz($id) {
        if (!$this->requireStudentRole()) {
            return;
        }

        $quizService = $this->service('QuizService');
        $quiz = $quizService->findWithChapterCourse((int) $id);
        if (!$quiz) {
            $this->setFlash('error', 'Quiz introuvable.');
            $this->redirect('student/quiz');
            return;
        }

        if (!empty($quiz['status']) && $quiz['status'] !== 'approved') {
            $this->setFlash('error', 'Ce quiz n’est pas disponible pour le moment.');
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

        $this->view('FrontOffice/student/take_quiz', [
            'title' => ($quiz['title'] ?? 'Quiz') . ' - ' . APP_NAME,
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

        $quizService = $this->service('QuizService');
        $quiz = $quizService->findWithChapterCourse((int) $id);
        if (!$quiz) {
            $this->setFlash('error', 'Quiz introuvable.');
            $this->redirect('student/quiz');
            return;
        }

        if (!empty($quiz['status']) && $quiz['status'] !== 'approved') {
            $this->setFlash('error', 'Ce quiz n’est pas disponible pour le moment.');
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
        $timedOut = !empty($_POST['timed_out']);
        if (!$timedOut) {
            $ansErr = QuizQuestionValidation::validateStudentQuizAnswers($answers, $questions);
            if ($ansErr !== null) {
                $this->setFlash('error', $ansErr);
                $this->redirect('student/quiz/' . (int) $id);
                return;
            }
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
        $quizService->recordAttempt((int) $_SESSION['user_id'], (int) $id, $score, $total, $percentage);

        $this->view('FrontOffice/student/quiz_result', [
            'title' => 'Résultat du quiz - ' . APP_NAME,
            'quiz' => $quiz,
            'score' => $score,
            'total' => $total,
            'percentage' => $percentage,
            'timed_out' => $timedOut,
        ]);
    }

    public function questionsBank() {
        if (!$this->requireStudentRole()) {
            return;
        }
        $quizService = $this->service('QuizService');
        $this->view('FrontOffice/student/questions_bank', [
            'title' => 'Banque de questions - ' . APP_NAME,
            'questions' => $quizService->getQuestionBankReadable(),
            'flash' => $this->getFlash(),
        ]);
    }

    public function quizHistory() {
        if (!$this->requireStudentRole()) {
            return;
        }
        $quizService = $this->service('QuizService');
        $this->view('FrontOffice/student/quiz_history', [
            'title' => 'Historique des quiz - ' . APP_NAME,
            'attempts' => $quizService->getAttemptsByUser((int) $_SESSION['user_id']),
            'flash' => $this->getFlash(),
        ]);
    }

    private function requireStudentRole(): bool {
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