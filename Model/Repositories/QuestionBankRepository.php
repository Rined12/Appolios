<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseRepository.php';

class QuestionBankRepository extends BaseRepository
{
    private string $table = 'question_bank';

    public function getForTeacher(int $teacherId): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE created_by = ? ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$teacherId]);
        return $this->decodeOptionsRows($stmt->fetchAll());
    }

    public function getAllForAdmin(): array
    {
        $sql = "SELECT qb.*, u.name AS author_name
                FROM {$this->table} qb
                JOIN users u ON u.id = qb.created_by
                ORDER BY qb.created_at DESC";
        $stmt = $this->db->query($sql);
        return $this->decodeOptionsRows($stmt ? $stmt->fetchAll() : []);
    }

    public function getAllReadable(): array
    {
        return $this->getAllForAdmin();
    }

    private function decodeOptionsRows(array $rows): array
    {
        foreach ($rows as &$r) {
            $d = json_decode($r['options_json'] ?? '[]', true);
            $r['options'] = is_array($d) ? $d : [];
        }
        unset($r);
        return $rows;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findOwned(int $id, int $userId): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? AND created_by = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id, $userId]);
        $row = $stmt->fetch();
        if ($row) {
            $d = json_decode($row['options_json'] ?? '[]', true);
            $row['options'] = is_array($d) ? $d : [];
        }
        return $row ?: null;
    }

    public function findByIdDecoded(int $id): ?array
    {
        $row = $this->findById($id);
        if ($row) {
            $d = json_decode($row['options_json'] ?? '[]', true);
            $row['options'] = is_array($d) ? $d : [];
        }
        return $row;
    }

    public function appendIdsToQuizQuestions(array $baseQuestions, array $bankIds, ?int $restrictToUserId): array
    {
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
                'question_bank_id' => $id,
                'question' => trim((string) ($row['question_text'] ?? '')),
                'options' => array_values($opts),
                'correctAnswer' => $ca,
            ];
        }
        return $out;
    }

    public function create(array $data)
    {
        $sql = "INSERT INTO {$this->table}
            (title, question_text, options_json, correct_answer, tags, difficulty, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $opts = json_encode($data['options'] ?? [], JSON_UNESCAPED_UNICODE);
        return $stmt->execute([
            $data['title'] ?? null,
            $data['question_text'] ?? '',
            $opts,
            (int) ($data['correct_answer'] ?? 0),
            $data['tags'] ?? null,
            $data['difficulty'] ?? 'beginner',
            (int) ($data['created_by'] ?? 0),
        ]) ? (int) $this->db->lastInsertId() : false;
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE {$this->table} SET title = ?, question_text = ?, options_json = ?, correct_answer = ?, tags = ?, difficulty = ?
                WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $opts = json_encode($data['options'] ?? [], JSON_UNESCAPED_UNICODE);
        return $stmt->execute([
            $data['title'] ?? null,
            $data['question_text'] ?? '',
            $opts,
            (int) ($data['correct_answer'] ?? 0),
            $data['tags'] ?? null,
            $data['difficulty'] ?? 'beginner',
            $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
