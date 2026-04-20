<?php
/**
 * APPOLIOS — Social Learning (routes index.php?url=social-learning/discussion/*)
 * Vues : app/views/social_learning/frontoffice/discussion
 */
require_once __DIR__ . '/../models/Discussion.php';
require_once __DIR__ . '/../models/Message.php';
require_once __DIR__ . '/../models/Groupe.php';

class SocialLearningDiscussionController
{
    private Discussion $discussionModel;
    private Groupe $groupeModel;

    public function __construct()
    {
        $this->discussionModel = new Discussion();
        $this->groupeModel = new Groupe();
    }

    private function canManageDiscussion(array $row): bool
    {
        $uid = $this->slUserId();
        if (!$uid) {
            return false;
        }
        if ($this->slPlatformAdmin()) {
            return true;
        }
        if ((int) $row['id_auteur'] === $uid) {
            return true;
        }
        $idGroupe = (int) ($row['id_groupe'] ?? 0);
        if (isset($row['groupe_id_createur']) && (int) $row['groupe_id_createur'] === $uid) {
            return true;
        }
        return $this->groupeModel->isMemberAdmin($idGroupe, $uid);
    }

    public function index(): void
    {
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $discussions = $this->discussionModel->paginateWithGroupe($limit, $offset);
        $total = $this->discussionModel->countVisibleFeed();
        $totalPages = max(1, (int) ceil($total / $limit));
        $currentPage = $page;
        require __DIR__ . '/../views/social_learning/frontoffice/discussion/index.php';
    }

    public function create(): void
    {
        $errors = [];
        $preselect = (int) ($_GET['id_groupe'] ?? 0);
        $old = ['titre' => '', 'contenu' => '', 'id_groupe' => $preselect > 0 ? (string) $preselect : ''];
        $groupes = $this->groupeModel->paginateWithCreator(200, 0, true);
        require __DIR__ . '/../views/social_learning/frontoffice/discussion/create.php';
    }

