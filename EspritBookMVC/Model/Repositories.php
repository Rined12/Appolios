<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
/**
 * Shared PDO persistence base for repositories (not a domain entity).
 */
abstract class BaseRepository
{
    abstract protected function tableName(): string;

    protected function openConnection(): PDO
    {
        return getConnection();
    }

    public function findById($id)
    {
        $stmt = $this->openConnection()->prepare("SELECT * FROM {$this->tableName()} WHERE id = ?");
        $stmt->execute([$id]);

        return $stmt->fetch();
    }

    public function findAll(): array
    {
        $stmt = $this->openConnection()->query("SELECT * FROM {$this->tableName()}");

        return $stmt->fetchAll();
    }

    public function delete($id): bool
    {
        $stmt = $this->openConnection()->prepare("DELETE FROM {$this->tableName()} WHERE id = ?");

        return $stmt->execute([$id]);
    }

    public function count(): int
    {
        $stmt = $this->openConnection()->query("SELECT COUNT(*) as count FROM {$this->tableName()}");
        $result = $stmt->fetch();

        return (int) ($result['count'] ?? 0);
    }
}

/**
 * APPOLIOS - Contact Message Model
 * Handles contact us messages storage and retrieval
 */

class ContactMessageRepository extends BaseRepository {
    protected function tableName(): string
    {
        return 'contact_messages';
    }

