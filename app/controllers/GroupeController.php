<?php
/**
 * APPOLIOS — Social Learning
 * Controller : GroupeController
 * Routes: student/groupes, admin/sl-groupes
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Groupe.php';
require_once __DIR__ . '/../models/Discussion.php';

class GroupeController extends Controller {

    private Groupe $groupeModel;

    public function __construct() {
        $this->groupeModel = new Groupe();
    }

    // ==================================================================
    // ▌ FRONT OFFICE — Student
    // ==================================================================

    /**
     * GET student/groupes — liste des groupes disponibles + mes groupes
     */
    public function index(): void {
        $this->requireLogin();

        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = 10;
        $offset = ($page - 1) * $limit;

        $total   = $this->groupeModel->countApproved();
        $groupes = $this->groupeModel->getAllWithCreatorPublic($limit, $offset);
        $pages   = max(1, (int)ceil($total / $limit));

        $this->view('student/groupe/index', [
            'title'              => 'Groupes d\'apprentissage — APPOLIOS',
            'description'        => 'Rejoignez un groupe collaboratif',
            'studentSidebarActive' => 'groupes',
            'groupes'            => $groupes,
            'currentPage'        => $page,
            'totalPages'         => $pages,
            'flash'              => $this->getFlash(),
        ]);
    }

    /**
     * GET student/groupes/create
     */
    public function create(): void {
        $this->requireLogin();

        $this->view('student/groupe/create', [
            'title'              => 'Créer un groupe — APPOLIOS',
            'description'        => 'Créer un nouveau groupe d\'apprentissage',
            'studentSidebarActive' => 'groupes',
            'errors'             => [],
            'old'                => [],
            'flash'              => $this->getFlash(),
        ]);
    }

    /**
     * POST student/groupes/store
     */
    public function store(): void {
        $this->requireLogin();

        $errors = [];
        $nom         = trim($_POST['nom_groupe'] ?? '');
        $description = trim($_POST['description'] ?? '');

        // Validation serveur
        if ($nom === '') {
            $errors[] = 'Le nom du groupe est obligatoire.';
        } elseif (strlen($nom) < 3) {
            $errors[] = 'Le nom doit contenir au moins 3 caractères.';
        } elseif (strlen($nom) > 100) {
            $errors[] = 'Le nom ne peut pas dépasser 100 caractères.';
        }

        if ($description === '') {
            $errors[] = 'La description est obligatoire.';
        } elseif (strlen($description) < 10) {
            $errors[] = 'La description doit contenir au moins 10 caractères.';
        }

        if (!empty($errors)) {
            $this->view('student/groupe/create', [
                'title'              => 'Créer un groupe — APPOLIOS',
                'description'        => 'Créer un nouveau groupe d\'apprentissage',
                'studentSidebarActive' => 'groupes',
                'errors'             => $errors,
                'old'                => ['nom_groupe' => htmlspecialchars($nom), 'description' => htmlspecialchars($description)],
                'flash'              => null,
            ]);
            return;
        }

        $nomSafe  = htmlspecialchars($nom,         ENT_QUOTES, 'UTF-8');
        $descSafe = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');
        $newId = $this->groupeModel->create($nomSafe, $descSafe, (int)$_SESSION['user_id'], 'en_attente');

        if ($newId) {
            $this->setFlash('success', 'Demande enregistrée : votre groupe est en attente de validation par un administrateur. Il ne sera visible dans le catalogue qu\'après acceptation.');
            $this->redirect($this->socialLearningGroupesPath() . '/' . $newId);
        } else {
            $this->setFlash('error', 'Erreur lors de la création du groupe.');
            $this->redirect($this->socialLearningGroupesPath() . '/create');
        }
    }

    /**
     * GET student/groupes/{id} — détail groupe
     */
    public function show(string $id): void {
        $this->requireLogin();
        $idGroupe = (int)$id;

        $groupe = $this->groupeModel->findByIdWithCreator($idGroupe);
        if (!$groupe) {
            $this->setFlash('error', 'Groupe introuvable.');
            $this->redirect($this->socialLearningGroupesPath());
            return;
        }

        $uid = (int)$_SESSION['user_id'];
        $ap  = $groupe['approval_statut'] ?? 'approuve';
        if ($ap !== 'approuve' && !$this->isAdmin() && (int)$groupe['id_createur'] !== $uid) {
            $this->setFlash('error', 'Ce groupe n\'est pas encore approuvé ou n\'est pas accessible.');
            $this->redirect($this->socialLearningGroupesPath());
            return;
        }

        $isMember = $this->groupeModel->isMember($idGroupe, $uid);
        $members  = $this->groupeModel->getMembers($idGroupe);
        $myRole   = $this->groupeModel->getMemberRole($idGroupe, $uid);

        $discModel = new Discussion();
        $page      = max(1, (int)($_GET['page'] ?? 1));
        $limit     = 10;
        $offset    = ($page - 1) * $limit;
        $total     = $discModel->countByGroupe($idGroupe);
        $discussions = $discModel->getByGroupe($idGroupe, $limit, $offset);

        $this->view('student/groupe/show', [
            'title'              => htmlspecialchars($groupe['nom_groupe']) . ' — APPOLIOS',
            'description'        => 'Détail du groupe et discussions',
            'studentSidebarActive' => 'groupes',
            'groupe'             => $groupe,
            'membres'            => $members,
            'isMember'           => $isMember,
            'myRole'             => $myRole,
            'discussions'        => $discussions,
            'currentPage'        => $page,
            'totalPages'         => (int)ceil($total / $limit),
            'groupePendingApproval' => (($groupe['approval_statut'] ?? 'approuve') !== 'approuve'),
            'flash'              => $this->getFlash(),
        ]);
    }

    /**
     * GET student/groupes/{id}/edit
     */
    public function edit(string $id): void {
        $this->requireLogin();
        $idGroupe = (int)$id;

        $groupe = $this->groupeModel->findByIdWithCreator($idGroupe);
        if (!$groupe) {
            $this->setFlash('error', 'Groupe introuvable.');
            $this->redirect($this->socialLearningGroupesPath());
            return;
        }

        $myRole = $this->groupeModel->getMemberRole($idGroupe, (int)$_SESSION['user_id']);
        if ($myRole !== 'admin' && !$this->isAdmin()) {
            $this->setFlash('error', 'Accès refusé.');
            $this->redirect($this->socialLearningGroupesPath() . '/' . $idGroupe);
            return;
        }

        $this->view('student/groupe/edit', [
            'title'              => 'Modifier le groupe — APPOLIOS',
            'description'        => 'Modifier le groupe d\'apprentissage',
            'studentSidebarActive' => 'groupes',
            'groupe'             => $groupe,
            'errors'             => [],
            'old'                => [],
            'flash'              => $this->getFlash(),
        ]);
    }

    /**
     * POST student/groupes/{id}/update
     */
    public function update(string $id): void {
        $this->requireLogin();
        $idGroupe = (int)$id;

        $groupe = $this->groupeModel->findByIdWithCreator($idGroupe);
        if (!$groupe) {
            $this->setFlash('error', 'Groupe introuvable.');
            $this->redirect($this->socialLearningGroupesPath());
            return;
        }

        $myRole = $this->groupeModel->getMemberRole($idGroupe, (int)$_SESSION['user_id']);
        if ($myRole !== 'admin' && !$this->isAdmin()) {
            $this->setFlash('error', 'Accès refusé.');
            $this->redirect($this->socialLearningGroupesPath() . '/' . $idGroupe);
            return;
        }

        $errors      = [];
        $nom         = trim($_POST['nom_groupe'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $statut      = trim($_POST['statut'] ?? 'actif');

        if ($nom === '') {
            $errors[] = 'Le nom est obligatoire.';
        } elseif (strlen($nom) < 3) {
            $errors[] = 'Le nom doit contenir au moins 3 caractères.';
        } elseif (strlen($nom) > 100) {
            $errors[] = 'Le nom ne peut pas dépasser 100 caractères.';
        }

        if ($description === '') {
            $errors[] = 'La description est obligatoire.';
        } elseif (strlen($description) < 10) {
            $errors[] = 'La description doit contenir au moins 10 caractères.';
        }

        if (!in_array($statut, ['actif', 'archivé'])) {
            $statut = 'actif';
        }

        if (!empty($errors)) {
            $this->view('student/groupe/edit', [
                'title'              => 'Modifier le groupe — APPOLIOS',
                'description'        => 'Modifier le groupe',
                'studentSidebarActive' => 'groupes',
                'groupe'             => $groupe,
                'errors'             => $errors,
                'old'                => ['nom_groupe' => htmlspecialchars($nom), 'description' => htmlspecialchars($description), 'statut' => $statut],
                'flash'              => null,
            ]);
            return;
        }

        $nomSafe  = htmlspecialchars($nom,         ENT_QUOTES, 'UTF-8');
        $descSafe = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');

        if ($this->groupeModel->update($idGroupe, $nomSafe, $descSafe, $statut)) {
            $this->setFlash('success', 'Groupe mis à jour avec succès.');
        } else {
            $this->setFlash('error', 'Erreur lors de la mise à jour.');
        }

        $this->redirect($this->socialLearningGroupesPath() . '/' . $idGroupe);
    }

    /**
     * GET student/groupes/{id}/join
     */
    public function join(string $id): void {
        $this->requireLogin();
        $idGroupe = (int)$id;

        if (!$this->groupeModel->isMember($idGroupe, (int)$_SESSION['user_id'])) {
            $this->groupeModel->addMember($idGroupe, (int)$_SESSION['user_id'], 'membre');
            $this->setFlash('success', 'Vous avez rejoint le groupe.');
        } else {
            $this->setFlash('info', 'Vous êtes déjà membre de ce groupe.');
        }

        $this->redirect($this->socialLearningGroupesPath() . '/' . $idGroupe);
    }

    /**
     * GET student/groupes/{id}/leave
     */
    public function leave(string $id): void {
        $this->requireLogin();
        $idGroupe = (int)$id;
        $userId   = (int)$_SESSION['user_id'];

        // Prevent the group creator (admin) from leaving
        $groupe = $this->groupeModel->findByIdWithCreator($idGroupe);
        if ($groupe && (int)$groupe['id_createur'] === $userId) {
            $this->setFlash('error', 'Le créateur ne peut pas quitter son propre groupe.');
        } else {
            $this->groupeModel->removeMember($idGroupe, $userId);
            $this->setFlash('success', 'Vous avez quitté le groupe.');
        }

        $this->redirect($this->socialLearningGroupesPath());
    }

    /**
     * GET student/groupes/{id}/delete  (admin du groupe ou admin plateforme)
     */
    public function destroy(string $id): void {
        $this->requireLogin();
        $idGroupe = (int)$id;
        $userId   = (int)$_SESSION['user_id'];

        $groupe = $this->groupeModel->findByIdWithCreator($idGroupe);
        if (!$groupe) {
            $this->setFlash('error', 'Groupe introuvable.');
            $this->redirect($this->socialLearningGroupesPath());
            return;
        }

        $myRole = $this->groupeModel->getMemberRole($idGroupe, $userId);
        if ($myRole !== 'admin' && !$this->isAdmin()) {
            $this->setFlash('error', 'Seul l\'administrateur du groupe peut le supprimer.');
            $this->redirect($this->socialLearningGroupesPath() . '/' . $idGroupe);
            return;
        }

        $this->groupeModel->deleteById($idGroupe);
        $this->setFlash('success', 'Groupe supprimé.');
        $this->redirect($this->socialLearningGroupesPath());
    }

    // ==================================================================
    // ▌ BACK OFFICE — Admin
    // ==================================================================

    /**
     * GET admin/sl-groupes
     */
    public function adminIndex(): void {
        $this->requireAdmin();

        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = 10;
        $offset = ($page - 1) * $limit;

        $total   = $this->groupeModel->countAll();
        $groupes = $this->groupeModel->getAllWithCreator($limit, $offset);
        $pages   = (int)ceil($total / $limit);

        $this->view('admin/groupe/index', [
            'title'            => 'Gestion des groupes — Admin APPOLIOS',
            'description'      => 'Tableau de bord admin — groupes Social Learning',
            'adminSidebarActive' => 'sl-groupes',
            'groupes'          => $groupes,
            'currentPage'      => $page,
            'totalPages'       => $pages,
            'totalGroupes'     => $total,
            'totalActifs'      => $this->groupeModel->countActif(),
            'totalArchives'    => $this->groupeModel->countArchive(),
            'totalPendingApproval' => $this->groupeModel->countPendingApproval(),
            'flash'            => $this->getFlash(),
        ]);
    }

    /**
     * GET admin/sl-groupes/create
     */
    public function adminCreate(): void {
        $this->requireAdmin();

        $this->view('admin/groupe/create', [
            'title'            => 'Créer un groupe — Admin APPOLIOS',
            'description'      => 'Créer un groupe Social Learning',
            'adminSidebarActive' => 'sl-groupes',
            'errors'           => [],
            'old'              => [],
            'flash'            => $this->getFlash(),
        ]);
    }

    /**
     * POST admin/sl-groupes/store
     */
    public function adminStore(): void {
        $this->requireAdmin();

        $errors      = [];
        $nom         = trim($_POST['nom_groupe'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($nom === '') {
            $errors[] = 'Le nom du groupe est obligatoire.';
        } elseif (strlen($nom) < 3) {
            $errors[] = 'Le nom doit contenir au moins 3 caractères.';
        } elseif (strlen($nom) > 100) {
            $errors[] = 'Le nom ne peut pas dépasser 100 caractères.';
        }

        if ($description === '') {
            $errors[] = 'La description est obligatoire.';
        } elseif (strlen($description) < 10) {
            $errors[] = 'La description doit contenir au moins 10 caractères.';
        }

        if (!empty($errors)) {
            $this->view('admin/groupe/create', [
                'title'            => 'Créer un groupe — Admin APPOLIOS',
                'description'      => 'Créer un groupe Social Learning',
                'adminSidebarActive' => 'sl-groupes',
                'errors'           => $errors,
                'old'              => ['nom_groupe' => htmlspecialchars($nom), 'description' => htmlspecialchars($description)],
                'flash'            => null,
            ]);
            return;
        }

        $newId = $this->groupeModel->create(
            htmlspecialchars($nom,         ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($description, ENT_QUOTES, 'UTF-8'),
            (int)$_SESSION['user_id'],
            'approuve'
        );

        if ($newId) {
            $this->setFlash('success', 'Groupe créé et approuvé.');
        } else {
            $this->setFlash('error', 'Erreur lors de la création.');
        }
        $this->redirect('admin/sl-groupes');
    }

    /**
     * POST admin/sl-groupes/{id}/approve
     */
    public function adminApprove(string $id): void {
        $this->requireAdmin();
        $idGroupe = (int)$id;
        if ($this->groupeModel->setApprovalStatut($idGroupe, 'approuve')) {
            $this->setFlash('success', 'Groupe approuvé.');
        } else {
            $this->setFlash('error', 'Impossible d\'approuver ce groupe.');
        }
        $this->redirect('admin/sl-groupes');
    }

    /**
     * POST admin/sl-groupes/{id}/reject
     */
    public function adminReject(string $id): void {
        $this->requireAdmin();
        $idGroupe = (int)$id;
        if ($this->groupeModel->setApprovalStatut($idGroupe, 'refuse')) {
            $this->setFlash('success', 'Groupe refusé.');
        } else {
            $this->setFlash('error', 'Impossible de refuser ce groupe.');
        }
        $this->redirect('admin/sl-groupes');
    }

    /**
     * GET admin/sl-groupes/{id}/edit
     */
    public function adminEdit(string $id): void {
        $this->requireAdmin();
        $idGroupe = (int)$id;

        $groupe = $this->groupeModel->findByIdWithCreator($idGroupe);
        if (!$groupe) {
            $this->setFlash('error', 'Groupe introuvable.');
            $this->redirect('admin/sl-groupes');
            return;
        }

        $this->view('admin/groupe/edit', [
            'title'            => 'Modifier le groupe — Admin APPOLIOS',
            'description'      => 'Modifier un groupe Social Learning',
            'adminSidebarActive' => 'sl-groupes',
            'groupe'           => $groupe,
            'errors'           => [],
            'old'              => [],
            'flash'            => $this->getFlash(),
        ]);
    }

    /**
     * POST admin/sl-groupes/{id}/update
     */
    public function adminUpdate(string $id): void {
        $this->requireAdmin();
        $idGroupe = (int)$id;

        $groupe = $this->groupeModel->findByIdWithCreator($idGroupe);
        if (!$groupe) {
            $this->setFlash('error', 'Groupe introuvable.');
            $this->redirect('admin/sl-groupes');
            return;
        }

        $errors      = [];
        $nom         = trim($_POST['nom_groupe'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $statut      = trim($_POST['statut'] ?? 'actif');

        if ($nom === '') {
            $errors[] = 'Le nom est obligatoire.';
        } elseif (strlen($nom) < 3) {
            $errors[] = 'Le nom doit contenir au moins 3 caractères.';
        } elseif (strlen($nom) > 100) {
            $errors[] = 'Le nom ne peut pas dépasser 100 caractères.';
        }

        if ($description === '') {
            $errors[] = 'La description est obligatoire.';
        } elseif (strlen($description) < 10) {
            $errors[] = 'La description doit contenir au moins 10 caractères.';
        }

        if (!in_array($statut, ['actif', 'archivé'])) {
            $statut = 'actif';
        }

        if (!empty($errors)) {
            $this->view('admin/groupe/edit', [
                'title'            => 'Modifier le groupe — Admin APPOLIOS',
                'description'      => 'Modifier un groupe',
                'adminSidebarActive' => 'sl-groupes',
                'groupe'           => $groupe,
                'errors'           => $errors,
                'old'              => ['nom_groupe' => htmlspecialchars($nom), 'description' => htmlspecialchars($description), 'statut' => $statut],
                'flash'            => null,
            ]);
            return;
        }

        if ($this->groupeModel->update($idGroupe, htmlspecialchars($nom, ENT_QUOTES, 'UTF-8'), htmlspecialchars($description, ENT_QUOTES, 'UTF-8'), $statut)) {
            $this->setFlash('success', 'Groupe mis à jour.');
        } else {
            $this->setFlash('error', 'Erreur lors de la mise à jour.');
        }
        $this->redirect('admin/sl-groupes');
    }

    /**
     * GET admin/sl-groupes/{id}/delete
     */
    public function adminDelete(string $id): void {
        $this->requireAdmin();
        $idGroupe = (int)$id;

        if ($this->groupeModel->deleteById($idGroupe)) {
            $this->setFlash('success', 'Groupe supprimé.');
        } else {
            $this->setFlash('error', 'Erreur lors de la suppression.');
        }
        $this->redirect('admin/sl-groupes');
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
