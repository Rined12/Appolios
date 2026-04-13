<?php
/**
 * APPOLIOS — Point d’entrée router pour les URLs social-learning/* (front office SL).
 */
class SocialLearningController
{
    public function groupeIndex(): void
    {
        require_once __DIR__ . '/SocialLearningGroupeController.php';
        (new SocialLearningGroupeController())->index();
    }

    public function groupeCreate(): void
    {
        require_once __DIR__ . '/SocialLearningGroupeController.php';
        (new SocialLearningGroupeController())->create();
    }

    public function groupeStore(): void
    {
        require_once __DIR__ . '/SocialLearningGroupeController.php';
        (new SocialLearningGroupeController())->store();
    }

    public function groupeShow($id): void
    {
        require_once __DIR__ . '/SocialLearningGroupeController.php';
        (new SocialLearningGroupeController())->show($id);
    }

    public function groupeEdit($id): void
    {
        require_once __DIR__ . '/SocialLearningGroupeController.php';
        (new SocialLearningGroupeController())->edit($id);
    }

    public function groupeUpdate($id): void
    {
        require_once __DIR__ . '/SocialLearningGroupeController.php';
        (new SocialLearningGroupeController())->update($id);
    }

    public function groupeDelete($id): void
    {
        require_once __DIR__ . '/SocialLearningGroupeController.php';
        (new SocialLearningGroupeController())->delete($id);
    }

    public function discussionIndex(): void
    {
        require_once __DIR__ . '/SocialLearningDiscussionController.php';
        (new SocialLearningDiscussionController())->index();
    }

    public function discussionCreate(): void
    {
        require_once __DIR__ . '/SocialLearningDiscussionController.php';
        (new SocialLearningDiscussionController())->create();
    }

    public function discussionStore(): void
    {
        require_once __DIR__ . '/SocialLearningDiscussionController.php';
        (new SocialLearningDiscussionController())->store();
    }

    public function discussionShow($id): void
    {
        require_once __DIR__ . '/SocialLearningDiscussionController.php';
        (new SocialLearningDiscussionController())->show($id);
    }

    public function discussionEdit($id): void
    {
        require_once __DIR__ . '/SocialLearningDiscussionController.php';
        (new SocialLearningDiscussionController())->edit($id);
    }

    public function discussionUpdate($id): void
    {
        require_once __DIR__ . '/SocialLearningDiscussionController.php';
        (new SocialLearningDiscussionController())->update($id);
    }

    public function discussionDelete($id): void
    {
        require_once __DIR__ . '/SocialLearningDiscussionController.php';
        (new SocialLearningDiscussionController())->delete($id);
    }

    public function messageStore(): void
    {
        require_once __DIR__ . '/SocialLearningMessageController.php';
        (new SocialLearningMessageController())->store();
    }

    public function messageDelete($id): void
    {
        require_once __DIR__ . '/SocialLearningMessageController.php';
        (new SocialLearningMessageController())->delete($id);
    }
}
