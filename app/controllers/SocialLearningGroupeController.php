<?php
/**
 * APPOLIOS — Social Learning (routes index.php?url=social-learning/groupe/*)
 * Vues : app/views/social_learning/frontoffice/groupe
 */
require_once __DIR__ . '/../models/Groupe.php';
require_once __DIR__ . '/../models/Discussion.php';

class SocialLearningGroupeController
{
    private Groupe $groupeModel;

    public function __construct()
    {
        $this->groupeModel = new Groupe();
    }

    private function canManageGroupe(array $groupe): bool
    {
        $uid = $this->slUserId();
        if (!$uid) {
            return false;
        }
        if ($this->slPlatformAdmin()) {
            return true;
        }
        if ((int) $groupe['id_createur'] === $uid) {
            return true;
        }
        return $this->groupeModel->isMemberAdmin((int) $groupe['id_groupe'], $uid);
    }

    public function index(): void
    {
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $groupes = $this->groupeModel->paginateWithCreator($limit, $offset, true);
        $total = $this->groupeModel->countApproved();
        $totalPages = max(1, (int) ceil($total / $limit));
        $currentPage = $page;
        require __DIR__ . '/../views/social_learning/frontoffice/groupe/index.php';
    }

    public function create(): void
    {
        $errors = [];
        $old = ['nom_groupe' => '', 'description' => ''];
        require __DIR__ . '/../views/social_learning/frontoffice/groupe/create.php';
    }

    public function store(): void
    {
        $nom = trim($_POST['nom_groupe'] ?? '');
        $description = trim($_POST['description'] ?? '');

        $errors = $this->validateGroupeCreate($nom, $description);
        if (!empty($errors)) {
            $old = [
                'nom_groupe' => htmlspecialchars($nom, ENT_QUOTES, 'UTF-8'),
                'description' => htmlspecialchars($description, ENT_QUOTES, 'UTF-8'),
            ];
            require __DIR__ . '/../views/social_learning/frontoffice/groupe/create.php';
            return;
        }

        $nomSafe = htmlspecialchars($nom, ENT_QUOTES, 'UTF-8');
        $descriptionSafe = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');
        $id_createur = $this->slUserId() ?? 1;
        $this->groupeModel->create($nomSafe, $descriptionSafe, $id_createur, 'en_attente');
        header('Location: ' . APP_URL . '/index.php?url=social-learning/groupe&request=sent');
    }

    public function show($id): void
    {
        $groupe = $this->groupeModel->findById((int) $id);
        if (!$groupe) {
            http_response_code(404);
            echo 'Groupe introuvable';
            return;
        }
        $uid = $this->slUserId();
        $ap = $groupe['approval_statut'] ?? 'approuve';
        if ($ap !== 'approuve' && !$this->slPlatformAdmin() && (int) $groupe['id_createur'] !== (int) $uid) {
            http_response_code(403);
            echo 'Ce groupe n\'est pas encore approuvé.';
            return;
        }
        $discussionModel = new Discussion();
        $discussions = $discussionModel->getByGroupe((int) $groupe['id_groupe'], 500, 0);
        $canManage = $this->canManageGroupe($groupe);
        $groupePendingApproval = ($ap !== 'approuve');
        require __DIR__ . '/../views/social_learning/frontoffice/groupe/show.php';
    }

    public function edit($id): void
    {
        $groupe = $this->groupeModel->findById((int) $id);
        if (!$groupe) {
            http_response_code(404);
            echo 'Groupe introuvable';
            return;
        }
        if (!$this->canManageGroupe($groupe)) {
            http_response_code(403);
            echo 'Accès refusé';
            return;
        }
        $errors = [];
        require __DIR__ . '/../views/social_learning/frontoffice/groupe/edit.php';
    }

    public function update($id): void
    {
        $groupe = $this->groupeModel->findById((int) $id);
        if (!$groupe) {
            http_response_code(404);
            echo 'Groupe introuvable';
            return;
        }
        if (!$this->canManageGroupe($groupe)) {
            http_response_code(403);
            echo 'Accès refusé';
            return;
        }

        $nom = trim($_POST['nom_groupe'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $statut = trim($_POST['statut'] ?? 'actif');

        $errors = $this->validateGroupeUpdate($nom, $description, $statut);
        if (!empty($errors)) {
            $groupe = [
                'id_groupe' => (int) $id,
                'nom_groupe' => htmlspecialchars($nom, ENT_QUOTES, 'UTF-8'),
                'description' => htmlspecialchars($description, ENT_QUOTES, 'UTF-8'),
                'statut' => $statut,
            ];
            require __DIR__ . '/../views/social_learning/frontoffice/groupe/edit.php';
            return;
        }

        $this->groupeModel->update(
            (int) $id,
            htmlspecialchars($nom, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($description, ENT_QUOTES, 'UTF-8'),
            $statut
        );
        header('Location: ' . APP_URL . '/index.php?url=social-learning/groupe/show/' . (int) $id);
    }

    public function delete($id): void
    {
        $groupe = $this->groupeModel->findById((int) $id);
        if (!$groupe) {
            http_response_code(404);
            echo 'Groupe introuvable';
            return;
        }
        if (!$this->canManageGroupe($groupe)) {
            http_response_code(403);
            echo 'Accès refusé';
            return;
        }
        $this->groupeModel->deleteById((int) $id);
        header('Location: ' . APP_URL . '/index.php?url=social-learning/groupe');
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
    private function validateGroupeCreate(string $nom, string $description): array
    {
        $errors = [];
        $nom = trim($nom);
        $description = trim($description);
        if ($nom === '') {
            $errors[] = 'Le nom du groupe est obligatoire.';
        } elseif (mb_strlen($nom) < 3) {
            $errors[] = 'Le nom doit contenir au moins 3 caractères.';
        } elseif (mb_strlen($nom) > 100) {
            $errors[] = 'Le nom ne peut pas dépasser 100 caractères.';
        }
        if ($description === '') {
            $errors[] = 'La description est obligatoire.';
        } elseif (mb_strlen($description) < 10) {
            $errors[] = 'La description doit contenir au moins 10 caractères.';
        } elseif (mb_strlen($description) > 8000) {
            $errors[] = 'La description ne peut pas dépasser 8000 caractères.';
        }
        return $errors;
    }

    /** @return string[] */
    private function validateGroupeUpdate(string $nom, string $description, string $statut): array
    {
        $errors = $this->validateGroupeCreate($nom, $description);
        if (!in_array($statut, ['actif', 'archivé'], true)) {
            $errors[] = 'Statut invalide.';
        }
        return $errors;
    }
}