    /**
     * Create a new contact message
     * @param array $data
     * @return bool|int
     */
    public function createMessage($data) {
        $sql = "INSERT INTO {$this->tableName()} (name, email, subject, message)
                VALUES (?, ?, ?, ?)";

        try {
            $stmt = $this->openConnection()->prepare($sql);
            $stmt->execute([
                $data['name'],
                $data['email'],
                $data['subject'],
                $data['message']
            ]);

            return $this->openConnection()->lastInsertId();
        } catch (PDOException $e) {
            error_log("ContactMessage::createMessage error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all messages with pagination
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function fetchAllMessages($limit = 50, $offset = 0) {
        $sql = "SELECT cm.*, u.name AS reader_name
                FROM {$this->tableName()} cm
                LEFT JOIN users u ON cm.read_by = u.id
                ORDER BY cm.created_at DESC
                LIMIT ? OFFSET ?";
        try {
            $stmt = $this->openConnection()->prepare($sql);
            $stmt->execute([$limit, $offset]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get unread messages count
     * @return int
     */
    public function countUnreadMessages() {
        $sql = "SELECT COUNT(*) FROM {$this->tableName()} WHERE is_read = 0";
        try {
            $stmt = $this->openConnection()->query($sql);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Get single message by ID
     * @param int $id
     * @return array|false
     */
    public function fetchMessageRow($id) {
        $sql = "SELECT cm.*, u.name AS reader_name
                FROM {$this->tableName()} cm
                LEFT JOIN users u ON cm.read_by = u.id
                WHERE cm.id = ?";
        try {
            $stmt = $this->openConnection()->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Mark message as read
     * @param int $id
     * @param int $adminId
     * @return bool
     */
    public function markAsRead($id, $adminId) {
        $sql = "UPDATE {$this->tableName()} 
                SET is_read = 1, read_by = ?, read_at = NOW() 
                WHERE id = ?";

        try {
            $stmt = $this->openConnection()->prepare($sql);
            return $stmt->execute([$adminId, $id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Mark message as unread
     * @param int $id
     * @return bool
     */
    public function markAsUnread($id) {
        $sql = "UPDATE {$this->tableName()} 
                SET is_read = 0, read_by = NULL, read_at = NULL 
                WHERE id = ?";

        try {
            $stmt = $this->openConnection()->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Delete message
     * @param int $id
     * @return bool
     */
    public function delete($id): bool {
        $sql = "DELETE FROM {$this->tableName()} WHERE id = ?";
        try {
            $stmt = $this->openConnection()->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            return false;
        }
    }
}

/**
 * APPOLIOS Course Model
 * Handles course-related database operations
 */

class CourseRepository extends BaseRepository {
    protected function tableName(): string
    {
        return 'courses';
    }

    
    /**
     * Get all courses with creator info
     * @return array
     */
    public function fetchAllWithCreator() {
        $sql = "SELECT c.*, u.name as creator_name
                FROM {$this->tableName()} c
                JOIN users u ON c.created_by = u.id
                ORDER BY c.created_at DESC";

        $stmt = $this->openConnection()->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Create a new course
     * @param array $data
     * @return int|false
     */
    public function create($data) {
        $sql = "INSERT INTO {$this->tableName()} (title, description, video_url, created_by, created_at)
                VALUES (?, ?, ?, ?, NOW())";

        try {
            $stmt = $this->openConnection()->prepare($sql);
            $stmt->execute([
                $data['title'],
                $data['description'],
                $data['video_url'],
                $data['created_by']
            ]);

            return $this->openConnection()->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Update course
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $sql = "UPDATE {$this->tableName()} SET title = ?, description = ?, video_url = ? WHERE id = ?";

        $stmt = $this->openConnection()->prepare($sql);
        return $stmt->execute([
            $data['title'],
            $data['description'],
            $data['video_url'],
            $id
        ]);
    }

    /**
     * Get courses by creator
     * @param int $userId
     * @return array
     */
    public function fetchByCreator($userId) {
        $sql = "SELECT * FROM {$this->tableName()} WHERE created_by = ? ORDER BY created_at DESC";
        $stmt = $this->openConnection()->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /**
     * Get course with creator info
     * @param int $id
     * @return array|null
     */
    public function fetchWithCreator($id) {
        $sql = "SELECT c.*, u.name as creator_name
                FROM {$this->tableName()} c
                JOIN users u ON c.created_by = u.id
                WHERE c.id = ?";

        $stmt = $this->openConnection()->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Get courses by teacher (alias for getByCreator)
     * @param int $teacherId
     * @return array
     */
    public function fetchCoursesByTeacher($teacherId) {
        return $this->fetchByCreator($teacherId);
    }

    /**
     * Count unique students enrolled in teacher's courses
     * @param int $teacherId
     * @return int
     */
    public function countStudentsByTeacher($teacherId) {
        $sql = "SELECT COUNT(DISTINCT e.user_id) as count 
                FROM enrollments e 
                JOIN courses c ON e.course_id = c.id 
                WHERE c.created_by = ?";
        $stmt = $this->openConnection()->prepare($sql);
        $stmt->execute([$teacherId]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }

    /**
     * Count active enrollments for teacher's courses
     * @param int $teacherId
     * @return int
     */
    public function countActiveEnrollmentsByTeacher($teacherId) {
        $sql = "SELECT COUNT(*) as count 
                FROM enrollments e 
                JOIN courses c ON e.course_id = c.id 
                WHERE c.created_by = ? AND e.progress < 100";
        $stmt = $this->openConnection()->prepare($sql);
        $stmt->execute([$teacherId]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }

    /**
     * Get enrolled students for a course
     * @param int $courseId
     * @return array
     */
    public function fetchEnrolledStudents($courseId) {
        $sql = "SELECT u.id, u.name, u.email, e.progress, e.enrolled_at
                FROM enrollments e
                JOIN users u ON e.user_id = u.id
                WHERE e.course_id = ?
                ORDER BY e.enrolled_at DESC";
        $stmt = $this->openConnection()->prepare($sql);
        $stmt->execute([$courseId]);
        return $stmt->fetchAll();
    }

    /**
     * Find course by ID
     * @param int $id
     * @return array|null
     */
    public function findById($id) {
        $sql = "SELECT * FROM {$this->tableName()} WHERE id = ?";
        $stmt = $this->openConnection()->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Delete course
     * @param int $id
     * @return bool
     */
    public function delete($id): bool {
        $sql = "DELETE FROM {$this->tableName()} WHERE id = ?";
        $stmt = $this->openConnection()->prepare($sql);
        return $stmt->execute([$id]);
    }
}

/**
 * Persistence layer for discussions (PDO / SQL only â€” no HTML).
 */
class DiscussionRepository extends BaseRepository
{
    protected function tableName(): string
    {
        return 'discussion';
    }

    /** @return string[] */
    private function discussionColumnNames(bool $reset = false): array
    {
        static $cached = null;
        if ($reset) {
            $cached = null;
        }
        if ($cached !== null) {
            return $cached;
        }

        $cached = [];
        try {
            $stmt = $this->openConnection()->query("SHOW COLUMNS FROM {$this->tableName()}");
            foreach ($stmt->fetchAll() as $row) {
                $cached[] = (string) ($row['Field'] ?? '');
            }
        } catch (PDOException $e) {
            $cached = [];
        }

        return $cached;
    }

    private function hasColumn(string $name): bool
    {
        return in_array($name, $this->discussionColumnNames(), true);
    }

    private function idCol(): string
    {
        return $this->hasColumn('id_discussion') ? 'id_discussion' : 'id';
    }

    private function groupCol(): string
    {
        return $this->hasColumn('id_groupe') ? 'id_groupe' : 'group_id';
    }

    private function authorCol(): string
    {
        return $this->hasColumn('id_auteur') ? 'id_auteur' : 'created_by';
    }

    private function dateCol(): string
    {
        return $this->hasColumn('date_creation') ? 'date_creation' : 'created_at';
    }

    private function titleCol(): string
    {
        return $this->hasColumn('titre') ? 'titre' : 'title';
    }

    private function contentCol(): string
    {
        return $this->hasColumn('contenu') ? 'contenu' : 'content';
    }

    private function approvalCol(): string
    {
        if ($this->hasColumn('approval_statut')) {
            return 'approval_statut';
        }
        if ($this->hasColumn('approval_status')) {
            return 'approval_status';
        }
        return '';
    }

    private function ensureApprovalSchema(): void
    {
        if ($this->approvalCol() !== '') {
            return;
        }
        try {
            $this->openConnection()->exec(
                "ALTER TABLE {$this->tableName()} ADD COLUMN approval_statut VARCHAR(32) NOT NULL DEFAULT 'approuve'"
            );
            $this->discussionColumnNames(true);
        } catch (Throwable $e) {
        }
    }

    public function fetchByGroupForViewer(int $groupId, int $viewerUserId, int $groupCreatorId): array
    {
        $this->ensureApprovalSchema();
        $approvalCol = $this->approvalCol();
        if ($approvalCol === '') {
            return $this->fetchByGroup($groupId);
        }

        $groupCol = $this->groupCol();
        $authorCol = $this->authorCol();
        $dateCol = $this->dateCol();
        $titleCol = $this->titleCol();
        $contentCol = $this->contentCol();

        try {
            $stmt = $this->openConnection()->prepare(
                "SELECT d.*, d.{$titleCol} AS titre, d.{$contentCol} AS contenu, u.name AS auteur_name
                 FROM {$this->tableName()} d
                 LEFT JOIN users u ON u.id = d.{$authorCol}
                 WHERE d.{$groupCol} = ?
                 ORDER BY d.{$dateCol} DESC"
            );
            $stmt->execute([$groupId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function fetchByGroup(int $groupId): array
    {
        $groupCol = $this->groupCol();
        $authorCol = $this->authorCol();
        $dateCol = $this->dateCol();
        $titleCol = $this->titleCol();
        $contentCol = $this->contentCol();

        try {
            $stmt = $this->openConnection()->prepare(
                "SELECT d.*, d.{$titleCol} AS titre, d.{$contentCol} AS contenu, u.name AS auteur_name
                 FROM {$this->tableName()} d
                 LEFT JOIN users u ON u.id = d.{$authorCol}
                 WHERE d.{$groupCol} = ?
                 ORDER BY d.{$dateCol} DESC"
            );
            $stmt->execute([$groupId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function fetchByAuthor(int $authorId): array
    {
        $this->ensureApprovalSchema();
        $idCol = $this->idCol();
        $groupCol = $this->groupCol();
        $authorCol = $this->authorCol();
        $dateCol = $this->dateCol();
        $titleCol = $this->titleCol();
        $contentCol = $this->contentCol();

        try {
            $stmt = $this->openConnection()->prepare(
                "SELECT d.*, d.{$idCol} AS id_discussion, d.{$titleCol} AS titre, d.{$contentCol} AS contenu, g.nom_groupe
                 FROM {$this->tableName()} d
                 LEFT JOIN groupe g ON g.id_groupe = d.{$groupCol}
                 WHERE d.{$authorCol} = ?
                 ORDER BY d.{$dateCol} DESC"
            );
            $stmt->execute([$authorId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function fetchVisibleForUser(int $userId): array
    {
        $this->ensureApprovalSchema();
        $idCol = $this->idCol();
        $groupCol = $this->groupCol();
        $authorCol = $this->authorCol();
        $dateCol = $this->dateCol();
        $titleCol = $this->titleCol();
        $contentCol = $this->contentCol();
        $approvalCol = $this->approvalCol();

        try {
            if ($approvalCol !== '') {
                $stmt = $this->openConnection()->prepare(
                    "SELECT d.*,
                            d.{$idCol} AS id_discussion,
                            d.{$titleCol} AS titre,
                            d.{$contentCol} AS contenu,
                            d.{$authorCol} AS id_auteur,
                            d.{$approvalCol} AS approval_statut,
                            g.nom_groupe
                     FROM {$this->tableName()} d
                     INNER JOIN groupe_user gu ON gu.id_groupe = d.{$groupCol}
                     LEFT JOIN groupe g ON g.id_groupe = d.{$groupCol}
                     WHERE gu.id_user = ?
                     ORDER BY d.{$dateCol} DESC"
                );
                $stmt->execute([$userId]);
                return $stmt->fetchAll();
            }

            $stmt = $this->openConnection()->prepare(
                "SELECT d.*,
                        d.{$idCol} AS id_discussion,
                        d.{$titleCol} AS titre,
                        d.{$contentCol} AS contenu,
                        d.{$authorCol} AS id_auteur,
                        g.nom_groupe
                 FROM {$this->tableName()} d
                 INNER JOIN groupe_user gu ON gu.id_groupe = d.{$groupCol}
                 LEFT JOIN groupe g ON g.id_groupe = d.{$groupCol}
                 WHERE gu.id_user = ?
                 ORDER BY d.{$dateCol} DESC"
            );
            $stmt->execute([$userId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function findOwnedBy(int $discussionId, int $authorId): ?array
    {
        $idCol = $this->idCol();
        $groupCol = $this->groupCol();
        $authorCol = $this->authorCol();
        $dateCol = $this->dateCol();
        $titleCol = $this->titleCol();
        $contentCol = $this->contentCol();

        try {
            $stmt = $this->openConnection()->prepare(
                "SELECT d.*, d.{$idCol} AS id_discussion, d.{$titleCol} AS titre, d.{$contentCol} AS contenu, d.{$groupCol} AS id_groupe, d.{$dateCol} AS date_creation
                 FROM {$this->tableName()} d
                 WHERE d.{$idCol} = ? AND d.{$authorCol} = ?
                 LIMIT 1"
            );
            $stmt->execute([$discussionId, $authorId]);
            $row = $stmt->fetch();
            return $row ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    public function createForGroup(int $groupId, int $authorId, string $title, string $content, ?string $initialApproval = null): bool
    {
        $this->ensureApprovalSchema();
        $groupCol = $this->groupCol();
        $authorCol = $this->authorCol();
        $dateCol = $this->dateCol();
        $titleCol = $this->titleCol();
        $contentCol = $this->contentCol();
        $approvalCol = $this->approvalCol();

        try {
            if ($approvalCol !== '') {
                $approvalValue = 'approuve';
                if ($initialApproval !== null && in_array($initialApproval, ['en_cours', 'approuve', 'rejete'], true)) {
                    $approvalValue = $initialApproval;
                }
                $stmt = $this->openConnection()->prepare(
                    "INSERT INTO {$this->tableName()} ({$titleCol}, {$contentCol}, {$dateCol}, {$groupCol}, {$authorCol}, {$approvalCol})
                     VALUES (?, ?, NOW(), ?, ?, ?)"
                );
                return $stmt->execute([$title, $content, $groupId, $authorId, $approvalValue]);
            }
            $stmt = $this->openConnection()->prepare(
                "INSERT INTO {$this->tableName()} ({$titleCol}, {$contentCol}, {$dateCol}, {$groupCol}, {$authorCol})
                 VALUES (?, ?, NOW(), ?, ?)"
            );
            return $stmt->execute([$title, $content, $groupId, $authorId]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function updateOwned(int $discussionId, int $authorId, string $title, string $content, int $groupId): bool
    {
        $this->ensureApprovalSchema();
        $idCol = $this->idCol();
        $groupCol = $this->groupCol();
        $authorCol = $this->authorCol();
        $titleCol = $this->titleCol();
        $contentCol = $this->contentCol();
        $approvalCol = $this->approvalCol();

        try {
            if ($approvalCol !== '') {
                $stmt = $this->openConnection()->prepare(
                    "UPDATE {$this->tableName()}
                     SET {$titleCol} = ?, {$contentCol} = ?, {$groupCol} = ?
                     WHERE {$idCol} = ? AND {$authorCol} = ?"
                );
                return $stmt->execute([$title, $content, $groupId, $discussionId, $authorId]);
            }
            $stmt = $this->openConnection()->prepare(
                "UPDATE {$this->tableName()}
                 SET {$titleCol} = ?, {$contentCol} = ?, {$groupCol} = ?
                 WHERE {$idCol} = ? AND {$authorCol} = ?"
            );
            return $stmt->execute([$title, $content, $groupId, $discussionId, $authorId]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function fetchAllForAdmin(int $limit = 200): array
    {
        $this->ensureApprovalSchema();
        $groupCol = $this->groupCol();
        $authorCol = $this->authorCol();
        $dateCol = $this->dateCol();

        try {
            $stmt = $this->openConnection()->prepare(
                "SELECT d.*, g.nom_groupe, u.name AS auteur_name
                 FROM {$this->tableName()} d
                 LEFT JOIN groupe g ON g.id_groupe = d.{$groupCol}
                 LEFT JOIN users u ON u.id = d.{$authorCol}
                 ORDER BY d.{$dateCol} DESC
                 LIMIT ?"
            );
            $stmt->bindValue(1, $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function updateApprovalStatus(int $discussionId, string $status): bool
    {
        if (!in_array($status, ['approuve', 'rejete'], true)) {
            return false;
        }
        $this->ensureApprovalSchema();
        $col = $this->approvalCol();
        if ($col === '') {
            return false;
        }
        $idCol = $this->idCol();
        try {
            $stmt = $this->openConnection()->prepare(
                "UPDATE {$this->tableName()} SET {$col} = ? WHERE {$idCol} = ?"
            );
            return $stmt->execute([$status, $discussionId]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function fetchRowByPk(int $discussionId): ?array
    {
        $idCol = $this->idCol();
        try {
            $stmt = $this->openConnection()->prepare("SELECT * FROM {$this->tableName()} WHERE {$idCol} = ? LIMIT 1");
            $stmt->execute([$discussionId]);
            $row = $stmt->fetch();
            return $row ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    public function deleteByPrimaryKey(int $discussionId): bool
    {
        $idCol = $this->idCol();
        try {
            $stmt = $this->openConnection()->prepare("DELETE FROM {$this->tableName()} WHERE {$idCol} = ?");
            return $stmt->execute([$discussionId]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function deleteAllForGroup(int $groupId): bool
    {
        $groupCol = $this->groupCol();
        try {
            $stmt = $this->openConnection()->prepare("DELETE FROM {$this->tableName()} WHERE {$groupCol} = ?");
            return $stmt->execute([$groupId]);
        } catch (PDOException $e) {
            return false;
        }
    }
}

/**
 * Simple wall posts on a group (member-authored updates, separate from threaded discussions).
 */
class GroupPostRepository extends BaseRepository
{
    private bool $schemaEnsured = false;

    protected function tableName(): string
    {
        return 'groupe_post';
    }

    public function ensureSchema(): void
    {
        if ($this->schemaEnsured) {
            return;
        }
        try {
            $this->openConnection()->exec(
                "CREATE TABLE IF NOT EXISTS groupe_post (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    id_groupe INT NOT NULL,
                    id_user INT NOT NULL,
                    body TEXT NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_groupe_created (id_groupe, created_at),
                    INDEX idx_user (id_user)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
            );
        } catch (Throwable $e) {
        }
        $this->schemaEnsured = true;
    }

    public function create(int $groupId, int $userId, string $body): bool
    {
        $this->ensureSchema();
        try {
            $stmt = $this->openConnection()->prepare(
                "INSERT INTO {$this->tableName()} (id_groupe, id_user, body) VALUES (?, ?, ?)"
            );

            return $stmt->execute([$groupId, $userId, $body]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function fetchByGroup(int $groupId, int $limit = 80): array
    {
        $this->ensureSchema();
        $limit = max(1, min(200, $limit));
        try {
            $stmt = $this->openConnection()->prepare(
                "SELECT p.id, p.id_groupe, p.id_user, p.body, p.created_at, u.name AS author_name
                 FROM {$this->tableName()} p
                 INNER JOIN users u ON u.id = p.id_user
                 WHERE p.id_groupe = ?
                 ORDER BY p.created_at DESC
                 LIMIT {$limit}"
            );
            $stmt->execute([$groupId]);

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function fetchPostById(int $postId): ?array
    {
        $this->ensureSchema();
        try {
            $stmt = $this->openConnection()->prepare(
                "SELECT p.*, u.name AS author_name FROM {$this->tableName()} p
                 INNER JOIN users u ON u.id = p.id_user
                 WHERE p.id = ? LIMIT 1"
            );
            $stmt->execute([$postId]);
            $row = $stmt->fetch();

            return $row ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    public function deleteByIdForGroup(int $postId, int $groupId): bool
    {
        $this->ensureSchema();
        try {
            $stmt = $this->openConnection()->prepare(
                "DELETE FROM {$this->tableName()} WHERE id = ? AND id_groupe = ? LIMIT 1"
            );

            return $stmt->execute([$postId, $groupId]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function deleteAllForGroup(int $groupId): bool
    {
        $this->ensureSchema();
        try {
            $stmt = $this->openConnection()->prepare("DELETE FROM {$this->tableName()} WHERE id_groupe = ?");

            return $stmt->execute([$groupId]);
        } catch (PDOException $e) {
            return false;
        }
    }
}

/**
 * APPOLIOS Enrollment Model
 * Handles student course enrollments
 */

class EnrollmentRepository extends BaseRepository {
    protected function tableName(): string
    {
        return 'enrollments';
    }

    
    /**
     * Enroll student in course
     * @param int $userId
     * @param int $courseId
     * @return bool
     */
    public function enroll($userId, $courseId) {
        $sql = "INSERT INTO {$this->tableName()} (user_id, course_id, enrolled_at, progress) VALUES (?, ?, NOW(), 0)";

        try {
            $stmt = $this->openConnection()->prepare($sql);
            return $stmt->execute([$userId, $courseId]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Check if user is enrolled
     * @param int $userId
     * @param int $courseId
     * @return bool
     */
    public function isEnrolled($userId, $courseId) {
        $sql = "SELECT * FROM {$this->tableName()} WHERE user_id = ? AND course_id = ?";
        $stmt = $this->openConnection()->prepare($sql);
        $stmt->execute([$userId, $courseId]);
        return $stmt->fetch() !== false;
    }

    /**
     * Get user's enrolled courses
     * @param int $userId
     * @return array
     */
    public function fetchEnrollmentsForUser($userId) {
        $sql = "SELECT e.*, c.title, c.description, c.video_url
                FROM {$this->tableName()} e
                JOIN courses c ON e.course_id = c.id
                WHERE e.user_id = ?
                ORDER BY e.enrolled_at DESC";

        $stmt = $this->openConnection()->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /**
     * Update progress
     * @param int $userId
     * @param int $courseId
     * @param int $progress
     * @return bool
     */
    public function updateProgress($userId, $courseId, $progress) {
        $sql = "UPDATE {$this->tableName()} SET progress = ? WHERE user_id = ? AND course_id = ?";
        $stmt = $this->openConnection()->prepare($sql);
        return $stmt->execute([$progress, $userId, $courseId]);
    }

    /**
     * Count total enrollments
     * @return int
     */
    public function countAll() {
        $sql = "SELECT COUNT(*) as count FROM {$this->tableName()}";
        $stmt = $this->openConnection()->query($sql);
        $result = $stmt->fetch();
        return $result['count'];
    }
}

/**
 * Persistence for evenements. Domain fields live in EvenementEntity; rows stay arrays here.
 */
class EvenementRepository extends BaseRepository
{
    protected function tableName(): string
    {
        return 'evenements';
    }

    public function countAll(): int
    {
        return (int) $this->openConnection()->query('SELECT COUNT(*) FROM evenements')->fetchColumn();
    }

    public function findRecent(int $limit): array
    {
        $st = $this->openConnection()->prepare(
            'SELECT * FROM evenements
             ORDER BY COALESCE(CONCAT(date_debut,\' \',heure_debut), event_date) ASC
             LIMIT ?'
        );
        $st->bindValue(1, $limit, PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll();
    }

    public function findAllWithCreatorAndResourceCount(): array
    {
        return $this->openConnection()->query(
            'SELECT e.*, u.name as creator_name, u.role as creator_role, COUNT(r.id) as resource_count
             FROM evenements e
             JOIN users u ON e.created_by = u.id
             LEFT JOIN evenement_ressources r ON r.evenement_id = e.id
             GROUP BY e.id
             ORDER BY COALESCE(CONCAT(e.date_debut,\' \',e.heure_debut), e.event_date) ASC'
        )->fetchAll();
    }

    public function findById($id)
    {
        $st = $this->openConnection()->prepare('SELECT * FROM evenements WHERE id = ? LIMIT 1');
        $st->execute([(int) $id]);
        return $st->fetch();
    }

    /**
     * @param array<string, mixed> $d
     */
    public function create(array $d): int|false
    {
        try {
            $st = $this->openConnection()->prepare(
                'INSERT INTO evenements
                 (title,titre,description,date_debut,date_fin,heure_debut,heure_fin,
                  lieu,capacite_max,type,statut,approval_status,location,event_date,created_by,created_at)
                 VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW())'
            );
            $st->execute([
                $d['title'],
                $d['titre'],
                $d['description'],
                $d['date_debut'],
                $d['date_fin'],
                $d['heure_debut'],
                $d['heure_fin'],
                $d['lieu'],
                $d['capacite_max'],
                $d['type'],
                $d['statut'],
                $d['approval_status'] ?? 'approved',
                $d['location'],
                $d['event_date'],
                $d['created_by'],
            ]);

            return (int) $this->openConnection()->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * @param array<string, mixed> $d
     */
    public function update(int $id, array $d): bool
    {
        $st = $this->openConnection()->prepare(
            'UPDATE evenements
             SET title=?,titre=?,description=?,date_debut=?,date_fin=?,
                 heure_debut=?,heure_fin=?,lieu=?,capacite_max=?,type=?,
                 statut=?,location=?,event_date=?,updated_at=CURRENT_TIMESTAMP
             WHERE id=?'
        );

        return $st->execute([
            $d['title'],
            $d['titre'],
            $d['description'],
            $d['date_debut'],
            $d['date_fin'],
            $d['heure_debut'],
            $d['heure_fin'],
            $d['lieu'],
            $d['capacite_max'],
            $d['type'],
            $d['statut'],
            $d['location'],
            $d['event_date'],
            $id,
        ]);
    }

    public function delete($id): bool
    {
        $st = $this->openConnection()->prepare('DELETE FROM evenements WHERE id=?');

        return $st->execute([(int) $id]);
    }

    public function findPendingTeacherRequests(): array
    {
        return $this->openConnection()->query(
            'SELECT e.*,u.name as creator_name,u.email as creator_email
             FROM evenements e JOIN users u ON u.id=e.created_by
             WHERE e.approval_status=\'pending\' AND u.role=\'teacher\'
             ORDER BY e.created_at DESC'
        )->fetchAll();
    }

    public function findRejectedTeacherRequests(): array
    {
        return $this->openConnection()->query(
            'SELECT e.*,u.name as creator_name,u.email as creator_email
             FROM evenements e JOIN users u ON u.id=e.created_by
             WHERE e.approval_status=\'rejected\' AND u.role=\'teacher\'
             ORDER BY e.updated_at DESC'
        )->fetchAll();
    }

    public function updateApproval(int $id, string $status, ?int $adminId, ?string $reason): bool
    {
        $s = strtolower($status) === 'approved' ? 'approved' : 'rejected';
        $st = $this->openConnection()->prepare(
            'UPDATE evenements
             SET approval_status=?,approved_by=?,approved_at=NOW(),
                 rejection_reason=?,updated_at=CURRENT_TIMESTAMP
             WHERE id=?'
        );

        return $st->execute([$s, $adminId, $s === 'rejected' ? $reason : null, $id]);
    }

    public function findByCreator(int $userId): array
    {
        $st = $this->openConnection()->prepare(
            'SELECT e.*, COUNT(r.id) as resource_count
             FROM evenements e
             LEFT JOIN evenement_ressources r ON r.evenement_id = e.id
             WHERE e.created_by = ?
             GROUP BY e.id
             ORDER BY COALESCE(CONCAT(e.date_debut,\' \',e.heure_debut), e.event_date) ASC'
        );
        $st->execute([$userId]);

        return $st->fetchAll();
    }

    public function findByIdAndCreator(int $id, int $userId): array|false
    {
        $st = $this->openConnection()->prepare(
            'SELECT * FROM evenements WHERE id = ? AND created_by = ? LIMIT 1'
        );
        $st->execute([$id, $userId]);

        return $st->fetch();
    }

    public function markNonPendingAsPending(int $id): void
    {
        $st = $this->openConnection()->prepare(
            'UPDATE evenements SET approval_status=\'pending\', updated_at=CURRENT_TIMESTAMP
             WHERE id=? AND approval_status != \'pending\''
        );
        $st->execute([$id]);
    }

    public function findApprovedWithCreators(): array
    {
        return $this->openConnection()->query(
            'SELECT e.*, u.name as creator_name
             FROM evenements e
             JOIN users u ON e.created_by = u.id
             WHERE e.approval_status = \'approved\'
             ORDER BY COALESCE(CONCAT(e.date_debut,\' \',e.heure_debut), e.event_date) ASC'
        )->fetchAll();
    }

    public function findWithCreatorById(int $id): array|false
    {
        $st = $this->openConnection()->prepare(
            'SELECT e.*, u.name as creator_name, u.role as creator_role
             FROM evenements e
             JOIN users u ON u.id = e.created_by
             WHERE e.id = ? LIMIT 1'
        );
        $st->execute([$id]);

        return $st->fetch();
    }

    public function findApprovedById(int $id): array|false
    {
        $st = $this->openConnection()->prepare(
            'SELECT * FROM evenements WHERE id = ? AND approval_status = \'approved\' LIMIT 1'
        );
        $st->execute([$id]);

        return $st->fetch();
    }

    public function isCreatedBy(int $eventId, int $userId): bool
    {
        $st = $this->openConnection()->prepare(
            'SELECT id FROM evenements WHERE id = ? AND created_by = ? LIMIT 1'
        );
        $st->execute([$eventId, $userId]);

        return (bool) $st->fetch();
    }
}

/**
 * Persistence for evenement_ressources (rules, materiel, plans, participation rows).
 */
class EvenementRessourceRepository extends BaseRepository
{
    protected function tableName(): string
    {
        return 'evenement_ressources';
    }

    public function ressourcesTableExists(): bool
    {
        static $cached = null;
        if ($cached !== null) {
            return $cached;
        }
        try {
            $st = $this->openConnection()->query("SHOW TABLES LIKE 'evenement_ressources'");
            $cached = $st->fetch() !== false;
        } catch (PDOException $e) {
            $cached = false;
        }

        return $cached;
    }

    public function findById($id)
    {
        $st = $this->openConnection()->prepare('SELECT * FROM evenement_ressources WHERE id = ? LIMIT 1');
        $st->execute([(int) $id]);

        return $st->fetch();
    }

    /**
     * Admin / teacher list: includes evenement title.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findByTypeAndEvent(string $type, int $evenementId): array
    {
        $st = $this->openConnection()->prepare(
            'SELECT r.*, u.name as creator_name, e.title as evenement_title
             FROM evenement_ressources r
             JOIN users u ON r.created_by = u.id
             JOIN evenements e ON r.evenement_id = e.id
             WHERE r.type = ? AND r.evenement_id = ?
             ORDER BY r.created_at DESC'
        );
        $st->execute([$type, $evenementId]);

        return $st->fetchAll();
    }

    /**
     * Student view: no join on event title column.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findByTypeAndEventForStudent(string $type, int $evenementId): array
    {
        if (!$this->ressourcesTableExists()) {
            return [];
        }
        $st = $this->openConnection()->prepare(
            'SELECT r.*, u.name as creator_name
             FROM evenement_ressources r
             JOIN users u ON r.created_by = u.id
             WHERE r.type = ? AND r.evenement_id = ?
             ORDER BY r.created_at DESC'
        );
        $st->execute([$type, $evenementId]);

        return $st->fetchAll();
    }

    public function existsInScope(int $id, int $evenementId, string $type): bool
    {
        $st = $this->openConnection()->prepare(
            'SELECT id FROM evenement_ressources WHERE id=? AND evenement_id=? AND type=? LIMIT 1'
        );
        $st->execute([$id, $evenementId, $type]);

        return (bool) $st->fetch();
    }

    /**
     * @param array{evenement_id:int,type:string,title:string,details:string,created_by:int} $d
     */
    public function create(array $d): int|false
    {
        try {
            $st = $this->openConnection()->prepare(
                'INSERT INTO evenement_ressources (evenement_id,type,title,details,created_by,created_at)
                 VALUES (?,?,?,?,?,NOW())'
            );
            $st->execute([
                $d['evenement_id'],
                $d['type'],
                $d['title'],
                $d['details'],
                $d['created_by'],
            ]);

            return (int) $this->openConnection()->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * @param array{title:string,details:string,evenement_id:int} $d
     */
    public function update(int $id, array $d): bool
    {
        $st = $this->openConnection()->prepare(
            'UPDATE evenement_ressources SET title=?,details=?,updated_at=CURRENT_TIMESTAMP
             WHERE id=? AND evenement_id=?'
        );

        return $st->execute([$d['title'], $d['details'], $id, $d['evenement_id']]);
    }

    public function delete($id, $evenementId = null): bool
    {
        if ($evenementId === null) {
            $st = $this->openConnection()->prepare('DELETE FROM evenement_ressources WHERE id=?');
            return $st->execute([(int) $id]);
        }
        $st = $this->openConnection()->prepare('DELETE FROM evenement_ressources WHERE id=? AND evenement_id=?');
        return $st->execute([(int) $id, (int) $evenementId]);
    }

    /**
     * Group rule / materiel / plan rows by type for approval UI.
     *
     * @return array{rule: array<int, array{title: string, details: string}>, materiel: array<int, array{title: string, details: string}>, plan: array<int, array{title: string, details: string}>}
     */
    public function fetchGroupedPublicRessources(int $evenementId): array
    {
        $st = $this->openConnection()->prepare(
            'SELECT type, title, details FROM evenement_ressources
             WHERE evenement_id = ? AND type IN (\'rule\',\'materiel\',\'plan\')
             ORDER BY type, created_at ASC'
        );
        $st->execute([$evenementId]);
        $rows = $st->fetchAll();
        $grouped = ['rule' => [], 'materiel' => [], 'plan' => []];
        foreach ($rows as $r) {
            $t = $r['type'];
            if (!isset($grouped[$t])) {
                continue;
            }
            $grouped[$t][] = ['title' => $r['title'], 'details' => $r['details'] ?? ''];
        }

        return $grouped;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findParticipationsByEvent(int $eventId): array
    {
        $st = $this->openConnection()->prepare(
            'SELECT r.id, r.evenement_id, r.created_by as student_id,
                    r.title as student_name, r.details as status, r.created_at,
                    (SELECT u.email FROM users u WHERE u.id = r.created_by LIMIT 1) as student_email
             FROM evenement_ressources r
             WHERE r.evenement_id = ? AND r.type = \'participation\'
             ORDER BY r.created_at DESC'
        );
        $st->execute([$eventId]);

        return $st->fetchAll();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findParticipationsByCreator(int $teacherId): array
    {
        $st = $this->openConnection()->prepare(
            'SELECT r.id, r.evenement_id, r.created_by as student_id,
                    r.title as student_name, r.details as status, r.created_at,
                    e.title as event_title, e.date_debut, e.heure_debut,
                    u.name as student_name_full, u.email as student_email
             FROM evenement_ressources r
             JOIN evenements e ON r.evenement_id = e.id
             JOIN users u ON r.created_by = u.id
             WHERE r.type = \'participation\' AND e.created_by = ?
             ORDER BY r.created_at DESC'
        );
        $st->execute([$teacherId]);

        return $st->fetchAll();
    }

    public function findParticipationById(int $id): array|false
    {
        $st = $this->openConnection()->prepare(
            'SELECT * FROM evenement_ressources WHERE id = ? AND type = \'participation\' LIMIT 1'
        );
        $st->execute([$id]);

        return $st->fetch();
    }

    /**
     * Admin UI: normalize status to approved/rejected/pending.
     */
    public function updateParticipationStatusAdmin(int $id, string $status, ?string $reason = null): bool
    {
        $s = in_array($status, ['approved', 'rejected'], true) ? $status : 'pending';
        $st = $this->openConnection()->prepare(
            'UPDATE evenement_ressources
             SET details = ?, rejection_reason = ?, updated_at = CURRENT_TIMESTAMP
             WHERE id = ? AND type = \'participation\''
        );

        return $st->execute([$s, $reason, $id]);
    }

    /**
     * Teacher UI: stores raw status in details.
     */
    public function updateParticipationStatusTeacher(int $id, string $status, ?string $reason = null): bool
    {
        $st = $this->openConnection()->prepare(
            'UPDATE evenement_ressources
             SET details = ?, rejection_reason = ?, updated_at = CURRENT_TIMESTAMP
             WHERE id = ? AND type = \'participation\''
        );

        return $st->execute([$status, $reason, $id]);
    }

    public function deleteParticipationForEvent(int $id, int $evenementId): int
    {
        $st = $this->openConnection()->prepare(
            'DELETE FROM evenement_ressources WHERE id = ? AND evenement_id = ? AND type = \'participation\''
        );
        $st->execute([(int) $id, $evenementId]);

        return $st->rowCount();
    }

    public function deleteParticipationById(int $id): int
    {
        $st = $this->openConnection()->prepare(
            'DELETE FROM evenement_ressources WHERE id = ? AND type = \'participation\''
        );
        $st->execute([(int) $id]);

        return $st->rowCount();
    }

    /**
     * @return array<int, int|string>
     */
    public function fetchParticipationMapForStudent(int $studentId): array
    {
        if (!$this->ressourcesTableExists()) {
            return [];
        }
        $st = $this->openConnection()->prepare(
            'SELECT evenement_id, details as status
             FROM evenement_ressources
             WHERE type = \'participation\' AND created_by = ?'
        );
        $st->execute([$studentId]);
        $map = [];
        foreach ($st->fetchAll() as $row) {
            $map[(int) $row['evenement_id']] = $row['status'];
        }

        return $map;
    }

    public function findStudentParticipation(int $eventId, int $studentId): array|false
    {
        if (!$this->ressourcesTableExists()) {
            return false;
        }
        $st = $this->openConnection()->prepare(
            'SELECT * FROM evenement_ressources
             WHERE evenement_id = ? AND created_by = ? AND type = \'participation\' LIMIT 1'
        );
        $st->execute([$eventId, $studentId]);

        return $st->fetch();
    }

    public function createPendingParticipation(int $eventId, int $studentId, string $studentName): bool
    {
        if (!$this->ressourcesTableExists()) {
            return false;
        }
        try {
            $st = $this->openConnection()->prepare(
                'INSERT INTO evenement_ressources (evenement_id, type, title, details, created_by, created_at)
                 VALUES (?, \'participation\', ?, \'pending\', ?, NOW())'
            );

            return $st->execute([$eventId, $studentName, $studentId]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function cancelPendingParticipation(int $eventId, int $studentId): bool
    {
        if (!$this->ressourcesTableExists()) {
            return false;
        }
        $st = $this->openConnection()->prepare(
            'DELETE FROM evenement_ressources
             WHERE evenement_id = ? AND created_by = ? AND type = \'participation\' AND details = \'pending\''
        );

        return $st->execute([$eventId, $studentId]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findMyParticipations(int $studentId): array
    {
        if (!$this->ressourcesTableExists()) {
            return [];
        }
        $st = $this->openConnection()->prepare(
            'SELECT e.*, er.id as p_id, er.details as p_status, er.rejection_reason, er.created_at as p_date, er.updated_at as p_update_date, u.name as creator_name
             FROM evenements e
             JOIN evenement_ressources er ON e.id = er.evenement_id
             JOIN users u ON e.created_by = u.id
             WHERE er.type = \'participation\' AND er.created_by = ?
             ORDER BY er.created_at DESC'
        );
        $st->execute([$studentId]);

        return $st->fetchAll();
    }

    public function findApprovedTicketForStudent(int $participationId, int $studentId): array|false
    {
        if (!$this->ressourcesTableExists()) {
            return false;
        }
        $st = $this->openConnection()->prepare(
            'SELECT er.*, e.title as event_title, e.location as event_location,
                    COALESCE(CONCAT(e.date_debut, \' \', e.heure_debut), e.event_date) as event_full_date,
                    u.name as student_name, u.email as student_email
             FROM evenement_ressources er
             JOIN evenements e ON er.evenement_id = e.id
             JOIN users u ON er.created_by = u.id
             WHERE er.id = ? AND er.created_by = ? AND er.type = \'participation\' AND er.details = \'approved\'
             LIMIT 1'
        );
        $st->execute([$participationId, $studentId]);

        return $st->fetch();
    }
}

class GroupeRepository extends BaseRepository
{
    protected function tableName(): string
    {
        return 'groupe';
    }

    /** @return string[] */
    private function groupeColumnNames(bool $reset = false): array
    {
        static $cached = null;
        if ($reset) {
            $cached = null;
        }
        if ($cached !== null) {
            return $cached;
        }

        $stmt = $this->openConnection()->query("SHOW COLUMNS FROM {$this->tableName()}");
        $cols = [];
        foreach ($stmt->fetchAll() as $row) {
            $cols[] = (string) ($row['Field'] ?? '');
        }
        $cached = $cols;

        return $cached;
    }

    private function hasColumn(string $name): bool
    {
        return in_array($name, $this->groupeColumnNames(), true);
    }

    private function approvalColumn(): string
    {
        if ($this->hasColumn('approval_statut')) {
            return 'approval_statut';
        }
        if ($this->hasColumn('approval_status')) {
            return 'approval_status';
        }
        return '';
    }

    private function ensureApprovalSchema(): void
    {
        if ($this->approvalColumn() !== '') {
            return;
        }
        try {
            $this->openConnection()->exec(
                "ALTER TABLE {$this->tableName()} ADD COLUMN approval_statut VARCHAR(32) NOT NULL DEFAULT 'en_cours'"
            );
            $this->groupeColumnNames(true);
        } catch (Throwable $e) {
        }
    }

    private function statutColumn(): string
    {
        if ($this->hasColumn('statut')) {
            return 'statut';
        }
        if ($this->hasColumn('status')) {
            return 'status';
        }
        return '';
    }

    private function creatorColumn(): string
    {
        if ($this->hasColumn('id_createur')) {
            return 'id_createur';
        }
        if ($this->hasColumn('created_by')) {
            return 'created_by';
        }
        return 'id_createur';
    }

    private function imageColumn(): string
    {
        foreach (['image_url', 'photo', 'image'] as $c) {
            if ($this->hasColumn($c)) {
                return $c;
            }
        }
        return '';
    }

    private function ensureImageSchema(): void
    {
        if ($this->imageColumn() !== '') {
            return;
        }
        try {
            $this->openConnection()->exec('ALTER TABLE groupe ADD COLUMN image_url VARCHAR(500) NULL DEFAULT NULL');
            $this->groupeColumnNames(true);
        } catch (Throwable $e) {
        }
    }

    public function supportsStoredImage(): bool
    {
        $this->ensureImageSchema();
        return $this->imageColumn() !== '';
    }

    public function findById($id)
    {
        $this->ensureApprovalSchema();
        $approvalCol = $this->approvalColumn();
        $statutCol = $this->statutColumn();
        $creatorCol = $this->creatorColumn();
        $approvalSelect = $approvalCol !== '' ? "g.{$approvalCol} AS approval_statut" : "'en_cours' AS approval_statut";
        $statutSelect = $statutCol !== '' ? "g.{$statutCol} AS statut" : "'actif' AS statut";

        $stmt = $this->openConnection()->prepare(
            "SELECT
                g.*,
                {$approvalSelect},
                {$statutSelect},
                u.name AS createur_name
             FROM groupe g
             LEFT JOIN users u ON u.id = g.{$creatorCol}
             WHERE g.id_groupe = ?"
        );
        $stmt->execute([(int) $id]);
        return $stmt->fetch();
    }

    public function fetchAllWithCreator($limit = 20, $offset = 0): array
    {
        $this->ensureApprovalSchema();
        $approvalCol = $this->approvalColumn();
        $statutCol = $this->statutColumn();
        $creatorCol = $this->creatorColumn();
        $approvalSelect = $approvalCol !== '' ? "g.{$approvalCol} AS approval_statut" : "'en_cours' AS approval_statut";
        $statutSelect = $statutCol !== '' ? "g.{$statutCol} AS statut" : "'actif' AS statut";

        $stmt = $this->openConnection()->prepare(
            "SELECT
                g.*,
                {$approvalSelect},
                {$statutSelect},
                u.name AS createur_name
             FROM groupe g
             LEFT JOIN users u ON u.id = g.{$creatorCol}
             ORDER BY g.date_creation DESC
             LIMIT ? OFFSET ?"
        );
        $stmt->bindValue(1, (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue(2, (int) $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function fetchAllWithCreatorPublic($limit = 20, $offset = 0): array
    {
        $this->ensureApprovalSchema();
        $approvalCol = $this->approvalColumn();
        $statutCol = $this->statutColumn();
        $creatorCol = $this->creatorColumn();
        $approvalSelect = $approvalCol !== '' ? "g.{$approvalCol} AS approval_statut" : "'en_cours' AS approval_statut";
        $statutSelect = $statutCol !== '' ? "g.{$statutCol} AS statut" : "'actif' AS statut";
        if ($approvalCol === '') {
            return [];
        }
        $approvalWhere = "g.{$approvalCol} = 'approuve'";

        $stmt = $this->openConnection()->prepare(
            "SELECT
                g.*,
                {$approvalSelect},
                {$statutSelect},
                u.name AS createur_name
             FROM groupe g
             LEFT JOIN users u ON u.id = g.{$creatorCol}
             WHERE {$approvalWhere}
             ORDER BY g.date_creation DESC
             LIMIT ? OFFSET ?"
        );
        $stmt->bindValue(1, (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue(2, (int) $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function create(array $data)
    {
        $this->ensureImageSchema();
        $this->ensureApprovalSchema();
        $approvalCol = $this->approvalColumn();
        $statutCol = $this->statutColumn();
        $creatorCol = $this->creatorColumn();
        $imgCol = $this->imageColumn();

        $columns = ['nom_groupe', 'description', 'date_creation', $creatorCol];
        $values = ['?', '?', 'NOW()', '?'];
        $params = [
            $data['nom_groupe'],
            $data['description'],
            (int) $data['id_createur'],
        ];

        if ($statutCol !== '') {
            $columns[] = $statutCol;
            $values[] = '?';
            $params[] = $data['statut'];
        }

        if ($approvalCol !== '') {
            $columns[] = $approvalCol;
            $values[] = '?';
            $params[] = $data['approval_statut'];
        }

        if ($imgCol !== '' && !empty($data['image_url'])) {
            $columns[] = $imgCol;
            $values[] = '?';
            $params[] = $data['image_url'];
        }

        $stmt = $this->openConnection()->prepare(
            "INSERT INTO groupe (" . implode(', ', $columns) . ")
             VALUES (" . implode(', ', $values) . ")"
        );
        $ok = $stmt->execute($params);
        if (!$ok) {
            return false;
        }

        return (int) $this->openConnection()->lastInsertId();
    }

    public function updateGroupe(int $idGroupe, array $data): bool
    {
        $this->ensureImageSchema();
        $this->ensureApprovalSchema();
        $approvalCol = $this->approvalColumn();
        $statutCol = $this->statutColumn();
        $imgCol = $this->imageColumn();
        $setParts = ["nom_groupe = ?", "description = ?"];
        $params = [$data['nom_groupe'], $data['description']];
        if ($statutCol !== '') {
            $setParts[] = "{$statutCol} = ?";
            $params[] = $data['statut'];
        }
        if ($approvalCol !== '') {
            $setParts[] = "{$approvalCol} = ?";
            $params[] = $data['approval_statut'];
        }
        if ($imgCol !== '' && array_key_exists('image_url', $data)) {
            $setParts[] = "{$imgCol} = ?";
            $params[] = $data['image_url'];
        }
        $params[] = $idGroupe;

        $stmt = $this->openConnection()->prepare(
            "UPDATE groupe
             SET " . implode(', ', $setParts) . "
             WHERE id_groupe = ?"
        );
        return $stmt->execute($params);
    }

    public function delete($id): bool
    {
        $stmt = $this->openConnection()->prepare("DELETE FROM groupe WHERE id_groupe = ?");
        return $stmt->execute([(int) $id]);
    }

    public function deleteMembresForGroup(int $idGroupe): bool
    {
        $stmt = $this->openConnection()->prepare('DELETE FROM groupe_user WHERE id_groupe = ?');
        return $stmt->execute([(int) $idGroupe]);
    }

    public function estMembre($id_groupe, $id_user): bool
    {
        $stmt = $this->openConnection()->prepare("SELECT 1 FROM groupe_user WHERE id_groupe = ? AND id_user = ?");
        $stmt->execute([(int) $id_groupe, (int) $id_user]);
        return (bool) $stmt->fetchColumn();
    }

    public function ajouterMembre($id_groupe, $id_user, $role = 'membre'): bool
    {
        $stmt = $this->openConnection()->prepare(
            "INSERT INTO groupe_user (id_groupe, id_user, role, date_adhesion)
             VALUES (?, ?, ?, NOW())"
        );
        return $stmt->execute([(int) $id_groupe, (int) $id_user, $role]);
    }

    public function retirerMembre($id_groupe, $id_user): bool
    {
        $stmt = $this->openConnection()->prepare("DELETE FROM groupe_user WHERE id_groupe = ? AND id_user = ?");
        return $stmt->execute([(int) $id_groupe, (int) $id_user]);
    }

    public function fetchMembres($id_groupe): array
    {
        $stmt = $this->openConnection()->prepare(
            "SELECT gu.*, u.name, u.email
             FROM groupe_user gu
             JOIN users u ON u.id = gu.id_user
             WHERE gu.id_groupe = ?
             ORDER BY gu.date_adhesion DESC"
        );
        $stmt->execute([(int) $id_groupe]);
        return $stmt->fetchAll();
    }

    public function fetchByCreator(int $idCreateur): array
    {
        $this->ensureApprovalSchema();
        $creatorCol = $this->creatorColumn();
        $stmt = $this->openConnection()->prepare(
            "SELECT g.*, u.name AS createur_name
             FROM groupe g
             LEFT JOIN users u ON u.id = g.{$creatorCol}
             WHERE g.{$creatorCol} = ?
             ORDER BY g.date_creation DESC"
        );
        $stmt->execute([$idCreateur]);
        return $stmt->fetchAll();
    }

    /**
     * Approved groups the user can host discussions in: member of the group or group creator.
     *
     * @return array<int, array<string, mixed>>
     */
    public function fetchApprovedGroupsWhereUserCanParticipate(int $userId): array
    {
        $this->ensureApprovalSchema();
        $approvalCol = $this->approvalColumn();
        $creatorCol = $this->creatorColumn();
        $statutCol = $this->statutColumn();
        if ($approvalCol === '') {
            return [];
        }
        $approvalSelect = "g.{$approvalCol} AS approval_statut";
        $statutSelect = $statutCol !== '' ? "g.{$statutCol} AS statut" : "'actif' AS statut";
        $uid = (int) $userId;
        try {
            $stmt = $this->openConnection()->prepare(
                "SELECT DISTINCT g.*, {$approvalSelect}, {$statutSelect}, u.name AS createur_name
                 FROM groupe g
                 LEFT JOIN users u ON u.id = g.{$creatorCol}
                 LEFT JOIN groupe_user gu ON gu.id_groupe = g.id_groupe AND gu.id_user = ?
                 WHERE g.{$approvalCol} = 'approuve'
                   AND (g.{$creatorCol} = ? OR gu.id_user IS NOT NULL)
                 ORDER BY g.date_creation DESC"
            );
            $stmt->execute([$uid, $uid]);

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
}

/**
 * APPOLIOS Teacher Application Model
 * Handles teacher registration requests with CV
 */

class TeacherApplicationRepository extends BaseRepository {
    protected function tableName(): string
    {
        return 'teacher_applications';
    }

    /**
     * Create a new teacher application
     * @param array $data
     * @return int|false
     */
    public function createApplication($data) {
        $sql = "INSERT INTO {$this->tableName()} (name, email, password, cv_filename, cv_path, status) 
                VALUES (?, ?, ?, ?, ?, 'pending')";

        try {
            $stmt = $this->openConnection()->prepare($sql);
            $stmt->execute([
                $data['name'],
                $data['email'],
                $data['password'], // Store plain password (will be hashed when account is created)
                $data['cv_filename'],
                $data['cv_path']
            ]);

            return $this->openConnection()->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Get all pending applications
     * @return array
     */
    public function fetchPendingApplications() {
        $sql = "SELECT * FROM v_pending_teachers ORDER BY created_at DESC";
        try {
            $stmt = $this->openConnection()->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get application by ID
     * @param int $id
     * @return array|null
     */
    public function fetchApplicationRow($id) {
        $sql = "SELECT * FROM {$this->tableName()} WHERE id = ?";
        try {
            $stmt = $this->openConnection()->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Approve teacher application
     * @param int $id
     * @param int $adminId
     * @param string $notes
     * @return bool
     */
    public function approve($id, $adminId, $notes = '') {
        $sql = "UPDATE {$this->tableName()} 
                SET status = 'approved', reviewed_by = ?, reviewed_at = NOW(), admin_notes = ? 
                WHERE id = ?";

        try {
            $stmt = $this->openConnection()->prepare($sql);
            return $stmt->execute([$adminId, $notes, $id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Reject teacher application
     * @param int $id
     * @param int $adminId
     * @param string $notes
     * @return bool
     */
    public function reject($id, $adminId, $notes = '') {
        $sql = "UPDATE {$this->tableName()} 
                SET status = 'rejected', reviewed_by = ?, reviewed_at = NOW(), admin_notes = ? 
                WHERE id = ?";

        try {
            $stmt = $this->openConnection()->prepare($sql);
            return $stmt->execute([$adminId, $notes, $id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Check if email exists in applications
     * @param string $email
     * @return bool
     */
    public function emailExists($email) {
        $sql = "SELECT id FROM {$this->tableName()} WHERE email = ?";
        try {
            $stmt = $this->openConnection()->prepare($sql);
            $stmt->execute([$email]);
            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Count pending applications
     * @return int
     */
    public function countPending() {
        $sql = "SELECT COUNT(*) as count FROM {$this->tableName()} WHERE status = 'pending'";
        try {
            $stmt = $this->openConnection()->query($sql);
            $result = $stmt->fetch();
            return (int) ($result['count'] ?? 0);
        } catch (PDOException $e) {
            return 0;
        }
    }
}

/**
 * User persistence (PDO). Domain fields live in UserEntity.
 */
class UserRepository extends BaseRepository
{
    protected function tableName(): string
    {
        return 'users';
    }

    public function create(array $data): int|false
    {
        $sql = "INSERT INTO {$this->tableName()} (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())";

        try {
            $stmt = $this->openConnection()->prepare($sql);
            $stmt->execute([
                $data['name'],
                $data['email'],
                password_hash($data['password'], PASSWORD_DEFAULT, ['cost' => HASH_COST]),
                $data['role'] ?? 'student',
            ]);

            return (int) $this->openConnection()->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT * FROM {$this->tableName()} WHERE email = ?";
        $stmt = $this->openConnection()->prepare($sql);
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function authenticate(string $email, string $password): array|false
    {
        $user = $this->findByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        return false;
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];
        $values = [];

        foreach ($data as $key => $value) {
            $fields[] = "{$key} = ?";
            $values[] = $value;
        }

        $values[] = $id;
        $sql = 'UPDATE ' . $this->tableName() . ' SET ' . implode(', ', $fields) . ' WHERE id = ?';

        $stmt = $this->openConnection()->prepare($sql);
        return $stmt->execute($values);
    }

    public function fetchStudentRows(): array
    {
        $sql = "SELECT * FROM {$this->tableName()} WHERE role = 'student'";
        $stmt = $this->openConnection()->query($sql);
        return $stmt->fetchAll();
    }

    public function countStudents(): int
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->tableName()} WHERE role = 'student'";
        $stmt = $this->openConnection()->query($sql);
        $result = $stmt->fetch();
        return (int) ($result['count'] ?? 0);
    }

    public function emailExists(string $email): bool
    {
        return $this->findByEmail($email) !== null;
    }

    public function fetchTeacherRows(): array
    {
        $sql = "SELECT * FROM {$this->tableName()} WHERE role = 'teacher' ORDER BY created_at DESC";
        $stmt = $this->openConnection()->query($sql);
        return $stmt->fetchAll();
    }

    public function findById($id): ?array
    {
        $sql = "SELECT * FROM {$this->tableName()} WHERE id = ?";
        $stmt = $this->openConnection()->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function block(int $id): bool
    {
        $sql = "UPDATE {$this->tableName()} SET is_blocked = 1 WHERE id = ?";
        $stmt = $this->openConnection()->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function unblock(int $id): bool
    {
        $sql = "UPDATE {$this->tableName()} SET is_blocked = 0 WHERE id = ?";
        $stmt = $this->openConnection()->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function isBlocked(int $id): bool
    {
        $sql = "SELECT is_blocked FROM {$this->tableName()} WHERE id = ?";
        $stmt = $this->openConnection()->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result && (int) ($result['is_blocked'] ?? 0) === 1;
    }
}

