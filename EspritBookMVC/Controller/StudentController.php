<?php
/**
 * APPOLIOS Student Controller
 * Handles student dashboard and course enrollment
 */

require_once __DIR__ . '/../Controller/BaseController.php';
require_once __DIR__ . '/../Model/Course.php';
require_once __DIR__ . '/../Model/Enrollment.php';
require_once __DIR__ . '/../Model/Groupe.php';
require_once __DIR__ . '/../Model/Discussion.php';

class StudentController extends BaseController {
    private ?bool $hasEvenementRessourcesTable = null;

    private function getDb(): PDO {
        static $pdo = null;
        if ($pdo === null) {
            $pdo = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET,
                DB_USER, DB_PASS,
                [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        }
        return $pdo;
    }

    private function queryApprovedEvenements(): array {
        return $this->getDb()->query(
            "SELECT e.*, u.name as creator_name
             FROM evenements e
             JOIN users u ON e.created_by = u.id
             WHERE e.approval_status = 'approved'
             ORDER BY COALESCE(CONCAT(e.date_debut,' ',e.heure_debut), e.event_date) ASC"
        )->fetchAll();
    }

    private function queryEvenementWithCreator(int $id): array|false {
        $st = $this->getDb()->prepare(
            "SELECT e.*, u.name as creator_name, u.role as creator_role
             FROM evenements e
             JOIN users u ON u.id = e.created_by
             WHERE e.id = ? LIMIT 1"
        );
        $st->execute([$id]);
        return $st->fetch();
    }

    private function queryRessourcesByType(string $type, int $evenementId): array {
        if (!$this->hasEvenementRessourcesTable()) {
            return [];
        }

        $st = $this->getDb()->prepare(
            "SELECT r.*, u.name as creator_name
             FROM evenement_ressources r
             JOIN users u ON r.created_by = u.id
             WHERE r.type = ? AND r.evenement_id = ?
             ORDER BY r.created_at DESC"
        );
        $st->execute([$type, $evenementId]);
        return $st->fetchAll();
    }

    private function hasEvenementRessourcesTable(): bool {
        if ($this->hasEvenementRessourcesTable !== null) {
            return $this->hasEvenementRessourcesTable;
        }

        try {
            $st = $this->getDb()->query("SHOW TABLES LIKE 'evenement_ressources'");
            $this->hasEvenementRessourcesTable = $st->fetch() !== false;
        } catch (PDOException $e) {
            $this->hasEvenementRessourcesTable = false;
        }

        return $this->hasEvenementRessourcesTable;
    }

    private function withFoContext(array $data, string $teacherNav): array
    {
        $data['foPrefix'] = $this->frontOfficeRoutePrefix();
        if ($data['foPrefix'] === 'teacher') {
            $data['teacherSidebarActive'] = $teacherNav;
        }
        return $data;
    }

    private function foRedirect(string $path): void
    {
        $this->redirect($this->frontOfficeRoutePrefix() . '/' . ltrim($path, '/'));
    }

    private function getApprovedOwnedGroups($groupeModel): array
    {
        $groups = $groupeModel->getByCreator((int) $_SESSION['user_id']);
        return array_values(array_filter(
            $groups,
            static function (array $g): bool {
                $a = (string) ($g['approval_statut'] ?? $g['approval_status'] ?? '');
                return $a === 'approuve';
            }
        ));
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
            $approvedOwnedGroups = $this->getApprovedOwnedGroups($groupeModel);
            $data = $this->withFoContext([
                'title' => 'Create Discussion - APPOLIOS',
                'studentSidebarActive' => 'discussions',
                'groups' => $approvedOwnedGroups,
                'old' => $old,
                'errors' => $_SESSION['discussion_errors'] ?? [],
                'flash' => $this->getFlash()
            ], 'discussions');
            unset($_SESSION['discussion_errors']);
            $this->view('FrontOffice/student/discussions_create', $data);
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
        if ($id > 0 && $second === 'chat') {
            $this->studentDiscussionsChat($discussionModel, $groupeModel, $id);
            return;
        }
        if ($id > 0 && $second === 'upload' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->studentDiscussionsUploadAttachment($discussionModel, $groupeModel, $id);
            return;
        }
        if ($id > 0 && $second === 'delete') {
            $ok = $this->studentDiscussionDeleteAuthorized($discussionModel, $groupeModel, $id);
            $this->setFlash($ok ? 'success' : 'error', $ok ? 'Discussion supprimee.' : 'Suppression impossible (non autorisee).');
            $this->foRedirect('discussions');
            return;
        }

        $data = $this->withFoContext([
            'title' => 'Discussions - APPOLIOS',
            'studentSidebarActive' => 'discussions',
            'discussions' => $discussionModel->getVisibleForUser((int) $_SESSION['user_id']),
            'currentUserId' => (int) $_SESSION['user_id'],
            'flash' => $this->getFlash()
        ], 'discussions');
        $this->view('FrontOffice/student/discussions_index', $data);
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

        $evenements = $this->queryApprovedEvenements();

        $data = [
            'title'            => 'My Dashboard - APPOLIOS',
            'description'      => 'Student evenement dashboard',
            'userName'         => $_SESSION['user_name'],
            'evenements'       => $evenements,
            'participationMap' => $this->queryParticipationMap((int)$_SESSION['user_id']),
            'participations'   => $this->queryMyParticipations((int)$_SESSION['user_id']),
            'flash'            => $this->getFlash()
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

        $data = [
            'title'            => 'Evenements - APPOLIOS',
            'description'      => 'Browse upcoming evenements',
            'userName'         => $_SESSION['user_name'],
            'evenements'       => $this->queryApprovedEvenements(),
            'participationMap' => $this->queryParticipationMap((int)$_SESSION['user_id']),
            'participations'   => $this->queryMyParticipations((int)$_SESSION['user_id']),
            'flash'            => $this->getFlash()
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

        $evenement = $this->queryEvenementWithCreator((int)$id);
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

        $grouped = [
            'rules'     => $this->queryRessourcesByType('rule',     (int)$id),
            'materiels' => $this->queryRessourcesByType('materiel', (int)$id),
            'plans'     => $this->queryRessourcesByType('plan',     (int)$id),
        ];

        $data = [
            'title' => (($evenement['titre'] ?? '') ?: ($evenement['title'] ?? 'Evenement')) . ' - APPOLIOS',
            'description' => $evenement['description'] ?? 'Evenement details',
            'evenement' => $evenement,
            'rules' => $grouped['rules'],
            'materiels' => $grouped['materiels'],
            'plans' => $grouped['plans'],
            'participation' => $this->queryFindParticipation((int)$id, (int)$_SESSION['user_id']),
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
     * My events page (Events student is participating in)
     */
    public function myEvents() {
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Please login to view your events.');
            $this->redirect('login');
            return;
        }

        $studentId = (int) $_SESSION['user_id'];
        $participations = $this->queryMyParticipations($studentId);

        $data = [
            'title'          => 'My Events - APPOLIOS',
            'description'    => 'Events you are participating in',
            'userName'       => $_SESSION['user_name'],
            'participations' => $participations,
            'flash'          => $this->getFlash()
        ];

        $this->view('FrontOffice/student/my_events', $data);
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

    /**
     * Edit profile page
     */
    public function editProfile() {
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Please login to edit your profile.');
            $this->redirect('login');
            return;
        }

        require_once __DIR__ . '/../Model/User.php';
        $userModel = $this->model('User');
        $user = $userModel->findById($_SESSION['user_id']);

        $data = [
            'title' => 'Edit Profile - APPOLIOS',
            'description' => 'Edit your profile information',
            'user' => $user,
            'flash' => $this->getFlash()
        ];

        $this->view('FrontOffice/student/edit_profile', $data);
    }

    /**
     * Update profile
     */
    public function updateProfile() {
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Please login to update your profile.');
            $this->redirect('login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('student/edit-profile');
            return;
        }

        $name = $this->sanitize($_POST['name'] ?? '');
        $email = $this->sanitize($_POST['email'] ?? '');
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $errors = [];

        // Validate name
        if (empty($name)) {
            $errors[] = 'Full name is required.';
        }

        // Validate email
        if (empty($email)) {
            $errors[] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        }

        require_once __DIR__ . '/../Model/User.php';
        $userModel = $this->model('User');
        $currentUser = $userModel->findById($_SESSION['user_id']);

        // Check if email is taken by another user
        if ($email !== $currentUser['email']) {
            if ($userModel->emailExists($email)) {
                $errors[] = 'This email is already taken.';
            }
        }

        // Validate password change if requested
        $updatePassword = false;
        if (!empty($newPassword)) {
            if (empty($currentPassword)) {
                $errors[] = 'Current password is required to change password.';
            } elseif (!password_verify($currentPassword, $currentUser['password'])) {
                $errors[] = 'Current password is incorrect.';
            } elseif (strlen($newPassword) < 6) {
                $errors[] = 'New password must be at least 6 characters.';
            } elseif ($newPassword !== $confirmPassword) {
                $errors[] = 'New password and confirmation do not match.';
            } else {
                $updatePassword = true;
            }
        }

        if (!empty($errors)) {
            $this->setFlash('error', implode('<br>', $errors));
            $this->redirect('student/edit-profile');
            return;
        }

        // Update user data
        $updateData = [
            'name' => $name,
            'email' => $email
        ];

        if ($updatePassword) {
            $updateData['password'] = password_hash($newPassword, PASSWORD_DEFAULT, ['cost' => HASH_COST]);
        }

        if ($userModel->update($_SESSION['user_id'], $updateData)) {
            // Update session data
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;

            $this->setFlash('success', 'Profile updated successfully!');
        } else {
            $this->setFlash('error', 'Failed to update profile. Please try again.');
        }

        $this->redirect('student/profile');
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
        $this->view('FrontOffice/student/groupes_index', $data);
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
        $this->view('FrontOffice/student/groupes_create', $data);
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
        $this->view('FrontOffice/student/groupes_show', $data);
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
        if ((string) ($groupe['approval_statut'] ?? $groupe['approval_status'] ?? '') !== 'approuve') {
            $this->setFlash('error', 'You can create discussions only after the group is approved.');
            $this->foRedirect('groupes/' . $groupId);
            return;
        }

        $payload = [
            'titre' => trim((string) ($_POST['titre'] ?? '')),
            'contenu' => trim((string) ($_POST['contenu'] ?? '')),
        ];
        $errors = [];
        if ($payload['titre'] === '' || strlen($payload['titre']) < 3 || strlen($payload['titre']) > 200) {
            $errors['titre'] = 'Title must be between 3 and 200 characters.';
        }
        if ($payload['contenu'] === '' || strlen($payload['contenu']) < 5 || strlen($payload['contenu']) > 5000) {
            $errors['contenu'] = 'Content must be between 5 and 5000 characters.';
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
        $this->view('FrontOffice/student/groupes_edit', $data);
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
        $this->setFlash($ok ? 'success' : 'error', $ok ? 'Groupe mis a jour.' : 'Echec de mise a jour.');
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
        if ($payload['nom_groupe'] === '') {
            $errors['nom_groupe'] = 'Group name should not be empty.';
        } elseif (strlen($payload['nom_groupe']) < 3 || strlen($payload['nom_groupe']) > 100) {
            $errors['nom_groupe'] = 'Group name must be between 3 and 100 characters.';
        }
        if ($payload['description'] === '') {
            $errors['description'] = 'Description should not be empty.';
        } elseif (strlen($payload['description']) < 10 || strlen($payload['description']) > 3000) {
            $errors['description'] = 'Description must be between 10 and 3000 characters.';
        }
        if (!in_array($payload['statut'], ['actif', 'archivé'], true)) {
            $errors['statut'] = 'Statut invalide.';
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
        $groups = $this->getApprovedOwnedGroups($groupeModel);
        $ownedGroupIds = array_map(static fn($g) => (int) ($g['id_groupe'] ?? 0), $groups);
        $errors = [];
        if ($payload['id_groupe'] === 0 || !in_array($payload['id_groupe'], $ownedGroupIds, true)) {
            $errors['id_groupe'] = 'Please select an approved group that you own.';
        }
        if ($payload['titre'] === '') {
            $errors['titre'] = 'Discussion title should not be empty.';
        } elseif (strlen($payload['titre']) < 3 || strlen($payload['titre']) > 200) {
            $errors['titre'] = 'Title must be between 3 and 200 characters.';
        }
        if ($payload['contenu'] === '') {
            $errors['contenu'] = 'Discussion content should not be empty.';
        } elseif (strlen($payload['contenu']) < 5 || strlen($payload['contenu']) > 5000) {
            $errors['contenu'] = 'Content must be between 5 and 5000 characters.';
        }
        if (!empty($errors)) {
            $_SESSION['discussion_errors'] = $errors;
            $_SESSION['discussion_old'] = $_POST;
            $this->foRedirect('discussions/create');
            return;
        }
        $ok = $discussionModel->createForGroup($payload['id_groupe'], (int) $_SESSION['user_id'], htmlspecialchars($payload['titre'], ENT_QUOTES, 'UTF-8'), htmlspecialchars($payload['contenu'], ENT_QUOTES, 'UTF-8'));
        $this->setFlash($ok ? 'success' : 'error', $ok ? 'Discussion creee.' : 'Failed to create discussion.');
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
        $approval = (string) ($discussion['approval_statut'] ?? $discussion['approval_status'] ?? 'approuve');
        if ($approval !== 'approuve') {
            $this->setFlash('error', 'This discussion is not approved yet. You can use it after admin approval.');
            $this->foRedirect('discussions');
            return;
        }
        $old = $_SESSION['discussion_old'] ?? [];
        unset($_SESSION['discussion_old']);
        $data = $this->withFoContext([
            'title' => 'Edit Discussion - APPOLIOS',
            'studentSidebarActive' => 'discussions',
            'discussion' => $discussion,
            'groups' => $this->getApprovedOwnedGroups($groupeModel),
            'old' => $old,
            'errors' => $_SESSION['discussion_errors'] ?? [],
            'flash' => $this->getFlash()
        ], 'discussions');
        unset($_SESSION['discussion_errors']);
        $this->view('FrontOffice/student/discussions_edit', $data);
    }

    private function studentDiscussionsUpdate($discussionModel, $groupeModel, int $discussionId): void
    {
        $existing = $discussionModel->findOwnedBy($discussionId, (int) $_SESSION['user_id']);
        if (!$existing) {
            $this->setFlash('error', 'Discussion not found.');
            $this->foRedirect('discussions');
            return;
        }
        $approval = (string) ($existing['approval_statut'] ?? $existing['approval_status'] ?? 'approuve');
        if ($approval !== 'approuve') {
            $this->setFlash('error', 'This discussion is not approved yet. You can use it after admin approval.');
            $this->foRedirect('discussions');
            return;
        }
        $payload = [
            'titre' => trim((string) ($_POST['titre'] ?? '')),
            'contenu' => trim((string) ($_POST['contenu'] ?? '')),
            'id_groupe' => (int) ($_POST['id_groupe'] ?? 0),
        ];
        $groups = $this->getApprovedOwnedGroups($groupeModel);
        $ownedGroupIds = array_map(static fn($g) => (int) ($g['id_groupe'] ?? 0), $groups);
        $errors = [];
        if ($payload['id_groupe'] === 0 || !in_array($payload['id_groupe'], $ownedGroupIds, true)) {
            $errors['id_groupe'] = 'Please select an approved group that you own.';
        }
        if ($payload['titre'] === '') {
            $errors['titre'] = 'Discussion title should not be empty.';
        } elseif (strlen($payload['titre']) < 3 || strlen($payload['titre']) > 200) {
            $errors['titre'] = 'Title must be between 3 and 200 characters.';
        }
        if ($payload['contenu'] === '') {
            $errors['contenu'] = 'Discussion content should not be empty.';
        } elseif (strlen($payload['contenu']) < 5 || strlen($payload['contenu']) > 5000) {
            $errors['contenu'] = 'Content must be between 5 and 5000 characters.';
        }
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
        $this->setFlash($ok ? 'success' : 'error', $ok ? 'Discussion mise a jour.' : 'Failed to update discussion.');
        $this->foRedirect('discussions');
    }

    private function studentDiscussionsChat($discussionModel, $groupeModel, int $discussionId): void
    {
        $discussion = $discussionModel->getRowByPk($discussionId);
        if (!$discussion) {
            $this->setFlash('error', 'Discussion not found.');
            $this->foRedirect('discussions');
            return;
        }

        $approval = (string) ($discussion['approval_statut'] ?? $discussion['approval_status'] ?? 'approuve');
        if ($approval !== 'approuve') {
            $this->setFlash('error', 'Live chat is available only after discussion approval.');
            $this->foRedirect('discussions');
            return;
        }

        $groupId = (int) ($discussion['id_groupe'] ?? $discussion['group_id'] ?? 0);
        $group = $groupeModel->findById($groupId);
        if (!$group) {
            $this->setFlash('error', 'Parent group not found.');
            $this->foRedirect('discussions');
            return;
        }

        $uid = (int) ($_SESSION['user_id'] ?? 0);
        $isOwner = (int) ($group['id_createur'] ?? $group['created_by'] ?? 0) === $uid;
        $isMember = $groupeModel->estMembre($groupId, $uid);
        if (!$isOwner && !$isMember) {
            $this->setFlash('error', 'You must join the group to access live chat.');
            $this->foRedirect('groupes/' . $groupId);
            return;
        }

        $data = $this->withFoContext([
            'title' => 'Live Chat - APPOLIOS',
            'studentSidebarActive' => 'discussions',
            'discussion' => $discussion,
            'group' => $group,
            'socketUrl' => SOCKET_IO_URL,
            'chatRoom' => 'discussion_' . $discussionId,
            'currentUserId' => $uid,
            'currentUserName' => (string) ($_SESSION['user_name'] ?? 'User'),
            'flash' => $this->getFlash(),
        ], 'discussions');
        $this->view('FrontOffice/student/discussions_chat', $data);
    }

    private function studentDiscussionsUploadAttachment($discussionModel, $groupeModel, int $discussionId): void
    {
        $discussion = $discussionModel->getRowByPk($discussionId);
        if (!$discussion) {
            $this->jsonResponse(['ok' => false, 'error' => 'Discussion not found.'], 404);
        }

        $approval = (string) ($discussion['approval_statut'] ?? $discussion['approval_status'] ?? 'approuve');
        if ($approval !== 'approuve') {
            $this->jsonResponse(['ok' => false, 'error' => 'Discussion is not approved yet.'], 403);
        }

        $groupId = (int) ($discussion['id_groupe'] ?? $discussion['group_id'] ?? 0);
        $group = $groupeModel->findById($groupId);
        if (!$group) {
            $this->jsonResponse(['ok' => false, 'error' => 'Parent group not found.'], 404);
        }

        $uid = (int) ($_SESSION['user_id'] ?? 0);
        $isOwner = (int) ($group['id_createur'] ?? $group['created_by'] ?? 0) === $uid;
        $isMember = $groupeModel->estMembre($groupId, $uid);
        if (!$isOwner && !$isMember) {
            $this->jsonResponse(['ok' => false, 'error' => 'You must join the group first.'], 403);
        }

        $upload = $this->handleChatAttachmentUpload('attachment');
        if (!$upload['ok']) {
            $this->jsonResponse(['ok' => false, 'error' => (string) $upload['error']], 422);
        }

        $this->jsonResponse([
            'ok' => true,
            'data' => [
                'url' => $upload['url'],
                'fileName' => $upload['fileName'],
                'mime' => $upload['mime'],
                'size' => $upload['size'],
                'messageType' => $upload['messageType'],
            ],
        ]);
    }

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

    // =========================================================================
    // PUBLIC PARTICIPATION ACTIONS
    // =========================================================================

    /**
     * Student requests to participate in an event.
     */
    public function participate($id) {
        if (!$this->isLoggedIn()) {
            $this->redirect('login');
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('student/evenements');
            return;
        }

        $eventId   = (int) $id;
        $studentId = (int) $_SESSION['user_id'];

        $event = $this->queryApprovedEventById($eventId);
        if (!$event) {
            $this->setFlash('error', 'Event not found or not available.');
            $this->redirect('student/evenements');
            return;
        }

        $existing = $this->queryFindParticipation($eventId, $studentId);
        if ($existing) {
            $this->setFlash('info', 'You already requested participation for this event.');
            $this->redirect('student/evenements');
            return;
        }

        if ($this->queryCreateParticipation($eventId, $studentId)) {
            $this->setFlash('success', 'Participation request sent! Waiting for teacher approval.');
        } else {
            $this->setFlash('error', 'Failed to send participation request.');
        }

        $this->redirect('student/evenements');
    }

    /**
     * Student cancels a pending participation.
     */
    public function cancelParticipation($id) {
        if (!$this->isLoggedIn()) {
            $this->redirect('login');
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('student/evenements');
            return;
        }

        $eventId   = (int) $id;
        $studentId = (int) $_SESSION['user_id'];

        $existing = $this->queryFindParticipation($eventId, $studentId);
        if (!$existing || $existing['details'] !== 'pending') {
            $this->setFlash('error', 'Only pending participation requests can be cancelled.');
            $this->redirect('student/evenements');
            return;
        }

        if ($this->queryCancelParticipation($eventId, $studentId)) {
            $this->setFlash('success', 'Participation request cancelled.');
        } else {
            $this->setFlash('error', 'Failed to cancel participation.');
        }

        $this->redirect('student/evenements');
    }

    // =========================================================================
    // PRIVATE DB QUERY METHODS — Participation (via evenement_ressources)
    // =========================================================================

    /**
     * Returns [evenement_id => status] map for the student.
     * Uses type='participation', details=status in evenement_ressources.
     */
    private function queryParticipationMap(int $studentId): array {
        if (!$this->hasEvenementRessourcesTable()) {
            return [];
        }

        $st = $this->getDb()->prepare(
            "SELECT evenement_id, details as status
             FROM evenement_ressources
             WHERE type = 'participation' AND created_by = ?"
        );
        $st->execute([$studentId]);
        $map = [];
        foreach ($st->fetchAll() as $row) {
            $map[(int)$row['evenement_id']] = $row['status'];
        }
        return $map;
    }

    private function queryApprovedEventById(int $id): array|false {
        $st = $this->getDb()->prepare(
            "SELECT * FROM evenements WHERE id = ? AND approval_status = 'approved' LIMIT 1"
        );
        $st->execute([$id]);
        return $st->fetch();
    }

    private function queryFindParticipation(int $eventId, int $studentId): array|false {
        if (!$this->hasEvenementRessourcesTable()) {
            return false;
        }

        $st = $this->getDb()->prepare(
            "SELECT * FROM evenement_ressources
             WHERE evenement_id = ? AND created_by = ? AND type = 'participation' LIMIT 1"
        );
        $st->execute([$eventId, $studentId]);
        return $st->fetch();
    }

    private function queryCreateParticipation(int $eventId, int $studentId): bool {
        if (!$this->hasEvenementRessourcesTable()) {
            return false;
        }

        try {
            $stUser = $this->getDb()->prepare("SELECT name FROM users WHERE id = ? LIMIT 1");
            $stUser->execute([$studentId]);
            $user = $stUser->fetch();
            $studentName = $user['name'] ?? 'Student';

            $st = $this->getDb()->prepare(
                "INSERT INTO evenement_ressources (evenement_id, type, title, details, created_by, created_at)
                 VALUES (?, 'participation', ?, 'pending', ?, NOW())"
            );
            return $st->execute([$eventId, $studentName, $studentId]);
        } catch (PDOException $e) { return false; }
    }

    private function queryCancelParticipation(int $eventId, int $studentId): bool {
        if (!$this->hasEvenementRessourcesTable()) {
            return false;
        }

        $st = $this->getDb()->prepare(
            "DELETE FROM evenement_ressources
             WHERE evenement_id = ? AND created_by = ? AND type = 'participation' AND details = 'pending'"
        );
        return $st->execute([$eventId, $studentId]);
    }

    /**
     * Fetch events where the student has a participation record.
     */
    private function queryMyParticipations(int $studentId): array {
        if (!$this->hasEvenementRessourcesTable()) {
            return [];
        }

        $st = $this->getDb()->prepare(
            "SELECT e.*, er.id as p_id, er.details as p_status, er.rejection_reason, er.created_at as p_date, er.updated_at as p_update_date, u.name as creator_name
             FROM evenements e
             JOIN evenement_ressources er ON e.id = er.evenement_id
             JOIN users u ON e.created_by = u.id
             WHERE er.type = 'participation' AND er.created_by = ?
             ORDER BY er.created_at DESC"
        );
        $st->execute([$studentId]);
        return $st->fetchAll();
    }

    public function downloadTicket($pId) {
        if (!$this->isLoggedIn()) { $this->redirect('auth/login'); return; }
        if (!$this->hasEvenementRessourcesTable()) {
            $this->setFlash('error', 'Ticket system is not available yet.');
            $this->redirect('student/my-events');
            return;
        }
        $pId = (int)$pId;
        $studentId = $_SESSION['user_id'];
        $st = $this->getDb()->prepare(
            "SELECT er.*, e.title as event_title, e.location as event_location, 
                    COALESCE(CONCAT(e.date_debut, ' ', e.heure_debut), e.event_date) as event_full_date,
                    u.name as student_name, u.email as student_email
             FROM evenement_ressources er
             JOIN evenements e ON er.evenement_id = e.id
             JOIN users u ON er.created_by = u.id
             WHERE er.id = ? AND er.created_by = ? AND er.type = 'participation' AND er.details = 'approved'
             LIMIT 1"
        );
        $st->execute([$pId, $studentId]);
        $ticket = $st->fetch();
        if (!$ticket) {
            $this->setFlash('error', 'Ticket not found or not approved yet.');
            $this->redirect('student/my-participations');
            return;
        }

        // Create QR Data
        $qrData = "Ticket ID: " . str_pad($ticket['id'], 6, '0', STR_PAD_LEFT) . "\n"
                . "Event: " . $ticket['event_title'] . "\n"
                . "Attendee: " . $ticket['student_name'] . "\n"
                . "Status: Approved by Appolios";
        $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($qrData);

        header('Content-Type: text/html; charset=utf-8');
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Event Ticket - <?= htmlspecialchars($ticket['event_title']) ?></title>
            <style>
                @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
                * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
                body { background: #f1f5f9; padding: 40px; display: flex; justify-content: center; min-height: 100vh; align-items: center; }
                .ticket-container { background: white; width: 700px; border-radius: 24px; overflow: hidden; box-shadow: 0 20px 50px rgba(0,0,0,0.1); display: flex; position: relative; }
                .ticket-left { flex: 1; padding: 40px; border-right: 2px dashed #e2e8f0; }
                .ticket-right { width: 220px; background: #2B4865; color: white; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 20px; text-align: center; }
                .brand { color: #548CA8; font-weight: 800; font-size: 1.2rem; margin-bottom: 30px; display: block; }
                .event-badge { background: #e0f2fe; color: #0369a1; padding: 6px 14px; border-radius: 100px; font-size: 0.75rem; font-weight: 700; margin-bottom: 15px; display: inline-block; }
                h1 { font-size: 2rem; color: #1e293b; line-height: 1.2; margin-bottom: 25px; }
                .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 30px; }
                .info-item label { display: block; font-size: 0.7rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; margin-bottom: 5px; }
                .info-item span { display: block; font-size: 1rem; color: #334155; font-weight: 600; }
                .qr-box { width: 140px; height: 140px; background: white; border-radius: 12px; margin-bottom: 20px; display: flex; align-items: center; justify-content: center; overflow: hidden; border: 6px solid #355C7D; }
                .qr-box img { width: 100%; height: 100%; object-fit: contain; }
                .status-approved { color: #10b981; font-weight: 800; font-size: 1.2rem; transform: rotate(-15deg); border: 3px solid #10b981; padding: 5px 15px; border-radius: 8px; margin-top: 20px; text-transform: uppercase; }
                .ticket-id { font-size: 0.6rem; color: rgba(255,255,255,0.5); margin-top: auto; font-family: monospace; }
                .ticket-container::before, .ticket-container::after { content: ''; position: absolute; width: 30px; height: 30px; background: #f1f5f9; border-radius: 50%; left: 465px; }
                .ticket-container::before { top: -15px; }
                .ticket-container::after { bottom: -15px; }
                @media print { body { background: white; padding: 0; } .ticket-container { box-shadow: none; border: 1px solid #e2e8f0; margin: 0 auto; } .no-print { display: none; } }
                .no-print-btn { position: fixed; top: 20px; right: 20px; background: #2B4865; color: white; border: none; padding: 12px 24px; border-radius: 12px; cursor: pointer; font-weight: 700; box-shadow: 0 10px 20px rgba(0,0,0,0.1); display: flex; align-items: center; gap: 10px; z-index: 100; transition: all 0.2s; }
                .no-print-btn:hover { background: #548CA8; transform: translateY(-2px); }
            </style>
        </head>
        <body>
            <button class="no-print-btn no-print" onclick="window.print()">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                Print / Download PDF
            </button>
            <div class="ticket-container">
                <div class="ticket-left">
                    <span class="brand">APPOLIOS</span>
                    <div class="event-badge">Official Event Pass</div>
                    <h1><?= htmlspecialchars($ticket['event_title']) ?></h1>
                    <div class="info-grid">
                        <div class="info-item" style="grid-column: 1 / -1;"><label>Event</label><span><?= htmlspecialchars($ticket['event_title']) ?></span></div>
                        <div class="info-item"><label>Attendee</label><span><?= htmlspecialchars($ticket['student_name']) ?></span></div>
                        <div class="info-item"><label>Date & Time</label><span><?= date('M d, Y - H:i', strtotime($ticket['event_full_date'])) ?></span></div>
                        <div class="info-item"><label>Location</label><span><?= htmlspecialchars($ticket['event_location'] ?: 'To be announced') ?></span></div>
                        <div class="info-item"><label>Ticket Type</label><span>Student Pass</span></div>
                    </div>
                </div>
                <div class="ticket-right">
                    <div class="qr-box">
                        <img src="<?= $qrUrl ?>" alt="Ticket QR Code">
                    </div>
                    <div style="font-size: 0.65rem; font-weight: 700; letter-spacing: 1px; color: #94a3b8; margin-top: -10px;">SCAN TO VALIDATE</div>
                    <div class="status-approved">Approved</div>
                    <div class="ticket-id">#ID-<?= str_pad($ticket['id'], 6, '0', STR_PAD_LEFT) ?></div>
                </div>
            </div>
            <script>window.onload = function() { setTimeout(() => { window.print(); }, 800); }</script>
        </body>
        </html>
        <?php
        exit;
    }
}