    public function store(): void
    {
        $titre = trim($_POST['titre'] ?? '');
        $contenu = trim($_POST['contenu'] ?? '');
        $id_groupe = (int) ($_POST['id_groupe'] ?? 0);

        $errors = $this->validateDiscussionCreate($titre, $contenu, $id_groupe);
        if ($id_groupe >= 1 && !$this->groupeModel->existsApproved($id_groupe)) {
            $errors[] = 'Choisissez un groupe approuvé par l\'administrateur.';
        }

        if (!empty($errors)) {
            $old = [
                'titre' => htmlspecialchars($titre, ENT_QUOTES, 'UTF-8'),
                'contenu' => htmlspecialchars($contenu, ENT_QUOTES, 'UTF-8'),
                'id_groupe' => $id_groupe,
            ];
            $groupes = $this->groupeModel->paginateWithCreator(200, 0, true);
            require __DIR__ . '/../views/social_learning/frontoffice/discussion/create.php';
            return;
        }

        $id_auteur = $this->slUserId() ?? 1;
        $this->discussionModel->create(
            htmlspecialchars($titre, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($contenu, ENT_QUOTES, 'UTF-8'),
            $id_groupe,
            $id_auteur,
            'en_attente'
        );
        header('Location: ' . APP_URL . '/index.php?url=social-learning/groupe/show/' . $id_groupe . '&discussion=request');
    }

    public function show($id): void
    {
        $discussion = $this->discussionModel->findWithGroupe((int) $id);
        if (!$discussion) {
            http_response_code(404);
            echo 'Discussion introuvable';
            return;
        }
        $uid = $this->slUserId();
        $dap = $discussion['approval_statut'] ?? 'approuve';
        if ($dap !== 'approuve' && !$this->slPlatformAdmin() && (int) $discussion['id_auteur'] !== (int) $uid) {
            http_response_code(403);
            echo 'Cette discussion n\'est pas encore approuvée.';
            return;
        }
        $gap = $discussion['groupe_approval_statut'] ?? 'approuve';
        if ($gap !== 'approuve' && !$this->slPlatformAdmin()) {
            http_response_code(403);
            echo 'Le groupe de cette discussion n\'est pas approuvé.';
            return;
        }
        $discussionApproved = ($dap === 'approuve');
        $messageModel = new Message();
        $messages = $discussionApproved
            ? $messageModel->listByDiscussion((int) $discussion['id_discussion'])
            : [];
        $canManageDiscussion = $this->canManageDiscussion($discussion);
        require __DIR__ . '/../views/social_learning/frontoffice/discussion/show.php';
    }

    public function edit($id): void
    {
        $discussion = $this->discussionModel->findWithGroupe((int) $id);
        if (!$discussion) {
            http_response_code(404);
            echo 'Discussion introuvable';
            return;
        }
        if (!$this->canManageDiscussion($discussion)) {
            http_response_code(403);
            echo 'Accès refusé';
            return;
        }
        $errors = [];
        require __DIR__ . '/../views/social_learning/frontoffice/discussion/edit.php';
    }

    public function update($id): void
    {
        $discussion = $this->discussionModel->findWithGroupe((int) $id);
        if (!$discussion) {
            http_response_code(404);
            echo 'Discussion introuvable';
            return;
        }
        if (!$this->canManageDiscussion($discussion)) {
            http_response_code(403);
            echo 'Accès refusé';
            return;
        }

        $titre = trim($_POST['titre'] ?? '');
        $contenu = trim($_POST['contenu'] ?? '');

        $errors = $this->validateDiscussionContent($titre, $contenu);
        if (!empty($errors)) {
            $discussion = [
                'id_discussion' => (int) $id,
                'titre' => htmlspecialchars($titre, ENT_QUOTES, 'UTF-8'),
                'contenu' => htmlspecialchars($contenu, ENT_QUOTES, 'UTF-8'),
                'nom_groupe' => $discussion['nom_groupe'] ?? '',
                'id_groupe' => $discussion['id_groupe'] ?? 0,
            ];
            require __DIR__ . '/../views/social_learning/frontoffice/discussion/edit.php';
            return;
        }

        $this->discussionModel->update(
            (int) $id,
            htmlspecialchars($titre, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($contenu, ENT_QUOTES, 'UTF-8')
        );
        header('Location: ' . APP_URL . '/index.php?url=social-learning/discussion/show/' . (int) $id);
    }

    public function delete($id): void
    {
        $discussion = $this->discussionModel->findWithGroupe((int) $id);
        if (!$discussion) {
            http_response_code(404);
            echo 'Discussion introuvable';
            return;
        }
        if (!$this->canManageDiscussion($discussion)) {
            http_response_code(403);
            echo 'Accès refusé';
            return;
        }
        $idGroupe = (int) $discussion['id_groupe'];
        $this->discussionModel->deleteById((int) $id);
        header('Location: ' . APP_URL . '/index.php?url=social-learning/groupe/show/' . $idGroupe);
    }

    private function slUserId(): ?int
    {
        return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
    }

    private function slPlatformAdmin(): bool
    {
        return ($_SESSION['role'] ?? '') === 'admin';
    }

    /** @return string[] */
    private function validateDiscussionContent(string $titre, string $contenu): array
    {
        $errors = [];
        $titre = trim($titre);
        $contenu = trim($contenu);
        if ($titre === '') {
            $errors[] = 'Le titre est obligatoire.';
        } elseif (mb_strlen($titre) < 5) {
            $errors[] = 'Le titre doit contenir au moins 5 caractères.';
        } elseif (mb_strlen($titre) > 200) {
            $errors[] = 'Le titre ne peut pas dépasser 200 caractères.';
        }
        if ($contenu === '') {
            $errors[] = 'Le contenu est obligatoire.';
        } elseif (mb_strlen($contenu) < 10) {
            $errors[] = 'Le contenu doit contenir au moins 10 caractères.';
        } elseif (mb_strlen($contenu) > 20000) {
            $errors[] = 'Le contenu ne peut pas dépasser 20000 caractères.';
        }
        return $errors;
    }

    /** @return string[] */
    private function validateDiscussionCreate(string $titre, string $contenu, int $idGroupe): array
    {
        $errors = $this->validateDiscussionContent($titre, $contenu);
        if ($idGroupe < 1) {
            $errors[] = 'Veuillez sélectionner un groupe.';
        }
        return $errors;
    }
}
