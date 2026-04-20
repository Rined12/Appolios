<?php
/**
 * APPOLIOS Student Controller
 * Handles student dashboard and course enrollment
 */

require_once __DIR__ . '/../Controller/BaseController.php';
require_once __DIR__ . '/../Model/Course.php';
require_once __DIR__ . '/../Model/Enrollment.php';
require_once __DIR__ . '/../Model/Evenement.php';
require_once __DIR__ . '/../Model/EvenementRessource.php';
require_once __DIR__ . '/../Model/Groupe.php';
require_once __DIR__ . '/../Model/Discussion.php';

class StudentController extends BaseController {
    /**
     * Adds foPrefix and teacherSidebarActive for shared group/discussion views.
     */
    private function withFoContext(array $data, string $teacherNav): array
    {
        $data['foPrefix'] = $this->frontOfficeRoutePrefix();
        if ($data['foPrefix'] === 'teacher') {
            $data['teacherSidebarActive'] = $teacherNav;
        }

        return $data;
    }

    /**
     * Redirect under student/ or teacher/ for groups & discussions.
     */
    private function foRedirect(string $path): void
    {
        $this->redirect($this->frontOfficeRoutePrefix() . '/' . ltrim($path, '/'));
    }

    public function discussions(...$params) {
        if (!$this->isLoggedIn() || !in_array($_SESSION['role'] ?? '', ['student', 'teacher'], true)) {
            $this->setFlash('error', 'Access denied.');
            $this->redirect('login');
            return;
        }

        $discussionModel = $this->model('Discussion');
        $groupeModel = $this->model('Groupe');
        $first = $params[0] ?? null;
        $second = $params[1] ?? null;

        if ($first === 'create') {
            $old = $_SESSION['discussion_old'] ?? [];
            unset($_SESSION['discussion_old']);
            $data = $this->withFoContext([
                'title' => 'Create Discussion - APPOLIOS',
                'studentSidebarActive' => 'discussions',
                'groups' => $groupeModel->getByCreator((int) $_SESSION['user_id']),
                'old' => $old,
                'errors' => $_SESSION['discussion_errors'] ?? [],
                'flash' => $this->getFlash()
            ], 'discussions');
            unset($_SESSION['discussion_errors']);
            $this->view('FrontOffice/student/discussions/create', $data);
            return;
        }

        if ($first === 'store' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->studentDiscussionsStore($discussionModel, $groupeModel);
            return;
        }

        $id = (int) $first;
        if ($id > 0 && $second === 'edit') {
            $this->studentDiscussionsEdit($discussionModel, $groupeModel, $id);
            return;
        }
        if ($id > 0 && $second === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->studentDiscussionsUpdate($discussionModel, $groupeModel, $id);
            return;
        }
        if ($id > 0 && $second === 'delete') {
            $groupeModel = $this->model('Groupe');
            $ok = $this->studentDiscussionDeleteAuthorized($discussionModel, $groupeModel, $id);
            $this->setFlash($ok ? 'success' : 'error', $ok ? 'Discussion supprimee.' : 'Suppression impossible (non autorisee).');
            $this->foRedirect('discussions');
            return;
        }

        $data = $this->withFoContext([
            'title' => 'Discussions - APPOLIOS',
            'studentSidebarActive' => 'discussions',
            'discussions' => $discussionModel->getByAuthor((int) $_SESSION['user_id']),
            'flash' => $this->getFlash()
        ], 'discussions');
        $this->view('FrontOffice/student/discussions/index', $data);
    }

