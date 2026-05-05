<?php

require_once __DIR__ . '/BaseController.php';

class QuestionController extends BaseController
{
    public function questions()
    {
        $role = (string) ($_SESSION['role'] ?? '');
        if ($role === 'admin') {
            $this->adminQuestions();
            return;
        }
        if ($role === 'teacher') {
            $this->teacherQuestions();
            return;
        }
        $this->setFlash('error', 'Accès refusé.');
        $this->redirect('login');
    }

    public function addQuestion()
    {
        $role = (string) ($_SESSION['role'] ?? '');
        if ($role === 'admin') {
            $this->adminAddQuestion();
            return;
        }
        if ($role === 'teacher') {
            $this->teacherAddQuestion();
            return;
        }
        $this->setFlash('error', 'Accès refusé.');
        $this->redirect('login');
    }

    public function storeQuestion()
    {
        $role = (string) ($_SESSION['role'] ?? '');
        if ($role === 'admin') {
            $this->adminStoreQuestion();
            return;
        }
        if ($role === 'teacher') {
            $this->teacherStoreQuestion();
            return;
        }
        $this->setFlash('error', 'Accès refusé.');
        $this->redirect('login');
    }

    public function editQuestion($id)
    {
        $role = (string) ($_SESSION['role'] ?? '');
        if ($role === 'admin') {
            $this->adminEditQuestion($id);
            return;
        }
        if ($role === 'teacher') {
            $this->teacherEditQuestion($id);
            return;
        }
        $this->setFlash('error', 'Accès refusé.');
        $this->redirect('login');
    }

    public function updateQuestion($id)
    {
        $role = (string) ($_SESSION['role'] ?? '');
        if ($role === 'admin') {
            $this->adminUpdateQuestion($id);
            return;
        }
        if ($role === 'teacher') {
            $this->teacherUpdateQuestion($id);
            return;
        }
        $this->setFlash('error', 'Accès refusé.');
        $this->redirect('login');
    }

    public function questionsBank()
    {
        if (!$this->requireStudentRole()) {
            return;
        }
        $this->view('FrontOffice/student/questions_bank', [
            'title' => 'Banque de questions - ' . APP_NAME,
            'questions' => $this->getAllReadableQuestions(),
            'flash' => $this->getFlash(),
        ]);
    }

    public function training()
    {
        $this->questionsBankDifficulty();
    }

    public function questionsBankDifficulty()
    {
        if (!$this->requireStudentRole()) {
            return;
        }

        $all = $this->getAllReadableQuestions();
        $difficulty = isset($_GET['difficulty']) ? strtolower(trim((string) $_GET['difficulty'])) : '';
        if (!in_array($difficulty, ['', 'beginner', 'intermediate', 'advanced'], true)) {
            $difficulty = '';
        }
        $count = isset($_GET['count']) ? (int) $_GET['count'] : 10;
        $count = max(5, min(30, $count));

        $list = [];
        foreach ($all as $q) {
            $opts = isset($q['options']) && is_array($q['options']) ? array_values($q['options']) : [];
            if (count($opts) < 2) {
                continue;
            }
            $d = strtolower((string) ($q['difficulty'] ?? 'beginner'));
            if (!in_array($d, ['beginner', 'intermediate', 'advanced'], true)) {
                $d = 'beginner';
            }
            if ($difficulty !== '' && $d !== $difficulty) {
                continue;
            }
            $ca = isset($q['correct_answer']) ? (int) $q['correct_answer'] : 0;
            if ($ca < 0 || $ca >= count($opts)) {
                $ca = 0;
            }
            $list[] = [
                'id' => (int) ($q['id'] ?? 0),
                'title' => (string) ($q['title'] ?? 'Question'),
                'question_text' => (string) ($q['question_text'] ?? ''),
                'difficulty' => $d,
                'options' => $opts,
                'correct_answer' => $ca,
            ];
        }
        if (count($list) > 1) {
            shuffle($list);
        }
        $list = array_values(array_slice($list, 0, min($count, count($list))));

        $this->view('FrontOffice/student/training', [
            'title' => 'Training Lab - ' . APP_NAME,
            'questions' => $list,
            'difficulty' => $difficulty,
            'count' => $count,
            'flash' => $this->getFlash(),
        ]);
    }

    public function createQuestionCollection()
    {
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
        $this->setFlash($id === false ? 'error' : 'success', $id === false ? 'Impossible de créer le pack.' : 'Pack créé.');
        $this->redirect('teacher/questions');
    }

    public function deleteQuestionCollection($id)
    {
        $this->requireTeacher();
        $ok = $this->deleteQuestionCollectionOwned((int) $id, (int) $_SESSION['user_id']);
        $this->setFlash($ok ? 'success' : 'error', $ok ? 'Pack supprimé.' : 'Impossible de supprimer.');
        $this->redirect('teacher/questions');
    }

