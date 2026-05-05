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

    public function remediationPlan()
    {
        $this->requireTeacher();

        header('Content-Type: application/json; charset=utf-8');

        try {
            $teacherId = (int) $_SESSION['user_id'];
            $rows = $this->queryTeacherQuizRemediationRows($teacherId);
            $items = [];

            foreach ($rows as $r) {
                $id = (int) ($r['id'] ?? 0);
                if ($id <= 0) {
                    continue;
                }

                $attempts = (int) ($r['attempts_count'] ?? 0);
                $avg = (float) ($r['avg_percentage'] ?? 0);
                $status = (string) ($r['status'] ?? '');

                $attemptsNorm = log((float) ($attempts + 1), 10) / log(51.0, 10);
                if ($attemptsNorm < 0) $attemptsNorm = 0;
                if ($attemptsNorm > 1) $attemptsNorm = 1;

                $avgNorm = 1.0 - max(0.0, min(1.0, $avg / 100.0));

                $score = (int) round((($attemptsNorm * 0.55) + ($avgNorm * 0.45)) * 100.0);
                if ($status === 'pending') {
                    $score = min(100, $score + 10);
                }
                if ($score < 0) $score = 0;
                if ($score > 100) $score = 100;

                $level = 'LOW';
                if ($score >= 70) $level = 'HIGH';
                elseif ($score >= 40) $level = 'MEDIUM';

                $recs = [];
                if ($attempts < 5) {
                    $recs[] = 'Manque de données : encourage les étudiants à tenter ce quiz (partage / annonce / devoir).';
                }
                if ($attempts >= 10 && $avg <= 45) {
                    $recs[] = 'Quiz difficile : vérifie les questions les plus piégeuses et ajoute 1–2 questions plus progressives.';
                } elseif ($attempts >= 10 && $avg >= 85) {
                    $recs[] = 'Quiz trop facile : augmente la difficulté ou ajoute des questions avancées.';
                } else {
                    $recs[] = 'Quiz équilibré : tu peux améliorer la couverture en ajoutant une question ciblée sur les notions clés.';
                }
                if ($status === 'pending') {
                    $recs[] = 'Statut en attente : pense à soumettre/valider le quiz pour le rendre visible.';
                }

                $items[] = [
                    'id' => $id,
                    'title' => (string) ($r['title'] ?? ''),
                    'sub' => trim(((string) ($r['course_title'] ?? '')) . ' — ' . ((string) ($r['chapter_title'] ?? ''))),
                    'attempts' => $attempts,
                    'avg' => round($avg, 1),
                    'score' => $score,
                    'level' => $level,
                    'recommendations' => $recs,
                ];
            }

            usort($items, static function ($a, $b) {
                return (int) ($b['score'] ?? 0) <=> (int) ($a['score'] ?? 0);
            });

            echo json_encode(['ok' => true, 'items' => $items]);
        } catch (Throwable $e) {
            echo json_encode(['ok' => false, 'items' => [], 'error' => 'Erreur interne']);
        }
        exit;
    }

    public function riskQueue()
    {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        $filters = [
            'type' => isset($_GET['type']) ? strtolower(trim((string) $_GET['type'])) : '',
            'level' => isset($_GET['level']) ? strtoupper(trim((string) $_GET['level'])) : '',
        ];

        $items = $this->queryAdminRiskQueueItems();

        if ($filters['type'] !== '' && in_array($filters['type'], ['quiz', 'question'], true)) {
            $items = array_values(array_filter($items, static function ($it) use ($filters) {
                return isset($it['type']) && strtolower((string) $it['type']) === $filters['type'];
            }));
        }
        if ($filters['level'] !== '' && in_array($filters['level'], ['HIGH', 'MEDIUM', 'LOW'], true)) {
            $items = array_values(array_filter($items, static function ($it) use ($filters) {
                return isset($it['risk_level']) && strtoupper((string) $it['risk_level']) === $filters['level'];
            }));
        }

        usort($items, static function ($a, $b) {
            return (int) ($b['risk_score'] ?? 0) <=> (int) ($a['risk_score'] ?? 0);
        });

        $this->view('BackOffice/admin/risk_queue', [
            'title' => 'Risk Queue - ' . APP_NAME,
            'items' => $items,
            'filters' => $filters,
            'flash' => $this->getFlash(),
        ]);
    }

    private function requireTeacher(): void
    {
        if (!$this->isLoggedIn() || (($_SESSION['role'] ?? '') !== 'teacher')) {
            $this->setFlash('error', 'Accès refusé.');
            $this->redirect('login');
        }
    }

    private function queryTeacherQuizRemediationRows(int $teacherId): array
    {
        $db = $this->getDb();
        $sql = "SELECT q.id, q.title, q.status,
                       ch.title AS chapter_title,
                       c.title AS course_title,
                       COUNT(a.id) AS attempts_count,
                       COALESCE(ROUND(AVG(a.percentage), 1), 0) AS avg_percentage
                FROM quizzes q
                JOIN chapters ch ON ch.id = q.chapter_id
                JOIN courses c ON c.id = ch.course_id
                LEFT JOIN quiz_attempts a ON a.quiz_id = q.id
                WHERE c.created_by = ?
                GROUP BY q.id
                ORDER BY q.created_at DESC";
        $st = $db->prepare($sql);
        $st->execute([(int) $teacherId]);
        return $st->fetchAll();
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

            $rank = $this->queryGetOrCreateStudentRank($studentId);
            $rankProgress = $this->rankProgressInfo((int) ($rank['rating'] ?? 1000));
            $rankSpark = $this->queryLastPercentagesByUser($studentId, 7);

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
                'rank' => $rank,
                'rankProgress' => $rankProgress,
                'rankSpark' => $rankSpark,
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

        $recommendations = $this->buildAfterQuizRecommendations($quiz, $percentage, (int) $_SESSION['user_id']);

        $rankBefore = $this->queryGetOrCreateStudentRank((int) $_SESSION['user_id']);
        $ratingUpdate = $this->updateStudentRatingAfterQuiz($rankBefore, $quiz, $percentage);

        $rankAfter = $this->queryGetOrCreateStudentRank((int) $_SESSION['user_id']);
        $rankProgress = $this->rankProgressInfo((int) ($rankAfter['rating'] ?? ($ratingUpdate['new_rating'] ?? 1000)));
        $spark = $this->queryLastPercentagesByUser((int) $_SESSION['user_id'], 7);

        $coach = $this->buildRankCoachMessage($quiz, $percentage, $ratingUpdate, $rankProgress);

        $chapterAverages = $this->queryChapterAveragesByUser((int) $_SESSION['user_id']);
        $chapterRecent = $this->queryRecentPercentagesByChapterForUser((int) $_SESSION['user_id'], 60);
        uasort($chapterAverages, static function ($a, $b) {
            $av = (float) ($a['avg'] ?? 0);
            $bv = (float) ($b['avg'] ?? 0);
            return $av <=> $bv;
        });

        $chapterNames = [];
        $enrolledQuizzes = $this->queryQuizzesForEnrolledStudent((int) $_SESSION['user_id']);
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

        $cert = null;
        if ($attemptId !== null && $attemptId > 0 && $percentage >= 70) {
            $cert = $this->buildCertificatePayload((int) $attemptId, (int) $_SESSION['user_id'], (int) $id, (int) $percentage);
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
            'cert' => $cert,
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

        $uid = (int) $_SESSION['user_id'];

        $rank = $this->queryGetOrCreateStudentRank($uid);
        $rankProgress = $this->rankProgressInfo((int) ($rank['rating'] ?? 1000));
        $spark = $this->queryLastPercentagesByUser($uid, 7);

        $all = $this->queryQuizzesForEnrolledStudent($uid);

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

        $chapterAverages = $this->queryChapterAveragesByUser($uid);
        $chapterRecent = $this->queryRecentPercentagesByChapterForUser($uid, 60);
        uasort($chapterAverages, static function ($a, $b) {
            $av = (float) ($a['avg'] ?? 0);
            $bv = (float) ($b['avg'] ?? 0);
            return $av <=> $bv;
        });

        $weakChapters = [];
        foreach ($chapterAverages as $cid => $info) {
            $avg = (int) round((float) ($info['avg'] ?? 0));
            $attempts = (int) ($info['attempts'] ?? 0);
            $last = isset($chapterRecent[(int) $cid]) ? $chapterRecent[(int) $cid] : null;
            $lastVal = is_array($last) ? ($last['last'] ?? null) : null;
            $prevVal = is_array($last) ? ($last['prev'] ?? null) : null;
            $lastPct = $lastVal !== null ? (int) $lastVal : null;
            $prevPct = $prevVal !== null ? (int) $prevVal : null;
            $delta = ($lastPct !== null && $prevPct !== null) ? ($lastPct - $prevPct) : null;
            $lastAt = is_array($last) ? ($last['last_at'] ?? null) : null;

            $needBoost = max(0.0, min(1.0, (70.0 - (float) $avg) / 70.0));
            $confidence = 1.0 - exp(-0.45 * max(0, $attempts));
            $priority = $needBoost * $confidence;

            $weakChapters[] = [
                'chapter_id' => (int) $cid,
                'chapter_title' => (string) ($chapterNames[(int) $cid] ?? ''),
                'avg' => $avg,
                'attempts' => $attempts,
                'last' => $lastPct,
                'prev' => $prevPct,
                'delta' => $delta,
                'last_at' => $lastAt ? (string) $lastAt : null,
                'priority' => $priority,
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
            'url' => APP_ENTRY . '?url=student-quiz/quiz',
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

    public function remedial($id)
    {
        if (!$this->requireStudentRole()) {
            return;
        }

        $quizId = (int) $id;
        if ($quizId <= 0) {
            $this->redirect('student/quiz');
            return;
        }

        $quiz = $this->queryFindQuizWithChapterCourse($quizId);
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

        if (!$this->queryStudentIsEnrolledToCourse((int) $_SESSION['user_id'], (int) ($quiz['course_id'] ?? 0))) {
            $this->setFlash('error', 'Accès refusé.');
            $this->redirect('student/quiz');
            return;
        }

        $lastPct = $this->queryLastAttemptPercentageForUserQuiz((int) $_SESSION['user_id'], $quizId);
        if ($lastPct === null) {
            $this->setFlash('error', 'Passe d’abord ce quiz pour débloquer le rattrapage.');
            $this->redirect('student/quiz/' . $quizId);
            return;
        }
        if ($lastPct >= 60) {
            $this->setFlash('success', 'Bravo ! Ton dernier score est suffisant, pas besoin de rattrapage.');
            $this->redirect('student/quiz/' . $quizId);
            return;
        }

        $tags = (string) ($quiz['tags'] ?? '');
        $count = 10;
        $picked = $this->queryPickReadableQuestionsByTags($tags, $count);

        $this->view('FrontOffice/student/training', [
            'title' => 'Rattrapage - ' . APP_NAME,
            'difficulty' => '',
            'count' => $count,
            'questions' => $picked,
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

    private function queryAdminRiskQueueItems(int $limit = 120): array
    {
        $limit = max(20, min(400, $limit));

        $quizRows = $this->queryAdminRiskyQuizzes($limit);
        $questionRows = $this->queryAdminRiskyQuestions($limit);

        $out = [];
        foreach ($quizRows as $r) {
            $score = (int) ($r['risk_score'] ?? 0);
            $out[] = [
                'type' => 'quiz',
                'id' => (int) ($r['id'] ?? 0),
                'title' => (string) ($r['title'] ?? ''),
                'sub' => trim(((string) ($r['course_title'] ?? '')) . ' — ' . ((string) ($r['chapter_title'] ?? ''))),
                'risk_score' => $score,
                'risk_level' => $this->riskLevelForScore($score),
                'reasons' => $this->riskReasonsForRow($r, 'quiz'),
            ];
        }
        foreach ($questionRows as $r) {
            $score = (int) ($r['risk_score'] ?? 0);
            $out[] = [
                'type' => 'question',
                'id' => (int) ($r['id'] ?? 0),
                'title' => (string) ($r['title'] ?? ''),
                'sub' => (string) ($r['author_name'] ?? ''),
                'risk_score' => $score,
                'risk_level' => $this->riskLevelForScore($score),
                'reasons' => $this->riskReasonsForRow($r, 'question'),
            ];
        }
        return $out;
    }

    private function queryAdminRiskyQuizzes(int $limit): array
    {
        $db = $this->getDb();
        $sql = "SELECT q.id, q.title, q.difficulty, q.status,
                       ch.title AS chapter_title,
                       c.title AS course_title,
                       COUNT(a.id) AS attempts_count,
                       COALESCE(ROUND(AVG(a.percentage), 1), 0) AS avg_percentage
                FROM quizzes q
                JOIN chapters ch ON ch.id = q.chapter_id
                JOIN courses c ON c.id = ch.course_id
                LEFT JOIN quiz_attempts a ON a.quiz_id = q.id
                GROUP BY q.id
                ORDER BY q.created_at DESC
                LIMIT " . (int) $limit;
        $stmt = $db->query($sql);
        $rows = $stmt ? $stmt->fetchAll() : [];

        foreach ($rows as &$r) {
            $att = (int) ($r['attempts_count'] ?? 0);
            $avg = (float) ($r['avg_percentage'] ?? 0);
            $risk = 0;
            $risk += (int) round(max(0, min(1, (10 - $att) / 10)) * 40);
            $risk += (int) round(max(0, min(1, (55 - $avg) / 55)) * 60);
            $r['risk_score'] = max(0, min(100, $risk));
        }
        unset($r);

        usort($rows, static function ($a, $b) {
            return (int) ($b['risk_score'] ?? 0) <=> (int) ($a['risk_score'] ?? 0);
        });

        return array_slice($rows, 0, min($limit, count($rows)));
    }

    private function queryAdminRiskyQuestions(int $limit): array
    {
        $db = $this->getDb();
        $sql = "SELECT qb.id, COALESCE(NULLIF(qb.title,''), CONCAT('Question #', qb.id)) AS title,
                       u.name AS author_name,
                       COUNT(a.id) AS attempts_count,
                       COALESCE(ROUND(AVG(a.percentage), 1), 0) AS avg_percentage
                FROM question_bank qb
                JOIN users u ON u.id = qb.created_by
                LEFT JOIN quiz_question_bank qqb ON qqb.question_bank_id = qb.id
                LEFT JOIN quiz_attempts a ON a.quiz_id = qqb.quiz_id
                GROUP BY qb.id
                ORDER BY qb.created_at DESC
                LIMIT " . (int) $limit;
        $stmt = $db->query($sql);
        $rows = $stmt ? $stmt->fetchAll() : [];

        foreach ($rows as &$r) {
            $att = (int) ($r['attempts_count'] ?? 0);
            $avg = (float) ($r['avg_percentage'] ?? 0);
            $risk = 0;
            $risk += (int) round(max(0, min(1, (10 - $att) / 10)) * 45);
            $risk += (int) round(max(0, min(1, (55 - $avg) / 55)) * 55);
            $r['risk_score'] = max(0, min(100, $risk));
        }
        unset($r);

        usort($rows, static function ($a, $b) {
            return (int) ($b['risk_score'] ?? 0) <=> (int) ($a['risk_score'] ?? 0);
        });

        return array_slice($rows, 0, min($limit, count($rows)));
    }

    private function riskLevelForScore(int $score): string
    {
        if ($score >= 70) {
            return 'HIGH';
        }
        if ($score >= 40) {
            return 'MEDIUM';
        }
        return 'LOW';
    }

    private function riskReasonsForRow(array $row, string $type): array
    {
        $reasons = [];
        $att = (int) ($row['attempts_count'] ?? 0);
        $avg = (float) ($row['avg_percentage'] ?? 0);

        if ($att <= 0) {
            $reasons[] = 'Aucune tentative';
        } elseif ($att < 3) {
            $reasons[] = 'Peu de tentatives';
        }

        if ($avg > 0 && $avg < 45) {
            $reasons[] = 'Moyenne faible';
        }

        if ($type === 'quiz') {
            $st = (string) ($row['status'] ?? '');
            if ($st === 'pending') {
                $reasons[] = 'En attente';
            }
        }

        return $reasons;
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
        $autoIds = $this->pickQuestionBankIdsForBlueprint(
            (int) $_SESSION['user_id'],
            $_POST['auto_bank_count'] ?? null,
            $_POST['auto_bank_difficulty'] ?? null,
            $_POST['auto_bank_tags'] ?? null
        );
        $bankIds = array_values(array_unique(array_merge($bankIds, $autoIds)));
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
        $this->redirect('teacher-quiz/quiz');
    }

    private function teacherEditQuiz($id): void
    {
        $this->requireTeacher();
        $quiz = $this->queryFindQuizWithChapterCourse((int) $id);
        if (!$quiz || (int) ($quiz['course_owner_id'] ?? 0) !== (int) $_SESSION['user_id']) {
            $this->setFlash('error', 'Quiz introuvable.');
            $this->redirect('teacher-quiz/quiz');
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
            $this->redirect('teacher-quiz/quiz');
            return;
        }
        $existing = $this->queryFindQuizWithChapterCourse((int) $id);
        if (!$existing || (int) ($existing['course_owner_id'] ?? 0) !== (int) $_SESSION['user_id']) {
            $this->setFlash('error', 'Quiz introuvable.');
            $this->redirect('teacher-quiz/quiz');
            return;
        }
        $chapterId = (int) ($_POST['chapter_id'] ?? 0);
        if (!$this->queryTeacherChapterOwnedByUser($chapterId, (int) $_SESSION['user_id'])) {
            $this->setFlash('error', 'Chapitre invalide.');
            $this->redirect('teacher-quiz/edit-quiz/' . (int) $id);
            return;
        }
        $meta = $this->validateQuizMetaFromPost($_POST);
        if (!empty($meta['errors'])) {
            $this->setFlash('error', $meta['errors'][0]);
            $this->redirect('teacher-quiz/edit-quiz/' . (int) $id);
            return;
        }
        $bankIds = isset($_POST['bank_question_ids']) && is_array($_POST['bank_question_ids']) ? $_POST['bank_question_ids'] : [];
        $autoIds = $this->pickQuestionBankIdsForBlueprint(
            (int) $_SESSION['user_id'],
            $_POST['auto_bank_count'] ?? null,
            $_POST['auto_bank_difficulty'] ?? null,
            $_POST['auto_bank_tags'] ?? null
        );
        $bankIds = array_values(array_unique(array_merge($bankIds, $autoIds)));
        $questions = $this->queryAppendBankQuestionsToQuiz(
            $this->normalizeQuizQuestionsFromPost($_POST),
            $bankIds,
            (int) $_SESSION['user_id']
        );
        if (count($questions) < 1) {
            $this->setFlash('error', 'Au moins une question requise (saisie ou banque).');
            $this->redirect('teacher-quiz/edit-quiz/' . (int) $id);
            return;
        }
        $qErr = $this->validateNormalizedQuizQuestions($questions);
        if (!empty($qErr)) {
            $this->setFlash('error', $qErr[0]);
            $this->redirect('teacher-quiz/edit-quiz/' . (int) $id);
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
            $this->redirect('teacher-quiz/edit-quiz/' . (int) $id);
            return;
        }
        $this->setFlash('success', 'Quiz mis à jour.');
        $this->redirect('teacher-quiz/quiz');
    }

    private function teacherDeleteQuiz($id): void
    {
        $this->requireTeacher();
        $existing = $this->queryFindQuizWithChapterCourse((int) $id);
        if (!$existing || (int) ($existing['course_owner_id'] ?? 0) !== (int) $_SESSION['user_id']) {
            $this->setFlash('error', 'Quiz introuvable.');
            $this->redirect('teacher-quiz/quiz');
            return;
        }
        $this->queryDeleteTeacherQuiz((int) $id, (int) $_SESSION['user_id']);
        $this->setFlash('success', 'Quiz supprimé.');
        $this->redirect('teacher-quiz/quiz');
    }

    private function teacherDuplicateQuiz($id): void
    {
        $this->requireTeacher();
        $existing = $this->queryFindQuizWithChapterCourse((int) $id);
        if (!$existing || (int) ($existing['course_owner_id'] ?? 0) !== (int) $_SESSION['user_id']) {
            $this->setFlash('error', 'Quiz introuvable.');
            $this->redirect('teacher-quiz/quiz');
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
        $this->redirect('teacher-quiz/quiz');
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
        $autoIds = $this->pickQuestionBankIdsForBlueprint(
            null,
            $_POST['auto_bank_count'] ?? null,
            $_POST['auto_bank_difficulty'] ?? null,
            $_POST['auto_bank_tags'] ?? null
        );
        $bankIds = array_values(array_unique(array_merge($bankIds, $autoIds)));
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
        $autoIds = $this->pickQuestionBankIdsForBlueprint(
            null,
            $_POST['auto_bank_count'] ?? null,
            $_POST['auto_bank_difficulty'] ?? null,
            $_POST['auto_bank_tags'] ?? null
        );
        $bankIds = array_values(array_unique(array_merge($bankIds, $autoIds)));
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

    private function pickQuestionBankIdsForBlueprint(?int $restrictToUserId, $rawCount, $rawDifficulty, $rawTags): array
    {
        $count = is_numeric($rawCount) ? (int) $rawCount : 0;
        $count = max(0, min(30, $count));
        if ($count <= 0) {
            return [];
        }

        $difficulty = is_string($rawDifficulty) ? strtolower(trim($rawDifficulty)) : '';
        if (!in_array($difficulty, ['', 'beginner', 'intermediate', 'advanced'], true)) {
            $difficulty = '';
        }

        $tags = is_string($rawTags) ? trim($rawTags) : '';
        $tagList = preg_split('/[\s,;]+/', strtolower($tags), -1, PREG_SPLIT_NO_EMPTY);
        $tagList = is_array($tagList) ? array_values(array_unique($tagList)) : [];

        $db = $this->getDb();
        $where = [];
        $params = [];

        if ($restrictToUserId !== null) {
            $where[] = 'qb.created_by = ?';
            $params[] = (int) $restrictToUserId;
        }
        if ($difficulty !== '') {
            $where[] = 'qb.difficulty = ?';
            $params[] = (string) $difficulty;
        }
        foreach ($tagList as $t) {
            $where[] = 'LOWER(COALESCE(qb.tags, \'\')) LIKE ?';
            $params[] = '%' . $t . '%';
        }

        $sql = 'SELECT qb.id FROM question_bank qb';
        if (!empty($where)) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY RAND() LIMIT ' . (int) $count;

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        $ids = [];
        foreach ($rows as $r) {
            $id = (int) ($r['id'] ?? 0);
            if ($id > 0) {
                $ids[] = $id;
            }
        }
        return array_values(array_unique($ids));
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

    private function queryEnsureStudentRanksTable(): void
    {
        $db = $this->getDb();
        $sql = "CREATE TABLE IF NOT EXISTS student_ranks (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    rating INT NOT NULL DEFAULT 1000,
                    league VARCHAR(30) NOT NULL DEFAULT 'Bronze',
                    division VARCHAR(10) NOT NULL DEFAULT 'III',
                    last_updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    UNIQUE KEY uq_user_rank (user_id),
                    INDEX idx_rating (rating)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        try {
            $db->exec($sql);
        } catch (Throwable $e) {
        }
    }

    private function queryGetOrCreateStudentRank(int $userId): array
    {
        $this->queryEnsureStudentRanksTable();
        $db = $this->getDb();
        $stmt = $db->prepare('SELECT * FROM student_ranks WHERE user_id = ? LIMIT 1');
        $stmt->execute([(int) $userId]);
        $row = $stmt->fetch();
        if ($row) {
            return $row;
        }
        $ins = $db->prepare("INSERT INTO student_ranks (user_id, rating, league, division) VALUES (?, 1000, 'Bronze', 'III')");
        $ins->execute([(int) $userId]);
        $stmt = $db->prepare('SELECT * FROM student_ranks WHERE user_id = ? LIMIT 1');
        $stmt->execute([(int) $userId]);
        $row = $stmt->fetch();
        return $row ?: ['user_id' => (int) $userId, 'rating' => 1000, 'league' => 'Bronze', 'division' => 'III'];
    }

    private function queryUpdateStudentRankRating(int $userId, int $rating, string $league, string $division): bool
    {
        $this->queryEnsureStudentRanksTable();
        $db = $this->getDb();
        $stmt = $db->prepare('UPDATE student_ranks SET rating = ?, league = ?, division = ? WHERE user_id = ?');
        return $stmt->execute([(int) $rating, (string) $league, (string) $division, (int) $userId]);
    }

    private function queryLastPercentagesByUser(int $userId, int $limit = 7): array
    {
        $limit = max(1, min(30, $limit));
        $db = $this->getDb();
        $sql = "SELECT percentage FROM quiz_attempts WHERE user_id = ? ORDER BY submitted_at DESC LIMIT {$limit}";
        $stmt = $db->prepare($sql);
        $stmt->execute([(int) $userId]);
        $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $vals = array_map('intval', is_array($rows) ? $rows : []);
        $vals = array_reverse($vals);
        return $vals;
    }

    private function queryChapterAveragesByUser(int $userId): array
    {
        $db = $this->getDb();
        $sql = 'SELECT q.chapter_id, AVG(a.percentage) AS avg_percentage, COUNT(*) AS attempts
                FROM quiz_attempts a
                JOIN quizzes q ON q.id = a.quiz_id
                WHERE a.user_id = ?
                GROUP BY q.chapter_id';
        $stmt = $db->prepare($sql);
        $stmt->execute([(int) $userId]);
        $rows = $stmt->fetchAll();
        $out = [];
        foreach ($rows as $r) {
            $cid = (int) ($r['chapter_id'] ?? 0);
            if ($cid <= 0) continue;
            $out[$cid] = [
                'avg' => (float) ($r['avg_percentage'] ?? 0),
                'attempts' => (int) ($r['attempts'] ?? 0),
            ];
        }
        return $out;
    }

    private function queryLastAttemptByChapterInCourse(int $userId, int $courseId): array
    {
        $db = $this->getDb();
        $sql = 'SELECT q.chapter_id, MAX(a.submitted_at) AS last_attempt_at
                FROM quiz_attempts a
                JOIN quizzes q ON q.id = a.quiz_id
                JOIN chapters ch ON ch.id = q.chapter_id
                WHERE a.user_id = ? AND ch.course_id = ?
                GROUP BY q.chapter_id';
        $stmt = $db->prepare($sql);
        $stmt->execute([(int) $userId, (int) $courseId]);
        $rows = $stmt->fetchAll();
        $out = [];
        foreach ($rows as $r) {
            $cid = (int) ($r['chapter_id'] ?? 0);
            if ($cid <= 0) continue;
            $out[$cid] = [
                'last_attempt_at' => isset($r['last_attempt_at']) ? (string) $r['last_attempt_at'] : null,
            ];
        }
        return $out;
    }

    private function queryApprovedCandidatesForCourse(int $courseId, int $excludeQuizId, int $limit = 80): array
    {
        $limit = max(5, min(200, $limit));
        $db = $this->getDb();
        $sql = "SELECT q.*, ch.title AS chapter_title, ch.id AS chapter_id, c.title AS course_title, c.id AS course_id
                FROM quizzes q
                JOIN chapters ch ON ch.id = q.chapter_id
                JOIN courses c ON c.id = ch.course_id
                WHERE c.id = ? AND q.status = 'approved' AND q.id <> ?
                ORDER BY q.created_at DESC
                LIMIT {$limit}";
        $stmt = $db->prepare($sql);
        $stmt->execute([(int) $courseId, (int) $excludeQuizId]);
        $rows = $stmt->fetchAll();
        foreach ($rows as &$r) {
            $r['questions'] = $this->decodeQuestionsJson((string) ($r['questions_json'] ?? '[]'));
        }
        unset($r);
        return $rows;
    }

    private function queryRecentPercentagesByChapterForUser(int $userId, int $days = 60): array
    {
        $days = max(1, min(365, $days));
        $db = $this->getDb();
        $sql = "SELECT q.chapter_id, a.percentage, a.submitted_at
                FROM quiz_attempts a
                JOIN quizzes q ON q.id = a.quiz_id
                WHERE a.user_id = ? AND a.submitted_at >= DATE_SUB(NOW(), INTERVAL {$days} DAY)
                ORDER BY a.submitted_at DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute([(int) $userId]);
        $rows = $stmt->fetchAll();
        $out = [];
        foreach ($rows as $r) {
            $cid = (int) ($r['chapter_id'] ?? 0);
            if ($cid <= 0) continue;
            if (!isset($out[$cid])) {
                $out[$cid] = [];
            }
            $out[$cid][] = [
                'percentage' => (int) ($r['percentage'] ?? 0),
                'at' => isset($r['submitted_at']) ? (string) $r['submitted_at'] : null,
            ];
            if (count($out[$cid]) > 30) {
                array_pop($out[$cid]);
            }
        }

        $summary = [];
        foreach ($out as $cid => $list) {
            $last = isset($list[0]) ? $list[0] : null;
            $prev = isset($list[1]) ? $list[1] : null;
            $summary[(int) $cid] = [
                'last' => $last ? (int) ($last['percentage'] ?? 0) : null,
                'prev' => $prev ? (int) ($prev['percentage'] ?? 0) : null,
                'last_at' => $last ? (string) ($last['at'] ?? '') : null,
            ];
        }
        return $summary;
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

    private function updateStudentRatingAfterQuiz(array $rankBefore, array $quiz, int $percentage): array
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
        $this->queryUpdateStudentRankRating((int) ($rankBefore['user_id'] ?? 0), $newRating, $league, $division);

        return [
            'old_rating' => $oldRating,
            'new_rating' => $newRating,
            'delta' => $newRating - $oldRating,
            'league' => $league,
            'division' => $division,
            'expected' => $expected,
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
            $plan[] = 'Ensuite refais ce quiz (ou un équivalent) après 24h';
        } elseif ($percentage < $goalPct) {
            $plan[] = "Prochaine tentative : vise {$goalPct}% pour stabiliser ton rating";
            $plan[] = 'Fais 1 quiz de consolidation + 1 quiz un peu plus dur';
        } else {
            $plan[] = 'Continue sur cette difficulté et vise +10 points de rating';
            $plan[] = 'Ajoute un quiz plus difficile pour accélérer la progression';
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

    private function buildAfterQuizRecommendations(array $quiz, int $percentage, int $userId): array
    {
        $courseId = (int) ($quiz['course_id'] ?? 0);
        $chapterId = (int) ($quiz['chapter_id'] ?? 0);
        $currentQuizId = (int) ($quiz['id'] ?? 0);
        $difficulty = (string) ($quiz['difficulty'] ?? 'beginner');

        if ($courseId <= 0) {
            return [];
        }

        $attemptedQuizIds = $this->queryAttemptedQuizIdsByUser($userId);
        $attemptedSet = [];
        foreach ($attemptedQuizIds as $aqid) {
            $attemptedSet[(int) $aqid] = true;
        }

        $chapterAverages = $this->queryChapterAveragesByUser($userId);
        $lastAttemptByChapter = $this->queryLastAttemptByChapterInCourse($userId, $courseId);

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

        $candidates = $this->queryApprovedCandidatesForCourse($courseId, $currentQuizId, 80);
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
                'is_new' => !isset($attemptedSet[$cid]),
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

        $exp = $pick($best, static function ($x) use ($exploreChapterId) {
            return ($exploreChapterId > 0 && (int) $x['chapter_id'] === $exploreChapterId);
        }, $attemptedSet);

        $push = static function (?array $item, string $goal, string $reason, array $insights = []) use (&$out) {
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
                'is_new' => !empty($item['is_new']),
                'insights' => $insights,
            ];
        };

        $mkInsights = static function (?array $item, string $needLabel) use ($need, $weakChapterId, $chapterId): array {
            if (!$item) {
                return [];
            }
            $ins = [];
            $ins[] = $needLabel;
            if (!empty($item['is_new'])) {
                $ins[] = 'Nouveau (pas encore tenté)';
            } else {
                $ins[] = 'Déjà tenté (révision ciblée)';
            }
            $cid = (int) ($item['chapter_id'] ?? 0);
            if ($weakChapterId > 0 && $cid === $weakChapterId) {
                $ins[] = 'Chapitre faible (priorité)';
            } elseif ($chapterId > 0 && $cid === $chapterId) {
                $ins[] = 'Même chapitre (continuité)';
            }
            return $ins;
        };

        if ($need === 'remediation') {
            $push($rem, 'Remédiation', 'Ciblé sur la faiblesse principale (objectif : remonter au-dessus de 70%).', $mkInsights($rem, 'Focus : récupérer des points'));
            $push($con, 'Consolidation', 'Stabilise la base (même chapitre / difficulté similaire).', $mkInsights($con, 'Focus : consolider'));
            $push($pro, 'Progression', 'Une fois stabilisé, monte en difficulté pour progresser durablement.', $mkInsights($pro, 'Focus : progression'));
        } elseif ($need === 'progression') {
            $push($pro, 'Challenge', 'Tu es en forme : monte d’un cran pour gagner du rating.', $mkInsights($pro, 'Focus : challenge'));
            $push($con, 'Consolidation', 'Assure un second quiz “safe” pour stabiliser ton niveau.', $mkInsights($con, 'Focus : consolider'));
            $push($exp, 'Exploration', 'Découvre un autre chapitre (espacement des révisions).', $mkInsights($exp, 'Focus : variété'));
        } else {
            $push($con, 'Consolidation', 'Consolide avant de monter en difficulté.', $mkInsights($con, 'Focus : consolider'));
            $push($pro, 'Progression', 'Monte doucement en difficulté pour débloquer le palier suivant.', $mkInsights($pro, 'Focus : progression'));
            $push($exp, 'Exploration', 'Varie les chapitres pour éviter l’oubli et élargir ta maîtrise.', $mkInsights($exp, 'Focus : variété'));
        }

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
                    'reason' => 'Quiz recommandé selon ton résultat et ton parcours.',
                    'is_new' => !empty($b['is_new']),
                    'insights' => !empty($b['is_new']) ? ['Nouveau (pas encore tenté)'] : ['Déjà tenté (révision)'],
                ];
            }
        }

        return array_values(array_slice($out, 0, 3));
    }

    private function buildCertificatePayload(int $attemptId, int $userId, int $quizId, int $percentage): array
    {
        $payload = [
            'v' => 1,
            'aid' => (int) $attemptId,
            'uid' => (int) $userId,
            'qid' => (int) $quizId,
            'pct' => (int) $percentage,
            'iat' => time(),
            'exp' => time() + (60 * 60 * 24 * 365),
        ];
        $json = json_encode($payload);
        $b64 = rtrim(strtr(base64_encode($json !== false ? $json : '{}'), '+/', '-_'), '=');
        $sig = hash_hmac('sha256', $b64, (string) APP_QR_SECRET, true);
        $sigB64 = rtrim(strtr(base64_encode($sig), '+/', '-_'), '=');
        $token = $b64 . '.' . $sigB64;

        $entry = (string) APP_ENTRY;
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = isset($_SERVER['HTTP_HOST']) ? trim((string) $_SERVER['HTTP_HOST']) : '';
        $script = isset($_SERVER['SCRIPT_NAME']) ? trim((string) $_SERVER['SCRIPT_NAME']) : '';
        if ($host !== '' && $script !== '') {
            $entry = $scheme . '://' . $host . $script;
        }

        $needLan = (strpos($entry, '://localhost') !== false) || (strpos($entry, '://127.0.0.1') !== false);
        if ($needLan) {
            $lan = '';
            if (defined('APP_LAN_HOST') && is_string(APP_LAN_HOST) && trim(APP_LAN_HOST) !== '') {
                $lan = trim((string) APP_LAN_HOST);
            } else {
                try {
                    $ips = @gethostbynamel(gethostname());
                    $ips = is_array($ips) ? $ips : [];

                    $candidates = [];
                    foreach ($ips as $ip) {
                        $ip = (string) $ip;
                        if ($ip === '' || $ip === '127.0.0.1') {
                            continue;
                        }
                        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                            continue;
                        }
                        $candidates[] = $ip;
                    }

                    $pick = function (string $prefix) use ($candidates): string {
                        foreach ($candidates as $c) {
                            if (strncmp($c, $prefix, strlen($prefix)) === 0) {
                                return $c;
                            }
                        }
                        return '';
                    };

                    $lan = $pick('192.168.1.');
                    if ($lan === '') $lan = $pick('192.168.0.');
                    if ($lan === '') $lan = $pick('10.');
                    if ($lan === '') $lan = $pick('172.16.');
                    if ($lan === '' && !empty($candidates)) {
                        $lan = (string) $candidates[0];
                    }
                } catch (Throwable $e) {
                    $lan = '';
                }
            }
            if ($lan !== '') {
                $entry = str_replace('://localhost', '://' . $lan, $entry);
                $entry = str_replace('://127.0.0.1', '://' . $lan, $entry);
            }
        }

        return [
            'attempt_id' => (int) $attemptId,
            'token' => $token,
            'verify_url' => $entry . '?url=home/verify-cert&token=' . rawurlencode($token),
        ];
    }

    private function queryLastAttemptPercentageForUserQuiz(int $userId, int $quizId): ?int
    {
        $db = $this->getDb();
        $sql = 'SELECT percentage
                FROM quiz_attempts
                WHERE user_id = ? AND quiz_id = ?
                ORDER BY submitted_at DESC
                LIMIT 1';
        $stmt = $db->prepare($sql);
        $stmt->execute([(int) $userId, (int) $quizId]);
        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }
        return (int) ($row['percentage'] ?? 0);
    }

    private function queryAllReadableQuestions(): array
    {
        $db = $this->getDb();
        $sql = 'SELECT qb.* FROM question_bank qb ORDER BY qb.created_at DESC';
        $stmt = $db->query($sql);
        $rows = $stmt ? $stmt->fetchAll() : [];
        foreach ($rows as &$r) {
            $d = json_decode($r['options_json'] ?? '[]', true);
            $r['options'] = is_array($d) ? $d : [];
        }
        unset($r);
        return $rows;
    }

    private function queryPickReadableQuestionsByTags(string $tags, int $count = 10): array
    {
        $count = max(5, min(30, $count));
        $all = $this->queryAllReadableQuestions();
        $tagList = preg_split('/[\s,;]+/', strtolower(trim($tags)), -1, PREG_SPLIT_NO_EMPTY);
        $tagList = is_array($tagList) ? array_values(array_unique($tagList)) : [];

        $filtered = [];
        foreach ($all as $q) {
            $opts = isset($q['options']) && is_array($q['options']) ? array_values($q['options']) : [];
            if (count($opts) < 2) {
                continue;
            }

            if (!empty($tagList)) {
                $t = strtolower((string) ($q['tags'] ?? ''));
                $ok = true;
                foreach ($tagList as $tg) {
                    if ($tg === '') continue;
                    if (strpos($t, $tg) === false) {
                        $ok = false;
                        break;
                    }
                }
                if (!$ok) {
                    continue;
                }
            }

            $ca = isset($q['correct_answer']) ? (int) $q['correct_answer'] : 0;
            if ($ca < 0 || $ca >= count($opts)) {
                $ca = 0;
            }

            $d = strtolower((string) ($q['difficulty'] ?? 'beginner'));
            if (!in_array($d, ['beginner', 'intermediate', 'advanced'], true)) {
                $d = 'beginner';
            }

            $filtered[] = [
                'id' => (int) ($q['id'] ?? 0),
                'title' => (string) ($q['title'] ?? 'Question'),
                'question_text' => (string) ($q['question_text'] ?? ''),
                'difficulty' => $d,
                'options' => $opts,
                'correct_answer' => $ca,
            ];
        }

        if (count($filtered) > 1) {
            shuffle($filtered);
        }
        return array_values(array_slice($filtered, 0, min($count, count($filtered))));
    }
}