    public function groupes(...$params) {
        if (!$this->isLoggedIn() || !in_array($_SESSION['role'] ?? '', ['student', 'teacher'], true)) {
            $this->setFlash('error', 'Access denied.');
            $this->redirect('login');
            return;
        }

        $groupeModel = $this->model('Groupe');
        $first = $params[0] ?? null;
        $second = $params[1] ?? null;
        $third = $params[2] ?? null;
        $fourth = $params[3] ?? null;

        if ($first === null) {
            $this->studentGroupesIndex($groupeModel);
            return;
        }

        if ($first === 'create') {
            $this->studentGroupesCreate();
            return;
        }

        if ($first === 'store' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->studentGroupesStore($groupeModel);
            return;
        }

        $id = (int) $first;
        if ($id <= 0) {
            $this->setFlash('error', 'Invalid group identifier.');
            $this->foRedirect('groupes');
            return;
        }

        if ($second === null) {
            $this->studentGroupesShow($groupeModel, $id);
            return;
        }

        if ($second === 'edit') {
            $this->studentGroupesEdit($groupeModel, $id);
            return;
        }

        if ($second === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->studentGroupesUpdate($groupeModel, $id);
            return;
        }

        if ($second === 'join') {
            $this->studentGroupesJoin($groupeModel, $id);
            return;
        }

        if ($second === 'delete') {
            $discussionModel = $this->model('Discussion');
            $this->studentGroupesDelete($groupeModel, $discussionModel, $id);
            return;
        }

        if ($second === 'discussions' && $third === 'store' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->studentGroupesDiscussionStore($groupeModel, $id);
            return;
        }

        if ($second === 'discussions' && $fourth === 'delete' && ctype_digit((string) $third) && (int) $third > 0) {
            $discussionModel = $this->model('Discussion');
            $this->studentGroupDiscussionDeleteFromPage($groupeModel, $discussionModel, $id, (int) $third);
            return;
        }

        $this->setFlash('error', 'Route not found.');
        $this->foRedirect('groupes');
    }

    /**
     * Route alias for /student/evenement/{id}
     */
    public function evenement($id) {
        $this->evenementDetail($id);
    }

    /**
     * Route alias for /student/course/{id}
     */
    public function course($id) {
        $this->viewCourse($id);
    }

    /**
     * Student dashboard
     */
    public function dashboard() {
        // Check if logged in
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Please login to access your dashboard.');
            $this->redirect('login');
            return;
        }

        $evenementModel = $this->model('Evenement');
        $evenements = $evenementModel->findApprovedUpcoming();

        $data = [
            'title' => 'My Dashboard - APPOLIOS',
            'description' => 'Student evenement dashboard',
            'userName' => $_SESSION['user_name'],
            'evenements' => $evenements,
            'flash' => $this->getFlash()
        ];

