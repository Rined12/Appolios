<?php
declare(strict_types=1);

require_once __DIR__ . '/../Model/SessionEntities.php';

/**
 * HTTP session facade — logic lives on BaseController (Controller layer, not Model).
 */
class SessionService
{
    /** @var BaseController */
    private $owner;

    public function __construct(BaseController $owner)
    {
        $this->owner = $owner;
    }

    public function flashPersist(string $type, string $message): void
    {
        $this->owner->layerSession_flashPersist($type, $message);
    }

    /** @return array{type: string, message: string}|null */
    public function flashConsumeForView(): ?array
    {
        return $this->owner->layerSession_flashConsumeForView();
    }

    /** @param array<string, mixed> $errors */
    public function validationPersist(array $errors): void
    {
        $this->owner->layerSession_validationPersist($errors);
    }

    public function persistFlash(FlashMessageEntity $message): void
    {
        $this->owner->layerSession_persistFlash($message);
    }

    public function takeFlash(): ?FlashMessageEntity
    {
        return $this->owner->layerSession_takeFlash();
    }

    public function persistValidationMessages(FormValidationMessagesEntity $entity): void
    {
        $this->owner->layerSession_persistValidationMessages($entity);
    }

    public function takeValidationMessages(): FormValidationMessagesEntity
    {
        return $this->owner->layerSession_takeValidationMessages();
    }

    public function consumeOld(): array
    {
        return $this->owner->layerSession_consumeOld();
    }

    public function pullInlineRegistrationErrors(): array
    {
        return $this->owner->layerSession_pullInlineRegistrationErrors();
    }
}

require_once __DIR__ . '/../Model/Repositories.php';

/**
 * Student-facing query helpers — implementation on StudentController.
 */
class StudentQueryService
{
    public function approvedOwnedGroupsForUser(GroupeRepository $repo, int $userId): array
    {
        return StudentController::layerStudentQuery_approvedOwnedGroupsForUser($repo, $userId);
    }

    /** @return mixed */
    public function sortKeyGroupId(array $row)
    {
        return StudentController::layerStudentQuery_sortKeyGroupId($row);
    }

    /** @return mixed */
    public function sortKeyDiscussionId(array $row)
    {
        return StudentController::layerStudentQuery_sortKeyDiscussionId($row);
    }
}

/**
 * Group activity report — implementation on BaseController.
 */
class GroupActivityReportService
{
    /** @var BaseController */
    private $owner;

    public function __construct(BaseController $owner)
    {
        $this->owner = $owner;
    }

    /**
     * @return array<string, mixed>
     */
    public function build(int $groupId): array
    {
        return $this->owner->implGroupActivityReportBuild($groupId);
    }
}
