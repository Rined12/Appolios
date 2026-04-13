<?php

require_once __DIR__ . '/../core/Model.php';

class Quiz extends Model {
    protected $table = 'quizzes';

    /**
     * @param array<string,mixed> $post
     * @return list<array{question:string,options:list<string>,correctAnswer:int}>
     */
    /** Libellé français pour affichage étudiant */
    public static function difficultyLabelFr($code) {
        $map = [
            'beginner' => 'Débutant',
            'intermediate' => 'Intermédiaire',
            'advanced' => 'Avancé',
        ];
        $c = (string) $code;

        return $map[$c] ?? $c;
    }

    /**
     * Nombre de questions par quiz (pour listes étudiant).
     * @param list<int> $ids
     * @return array<int,int> id => count
     */
    public function getQuestionCountsForQuizIds(array $ids) {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));
        if ($ids === []) {
            return [];
        }
        $ph = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT id, questions_json FROM {$this->table} WHERE id IN ($ph)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($ids);
        $out = [];
        while ($row = $stmt->fetch()) {
            $d = json_decode($row['questions_json'] ?? '[]', true);
            $out[(int) $row['id']] = is_array($d) ? count($d) : 0;
        }

        return $out;
    }

    public static function normalizeQuestionsFromPost(array $post) {
        $raw = $post['questions'] ?? [];
        if (!is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $q) {
            if (!is_array($q)) {
                continue;
            }
            $text = isset($q['question']) ? trim((string) $q['question']) : '';
            $opts = $q['options'] ?? [];
            if (!is_array($opts)) {
                $opts = [];
            }
            $opts = array_values(array_filter(array_map(function ($o) {
                return trim((string) $o);
            }, $opts)));
            $ca = isset($q['correctAnswer']) ? (int) $q['correctAnswer'] : 0;
            if ($text === '' || count($opts) < 2) {
                continue;
            }
            if ($ca < 0 || $ca >= count($opts)) {
                $ca = 0;
            }
            $out[] = [
                'question' => $text,
                'options' => $opts,
                'correctAnswer' => $ca,
            ];
        }
        return $out;
    }

    public function findWithChapterCourse($id) {
        $sql = "SELECT q.*, ch.course_id, ch.title AS chapter_title, c.title AS course_title,
                       c.created_by AS course_owner_id
                FROM {$this->table} q
                JOIN chapters ch ON ch.id = q.chapter_id
                JOIN courses c ON c.id = ch.course_id
                WHERE q.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([(int) $id]);
        $row = $stmt->fetch();
        if ($row && !empty($row['questions_json'])) {
            $decoded = json_decode($row['questions_json'], true);
            $row['questions'] = is_array($decoded) ? $decoded : [];
        } elseif ($row) {
            $row['questions'] = [];
        }
        return $row;
    }

    public function getAllForTeacher($teacherId) {
        $sql = "SELECT q.*, ch.title AS chapter_title, ch.id AS chapter_id, c.title AS course_title, c.id AS course_id
                FROM {$this->table} q
                JOIN chapters ch ON ch.id = q.chapter_id
                JOIN courses c ON c.id = ch.course_id
                WHERE c.created_by = ?
                ORDER BY q.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([(int) $teacherId]);
        return $this->hydrateQuestionsList($stmt->fetchAll());
    }

    public function getAllForAdmin() {
        $sql = "SELECT q.*, ch.title AS chapter_title, c.title AS course_title, c.id AS course_id
                FROM {$this->table} q
                JOIN chapters ch ON ch.id = q.chapter_id
                JOIN courses c ON c.id = ch.course_id
                ORDER BY q.created_at DESC";
        $stmt = $this->db->query($sql);
        return $this->hydrateQuestionsList($stmt->fetchAll());
    }

    public function getByChapterId($chapterId) {
        $sql = "SELECT * FROM {$this->table} WHERE chapter_id = ? ORDER BY id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([(int) $chapterId]);
        return $this->hydrateQuestionsList($stmt->fetchAll());
    }

    /**
     * Quiz accessibles aux cours où l'étudiant est inscrit.
     * @return list<array<string,mixed>>
     */
    public function getForEnrolledStudent($userId) {
        $sql = "SELECT q.id, q.title, q.difficulty, q.tags, q.time_limit_sec, q.chapter_id,
                       ch.title AS chapter_title, c.id AS course_id, c.title AS course_title
                FROM {$this->table} q
                JOIN chapters ch ON ch.id = q.chapter_id
                JOIN courses c ON c.id = ch.course_id
                JOIN enrollments e ON e.course_id = c.id AND e.user_id = ?
                ORDER BY c.title ASC, ch.sort_order ASC, q.id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([(int) $userId]);
        $rows = $stmt->fetchAll();
        if ($rows !== []) {
            $counts = $this->getQuestionCountsForQuizIds(array_column($rows, 'id'));
            foreach ($rows as &$row) {
                $row['question_count'] = $counts[(int) $row['id']] ?? 0;
            }
            unset($row);
        }

        return $rows;
    }

    public function getChaptersForEnrolledStudent($userId) {
        $sql = "SELECT ch.*, c.title AS course_title, c.id AS course_id
                FROM chapters ch
                JOIN courses c ON c.id = ch.course_id
                JOIN enrollments e ON e.course_id = c.id AND e.user_id = ?
                ORDER BY c.title ASC, ch.sort_order ASC, ch.id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([(int) $userId]);
        return $stmt->fetchAll();
    }

    private function hydrateQuestionsList(array $rows) {
        foreach ($rows as &$row) {
            if (!empty($row['questions_json'])) {
                $d = json_decode($row['questions_json'], true);
                $row['questions'] = is_array($d) ? $d : [];
            } else {
                $row['questions'] = [];
            }
        }
        unset($row);
        return $rows;
    }

    public function create($data) {
        $sql = "INSERT INTO {$this->table}
            (chapter_id, title, difficulty, tags, time_limit_sec, questions_json, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $tags = isset($data['tags']) ? (string) $data['tags'] : null;
        $json = json_encode($data['questions'] ?? [], JSON_UNESCAPED_UNICODE);
        return $stmt->execute([
            (int) $data['chapter_id'],
            $data['title'],
            $data['difficulty'] ?? 'beginner',
            $tags,
            $data['time_limit_sec'] !== null && $data['time_limit_sec'] !== '' ? (int) $data['time_limit_sec'] : null,
            $json,
            (int) $data['created_by'],
        ]) ? (int) $this->db->lastInsertId() : false;
    }

    public function update($id, $data) {
        $sql = "UPDATE {$this->table} SET chapter_id = ?, title = ?, difficulty = ?, tags = ?, time_limit_sec = ?, questions_json = ?
                WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $tags = isset($data['tags']) ? (string) $data['tags'] : null;
        $json = json_encode($data['questions'] ?? [], JSON_UNESCAPED_UNICODE);
        return $stmt->execute([
            (int) $data['chapter_id'],
            $data['title'],
            $data['difficulty'] ?? 'beginner',
            $tags,
            $data['time_limit_sec'] !== null && $data['time_limit_sec'] !== '' ? (int) $data['time_limit_sec'] : null,
            $json,
            (int) $id,
        ]);
    }
}