    public function addQuestionToCollection($collectionId, $questionId)
    {
        $this->requireTeacher();
        $cid = (int) $collectionId;
        $qid = (int) $questionId;
        $ok = $this->addQuestionToCollectionOwned($cid, $qid, (int) $_SESSION['user_id']);
        $this->setFlash($ok ? 'success' : 'error', $ok ? 'Question ajoutée au pack.' : 'Impossible d\'ajouter la question.');
        $this->redirect('teacher/questions?collection_id=' . $cid);
    }

    public function removeQuestionFromCollection($collectionId, $questionId)
    {
        $this->requireTeacher();
        $cid = (int) $collectionId;
        $qid = (int) $questionId;
        $ok = $this->removeQuestionFromCollectionOwned($cid, $qid, (int) $_SESSION['user_id']);
        $this->setFlash($ok ? 'success' : 'error', $ok ? 'Question retirée du pack.' : 'Impossible de retirer la question.');
        $this->redirect('teacher/questions?collection_id=' . $cid);
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

    private function normalizeDifficulty($raw): string
    {
        $s = is_string($raw) ? trim($raw) : '';
        return in_array($s, ['beginner', 'intermediate', 'advanced'], true) ? $s : 'beginner';
    }

    private function validateQuestionBankFields(string $title, string $questionText, array $opts, int $correct, string $tags): array
    {
        $errors = [];
        if (mb_strlen(trim($questionText)) < 12) {
            $errors[] = 'Énoncé trop court.';
        }
        $opts = array_values(array_filter(array_map('trim', array_map('strval', $opts)), static fn($v) => $v !== ''));
        if (count($opts) < 2) {
            $errors[] = 'Ajoutez au moins 2 options.';
        }
        if ($correct < 0 || $correct >= count($opts)) {
            $errors[] = 'Réponse correcte invalide.';
        }
        if (mb_strlen(trim($tags)) < 2) {
            $errors[] = 'Tags requis.';
        }
        if (mb_strlen($title) > 255) {
            $errors[] = 'Titre trop long.';
        }
        return $errors;
    }

    private function getAllReadableQuestions(): array
    {
        $db = $this->db();
        $sql = "SELECT qb.*, u.name AS author_name
                FROM question_bank qb
                JOIN users u ON u.id = qb.created_by
                ORDER BY qb.created_at DESC";
        $stmt = $db->query($sql);
        $rows = $stmt ? $stmt->fetchAll() : [];
        foreach ($rows as &$r) {
            $d = json_decode($r['options_json'] ?? '[]', true);
            $r['options'] = is_array($d) ? $d : [];
        }
        unset($r);
        return $rows;
    }

    private function getQuestionBankForTeacher(int $teacherId): array
    {
        $db = $this->db();
        $stmt = $db->prepare("SELECT qb.*
                              FROM question_bank qb
                              WHERE qb.created_by = ?
                              ORDER BY qb.created_at DESC");
        $stmt->execute([(int) $teacherId]);
        $rows = $stmt->fetchAll();
        foreach ($rows as &$r) {
            $d = json_decode($r['options_json'] ?? '[]', true);
            $r['options'] = is_array($d) ? $d : [];
        }
        unset($r);
        return $rows;
    }

    private function getQuestionCollectionsForTeacher(int $teacherId): array
    {
        $db = $this->db();
        $st = $db->prepare('SELECT * FROM question_collections WHERE created_by = ? ORDER BY created_at DESC');
        $st->execute([(int) $teacherId]);
        return $st->fetchAll();
    }

    private function getQuestionIdsForCollection(int $collectionId, int $teacherId): array
    {
        $db = $this->db();
        $sql = 'SELECT cq.question_id
                FROM collection_questions cq
                JOIN question_collections qc ON qc.id = cq.collection_id
                WHERE cq.collection_id = ? AND qc.created_by = ?';
        $st = $db->prepare($sql);
        $st->execute([(int) $collectionId, (int) $teacherId]);
        $ids = $st->fetchAll(PDO::FETCH_COLUMN);
        return array_map('intval', is_array($ids) ? $ids : []);
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
        $st = $db->prepare($sql);
        $st->execute([(int) $teacherId]);
        $rows = $st->fetchAll();
        $out = [];
        foreach ($rows as $r) {
            $id = (int) ($r['question_bank_id'] ?? 0);
            if ($id <= 0) continue;
            $out[$id] = [
                'quizzes' => (int) ($r['quizzes_count'] ?? 0),
                'attempts' => (int) ($r['attempts_count'] ?? 0),
                'avg' => (float) ($r['avg_percentage'] ?? 0),
                'last_attempt_at' => isset($r['last_attempt_at']) ? (string) $r['last_attempt_at'] : null,
            ];
        }
        return $out;
    }

    private function teacherQuestions(): void
    {
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

        $this->view('FrontOffice/teacher/questions_bank', [
            'title' => 'Banque de questions - ' . APP_NAME,
            'questions' => $questions,
            'questionUsage' => $questionUsage,
            'qbTopStats' => $qbTopStats,
            'questionQa' => [],
            'charts' => [
                'difficulty' => $diffDist,
            ],
            'collections' => $collections,
            'selectedCollectionId' => $selectedCollectionId,
            'collectionSelectedMap' => $selectedMap,
            'flash' => $this->getFlash(),
        ]);
    }

    private function createQuestionCollectionRow(int $teacherId, string $title)
    {
        $db = $this->db();
        $st = $db->prepare('INSERT INTO question_collections (title, created_by) VALUES (?, ?)');
        $ok = $st->execute([(string) $title, (int) $teacherId]);
        return $ok ? (int) $db->lastInsertId() : false;
    }

    private function deleteQuestionCollectionOwned(int $collectionId, int $teacherId): bool
    {
        $db = $this->db();
        $db->prepare('DELETE cq FROM collection_questions cq JOIN question_collections qc ON qc.id=cq.collection_id WHERE cq.collection_id=? AND qc.created_by=?')
            ->execute([(int) $collectionId, (int) $teacherId]);
        $st = $db->prepare('DELETE FROM question_collections WHERE id = ? AND created_by = ?');
        return $st->execute([(int) $collectionId, (int) $teacherId]);
    }

    private function addQuestionToCollectionOwned(int $collectionId, int $questionId, int $teacherId): bool
    {
        $db = $this->db();
        $st = $db->prepare('SELECT id FROM question_collections WHERE id = ? AND created_by = ? LIMIT 1');
        $st->execute([(int) $collectionId, (int) $teacherId]);
        if (!$st->fetch()) {
            return false;
        }
        $ins = $db->prepare('INSERT IGNORE INTO collection_questions (collection_id, question_id) VALUES (?, ?)');
        return $ins->execute([(int) $collectionId, (int) $questionId]);
    }

    private function removeQuestionFromCollectionOwned(int $collectionId, int $questionId, int $teacherId): bool
    {
        $db = $this->db();
        $st = $db->prepare('DELETE cq FROM collection_questions cq JOIN question_collections qc ON qc.id=cq.collection_id WHERE cq.collection_id=? AND cq.question_id=? AND qc.created_by=?');
        return $st->execute([(int) $collectionId, (int) $questionId, (int) $teacherId]);
    }

    private function teacherAddQuestion(): void
    {
        $this->requireTeacher();
        $this->view('FrontOffice/teacher/question_form', [
            'title' => 'Nouvelle question - ' . APP_NAME,
            'question' => null,
            'flash' => $this->getFlash(),
        ]);
    }

    private function teacherStoreQuestion(): void
    {
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
        $this->createQuestionBankQuestion((int) $_SESSION['user_id'], $title !== '' ? $title : null, $questionText, $opts, $correct, $tags !== '' ? $tags : null, $difficulty);
        $this->setFlash('success', 'Question enregistrée.');
        $this->redirect('teacher/questions');
    }

    private function teacherEditQuestion($id): void
    {
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

    private function teacherUpdateQuestion($id): void
    {
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
        $this->updateQuestionBankOwned((int) $id, (int) $_SESSION['user_id'], $title !== '' ? $title : null, $questionText, $opts, $correct, $tags !== '' ? $tags : null, $difficulty);
        $this->setFlash('success', 'Question mise à jour.');
        $this->redirect('teacher/questions');
    }

    private function findQuestionBankOwned(int $questionId, int $teacherId): ?array
    {
        $db = $this->db();
        $stmt = $db->prepare('SELECT * FROM question_bank WHERE id = ? AND created_by = ? LIMIT 1');
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
        $check = $db->prepare('SELECT id FROM question_bank WHERE id = ? AND created_by = ? LIMIT 1');
        $check->execute([(int) $questionId, (int) $teacherId]);
        if (!$check->fetch()) {
            return false;
        }
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

    private function adminQuestions(): void
    {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        $questions = $this->getAllQuestionBankForAdmin();
        $usage = $this->getQuestionBankUsageStatsMapForAdmin();

        $top = [
            'questions_total' => count($questions),
            'used_questions' => 0,
            'attempts_total' => 0,
            'avg_percentage' => 0.0,
        ];
        $wSum = 0.0;
        foreach ($usage as $id => $u) {
            $qz = (int) ($u['quizzes'] ?? 0);
            $att = (int) ($u['attempts'] ?? 0);
            $avg = (float) ($u['avg'] ?? 0);
            if ($qz > 0) {
                $top['used_questions']++;
            }
            $top['attempts_total'] += $att;
            $wSum += ($avg * $att);
        }
        if ($top['attempts_total'] > 0) {
            $top['avg_percentage'] = round($wSum / (float) $top['attempts_total'], 1);
        }

        $this->view('BackOffice/admin/questions_bank', [
            'title' => 'Banque de questions (admin) - ' . APP_NAME,
            'questions' => $questions,
            'qbTopStats' => $top,
            'questionQa' => [],
            'charts' => [],
            'flash' => $this->getFlash(),
        ]);
    }

    private function adminAddQuestion(): void
    {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        $this->view('BackOffice/admin/question_form', [
            'title' => 'Nouvelle question - ' . APP_NAME,
            'question' => null,
            'flash' => $this->getFlash(),
        ]);
    }

    private function adminStoreQuestion(): void
    {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/add-question');
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
            $this->redirect('admin/add-question');
            return;
        }
        $this->createQuestionBankQuestion((int) $_SESSION['user_id'], $title !== '' ? $title : null, $questionText, $opts, $correct, $tags !== '' ? $tags : null, $difficulty);
        $this->setFlash('success', 'Question enregistrée.');
        $this->redirect('admin/questions');
    }

    private function adminEditQuestion($id): void
    {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        $row = $this->findQuestionByIdDecoded((int) $id);
        if (!$row) {
            $this->setFlash('error', 'Question introuvable.');
            $this->redirect('admin/questions');
            return;
        }
        $this->view('BackOffice/admin/question_form', [
            'title' => 'Modifier la question - ' . APP_NAME,
            'question' => $row,
            'flash' => $this->getFlash(),
        ]);
    }

    private function adminUpdateQuestion($id): void
    {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/questions');
            return;
        }
        $existing = $this->findQuestionByIdDecoded((int) $id);
        if (!$existing) {
            $this->setFlash('error', 'Question introuvable.');
            $this->redirect('admin/questions');
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
            $this->redirect('admin/edit-question/' . (int) $id);
            return;
        }
        $this->updateQuestionBank((int) $id, $title !== '' ? $title : null, $questionText, $opts, $correct, $tags !== '' ? $tags : null, $difficulty);
        $this->setFlash('success', 'Question mise à jour.');
        $this->redirect('admin/questions');
    }

    private function getAllQuestionBankForAdmin(): array
    {
        $db = $this->db();
        $sql = "SELECT qb.*, u.name AS author_name
                FROM question_bank qb
                JOIN users u ON u.id = qb.created_by
                ORDER BY qb.created_at DESC";
        $stmt = $db->query($sql);
        $rows = $stmt ? $stmt->fetchAll() : [];
        foreach ($rows as &$r) {
            $d = json_decode($r['options_json'] ?? '[]', true);
            $r['options'] = is_array($d) ? $d : [];
        }
        unset($r);
        return $rows;
    }

    private function getQuestionBankUsageStatsMapForAdmin(): array
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
                GROUP BY qb.id";
        $stmt = $db->query($sql);
        $rows = $stmt ? $stmt->fetchAll() : [];
        $out = [];
        foreach ($rows as $r) {
            $id = (int) ($r['question_bank_id'] ?? 0);
            if ($id <= 0) continue;
            $out[$id] = [
                'quizzes' => (int) ($r['quizzes_count'] ?? 0),
                'attempts' => (int) ($r['attempts_count'] ?? 0),
                'avg' => (float) ($r['avg_percentage'] ?? 0),
                'last_attempt_at' => isset($r['last_attempt_at']) ? (string) $r['last_attempt_at'] : null,
            ];
        }
        return $out;
    }

    private function createQuestionBankQuestion(int $adminId, ?string $title, string $questionText, array $options, int $correctAnswer, ?string $tags, string $difficulty)
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
            (int) $adminId,
        ]);
        return $ok ? (int) $db->lastInsertId() : false;
    }

    private function findQuestionByIdDecoded(int $questionId): ?array
    {
        $db = $this->db();
        $stmt = $db->prepare('SELECT * FROM question_bank WHERE id = ? LIMIT 1');
        $stmt->execute([(int) $questionId]);
        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }
        $d = json_decode($row['options_json'] ?? '[]', true);
        $row['options'] = is_array($d) ? $d : [];
        return $row;
    }

    private function updateQuestionBank(int $questionId, ?string $title, string $questionText, array $options, int $correctAnswer, ?string $tags, string $difficulty): bool
    {
        $db = $this->db();
        $sql = "UPDATE question_bank
                SET title = ?, question_text = ?, options_json = ?, correct_answer = ?, tags = ?, difficulty = ?, updated_at = CURRENT_TIMESTAMP
                WHERE id = ?";
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
        ]);
    }
}
