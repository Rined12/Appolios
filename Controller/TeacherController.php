<?php
/**
 * APPOLIOS Teacher Controller
 * Handles teacher-specific functionality
 */

require_once __DIR__ . '/../Controller/BaseController.php';
require_once __DIR__ . '/../Model/User.php';
require_once __DIR__ . '/../Model/Course.php';
require_once __DIR__ . '/../Model/Evenement.php';
require_once __DIR__ . '/../Model/EvenementRessource.php';
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Model/QuizServer.php';

class TeacherController extends BaseController {

    /**
     * Route alias for /teacher/courses
     */
    public function courses() {
        $this->myCourses();
    }

    /**
     * Route alias for /teacher/course/{id}
     */
    public function course($id) {
        $this->viewCourse($id);
    }

    /**
     * Check if user is teacher
     */
    protected function isTeacher() {
        return $this->isLoggedIn() && $_SESSION['role'] === 'teacher';
    }

    /**
     * Middleware to check teacher access
     */
    protected function requireTeacher() {
        if (!$this->isTeacher()) {
            $this->setFlash('error', 'Access denied. Teachers only.');
            $this->redirect('login');
        }
    }

    /**
     * Teacher Dashboard
     */
    public function dashboard() {
        $this->requireTeacher();

        $userModel = $this->model('User');
        $courseModel = $this->model('Course');
        $evenementModel = $this->model('Evenement');

        // Get stats
        $myCourses = $courseModel->getCoursesByTeacher($_SESSION['user_id']);
        $stats = [
            'total_courses' => count($myCourses),
            'total_students' => $courseModel->countStudentsByTeacher($_SESSION['user_id']),
            'active_enrollments' => $courseModel->countActiveEnrollmentsByTeacher($_SESSION['user_id']),
            'total_evenements' => count($evenementModel->getByCreator($_SESSION['user_id']))
        ];

        $data = [
            'title' => 'Teacher Dashboard - APPOLIOS',
            'userName' => $_SESSION['user_name'],
            'courses' => $myCourses,
            'stats' => $stats
        ];

        $this->view('FrontOffice/teacher/dashboard', $data);
    }

    /**
     * List all my courses
     */
    public function myCourses() {
        $this->requireTeacher();

        $courseModel = $this->model('Course');
        $courses = $courseModel->getCoursesByTeacher($_SESSION['user_id']);

        $data = [
            'title' => 'My Courses - APPOLIOS',
            'courses' => $courses
        ];

        $this->view('FrontOffice/teacher/courses', $data);
    }

    /**
     * Show add course form
     */
    public function addCourse() {
        $this->requireTeacher();

        $data = [
            'title' => 'Add Course - APPOLIOS',
            'flash' => $this->getFlash()
        ];

        $this->view('FrontOffice/teacher/add_course', $data);
    }

    /**
     * Store new course
     */
    public function storeCourse() {
        $this->requireTeacher();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('teacher/add-course');
            return;
        }

        $title = $this->sanitize($_POST['title'] ?? '');
        $description = $this->sanitize($_POST['description'] ?? '');
        $video_url = $this->sanitize($_POST['video_url'] ?? '');

        // Validation
        $errors = [];
        if (empty($title)) $errors['title'] = 'Title is required';
        if (empty($description)) $errors['description'] = 'Description is required';

        if (!empty($errors)) {
            $this->setErrors($errors);
            $_SESSION['old'] = $_POST;
            $this->setFlash('error', 'Please fix the errors below');
            $this->redirect('teacher/add-course');
            return;
        }

        $courseModel = $this->model('Course');
        
        $result = $courseModel->create([
            'title' => $title,
            'description' => $description,
            'video_url' => $video_url,
            'created_by' => $_SESSION['user_id']
        ]);

