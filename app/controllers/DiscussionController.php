<?php
/**
 * APPOLIOS — Social Learning
 * Controller : DiscussionController
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Groupe.php';
require_once __DIR__ . '/../models/Discussion.php';
require_once __DIR__ . '/../models/Message.php';

class DiscussionController extends Controller {

    private Groupe     $groupeModel;
    private Discussion $discussionModel;

    public function __construct() {
        $this->groupeModel     = new Groupe();
        $this->discussionModel = new Discussion();
    }

    // ==================================================================
    // ▌ FRONT OFFICE
    // ==================================================================

    /**
     * GET student/groupes/{idGroupe}/discussions
     */
    public function index(string $idGroupe): void {
        $this->requireLogin();
        $idG = (int)$idGroupe;

        $groupe = $this->groupeModel->findByIdWithCreator($idG);
        if (!$groupe) {
            $this->setFlash('error', 'Groupe introuvable.');
            $this->redirect($this->socialLearningGroupesPath());
            return;
        }

        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = 10;
        $offset = ($page - 1) * $limit;
        $total  = $this->discussionModel->countByGroupe($idG);

        $this->view('student/discussion/index', [
            'title'              => 'Discussions — ' . htmlspecialchars($groupe['nom_groupe']) . ' — APPOLIOS',
            'description'        => 'Discussions du groupe',
            'studentSidebarActive' => 'groupes',
            'groupe'             => $groupe,
            'discussions'        => $this->discussionModel->getByGroupe($idG, $limit, $offset),
            'isMember'           => $this->groupeModel->isMember($idG, (int)$_SESSION['user_id']),
            'currentPage'        => $page,
            'totalPages'         => (int)ceil($total / $limit),
            'flash'              => $this->getFlash(),
        ]);
    }

    /**
     * GET student/groupes/{idGroupe}/discussions/create
     */
    public function create(string $idGroupe): void {
        $this->requireLogin();
        $idG = (int)$idGroupe;

        $groupe = $this->groupeModel->findByIdWithCreator($idG);
        if (!$groupe) {
            $this->setFlash('error', 'Groupe introuvable.');
            $this->redirect($this->socialLearningGroupesPath());
            return;
        }

        if (!$this->groupeModel->isMember($idG, (int)$_SESSION['user_id'])) {
            $this->setFlash('error', 'Vous devez être membre du groupe pour poster une discussion.');
            $this->redirect($this->socialLearningGroupesPath() . '/' . $idG);
            return;
        }

        $this->view('student/discussion/create', [
            'title'              => 'Nouvelle discussion — APPOLIOS',
            'description'        => 'Créer une discussion dans le groupe',
            'studentSidebarActive' => 'groupes',
            'groupe'             => $groupe,
            'errors'             => [],
            'old'                => [],
            'flash'              => $this->getFlash(),
        ]);
    }

    /**
     * POST student/groupes/{idGroupe}/discussions/store
     */
    public function store(string $idGroupe): void {
        $this->requireLogin();
        $idG = (int)$idGroupe;

        $groupe = $this->groupeModel->findByIdWithCreator($idG);
        if (!$groupe) {
            $this->setFlash('error', 'Groupe introuvable.');
            $this->redirect($this->socialLearningGroupesPath());
            return;
        }

        if (!$this->groupeModel->isMember($idG, (int)$_SESSION['user_id'])) {
            $this->setFlash('error', 'Accès refusé.');
            $this->redirect($this->socialLearningGroupesPath() . '/' . $idG);
            return;
        }

        if (($groupe['approval_statut'] ?? 'approuve') !== 'approuve') {
            $this->setFlash('error', 'Impossible de créer une discussion : le groupe doit d\'abord être approuvé par un administrateur.');
            $this->redirect($this->socialLearningGroupesPath() . '/' . $idG);
            return;
        }

        $errors  = [];
        $titre   = trim($_POST['titre'] ?? '');
        $contenu = trim($_POST['contenu'] ?? '');

        if ($titre === '') {
            $errors[] = 'Le titre est obligatoire.';
        } elseif (strlen($titre) < 5) {
            $errors[] = 'Le titre doit contenir au moins 5 caractères.';
        } elseif (strlen($titre) > 200) {
            $errors[] = 'Le titre ne peut pas dépasser 200 caractères.';
        }

        if ($contenu === '') {
            $errors[] = 'Le contenu est obligatoire.';
        } elseif (strlen($contenu) < 10) {
            $errors[] = 'Le contenu doit contenir au moins 10 caractères.';
        }

        if (!empty($errors)) {
            $this->view('student/discussion/create', [
                'title'              => 'Nouvelle discussion — APPOLIOS',
                'description'        => 'Créer une discussion',
                'studentSidebarActive' => 'groupes',
                'groupe'             => $groupe,
                'errors'             => $errors,
                'old'                => ['titre' => htmlspecialchars($titre), 'contenu' => htmlspecialchars($contenu)],
                'flash'              => null,
            ]);
            return;
        }

        $newId = $this->discussionModel->create(
            htmlspecialchars($titre,   ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($contenu, ENT_QUOTES, 'UTF-8'),
            $idG,
            (int)$_SESSION['user_id'],
            'en_attente'
        );

        if ($newId) {
            $this->setFlash('success', 'Demande enregistrée : cette discussion est en attente de validation par un administrateur. Elle ne sera publique qu\'après acceptation.');
            $this->redirect($this->socialLearningGroupesPath() . '/' . $idG . '/discussions/' . $newId);
        } else {
            $this->setFlash('error', 'Erreur lors de la création.');
            $this->redirect($this->socialLearningGroupesPath() . '/' . $idG . '/discussions/create');
        }
    }

    /**
     * GET student/groupes/{idGroupe}/discussions/{id}
     */
    public function show(string $idGroupe, string $id): void {
        $this->requireLogin();
        $idG  = (int)$idGroupe;
        $idD  = (int)$id;

        $groupe = $this->groupeModel->findByIdWithCreator($idG);
        if (!$groupe) {
            $this->setFlash('error', 'Groupe introuvable.');
            $this->redirect($this->socialLearningGroupesPath());
            return;
        }

        $discussion = $this->discussionModel->findByIdWithAuthor($idD);
        if (!$discussion || (int)$discussion['id_groupe'] !== $idG) {
            $this->setFlash('error', 'Discussion introuvable.');
            $this->redirect($this->socialLearningGroupesPath() . '/' . $idG);
            return;
        }

        $uid = (int)$_SESSION['user_id'];
        $dap = $discussion['approval_statut'] ?? 'approuve';
        if ($dap !== 'approuve' && !$this->isAdmin() && (int)$discussion['id_auteur'] !== $uid) {
            $this->setFlash('error', 'Cette discussion n\'est pas encore approuvée.');
            $this->redirect($this->socialLearningGroupesPath() . '/' . $idG);
            return;
        }

        $msgModel = new Message();
        $page     = max(1, (int)($_GET['page'] ?? 1));
        $limit    = 20;
        $offset   = ($page - 1) * $limit;
        $total    = $msgModel->countByDiscussion($idD);

        $discussionApproved = ($dap === 'approuve');
        $messages = $discussionApproved
            ? $msgModel->getByDiscussion($idD, $limit, $offset)
            : [];

        $this->view('student/discussion/show', [
            'title'              => htmlspecialchars($discussion['titre']) . ' — APPOLIOS',
            'description'        => 'Discussion et messages',
            'studentSidebarActive' => 'groupes',
            'groupe'             => $groupe,
            'discussion'         => $discussion,
            'messages'           => $messages,
            'discussionApproved' => $discussionApproved,
            'isMember'           => $this->groupeModel->isMember($idG, $uid),
            'myRole'             => $this->groupeModel->getMemberRole($idG, $uid),
            'currentPage'        => $page,
            'totalPages'         => $discussionApproved ? (int)ceil($total / $limit) : 1,
            'errors'             => [],
            'flash'              => $this->getFlash(),
        ]);
    }

    /**
     * POST student/groupes/{idGroupe}/discussions/{id}/update
     */
    public function update(string $idGroupe, string $id): void {
        $this->requireLogin();
        $idG = (int)$idGroupe;
        $idD = (int)$id;

        $discussion = $this->discussionModel->findByIdWithAuthor($idD);
        if (!$discussion || (int)$discussion['id_groupe'] !== $idG) {
            $this->setFlash('error', 'Discussion introuvable.');
            $this->redirect($this->socialLearningGroupesPath() . '/' . $idG);
            return;
        }

        $userId = (int)$_SESSION['user_id'];
        $myRole = $this->groupeModel->getMemberRole($idG, $userId);
        if ((int)$discussion['id_auteur'] !== $userId && $myRole !== 'admin' && !$this->isAdmin()) {
            $this->setFlash('error', 'Accès refusé.');
            $this->redirect($this->socialLearningGroupesPath() . '/' . $idG . '/discussions/' . $idD);
            return;
        }

        $errors  = [];
        $titre   = trim($_POST['titre'] ?? '');
        $contenu = trim($_POST['contenu'] ?? '');

        if ($titre === '') {
            $errors[] = 'Le titre est obligatoire.';
        } elseif (strlen($titre) < 5) {
            $errors[] = 'Le titre doit contenir au moins 5 caractères.';
        } elseif (strlen($titre) > 200) {
            $errors[] = 'Le titre ne peut pas dépasser 200 caractères.';
        }

        if ($contenu === '') {
            $errors[] = 'Le contenu est obligatoire.';
        } elseif (strlen($contenu) < 10) {
            $errors[] = 'Le contenu doit contenir au moins 10 caractères.';
        }

        $groupe = $this->groupeModel->findByIdWithCreator($idG);

        if (!empty($errors)) {
            $this->view('student/discussion/show', [
                'title'              => 'Modifier discussion — APPOLIOS',
                'description'        => 'Modifier la discussion',
                'studentSidebarActive' => 'groupes',
                'groupe'             => $groupe,
                'discussion'         => $discussion,
                'messages'           => (new Message())->getByDiscussion($idD),
                'isMember'           => true,
                'myRole'             => $myRole,
                'currentPage'        => 1,
                'totalPages'         => 1,
                'errors'             => $errors,
                'flash'              => null,
            ]);
            return;
        }

        if ($this->discussionModel->update($idD, htmlspecialchars($titre, ENT_QUOTES, 'UTF-8'), htmlspecialchars($contenu, ENT_QUOTES, 'UTF-8'))) {
            $this->setFlash('success', 'Discussion mise à jour.');
        } else {
            $this->setFlash('error', 'Erreur lors de la mise à jour.');
        }
        $this->redirect($this->socialLearningGroupesPath() . '/' . $idG . '/discussions/' . $idD);
    }

    /**
     * GET student/groupes/{idGroupe}/discussions/{id}/delete
     */
    public function destroy(string $idGroupe, string $id): void {
        $this->requireLogin();
        $idG    = (int)$idGroupe;
        $idD    = (int)$id;
        $userId = (int)$_SESSION['user_id'];

        $discussion = $this->discussionModel->findByIdWithAuthor($idD);
        if (!$discussion || (int)$discussion['id_groupe'] !== $idG) {
            $this->setFlash('error', 'Discussion introuvable.');
            $this->redirect($this->socialLearningGroupesPath() . '/' . $idG);
            return;
        }

        $myRole = $this->groupeModel->getMemberRole($idG, $userId);
        if ((int)$discussion['id_auteur'] !== $userId && $myRole !== 'admin' && !$this->isAdmin()) {
            $this->setFlash('error', 'Accès refusé.');
            $this->redirect($this->socialLearningGroupesPath() . '/' . $idG . '/discussions/' . $idD);
            return;
        }

        $this->discussionModel->deleteById($idD);
        $this->setFlash('success', 'Discussion supprimée.');
        $this->redirect($this->socialLearningGroupesPath() . '/' . $idG);
    }

    /**
     * POST student/groupes/{idGroupe}/discussions/{id}/like
     */
    public function like(string $idGroupe, string $id): void {
        $this->requireLogin();
        $this->discussionModel->incrementLikes((int)$id);
        $this->redirect($this->socialLearningGroupesPath() . '/' . $idGroupe . '/discussions/' . $id);
    }

    // ==================================================================
    // ▌ BACK OFFICE — Admin
    // ==================================================================

    /**
     * GET admin/sl-discussions
     */
    public function adminIndex(): void {
        $this->requireAdmin();

        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = 10;
        $offset = ($page - 1) * $limit;
        $total  = $this->discussionModel->countAll();

        $this->view('admin/discussion/index', [
            'title'            => 'Gestion des discussions — Admin APPOLIOS',
            'description'      => 'Tableau de bord admin — discussions Social Learning',
            'adminSidebarActive' => 'sl-discussions',
            'discussions'      => $this->discussionModel->getAllWithDetails($limit, $offset),
            'currentPage'      => $page,
            'totalPages'       => (int)ceil($total / $limit),
            'totalDiscussions' => $total,
            'totalPendingApproval' => $this->discussionModel->countPendingApproval(),
            'flash'            => $this->getFlash(),
        ]);
    }

    /**
     * POST admin/sl-discussions/{id}/approve
     */
    public function adminApprove(string $id): void {
        $this->requireAdmin();
        $idD = (int)$id;
        if ($this->discussionModel->setApprovalStatut($idD, 'approuve')) {
            $this->setFlash('success', 'Discussion approuvée.');
        } else {
            $this->setFlash('error', 'Impossible d\'approuver cette discussion.');
        }
        $this->redirect('admin/sl-discussions');
    }

    /**
     * POST admin/sl-discussions/{id}/reject
     */
    public function adminReject(string $id): void {
        $this->requireAdmin();
        $idD = (int)$id;
        if ($this->discussionModel->setApprovalStatut($idD, 'refuse')) {
            $this->setFlash('success', 'Discussion refusée.');
        } else {
            $this->setFlash('error', 'Impossible de refuser cette discussion.');
        }
        $this->redirect('admin/sl-discussions');
    }

    /**
     * GET admin/sl-discussions/{id}/edit
     */
    public function adminEdit(string $id): void {
        $this->requireAdmin();
        $idD = (int)$id;

        $discussion = $this->discussionModel->findByIdWithAuthor($idD);
        if (!$discussion) {
            $this->setFlash('error', 'Discussion introuvable.');
            $this->redirect('admin/sl-discussions');
            return;
        }

        $this->view('admin/discussion/edit', [
            'title'            => 'Modifier discussion — Admin APPOLIOS',
            'description'      => 'Modifier une discussion',
            'adminSidebarActive' => 'sl-discussions',
            'discussion'       => $discussion,
            'errors'           => [],
            'old'              => [],
            'flash'            => $this->getFlash(),
        ]);
    }

    /**
     * POST admin/sl-discussions/{id}/update
     */
    public function adminUpdate(string $id): void {
        $this->requireAdmin();
        $idD = (int)$id;

        $discussion = $this->discussionModel->findByIdWithAuthor($idD);
        if (!$discussion) {
            $this->setFlash('error', 'Discussion introuvable.');
            $this->redirect('admin/sl-discussions');
            return;
        }

        $errors  = [];
        $titre   = trim($_POST['titre'] ?? '');
        $contenu = trim($_POST['contenu'] ?? '');

        if ($titre === '' || strlen($titre) < 5 || strlen($titre) > 200) {
            $errors[] = 'Le titre doit faire entre 5 et 200 caractères.';
        }
        if ($contenu === '' || strlen($contenu) < 10) {
            $errors[] = 'Le contenu doit faire au moins 10 caractères.';
        }

        if (!empty($errors)) {
            $this->view('admin/discussion/edit', [
                'title'            => 'Modifier discussion — Admin APPOLIOS',
                'description'      => 'Modifier une discussion',
                'adminSidebarActive' => 'sl-discussions',
                'discussion'       => $discussion,
                'errors'           => $errors,
                'old'              => ['titre' => htmlspecialchars($titre), 'contenu' => htmlspecialchars($contenu)],
                'flash'            => null,
            ]);
            return;
        }

        if ($this->discussionModel->update($idD, htmlspecialchars($titre, ENT_QUOTES, 'UTF-8'), htmlspecialchars($contenu, ENT_QUOTES, 'UTF-8'))) {
            $this->setFlash('success', 'Discussion mise à jour.');
        } else {
            $this->setFlash('error', 'Erreur lors de la mise à jour.');
        }
        $this->redirect('admin/sl-discussions');
    }

    /**
     * GET admin/sl-discussions/{id}/delete
     */
    public function adminDelete(string $id): void {
        $this->requireAdmin();

        if ($this->discussionModel->deleteById((int)$id)) {
            $this->setFlash('success', 'Discussion supprimée.');
        } else {
            $this->setFlash('error', 'Erreur lors de la suppression.');
        }
        $this->redirect('admin/sl-discussions');
    }

    // ==================================================================
    // ▌ Helpers
    // ==================================================================

    private function requireLogin(): void {
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Connexion requise.');
            $this->redirect('login');
            exit();
        }
    }

    private function requireAdmin(): void {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Accès administrateur requis.');
            $this->redirect('login');
            exit();
        }
    }
}
