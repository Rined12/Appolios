<?php

require_once __DIR__ . '/BaseController.php';

class QuizController extends BaseController
{
    private function getDb(): PDO
    {
        static $pdo = null;
        if ($pdo === null) {
            $pdo = $this->db();
        }
        return $pdo;
    }

    private function requireTeacher(): void
    {
        if (!$this->isLoggedIn() || (($_SESSION['role'] ?? '') !== 'teacher')) {
            $this->setFlash('error', 'Accès refusé.');
            $this->redirect('login');
        }
    }

    private function requireStudentRole(): bool
    {
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

    private function decodeQuestionsJson(string $json): array
    {
        $d = json_decode($json !== '' ? $json : '[]', true);
        return is_array($d) ? $d : [];
    }

    public function quizzes()
    {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        $statsRows = $this->queryQuizStatsForAdmin();
        $attemptsTotal = 0;
        $wSum = 0.0;
        foreach ($statsRows as $r) {
            $att = (int) ($r['attempts_count'] ?? 0);
            $avg = (float) ($r['avg_percentage'] ?? 0);
            $attemptsTotal += $att;
            $wSum += ($avg * $att);
        }
        $top = [
            'attempts_total' => $attemptsTotal,
            'avg_percentage' => $attemptsTotal > 0 ? round($wSum / (float) $attemptsTotal, 1) : 0.0,
        ];

        $this->view('BackOffice/admin/quizzes', [
            'title' => 'Quiz (admin) - ' . APP_NAME,
            'quizzes' => $this->queryAllQuizzesForAdmin(),
            'quizTopStats' => $top,
            'flash' => $this->getFlash(),
        ]);
    }

    public function quizHistory()
    {
        $role = (string) ($_SESSION['role'] ?? '');

        if ($role === 'admin') {
            $this->adminQuizHistory();
            return;
        }
        if ($role === 'student') {
            $this->studentQuizHistory();
            return;
        }
        $this->setFlash('error', 'Accès refusé.');
        $this->redirect('login');
    }

    public function quizStats()
    {
        $role = (string) ($_SESSION['role'] ?? '');
        if ($role === 'admin') {
            $this->adminQuizStats();
            return;
        }
        if ($role === 'teacher') {
            $this->teacherQuizStats();
            return;
        }
        $this->setFlash('error', 'Accès refusé.');
        $this->redirect('login');
    }

    public function addQuiz()
    {
        $role = (string) ($_SESSION['role'] ?? '');
        if ($role === 'admin') {
            $this->adminAddQuiz();
            return;
        }
        if ($role === 'teacher') {
            $this->teacherAddQuiz();
            return;
        }
        $this->setFlash('error', 'Accès refusé.');
        $this->redirect('login');
    }

    public function storeQuiz()
    {
        $role = (string) ($_SESSION['role'] ?? '');
        if ($role === 'admin') {
            $this->adminStoreQuiz();
            return;
        }
        if ($role === 'teacher') {
            $this->teacherStoreQuiz();
            return;
        }
        $this->setFlash('error', 'Accès refusé.');
        $this->redirect('login');
    }

    public function editQuiz($id)
    {
        $role = (string) ($_SESSION['role'] ?? '');
        if ($role === 'admin') {
            $this->adminEditQuiz($id);
            return;
        }
        if ($role === 'teacher') {
            $this->teacherEditQuiz($id);
            return;
        }
        $this->setFlash('error', 'Accès refusé.');
        $this->redirect('login');
    }

    public function updateQuiz($id)
    {
        $role = (string) ($_SESSION['role'] ?? '');
        if ($role === 'admin') {
            $this->adminUpdateQuiz($id);
            return;
        }
        if ($role === 'teacher') {
            $this->teacherUpdateQuiz($id);
            return;
        }
        $this->setFlash('error', 'Accès refusé.');
        $this->redirect('login');
    }

    public function deleteQuiz($id)
    {
        $role = (string) ($_SESSION['role'] ?? '');
        if ($role === 'admin') {
            $this->adminDeleteQuiz($id);
            return;
        }
        if ($role === 'teacher') {
            $this->teacherDeleteQuiz($id);
            return;
        }
        $this->setFlash('error', 'Accès refusé.');
        $this->redirect('login');
    }

    public function approveQuiz($id)
    {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        $ok = $this->querySetQuizStatus((int) $id, 'approved');
        $this->setFlash($ok ? 'success' : 'error', $ok ? 'Quiz approuvé.' : 'Impossible d’approuver ce quiz.');
        $this->redirect('admin/quizzes');
    }

    public function rejectQuiz($id)
    {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        $ok = $this->querySetQuizStatus((int) $id, 'rejected');
        $this->setFlash($ok ? 'success' : 'error', $ok ? 'Quiz refusé.' : 'Impossible de refuser ce quiz.');
        $this->redirect('admin/quizzes');
    }

    public function duplicateQuiz($id)
    {
        $this->teacherDuplicateQuiz($id);
    }

    public function quiz($id = null)
    {
        $role = (string) ($_SESSION['role'] ?? '');

        if ($role === 'teacher') {
            $this->teacherQuiz();
            return;
        }
        if ($role === 'student') {
            if ($id !== null && $id !== '') {
                $this->takeQuiz((int) $id);
                return;
            }
            if (!$this->requireStudentRole()) {
                return;
            }
            $studentId = (int) $_SESSION['user_id'];
            $filter = isset($_GET['filter']) ? strtolower(trim((string) $_GET['filter'])) : '';
            if (!in_array($filter, ['', 'favorites', 'redo'], true)) {
                $filter = '';
            }

            $quizzes = $this->queryQuizzesForEnrolledStudent($studentId);
            $flags = $this->queryFlagsMapByUser($studentId);

            if ($filter !== '') {
                $filtered = [];
                foreach ($quizzes as $q) {
                    $qid = (int) ($q['id'] ?? 0);
                    $f = $flags[$qid] ?? ['favorite' => false, 'redo' => false];
                    if ($filter === 'favorites' && !empty($f['favorite'])) {
                        $filtered[] = $q;
                    } elseif ($filter === 'redo' && !empty($f['redo'])) {
                        $filtered[] = $q;
                    }
                }
                $quizzes = $filtered;
            }

            $this->view('FrontOffice/student/quiz_list', [
                'title' => 'Quiz - ' . APP_NAME,
                'quizzes' => $quizzes,
                'flags' => $flags,
                'filter' => $filter,
                'flash' => $this->getFlash(),
            ]);
            return;
        }

        $this->setFlash('error', 'Accès refusé.');
        $this->redirect('login');
    }

    public function takeQuiz($id)
    {
        if (!$this->requireStudentRole()) {
            return;
        }

        $quiz = $this->queryFindQuizWithChapterCourse((int) $id);
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
        if (!$this->queryStudentIsEnrolledToCourse((int) $_SESSION['user_id'], (int) $quiz['course_id'])) {
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

    public function submitQuiz($id)
    {
        if (!$this->requireStudentRole()) {
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('student/quiz/' . (int) $id);
            return;
        }

        $quiz = $this->queryFindQuizWithChapterCourse((int) $id);
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
        if (!$this->queryStudentIsEnrolledToCourse((int) $_SESSION['user_id'], (int) $quiz['course_id'])) {
            $this->setFlash('error', 'Accès refusé.');
            $this->redirect('student/quiz');
            return;
        }

        $questions = $quiz['questions'] ?? [];
        $answers = $_POST['answers'] ?? [];
        $timedOut = !empty($_POST['timed_out']);
        if (!$timedOut) {
            $ansErr = $this->validateStudentQuizAnswers($answers, $questions);
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
        $attemptId = $this->queryRecordAttemptAndGetId((int) $_SESSION['user_id'], (int) $id, $score, $total, $percentage);

        $this->view('FrontOffice/student/quiz_result', [
            'title' => 'Résultat du quiz - ' . APP_NAME,
            'quiz' => $quiz,
            'score' => $score,
            'total' => $total,
            'percentage' => $percentage,
            'timed_out' => $timedOut,
            'recommendations' => [],
            'rank_before' => null,
            'rank_update' => null,
            'rank_progress' => null,
            'rank_spark' => [],
            'coach' => null,
            'weakChapters' => [],
            'cert' => $attemptId ? ['attempt_id' => (int) $attemptId] : null,
        ]);
    }

    public function toggleFavoriteQuiz($quizId)
    {
        if (!$this->requireStudentRole()) {
            return;
        }
        $ok = $this->queryToggleFavorite((int) $_SESSION['user_id'], (int) $quizId);
        $this->setFlash($ok ? 'success' : 'error', $ok ? 'Favori mis à jour.' : 'Impossible de mettre à jour.');
        $this->redirect('student/quiz');
    }

    public function toggleRedoQuiz($quizId)
    {
        if (!$this->requireStudentRole()) {
            return;
        }
        $ok = $this->queryToggleRedo((int) $_SESSION['user_id'], (int) $quizId);
        $this->setFlash($ok ? 'success' : 'error', $ok ? 'Redo mis à jour.' : 'Impossible de mettre à jour.');
        $this->redirect('student/quiz');
    }

    public function coach()
    {
        if (!$this->requireStudentRole()) {
            return;
        }
        $this->view('FrontOffice/student/coach', [
            'title' => 'Coach - ' . APP_NAME,
            'flash' => $this->getFlash(),
        ]);
    }

    private function adminQuizHistory(): void
    {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        $this->view('BackOffice/admin/quiz_history', [
            'title' => 'Historique des quiz - ' . APP_NAME,
            'quizzes' => $this->queryQuizHistoryForAdmin(),
            'flash' => $this->getFlash(),
        ]);
    }

    private function studentQuizHistory(): void
    {
        if (!$this->requireStudentRole()) {
            return;
        }
        $this->view('FrontOffice/student/quiz_history', [
            'title' => 'Historique des quiz - ' . APP_NAME,
            'attempts' => $this->queryAttemptsByUserWithQuizTitles((int) $_SESSION['user_id']),
            'flash' => $this->getFlash(),
        ]);
    }

    private function validateStudentQuizAnswers($answers, array $questions): ?string
    {
        if (!is_array($answers)) {
            return 'Données de réponses invalides.';
        }
        foreach ($questions as $i => $q) {
            $nopts = count($q['options'] ?? []);
            if ($nopts < 1) {
                continue;
            }
            if (!array_key_exists($i, $answers)) {
                return 'Veuillez répondre à toutes les questions.';
            }
            $given = (int) $answers[$i];
            if ($given < 0 || $given >= $nopts) {
                return 'Une réponse sélectionnée est invalide.';
            }
        }
        return null;
    }

    private function adminQuizStats(): void
    {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        $rows = $this->queryQuizStatsForAdmin();
        $series = $this->queryQuizAttemptSeriesMapForAdmin(120);

        $diffDist = ['beginner' => 0, 'intermediate' => 0, 'advanced' => 0];
        $statusDist = ['approved' => 0, 'pending' => 0, 'rejected' => 0];
        $totalQuizzes = count($rows);
        $totalAttempts = 0;
        $sumAvg = 0.0;
        $avgCount = 0;
        $approved = 0;

        foreach ($rows as $r) {
            $totalAttempts += (int) ($r['attempts_count'] ?? 0);
            if ((int) ($r['attempts_count'] ?? 0) > 0) {
                $sumAvg += (float) ($r['avg_percentage'] ?? 0);
                $avgCount++;
            }
            if ((string) ($r['status'] ?? '') === 'approved') {
                $approved++;
            }
            $d = (string) ($r['difficulty'] ?? 'beginner');
            if (!isset($diffDist[$d])) $diffDist[$d] = 0;
            $diffDist[$d]++;
            $st = (string) ($r['status'] ?? 'approved');
            if (!isset($statusDist[$st])) $statusDist[$st] = 0;
            $statusDist[$st]++;
        }

        $overallAvg = $avgCount > 0 ? round($sumAvg / $avgCount, 1) : 0.0;
        $trendRows = $this->queryQuizAttemptsTrendForAdmin(21);
        $trendMap = [];
        foreach ($trendRows as $qid => $list) {
            foreach (($list ?? []) as $p) {
                $day = (string) ($p['day'] ?? '');
                if ($day === '') continue;
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

        $this->view('BackOffice/admin/quiz_stats', [
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
            ],
            'flash' => $this->getFlash(),
        ]);
    }

    private function teacherQuiz(): void
    {
        $this->requireTeacher();
        $teacherId = (int) $_SESSION['user_id'];
        $quizUsage = $this->queryUsageStatsMapForTeacher($teacherId);
        $quizzes = $this->queryAllQuizzesForTeacher($teacherId);

        $quizTopStats = [
            'quizzes_count' => is_array($quizzes) ? count($quizzes) : 0,
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

    private function teacherQuizStats(): void
    {
        $this->requireTeacher();
        $teacherId = (int) $_SESSION['user_id'];
        $rows = $this->queryQuizStatsForTeacher($teacherId);
        $series = $this->queryQuizAttemptSeriesMapForTeacher($teacherId, 120);

        $diffDist = ['beginner' => 0, 'intermediate' => 0, 'advanced' => 0];
        $statusDist = ['approved' => 0, 'pending' => 0, 'rejected' => 0];
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
            if (!isset($diffDist[$d])) $diffDist[$d] = 0;
            $diffDist[$d]++;
            $st = (string) ($r['status'] ?? 'approved');
            if (!isset($statusDist[$st])) $statusDist[$st] = 0;
            $statusDist[$st]++;
        }
        $overallAvg = $avgCount > 0 ? round($sumAvg / $avgCount, 1) : 0.0;

        $trendRows = $this->queryQuizAttemptsTrendForTeacher($teacherId, 21);
        $trendMap = [];
        foreach ($trendRows as $qid => $list) {
            foreach (($list ?? []) as $p) {
                $day = (string) ($p['day'] ?? '');
                if ($day === '') continue;
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

        $coveragePct = $totalQuizzes > 0 ? min(100, max(0, ($this->queryPlayedQuizzesCountFromRows($rows) / $totalQuizzes) * 100)) : 0;
        $attemptsPerQuiz = $totalQuizzes > 0 ? ($totalAttempts / $totalQuizzes) : 0;
        $engagementPct = 100 * (1 - exp(-($attemptsPerQuiz / 3)));
        if ($engagementPct < 0) $engagementPct = 0;
        if ($engagementPct > 100) $engagementPct = 100;

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

    private function queryPlayedQuizzesCountFromRows(array $rows): int
    {
        $played = 0;
        foreach ($rows as $r) {
            if ((int) ($r['attempts_count'] ?? 0) > 0) {
                $played++;
            }
        }
        return $played;
    }

    private function teacherAddQuiz(): void
    {
        $this->requireTeacher();
        $teacherId = (int) $_SESSION['user_id'];
        $chapters = $this->queryChaptersForTeacher($teacherId);
        $this->view('FrontOffice/teacher/quiz_form', [
            'title' => 'Nouveau quiz - ' . APP_NAME,
            'quiz' => null,
            'chapters' => $chapters,
            'questionBank' => $this->queryQuestionBankForTeacher($teacherId),
            'flash' => $this->getFlash(),
        ]);
    }

    private function teacherStoreQuiz(): void
    {
        $this->requireTeacher();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('teacher/add-quiz');
            return;
        }
        $chapterId = (int) ($_POST['chapter_id'] ?? 0);
        if (!$this->queryTeacherChapterOwnedByUser($chapterId, (int) $_SESSION['user_id'])) {
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
        $questions = $this->queryAppendBankQuestionsToQuiz(
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

        $createdId = $this->queryCreateTeacherQuiz(
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

    private function teacherEditQuiz($id): void
    {
        $this->requireTeacher();
        $quiz = $this->queryFindQuizWithChapterCourse((int) $id);
        if (!$quiz || (int) ($quiz['course_owner_id'] ?? 0) !== (int) $_SESSION['user_id']) {
            $this->setFlash('error', 'Quiz introuvable.');
            $this->redirect('teacher/quiz');
            return;
        }
        $teacherId = (int) $_SESSION['user_id'];
        $chapters = $this->queryChaptersForTeacher($teacherId);
        $this->view('FrontOffice/teacher/quiz_form', [
            'title' => 'Modifier le quiz - ' . APP_NAME,
            'quiz' => $quiz,
            'chapters' => $chapters,
            'questionBank' => $this->queryQuestionBankForTeacher($teacherId),
            'flash' => $this->getFlash(),
        ]);
    }

    private function teacherUpdateQuiz($id): void
    {
        $this->requireTeacher();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('teacher/quiz');
            return;
        }
        $existing = $this->queryFindQuizWithChapterCourse((int) $id);
        if (!$existing || (int) ($existing['course_owner_id'] ?? 0) !== (int) $_SESSION['user_id']) {
            $this->setFlash('error', 'Quiz introuvable.');
            $this->redirect('teacher/quiz');
            return;
        }
        $chapterId = (int) ($_POST['chapter_id'] ?? 0);
        if (!$this->queryTeacherChapterOwnedByUser($chapterId, (int) $_SESSION['user_id'])) {
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
        $questions = $this->queryAppendBankQuestionsToQuiz(
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
        $updated = $this->queryUpdateTeacherQuiz(
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

    private function teacherDeleteQuiz($id): void
    {
        $this->requireTeacher();
        $existing = $this->queryFindQuizWithChapterCourse((int) $id);
        if (!$existing || (int) ($existing['course_owner_id'] ?? 0) !== (int) $_SESSION['user_id']) {
            $this->setFlash('error', 'Quiz introuvable.');
            $this->redirect('teacher/quiz');
            return;
        }
        $this->queryDeleteTeacherQuiz((int) $id, (int) $_SESSION['user_id']);
        $this->setFlash('success', 'Quiz supprimé.');
        $this->redirect('teacher/quiz');
    }

    private function teacherDuplicateQuiz($id): void
    {
        $this->requireTeacher();
        $existing = $this->queryFindQuizWithChapterCourse((int) $id);
        if (!$existing || (int) ($existing['course_owner_id'] ?? 0) !== (int) $_SESSION['user_id']) {
            $this->setFlash('error', 'Quiz introuvable.');
            $this->redirect('teacher/quiz');
            return;
        }
        $cloneQuestions = $existing['questions'] ?? [];
        $newTitle = trim((string) ($existing['title'] ?? '')) . ' (copie)';
        $newId = $this->queryCreateTeacherQuiz(
            (int) ($existing['chapter_id'] ?? 0),
            (int) $_SESSION['user_id'],
            $newTitle !== '' ? $newTitle : 'Quiz',
            (string) ($existing['difficulty'] ?? 'beginner'),
            isset($existing['tags']) ? (string) $existing['tags'] : null,
            isset($existing['time_limit_sec']) ? $existing['time_limit_sec'] : null,
            is_array($cloneQuestions) ? $cloneQuestions : [],
            'pending'
        );
        if ($newId === false) {
            $this->setFlash('error', 'Impossible de dupliquer.');
        } else {
            $this->setFlash('success', 'Quiz dupliqué.');
        }
        $this->redirect('teacher/quiz');
    }

    private function adminAddQuiz(): void
    {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        $chapters = $this->queryAllChaptersWithCourseTitlesForAdmin();
        $this->view('BackOffice/admin/quiz_form', [
            'title' => 'Nouveau quiz - ' . APP_NAME,
            'quiz' => null,
            'chapters' => $chapters,
            'questionBank' => $this->queryAllQuestionBankForAdmin(),
            'flash' => $this->getFlash(),
        ]);
    }

    private function adminStoreQuiz(): void
    {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/add-quiz');
            return;
        }
        $chapterId = (int) ($_POST['chapter_id'] ?? 0);
        if (!$this->queryAdminChapterExists($chapterId)) {
            $this->setFlash('error', 'Chapitre invalide.');
            $this->redirect('admin/add-quiz');
            return;
        }
        $meta = $this->validateQuizMetaFromPost($_POST);
        if (!empty($meta['errors'])) {
            $this->setFlash('error', $meta['errors'][0]);
            $this->redirect('admin/add-quiz');
            return;
        }
        $bankIds = isset($_POST['bank_question_ids']) && is_array($_POST['bank_question_ids']) ? $_POST['bank_question_ids'] : [];
        $questions = $this->queryAppendBankQuestionsToQuiz(
            $this->normalizeQuizQuestionsFromPost($_POST),
            $bankIds,
            null
        );
        if (count($questions) < 1) {
            $this->setFlash('error', 'Ajoutez au moins une question (saisie manuelle ou depuis la banque).');
            $this->redirect('admin/add-quiz');
            return;
        }
        $qErr = $this->validateNormalizedQuizQuestions($questions);
        if (!empty($qErr)) {
            $this->setFlash('error', $qErr[0]);
            $this->redirect('admin/add-quiz');
            return;
        }
        $createdId = $this->queryCreateAdminQuiz(
            (int) $_SESSION['user_id'],
            $chapterId,
            $this->sanitize($meta['title']),
            (string) $meta['difficulty'],
            $meta['tags'] !== null ? $this->sanitize($meta['tags']) : null,
            $meta['time_limit_sec'],
            $questions,
            'approved'
        );
        if ($createdId === false) {
            $this->setFlash('error', 'Impossible d’enregistrer le quiz (vérifiez la base de données).');
            $this->redirect('admin/add-quiz');
            return;
        }
        $this->setFlash('success', 'Quiz enregistré.');
        $this->redirect('admin/quizzes');
    }

    private function adminEditQuiz($id): void
    {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        $quiz = $this->queryFindQuizWithChapterCourse((int) $id);
        if (!$quiz) {
            $this->setFlash('error', 'Quiz introuvable.');
            $this->redirect('admin/quizzes');
            return;
        }
        $chapters = $this->queryAllChaptersWithCourseTitlesForAdmin();
        $this->view('BackOffice/admin/quiz_form', [
            'title' => 'Modifier le quiz - ' . APP_NAME,
            'quiz' => $quiz,
            'chapters' => $chapters,
            'questionBank' => $this->queryAllQuestionBankForAdmin(),
            'flash' => $this->getFlash(),
        ]);
    }

    private function adminUpdateQuiz($id): void
    {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        $chapterId = (int) ($_POST['chapter_id'] ?? 0);
        if (!$this->queryAdminChapterExists($chapterId)) {
            $this->setFlash('error', 'Chapitre invalide.');
            $this->redirect('admin/edit-quiz/' . (int) $id);
            return;
        }
        $meta = $this->validateQuizMetaFromPost($_POST);
        if (!empty($meta['errors'])) {
            $this->setFlash('error', $meta['errors'][0]);
            $this->redirect('admin/edit-quiz/' . (int) $id);
            return;
        }
        $bankIds = isset($_POST['bank_question_ids']) && is_array($_POST['bank_question_ids']) ? $_POST['bank_question_ids'] : [];
        $questions = $this->queryAppendBankQuestionsToQuiz(
            $this->normalizeQuizQuestionsFromPost($_POST),
            $bankIds,
            null
        );
        if (count($questions) < 1) {
            $this->setFlash('error', 'Au moins une question requise (saisie ou banque).');
            $this->redirect('admin/edit-quiz/' . (int) $id);
            return;
        }
        $qErr = $this->validateNormalizedQuizQuestions($questions);
        if (!empty($qErr)) {
            $this->setFlash('error', $qErr[0]);
            $this->redirect('admin/edit-quiz/' . (int) $id);
            return;
        }
        $updated = $this->queryUpdateAdminQuiz(
            (int) $id,
            $chapterId,
            $this->sanitize($meta['title']),
            (string) $meta['difficulty'],
            $meta['tags'] !== null ? $this->sanitize($meta['tags']) : null,
            $meta['time_limit_sec'],
            $questions
        );
        if ($updated === false) {
            $this->setFlash('error', 'Impossible de mettre à jour le quiz (vérifiez la base de données).');
            $this->redirect('admin/edit-quiz/' . (int) $id);
            return;
        }
        $this->setFlash('success', 'Quiz mis à jour.');
        $this->redirect('admin/quizzes');
    }

    private function adminDeleteQuiz($id): void
    {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        $existing = $this->queryFindQuizWithChapterCourse((int) $id);
        if (!$existing) {
            $this->setFlash('error', 'Quiz introuvable.');
            $this->redirect('admin/quizzes');
            return;
        }
        $this->queryDeleteAdminQuiz((int) $id);
        $this->setFlash('success', 'Quiz supprimé.');
        $this->redirect('admin/quizzes');
    }

    private function validateQuizMetaFromPost(array $post): array
    {
        $title = isset($post['title']) ? trim((string) $post['title']) : '';
        $difficulty = isset($post['difficulty']) ? strtolower(trim((string) $post['difficulty'])) : 'beginner';
        $tags = isset($post['tags']) ? trim((string) $post['tags']) : '';
        $time = $post['time_limit_sec'] ?? null;
        $errs = [];

        if ($title === '') {
            $errs[] = 'Le titre est obligatoire.';
        }
        if (!in_array($difficulty, ['beginner', 'intermediate', 'advanced'], true)) {
            $difficulty = 'beginner';
        }
        if ($time !== null && $time !== '') {
            $t = (int) $time;
            if ($t < 0) {
                $errs[] = 'Time limit invalide.';
            }
        }
        return [
            'title' => $title,
            'difficulty' => $difficulty,
            'tags' => $tags !== '' ? $tags : null,
            'time_limit_sec' => $time,
            'errors' => $errs,
        ];
    }

    private function validateNormalizedQuizQuestions(array $questions): array
    {
        $errs = [];
        foreach ($questions as $q) {
            if (!is_array($q)) {
                $errs[] = 'Question invalide.';
                break;
            }
            $text = trim((string) ($q['question_text'] ?? ($q['question'] ?? '')));
            $opts = $q['options'] ?? [];
            if ($text === '' || !is_array($opts) || count($opts) < 2) {
                $errs[] = 'Une question est invalide.';
                break;
            }
        }
        return $errs;
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

    private function queryAppendBankQuestionsToQuiz(array $baseQuestions, array $bankIds, ?int $restrictToUserId): array
    {
        $bankIds = array_map('intval', $bankIds);
        $bankIds = array_values(array_filter($bankIds, static fn($v) => $v > 0));
        if (empty($bankIds)) {
            return $baseQuestions;
        }
        $db = $this->getDb();
        $placeholders = implode(',', array_fill(0, count($bankIds), '?'));
        $params = $bankIds;
        $sql = "SELECT * FROM question_bank WHERE id IN ({$placeholders})";
        if ($restrictToUserId !== null) {
            $sql .= ' AND created_by = ?';
            $params[] = (int) $restrictToUserId;
        }
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

    private function queryAllQuestionBankForAdmin(): array
    {
        $db = $this->getDb();
        $stmt = $db->query('SELECT * FROM question_bank ORDER BY created_at DESC');
        $rows = $stmt ? $stmt->fetchAll() : [];
        foreach ($rows as &$r) {
            $d = json_decode($r['options_json'] ?? '[]', true);
            $r['options'] = is_array($d) ? $d : [];
        }
        unset($r);
        return $rows;
    }

    private function queryQuestionBankForTeacher(int $teacherId): array
    {
        $db = $this->getDb();
        $stmt = $db->prepare('SELECT * FROM question_bank WHERE created_by = ? ORDER BY created_at DESC');
        $stmt->execute([(int) $teacherId]);
        $rows = $stmt->fetchAll();
        foreach ($rows as &$r) {
            $d = json_decode($r['options_json'] ?? '[]', true);
            $r['options'] = is_array($d) ? $d : [];
        }
        unset($r);
        return $rows;
    }

    private function queryAllChaptersWithCourseTitlesForAdmin(): array
    {
        $db = $this->getDb();
        $sql = 'SELECT ch.*, c.title AS course_title FROM chapters ch JOIN courses c ON c.id = ch.course_id ORDER BY c.title ASC, ch.sort_order ASC';
        $stmt = $db->query($sql);
        return $stmt ? $stmt->fetchAll() : [];
    }

    private function queryAdminChapterExists(int $chapterId): bool
    {
        if ($chapterId <= 0) {
            return false;
        }
        $db = $this->getDb();
        $st = $db->prepare('SELECT id FROM chapters WHERE id = ? LIMIT 1');
        $st->execute([(int) $chapterId]);
        return (bool) $st->fetch();
    }

    private function queryChaptersForTeacher(int $teacherId): array
    {
        $db = $this->getDb();
        $sql = 'SELECT ch.*, c.title AS course_title
                FROM chapters ch
                JOIN courses c ON c.id = ch.course_id
                WHERE c.created_by = ?
                ORDER BY c.title ASC, ch.sort_order ASC';
        $st = $db->prepare($sql);
        $st->execute([(int) $teacherId]);
        return $st->fetchAll();
    }

    private function queryTeacherChapterOwnedByUser(int $chapterId, int $teacherId): bool
    {
        $db = $this->getDb();
        $sql = 'SELECT ch.id
                FROM chapters ch
                JOIN courses c ON c.id = ch.course_id
                WHERE ch.id = ? AND c.created_by = ?
                LIMIT 1';
        $st = $db->prepare($sql);
        $st->execute([(int) $chapterId, (int) $teacherId]);
        return (bool) $st->fetch();
    }

    private function queryAllQuizzesForAdmin(): array
    {
        $db = $this->getDb();
        $sql = 'SELECT q.*, ch.title AS chapter_title, ch.id AS chapter_id, c.title AS course_title, c.id AS course_id, u.name AS author_name
                FROM quizzes q
                JOIN chapters ch ON ch.id = q.chapter_id
                JOIN courses c ON c.id = ch.course_id
                JOIN users u ON u.id = q.created_by
                ORDER BY q.created_at DESC';
        $stmt = $db->query($sql);
        $rows = $stmt ? $stmt->fetchAll() : [];
        foreach ($rows as &$r) {
            $r['questions'] = $this->decodeQuestionsJson((string) ($r['questions_json'] ?? '[]'));
        }
        unset($r);
        return $rows;
    }

    private function queryQuizHistoryForAdmin(): array
    {
        $db = $this->getDb();
        $sql = "SELECT q.id, q.title, q.status, q.created_at, q.updated_at,
                       ch.title AS chapter_title, c.title AS course_title,
                       u.name AS author_name
                FROM quizzes q
                JOIN chapters ch ON ch.id = q.chapter_id
                JOIN courses c ON c.id = ch.course_id
                JOIN users u ON u.id = q.created_by
                ORDER BY q.updated_at DESC, q.created_at DESC";
        $stmt = $db->query($sql);
        return $stmt ? $stmt->fetchAll() : [];
    }

    private function queryQuizStatsForAdmin(): array
    {
        $db = $this->getDb();
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
                GROUP BY q.id
                ORDER BY attempts_count DESC, q.created_at DESC";
        $stmt = $db->query($sql);
        return $stmt ? $stmt->fetchAll() : [];
    }

    private function queryQuizAttemptSeriesMapForAdmin(int $limitPerQuiz = 120): array
    {
        $limitPerQuiz = max(10, min(300, $limitPerQuiz));
        $db = $this->getDb();
        $sql = "SELECT a.quiz_id, a.submitted_at, a.percentage, a.score, a.total
                FROM quiz_attempts a
                JOIN (
                    SELECT x.id
                    FROM (
                        SELECT a2.id,
                               ROW_NUMBER() OVER (PARTITION BY a2.quiz_id ORDER BY a2.submitted_at ASC) AS rn
                        FROM quiz_attempts a2
                    ) x
                ) keep ON keep.id = a.id
                ORDER BY a.quiz_id ASC, a.submitted_at ASC";
        $rows = [];
        try {
            $stmt = $db->query($sql);
            $rows = $stmt ? $stmt->fetchAll() : [];
        } catch (Throwable $e) {
            $rows = [];
        }
        $out = [];
        foreach ($rows as $r) {
            $qid = (int) ($r['quiz_id'] ?? 0);
            if ($qid <= 0) continue;
            if (!isset($out[$qid])) $out[$qid] = [];
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
        return $out;
    }

    private function queryQuizAttemptsTrendForAdmin(int $days = 21): array
    {
        $days = max(1, min(90, $days));
        $db = $this->getDb();
        $sql = "SELECT a.quiz_id, DATE(a.submitted_at) AS day,
                       COUNT(*) AS count,
                       ROUND(AVG(a.percentage), 1) AS avg
                FROM quiz_attempts a
                WHERE a.submitted_at >= DATE_SUB(NOW(), INTERVAL {$days} DAY)
                GROUP BY a.quiz_id, DATE(a.submitted_at)
                ORDER BY a.quiz_id ASC, day ASC";
        $stmt = $db->query($sql);
        $rows = $stmt ? $stmt->fetchAll() : [];
        $out = [];
        foreach ($rows as $r) {
            $qid = (int) ($r['quiz_id'] ?? 0);
            if ($qid <= 0) continue;
            if (!isset($out[$qid])) $out[$qid] = [];
            $out[$qid][] = [
                'day' => (string) ($r['day'] ?? ''),
                'count' => (int) ($r['count'] ?? 0),
                'avg' => (float) ($r['avg'] ?? 0),
            ];
        }
        return $out;
    }

    private function queryFindQuizWithChapterCourse(int $id): ?array
    {
        $db = $this->getDb();
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

    private function queryEnsureQuizLinkTable(): void
    {
        $db = $this->getDb();
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

    private function queryEnsureQuizStatusColumn(): void
    {
        $db = $this->getDb();
        try {
            $db->exec("ALTER TABLE quizzes ADD COLUMN status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'approved'");
        } catch (Throwable $e) {
        }
    }

    private function queryCreateAdminQuiz(int $adminId, int $chapterId, string $title, string $difficulty, ?string $tags, $timeLimitSec, array $questions, string $status)
    {
        $this->queryEnsureQuizLinkTable();
        $this->queryEnsureQuizStatusColumn();
        $db = $this->getDb();
        $sql = 'INSERT INTO quizzes (chapter_id, title, difficulty, tags, time_limit_sec, questions_json, created_by, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
        $stmt = $db->prepare($sql);
        $json = json_encode($questions);
        $ok = $stmt->execute([
            (int) $chapterId,
            (string) $title,
            (string) $difficulty,
            $tags,
            $timeLimitSec !== null && $timeLimitSec !== '' ? (int) $timeLimitSec : null,
            $json !== false ? $json : '[]',
            (int) $adminId,
            (string) $status,
        ]);
        if (!$ok) {
            return false;
        }
        $quizId = (int) $db->lastInsertId();
        $this->querySyncQuizQuestionBankLinks($quizId, $questions);
        return $quizId;
    }

    private function queryUpdateAdminQuiz(int $quizId, int $chapterId, string $title, string $difficulty, ?string $tags, $timeLimitSec, array $questions): bool
    {
        $this->queryEnsureQuizLinkTable();
        $this->queryEnsureQuizStatusColumn();
        $db = $this->getDb();
        $sql = 'UPDATE quizzes
                SET chapter_id = ?, title = ?, difficulty = ?, tags = ?, time_limit_sec = ?, questions_json = ?, updated_at = CURRENT_TIMESTAMP
                WHERE id = ?';
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
        $db->prepare('DELETE FROM quiz_question_bank WHERE quiz_id = ?')->execute([(int) $quizId]);
        $this->querySyncQuizQuestionBankLinks($quizId, $questions);
        return true;
    }

    private function queryDeleteAdminQuiz(int $quizId): bool
    {
        $db = $this->getDb();
        try {
            $db->prepare('DELETE FROM quiz_question_bank WHERE quiz_id = ?')->execute([(int) $quizId]);
        } catch (Throwable $e) {
        }
        $stmt = $db->prepare('DELETE FROM quizzes WHERE id = ?');
        return $stmt->execute([(int) $quizId]);
    }

    private function querySetQuizStatus(int $quizId, string $status): bool
    {
        $this->queryEnsureQuizStatusColumn();
        $status = in_array($status, ['pending', 'approved', 'rejected'], true) ? $status : 'approved';
        $db = $this->getDb();
        $stmt = $db->prepare('UPDATE quizzes SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?');
        return $stmt->execute([(string) $status, (int) $quizId]);
    }

    private function querySyncQuizQuestionBankLinks(int $quizId, array $questions): void
    {
        $db = $this->getDb();
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
        if (empty($bankIds)) {
            return;
        }
        $ins = $db->prepare('INSERT IGNORE INTO quiz_question_bank (quiz_id, question_bank_id, sort_order) VALUES (?, ?, ?)');
        foreach ($bankIds as $so => $bid) {
            $ins->execute([(int) $quizId, (int) $bid, (int) $so]);
        }
    }

    private function queryCreateTeacherQuiz(int $chapterId, int $teacherId, string $title, string $difficulty, ?string $tags, $timeLimitSec, array $questions, string $status)
    {
        $this->queryEnsureQuizLinkTable();
        $this->queryEnsureQuizStatusColumn();
        $db = $this->getDb();
        $sql = 'INSERT INTO quizzes (chapter_id, title, difficulty, tags, time_limit_sec, questions_json, created_by, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
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
        $this->querySyncQuizQuestionBankLinks($quizId, $questions);
        return $quizId;
    }

    private function queryUpdateTeacherQuiz(int $quizId, int $teacherId, int $chapterId, string $title, string $difficulty, ?string $tags, $timeLimitSec, array $questions): bool
    {
        $this->queryEnsureQuizLinkTable();
        $this->queryEnsureQuizStatusColumn();
        $db = $this->getDb();
        $quiz = $this->queryFindQuizWithChapterCourse($quizId);
        if (!$quiz || (int) ($quiz['course_owner_id'] ?? 0) !== (int) $teacherId) {
            return false;
        }
        $sql = 'UPDATE quizzes
                SET chapter_id = ?, title = ?, difficulty = ?, tags = ?, time_limit_sec = ?, questions_json = ?, updated_at = CURRENT_TIMESTAMP
                WHERE id = ?';
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
        $db->prepare('DELETE FROM quiz_question_bank WHERE quiz_id = ?')->execute([(int) $quizId]);
        $this->querySyncQuizQuestionBankLinks($quizId, $questions);
        return true;
    }

    private function queryDeleteTeacherQuiz(int $quizId, int $teacherId): bool
    {
        $db = $this->getDb();
        $quiz = $this->queryFindQuizWithChapterCourse($quizId);
        if (!$quiz || (int) ($quiz['course_owner_id'] ?? 0) !== (int) $teacherId) {
            return false;
        }
        try {
            $db->prepare('DELETE FROM quiz_question_bank WHERE quiz_id = ?')->execute([(int) $quizId]);
        } catch (Throwable $e) {
        }
        $stmt = $db->prepare('DELETE FROM quizzes WHERE id = ?');
        return $stmt->execute([(int) $quizId]);
    }

    private function queryAllQuizzesForTeacher(int $teacherId): array
    {
        $db = $this->getDb();
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

    private function queryUsageStatsMapForTeacher(int $teacherId): array
    {
        $db = $this->getDb();
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
            if ($id <= 0) continue;
            $out[$id] = [
                'attempts' => (int) ($r['attempts_count'] ?? 0),
                'avg' => (float) ($r['avg_percentage'] ?? 0),
                'last_attempt_at' => isset($r['last_attempt_at']) ? (string) $r['last_attempt_at'] : null,
            ];
        }
        return $out;
    }

    private function queryQuizStatsForTeacher(int $teacherId): array
    {
        $db = $this->getDb();
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

    private function queryQuizAttemptSeriesMapForTeacher(int $teacherId, int $limitPerQuiz = 120): array
    {
        $limitPerQuiz = max(10, min(300, $limitPerQuiz));
        $db = $this->getDb();
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
        foreach ($rows as $r) {
            $qid = (int) ($r['quiz_id'] ?? 0);
            if ($qid <= 0) continue;
            if (!isset($out[$qid])) $out[$qid] = [];
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
        return $out;
    }

    private function queryQuizAttemptsTrendForTeacher(int $teacherId, int $days = 21): array
    {
        $days = max(1, min(90, $days));
        $db = $this->getDb();
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
            if ($qid <= 0) continue;
            if (!isset($out[$qid])) $out[$qid] = [];
            $out[$qid][] = [
                'day' => (string) ($r['day'] ?? ''),
                'count' => (int) ($r['count'] ?? 0),
                'avg' => (float) ($r['avg'] ?? 0),
            ];
        }
        return $out;
    }

    private function queryQuizzesForEnrolledStudent(int $studentId): array
    {
        $db = $this->getDb();
        $sql = "SELECT q.*, ch.title AS chapter_title, ch.id AS chapter_id, c.title AS course_title, c.id AS course_id
                FROM quizzes q
                JOIN chapters ch ON ch.id = q.chapter_id
                JOIN courses c ON c.id = ch.course_id
                JOIN enrollments e ON e.course_id = c.id AND e.user_id = ?
                WHERE q.status = 'approved'
                ORDER BY c.title ASC, ch.sort_order ASC, q.created_at DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute([(int) $studentId]);
        $rows = $stmt->fetchAll();
        foreach ($rows as &$r) {
            $r['questions'] = $this->decodeQuestionsJson((string) ($r['questions_json'] ?? '[]'));
            $r['question_count'] = is_array($r['questions']) ? count($r['questions']) : 0;
        }
        unset($r);
        return $rows;
    }

    private function queryStudentIsEnrolledToCourse(int $userId, int $courseId): bool
    {
        if ($userId <= 0 || $courseId <= 0) {
            return false;
        }
        $db = $this->getDb();
        $stmt = $db->prepare('SELECT id FROM enrollments WHERE user_id = ? AND course_id = ? LIMIT 1');
        $stmt->execute([(int) $userId, (int) $courseId]);
        return (bool) $stmt->fetch();
    }

    private function queryRecordAttemptAndGetId(int $userId, int $quizId, int $score, int $total, int $percentage): ?int
    {
        $db = $this->getDb();
        $sql = 'INSERT INTO quiz_attempts (user_id, quiz_id, score, total, percentage) VALUES (?, ?, ?, ?, ?)';
        $stmt = $db->prepare($sql);
        $ok = $stmt->execute([(int) $userId, (int) $quizId, (int) $score, (int) $total, (int) $percentage]);
        if (!$ok) {
            return null;
        }
        return (int) $db->lastInsertId();
    }

    private function queryAttemptsByUserWithQuizTitles(int $userId): array
    {
        $db = $this->getDb();
        $sql = 'SELECT a.*, q.title AS quiz_title
                FROM quiz_attempts a
                JOIN quizzes q ON q.id = a.quiz_id
                WHERE a.user_id = ?
                ORDER BY a.submitted_at DESC';
        $stmt = $db->prepare($sql);
        $stmt->execute([(int) $userId]);
        return $stmt->fetchAll();
    }

    private function queryAttemptedQuizIdsByUser(int $userId): array
    {
        $db = $this->getDb();
        $stmt = $db->prepare('SELECT DISTINCT quiz_id FROM quiz_attempts WHERE user_id = ?');
        $stmt->execute([(int) $userId]);
        $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return array_map('intval', is_array($ids) ? $ids : []);
    }

    private function queryEnsureStudentQuizFlagsTable(): void
    {
        $db = $this->getDb();
        $sql = "CREATE TABLE IF NOT EXISTS student_quiz_flags (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    quiz_id INT NOT NULL,
                    is_favorite TINYINT(1) NOT NULL DEFAULT 0,
                    is_redo TINYINT(1) NOT NULL DEFAULT 0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    UNIQUE KEY uq_user_quiz (user_id, quiz_id),
                    INDEX idx_user_fav (user_id, is_favorite),
                    INDEX idx_user_redo (user_id, is_redo)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        try {
            $db->exec($sql);
        } catch (Throwable $e) {
        }
    }

    private function queryFlagsMapByUser(int $userId): array
    {
        $this->queryEnsureStudentQuizFlagsTable();
        $db = $this->getDb();
        $stmt = $db->prepare('SELECT quiz_id, is_favorite, is_redo FROM student_quiz_flags WHERE user_id = ?');
        $stmt->execute([(int) $userId]);
        $rows = $stmt->fetchAll();
        $out = [];
        foreach ($rows as $r) {
            $qid = (int) ($r['quiz_id'] ?? 0);
            if ($qid <= 0) continue;
            $out[$qid] = [
                'favorite' => !empty($r['is_favorite']),
                'redo' => !empty($r['is_redo']),
            ];
        }
        return $out;
    }

    private function queryToggleFavorite(int $userId, int $quizId): bool
    {
        $this->queryEnsureStudentQuizFlagsTable();
        $db = $this->getDb();
        $sql = 'INSERT INTO student_quiz_flags (user_id, quiz_id, is_favorite)
                VALUES (?, ?, 1)
                ON DUPLICATE KEY UPDATE is_favorite = 1 - is_favorite';
        $stmt = $db->prepare($sql);
        return $stmt->execute([(int) $userId, (int) $quizId]);
    }

    private function queryToggleRedo(int $userId, int $quizId): bool
    {
        $this->queryEnsureStudentQuizFlagsTable();
        $db = $this->getDb();
        $sql = 'INSERT INTO student_quiz_flags (user_id, quiz_id, is_redo)
                VALUES (?, ?, 1)
                ON DUPLICATE KEY UPDATE is_redo = 1 - is_redo';
        $stmt = $db->prepare($sql);
        return $stmt->execute([(int) $userId, (int) $quizId]);
    }
}
