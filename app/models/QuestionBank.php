<?php

require_once __DIR__ . '/../core/Model.php';

class QuestionBank extends Model {
    protected $table = 'question_bank';

    public function getForTeacher($teacherId) {
        $sql = "SELECT * FROM {$this->table} WHERE created_by = ? ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([(int) $teacherId]);
        return $this->decodeOptionsRows($stmt->fetchAll());
    }

    public function getAllForAdmin() {
        $sql = "SELECT qb.*, u.name AS author_name
                FROM {$this->table} qb
                JOIN users u ON u.id = qb.created_by
                ORDER BY qb.created_at DESC";
        $stmt = $this->db->query($sql);
        return $this->decodeOptionsRows($stmt->fetchAll());
    }

    /** Banque en lecture pour étudiants (toutes les questions publiées par enseignants / admin). */
    public function getAllReadable() {
        $sql = "SELECT qb.*, u.name AS author_name
                FROM {$this->table} qb
                JOIN users u ON u.id = qb.created_by
                ORDER BY qb.created_at DESC";
        $stmt = $this->db->query($sql);
        return $this->decodeOptionsRows($stmt->fetchAll());
    }

    private function decodeOptionsRows(array $rows) {
        foreach ($rows as &$r) {
            $d = json_decode($r['options_json'] ?? '[]', true);
            $r['options'] = is_array($d) ? $d : [];
        }
        unset($r);
        return $rows;
    }

    public function findOwned($id, $userId) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? AND created_by = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([(int) $id, (int) $userId]);
        $row = $stmt->fetch();
        if ($row) {
            $d = json_decode($row['options_json'] ?? '[]', true);
            $row['options'] = is_array($d) ? $d : [];
        }
        return $row;
    }

    public function findByIdDecoded($id) {
        $row = $this->findById($id);
        if ($row) {
            $d = json_decode($row['options_json'] ?? '[]', true);
            $row['options'] = is_array($d) ? $d : [];
        }
        return $row;
    }

    /**
     * Ajoute au tableau quiz les questions banque sélectionnées (IDs).
     * @param list<array{question:string,options:list<string>,correctAnswer:int}> $baseQuestions
     * @param list<int|string> $bankIds
     * @param int|null $restrictToUserId null = pas de filtre (admin)
     * @return list<array{question:string,options:list<string>,correctAnswer:int}>
     */
    public function appendIdsToQuizQuestions(array $baseQuestions, array $bankIds, ?int $restrictToUserId) {
        $out = $baseQuestions;
        foreach ($bankIds as $bid) {
            $id = (int) $bid;
            if ($id <= 0) {
                continue;
            }
            $row = $this->findByIdDecoded($id);
            if (!$row) {
                continue;
            }
            if ($restrictToUserId !== null && (int) ($row['created_by'] ?? 0) !== $restrictToUserId) {
                continue;
            }
            $opts = $row['options'] ?? [];
            if (count($opts) < 2) {
                continue;
            }
            $ca = (int) ($row['correct_answer'] ?? 0);
            if ($ca < 0 || $ca >= count($opts)) {
                $ca = 0;
            }
            $out[] = [
                'question' => trim((string) ($row['question_text'] ?? '')),
                'options' => array_values($opts),
                'correctAnswer' => $ca,
            ];
        }
        return $out;
    }

    public function create($data) {
        $sql = "INSERT INTO {$this->table}
            (title, question_text, options_json, correct_answer, tags, difficulty, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $opts = json_encode($data['options'] ?? [], JSON_UNESCAPED_UNICODE);
        return $stmt->execute([
            $data['title'] ?? null,
            $data['question_text'],
            $opts,
            (int) ($data['correct_answer'] ?? 0),
            $data['tags'] ?? null,
            $data['difficulty'] ?? 'beginner',
            (int) $data['created_by'],
        ]) ? (int) $this->db->lastInsertId() : false;
    }

    public function update($id, $data) {
        $sql = "UPDATE {$this->table} SET title = ?, question_text = ?, options_json = ?, correct_answer = ?, tags = ?, difficulty = ?
                WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $opts = json_encode($data['options'] ?? [], JSON_UNESCAPED_UNICODE);
        return $stmt->execute([
            $data['title'] ?? null,
            $data['question_text'],
            $opts,
            (int) ($data['correct_answer'] ?? 0),
            $data['tags'] ?? null,
            $data['difficulty'] ?? 'beginner',
            (int) $id,
        ]);
    }
}
