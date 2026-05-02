<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseRepository.php';

class QuestionCollectionRepository extends BaseRepository
{
    private string $collectionsTable = 'question_collections';
    private string $linksTable = 'question_collection_items';

    public function __construct(?PDO $db = null)
    {
        parent::__construct($db);
        $this->ensureTables();
    }

    private function ensureTables(): void
    {
        try {
            $this->db->exec(
                "CREATE TABLE IF NOT EXISTS {$this->collectionsTable} (
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
            $this->db->exec(
                "CREATE TABLE IF NOT EXISTS {$this->linksTable} (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    collection_id INT NOT NULL,
                    question_bank_id INT NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    UNIQUE KEY uq_collection_question (collection_id, question_bank_id),
                    INDEX idx_collection_id (collection_id),
                    INDEX idx_question_id (question_bank_id),
                    FOREIGN KEY (collection_id) REFERENCES {$this->collectionsTable}(id) ON DELETE CASCADE,
                    FOREIGN KEY (question_bank_id) REFERENCES question_bank(id) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
            );
        } catch (Throwable $e) {
        }
    }

    public function getForTeacher(int $teacherId): array
    {
        $sql = "SELECT * FROM {$this->collectionsTable} WHERE created_by = ? ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([(int) $teacherId]);
        return $stmt->fetchAll();
    }

    public function create(int $teacherId, string $title)
    {
        $title = trim($title);
        if ($teacherId <= 0 || $title === '' || mb_strlen($title) > 255) {
            return false;
        }
        $stmt = $this->db->prepare("INSERT INTO {$this->collectionsTable} (title, created_by) VALUES (?, ?)");
        $ok = $stmt->execute([$title, (int) $teacherId]);
        return $ok ? (int) $this->db->lastInsertId() : false;
    }

    public function deleteOwned(int $collectionId, int $teacherId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->collectionsTable} WHERE id = ? AND created_by = ?");
        return $stmt->execute([(int) $collectionId, (int) $teacherId]);
    }

    public function addQuestion(int $collectionId, int $questionBankId, int $teacherId): bool
    {
        if (!$this->isOwnedByTeacher($collectionId, $teacherId)) {
            return false;
        }
        $stmt = $this->db->prepare("INSERT IGNORE INTO {$this->linksTable} (collection_id, question_bank_id) VALUES (?, ?)");
        return $stmt->execute([(int) $collectionId, (int) $questionBankId]);
    }

    public function removeQuestion(int $collectionId, int $questionBankId, int $teacherId): bool
    {
        if (!$this->isOwnedByTeacher($collectionId, $teacherId)) {
            return false;
        }
        $stmt = $this->db->prepare("DELETE FROM {$this->linksTable} WHERE collection_id = ? AND question_bank_id = ?");
        return $stmt->execute([(int) $collectionId, (int) $questionBankId]);
    }

    public function getQuestionIdsForCollection(int $collectionId, int $teacherId): array
    {
        if (!$this->isOwnedByTeacher($collectionId, $teacherId)) {
            return [];
        }
        $stmt = $this->db->prepare("SELECT question_bank_id FROM {$this->linksTable} WHERE collection_id = ?");
        $stmt->execute([(int) $collectionId]);
        $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return array_map('intval', is_array($ids) ? $ids : []);
    }

    private function isOwnedByTeacher(int $collectionId, int $teacherId): bool
    {
        $stmt = $this->db->prepare("SELECT id FROM {$this->collectionsTable} WHERE id = ? AND created_by = ? LIMIT 1");
        $stmt->execute([(int) $collectionId, (int) $teacherId]);
        return (bool) $stmt->fetch();
    }
}