        $this->view('FrontOffice/student/evenements', $data);
    }

    /**
     * Student evenements catalog page
     */
    public function evenements() {
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Please login to access events.');
            $this->redirect('login');
            return;
        }

        $evenementModel = $this->model('Evenement');

        $data = [
            'title' => 'Evenements - APPOLIOS',
            'description' => 'Browse upcoming evenements',
            'userName' => $_SESSION['user_name'],
            'evenements' => $evenementModel->findApprovedUpcoming(),
            'flash' => $this->getFlash()
        ];

        $this->view('FrontOffice/student/evenements', $data);
    }

    /**
     * Student evenement detail page with resources
     */
    public function evenementDetail($id) {
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Please login to view evenement details.');
            $this->redirect('login');
            return;
        }

        $evenementModel = $this->model('Evenement');
        $ressourceModel = $this->model('EvenementRessource');

        $evenement = $evenementModel->findByIdWithCreator($id);
        if (!$evenement) {
            $this->setFlash('error', 'Evenement not found.');
            $this->redirect('student/evenements');
            return;
        }

        if (($evenement['approval_status'] ?? 'approved') !== 'approved') {
            $this->setFlash('error', 'This evenement is not available yet.');
            $this->redirect('student/evenements');
            return;
        }

        $grouped = $ressourceModel->getGroupedByEvenement($id);

        $data = [
            'title' => (($evenement['titre'] ?? '') ?: ($evenement['title'] ?? 'Evenement')) . ' - APPOLIOS',
            'description' => $evenement['description'] ?? 'Evenement details',
            'evenement' => $evenement,
            'rules' => $grouped['rules'],
            'materiels' => $grouped['materiels'],
            'plans' => $grouped['plans'],
            'flash' => $this->getFlash()
        ];

        $this->view('FrontOffice/student/evenement_detail', $data);
    }

    /**
     * Browse all available courses (for students)
     */
    public function courses() {
        // Check if logged in
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Please login to browse courses.');
            $this->redirect('login');
            return;
        }

        // Only students can access this page
        if ($_SESSION['role'] !== 'student') {
            $this->setFlash('error', 'Access denied.');
            $this->redirect('login');
            return;
        }

        $courseModel = $this->model('Course');
        $enrollmentModel = $this->model('Enrollment');

        // Get all courses
        $allCourses = $courseModel->getAllWithCreator();

        // Get enrolled course IDs to mark them
        $enrollments = $enrollmentModel->getUserEnrollments($_SESSION['user_id']);
        $enrolledIds = array_column($enrollments, 'course_id');

        $data = [
            'title' => 'Browse Courses - APPOLIOS',
            'description' => 'Explore all available courses',
            'courses' => $allCourses,
            'enrolledIds' => $enrolledIds,
            'flash' => $this->getFlash()
        ];

        $this->view('FrontOffice/student/courses', $data);
    }

    /**
     * View course details
     */
    public function viewCourse($id) {
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Please login to view courses.');
            $this->redirect('login');
            return;
        }

        $courseModel = $this->model('Course');
        $enrollmentModel = $this->model('Enrollment');

        $course = $courseModel->getWithCreator($id);

        if (!$course) {
            $this->setFlash('error', 'Course not found.');
            $this->redirect('student/dashboard');
            return;
        }

        $isEnrolled = $enrollmentModel->isEnrolled($_SESSION['user_id'], $id);

        $data = [
            'title' => $course['title'] . ' - APPOLIOS',
            'description' => $course['description'],
            'course' => $course,
            'isEnrolled' => $isEnrolled,
            'flash' => $this->getFlash()
        ];

        $this->view('FrontOffice/student/course', $data);
    }

    /**
     * Enroll in a course
     */
    public function enroll($id) {
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Please login to enroll in courses.');
            $this->redirect('login');
            return;
        }

        $enrollmentModel = $this->model('Enrollment');

        // Check if already enrolled
        if ($enrollmentModel->isEnrolled($_SESSION['user_id'], $id)) {
            $this->setFlash('info', 'You are already enrolled in this course.');
            $this->redirect('student/course/' . $id);
            return;
        }

        // Enroll user
        if ($enrollmentModel->enroll($_SESSION['user_id'], $id)) {
            $this->setFlash('success', 'Successfully enrolled in the course!');
        } else {
            $this->setFlash('error', 'Failed to enroll. Please try again.');
        }

        $this->redirect('student/course/' . $id);
    }

    /**
     * My courses page
     */
    public function myCourses() {
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Please login to view your courses.');
            $this->redirect('login');
            return;
        }

        $enrollmentModel = $this->model('Enrollment');
        $enrollments = $enrollmentModel->getUserEnrollments($_SESSION['user_id']);

        $data = [
            'title' => 'My Courses - APPOLIOS',
            'description' => 'Your enrolled courses',
            'enrollments' => $enrollments,
            'flash' => $this->getFlash()
        ];

        $this->view('FrontOffice/student/my_courses', $data);
    }

    /**
     * Student profile page
     */
    public function profile() {
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Please login to view your profile.');
            $this->redirect('login');
            return;
        }

        require_once __DIR__ . '/../Model/User.php';
        $userModel = $this->model('User');
        $user = $userModel->findById($_SESSION['user_id']);

        $data = [
            'title' => 'My Profile - APPOLIOS',
            'description' => 'Student profile',
            'user' => $user,
            'flash' => $this->getFlash()
        ];

        $this->view('FrontOffice/student/profile', $data);
    }

    private function studentGroupesIndex($groupeModel): void
    {
        $uid = (int) $_SESSION['user_id'];
        $mesGroupes = $groupeModel->getByCreator($uid);
        $mesGroupesEnApprobation = array_values(array_filter(
            $mesGroupes,
            static function (array $g): bool {
                $a = (string) ($g['approval_statut'] ?? $g['approval_status'] ?? '');

                return $a !== 'approuve';
            }
        ));

        $rawPublic = $groupeModel->getAllWithCreatorPublic(100, 0);
        $groupes = array_values(array_filter(
            $rawPublic,
            static function (array $g): bool {
                $a = (string) ($g['approval_statut'] ?? $g['approval_status'] ?? '');

                return $a === 'approuve';
            }
        ));

        $data = $this->withFoContext([
            'title' => 'Groupes - APPOLIOS',
            'groupes' => $groupes,
            'mesGroupesEnApprobation' => $mesGroupesEnApprobation,
            'flash' => $this->getFlash(),
            'studentSidebarActive' => 'groupes',
        ], 'groupes');
        $this->view('FrontOffice/student/groupes/index', $data);
    }

    private function studentGroupesCreate(): void
    {
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['old']);
        $data = $this->withFoContext([
            'title' => 'Creer un groupe - APPOLIOS',
            'old' => $old,
            'errors' => $this->getErrors(),
            'flash' => $this->getFlash(),
            'studentSidebarActive' => 'groupes',
        ], 'groupes');
        $this->view('FrontOffice/student/groupes/create', $data);
    }

    private function studentGroupesStore($groupeModel): void
    {
        $payload = $this->extractGroupePayload();
        $errors = $this->validateGroupePayload($payload);
        if (!empty($errors)) {
            $this->setErrors($errors);
            $_SESSION['old'] = $_POST;
            $this->foRedirect('groupes/create');
            return;
        }

        $canStoreImg = $groupeModel->supportsStoredImage();
        $photo = ['url' => null, 'error' => null];
        if ($canStoreImg) {
            $photo = $this->handleGroupPhotoUpload('group_photo');
        } elseif ((int) ($_FILES['group_photo']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $photo['error'] = 'Image storage is not available. Contact the administrator.';
        }
        if ($photo['error']) {
            $this->setErrors(['group_photo' => $photo['error']]);
            $_SESSION['old'] = $_POST;
            $this->foRedirect('groupes/create');
            return;
        }

        $createData = [
            'nom_groupe' => htmlspecialchars($payload['nom_groupe'], ENT_QUOTES, 'UTF-8'),
            'description' => htmlspecialchars($payload['description'], ENT_QUOTES, 'UTF-8'),
            'statut' => 'actif',
            'id_createur' => (int) $_SESSION['user_id'],
            'approval_statut' => 'en_cours',
        ];
        if ($photo['url'] !== null) {
            $createData['image_url'] = $photo['url'];
        }

        $createdId = $groupeModel->create($createData);

        if ($createdId) {
            $groupeModel->ajouterMembre((int) $createdId, (int) $_SESSION['user_id'], 'admin');
            $this->setFlash('success', 'Groupe cree. Etat: en cours d approbation jusqu a la decision de l administrateur.');
            $this->foRedirect('groupes');
            return;
        }

        if ($photo['url'] !== null) {
            $this->deleteGroupPhotoFileIfManaged($photo['url']);
        }
        $this->setFlash('error', 'Erreur lors de la creation du groupe.');
        $this->foRedirect('groupes/create');
    }

    private function studentGroupesShow($groupeModel, int $id): void
    {
        $groupe = $groupeModel->findById($id);
        if (!$groupe) {
            $this->setFlash('error', 'Groupe introuvable.');
            $this->foRedirect('groupes');
            return;
        }

        $uid = (int) $_SESSION['user_id'];
        $approval = (string) ($groupe['approval_statut'] ?? '');
        $isCreator = (int) ($groupe['id_createur'] ?? 0) === $uid;
        if ($approval !== 'approuve' && !$isCreator) {
            $this->setFlash('error', 'Ce groupe est encore en cours d approbation. Seul le createur peut le consulter.');
            $this->foRedirect('groupes');
            return;
        }

        $discussionModel = $this->model('Discussion');
        $discussionOld = $_SESSION['discussion_old'] ?? [];
        unset($_SESSION['discussion_old']);

        $data = $this->withFoContext([
            'title' => 'Detail groupe - APPOLIOS',
            'groupe' => $groupe,
            'membres' => $groupeModel->getMembres($id),
            'discussions' => $discussionModel->getByGroupForViewer(
                $id,
                (int) $_SESSION['user_id'],
                (int) ($groupe['id_createur'] ?? 0)
            ),
            'discussionOld' => $discussionOld,
            'discussionErrors' => $_SESSION['discussion_errors'] ?? [],
            'isMembre' => $groupeModel->estMembre($id, (int) $_SESSION['user_id']),
            'flash' => $this->getFlash(),
            'studentSidebarActive' => 'groupes',
        ], 'groupes');
        unset($_SESSION['discussion_errors']);
        $this->view('FrontOffice/student/groupes/show', $data);
    }

    private function studentGroupesDiscussionStore($groupeModel, int $groupId): void
    {
        $groupe = $groupeModel->findById($groupId);
        if (!$groupe) {
            $this->setFlash('error', 'Group not found.');
            $this->foRedirect('groupes');
            return;
        }

        if ((int) ($groupe['id_createur'] ?? 0) !== (int) $_SESSION['user_id']) {
            $this->setFlash('error', 'Only the group creator can create discussions.');
            $this->foRedirect('groupes/' . $groupId);
            return;
        }

        $payload = [
            'titre' => trim((string) ($_POST['titre'] ?? '')),
            'contenu' => trim((string) ($_POST['contenu'] ?? '')),
        ];
        $errors = [];
        $titre = (string) ($payload['titre'] ?? '');
        $contenu = (string) ($payload['contenu'] ?? '');
        if ($titre === '') {
            $errors['titre'] = 'This field cannot be empty.';
        } elseif (strlen($titre) < 3) {
            $errors['titre'] = 'Title must be between 3 and 200 characters.';
        } elseif (strlen($titre) > 200) {
            $errors['titre'] = 'Title must not exceed 200 characters.';
        }
        if ($contenu === '') {
            $errors['contenu'] = 'This field cannot be empty.';
        } elseif (strlen($contenu) < 5) {
            $errors['contenu'] = 'Content must be between 5 and 5000 characters.';
        } elseif (strlen($contenu) > 5000) {
            $errors['contenu'] = 'Content must not exceed 5000 characters.';
        }

        if (!empty($errors)) {
            $_SESSION['discussion_errors'] = $errors;
            $_SESSION['discussion_old'] = $_POST;
            $this->foRedirect('groupes/' . $groupId);
            return;
        }

        $discussionModel = $this->model('Discussion');
        $ok = $discussionModel->createForGroup(
            $groupId,
            (int) $_SESSION['user_id'],
            htmlspecialchars($payload['titre'], ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($payload['contenu'], ENT_QUOTES, 'UTF-8')
        );

        $this->setFlash($ok ? 'success' : 'error', $ok ? 'Discussion creee. Statut: en cours — en attente de validation par l administrateur.' : 'Failed to create discussion.');
        $this->foRedirect('groupes/' . $groupId);
    }

    private function studentGroupesEdit($groupeModel, int $id): void
    {
        $groupe = $groupeModel->findById($id);
        if (!$groupe || (int) $groupe['id_createur'] !== (int) $_SESSION['user_id']) {
            $this->setFlash('error', 'Vous ne pouvez modifier que vos groupes.');
            $this->foRedirect('groupes');
            return;
        }

        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['old']);
        $data = $this->withFoContext([
            'title' => 'Modifier groupe - APPOLIOS',
            'groupe' => $groupe,
            'old' => $old,
            'errors' => $this->getErrors(),
            'flash' => $this->getFlash(),
            'studentSidebarActive' => 'groupes',
        ], 'groupes');
        $this->view('FrontOffice/student/groupes/edit', $data);
    }

    private function studentGroupesUpdate($groupeModel, int $id): void
    {
        $groupe = $groupeModel->findById($id);
        if (!$groupe || (int) $groupe['id_createur'] !== (int) $_SESSION['user_id']) {
            $this->setFlash('error', 'Vous ne pouvez modifier que vos groupes.');
            $this->foRedirect('groupes');
            return;
        }

        $payload = $this->extractGroupePayload();
        $currentApproval = (string) ($groupe['approval_statut'] ?? '');
        $isApproved = $currentApproval === 'approuve';
        if (!$isApproved) {
            $payload['statut'] = (string) ($groupe['statut'] ?? 'actif');
        }
        $errors = $this->validateGroupePayload($payload);
        if (!empty($errors)) {
            $this->setErrors($errors);
            $_SESSION['old'] = $_POST;
            $this->foRedirect('groupes/' . $id . '/edit');
            return;
        }

        $canStoreImg = $groupeModel->supportsStoredImage();
        $photo = ['url' => null, 'error' => null];
        if ($canStoreImg) {
            $photo = $this->handleGroupPhotoUpload('group_photo');
        } elseif ((int) ($_FILES['group_photo']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $photo['error'] = 'Image storage is not available. Contact the administrator.';
        }
        if ($photo['error']) {
            $this->setErrors(['group_photo' => $photo['error']]);
            $_SESSION['old'] = $_POST;
            $this->foRedirect('groupes/' . $id . '/edit');
            return;
        }

        $existingImg = $this->groupeImageUrlFromRow($groupe);
        $updateData = [
            'nom_groupe' => htmlspecialchars($payload['nom_groupe'], ENT_QUOTES, 'UTF-8'),
            'description' => htmlspecialchars($payload['description'], ENT_QUOTES, 'UTF-8'),
            'statut' => $isApproved ? $payload['statut'] : (string) ($groupe['statut'] ?? 'actif'),
            'approval_statut' => $isApproved ? 'approuve' : 'en_cours',
        ];
        if ($photo['url'] !== null) {
            $this->deleteGroupPhotoFileIfManaged($existingImg);
            $updateData['image_url'] = $photo['url'];
        }

        $ok = $groupeModel->updateGroupe($id, $updateData);

        $msgOk = $isApproved
            ? 'Groupe mis a jour.'
            : 'Groupe mis a jour. Etat: en cours d approbation jusqu a la decision de l administrateur.';
        $this->setFlash($ok ? 'success' : 'error', $ok ? $msgOk : 'Echec de mise a jour.');
        $this->foRedirect('groupes/' . $id);
    }

    private function studentGroupesJoin($groupeModel, int $id): void
    {
        $groupe = $groupeModel->findById($id);
        if (!$groupe || ($groupe['approval_statut'] ?? '') !== 'approuve') {
            $this->setFlash('error', 'Groupe non disponible.');
            $this->foRedirect('groupes');
            return;
        }

        $uid = (int) $_SESSION['user_id'];
        if (!$groupeModel->estMembre($id, $uid)) {
            $groupeModel->ajouterMembre($id, $uid, 'membre');
            $this->setFlash('success', 'Vous avez rejoint le groupe.');
        } else {
            $this->setFlash('error', 'Vous etes deja membre.');
        }
        $this->foRedirect('groupes/' . $id);
    }

    private function extractGroupePayload(): array
    {
        return [
            'nom_groupe' => trim((string) ($_POST['nom_groupe'] ?? '')),
            'description' => trim((string) ($_POST['description'] ?? '')),
            'statut' => trim((string) ($_POST['statut'] ?? 'actif')),
        ];
    }

    private function validateGroupePayload(array $payload): array
    {
        $errors = [];
        $nom = (string) ($payload['nom_groupe'] ?? '');
        $desc = (string) ($payload['description'] ?? '');

        if ($nom === '') {
            $errors['nom_groupe'] = 'This field cannot be empty.';
        } elseif (strlen($nom) < 3) {
            $errors['nom_groupe'] = 'Group name must be between 3 and 100 characters.';
        } elseif (strlen($nom) > 100) {
            $errors['nom_groupe'] = 'Group name must not exceed 100 characters.';
        }

        if ($desc === '') {
            $errors['description'] = 'This field cannot be empty.';
        } elseif (strlen($desc) < 10) {
            $errors['description'] = 'Description must be between 10 and 3000 characters.';
        } elseif (strlen($desc) > 3000) {
            $errors['description'] = 'Description must not exceed 3000 characters.';
        }

        if (!in_array($payload['statut'], ['actif', 'archivé'], true)) {
            $errors['statut'] = 'Statut invalide.';
        }

        return $errors;
    }

    private function validateDiscussionPayload(array $payload, array $ownedGroupIds): array
    {
        $errors = [];
        $titre = (string) ($payload['titre'] ?? '');
        $contenu = (string) ($payload['contenu'] ?? '');
        $idGroupe = (int) ($payload['id_groupe'] ?? 0);

        if ($idGroupe === 0) {
            $errors['id_groupe'] = 'Please select a group.';
        } elseif (!in_array($idGroupe, $ownedGroupIds, true)) {
            $errors['id_groupe'] = 'You can only post in groups that you created.';
        }

        if ($titre === '') {
            $errors['titre'] = 'This field cannot be empty.';
        } elseif (strlen($titre) < 3) {
            $errors['titre'] = 'Title must be between 3 and 200 characters.';
        } elseif (strlen($titre) > 200) {
            $errors['titre'] = 'Title must not exceed 200 characters.';
        }

        if ($contenu === '') {
            $errors['contenu'] = 'This field cannot be empty.';
        } elseif (strlen($contenu) < 5) {
            $errors['contenu'] = 'Content must be between 5 and 5000 characters.';
        } elseif (strlen($contenu) > 5000) {
            $errors['contenu'] = 'Content must not exceed 5000 characters.';
        }

        return $errors;
    }

    private function studentDiscussionsStore($discussionModel, $groupeModel): void
    {
        $payload = [
            'titre' => trim((string) ($_POST['titre'] ?? '')),
            'contenu' => trim((string) ($_POST['contenu'] ?? '')),
            'id_groupe' => (int) ($_POST['id_groupe'] ?? 0),
        ];
        $groups = $groupeModel->getByCreator((int) $_SESSION['user_id']);
        $ownedGroupIds = array_map(static fn($g) => (int) ($g['id_groupe'] ?? 0), $groups);
        $errors = $this->validateDiscussionPayload($payload, $ownedGroupIds);

        if (!empty($errors)) {
            $_SESSION['discussion_errors'] = $errors;
            $_SESSION['discussion_old'] = $_POST;
            $this->foRedirect('discussions/create');
            return;
        }

        $ok = $discussionModel->createForGroup(
            $payload['id_groupe'],
            (int) $_SESSION['user_id'],
            htmlspecialchars($payload['titre'], ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($payload['contenu'], ENT_QUOTES, 'UTF-8')
        );
        $this->setFlash($ok ? 'success' : 'error', $ok ? 'Discussion creee. Statut: en cours — en attente de validation par l administrateur.' : 'Failed to create discussion.');
        $this->foRedirect('discussions');
    }

    private function studentDiscussionsEdit($discussionModel, $groupeModel, int $discussionId): void
    {
        $discussion = $discussionModel->findOwnedBy($discussionId, (int) $_SESSION['user_id']);
        if (!$discussion) {
            $this->setFlash('error', 'Discussion not found.');
            $this->foRedirect('discussions');
            return;
        }
        $old = $_SESSION['discussion_old'] ?? [];
        unset($_SESSION['discussion_old']);
        $data = $this->withFoContext([
            'title' => 'Edit Discussion - APPOLIOS',
            'studentSidebarActive' => 'discussions',
            'discussion' => $discussion,
            'groups' => $groupeModel->getByCreator((int) $_SESSION['user_id']),
            'old' => $old,
            'errors' => $_SESSION['discussion_errors'] ?? [],
            'flash' => $this->getFlash()
        ], 'discussions');
        unset($_SESSION['discussion_errors']);
        $this->view('FrontOffice/student/discussions/edit', $data);
    }

    private function studentDiscussionsUpdate($discussionModel, $groupeModel, int $discussionId): void
    {
        $existing = $discussionModel->findOwnedBy($discussionId, (int) $_SESSION['user_id']);
        if (!$existing) {
            $this->setFlash('error', 'Discussion not found.');
            $this->foRedirect('discussions');
            return;
        }

        $payload = [
            'titre' => trim((string) ($_POST['titre'] ?? '')),
            'contenu' => trim((string) ($_POST['contenu'] ?? '')),
            'id_groupe' => (int) ($_POST['id_groupe'] ?? 0),
        ];
        $groups = $groupeModel->getByCreator((int) $_SESSION['user_id']);
        $ownedGroupIds = array_map(static fn($g) => (int) ($g['id_groupe'] ?? 0), $groups);
        $errors = $this->validateDiscussionPayload($payload, $ownedGroupIds);

        if (!empty($errors)) {
            $_SESSION['discussion_errors'] = $errors;
            $_SESSION['discussion_old'] = $_POST;
            $this->foRedirect('discussions/' . $discussionId . '/edit');
            return;
        }

        $ok = $discussionModel->updateOwned(
            $discussionId,
            (int) $_SESSION['user_id'],
            htmlspecialchars($payload['titre'], ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($payload['contenu'], ENT_QUOTES, 'UTF-8'),
            $payload['id_groupe']
        );
        $this->setFlash($ok ? 'success' : 'error', $ok ? 'Discussion mise a jour. Statut: en cours — en attente de validation par l administrateur.' : 'Failed to update discussion.');
        $this->foRedirect('discussions');
    }

    /**
     * Author of the discussion OR creator of the parent group may delete.
     */
    private function studentDiscussionDeleteAuthorized($discussionModel, $groupeModel, int $discussionId, ?int $mustBelongToGroupId = null): bool
    {
        $uid = (int) $_SESSION['user_id'];
        $row = $discussionModel->getRowByPk($discussionId);
        if (!$row) {
            return false;
        }
        $authorCol = isset($row['id_auteur']) ? 'id_auteur' : (isset($row['created_by']) ? 'created_by' : null);
        $groupCol = isset($row['id_groupe']) ? 'id_groupe' : (isset($row['group_id']) ? 'group_id' : null);
        if ($authorCol === null || $groupCol === null) {
            return false;
        }
        $gid = (int) $row[$groupCol];
        if ($mustBelongToGroupId !== null && $gid !== $mustBelongToGroupId) {
            return false;
        }
        $groupe = $groupeModel->findById($gid);
        if (!$groupe) {
            return false;
        }
        $isAuthor = (int) $row[$authorCol] === $uid;
        $creatorId = (int) ($groupe['id_createur'] ?? $groupe['created_by'] ?? 0);
        $isGroupOwner = $creatorId === $uid;
        if (!$isAuthor && !$isGroupOwner) {
            return false;
        }
        return $discussionModel->deleteByPrimaryKey($discussionId);
    }

    private function studentGroupDiscussionDeleteFromPage($groupeModel, $discussionModel, int $groupId, int $discussionId): void
    {
        $ok = $this->studentDiscussionDeleteAuthorized($discussionModel, $groupeModel, $discussionId, $groupId);
        $this->setFlash($ok ? 'success' : 'error', $ok ? 'Discussion supprimee.' : 'Suppression impossible (non autorisee).');
        $this->foRedirect('groupes/' . $groupId);
    }

    private function studentGroupesDelete($groupeModel, $discussionModel, int $id): void
    {
        $uid = (int) $_SESSION['user_id'];
        $groupe = $groupeModel->findById($id);
        if (!$groupe || (int) ($groupe['id_createur'] ?? $groupe['created_by'] ?? 0) !== $uid) {
            $this->setFlash('error', 'Suppression reservee au createur du groupe.');
            $this->foRedirect('groupes');
            return;
        }

        $img = $this->groupeImageUrlFromRow($groupe);
        $this->deleteGroupPhotoFileIfManaged($img);

        $discussionModel->deleteAllForGroup($id);
        $groupeModel->deleteMembresForGroup($id);
        $ok = $groupeModel->delete($id);

        $this->setFlash($ok ? 'success' : 'error', $ok ? 'Groupe supprime.' : 'Impossible de supprimer le groupe.');
        $this->foRedirect('groupes');
    }
}