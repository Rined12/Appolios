<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../Model/Repositories/QuizRepository.php';
require_once __DIR__ . '/../Model/Repositories/QuestionBankRepository.php';
require_once __DIR__ . '/../Model/Repositories/QuizAttemptRepository.php';

class QuizService extends BaseService
{
    private QuizRepository $quizRepo;
    private QuestionBankRepository $qbRepo;
    private QuizAttemptRepository $attemptRepo;

    public function __construct($controller = null)
    {
        $this->quizRepo = new QuizRepository();
        $this->qbRepo = new QuestionBankRepository();
        $this->attemptRepo = new QuizAttemptRepository();
    }

    public function difficultyLabelFr(string $code): string
    {
        return QuizRepository::difficultyLabelFr($code);
    }

    public function normalizeQuestionsFromPost(array $post): array
    {
        return QuizRepository::normalizeQuestionsFromPost($post);
    }

    public function getAllForTeacher(int $teacherId): array
    {
        return $this->quizRepo->getAllForTeacher($teacherId);
    }

    public function getAllForAdmin(): array
    {
        return $this->quizRepo->getAllForAdmin();
    }

    public function findWithChapterCourse(int $quizId): ?array
    {
        return $this->quizRepo->findWithChapterCourse($quizId);
    }

    public function createTeacherQuiz(int $teacherId, int $chapterId, array $meta, array $questions): int|false
    {
        return $this->quizRepo->create([
            'chapter_id' => $chapterId,
            'title' => $meta['title'] ?? '',
            'difficulty' => $meta['difficulty'] ?? 'beginner',
            'tags' => $meta['tags'] ?? null,
            'time_limit_sec' => $meta['time_limit_sec'] ?? null,
            'questions' => $questions,
            'created_by' => $teacherId,
            'status' => 'pending',
        ]);
    }

    public function createAdminQuiz(int $adminId, int $chapterId, array $meta, array $questions): int|false
    {
        return $this->quizRepo->create([
            'chapter_id' => $chapterId,
            'title' => $meta['title'] ?? '',
            'difficulty' => $meta['difficulty'] ?? 'beginner',
            'tags' => $meta['tags'] ?? null,
            'time_limit_sec' => $meta['time_limit_sec'] ?? null,
            'questions' => $questions,
            'created_by' => $adminId,
            'status' => 'approved',
        ]);
    }

    public function updateQuiz(int $quizId, array $data): bool
    {
        return $this->quizRepo->update($quizId, $data);
    }

    public function deleteQuiz(int $quizId): bool
    {
        return $this->quizRepo->delete($quizId);
    }

    public function setQuizStatus(int $quizId, string $status): bool
    {
        return $this->quizRepo->setStatus($quizId, $status);
    }

    public function getQuestionBankForTeacher(int $teacherId): array
    {
        return $this->qbRepo->getForTeacher($teacherId);
    }

    public function getQuestionBankForAdmin(): array
    {
        return $this->qbRepo->getAllForAdmin();
    }

    public function getQuestionBankReadable(): array
    {
        return $this->qbRepo->getAllReadable();
    }

    public function findQuestionOwned(int $questionId, int $userId): ?array
    {
        return $this->qbRepo->findOwned($questionId, $userId);
    }

    public function findQuestionByIdDecoded(int $questionId): ?array
    {
        return $this->qbRepo->findByIdDecoded($questionId);
    }

    public function createQuestion(array $data)
    {
        return $this->qbRepo->create($data);
    }

    public function updateQuestion(int $questionId, array $data): bool
    {
        return $this->qbRepo->update($questionId, $data);
    }

    public function deleteQuestion(int $questionId): bool
    {
        return $this->qbRepo->delete($questionId);
    }

    public function appendBankQuestions(array $baseQuestions, array $bankIds, ?int $restrictToUserId): array
    {
        return $this->qbRepo->appendIdsToQuizQuestions($baseQuestions, $bankIds, $restrictToUserId);
    }

    public function getQuizzesForEnrolledStudent(int $studentId): array
    {
        return $this->quizRepo->getForEnrolledStudent($studentId);
    }

    public function getChaptersForEnrolledStudent(int $studentId): array
    {
        $sql = "SELECT ch.*, c.title AS course_title, c.id AS course_id
                FROM chapters ch
                JOIN courses c ON c.id = ch.course_id
                JOIN enrollments e ON e.course_id = c.id AND e.user_id = ?
                ORDER BY c.title ASC, ch.sort_order ASC, ch.id ASC";
        $stmt = $this->quizRepo->getDb()->prepare($sql);
        $stmt->execute([$studentId]);
        return $stmt->fetchAll();
    }

    public function recordAttempt(int $studentId, int $quizId, int $score, int $total, int $percentage): bool
    {
        return $this->attemptRepo->record($studentId, $quizId, $score, $total, $percentage);
    }

    public function getAttemptsByUser(int $studentId): array
    {
        return $this->attemptRepo->getByUserWithQuizTitles($studentId);
    }
}
