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
require_once __DIR__ . '/../Model/Repositories/QuizRepository.php';
require_once __DIR__ . '/../Model/Repositories/QuizAttemptRepository.php';
require_once __DIR__ . '/../Model/Repositories/StudentRankRepository.php';
require_once __DIR__ . '/../Model/Repositories/StudentQuizFlagRepository.php';

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

        $rankRepo = new StudentRankRepository();
        $rank = $rankRepo->getOrCreate((int) $_SESSION['user_id']);

        $attemptRepo = new QuizAttemptRepository();
        $rankProgress = $this->rankProgressInfo((int) ($rank['rating'] ?? 1000));
        $spark = $attemptRepo->getLastPercentagesByUser((int) $_SESSION['user_id'], 7);

        $flagRepo = new StudentQuizFlagRepository();
        $favoriteIds = $flagRepo->getFavoriteQuizIds((int) $_SESSION['user_id']);
        $redoIds = $flagRepo->getRedoQuizIds((int) $_SESSION['user_id']);

        $data = [
            'title' => 'My Dashboard - APPOLIOS',
            'description' => 'Student evenement dashboard',
            'userName' => $_SESSION['user_name'],
            'evenements' => $evenements,
            'rank' => $rank,
            'rankProgress' => $rankProgress,
            'rankSpark' => $spark,
            'favoriteQuizIds' => $favoriteIds,
            'redoQuizIds' => $redoIds,
            'flash' => $this->getFlash()
        ];

        $this->view('FrontOffice/student/evenements', $data);
    }

    public function coach() {
        if (!$this->requireStudentRole()) {
            return;
        }

        $uid = (int) $_SESSION['user_id'];

        $rankRepo = new StudentRankRepository();
        $rank = $rankRepo->getOrCreate($uid);

        $attemptRepo = new QuizAttemptRepository();
        $rankProgress = $this->rankProgressInfo((int) ($rank['rating'] ?? 1000));
        $spark = $attemptRepo->getLastPercentagesByUser($uid, 7);

        $quizRepo = new QuizRepository();
        $all = $quizRepo->getForEnrolledStudent($uid);

        $chapterNames = [];
        foreach ($all as $q) {
            $cid = (int) ($q['chapter_id'] ?? 0);
            if ($cid <= 0) {
                continue;
            }
            if (!isset($chapterNames[$cid])) {
                $label = (string) ($q['chapter_title'] ?? '');
                $course = (string) ($q['course_title'] ?? '');
                $chapterNames[$cid] = trim($course !== '' ? ($course . ' — ' . $label) : $label);
            }
        }

        $chapterAverages = $attemptRepo->getChapterAveragesByUser($uid);
        uasort($chapterAverages, static function ($a, $b) {
            $av = (float) ($a['avg'] ?? 0);
            $bv = (float) ($b['avg'] ?? 0);
            return $av <=> $bv;
        });

        $weakChapters = [];
        foreach ($chapterAverages as $cid => $info) {
            $weakChapters[] = [
                'chapter_id' => (int) $cid,
                'chapter_title' => (string) ($chapterNames[(int) $cid] ?? ''),
                'avg' => (int) round((float) ($info['avg'] ?? 0)),
                'attempts' => (int) ($info['attempts'] ?? 0),
            ];
            if (count($weakChapters) >= 3) {
                break;
            }
        }

        $recommended = [];
        $queryChapterId = isset($weakChapters[0]) ? (int) ($weakChapters[0]['chapter_id'] ?? 0) : 0;
        foreach ($all as $q) {
            if (count($recommended) >= 4) {
                break;
            }
            $qid = (int) ($q['id'] ?? 0);
            if ($qid <= 0) {
                continue;
            }
            if ($queryChapterId > 0 && (int) ($q['chapter_id'] ?? 0) !== $queryChapterId) {
                continue;
            }
            $recommended[] = [
                'quiz_id' => $qid,
                'title' => (string) ($q['title'] ?? ('Quiz #' . $qid)),
                'chapter_title' => (string) ($q['chapter_title'] ?? ''),
                'difficulty' => (string) ($q['difficulty'] ?? 'beginner'),
                'reason' => $queryChapterId > 0 ? 'Chapitre à renforcer (moyenne faible)' : 'Suggestion',
            ];
        }

        $actions = [];
        $actions[] = [
            'title' => 'Objectif Rank',
            'text' => 'Vise +10 points de rating en faisant 2 quiz (consolidation + challenge).',
            'url' => APP_ENTRY . '?url=student/quiz',
        ];
        if (!empty($weakChapters)) {
            $actions[] = [
                'title' => 'Remédiation',
                'text' => 'Travaille le chapitre le plus faible puis retente un quiz après 24h.',
                'url' => APP_ENTRY . '?url=student/chapitres',
            ];
        }

        $this->view('FrontOffice/student/coach', [
            'title' => 'Coach - ' . APP_NAME,
            'rank' => $rank,
            'rankProgress' => $rankProgress,
            'rankSpark' => $spark,
            'weakChapters' => $weakChapters,
            'recommendedQuizzes' => $recommended,
            'actions' => $actions,
            'flash' => $this->getFlash(),
        ]);
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
        $rankRepo = new StudentRankRepository();
        $rank = $rankRepo->getOrCreate((int) $_SESSION['user_id']);

        $attemptRepo = new QuizAttemptRepository();
        $rankProgress = $this->rankProgressInfo((int) ($rank['rating'] ?? 1000));
        $spark = $attemptRepo->getLastPercentagesByUser((int) $_SESSION['user_id'], 7);

        $flagRepo = new StudentQuizFlagRepository();
        $flags = $flagRepo->getFlagsMapByUser((int) $_SESSION['user_id']);
        $filter = (string) ($_GET['filter'] ?? '');
        $this->view('FrontOffice/student/quiz_list', [
            'title' => 'Mes quiz - ' . APP_NAME,
            'quizzes' => $quizService->getQuizzesForEnrolledStudent((int) $_SESSION['user_id']),
            'rank' => $rank,
            'rankProgress' => $rankProgress,
            'rankSpark' => $spark,
            'flags' => $flags,
            'filter' => $filter,
            'flash' => $this->getFlash(),
        ]);
    }

    public function toggleFavoriteQuiz($quizId) {
        if (!$this->requireStudentRole()) {
            return;
        }
        $qid = (int) $quizId;
        if ($qid <= 0) {
            $this->redirect('student/quiz');
            return;
        }

        $flagRepo = new StudentQuizFlagRepository();
        $flagRepo->toggleFavorite((int) $_SESSION['user_id'], $qid);
        $back = isset($_SERVER['HTTP_REFERER']) ? (string) $_SERVER['HTTP_REFERER'] : '';
        if ($back !== '') {
            header('Location: ' . $back);
            return;
        }
        $this->redirect('student/quiz');
    }

    public function toggleRedoQuiz($quizId) {
        if (!$this->requireStudentRole()) {
            return;
        }
        $qid = (int) $quizId;
        if ($qid <= 0) {
            $this->redirect('student/quiz');
            return;
        }

        $flagRepo = new StudentQuizFlagRepository();
        $flagRepo->toggleRedo((int) $_SESSION['user_id'], $qid);
        $back = isset($_SERVER['HTTP_REFERER']) ? (string) $_SERVER['HTTP_REFERER'] : '';
        if ($back !== '') {
            header('Location: ' . $back);
            return;
        }
        $this->redirect('student/quiz');
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

        $quizRepo = new QuizRepository();
        $attemptRepo = new QuizAttemptRepository();
        $recommendations = $this->buildAfterQuizRecommendations($quizRepo, $attemptRepo, $quiz, $percentage, (int) $_SESSION['user_id']);

        $rankRepo = new StudentRankRepository();
        $rankBefore = $rankRepo->getOrCreate((int) $_SESSION['user_id']);
        $ratingUpdate = $this->updateStudentRatingAfterQuiz($rankRepo, $rankBefore, $quiz, $percentage);

        $rankAfter = $rankRepo->getOrCreate((int) $_SESSION['user_id']);
        $rankProgress = $this->rankProgressInfo((int) ($rankAfter['rating'] ?? ($ratingUpdate['new_rating'] ?? 1000)));
        $spark = $attemptRepo->getLastPercentagesByUser((int) $_SESSION['user_id'], 7);

        $coach = $this->buildRankCoachMessage($quiz, $percentage, $ratingUpdate, $rankProgress);

        $chapterAverages = $attemptRepo->getChapterAveragesByUser((int) $_SESSION['user_id']);
        uasort($chapterAverages, static function ($a, $b) {
            $av = (float) ($a['avg'] ?? 0);
            $bv = (float) ($b['avg'] ?? 0);
            return $av <=> $bv;
        });

        $chapterNames = [];
        $enrolledQuizzes = $quizRepo->getForEnrolledStudent((int) $_SESSION['user_id']);
        foreach ($enrolledQuizzes as $qz) {
            $cid = (int) ($qz['chapter_id'] ?? 0);
            if ($cid <= 0) {
                continue;
            }
            if (!isset($chapterNames[$cid])) {
                $label = (string) ($qz['chapter_title'] ?? '');
                $course = (string) ($qz['course_title'] ?? '');
                $chapterNames[$cid] = trim($course !== '' ? ($course . ' — ' . $label) : $label);
            }
        }

        $weakChapters = [];
        foreach ($chapterAverages as $cid => $info) {
            $weakChapters[] = [
                'chapter_id' => (int) $cid,
                'chapter_title' => (string) ($chapterNames[(int) $cid] ?? ''),
                'avg' => (int) round((float) ($info['avg'] ?? 0)),
                'attempts' => (int) ($info['attempts'] ?? 0),
            ];
            if (count($weakChapters) >= 3) {
                break;
            }
        }

        $this->view('FrontOffice/student/quiz_result', [
            'title' => 'Résultat du quiz - ' . APP_NAME,
            'quiz' => $quiz,
            'score' => $score,
            'total' => $total,
            'percentage' => $percentage,
            'timed_out' => $timedOut,
            'recommendations' => $recommendations,
            'rank_before' => $rankBefore,
            'rank_update' => $ratingUpdate,
            'rank_progress' => $rankProgress,
            'rank_spark' => $spark,
            'coach' => $coach,
            'weakChapters' => $weakChapters,
        ]);
    }

    private function rankMilestones(): array
    {
        return [
            'Bronze' => ['start' => 0, 'end' => 1100],
            'Silver' => ['start' => 1100, 'end' => 1300],
            'Gold' => ['start' => 1300, 'end' => 1500],
            'Platinum' => ['start' => 1500, 'end' => 1700],
        ];
    }

    private function rankProgressInfo(int $rating): array
    {
        $rating = max(0, $rating);
        [$league, $division] = $this->leagueForRating($rating);
        $m = $this->rankMilestones();
        $start = (int) ($m[$league]['start'] ?? 0);
        $end = (int) ($m[$league]['end'] ?? ($start + 200));
        $span = max(1, $end - $start);

        $divSpan = (int) floor($span / 3);
        if ($divSpan < 1) {
            $divSpan = 1;
        }

        if ($division === 'III') {
            $divStart = $start;
            $divEnd = min($end, $start + $divSpan);
            $nextLabel = $league . ' II';
        } elseif ($division === 'II') {
            $divStart = $start + $divSpan;
            $divEnd = min($end, $start + 2 * $divSpan);
            $nextLabel = $league . ' I';
        } else {
            $divStart = $start + 2 * $divSpan;
            $divEnd = $end;
            $nextLabel = 'Next League';
        }

        $pct = 0;
        $divDen = max(1, $divEnd - $divStart);
        $pct = (int) round(max(0, min(1, ($rating - $divStart) / $divDen)) * 100);
        $toNext = max(0, $divEnd - $rating);

        return [
            'rating' => $rating,
            'league' => $league,
            'division' => $division,
            'division_start' => $divStart,
            'division_end' => $divEnd,
            'pct' => $pct,
            'to_next' => $toNext,
            'next_label' => $nextLabel,
        ];
    }

    private function buildRankCoachMessage(array $quiz, int $percentage, array $rankUpdate, array $rankProgress): array
    {
        $diff = strtolower(trim((string) ($quiz['difficulty'] ?? 'beginner')));
        $delta = (int) ($rankUpdate['delta'] ?? 0);
        $league = (string) ($rankProgress['league'] ?? ($rankUpdate['league'] ?? 'Bronze'));
        $division = (string) ($rankProgress['division'] ?? ($rankUpdate['division'] ?? 'III'));
        $toNext = (int) ($rankProgress['to_next'] ?? 0);
        $nextLabel = (string) ($rankProgress['next_label'] ?? 'Next');

        $tone = 'neutral';
        if ($delta >= 15) {
            $tone = 'great';
        } elseif ($delta > 0) {
            $tone = 'good';
        } elseif ($delta <= -15) {
            $tone = 'warning';
        } elseif ($delta < 0) {
            $tone = 'soft';
        }

        $goalPct = 70;
        if ($league === 'Silver') {
            $goalPct = 75;
        } elseif ($league === 'Gold') {
            $goalPct = 80;
        } elseif ($league === 'Platinum') {
            $goalPct = 85;
        }

        if ($diff === 'advanced') {
            $goalPct = max(70, $goalPct - 5);
        } elseif ($diff === 'beginner') {
            $goalPct = min(90, $goalPct + 5);
        }

        $headline = "Objectif Rank : {$league} {$division}";
        $plan = [];
        if ($percentage < 50) {
            $plan[] = "Refais un quiz Débutant du même cours et vise {$goalPct}%";
            $plan[] = "Ensuite refais ce quiz (ou un équivalent) après 24h";
        } elseif ($percentage < $goalPct) {
            $plan[] = "Prochaine tentative : vise {$goalPct}% pour stabiliser ton rating";
            $plan[] = "Fais 1 quiz de consolidation + 1 quiz un peu plus dur";
        } else {
            $plan[] = "Continue sur cette difficulté et vise +10 points de rating";
            $plan[] = "Ajoute un quiz plus difficile pour accélérer la progression";
        }

        $meta = "Il te reste ~{$toNext} points pour {$nextLabel}.";
        if ($toNext <= 0) {
            $meta = "Tu es à la frontière de {$nextLabel} : refais un bon score pour passer !";
        }

        return [
            'tone' => $tone,
            'headline' => $headline,
            'meta' => $meta,
            'goal_pct' => $goalPct,
            'plan' => $plan,
        ];
    }

    private function updateStudentRatingAfterQuiz(StudentRankRepository $rankRepo, array $rankBefore, array $quiz, int $percentage): array
    {
        $oldRating = (int) ($rankBefore['rating'] ?? 1000);

        $diff = strtolower(trim((string) ($quiz['difficulty'] ?? 'beginner')));
        $quizStrength = 1000;
        if ($diff === 'intermediate') {
            $quizStrength = 1125;
        } elseif ($diff === 'advanced') {
            $quizStrength = 1250;
        }

        $result = max(0.0, min(1.0, $percentage / 100.0));
        $expected = 1.0 / (1.0 + pow(10.0, (($quizStrength - $oldRating) / 400.0)));
        $k = 28;
        if ($percentage >= 90) {
            $k = 34;
        } elseif ($percentage <= 40) {
            $k = 22;
        }

        $delta = (int) round($k * ($result - $expected) * 40);

        $newRating = max(0, $oldRating + $delta);

        [$league, $division] = $this->leagueForRating($newRating);
        $rankRepo->updateRating((int) ($rankBefore['user_id'] ?? 0), $newRating, $league, $division);

        return [
            'old_rating' => $oldRating,
            'new_rating' => $newRating,
            'delta' => $newRating - $oldRating,
            'league' => $league,
            'division' => $division,
            'expected' => $expected,
        ];
    }

    private function leagueForRating(int $rating): array
    {
        if ($rating >= 1500) {
            return ['Platinum', $this->divisionFromRating($rating, 1500, 200)];
        }
        if ($rating >= 1300) {
            return ['Gold', $this->divisionFromRating($rating, 1300, 200)];
        }
        if ($rating >= 1100) {
            return ['Silver', $this->divisionFromRating($rating, 1100, 200)];
        }
        return ['Bronze', $this->divisionFromRating($rating, 0, 1100)];
    }

    private function divisionFromRating(int $rating, int $start, int $span): string
    {
        $pos = max(0, $rating - $start);
        $pct = $span > 0 ? ($pos / $span) : 0.0;
        if ($pct >= 0.66) {
            return 'I';
        }
        if ($pct >= 0.33) {
            return 'II';
        }
        return 'III';
    }

    private function buildAfterQuizRecommendations(QuizRepository $quizRepo, QuizAttemptRepository $attemptRepo, array $quiz, int $percentage, int $userId): array
    {
        $courseId = (int) ($quiz['course_id'] ?? 0);
        $chapterId = (int) ($quiz['chapter_id'] ?? 0);
        $currentQuizId = (int) ($quiz['id'] ?? 0);
        $difficulty = (string) ($quiz['difficulty'] ?? 'beginner');

        if ($courseId <= 0) {
            return [];
        }

        $attemptedQuizIds = $attemptRepo->getAttemptedQuizIdsByUser($userId);
        $attemptedSet = [];
        foreach ($attemptedQuizIds as $aqid) {
            $attemptedSet[(int) $aqid] = true;
        }

        $chapterAverages = $attemptRepo->getChapterAveragesByUser($userId);
        $lastAttemptByChapter = $attemptRepo->getLastAttemptByChapterInCourse($userId, $courseId);

        $chapterSkill = [];
        foreach ($chapterAverages as $cid => $info) {
            $avg = (float) ($info['avg'] ?? 0);
            $attempts = (int) ($info['attempts'] ?? 0);
            $skill = 0.0;
            $skill += ($avg - 50.0) / 10.0;
            $skill += log(1 + max(0, $attempts)) * 0.35;
            $chapterSkill[(int) $cid] = $skill;
        }
        $weakChapterId = 0;
        $weakScore = -1.0;
        foreach ($chapterAverages as $cid => $info) {
            $avg = (float) ($info['avg'] ?? 0);
            $attempts = (int) ($info['attempts'] ?? 0);
            $needBoost = max(0.0, min(1.0, (70.0 - $avg) / 70.0));
            $confidence = 1.0 - exp(-0.45 * max(0, $attempts));
            $chapterNeedScore = $needBoost * $confidence;
            if ($chapterNeedScore > $weakScore) {
                $weakScore = $chapterNeedScore;
                $weakChapterId = (int) $cid;
            }
        }
        if ($weakChapterId <= 0) {
            $weakChapterId = $chapterId;
        }

        $exploreChapterId = 0;
        $maxDays = -1;
        foreach ($lastAttemptByChapter as $cid => $info) {
            $dt = isset($info['last_attempt_at']) ? (string) $info['last_attempt_at'] : null;
            $ts = $dt ? strtotime($dt) : false;
            $days = $ts ? (int) floor((time() - $ts) / 86400) : 9999;
            if ($cid === $chapterId || $cid === $weakChapterId) {
                continue;
            }
            if ($days > $maxDays) {
                $maxDays = $days;
                $exploreChapterId = (int) $cid;
            }
        }

        $candidates = $quizRepo->getApprovedCandidatesForCourse($courseId, $currentQuizId, 80);
        if (empty($candidates)) {
            return [];
        }

        $difficultyRank = static function (string $d): int {
            $d = strtolower(trim($d));
            if ($d === 'intermediate') { return 2; }
            if ($d === 'advanced') { return 3; }
            return 1;
        };

        $need = 'consolidation';
        if ($percentage < 50) {
            $need = 'remediation';
        } elseif ($percentage >= 80) {
            $need = 'progression';
        }

        $curRank = $difficultyRank($difficulty);

        $daysSince = static function (?string $dt): int {
            if ($dt === null || trim($dt) === '') {
                return 3650;
            }
            $ts = strtotime($dt);
            if ($ts === false) {
                return 3650;
            }
            $d = (int) floor((time() - $ts) / 86400);
            return max(0, $d);
        };

        $best = [];
        foreach ($candidates as $c) {
            $cid = (int) ($c['id'] ?? 0);
            if ($cid <= 0 || $cid === $currentQuizId) {
                continue;
            }
            $cChapterId = (int) ($c['chapter_id'] ?? 0);
            $cDiff = (string) ($c['difficulty'] ?? 'beginner');
            $cRank = $difficultyRank($cDiff);
            $recencyDays = $daysSince(isset($c['created_at']) ? (string) $c['created_at'] : null);

            $skill = $chapterSkill[$cChapterId] ?? 0.0;
            $quizDifficulty = $cRank === 1 ? 0.0 : ($cRank === 2 ? 1.0 : 2.0);
            $target = 0.0;
            if ($need === 'remediation') {
                $target = -0.3;
            } elseif ($need === 'progression') {
                $target = 0.8;
            }
            $skillFit = 1.0 - min(1.0, abs(($skill - $quizDifficulty) - $target) / 3.0);

            $chapterMatch = ($cChapterId === $chapterId && $chapterId > 0) ? 1.0 : 0.0;
            $weakChapterMatch = ($cChapterId === $weakChapterId && $weakChapterId > 0) ? 1.0 : 0.0;

            $difficultyFit = 0.0;
            if ($need === 'remediation') {
                $difficultyFit = $cRank <= $curRank ? 1.0 : 0.4;
            } elseif ($need === 'progression') {
                $difficultyFit = $cRank >= $curRank ? 1.0 : 0.6;
            } else {
                $difficultyFit = $cRank === $curRank ? 1.0 : 0.6;
            }

            $novelty = isset($attemptedSet[$cid]) ? 0.20 : 1.0;
            $recency = exp(-0.03 * $recencyDays);
            $weakBoost = $weakChapterMatch > 0 ? 1.0 : 0.0;
            $exploreBoost = ($exploreChapterId > 0 && $cChapterId === $exploreChapterId) ? 1.0 : 0.0;
            $score = 0.30 * $chapterMatch + 0.18 * $weakBoost + 0.22 * $difficultyFit + 0.12 * $novelty + 0.06 * $recency + 0.07 * $skillFit + 0.05 * $exploreBoost;

            $best[] = [
                'quiz_id' => $cid,
                'title' => (string) ($c['title'] ?? ''),
                'difficulty' => $cDiff,
                'chapter_title' => (string) ($c['chapter_title'] ?? ''),
                'chapter_id' => $cChapterId,
                '_recency_days' => $recencyDays,
                '_skill_fit' => $skillFit,
                '_score' => $score,
            ];
        }

        usort($best, static function ($a, $b) {
            return ($b['_score'] <=> $a['_score']);
        });

        $out = [];

        $pick = static function (array $list, callable $filter, array $attemptedSet): ?array {
            foreach ($list as $item) {
                $qid = (int) ($item['quiz_id'] ?? 0);
                if ($qid > 0 && isset($attemptedSet[$qid])) {
                    continue;
                }
                if ($filter($item)) {
                    return $item;
                }
            }
            foreach ($list as $item) {
                if ($filter($item)) {
                    return $item;
                }
            }
            return null;
        };

        $rem = $pick($best, static function ($x) use ($weakChapterId, $curRank, $difficultyRank) {
            return ($weakChapterId > 0 && (int) $x['chapter_id'] === $weakChapterId) && $difficultyRank((string) $x['difficulty']) <= $curRank;
        }, $attemptedSet);
        $con = $pick($best, static function ($x) use ($chapterId, $curRank, $difficultyRank) {
            return ($chapterId > 0 && (int) $x['chapter_id'] === $chapterId) && $difficultyRank((string) $x['difficulty']) === $curRank;
        }, $attemptedSet);

        $progressChapterId = $chapterId;
        if ($weakChapterId > 0 && $weakChapterId !== $chapterId) {
            $progressChapterId = $weakChapterId;
        }
        $pro = $pick($best, static function ($x) use ($progressChapterId, $curRank, $difficultyRank) {
            return ($progressChapterId > 0 && (int) $x['chapter_id'] === $progressChapterId) && $difficultyRank((string) $x['difficulty']) >= $curRank;
        }, $attemptedSet);

        $push = static function (?array $item, string $goal, string $reason) use (&$out) {
            if (!$item) {
                return;
            }
            $out[] = [
                'quiz_id' => (int) $item['quiz_id'],
                'title' => (string) $item['title'],
                'difficulty' => (string) $item['difficulty'],
                'chapter_title' => (string) $item['chapter_title'],
                'goal' => $goal,
                'reason' => $reason,
            ];
        };

        $push($rem, 'Remédiation', 'Ciblé sur tes lacunes (chapitre le plus faible) et évite les quiz déjà tentés si possible.');
        $push($con, 'Consolidation', 'Renforce le même chapitre avec une difficulté proche pour stabiliser le score.');
        $push($pro, 'Progression', 'Augmente le niveau (ou challenge) tout en gardant un parcours cohérent.');

        $seen = [];
        $dedup = [];
        foreach ($out as $r) {
            $qid = (int) ($r['quiz_id'] ?? 0);
            if ($qid <= 0 || isset($seen[$qid])) {
                continue;
            }
            $seen[$qid] = true;
            $dedup[] = $r;
        }
        $out = $dedup;

        if (count($out) < 3) {
            foreach ($best as $b) {
                if (count($out) >= 3) {
                    break;
                }
                $qid = (int) ($b['quiz_id'] ?? 0);
                if ($qid <= 0 || isset($seen[$qid])) {
                    continue;
                }
                $seen[$qid] = true;
                $out[] = [
                    'quiz_id' => $qid,
                    'title' => (string) $b['title'],
                    'difficulty' => (string) $b['difficulty'],
                    'chapter_title' => (string) $b['chapter_title'],
                    'goal' => 'Suggestion',
                    'reason' => 'Quiz recommandé selon ton résultat et le cours.'
                ];
            }
        }

        return array_values(array_slice($out, 0, 3));
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