<?php
/**
 * APPOLIOS — Contrôleur Social Learning : messages sur discussions (MVC app/).
 */
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Message.php';
require_once __DIR__ . '/../models/Discussion.php';
require_once __DIR__ . '/../models/Groupe.php';

class SocialLearningMessageController extends Controller
{
    private Message $messageModel;
    private Discussion $discussionModel;
    private Groupe $groupeModel;

    public function __construct()
    {
        $this->messageModel = new Message();
        $this->discussionModel = new Discussion();
        $this->groupeModel = new Groupe();
    }

    public function store(): void
    {
        $idDiscussion = (int) ($_POST['id_discussion'] ?? 0);
        $contenu = trim($_POST['contenu'] ?? '');

        $errors = $this->validateMessageContenu($contenu);
        $uid = $this->slUserId();
        if (!$uid) {
            $errors[] = 'Vous devez être connecté pour répondre.';
        }

        $discussion = $idDiscussion > 0 ? $this->discussionModel->findById($idDiscussion) : null;
        if (!$discussion) {
            $errors[] = 'Discussion introuvable.';
        } elseif (($discussion['approval_statut'] ?? 'approuve') !== 'approuve') {
            $errors[] = 'La discussion doit être approuvée avant d\'ajouter des messages.';
        }

        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['old'] = ['contenu' => $contenu];
            $redir = $discussion
                ? APP_URL . '/index.php?url=social-learning/discussion/show/' . $idDiscussion
                : APP_URL . '/index.php?url=social-learning/discussion';
            header('Location: ' . $redir);
            return;
        }

        $this->messageModel->create(
            htmlspecialchars($contenu, ENT_QUOTES, 'UTF-8'),
            $idDiscussion,
            $uid
        );
        unset($_SESSION['form_errors'], $_SESSION['old']);
        header('Location: ' . APP_URL . '/index.php?url=social-learning/discussion/show/' . $idDiscussion);
    }

    public function delete(string $idMessage): void
    {
        $mid = (int) $idMessage;
        $msg = $this->messageModel->findById($mid);
        if (!$msg) {
            http_response_code(404);
            echo 'Message introuvable';
            return;
        }

        $discussion = $this->discussionModel->findWithGroupe((int) $msg['id_discussion']);
        if (!$discussion) {
            http_response_code(404);
            return;
        }

        $uid = $this->slUserId();
        $isAuthor = $uid && (int) $msg['id_auteur'] === $uid;
        $isAdmin = $this->isAdmin();
        $isGroupeAdmin = $uid && $this->groupeModel->isMemberAdmin((int) $discussion['id_groupe'], $uid);
        $isDiscussionAuthor = $uid && (int) $discussion['id_auteur'] === $uid;
        $isGroupeCreator = $uid && isset($discussion['groupe_id_createur']) && (int) $discussion['groupe_id_createur'] === $uid;

        if (!($isAuthor || $isAdmin || $isGroupeAdmin || $isDiscussionAuthor || $isGroupeCreator)) {
            http_response_code(403);
            echo 'Accès refusé';
            return;
        }

        $this->messageModel->deleteById($mid);
        header('Location: ' . APP_URL . '/index.php?url=social-learning/discussion/show/' . (int) $discussion['id_discussion']);
    }

    private function slUserId(): ?int
    {
        return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
    }

    /** @return string[] */
    private function validateMessageContenu(string $contenu): array
    {
        $errors = [];
        $contenu = trim($contenu);
        if ($contenu === '') {
            $errors[] = 'Le message ne peut pas être vide.';
        } elseif (mb_strlen($contenu) < 10) {
            $errors[] = 'Le message doit contenir au moins 10 caractères.';
        } elseif (mb_strlen($contenu) > 10000) {
            $errors[] = 'Le message ne peut pas dépasser 10000 caractères.';
        }
        return $errors;
    }
}