        if ($result) {
            $this->setFlash('success', 'Course created successfully!');
            $this->redirect('teacher/courses');
        } else {
            $this->setFlash('error', 'Failed to create course');
            $this->redirect('teacher/add-course');
        }
    }

    /**
     * Show edit course form
     */
    public function editCourse($id) {
        $this->requireTeacher();

        $courseModel = $this->model('Course');
        $course = $courseModel->findById($id);

        // Check if course belongs to this teacher
        if (!$course || $course['created_by'] != $_SESSION['user_id']) {
            $this->setFlash('error', 'Course not found or access denied');
            $this->redirect('teacher/courses');
            return;
        }

        $data = [
            'title' => 'Edit Course - APPOLIOS',
            'course' => $course,
            'flash' => $this->getFlash()
        ];

        $this->view('FrontOffice/teacher/edit_course', $data);
    }

    /**
     * Update course
     */
    public function updateCourse($id) {
        $this->requireTeacher();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('teacher/edit-course/' . $id);
            return;
        }

        $courseModel = $this->model('Course');
        $course = $courseModel->findById($id);

        // Check if course belongs to this teacher
        if (!$course || $course['created_by'] != $_SESSION['user_id']) {
            $this->setFlash('error', 'Course not found or access denied');
            $this->redirect('teacher/courses');
            return;
        }

        $title = $this->sanitize($_POST['title'] ?? '');
        $description = $this->sanitize($_POST['description'] ?? '');
        $video_url = $this->sanitize($_POST['video_url'] ?? '');

        $errors = [];
        if (empty($title)) $errors['title'] = 'Title is required';
        if (empty($description)) $errors['description'] = 'Description is required';

        if (!empty($errors)) {
            $this->setErrors($errors);
            $_SESSION['old'] = $_POST;
            $this->setFlash('error', 'Please fix the errors below');
            $this->redirect('teacher/edit-course/' . $id);
            return;
        }

        $result = $courseModel->update($id, [
            'title' => $title,
            'description' => $description,
            'video_url' => $video_url
        ]);

        if ($result) {
            $this->setFlash('success', 'Course updated successfully!');
            $this->redirect('teacher/courses');
        } else {
            $this->setFlash('error', 'Failed to update course');
            $this->redirect('teacher/edit-course/' . $id);
        }
    }

    /**
     * Delete course
     */
    public function deleteCourse($id) {
        $this->requireTeacher();

        $courseModel = $this->model('Course');
        $course = $courseModel->findById($id);

        // Check if course belongs to this teacher
        if (!$course || $course['created_by'] != $_SESSION['user_id']) {
            $this->setFlash('error', 'Course not found or access denied');
            $this->redirect('teacher/courses');
            return;
        }

        $result = $courseModel->delete($id);

        if ($result) {
            $this->setFlash('success', 'Course deleted successfully!');
        } else {
            $this->setFlash('error', 'Failed to delete course');
        }

        $this->redirect('teacher/courses');
    }

    /**
     * View course with enrolled students
     */
    public function viewCourse($id) {
        $this->requireTeacher();

        $courseModel = $this->model('Course');
        $course = $courseModel->findById($id);

        // Check if course belongs to this teacher
        if (!$course || $course['created_by'] != $_SESSION['user_id']) {
            $this->setFlash('error', 'Course not found or access denied');
            $this->redirect('teacher/courses');
            return;
        }

        // Get enrolled students
        $students = $courseModel->getEnrolledStudents($id);

        $data = [
            'title' => htmlspecialchars($course['title']) . ' - APPOLIOS',
            'course' => $course,
            'students' => $students
        ];

        $this->view('FrontOffice/teacher/course_detail', $data);
    }

    /**
     * Teacher profile
     */
    public function profile() {
        $this->requireTeacher();

        $userModel = $this->model('User');
        $user = $userModel->findById($_SESSION['user_id']);

        $data = [
            'title' => 'My Profile - APPOLIOS',
            'user' => $user
        ];

        $this->view('FrontOffice/teacher/profile', $data);
    }

    /* ---------- Chapitres, quiz, banque de questions (enseignant) ---------- */

    public function chapitres() {
        $this->requireTeacher();

        $chapterModel = $this->model('Chapter');
        $this->view('FrontOffice/teacher/chapitres', [
            'title' => 'Chapitres - ' . APP_NAME,
            'chapters' => $chapterModel->getAllForTeacher((int) $_SESSION['user_id']),
            'flash' => $this->getFlash(),
        ]);
    }

    public function chapitresAdd() {
        $this->requireTeacher();
        $courseModel = $this->model('Course');
        $this->view('FrontOffice/teacher/chapter_form', [
            'title' => 'Nouveau chapitre - ' . APP_NAME,
            'course' => null,
            'chapter' => null,
            'allCourses' => $courseModel->getCoursesByTeacher((int) $_SESSION['user_id']),
            'flash' => $this->getFlash(),
        ]);
    }

    public function chapitresStore() {
        $this->requireTeacher();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('teacher/chapitres-add');
            return;
        }

        $courseId = (int) ($_POST['course_id'] ?? 0);
        $course = $this->teacherCourseOrFail($courseId);
        if (!$course) {
            return;
        }

        $title = $this->sanitize($_POST['title'] ?? '');
        $content = $this->sanitize($_POST['content'] ?? '');
        $sort = (int) ($_POST['sort_order'] ?? 0);
        if ($title === '') {
            $this->setFlash('error', 'Le titre est obligatoire.');
            $this->redirect('teacher/chapitres-add');
            return;
        }

        $chapterModel = $this->model('Chapter');
        $chapterModel->create([
            'course_id' => $courseId,
            'title' => $title,
            'content' => $content,
            'sort_order' => $sort,
        ]);
        $this->setFlash('success', 'Chapitre créé.');
        $this->redirect('teacher/chapitres');
    }

    public function editChapter($id) {
        $this->requireTeacher();
        $chapterModel = $this->model('Chapter');
        $row = $chapterModel->findWithCourse((int) $id);
        if (!$row || (int) $row['course_owner_id'] !== (int) $_SESSION['user_id']) {
            $this->setFlash('error', 'Chapitre introuvable.');
            $this->redirect('teacher/chapitres');
            return;
        }

        $courseModel = $this->model('Course');
        $course = $courseModel->findById((int) $row['course_id']);

        $this->view('FrontOffice/teacher/chapter_form', [
            'title' => 'Modifier le chapitre - ' . APP_NAME,
            'course' => $course,
            'chapter' => $row,
            'flash' => $this->getFlash(),
        ]);
    }

    public function updateChapter($id) {
        $this->requireTeacher();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('teacher/chapitres');
            return;
        }

        $chapterModel = $this->model('Chapter');
        $row = $chapterModel->findWithCourse((int) $id);
        if (!$row || (int) $row['course_owner_id'] !== (int) $_SESSION['user_id']) {
            $this->setFlash('error', 'Chapitre introuvable.');
            $this->redirect('teacher/chapitres');
            return;
        }

        $title = $this->sanitize($_POST['title'] ?? '');
        $content = $this->sanitize($_POST['content'] ?? '');
        $sort = (int) ($_POST['sort_order'] ?? 0);
        if ($title === '') {
            $this->setFlash('error', 'Le titre est obligatoire.');
            $this->redirect('teacher/edit-chapter/' . (int) $id);
            return;
        }

        $chapterModel->update((int) $id, ['title' => $title, 'content' => $content, 'sort_order' => $sort]);
        $this->setFlash('success', 'Chapitre mis à jour.');
        $this->redirect('teacher/chapitres');
    }

    public function deleteChapter($id) {
        $this->requireTeacher();
        $chapterModel = $this->model('Chapter');
        $row = $chapterModel->findWithCourse((int) $id);
        if (!$row || (int) $row['course_owner_id'] !== (int) $_SESSION['user_id']) {
            $this->setFlash('error', 'Chapitre introuvable.');
            $this->redirect('teacher/chapitres');
            return;
        }
        $chapterModel->delete((int) $id);
        $this->setFlash('success', 'Chapitre supprimé.');
        $this->redirect('teacher/chapitres');
    }

    public function quiz() {
        $this->requireTeacher();

        $teacherId = (int) $_SESSION['user_id'];

        $quizUsage = $this->getUsageStatsMapForTeacher($teacherId);

        $quizzes = $this->getAllQuizzesForTeacher($teacherId);

        $quizTopStats = [
            'quizzes_count' => 0,
            'attempts_total' => 0,
            'avg_percentage' => 0,
            'last_attempt_at' => null,
        ];
        $wSum = 0.0;
        $lastAttempt = null;
        foreach ($quizUsage as $qid => $u) {
            $att = (int) ($u['attempts'] ?? 0);
            $avg = (float) ($u['avg'] ?? 0);
            $quizTopStats['attempts_total'] += $att;
            $wSum += ($avg * $att);

            $la = $u['last_attempt_at'] ?? null;
            if ($la) {
                $ts = strtotime((string) $la);
                if ($ts !== false && ($lastAttempt === null || $ts > $lastAttempt)) {
                    $lastAttempt = $ts;
                    $quizTopStats['last_attempt_at'] = (string) $la;
                }
            }
        }

        $quizTopStats['quizzes_count'] = is_array($quizzes) ? count($quizzes) : 0;
        $quizTopStats['avg_percentage'] = $quizTopStats['attempts_total'] > 0
            ? round($wSum / (float) $quizTopStats['attempts_total'], 1)
            : 0;

        $this->view('FrontOffice/teacher/quizzes', [
            'title' => 'Quiz - ' . APP_NAME,
            'quizzes' => $quizzes,
            'quizUsage' => $quizUsage,
            'quizTopStats' => $quizTopStats,
            'flash' => $this->getFlash(),
        ]);
    }

    public function quizStats() {
        $this->requireTeacher();

        $teacherId = (int) $_SESSION['user_id'];

        $rows = $this->getQuizStatsForTeacher($teacherId);
        $series = $this->getQuizAttemptSeriesMapForTeacher($teacherId, 120);

        $diffDist = [
            'beginner' => 0,
            'intermediate' => 0,
            'advanced' => 0,
        ];
        $statusDist = [
            'approved' => 0,
            'pending' => 0,
            'rejected' => 0,
        ];

        $totalQuizzes = is_array($rows) ? count($rows) : 0;
        $totalAttempts = 0;
        $sumAvg = 0.0;
        $avgCount = 0;
        $approved = 0;

        foreach ($rows as $r) {
            $att = (int) ($r['attempts_count'] ?? 0);
            $totalAttempts += $att;
            if ($att > 0) {
                $sumAvg += (float) ($r['avg_percentage'] ?? 0);
                $avgCount++;
            }
            if ((string) ($r['status'] ?? '') === 'approved') {
                $approved++;
            }

            $d = (string) ($r['difficulty'] ?? 'beginner');
            if (!isset($diffDist[$d])) {
                $diffDist[$d] = 0;
            }
            $diffDist[$d]++;

            $st = (string) ($r['status'] ?? 'approved');
            if (!isset($statusDist[$st])) {
                $statusDist[$st] = 0;
            }
            $statusDist[$st]++;
        }

        $overallAvg = $avgCount > 0 ? round($sumAvg / $avgCount, 1) : 0.0;

        $playedQuizzes = 0;
        foreach ($rows as $r) {
            if ((int) ($r['attempts_count'] ?? 0) > 0) {
                $playedQuizzes++;
            }
        }
        $coveragePct = $totalQuizzes > 0 ? min(100, max(0, ($playedQuizzes / $totalQuizzes) * 100)) : 0;
        $attemptsPerQuiz = $totalQuizzes > 0 ? ($totalAttempts / $totalQuizzes) : 0;
        $engagementPct = 100 * (1 - exp(-($attemptsPerQuiz / 3)));
        if ($engagementPct < 0) $engagementPct = 0;
        if ($engagementPct > 100) $engagementPct = 100;

        $trendRows = $this->getQuizAttemptsTrendForTeacher($teacherId, 21);
        $trendMap = [];
        foreach ($trendRows as $qid => $list) {
            foreach (($list ?? []) as $p) {
                $day = (string) ($p['day'] ?? '');
                if ($day === '') {
                    continue;
                }
                if (!isset($trendMap[$day])) {
                    $trendMap[$day] = ['day' => $day, 'count' => 0, 'avg_sum' => 0.0, 'avg_n' => 0];
                }
                $trendMap[$day]['count'] += (int) ($p['count'] ?? 0);
                $trendMap[$day]['avg_sum'] += (float) ($p['avg'] ?? 0);
                $trendMap[$day]['avg_n'] += 1;
            }
        }
        ksort($trendMap);
        $trend = [];
        foreach ($trendMap as $day => $info) {
            $n = (int) ($info['avg_n'] ?? 0);
            $trend[] = [
                'day' => (string) ($info['day'] ?? ''),
                'count' => (int) ($info['count'] ?? 0),
                'avg' => $n > 0 ? round(((float) ($info['avg_sum'] ?? 0)) / $n, 1) : 0.0,
            ];
        }

        $this->view('FrontOffice/teacher/quiz_stats', [
            'title' => 'Statistiques quiz - ' . APP_NAME,
            'rows' => $rows,
            'series' => $series,
            'charts' => [
                'difficulty' => $diffDist,
                'status' => $statusDist,
                'trend' => $trend,
            ],
            'kpis' => [
                'total_quizzes' => $totalQuizzes,
                'total_attempts' => $totalAttempts,
                'overall_avg' => $overallAvg,
                'approved_quizzes' => $approved,
                'coverage_pct' => round((float) $coveragePct, 1),
                'engagement_pct' => round((float) $engagementPct, 1),
                'attempts_per_quiz' => round((float) $attemptsPerQuiz, 2),
            ],
            'flash' => $this->getFlash(),
        ]);
    }

    public function addQuiz() {
        $this->requireTeacher();

        $chapters = $this->getChaptersForTeacher((int) $_SESSION['user_id']);

        $this->view('FrontOffice/teacher/quiz_form', [
            'title' => 'Nouveau quiz - ' . APP_NAME,
            'quiz' => null,
            'chapters' => $chapters,
            'questionBank' => $this->getQuestionBankForTeacher((int) $_SESSION['user_id']),
            'flash' => $this->getFlash(),
        ]);
    }

    public function storeQuiz() {
        $this->requireTeacher();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('teacher/add-quiz');
            return;
        }

        $chapterId = (int) ($_POST['chapter_id'] ?? 0);
        if (!$this->teacherChapterOwnedByUser((int) $chapterId, (int) $_SESSION['user_id'])) {
            $this->setFlash('error', 'Chapitre invalide.');
            $this->redirect('teacher/add-quiz');
            return;
        }

        $meta = $this->validateQuizMetaFromPost($_POST);
        if (!empty($meta['errors'])) {
            $this->setFlash('error', $meta['errors'][0]);
            $this->redirect('teacher/add-quiz');
            return;
        }

        $bankIds = isset($_POST['bank_question_ids']) && is_array($_POST['bank_question_ids']) ? $_POST['bank_question_ids'] : [];
        $autoCount = (int) ($_POST['auto_bank_count'] ?? 0);
        $autoDifficulty = isset($_POST['auto_bank_difficulty']) ? strtolower(trim((string) $_POST['auto_bank_difficulty'])) : '';
        $autoTags = isset($_POST['auto_bank_tags']) ? trim((string) $_POST['auto_bank_tags']) : '';
        if ($autoCount > 0) {
            $picked = $this->pickQuestionBankIdsForBlueprint((int) $_SESSION['user_id'], $autoCount, $autoDifficulty, $autoTags, $bankIds);
            if (empty($picked)) {
                $this->setFlash('error', 'Aucune question disponible dans la banque pour ces critères.');
                $this->redirect('teacher/add-quiz');
                return;
            }
            $bankIds = array_values(array_unique(array_merge($bankIds, $picked)));
        }
        $questions = $this->appendBankQuestionsToQuiz(
            $this->normalizeQuizQuestionsFromPost($_POST),
            $bankIds,
            (int) $_SESSION['user_id']
        );

        if (count($questions) < 1) {
            $this->setFlash('error', 'Ajoutez au moins une question (saisie manuelle ou depuis la banque).');
            $this->redirect('teacher/add-quiz');
            return;
        }

        $qErr = $this->validateNormalizedQuizQuestions($questions);
        if (!empty($qErr)) {
            $this->setFlash('error', $qErr[0]);
            $this->redirect('teacher/add-quiz');
            return;
        }

        $createdId = $this->createTeacherQuiz(
            $chapterId,
            (int) $_SESSION['user_id'],
            $this->sanitize($meta['title']),
            (string) $meta['difficulty'],
            $meta['tags'] !== null ? $this->sanitize($meta['tags']) : null,
            $meta['time_limit_sec'],
            $questions,
            'pending'
        );

        if ($createdId === false) {
            $this->setFlash('error', 'Impossible d’enregistrer le quiz (vérifiez la base de données).');
            $this->redirect('teacher/add-quiz');
            return;
        }

        $this->setFlash('success', 'Quiz enregistré.');
        $this->redirect('teacher/quiz');
    }

    public function editQuiz($id) {
        $this->requireTeacher();

        $quiz = $this->findQuizWithChapterCourse((int) $id);
        if (!$quiz || (int) ($quiz['course_owner_id'] ?? 0) !== (int) $_SESSION['user_id']) {
            $this->setFlash('error', 'Quiz introuvable.');
            $this->redirect('teacher/quiz');
            return;
        }

        $chapters = $this->getChaptersForTeacher((int) $_SESSION['user_id']);

        $this->view('FrontOffice/teacher/quiz_form', [
            'title' => 'Modifier le quiz - ' . APP_NAME,
            'quiz' => $quiz,
            'chapters' => $chapters,
            'questionBank' => $this->getQuestionBankForTeacher((int) $_SESSION['user_id']),
            'flash' => $this->getFlash(),
        ]);
    }

    public function updateQuiz($id) {
        $this->requireTeacher();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('teacher/quiz');
            return;
        }

        $existing = $this->findQuizWithChapterCourse((int) $id);
        if (!$existing || (int) ($existing['course_owner_id'] ?? 0) !== (int) $_SESSION['user_id']) {
            $this->setFlash('error', 'Quiz introuvable.');
            $this->redirect('teacher/quiz');
            return;
        }

        $chapterId = (int) ($_POST['chapter_id'] ?? 0);
        if (!$this->teacherChapterOwnedByUser((int) $chapterId, (int) $_SESSION['user_id'])) {
            $this->setFlash('error', 'Chapitre invalide.');
            $this->redirect('teacher/edit-quiz/' . (int) $id);
            return;
        }

        $meta = $this->validateQuizMetaFromPost($_POST);
        if (!empty($meta['errors'])) {
            $this->setFlash('error', $meta['errors'][0]);
            $this->redirect('teacher/edit-quiz/' . (int) $id);
            return;
        }

        $bankIds = isset($_POST['bank_question_ids']) && is_array($_POST['bank_question_ids']) ? $_POST['bank_question_ids'] : [];
        $questions = $this->appendBankQuestionsToQuiz(
            $this->normalizeQuizQuestionsFromPost($_POST),
            $bankIds,
            (int) $_SESSION['user_id']
        );

        if (count($questions) < 1) {
            $this->setFlash('error', 'Au moins une question requise (saisie ou banque).');
            $this->redirect('teacher/edit-quiz/' . (int) $id);
            return;
        }

        $qErr = $this->validateNormalizedQuizQuestions($questions);
        if (!empty($qErr)) {
            $this->setFlash('error', $qErr[0]);
            $this->redirect('teacher/edit-quiz/' . (int) $id);
            return;
        }

        $updated = $this->updateTeacherQuiz(
            (int) $id,
            (int) $_SESSION['user_id'],
            $chapterId,
            $this->sanitize($meta['title']),
            (string) $meta['difficulty'],
            $meta['tags'] !== null ? $this->sanitize($meta['tags']) : null,
            $meta['time_limit_sec'],
            $questions
        );

        if ($updated === false) {
            $this->setFlash('error', 'Impossible de mettre à jour le quiz (vérifiez la base de données).');
            $this->redirect('teacher/edit-quiz/' . (int) $id);
            return;
        }

        $this->setFlash('success', 'Quiz mis à jour.');
        $this->redirect('teacher/quiz');
    }

    public function deleteQuiz($id) {
        $this->requireTeacher();
        $existing = $this->findQuizWithChapterCourse((int) $id);
        if (!$existing || (int) ($existing['course_owner_id'] ?? 0) !== (int) $_SESSION['user_id']) {
            $this->setFlash('error', 'Quiz introuvable.');
            $this->redirect('teacher/quiz');
            return;
        }
        $this->deleteTeacherQuiz((int) $id, (int) $_SESSION['user_id']);
        $this->setFlash('success', 'Quiz supprimé.');
        $this->redirect('teacher/quiz');
    }

    public function duplicateQuiz($id) {
        $this->requireTeacher();
        $existing = $this->findQuizWithChapterCourse((int) $id);
        if (!$existing || (int) ($existing['course_owner_id'] ?? 0) !== (int) $_SESSION['user_id']) {
            $this->setFlash('error', 'Quiz introuvable.');
            $this->redirect('teacher/quiz');
            return;
        }
        $newId = $this->duplicateQuizForTeacher((int) $id, (int) $_SESSION['user_id']);
        if ($newId === false) {
            $this->setFlash('error', 'Impossible de dupliquer le quiz.');
            $this->redirect('teacher/quiz');
            return;
        }
        $this->setFlash('success', 'Quiz dupliqué.');
        $this->redirect('teacher/edit-quiz/' . (int) $newId);
    }

    public function questions() {
        $this->requireTeacher();
        $teacherId = (int) $_SESSION['user_id'];

        $collections = $this->getQuestionCollectionsForTeacher($teacherId);
        $selectedCollectionId = isset($_GET['collection_id']) ? (int) $_GET['collection_id'] : 0;
        $selectedIds = $selectedCollectionId > 0 ? $this->getQuestionIdsForCollection($selectedCollectionId, $teacherId) : [];
        $selectedMap = [];
        foreach ($selectedIds as $id) {
            $selectedMap[(int) $id] = true;
        }

        $questions = $this->getQuestionBankForTeacher($teacherId);
        $questionUsage = $this->getQuestionBankUsageStatsMapForTeacher($teacherId);

        $qbTopStats = [
            'questions_total' => count($questions),
            'used_questions' => 0,
            'attempts_total' => 0,
            'avg_percentage' => 0,
        ];
        $wSum = 0.0;
        foreach ($questionUsage as $id => $u) {
            $qz = (int) ($u['quizzes'] ?? 0);
            $att = (int) ($u['attempts'] ?? 0);
            $avg = (float) ($u['avg'] ?? 0);
            if ($qz > 0) {
                $qbTopStats['used_questions']++;
            }
            $qbTopStats['attempts_total'] += $att;
            $wSum += ($avg * $att);
        }
        if ($qbTopStats['attempts_total'] > 0) {
            $qbTopStats['avg_percentage'] = round($wSum / (float) $qbTopStats['attempts_total'], 1);
        }

        $diffDist = [
            'beginner' => 0,
            'intermediate' => 0,
            'advanced' => 0,
        ];
        foreach ($questions as $q) {
            $d = (string) ($q['difficulty'] ?? 'beginner');
            if (!isset($diffDist[$d])) {
                $diffDist[$d] = 0;
            }
            $diffDist[$d]++;
        }

        $qaMap = [];
        foreach ($questions as $q) {
            $qid = (int) ($q['id'] ?? 0);
            if ($qid <= 0) {
                continue;
            }
            $u = $questionUsage[$qid] ?? null;
            $att = is_array($u) ? (int) ($u['attempts'] ?? 0) : 0;
            $avg = is_array($u) ? (float) ($u['avg'] ?? 0) : 0.0;
            $qz = is_array($u) ? (int) ($u['quizzes'] ?? 0) : 0;
            $qaMap[$qid] = $this->questionQaFromUsage($qz, $att, $avg);
        }

        $this->view('FrontOffice/teacher/questions_bank', [
            'title' => 'Banque de questions - ' . APP_NAME,
            'questions' => $questions,
            'questionUsage' => $questionUsage,
            'qbTopStats' => $qbTopStats,
            'questionQa' => $qaMap,
            'charts' => [
                'difficulty' => $diffDist,
            ],
            'collections' => $collections,
            'selectedCollectionId' => $selectedCollectionId,
            'collectionSelectedMap' => $selectedMap,
            'flash' => $this->getFlash(),
        ]);
    }

    private function questionQaFromUsage(int $quizzesCount, int $attempts, float $avgPercentage): array
    {
        if ($quizzesCount <= 0 || $attempts <= 0) {
            return ['label' => 'Non utilisée', 'badge' => 'pro-badge', 'score' => null];
        }
        if ($attempts < 10) {
            return ['label' => 'Données insuff.', 'badge' => 'pro-badge', 'score' => null];
        }

        $avg = max(0.0, min(100.0, $avgPercentage));
        if ($avg >= 85.0) {
            return ['label' => 'Trop facile', 'badge' => 'pro-badge pro-badge--beginner', 'score' => (int) round(100 - (($avg - 85.0) * 2.0))];
        }
        if ($avg <= 35.0) {
            return ['label' => 'Trop difficile', 'badge' => 'pro-badge pro-badge--advanced', 'score' => (int) round(100 - ((35.0 - $avg) * 2.0))];
        }

        $target = 60.0;
        $difficultyScore = 100.0 - (abs($avg - $target) * 1.5);
        if ($difficultyScore < 0) $difficultyScore = 0;
        if ($difficultyScore > 100) $difficultyScore = 100;

        $attemptsNorm = log((float) ($attempts + 1), 10) / log(51.0, 10);
        if ($attemptsNorm < 0) $attemptsNorm = 0;
        if ($attemptsNorm > 1) $attemptsNorm = 1;
        $reliability = $attemptsNorm * 100.0;

        $score = (int) round(($difficultyScore * 0.7) + ($reliability * 0.3));
        if ($score < 0) $score = 0;
        if ($score > 100) $score = 100;

        return ['label' => 'OK', 'badge' => 'pro-badge pro-badge--intermediate', 'score' => $score];
    }

    private function pickQuestionBankIdsForBlueprint(int $teacherId, int $count, string $difficulty, string $tags, array $excludeIds = []): array
    {
        $count = max(1, min(30, $count));

        $difficulty = strtolower(trim($difficulty));
        if (!in_array($difficulty, ['', 'beginner', 'intermediate', 'advanced'], true)) {
            $difficulty = '';
        }

        $excludeIds = array_map('intval', $excludeIds);
        $excludeIds = array_values(array_filter($excludeIds, static fn($v) => $v > 0));

        $tagList = preg_split('/[\s,;]+/', strtolower(trim($tags)), -1, PREG_SPLIT_NO_EMPTY);
        $tagList = is_array($tagList) ? array_values(array_unique($tagList)) : [];

        $db = $this->db();
        $sql = "SELECT qb.id
                FROM question_bank qb
                WHERE qb.created_by = ?";
        $params = [(int) $teacherId];

        if ($difficulty !== '') {
            $sql .= " AND qb.difficulty = ?";
            $params[] = $difficulty;
        }

        foreach ($tagList as $tg) {
            $sql .= " AND LOWER(COALESCE(qb.tags,'')) LIKE ?";
            $params[] = '%' . $tg . '%';
        }

        if (!empty($excludeIds)) {
            $in = implode(',', array_fill(0, count($excludeIds), '?'));
            $sql .= " AND qb.id NOT IN ($in)";
            foreach ($excludeIds as $x) {
                $params[] = (int) $x;
            }
        }

        $sql .= " ORDER BY RAND() LIMIT " . (int) $count;
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();
        $out = [];
        foreach ($rows as $r) {
            $id = (int) ($r['id'] ?? 0);
            if ($id > 0) {
                $out[] = $id;
            }
        }
        return $out;
    }

    public function createQuestionCollection() {
        $this->requireTeacher();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('teacher/questions');
            return;
        }
        $title = $this->sanitize($_POST['title'] ?? '');
        if ($title === '') {
            $this->setFlash('error', 'Titre requis.');
            $this->redirect('teacher/questions');
            return;
        }
        $id = $this->createQuestionCollectionRow((int) $_SESSION['user_id'], $title);
        if ($id === false) {
            $this->setFlash('error', 'Impossible de créer le pack.');
        } else {
            $this->setFlash('success', 'Pack créé.');
        }
        $this->redirect('teacher/questions');
    }

    public function deleteQuestionCollection($id) {
        $this->requireTeacher();
        $ok = $this->deleteQuestionCollectionOwned((int) $id, (int) $_SESSION['user_id']);
        $this->setFlash($ok ? 'success' : 'error', $ok ? 'Pack supprimé.' : 'Impossible de supprimer.');
        $this->redirect('teacher/questions');
    }

    public function addQuestionToCollection($collectionId, $questionId) {
        $this->requireTeacher();
        $cid = (int) $collectionId;
        $qid = (int) $questionId;
        $ok = $this->addQuestionToCollectionOwned($cid, $qid, (int) $_SESSION['user_id']);
        if ($ok) {
            $this->setFlash('success', 'Question ajoutée au pack.');
        } else {
            $this->setFlash('error', 'Impossible d\'ajouter la question.');
        }
        $this->redirect('teacher/questions?collection_id=' . $cid);
    }

    public function removeQuestionFromCollection($collectionId, $questionId) {
        $this->requireTeacher();
        $cid = (int) $collectionId;
        $qid = (int) $questionId;
        $ok = $this->removeQuestionFromCollectionOwned($cid, $qid, (int) $_SESSION['user_id']);
        if ($ok) {
            $this->setFlash('success', 'Question retirée du pack.');
        } else {
            $this->setFlash('error', 'Impossible de retirer la question.');
        }
        $this->redirect('teacher/questions?collection_id=' . $cid);
    }

    private function getChaptersForTeacher(int $teacherId): array
    {
        $db = $this->db();
        $sql = "SELECT ch.id, ch.course_id, ch.title, c.title AS course_title
                FROM chapters ch
                JOIN courses c ON c.id = ch.course_id
                WHERE c.created_by = ?
                ORDER BY c.title ASC, ch.title ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute([(int) $teacherId]);
        return $stmt->fetchAll();
    }

    private function teacherChapterOwnedByUser(int $chapterId, int $teacherId): bool
    {
        if ($chapterId <= 0) {
            return false;
        }
        $db = $this->db();
        $sql = "SELECT ch.id
                FROM chapters ch
                JOIN courses c ON c.id = ch.course_id
                WHERE ch.id = ? AND c.created_by = ?
                LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([(int) $chapterId, (int) $teacherId]);
        return (bool) $stmt->fetch();
    }

    public function addQuestion() {
        $this->requireTeacher();
        $this->view('FrontOffice/teacher/question_form', [
            'title' => 'Nouvelle question - ' . APP_NAME,
            'question' => null,
            'flash' => $this->getFlash(),
        ]);
    }

    public function storeQuestion() {
        $this->requireTeacher();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('teacher/add-question');
            return;
        }

        $title = $this->sanitize($_POST['title'] ?? '');
        $questionText = $this->sanitize($_POST['question_text'] ?? '');
        $optionsRaw = $_POST['options'] ?? [];
        $opts = is_array($optionsRaw) ? array_values(array_filter(array_map([$this, 'sanitize'], $optionsRaw))) : [];
        $correct = (int) ($_POST['correct_answer'] ?? 0);
        $tags = $this->sanitize($_POST['tags'] ?? '');
        $difficulty = $this->normalizeDifficulty($_POST['difficulty'] ?? 'beginner');

        $errs = $this->validateQuestionBankFields($title, $questionText, $opts, $correct, $tags);
        if (!empty($errs)) {
            $this->setFlash('error', $errs[0]);
            $this->redirect('teacher/add-question');
            return;
        }

        $this->createQuestionBankQuestion(
            (int) $_SESSION['user_id'],
            $title !== '' ? $title : null,
            $questionText,
            $opts,
            $correct,
            $tags !== '' ? $tags : null,
            $difficulty
        );

        $this->setFlash('success', 'Question enregistrée.');
        $this->redirect('teacher/questions');
    }

    public function editQuestion($id) {
        $this->requireTeacher();
        $row = $this->findQuestionBankOwned((int) $id, (int) $_SESSION['user_id']);
        if (!$row) {
            $this->setFlash('error', 'Question introuvable.');
            $this->redirect('teacher/questions');
            return;
        }
        $this->view('FrontOffice/teacher/question_form', [
            'title' => 'Modifier la question - ' . APP_NAME,
            'question' => $row,
            'flash' => $this->getFlash(),
        ]);
    }

    public function updateQuestion($id) {
        $this->requireTeacher();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('teacher/questions');
            return;
        }

        $existing = $this->findQuestionBankOwned((int) $id, (int) $_SESSION['user_id']);
        if (!$existing) {
            $this->setFlash('error', 'Question introuvable.');
            $this->redirect('teacher/questions');
            return;
        }

        $title = $this->sanitize($_POST['title'] ?? '');
        $questionText = $this->sanitize($_POST['question_text'] ?? '');
        $optionsRaw = $_POST['options'] ?? [];
        $opts = is_array($optionsRaw) ? array_values(array_filter(array_map([$this, 'sanitize'], $optionsRaw))) : [];
        $correct = (int) ($_POST['correct_answer'] ?? 0);
        $tags = $this->sanitize($_POST['tags'] ?? '');
        $difficulty = $this->normalizeDifficulty($_POST['difficulty'] ?? 'beginner');

        $errs = $this->validateQuestionBankFields($title, $questionText, $opts, $correct, $tags);
        if (!empty($errs)) {
            $this->setFlash('error', $errs[0]);
            $this->redirect('teacher/edit-question/' . (int) $id);
            return;
        }

        $this->updateQuestionBankOwned(
            (int) $id,
            (int) $_SESSION['user_id'],
            $title !== '' ? $title : null,
            $questionText,
            $opts,
            $correct,
            $tags !== '' ? $tags : null,
            $difficulty
        );

        $this->setFlash('success', 'Question mise à jour.');
        $this->redirect('teacher/questions');
    }

    public function deleteQuestion($id) {
        $this->requireTeacher();
        $existing = $this->findQuestionBankOwned((int) $id, (int) $_SESSION['user_id']);
        if (!$existing) {
            $this->setFlash('error', 'Question introuvable.');
            $this->redirect('teacher/questions');
            return;
        }
        $this->deleteQuestionBankOwned((int) $id, (int) $_SESSION['user_id']);
        $this->setFlash('success', 'Question supprimée.');
        $this->redirect('teacher/questions');
    }

    private function teacherCourseOrFail($courseId)
    {
        $courseModel = $this->model('Course');
        $course = $courseModel->findById((int) $courseId);
        if (!$course || (int) $course['created_by'] !== (int) $_SESSION['user_id']) {
            $this->setFlash('error', 'Cours introuvable ou accès refusé.');
            $this->redirect('teacher/courses');
            return null;
        }
        return $course;
    }

    private function normalizeDifficulty($difficulty): string
    {
        $d = strtolower(trim((string) $difficulty));
        if ($d === 'beginner' || $d === 'intermediate' || $d === 'advanced') {
            return $d;
        }
        return 'beginner';
    }

    private function validateQuizMetaFromPost(array $post): array
    {
        $errors = [];

        $title = $this->sanitize($post['title'] ?? '');
        if ($title === '' || mb_strlen($title) < 3) {
            $errors[] = 'Titre invalide.';
        }

        $difficulty = $this->normalizeDifficulty($post['difficulty'] ?? 'beginner');

        $tagsRaw = $post['tags'] ?? null;
        $tags = null;
        if ($tagsRaw !== null && $tagsRaw !== '') {
            $tagsSan = $this->sanitize((string) $tagsRaw);
            $tags = $tagsSan !== '' ? $tagsSan : null;
        }

        $timeLimitSecRaw = $post['time_limit_sec'] ?? null;
        $timeLimitSec = null;
        if ($timeLimitSecRaw !== null && $timeLimitSecRaw !== '') {
            $timeLimitSec = (int) $timeLimitSecRaw;
            if ($timeLimitSec < 0) {
                $errors[] = 'Temps limite invalide.';
            }
        }

        return [
            'errors' => $errors,
            'title' => $title,
            'difficulty' => $difficulty,
            'tags' => $tags,
            'time_limit_sec' => $timeLimitSec,
        ];
    }

    private function validateNormalizedQuizQuestions(array $questions): array
    {
        $errors = [];

        if (count($questions) < 1) {
            $errors[] = 'Au moins une question requise.';
            return $errors;
        }

        foreach ($questions as $idx => $q) {
            if (!is_array($q)) {
                $errors[] = 'Format de question invalide.';
                return $errors;
            }

            $text = '';
            if (isset($q['question_text'])) {
                $text = trim((string) $q['question_text']);
            } elseif (isset($q['question'])) {
                $text = trim((string) $q['question']);
            }
            if ($text === '') {
                $errors[] = 'Question vide.';
                return $errors;
            }

            $opts = $q['options'] ?? [];
            if (!is_array($opts) || count($opts) < 2) {
                $errors[] = 'Options invalides.';
                return $errors;
            }

            $correct = -1;
            if (isset($q['correct_answer'])) {
                $correct = (int) $q['correct_answer'];
            } elseif (isset($q['correctAnswer'])) {
                $correct = (int) $q['correctAnswer'];
            }
            if ($correct < 0 || $correct >= count($opts)) {
                $errors[] = 'Réponse correcte invalide.';
                return $errors;
            }

            if (isset($q['difficulty'])) {
                $this->normalizeDifficulty($q['difficulty']);
            }

            if (isset($q['type']) && !in_array((string) $q['type'], ['mcq', 'true_false'], true)) {
                $errors[] = 'Type de question invalide.';
                return $errors;
            }
        }

        return $errors;
    }

    private function decodeQuestionsJson(string $json): array
    {
        $d = json_decode($json !== '' ? $json : '[]', true);
        return is_array($d) ? $d : [];
    }

    private function normalizeQuizQuestionsFromPost(array $post): array
    {
        $raw = $post['questions'] ?? [];
        if (!is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $q) {
            if (!is_array($q)) {
                continue;
            }
            $bankId = isset($q['question_bank_id']) ? (int) $q['question_bank_id'] : 0;
            $text = isset($q['question']) ? trim((string) $q['question']) : '';
            if ($text === '' && isset($q['question_text'])) {
                $text = trim((string) $q['question_text']);
            }
            $opts = $q['options'] ?? [];
            if (!is_array($opts)) {
                $opts = [];
            }
            $opts = array_values(array_filter(array_map(static function ($o) {
                return trim((string) $o);
            }, $opts)));
            $ca = isset($q['correctAnswer']) ? (int) $q['correctAnswer'] : (isset($q['correct_answer']) ? (int) $q['correct_answer'] : 0);
            if ($text === '' || count($opts) < 2) {
                continue;
            }
            if ($ca < 0 || $ca >= count($opts)) {
                $ca = 0;
            }

            $item = [
                'question' => $text,
                'question_text' => $text,
                'options' => $opts,
                'correctAnswer' => $ca,
                'correct_answer' => $ca,
            ];
            if ($bankId > 0) {
                $item['question_bank_id'] = $bankId;
            }
            $out[] = $item;
        }
        return $out;
    }

    private function getQuizAttemptsTrendForTeacher(int $teacherId, int $days = 21): array
    {
        $days = max(1, min(90, $days));
        $db = $this->db();
        $sql = "SELECT a.quiz_id, DATE(a.submitted_at) AS day,
                       COUNT(*) AS count,
                       ROUND(AVG(a.percentage), 1) AS avg
                FROM quiz_attempts a
                JOIN quizzes q ON q.id = a.quiz_id
                JOIN chapters ch ON ch.id = q.chapter_id
                JOIN courses c ON c.id = ch.course_id
                WHERE c.created_by = ?
                  AND a.submitted_at >= DATE_SUB(NOW(), INTERVAL {$days} DAY)
                GROUP BY a.quiz_id, DATE(a.submitted_at)
                ORDER BY a.quiz_id ASC, day ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute([(int) $teacherId]);
        $rows = $stmt->fetchAll();
        $out = [];
        foreach ($rows as $r) {
            $qid = (int) ($r['quiz_id'] ?? 0);
            if ($qid <= 0) {
                continue;
            }
            if (!isset($out[$qid])) {
                $out[$qid] = [];
            }
            $out[$qid][] = [
                'day' => (string) ($r['day'] ?? ''),
                'count' => (int) ($r['count'] ?? 0),
                'avg' => (float) ($r['avg'] ?? 0),
            ];
        }
        return $out;
    }

    private function getAllQuizzesForTeacher(int $teacherId): array
    {
        $db = $this->db();
        $sql = "SELECT q.*, ch.title AS chapter_title, ch.id AS chapter_id, c.title AS course_title, c.id AS course_id
                FROM quizzes q
                JOIN chapters ch ON ch.id = q.chapter_id
                JOIN courses c ON c.id = ch.course_id
                WHERE c.created_by = ?
                ORDER BY q.created_at DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute([(int) $teacherId]);
        $rows = $stmt->fetchAll();
        foreach ($rows as &$r) {
            $r['questions'] = $this->decodeQuestionsJson((string) ($r['questions_json'] ?? '[]'));
        }
        unset($r);
        return $rows;
    }

    private function getUsageStatsMapForTeacher(int $teacherId): array
    {
        $db = $this->db();
        $sql = "SELECT q.id AS quiz_id,
                       COUNT(a.id) AS attempts_count,
                       COALESCE(ROUND(AVG(a.percentage), 1), 0) AS avg_percentage,
                       MAX(a.submitted_at) AS last_attempt_at
                FROM quizzes q
                JOIN chapters ch ON ch.id = q.chapter_id
                JOIN courses c ON c.id = ch.course_id
                LEFT JOIN quiz_attempts a ON a.quiz_id = q.id
                WHERE c.created_by = ?
                GROUP BY q.id";
        $stmt = $db->prepare($sql);
        $stmt->execute([(int) $teacherId]);
        $rows = $stmt->fetchAll();
        $out = [];
        foreach ($rows as $r) {
            $id = (int) ($r['quiz_id'] ?? 0);
            if ($id <= 0) {
                continue;
            }
            $out[$id] = [
                'attempts' => (int) ($r['attempts_count'] ?? 0),
                'avg' => (float) ($r['avg_percentage'] ?? 0),
                'last_attempt_at' => isset($r['last_attempt_at']) ? (string) $r['last_attempt_at'] : null,
            ];
        }
        return $out;
    }

    private function getQuizStatsForTeacher(int $teacherId): array
    {
        $db = $this->db();
        $sql = "SELECT q.id, q.title, q.difficulty, q.status, q.created_at,
                       ch.title AS chapter_title, c.title AS course_title,
                       COUNT(a.id) AS attempts_count,
                       COALESCE(ROUND(AVG(a.percentage), 1), 0) AS avg_percentage,
                       COALESCE(MAX(a.percentage), 0) AS best_percentage,
                       COALESCE(MAX(a.score), 0) AS best_score,
                       COALESCE(MAX(a.total), 0) AS best_total,
                       MAX(a.submitted_at) AS last_attempt_at
                FROM quizzes q
                JOIN chapters ch ON ch.id = q.chapter_id
                JOIN courses c ON c.id = ch.course_id
                LEFT JOIN quiz_attempts a ON a.quiz_id = q.id
                WHERE c.created_by = ?
                GROUP BY q.id
                ORDER BY attempts_count DESC, q.created_at DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute([(int) $teacherId]);
        return $stmt->fetchAll();
    }

    private function getQuizAttemptSeriesMapForTeacher(int $teacherId, int $limitPerQuiz = 120): array
    {
        $limitPerQuiz = max(10, min(300, $limitPerQuiz));
        $db = $this->db();

        $sql = "SELECT a.quiz_id, a.submitted_at, a.percentage, a.score, a.total
                FROM quiz_attempts a
                JOIN quizzes q ON q.id = a.quiz_id
                JOIN chapters ch ON ch.id = q.chapter_id
                JOIN courses c ON c.id = ch.course_id
                JOIN (
                    SELECT x.id
                    FROM (
                        SELECT a2.id,
                               ROW_NUMBER() OVER (PARTITION BY a2.quiz_id ORDER BY a2.submitted_at ASC) AS rn
                        FROM quiz_attempts a2
                    ) x
                ) keep ON keep.id = a.id
                WHERE c.created_by = ?
                ORDER BY a.quiz_id ASC, a.submitted_at ASC";

        $rows = [];
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute([(int) $teacherId]);
            $rows = $stmt->fetchAll();
        } catch (Throwable $e) {
            $rows = [];
        }

        $out = [];
        if (!empty($rows)) {
            foreach ($rows as $r) {
                $qid = (int) ($r['quiz_id'] ?? 0);
                if ($qid <= 0) {
                    continue;
                }
                if (!isset($out[$qid])) {
                    $out[$qid] = [];
                }
                $out[$qid][] = [
                    'at' => isset($r['submitted_at']) ? (string) $r['submitted_at'] : null,
                    'percentage' => (int) ($r['percentage'] ?? 0),
                    'score' => (int) ($r['score'] ?? 0),
                    'total' => (int) ($r['total'] ?? 0),
                ];
                if (count($out[$qid]) > $limitPerQuiz) {
                    array_shift($out[$qid]);
                }
            }
        }

        return $out;
    }

    private function findQuizWithChapterCourse(int $id): ?array
    {
        $db = $this->db();
        $sql = "SELECT q.*, ch.course_id, ch.title AS chapter_title, c.title AS course_title,
                       c.created_by AS course_owner_id
                FROM quizzes q
                JOIN chapters ch ON ch.id = q.chapter_id
                JOIN courses c ON c.id = ch.course_id
                WHERE q.id = ?
                LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([(int) $id]);
        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }
        $row['questions'] = $this->decodeQuestionsJson((string) ($row['questions_json'] ?? '[]'));
        return $row;
    }

    private function ensureQuizLinkTable(): void
    {
        $db = $this->db();
        $sql = "CREATE TABLE IF NOT EXISTS quiz_question_bank (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    quiz_id INT NOT NULL,
                    question_bank_id INT NOT NULL,
                    sort_order INT NOT NULL DEFAULT 0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    UNIQUE KEY uq_quiz_question (quiz_id, question_bank_id),
                    INDEX idx_quiz_sort (quiz_id, sort_order)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        try {
            $db->exec($sql);
        } catch (Throwable $e) {
        }
    }

    private function ensureQuizStatusColumn(): void
    {
        $db = $this->db();
        try {
            $db->exec("ALTER TABLE quizzes ADD COLUMN status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'approved'");
        } catch (Throwable $e) {
        }
    }

    private function createTeacherQuiz(int $chapterId, int $teacherId, string $title, string $difficulty, ?string $tags, $timeLimitSec, array $questions, string $status)
    {
        $this->ensureQuizLinkTable();
        $this->ensureQuizStatusColumn();
        $db = $this->db();
        $sql = "INSERT INTO quizzes (chapter_id, title, difficulty, tags, time_limit_sec, questions_json, created_by, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $json = json_encode($questions);
        $ok = $stmt->execute([
            (int) $chapterId,
            (string) $title,
            (string) $difficulty,
            $tags,
            $timeLimitSec !== null && $timeLimitSec !== '' ? (int) $timeLimitSec : null,
            $json !== false ? $json : '[]',
            (int) $teacherId,
            (string) $status,
        ]);
        if (!$ok) {
            return false;
        }
        $quizId = (int) $db->lastInsertId();

        $bankIds = [];
        foreach ($questions as $q) {
            if (is_array($q) && isset($q['question_bank_id'])) {
                $bid = (int) $q['question_bank_id'];
                if ($bid > 0) {
                    $bankIds[] = $bid;
                }
            }
        }
        $bankIds = array_values(array_unique($bankIds));
        if (!empty($bankIds)) {
            $ins = $db->prepare("INSERT IGNORE INTO quiz_question_bank (quiz_id, question_bank_id, sort_order) VALUES (?, ?, ?)");
            foreach ($bankIds as $so => $bid) {
                $ins->execute([$quizId, $bid, (int) $so]);
            }
        }

        return $quizId;
    }

    private function updateTeacherQuiz(int $quizId, int $teacherId, int $chapterId, string $title, string $difficulty, ?string $tags, $timeLimitSec, array $questions): bool
    {
        $this->ensureQuizLinkTable();
        $this->ensureQuizStatusColumn();
        $db = $this->db();

        $quiz = $this->findQuizWithChapterCourse($quizId);
        if (!$quiz || (int) ($quiz['course_owner_id'] ?? 0) !== (int) $teacherId) {
            return false;
        }

        $sql = "UPDATE quizzes
                SET chapter_id = ?, title = ?, difficulty = ?, tags = ?, time_limit_sec = ?, questions_json = ?, updated_at = CURRENT_TIMESTAMP
                WHERE id = ?";
        $json = json_encode($questions);
        $stmt = $db->prepare($sql);
        $ok = $stmt->execute([
            (int) $chapterId,
            (string) $title,
            (string) $difficulty,
            $tags,
            $timeLimitSec !== null && $timeLimitSec !== '' ? (int) $timeLimitSec : null,
            $json !== false ? $json : '[]',
            (int) $quizId,
        ]);
        if (!$ok) {
            return false;
        }

        $db->prepare("DELETE FROM quiz_question_bank WHERE quiz_id = ?")->execute([(int) $quizId]);
        $bankIds = [];
        foreach ($questions as $q) {
            if (is_array($q) && isset($q['question_bank_id'])) {
                $bid = (int) $q['question_bank_id'];
                if ($bid > 0) {
                    $bankIds[] = $bid;
                }
            }
        }
        $bankIds = array_values(array_unique($bankIds));
        if (!empty($bankIds)) {
            $ins = $db->prepare("INSERT IGNORE INTO quiz_question_bank (quiz_id, question_bank_id, sort_order) VALUES (?, ?, ?)");
            foreach ($bankIds as $so => $bid) {
                $ins->execute([(int) $quizId, (int) $bid, (int) $so]);
            }
        }
        return true;
    }

    private function deleteTeacherQuiz(int $quizId, int $teacherId): bool
    {
        $db = $this->db();
        $quiz = $this->findQuizWithChapterCourse($quizId);
        if (!$quiz || (int) ($quiz['course_owner_id'] ?? 0) !== (int) $teacherId) {
            return false;
        }
        try {
            $db->prepare("DELETE FROM quiz_question_bank WHERE quiz_id = ?")->execute([(int) $quizId]);
        } catch (Throwable $e) {
        }
        $stmt = $db->prepare("DELETE FROM quizzes WHERE id = ?");
        return $stmt->execute([(int) $quizId]);
    }

    private function duplicateQuizForTeacher(int $quizId, int $teacherId)
    {
        $this->ensureQuizLinkTable();
        $this->ensureQuizStatusColumn();
        $db = $this->db();

        $quiz = $this->findQuizWithChapterCourse($quizId);
        if (!$quiz || (int) ($quiz['course_owner_id'] ?? 0) !== (int) $teacherId) {
            return false;
        }

        $questions = $quiz['questions'] ?? [];
        if (!is_array($questions)) {
            $questions = [];
        }

        $title = (string) ($quiz['title'] ?? 'Quiz');
        $newTitle = mb_strlen($title) > 220 ? (mb_substr($title, 0, 220) . ' (copie)') : ($title . ' (copie)');
        $tags = isset($quiz['tags']) ? (string) $quiz['tags'] : null;
        $timeLimitSec = $quiz['time_limit_sec'] ?? null;
        $difficulty = (string) ($quiz['difficulty'] ?? 'beginner');
        $chapterId = (int) ($quiz['chapter_id'] ?? 0);

        return $this->createTeacherQuiz($chapterId, $teacherId, $newTitle, $difficulty, $tags, $timeLimitSec, $questions, 'pending');
    }

    private function getQuestionBankForTeacher(int $teacherId): array
    {
        $db = $this->db();
        $sql = "SELECT * FROM question_bank WHERE created_by = ? ORDER BY created_at DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute([(int) $teacherId]);
        $rows = $stmt->fetchAll();
        foreach ($rows as &$r) {
            $d = json_decode($r['options_json'] ?? '[]', true);
            $r['options'] = is_array($d) ? $d : [];
        }
        unset($r);
        return $rows;
    }

    private function getQuestionBankUsageStatsMapForTeacher(int $teacherId): array
    {
        $db = $this->db();
        $sql = "SELECT qb.id AS question_bank_id,
                       COUNT(DISTINCT qqb.quiz_id) AS quizzes_count,
                       COUNT(a.id) AS attempts_count,
                       COALESCE(ROUND(AVG(a.percentage), 1), 0) AS avg_percentage,
                       MAX(a.submitted_at) AS last_attempt_at
                FROM question_bank qb
                LEFT JOIN quiz_question_bank qqb ON qqb.question_bank_id = qb.id
                LEFT JOIN quiz_attempts a ON a.quiz_id = qqb.quiz_id
                WHERE qb.created_by = ?
                GROUP BY qb.id";
        $stmt = $db->prepare($sql);
        $stmt->execute([(int) $teacherId]);
        $rows = $stmt->fetchAll();
        $out = [];
        foreach ($rows as $r) {
            $id = (int) ($r['question_bank_id'] ?? 0);
            if ($id <= 0) {
                continue;
            }
            $out[$id] = [
                'quizzes' => (int) ($r['quizzes_count'] ?? 0),
                'attempts' => (int) ($r['attempts_count'] ?? 0),
                'avg' => (float) ($r['avg_percentage'] ?? 0),
                'last_attempt_at' => isset($r['last_attempt_at']) ? (string) $r['last_attempt_at'] : null,
            ];
        }
        return $out;
    }

    private function appendBankQuestionsToQuiz(array $baseQuestions, array $bankIds, int $restrictToUserId): array
    {
        $bankIds = array_map('intval', $bankIds);
        $bankIds = array_values(array_filter($bankIds, static fn($v) => $v > 0));
        if (empty($bankIds)) {
            return $baseQuestions;
        }

        $db = $this->db();
        $placeholders = implode(',', array_fill(0, count($bankIds), '?'));
        $params = $bankIds;
        $sql = "SELECT * FROM question_bank WHERE id IN ({$placeholders}) AND created_by = ?";
        $params[] = (int) $restrictToUserId;
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();
        foreach ($rows as &$r) {
            $d = json_decode($r['options_json'] ?? '[]', true);
            $r['options'] = is_array($d) ? $d : [];
        }
        unset($r);

        $out = $baseQuestions;
        foreach ($rows as $r) {
            $opts = $r['options'] ?? [];
            if (!is_array($opts)) {
                $opts = [];
            }
            $out[] = [
                'question_bank_id' => (int) ($r['id'] ?? 0),
                'question' => (string) ($r['question_text'] ?? ''),
                'question_text' => (string) ($r['question_text'] ?? ''),
                'options' => array_values($opts),
                'correctAnswer' => (int) ($r['correct_answer'] ?? 0),
                'correct_answer' => (int) ($r['correct_answer'] ?? 0),
            ];
        }
        return $out;
    }

    private function ensureQuestionCollectionTables(): void
    {
        $db = $this->db();
        try {
            $db->exec(
                "CREATE TABLE IF NOT EXISTS question_collections (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    title VARCHAR(255) NOT NULL,
                    created_by INT NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_created_by (created_by)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
            );
        } catch (Throwable $e) {
        }
        try {
            $db->exec(
                "CREATE TABLE IF NOT EXISTS question_collection_items (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    collection_id INT NOT NULL,
                    question_bank_id INT NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    UNIQUE KEY uq_collection_question (collection_id, question_bank_id),
                    INDEX idx_collection_id (collection_id),
                    INDEX idx_question_id (question_bank_id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
            );
        } catch (Throwable $e) {
        }
    }

    private function getQuestionCollectionsForTeacher(int $teacherId): array
    {
        $this->ensureQuestionCollectionTables();
        $db = $this->db();
        $stmt = $db->prepare("SELECT * FROM question_collections WHERE created_by = ? ORDER BY created_at DESC");
        $stmt->execute([(int) $teacherId]);
        return $stmt->fetchAll();
    }

    private function createQuestionCollectionRow(int $teacherId, string $title)
    {
        $this->ensureQuestionCollectionTables();
        $title = trim($title);
        if ($teacherId <= 0 || $title === '' || mb_strlen($title) > 255) {
            return false;
        }
        $db = $this->db();
        $stmt = $db->prepare("INSERT INTO question_collections (title, created_by) VALUES (?, ?)");
        $ok = $stmt->execute([$title, (int) $teacherId]);
        return $ok ? (int) $db->lastInsertId() : false;
    }

    private function deleteQuestionCollectionOwned(int $collectionId, int $teacherId): bool
    {
        $this->ensureQuestionCollectionTables();
        $db = $this->db();
        $stmt = $db->prepare("DELETE FROM question_collections WHERE id = ? AND created_by = ?");
        return $stmt->execute([(int) $collectionId, (int) $teacherId]);
    }

    private function isCollectionOwnedByTeacher(int $collectionId, int $teacherId): bool
    {
        $this->ensureQuestionCollectionTables();
        $db = $this->db();
        $stmt = $db->prepare("SELECT id FROM question_collections WHERE id = ? AND created_by = ? LIMIT 1");
        $stmt->execute([(int) $collectionId, (int) $teacherId]);
        return (bool) $stmt->fetch();
    }

    private function getQuestionIdsForCollection(int $collectionId, int $teacherId): array
    {
        if (!$this->isCollectionOwnedByTeacher($collectionId, $teacherId)) {
            return [];
        }
        $db = $this->db();
        $stmt = $db->prepare("SELECT question_bank_id FROM question_collection_items WHERE collection_id = ?");
        $stmt->execute([(int) $collectionId]);
        $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return array_map('intval', is_array($ids) ? $ids : []);
    }

    private function addQuestionToCollectionOwned(int $collectionId, int $questionBankId, int $teacherId): bool
    {
        if (!$this->isCollectionOwnedByTeacher($collectionId, $teacherId)) {
            return false;
        }
        $db = $this->db();
        $stmt = $db->prepare("INSERT IGNORE INTO question_collection_items (collection_id, question_bank_id) VALUES (?, ?)");
        return $stmt->execute([(int) $collectionId, (int) $questionBankId]);
    }

    private function removeQuestionFromCollectionOwned(int $collectionId, int $questionBankId, int $teacherId): bool
    {
        if (!$this->isCollectionOwnedByTeacher($collectionId, $teacherId)) {
            return false;
        }
        $db = $this->db();
        $stmt = $db->prepare("DELETE FROM question_collection_items WHERE collection_id = ? AND question_bank_id = ?");
        return $stmt->execute([(int) $collectionId, (int) $questionBankId]);
    }

    private function createQuestionBankQuestion(int $teacherId, ?string $title, string $questionText, array $options, int $correctAnswer, ?string $tags, string $difficulty)
    {
        $db = $this->db();
        $sql = "INSERT INTO question_bank (title, question_text, options_json, correct_answer, tags, difficulty, created_by)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $json = json_encode(is_array($options) ? array_values($options) : []);
        $ok = $stmt->execute([
            $title,
            (string) $questionText,
            $json !== false ? $json : '[]',
            (int) $correctAnswer,
            $tags,
            (string) $difficulty,
            (int) $teacherId,
        ]);
        if (!$ok) {
            return false;
        }
        return (int) $db->lastInsertId();
    }

    private function findQuestionBankOwned(int $questionId, int $teacherId): ?array
    {
        $db = $this->db();
        $sql = "SELECT * FROM question_bank WHERE id = ? AND created_by = ? LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([(int) $questionId, (int) $teacherId]);
        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }
        $d = json_decode($row['options_json'] ?? '[]', true);
        $row['options'] = is_array($d) ? $d : [];
        return $row;
    }

    private function updateQuestionBankOwned(int $questionId, int $teacherId, ?string $title, string $questionText, array $options, int $correctAnswer, ?string $tags, string $difficulty): bool
    {
        $db = $this->db();
        $sql = "UPDATE question_bank
                SET title = ?, question_text = ?, options_json = ?, correct_answer = ?, tags = ?, difficulty = ?, updated_at = CURRENT_TIMESTAMP
                WHERE id = ? AND created_by = ?";
        $stmt = $db->prepare($sql);
        $json = json_encode(is_array($options) ? array_values($options) : []);
        return $stmt->execute([
            $title,
            (string) $questionText,
            $json !== false ? $json : '[]',
            (int) $correctAnswer,
            $tags,
            (string) $difficulty,
            (int) $questionId,
            (int) $teacherId,
        ]);
    }

    private function deleteQuestionBankOwned(int $questionId, int $teacherId): bool
    {
        $db = $this->db();
        $stmt = $db->prepare("DELETE FROM question_bank WHERE id = ? AND created_by = ?");
        return $stmt->execute([(int) $questionId, (int) $teacherId]);
    }

    private function validateQuestionBankFields(string $title, string $questionText, array $opts, int $correct, string $tags): array
    {
        $errors = [];

        if (trim($questionText) === '') {
            $errors[] = 'Texte de question requis.';
        }

        if (!is_array($opts) || count($opts) < 2) {
            $errors[] = 'Ajoutez au moins deux options.';
        }

        if ($correct < 0 || $correct >= count($opts)) {
            $errors[] = 'Choisissez une réponse correcte valide.';
        }

        foreach ($opts as $o) {
            if (trim((string) $o) === '') {
                $errors[] = 'Option vide.';
                break;
            }
        }

        if ($title !== '' && mb_strlen($title) > 255) {
            $errors[] = 'Titre trop long.';
        }

        if ($tags !== '' && mb_strlen($tags) > 255) {
            $errors[] = 'Tags trop longs.';
        }

        return $errors;
    }

    /**
     * List all teacher evenements.
     */
    public function evenements() {
        $this->requireTeacher();

        $evenementModel = $this->model('Evenement');
        $data = [
            'title' => 'My Evenements - APPOLIOS',
            'evenements' => $evenementModel->getByCreator($_SESSION['user_id']),
            'flash' => $this->getFlash()
        ];

        $this->view('FrontOffice/teacher/evenements', $data);
    }

    /**
     * Show add evenement form for teacher.
     */
    public function addEvenement() {
        $this->requireTeacher();

        $data = [
            'title' => 'Propose Evenement - APPOLIOS',
            'flash' => $this->getFlash()
        ];

        $this->view('FrontOffice/teacher/add_evenement', $data);
    }

    /**
     * Store teacher evenement request (always pending approval).
     */
    public function storeEvenement() {
        $this->requireTeacher();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('teacher/evenements');
            return;
        }

        $payload = $this->extractEvenementPayload();
        $errors = $this->validateEvenementPayload($payload);

        if (!empty($errors)) {
            $this->setErrors($errors);
            $_SESSION['old'] = $_POST;
            $this->redirect('teacher/add-evenement');
            return;
        }

        $eventDate = $payload['date_debut'] . ' ' . (!empty($payload['heure_debut']) ? $payload['heure_debut'] : '00:00') . ':00';
        $evenementModel = $this->model('Evenement');

        $result = $evenementModel->create([
            'title' => $payload['title'],
            'titre' => $payload['title'],
            'description' => $payload['description'],
            'date_debut' => $payload['date_debut'],
            'date_fin' => !empty($payload['date_fin']) ? $payload['date_fin'] : null,
            'heure_debut' => !empty($payload['heure_debut']) ? $payload['heure_debut'] : null,
            'heure_fin' => !empty($payload['heure_fin']) ? $payload['heure_fin'] : null,
            'lieu' => $payload['lieu'],
            'capacite_max' => $payload['capacite_max'] > 0 ? $payload['capacite_max'] : null,
            'type' => $payload['type'],
            'statut' => $payload['statut'],
            'approval_status' => 'pending',
            'location' => $payload['lieu'],
            'event_date' => $eventDate,
            'created_by' => $_SESSION['user_id']
        ]);

        if ($result) {
            $this->setFlash('success', 'Evenement submitted to admin for approval.');
            if (isset($_POST['action']) && $_POST['action'] === 'save_and_resources') {
                $this->redirect('teacher/evenement-ressources&evenement_id=' . $result);
            } else {
                $this->redirect('teacher/evenements');
            }
            return;
        }

        $this->setFlash('error', 'Failed to create evenement request.');
        $this->redirect('teacher/add-evenement');
    }

    /**
     * Show edit teacher evenement form.
     */
    public function editEvenement($id) {
        $this->requireTeacher();

        $evenementModel = $this->model('Evenement');
        $evenement = $evenementModel->findByIdAndCreator((int) $id, (int) $_SESSION['user_id']);

        if (!$evenement) {
            $this->setFlash('error', 'Evenement not found or access denied.');
            $this->redirect('teacher/evenements');
            return;
        }

        $data = [
            'title' => 'Edit Evenement - APPOLIOS',
            'evenement' => $evenement,
            'flash' => $this->getFlash()
        ];

        $this->view('FrontOffice/teacher/edit_evenement', $data);
    }

    /**
     * Update teacher evenement.
     * If previously approved, event returns to pending.
     */
    public function updateEvenement($id) {
        $this->requireTeacher();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('teacher/evenements');
            return;
        }

        $evenementModel = $this->model('Evenement');
        $existing = $evenementModel->findByIdAndCreator((int) $id, (int) $_SESSION['user_id']);
        if (!$existing) {
            $this->setFlash('error', 'Evenement not found or access denied.');
            $this->redirect('teacher/evenements');
            return;
        }

        $payload = $this->extractEvenementPayload();
        $errors = $this->validateEvenementPayload($payload);

        if (!empty($errors)) {
            $this->setErrors($errors);
            $_SESSION['old'] = $_POST;
            $this->redirect('teacher/edit-evenement/' . (int) $id);
            return;
        }

        $eventDate = $payload['date_debut'] . ' ' . (!empty($payload['heure_debut']) ? $payload['heure_debut'] : '00:00') . ':00';
        $result = $evenementModel->update((int) $id, [
            'title' => $payload['title'],
            'titre' => $payload['title'],
            'description' => $payload['description'],
            'date_debut' => $payload['date_debut'],
            'date_fin' => !empty($payload['date_fin']) ? $payload['date_fin'] : null,
            'heure_debut' => !empty($payload['heure_debut']) ? $payload['heure_debut'] : null,
            'heure_fin' => !empty($payload['heure_fin']) ? $payload['heure_fin'] : null,
            'lieu' => $payload['lieu'],
            'capacite_max' => $payload['capacite_max'] > 0 ? $payload['capacite_max'] : null,
            'type' => $payload['type'],
            'statut' => $payload['statut'],
            'location' => $payload['lieu'],
            'event_date' => $eventDate
        ]);

        if (!$result) {
            $this->setFlash('error', 'Failed to update evenement.');
            $this->redirect('teacher/edit-evenement/' . (int) $id);
            return;
        }

        $wasApproved = ($existing['approval_status'] ?? 'approved') === 'approved';
        if ($wasApproved) {
            $evenementModel->markPendingIfApproved((int) $id);
            $this->setFlash('success', 'Evenement updated and sent back to pending approval.');
        } else {
            $this->setFlash('success', 'Evenement updated successfully.');
        }

        $this->redirect('teacher/evenements');
    }

    /**
     * Delete teacher evenement only while pending.
     */
    public function deleteEvenement($id) {
        $this->requireTeacher();

        $evenementModel = $this->model('Evenement');
        $evenement = $evenementModel->findByIdAndCreator((int) $id, (int) $_SESSION['user_id']);

        if (!$evenement) {
            $this->setFlash('error', 'Evenement not found or access denied.');
            $this->redirect('teacher/evenements');
            return;
        }

        if (($evenement['approval_status'] ?? 'approved') !== 'pending') {
            $this->setFlash('error', 'You can only delete pending evenements. Confirmed events cannot be deleted.');
            $this->redirect('teacher/evenements');
            return;
        }

        $result = $evenementModel->delete((int) $id);
        if ($result) {
            $this->setFlash('success', 'Pending evenement deleted successfully.');
        } else {
            $this->setFlash('error', 'Failed to delete evenement.');
        }

        $this->redirect('teacher/evenements');
    }

    /**
     * Teacher evenement resources page (own events only).
     */
    public function evenementRessources() {
        $this->requireTeacher();

        $eventId = (int) ($_GET['evenement_id'] ?? 0);
        if ($eventId <= 0) {
            $this->setFlash('error', 'Please choose an evenement first.');
            $this->redirect('teacher/evenements');
            return;
        }

        $evenementModel = $this->model('Evenement');
        $ressourceModel = $this->model('EvenementRessource');

        $event = $evenementModel->findByIdAndCreator($eventId, (int) $_SESSION['user_id']);
        if (!$event) {
            $this->setFlash('error', 'Evenement not found or access denied.');
            $this->redirect('teacher/evenements');
            return;
        }

        $editId = (int) ($_GET['edit_id'] ?? 0);
        $editResource = null;
        if ($editId > 0) {
            $candidate = $ressourceModel->findById($editId);
            if ($candidate && (int) $candidate['evenement_id'] === $eventId) {
                $editResource = $candidate;
            }
        }

        $data = [
            'title' => 'Evenement Resources - APPOLIOS',
            'selectedEvenementId' => $eventId,
            'selectedEvenement' => $event,
            'editResource' => $editResource,
            'rules' => $ressourceModel->getByTypeAndEvenement('rule', $eventId),
            'materials' => $ressourceModel->getByTypeAndEvenement('materiel', $eventId),
            'plans' => $ressourceModel->getByTypeAndEvenement('plan', $eventId),
            'flash' => $this->getFlash()
        ];

        $this->view('FrontOffice/teacher/evenement_ressources', $data);
    }

    /**
     * Store teacher resource and move approved event back to pending.
     */
    public function storeEvenementRessource() {
        $this->requireTeacher();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('teacher/evenements');
            return;
        }

        $type = $this->sanitize($_POST['type'] ?? '');
        $title = $this->sanitize($_POST['title'] ?? '');
        $details = $this->sanitize($_POST['details'] ?? '');
        $eventId = (int) ($_POST['evenement_id'] ?? 0);
        $isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
            || (isset($_POST['batch_mode']) && $_POST['batch_mode'] === '1');

        $errors = [];
        if (!in_array($type, ['rule', 'materiel', 'plan'], true)) {
            $errors[] = 'Invalid resource type.';
        }
        if (empty($title)) {
            $errors[] = 'Title is required.';
        }

        $evenementModel = $this->model('Evenement');
        $event = $evenementModel->findByIdAndCreator($eventId, (int) $_SESSION['user_id']);
        if (!$event) {
            $errors[] = 'Evenement not found or access denied.';
        }

        if (!empty($errors)) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
                exit();
            }

            $this->setErrors($errors);
            $_SESSION['old'] = $_POST;
            $this->redirect('teacher/evenement-ressources&evenement_id=' . $eventId);
            return;
        }

        $ressourceModel = $this->model('EvenementRessource');
        $createdId = $ressourceModel->create([
            'evenement_id' => $eventId,
            'type' => $type,
            'title' => $title,
            'details' => $details,
            'created_by' => $_SESSION['user_id']
        ]);

        $verified = false;
        if ($createdId) {
            $verified = $ressourceModel->existsInListScope($createdId, $eventId, $type);
        }

        if ($createdId && $verified) {
            $evenementModel->markPendingIfApproved($eventId);
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'verified_in_right_list' => true,
                    'message' => 'Resource saved successfully.'
                ]);
                exit();
            }

            $this->setFlash('success', 'Resource saved successfully. Event approval set to pending if it was previously approved.');
        } else {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'verified_in_right_list' => false,
                    'message' => 'Save verification failed. Check the right list and try again.'
                ]);
                exit();
            }
            $this->setFlash('error', 'Save verification failed. Check the right list and try again.');
        }

        $this->redirect('teacher/evenement-ressources&evenement_id=' . $eventId);
    }

    /**
     * Update teacher resource item.
     */
    public function updateEvenementRessource($id) {
        $this->requireTeacher();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('teacher/evenements');
            return;
        }

        $eventId = (int) ($_POST['evenement_id'] ?? 0);
        $title = $this->sanitize($_POST['title'] ?? '');
        $details = $this->sanitize($_POST['details'] ?? '');

        if ($eventId <= 0 || empty($title)) {
            $this->setFlash('error', 'Please provide valid data before saving.');
            $this->redirect('teacher/evenement-ressources&evenement_id=' . $eventId . '&edit_id=' . (int) $id);
            return;
        }

        $evenementModel = $this->model('Evenement');
        $event = $evenementModel->findByIdAndCreator($eventId, (int) $_SESSION['user_id']);
        if (!$event) {
            $this->setFlash('error', 'Evenement not found or access denied.');
            $this->redirect('teacher/evenements');
            return;
        }

        $ressourceModel = $this->model('EvenementRessource');
        $resource = $ressourceModel->findById((int) $id);
        if (!$resource || (int) $resource['evenement_id'] !== $eventId) {
            $this->setFlash('error', 'Resource not found for this evenement.');
            $this->redirect('teacher/evenement-ressources&evenement_id=' . $eventId);
            return;
        }

        $result = $ressourceModel->update((int) $id, [
            'title' => $title,
            'details' => $details,
            'evenement_id' => $eventId
        ]);

        if ($result) {
            $evenementModel->markPendingIfApproved($eventId);
            $this->setFlash('success', 'Resource updated successfully.');
        } else {
            $this->setFlash('error', 'Failed to update resource.');
        }

        $this->redirect('teacher/evenement-ressources&evenement_id=' . $eventId);
    }

    /**
     * Delete teacher resource item.
     */
    public function deleteEvenementRessource($id) {
        $this->requireTeacher();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('teacher/evenements');
            return;
        }

        $eventId = (int) ($_POST['evenement_id'] ?? 0);
        if ($eventId <= 0) {
            $this->setFlash('error', 'Invalid evenement context.');
            $this->redirect('teacher/evenements');
            return;
        }

        $evenementModel = $this->model('Evenement');
        $event = $evenementModel->findByIdAndCreator($eventId, (int) $_SESSION['user_id']);
        if (!$event) {
            $this->setFlash('error', 'Evenement not found or access denied.');
            $this->redirect('teacher/evenements');
            return;
        }

        $ressourceModel = $this->model('EvenementRessource');
        $result = $ressourceModel->deleteByEvenement((int) $id, $eventId);

        if ($result) {
            $evenementModel->markPendingIfApproved($eventId);
            $this->setFlash('success', 'Resource deleted successfully.');
        } else {
            $this->setFlash('error', 'Failed to delete resource.');
        }

        $this->redirect('teacher/evenement-ressources&evenement_id=' . $eventId);
    }

    /**
     * Extract evenement fields from request.
     * @return array
     */
    private function extractEvenementPayload() {
        return [
            'title' => $this->sanitize($_POST['title'] ?? ''),
            'description' => $this->sanitize($_POST['description'] ?? ''),
            'date_debut' => $this->sanitize($_POST['date_debut'] ?? ''),
            'date_fin' => $this->sanitize($_POST['date_fin'] ?? ''),
            'heure_debut' => $this->sanitize($_POST['heure_debut'] ?? ''),
            'heure_fin' => $this->sanitize($_POST['heure_fin'] ?? ''),
            'lieu' => $this->sanitize($_POST['lieu'] ?? ''),
            'capacite_max' => (int) ($_POST['capacite_max'] ?? 0),
            'type' => $this->sanitize($_POST['type'] ?? 'general'),
            'statut' => $this->sanitize($_POST['statut'] ?? 'planifie')
        ];
    }

    /**
     * Validate evenement payload.
     * @param array $payload
     * @return array
     */
    private function validateEvenementPayload($payload) {
        $errors = [];

        if (empty($payload['title'])) {
            $errors['title'] = 'Event title is required';
        }
        if (empty($payload['description'])) {
            $errors['description'] = 'Event description is required';
        }
        if (empty($payload['date_debut']) || strtotime($payload['date_debut']) === false) {
            $errors['date_debut'] = 'Valid start date is required';
        }
        $minDate = date('Y-m-d', strtotime('+1 day'));
        if (!empty($payload['date_debut']) && strtotime($payload['date_debut']) !== false && $payload['date_debut'] < $minDate) {
            $errors['date_debut'] = 'Start date must be at least tomorrow';
        }
        if (empty($payload['heure_debut'])) {
            $errors['heure_debut'] = 'Start time is required';
        }
        if (!empty($payload['date_fin']) && strtotime($payload['date_fin']) !== false && !empty($payload['date_debut']) && strtotime($payload['date_fin']) < strtotime($payload['date_debut'])) {
            $errors['date_fin'] = 'End date cannot be before start date';
        }
        if ($payload['capacite_max'] < 0) {
            $errors['capacite_max'] = 'Capacity must be a positive number';
        }

        return $errors;
    }
}
