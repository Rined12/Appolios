<?php
/**
 * APPOLIOS Student Controller
 * Handles student dashboard and course enrollment
 */

require_once __DIR__ . '/../Controller/BaseController.php';
require_once __DIR__ . '/../Model/Repositories.php';
require_once __DIR__ . '/../View/PresentationHelpers.php';

class StudentController extends BaseController {

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

    /**
     * Reads and clears discussion composer republish keys from the session (HTTP only).
     *
     * @return array{old: array<string, mixed>, errors: array<string, mixed>}
     */
    private function consumeDiscussionComposerSession(): array
    {
        $old = $_SESSION['discussion_old'] ?? [];
        unset($_SESSION['discussion_old']);
        $errors = $_SESSION['discussion_errors'] ?? [];
        unset($_SESSION['discussion_errors']);

        return [
            'old' => is_array($old) ? $old : [],
            'errors' => is_array($errors) ? $errors : [],
        ];
    }

    public function discussions(...$params) {
        if (!$this->isLoggedIn() || !in_array($_SESSION['role'] ?? '', ['student', 'teacher'], true)) {
            $this->sessionService()->flashPersist('error', 'Access denied.');
            $this->redirect('login');
            return;
        }

        $discussionRepository = $this->model('DiscussionRepository');
        $groupeRepository = $this->model('GroupeRepository');
        $first = $params[0] ?? null;
        $second = $params[1] ?? null;

        if ($first === 'create') {
            $old = $_SESSION['discussion_old'] ?? [];
            unset($_SESSION['discussion_old']);
            $approvedOwnedGroups = $this->model('StudentQueryService')->approvedOwnedGroupsForUser($groupeRepository, (int) $_SESSION['user_id']);
            $data = $this->withFoContext([
                'title' => 'Create Discussion - APPOLIOS',
                'studentSidebarActive' => 'discussions',
                'groups' => $approvedOwnedGroups,
                'old' => $old,
                'errors' => $_SESSION['discussion_errors'] ?? [],
                'flash' => $this->sessionService()->flashConsumeForView()
            ], 'discussions');
            unset($_SESSION['discussion_errors']);
            $this->view('FrontOffice/student/discussions/create', $data);
            return;
        }

        if ($first === 'store' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->studentDiscussionsStore($discussionRepository, $groupeRepository);
            return;
        }

        $id = (int) $first;
        if ($id > 0 && $second === 'edit') {
            $this->studentDiscussionsEdit($discussionRepository, $groupeRepository, $id);
            return;
        }
        if ($id > 0 && $second === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->studentDiscussionsUpdate($discussionRepository, $groupeRepository, $id);
            return;
        }
        if ($id > 0 && $second === 'chat') {
            $this->studentDiscussionsChat($discussionRepository, $groupeRepository, $id);
            return;
        }
        if ($id > 0 && $second === 'upload' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->studentDiscussionsUploadAttachment($discussionRepository, $groupeRepository, $id);
            return;
        }
        if ($id > 0 && $second === 'delete') {
            $ok = $this->studentDiscussionDeleteAuthorized($discussionRepository, $groupeRepository, $id);
            $this->sessionService()->flashPersist($ok ? 'success' : 'error', $ok ? 'Discussion supprimee.' : 'Suppression impossible (non autorisee).');
            $this->foRedirect('discussions');
            return;
        }

        $listFilter = $this->parseStudentListQuery(200);
        $q = $listFilter['q'];
        $sort = $this->normalizeDiscussionListSort($listFilter['sort'] !== '' ? $listFilter['sort'] : 'newest');
        $discussions = $discussionRepository->fetchVisibleForUser((int) $_SESSION['user_id']);
        $discussions = array_values(array_filter(
            $discussions,
            function (array $d) use ($q): bool {
                return $this->discussionRowMatchesQuery($d, $q);
            }
        ));
        $this->sortDiscussionRows($discussions, $sort);

        $prefix = $this->frontOfficeRoutePrefix();
        $uid = (int) $_SESSION['user_id'];
        $discussionCards = DiscussionPresenter::studentIndexCards($discussions, $uid, $prefix, APP_ENTRY);
        $data = $this->withFoContext([
            'title' => 'Discussions - APPOLIOS',
            'studentSidebarActive' => 'discussions',
            'discussion_cards' => $discussionCards,
            'search_suggestions' => DiscussionPresenter::discussionSearchSuggestionsFromCards($discussionCards),
            'listQ' => $q,
            'listSort' => $sort,
            'listQueryActive' => $q !== '',
            'currentUserId' => $uid,
            'flash' => $this->sessionService()->flashConsumeForView()
        ], 'discussions');
        $this->view('FrontOffice/student/discussions/index', $data);
    }

    public function groupes(...$params) {
        if (!$this->isLoggedIn() || !in_array($_SESSION['role'] ?? '', ['student', 'teacher'], true)) {
            $this->sessionService()->flashPersist('error', 'Access denied.');
            $this->redirect('login');
            return;
        }

        $groupeRepository = $this->model('GroupeRepository');
        $first = $params[0] ?? null;
        $second = $params[1] ?? null;
        $third = $params[2] ?? null;
        $fourth = $params[3] ?? null;

        if ($first === null) {
            $this->studentGroupesIndex($groupeRepository);
            return;
        }
        if ($first === 'create') {
            $this->studentGroupesCreate();
            return;
        }
        if ($first === 'store' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->studentGroupesStore($groupeRepository);
            return;
        }

        $id = (int) $first;
        if ($id <= 0) {
            $this->sessionService()->flashPersist('error', 'Invalid group identifier.');
            $this->foRedirect('groupes');
            return;
        }
        if ($second === null) {
            $this->studentGroupesShow($groupeRepository, $id);
            return;
        }
        if ($second === 'activity-report') {
            $this->studentGroupesActivityReport($groupeRepository, $id);
            return;
        }
        if ($second === 'edit') {
            $this->studentGroupesEdit($groupeRepository, $id);
            return;
        }
        if ($second === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->studentGroupesUpdate($groupeRepository, $id);
            return;
        }
        if ($second === 'join') {
            $this->studentGroupesJoin($groupeRepository, $id);
            return;
        }
        if ($second === 'quit') {
            $this->studentGroupesQuit($groupeRepository, $id);
            return;
        }
        if ($second === 'delete') {
            $discussionRepository = $this->model('DiscussionRepository');
            $this->studentGroupesDelete($groupeRepository, $discussionRepository, $id);
            return;
        }
        if ($second === 'discussions' && $third === 'store' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->studentGroupesDiscussionStore($groupeRepository, $id);
            return;
        }
        if ($second === 'discussions' && $fourth === 'delete' && ctype_digit((string) $third) && (int) $third > 0) {
            $discussionRepository = $this->model('DiscussionRepository');
            $this->studentGroupDiscussionDeleteFromPage($groupeRepository, $discussionRepository, $id, (int) $third);
            return;
        }

        $this->sessionService()->flashPersist('error', 'Route not found.');
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
            $this->sessionService()->flashPersist('error', 'Please login to access your dashboard.');
            $this->redirect('login');
            return;
        }

        $evenements = $this->model('EvenementRepository')->findApprovedWithCreators();

        $data = [
            'title'            => 'My Dashboard - APPOLIOS',
            'description'      => 'Student evenement dashboard',
            'userName'         => $_SESSION['user_name'],
            'evenements'       => $evenements,
            'participationMap' => $this->model('EvenementRessourceRepository')->fetchParticipationMapForStudent((int)$_SESSION['user_id']),
            'participations'   => $this->model('EvenementRessourceRepository')->findMyParticipations((int)$_SESSION['user_id']),
            'flash'            => $this->sessionService()->flashConsumeForView()
        ];

        $this->view('FrontOffice/student/evenements', $data);
    }

    /**
     * Student evenements catalog page
     */
    public function evenements() {
        if (!$this->isLoggedIn()) {
            $this->sessionService()->flashPersist('error', 'Please login to access events.');
            $this->redirect('login');
            return;
        }

        $data = [
            'title'            => 'Evenements - APPOLIOS',
            'description'      => 'Browse upcoming evenements',
            'userName'         => $_SESSION['user_name'],
            'evenements'       => $this->model('EvenementRepository')->findApprovedWithCreators(),
            'participationMap' => $this->model('EvenementRessourceRepository')->fetchParticipationMapForStudent((int)$_SESSION['user_id']),
            'participations'   => $this->model('EvenementRessourceRepository')->findMyParticipations((int)$_SESSION['user_id']),
            'flash'            => $this->sessionService()->flashConsumeForView()
        ];

        $this->view('FrontOffice/student/evenements', $data);
    }

    /**
     * Student evenement detail page with resources
     */
    public function evenementDetail($id) {
        if (!$this->isLoggedIn()) {
            $this->sessionService()->flashPersist('error', 'Please login to view evenement details.');
            $this->redirect('login');
            return;
        }

        $evenement = $this->model('EvenementRepository')->findWithCreatorById((int)$id);
        if (!$evenement) {
            $this->sessionService()->flashPersist('error', 'Evenement not found.');
            $this->redirect('student/evenements');
            return;
        }

        if (($evenement['approval_status'] ?? 'approved') !== 'approved') {
            $this->sessionService()->flashPersist('error', 'This evenement is not available yet.');
            $this->redirect('student/evenements');
            return;
        }

        $grouped = [
            'rules'     => $this->model('EvenementRessourceRepository')->findByTypeAndEventForStudent('rule',     (int)$id),
            'materiels' => $this->model('EvenementRessourceRepository')->findByTypeAndEventForStudent('materiel', (int)$id),
            'plans'     => $this->model('EvenementRessourceRepository')->findByTypeAndEventForStudent('plan',     (int)$id),
        ];

        $data = [
            'title' => (($evenement['titre'] ?? '') ?: ($evenement['title'] ?? 'Evenement')) . ' - APPOLIOS',
            'description' => $evenement['description'] ?? 'Evenement details',
            'evenement' => $evenement,
            'rules' => $grouped['rules'],
            'materiels' => $grouped['materiels'],
            'plans' => $grouped['plans'],
            'participation' => $this->model('EvenementRessourceRepository')->findStudentParticipation((int)$id, (int)$_SESSION['user_id']),
            'flash' => $this->sessionService()->flashConsumeForView()
        ];

        $this->view('FrontOffice/student/evenement_detail', $data);
    }

    /**
     * Browse all available courses (for students)
     */
    public function courses() {
        // Check if logged in
        if (!$this->isLoggedIn()) {
            $this->sessionService()->flashPersist('error', 'Please login to browse courses.');
            $this->redirect('login');
            return;
        }

        // Only students can access this page
        if ($_SESSION['role'] !== 'student') {
            $this->sessionService()->flashPersist('error', 'Access denied.');
            $this->redirect('login');
            return;
        }

        $courseRepository = $this->model('CourseRepository');
        $enrollmentRepository = $this->model('EnrollmentRepository');

        // Get all courses
        $allCourses = $courseRepository->fetchAllWithCreator();

        // Get enrolled course IDs to mark them
        $enrollments = $enrollmentRepository->fetchEnrollmentsForUser($_SESSION['user_id']);
        $enrolledIds = array_column($enrollments, 'course_id');

        $data = [
            'title' => 'Browse Courses - APPOLIOS',
            'description' => 'Explore all available courses',
            'courses' => $allCourses,
            'enrolledIds' => $enrolledIds,
            'flash' => $this->sessionService()->flashConsumeForView()
        ];

        $this->view('FrontOffice/student/courses', $data);
    }

    /**
     * View course details
     */
    public function viewCourse($id) {
        if (!$this->isLoggedIn()) {
            $this->sessionService()->flashPersist('error', 'Please login to view courses.');
            $this->redirect('login');
            return;
        }

        $courseRepository = $this->model('CourseRepository');
        $enrollmentRepository = $this->model('EnrollmentRepository');

        $course = $courseRepository->fetchWithCreator($id);

        if (!$course) {
            $this->sessionService()->flashPersist('error', 'Course not found.');
            $this->redirect('student/dashboard');
            return;
        }

        $isEnrolled = $enrollmentRepository->isEnrolled($_SESSION['user_id'], $id);

        $data = [
            'title' => $course['title'] . ' - APPOLIOS',
            'description' => $course['description'],
            'course' => $course,
            'isEnrolled' => $isEnrolled,
            'course_video_payload' => CourseVideoPresenter::normalizeVideo((string) ($course['video_url'] ?? '')),
            'flash' => $this->sessionService()->flashConsumeForView()
        ];

        $this->view('FrontOffice/student/course', $data);
    }

    /**
     * Enroll in a course
     */
    public function enroll($id) {
        if (!$this->isLoggedIn()) {
            $this->sessionService()->flashPersist('error', 'Please login to enroll in courses.');
            $this->redirect('login');
            return;
        }

        $enrollmentRepository = $this->model('EnrollmentRepository');

        // Check if already enrolled
        if ($enrollmentRepository->isEnrolled($_SESSION['user_id'], $id)) {
            $this->sessionService()->flashPersist('info', 'You are already enrolled in this course.');
            $this->redirect('student/course/' . $id);
            return;
        }

        // Enroll user
        if ($enrollmentRepository->enroll($_SESSION['user_id'], $id)) {
            $this->sessionService()->flashPersist('success', 'Successfully enrolled in the course!');
        } else {
            $this->sessionService()->flashPersist('error', 'Failed to enroll. Please try again.');
        }

        $this->redirect('student/course/' . $id);
    }

    /**
     * My courses page
     */
    public function myCourses() {
        if (!$this->isLoggedIn()) {
            $this->sessionService()->flashPersist('error', 'Please login to view your courses.');
            $this->redirect('login');
            return;
        }

        $enrollmentRepository = $this->model('EnrollmentRepository');
        $enrollments = $enrollmentRepository->fetchEnrollmentsForUser($_SESSION['user_id']);

        $data = [
            'title' => 'My Courses - APPOLIOS',
            'description' => 'Your enrolled courses',
            'enrollments' => $enrollments,
            'flash' => $this->sessionService()->flashConsumeForView()
        ];

        $this->view('FrontOffice/student/my_courses', $data);
    }

    /**
     * My events page (Events student is participating in)
     */
    public function myEvents() {
        if (!$this->isLoggedIn()) {
            $this->sessionService()->flashPersist('error', 'Please login to view your events.');
            $this->redirect('login');
            return;
        }

        $studentId = (int) $_SESSION['user_id'];
        $participations = $this->model('EvenementRessourceRepository')->findMyParticipations($studentId);

        $data = [
            'title'          => 'My Events - APPOLIOS',
            'description'    => 'Events you are participating in',
            'userName'       => $_SESSION['user_name'],
            'participations' => $participations,
            'flash'          => $this->sessionService()->flashConsumeForView()
        ];

        $this->view('FrontOffice/student/my_events', $data);
    }

    /**
     * Student profile page
     */
    public function profile() {
        if (!$this->isLoggedIn()) {
            $this->sessionService()->flashPersist('error', 'Please login to view your profile.');
            $this->redirect('login');
            return;
        }

        $userRepository = $this->model('UserRepository');
        $user = $userRepository->findById($_SESSION['user_id']);

        $data = [
            'title' => 'My Profile - APPOLIOS',
            'description' => 'Student profile',
            'user' => $user,
            'flash' => $this->sessionService()->flashConsumeForView()
        ];

        $this->view('FrontOffice/student/profile', $data);
    }

    /**
     * Edit profile page
     */
    public function editProfile() {
        if (!$this->isLoggedIn()) {
            $this->sessionService()->flashPersist('error', 'Please login to edit your profile.');
            $this->redirect('login');
            return;
        }

        $userRepository = $this->model('UserRepository');
        $user = $userRepository->findById($_SESSION['user_id']);

        $flashEntity = $this->sessionService()->takeFlash();
        $data = [
            'title' => 'Edit Profile - APPOLIOS',
            'description' => 'Edit your profile information',
            'user' => $user,
            'flash' => $this->flashMessageToViewArray($flashEntity),
            'flash_banner' => FlashBannerPresenter::fromFlash($flashEntity),
        ];

        $this->view('FrontOffice/student/edit_profile', $data);
    }

    /**
     * Update profile
     */
    public function updateProfile() {
        if (!$this->isLoggedIn()) {
            $this->sessionService()->flashPersist('error', 'Please login to update your profile.');
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

        $userRepository = $this->model('UserRepository');
        $currentUser = $userRepository->findById($_SESSION['user_id']);

        // Check if email is taken by another user
        if ($email !== $currentUser['email']) {
            if ($userRepository->emailExists($email)) {
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
            $this->sessionService()->flashPersist('error', implode('<br>', $errors));
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

        if ($userRepository->update($_SESSION['user_id'], $updateData)) {
            // Update session data
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;

            $this->sessionService()->flashPersist('success', 'Profile updated successfully!');
        } else {
            $this->sessionService()->flashPersist('error', 'Failed to update profile. Please try again.');
        }

        $this->redirect('student/profile');
    }

    private function studentGroupesIndex($groupeRepository): void
    {
        $uid = (int) $_SESSION['user_id'];
        $listFilter = $this->parseStudentListQuery(200);
        $q = $listFilter['q'];
        $sort = $this->normalizeGroupListSort($listFilter['sort'] !== '' ? $listFilter['sort'] : 'name_asc');

        $mesGroupes = $groupeRepository->fetchByCreator($uid);
        $mesGroupesEnApprobation = array_values(array_filter(
            $mesGroupes,
            static function (array $g): bool {
                $a = (string) ($g['approval_statut'] ?? $g['approval_status'] ?? '');
                return $a !== 'approuve';
            }
        ));

        $rawPublic = $groupeRepository->fetchAllWithCreatorPublic(100, 0);
        $groupes = array_values(array_filter(
            $rawPublic,
            static function (array $g): bool {
                $a = (string) ($g['approval_statut'] ?? $g['approval_status'] ?? '');
                return $a === 'approuve';
            }
        ));

        $mesGroupesEnApprobation = array_values(array_filter(
            $mesGroupesEnApprobation,
            function (array $g) use ($q): bool {
                return $this->groupRowMatchesQuery($g, $q);
            }
        ));
        $groupes = array_values(array_filter(
            $groupes,
            function (array $g) use ($q): bool {
                return $this->groupRowMatchesQuery($g, $q);
            }
        ));
        $mesGroupesEnApprobation = GroupPresenter::decorateListingRows($mesGroupesEnApprobation, $groupeRepository, $uid, false);
        $groupes = GroupPresenter::decorateListingRows($groupes, $groupeRepository, $uid, true);
        $this->sortGroupRows($mesGroupesEnApprobation, $sort);
        $this->sortGroupRows($groupes, $sort);

        $data = $this->withFoContext([
            'title' => 'Groupes - APPOLIOS',
            'groupes' => $groupes,
            'mesGroupesEnApprobation' => $mesGroupesEnApprobation,
            'listQ' => $q,
            'listSort' => $sort,
            'listQueryActive' => $q !== '',
            'flash' => $this->sessionService()->flashConsumeForView(),
            'studentSidebarActive' => 'groupes',
        ], 'groupes');
        $this->view('FrontOffice/student/groupes/index', $data);
    }

    private function studentGroupesCreate(): void
    {
        $data = $this->withFoContext([
            'title' => 'Creer un groupe - APPOLIOS',
            'old' => $this->sessionService()->consumeOld(),
            'errors' => $this->sessionService()->takeValidationMessages()->getMessages(),
            'flash' => $this->sessionService()->flashConsumeForView(),
            'studentSidebarActive' => 'groupes',
        ], 'groupes');
        $this->view('FrontOffice/student/groupes/create', $data);
    }

    private function studentGroupesStore($groupeRepository): void
    {
        $payload = $this->extractGroupePayload();
        $errors = $this->validateGroupePayload($payload);
        if (!empty($errors)) {
            $this->sessionService()->validationPersist($errors);
            $_SESSION['old'] = $_POST;
            $this->foRedirect('groupes/create');
            return;
        }

        $canStoreImg = $groupeRepository->supportsStoredImage();
        $photo = ['url' => null, 'error' => null];
        if ($canStoreImg) {
            $photo = $this->handleGroupPhotoUpload('group_photo');
        } elseif ((int) ($_FILES['group_photo']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $photo['error'] = 'Image storage is not available. Contact the administrator.';
        }
        if ($photo['error']) {
            $this->sessionService()->validationPersist(['group_photo' => $photo['error']]);
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

        $createdId = $groupeRepository->create($createData);
        if ($createdId) {
            $groupeRepository->ajouterMembre((int) $createdId, (int) $_SESSION['user_id'], 'admin');
            $this->sessionService()->flashPersist('success', 'Groupe cree. Etat: en cours d approbation jusqu a la decision de l administrateur.');
            $this->foRedirect('groupes');
            return;
        }

        if ($photo['url'] !== null) {
            $this->deleteGroupPhotoFileIfManaged($photo['url']);
        }
        $this->sessionService()->flashPersist('error', 'Erreur lors de la creation du groupe.');
        $this->foRedirect('groupes/create');
    }

    private function studentGroupesShow($groupeRepository, int $id): void
    {
        $groupe = $groupeRepository->findById($id);
        if (!$groupe) {
            $this->sessionService()->flashPersist('error', 'Groupe introuvable.');
            $this->foRedirect('groupes');
            return;
        }

        $uid = (int) $_SESSION['user_id'];
        $approval = (string) ($groupe['approval_statut'] ?? '');
        $isCreator = (int) ($groupe['id_createur'] ?? 0) === $uid;
        $isGroupCreatorViewer = $isCreator;
        if ($approval !== 'approuve' && !$isCreator) {
            $this->sessionService()->flashPersist('error', 'Ce groupe est encore en cours d approbation. Seul le createur peut le consulter.');
            $this->foRedirect('groupes');
            return;
        }

        $discussionRepository = $this->model('DiscussionRepository');
        $composerSession = $this->consumeDiscussionComposerSession();
        $discussionRepublish = DiscussionPresenter::groupDetailComposerPayload($composerSession['old'], $composerSession['errors']);

        $discussionRows = $discussionRepository->fetchByGroupForViewer(
            $id,
            $uid,
            (int) ($groupe['id_createur'] ?? 0)
        );
        $prefix = $this->frontOfficeRoutePrefix();

        $data = $this->withFoContext([
            'title' => 'Detail groupe - APPOLIOS',
            'groupe' => $groupe,
            'is_owner_viewer' => $isCreator,
            'group_cover_url' => GroupPresenter::detailCoverUrl($groupe),
            'member_chips' => GroupPresenter::formatMembers($groupeRepository->fetchMembres($id)),
            'discussion_cards' => DiscussionPresenter::groupShowCards($discussionRows, $uid, $prefix, $id, $isGroupCreatorViewer, APP_ENTRY),
            'discussion_old' => $discussionRepublish['old'],
            'discussion_error_messages' => $discussionRepublish['error_messages'],
            'flash' => $this->sessionService()->flashConsumeForView(),
            'studentSidebarActive' => 'groupes',
        ], 'groupes');
        $this->view('FrontOffice/student/groupes/show', $data);
    }

    private function studentGroupesActivityReport($groupeRepository, int $id): void
    {
        $groupe = $groupeRepository->findById($id);
        if (!$groupe) {
            $this->sessionService()->flashPersist('error', 'Groupe introuvable.');
            $this->foRedirect('groupes');
            return;
        }

        $uid = (int) $_SESSION['user_id'];
        $approval = (string) ($groupe['approval_statut'] ?? '');
        $isCreator = (int) ($groupe['id_createur'] ?? 0) === $uid;
        if ($approval !== 'approuve' && !$isCreator) {
            $this->sessionService()->flashPersist('error', 'Ce groupe est encore en cours d approbation. Seul le createur peut le consulter.');
            $this->foRedirect('groupes');
            return;
        }
        if ($approval === 'approuve' && !$isCreator && !$groupeRepository->estMembre($id, $uid)) {
            $this->sessionService()->flashPersist('error', 'Access denied.');
            $this->foRedirect('groupes');
            return;
        }

        try {
            $report = $this->model('GroupActivityReportService')->build($id);
        } catch (Throwable $e) {
            $this->sessionService()->flashPersist('error', 'Unable to build report.');
            $this->foRedirect('groupes/' . $id);
            return;
        }

        $prefix = $this->frontOfficeRoutePrefix();
        $report['backUrl'] = APP_ENTRY . '?url=' . rawurlencode($prefix . '/groupes/' . $id);
        $report['report_title'] = 'Group Activity Report';
        $this->renderStandaloneView('Reports/group_activity_report_pdf', $report);
    }

    private function studentGroupesDiscussionStore($groupeRepository, int $groupId): void
    {
        $groupe = $groupeRepository->findById($groupId);
        if (!$groupe) {
            $this->sessionService()->flashPersist('error', 'Group not found.');
            $this->foRedirect('groupes');
            return;
        }
        if ((int) ($groupe['id_createur'] ?? 0) !== (int) $_SESSION['user_id']) {
            $this->sessionService()->flashPersist('error', 'Only the group creator can create discussions.');
            $this->foRedirect('groupes/' . $groupId);
            return;
        }
        if ((string) ($groupe['approval_statut'] ?? $groupe['approval_status'] ?? '') !== 'approuve') {
            $this->sessionService()->flashPersist('error', 'You can create discussions only after the group is approved.');
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

        $discussionRepository = $this->model('DiscussionRepository');
        $ok = $discussionRepository->createForGroup(
            $groupId,
            (int) $_SESSION['user_id'],
            htmlspecialchars($payload['titre'], ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($payload['contenu'], ENT_QUOTES, 'UTF-8'),
            'approuve'
        );
        $this->sessionService()->flashPersist($ok ? 'success' : 'error', $ok ? 'Discussion creee. Vous pouvez l utiliser tout de suite (chat, fichiers).' : 'Failed to create discussion.');
        $this->foRedirect('groupes/' . $groupId);
    }

    private function studentGroupesEdit($groupeRepository, int $id): void
    {
        $groupe = $groupeRepository->findById($id);
        if (!$groupe || (int) $groupe['id_createur'] !== (int) $_SESSION['user_id']) {
            $this->sessionService()->flashPersist('error', 'Vous ne pouvez modifier que vos groupes.');
            $this->foRedirect('groupes');
            return;
        }
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['old']);
        $discussionRepository = $this->model('DiscussionRepository');
        $groupActivitySeries = $this->buildGroupActivitySeries(
            $id,
            $discussionRepository->fetchByGroup($id),
            $groupeRepository->fetchMembres($id)
        );
        $data = $this->withFoContext([
            'title' => 'Modifier groupe - APPOLIOS',
            'groupe' => $groupe,
            'old' => $old,
            'group_activity_series' => $groupActivitySeries,
            'errors' => $this->sessionService()->takeValidationMessages()->getMessages(),
            'flash' => $this->sessionService()->flashConsumeForView(),
            'studentSidebarActive' => 'groupes',
        ], 'groupes');
        $this->view('FrontOffice/student/groupes/edit', $data);
    }

    private function studentGroupesUpdate($groupeRepository, int $id): void
    {
        $groupe = $groupeRepository->findById($id);
        if (!$groupe || (int) $groupe['id_createur'] !== (int) $_SESSION['user_id']) {
            $this->sessionService()->flashPersist('error', 'Vous ne pouvez modifier que vos groupes.');
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
            $this->sessionService()->validationPersist($errors);
            $_SESSION['old'] = $_POST;
            $this->foRedirect('groupes/' . $id . '/edit');
            return;
        }

        $canStoreImg = $groupeRepository->supportsStoredImage();
        $photo = ['url' => null, 'error' => null];
        if ($canStoreImg) {
            $photo = $this->handleGroupPhotoUpload('group_photo');
        }
        if ($photo['error']) {
            $this->sessionService()->validationPersist(['group_photo' => $photo['error']]);
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
        $ok = $groupeRepository->updateGroupe($id, $updateData);
        $this->sessionService()->flashPersist($ok ? 'success' : 'error', $ok ? 'Groupe mis a jour.' : 'Echec de mise a jour.');
        $this->foRedirect('groupes/' . $id);
    }

    private function studentGroupesJoin($groupeRepository, int $id): void
    {
        $groupe = $groupeRepository->findById($id);
        if (!$groupe || ($groupe['approval_statut'] ?? '') !== 'approuve') {
            $this->sessionService()->flashPersist('error', 'Groupe non disponible.');
            $this->foRedirect('groupes');
            return;
        }
        $uid = (int) $_SESSION['user_id'];
        if (!$groupeRepository->estMembre($id, $uid)) {
            $groupeRepository->ajouterMembre($id, $uid, 'membre');
            $this->sessionService()->flashPersist('success', 'Vous avez rejoint le groupe.');
        } else {
            $this->sessionService()->flashPersist('error', 'Vous etes deja membre.');
        }
        $this->foRedirect('groupes/' . $id);
    }

    private function studentGroupesQuit($groupeRepository, int $id): void
    {
        $groupe = $groupeRepository->findById($id);
        if (!$groupe || ($groupe['approval_statut'] ?? '') !== 'approuve') {
            $this->sessionService()->flashPersist('error', 'Groupe non disponible.');
            $this->foRedirect('groupes');
            return;
        }
        $uid = (int) $_SESSION['user_id'];
        $isCreator = (int) ($groupe['id_createur'] ?? 0) === $uid;
        if ($isCreator) {
            $this->sessionService()->flashPersist('error', 'Le createur du groupe ne peut pas quitter son propre groupe.');
            $this->foRedirect('groupes/' . $id);
            return;
        }
        if (!$groupeRepository->estMembre($id, $uid)) {
            $this->sessionService()->flashPersist('error', 'Vous n etes pas membre de ce groupe.');
            $this->foRedirect('groupes/' . $id);
            return;
        }
        $ok = $groupeRepository->retirerMembre($id, $uid);
        $this->sessionService()->flashPersist($ok ? 'success' : 'error', $ok ? 'Vous avez quitte le groupe.' : 'Impossible de quitter le groupe pour le moment.');
        $this->foRedirect('groupes');
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

    private function studentDiscussionsStore($discussionRepository, $groupeRepository): void
    {
        $payload = [
            'titre' => trim((string) ($_POST['titre'] ?? '')),
            'contenu' => trim((string) ($_POST['contenu'] ?? '')),
            'id_groupe' => (int) ($_POST['id_groupe'] ?? 0),
        ];
        $groups = $this->model('StudentQueryService')->approvedOwnedGroupsForUser($groupeRepository, (int) $_SESSION['user_id']);
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
        $ok = $discussionRepository->createForGroup(
            $payload['id_groupe'],
            (int) $_SESSION['user_id'],
            htmlspecialchars($payload['titre'], ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($payload['contenu'], ENT_QUOTES, 'UTF-8'),
            'approuve'
        );
        $this->sessionService()->flashPersist($ok ? 'success' : 'error', $ok ? 'Discussion creee. Vous pouvez la modifier et ouvrir le chat tout de suite.' : 'Failed to create discussion.');
        $this->foRedirect('discussions');
    }

    private function studentDiscussionsEdit($discussionRepository, $groupeRepository, int $discussionId): void
    {
        $authorId = (int) $_SESSION['user_id'];
        $discussion = $discussionRepository->findOwnedBy($discussionId, $authorId);
        if (!$discussion) {
            $this->sessionService()->flashPersist('error', 'Discussion not found.');
            $this->foRedirect('discussions');
            return;
        }
        $old = $_SESSION['discussion_old'] ?? [];
        unset($_SESSION['discussion_old']);
        $prefix = $this->frontOfficeRoutePrefix();
        $form = DiscussionPresenter::editForm($discussion, $prefix, APP_ENTRY);
        if ($old !== []) {
            if (isset($old['id_groupe'])) {
                $form['selected_group_id'] = (int) $old['id_groupe'];
            }
            if (isset($old['titre'])) {
                $form['title_value'] = (string) $old['titre'];
            }
            if (isset($old['contenu'])) {
                $form['content_value'] = (string) $old['contenu'];
            }
        }

        $groupId = (int) ($discussion['id_groupe'] ?? 0);
        $groupRows = $groupId > 0 ? $discussionRepository->fetchByGroup($groupId) : [];
        $ownerMessages = 0;
        $latestTs = 0;
        $earliestTs = 0;
        $seriesDays = 14;
        $seriesMapAll = [];
        $seriesMapOwner = [];
        $seriesLabels = [];
        $today = new DateTimeImmutable('today');
        for ($i = $seriesDays - 1; $i >= 0; $i--) {
            $d = $today->sub(new DateInterval('P' . $i . 'D'));
            $k = $d->format('Y-m-d');
            $seriesMapAll[$k] = 0;
            $seriesMapOwner[$k] = 0;
            $seriesLabels[] = $d->format('d M');
        }
        foreach ($groupRows as $row) {
            $rowAuthor = (int) ($row['id_auteur'] ?? $row['created_by'] ?? 0);
            if ($rowAuthor === $authorId) {
                ++$ownerMessages;
            }
            $rawDate = (string) ($row['date_creation'] ?? $row['created_at'] ?? '');
            $ts = $rawDate !== '' ? strtotime($rawDate) : false;
            if ($ts !== false) {
                if ($latestTs === 0 || $ts > $latestTs) {
                    $latestTs = $ts;
                }
                if ($earliestTs === 0 || $ts < $earliestTs) {
                    $earliestTs = $ts;
                }
                $k = date('Y-m-d', $ts);
                if (isset($seriesMapAll[$k])) {
                    $seriesMapAll[$k]++;
                    if ($rowAuthor === $authorId) {
                        $seriesMapOwner[$k]++;
                    }
                }
            }
        }
        $currentContentWords = str_word_count(trim(strip_tags((string) ($discussion['contenu'] ?? ''))));
        $daysSpan = 1;
        if ($latestTs > 0 && $earliestTs > 0 && $latestTs >= $earliestTs) {
            $daysSpan = max(1, (int) floor(($latestTs - $earliestTs) / 86400) + 1);
        }
        $discussionStats = [
            'total_messages' => count($groupRows),
            'owner_messages' => $ownerMessages,
            'current_message_words' => $currentContentWords,
            'avg_messages_per_day' => $daysSpan > 0 ? round(count($groupRows) / $daysSpan, 1) : (float) count($groupRows),
            'last_activity_label' => $latestTs > 0 ? date('d M Y H:i', $latestTs) : 'No activity yet',
            'series_labels' => $seriesLabels,
            'series_group_messages' => array_values($seriesMapAll),
            'series_owner_messages' => array_values($seriesMapOwner),
        ];

        $data = $this->withFoContext([
            'title' => 'Edit Discussion - APPOLIOS',
            'studentSidebarActive' => 'discussions',
            'discussion_edit' => $form,
            'discussion_stats' => $discussionStats,
            'groups' => $this->model('StudentQueryService')->approvedOwnedGroupsForUser($groupeRepository, (int) $_SESSION['user_id']),
            'errors' => $_SESSION['discussion_errors'] ?? [],
            'flash' => $this->sessionService()->flashConsumeForView()
        ], 'discussions');
        unset($_SESSION['discussion_errors']);
        $this->view('FrontOffice/student/discussions/edit', $data);
    }

    private function studentDiscussionsUpdate($discussionRepository, $groupeRepository, int $discussionId): void
    {
        $existing = $discussionRepository->findOwnedBy($discussionId, (int) $_SESSION['user_id']);
        if (!$existing) {
            $this->sessionService()->flashPersist('error', 'Discussion not found.');
            $this->foRedirect('discussions');
            return;
        }
        $payload = [
            'titre' => trim((string) ($_POST['titre'] ?? '')),
            'contenu' => trim((string) ($_POST['contenu'] ?? '')),
            'id_groupe' => (int) ($_POST['id_groupe'] ?? 0),
        ];
        $groups = $this->model('StudentQueryService')->approvedOwnedGroupsForUser($groupeRepository, (int) $_SESSION['user_id']);
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
        $ok = $discussionRepository->updateOwned(
            $discussionId,
            (int) $_SESSION['user_id'],
            htmlspecialchars($payload['titre'], ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($payload['contenu'], ENT_QUOTES, 'UTF-8'),
            $payload['id_groupe']
        );
        $this->sessionService()->flashPersist($ok ? 'success' : 'error', $ok ? 'Discussion mise a jour.' : 'Failed to update discussion.');
        $this->foRedirect('discussions');
    }

    private function studentDiscussionsChat($discussionRepository, $groupeRepository, int $discussionId): void
    {
        $discussion = $discussionRepository->fetchRowByPk($discussionId);
        if (!$discussion) {
            $this->sessionService()->flashPersist('error', 'Discussion not found.');
            $this->foRedirect('discussions');
            return;
        }

        $uid = (int) ($_SESSION['user_id'] ?? 0);

        $groupId = (int) ($discussion['id_groupe'] ?? $discussion['group_id'] ?? 0);
        $group = $groupeRepository->findById($groupId);
        if (!$group) {
            $this->sessionService()->flashPersist('error', 'Parent group not found.');
            $this->foRedirect('discussions');
            return;
        }

        $isOwner = (int) ($group['id_createur'] ?? $group['created_by'] ?? 0) === $uid;
        $isMember = $groupeRepository->estMembre($groupId, $uid);
        $discAuthorId = (int) ($discussion['id_auteur'] ?? $discussion['created_by'] ?? 0);
        $isDiscussionAuthor = $discAuthorId === $uid && $discAuthorId > 0;
        if (!$isOwner && !$isMember && !$isDiscussionAuthor) {
            $this->sessionService()->flashPersist('error', 'You must join the group to access live chat.');
            $this->foRedirect('groupes/' . $groupId);
            return;
        }

        $prefix = $this->frontOfficeRoutePrefix();
        $chatUrls = DiscussionPresenter::chatUrls($discussion, $prefix, APP_ENTRY);
        $discussionTitle = trim((string) ($discussion['titre'] ?? $discussion['title'] ?? ''));
        $data = $this->withFoContext([
            'title' => $discussionTitle !== '' ? $discussionTitle . ' · Live Chat · APPOLIOS' : 'Live Chat - APPOLIOS',
            'studentSidebarActive' => 'discussions',
            'discussion' => $discussion,
            'group' => $group,
            'discussion_chat' => [
                'discussion_title' => $discussionTitle !== '' ? $discussionTitle : 'Discussion',
                'group_name' => (string) ($group['nom_groupe'] ?? 'N/A'),
                'back_url' => $chatUrls['back_url'],
                'upload_url' => $chatUrls['upload_url'],
            ],
            'socketUrl' => SOCKET_IO_URL,
            'chatRoom' => 'discussion_' . $discussionId,
            'currentUserId' => $uid,
            'currentUserName' => (string) ($_SESSION['user_name'] ?? 'User'),
            'flash' => $this->sessionService()->flashConsumeForView(),
        ], 'discussions');
        $this->view('FrontOffice/student/discussions/chat', $data);
    }

    private function studentDiscussionsUploadAttachment($discussionRepository, $groupeRepository, int $discussionId): void
    {
        $discussion = $discussionRepository->fetchRowByPk($discussionId);
        if (!$discussion) {
            $this->jsonResponse(['ok' => false, 'error' => 'Discussion not found.'], 404);
        }

        $uid = (int) ($_SESSION['user_id'] ?? 0);

        $groupId = (int) ($discussion['id_groupe'] ?? $discussion['group_id'] ?? 0);
        $group = $groupeRepository->findById($groupId);
        if (!$group) {
            $this->jsonResponse(['ok' => false, 'error' => 'Parent group not found.'], 404);
        }

        $isOwner = (int) ($group['id_createur'] ?? $group['created_by'] ?? 0) === $uid;
        $isMember = $groupeRepository->estMembre($groupId, $uid);
        $discAuthorId = (int) ($discussion['id_auteur'] ?? $discussion['created_by'] ?? 0);
        $isDiscussionAuthor = $discAuthorId === $uid && $discAuthorId > 0;
        if (!$isOwner && !$isMember && !$isDiscussionAuthor) {
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

    private function studentDiscussionDeleteAuthorized($discussionRepository, $groupeRepository, int $discussionId, ?int $mustBelongToGroupId = null): bool
    {
        $uid = (int) $_SESSION['user_id'];
        $row = $discussionRepository->fetchRowByPk($discussionId);
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
        $groupe = $groupeRepository->findById($gid);
        if (!$groupe) {
            return false;
        }
        $isAuthor = (int) $row[$authorCol] === $uid;
        $creatorId = (int) ($groupe['id_createur'] ?? $groupe['created_by'] ?? 0);
        $isGroupOwner = $creatorId === $uid;
        if (!$isAuthor && !$isGroupOwner) {
            return false;
        }
        return $discussionRepository->deleteByPrimaryKey($discussionId);
    }

    private function studentGroupDiscussionDeleteFromPage($groupeRepository, $discussionRepository, int $groupId, int $discussionId): void
    {
        $ok = $this->studentDiscussionDeleteAuthorized($discussionRepository, $groupeRepository, $discussionId, $groupId);
        $this->sessionService()->flashPersist($ok ? 'success' : 'error', $ok ? 'Discussion supprimee.' : 'Suppression impossible (non autorisee).');
        $this->foRedirect('groupes/' . $groupId);
    }

    private function studentGroupesDelete($groupeRepository, $discussionRepository, int $id): void
    {
        $uid = (int) $_SESSION['user_id'];
        $groupe = $groupeRepository->findById($id);
        if (!$groupe || (int) ($groupe['id_createur'] ?? $groupe['created_by'] ?? 0) !== $uid) {
            $this->sessionService()->flashPersist('error', 'Suppression reservee au createur du groupe.');
            $this->foRedirect('groupes');
            return;
        }
        $img = $this->groupeImageUrlFromRow($groupe);
        $this->deleteGroupPhotoFileIfManaged($img);
        $discussionRepository->deleteAllForGroup($id);
        $groupeRepository->deleteMembresForGroup($id);
        $ok = $groupeRepository->delete($id);
        $this->sessionService()->flashPersist($ok ? 'success' : 'error', $ok ? 'Groupe supprime.' : 'Impossible de supprimer le groupe.');
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

        $event = $this->model('EvenementRepository')->findApprovedById($eventId);
        if (!$event) {
            $this->sessionService()->flashPersist('error', 'Event not found or not available.');
            $this->redirect('student/evenements');
            return;
        }

        $resRepo = $this->model('EvenementRessourceRepository');
        $existing = $resRepo->findStudentParticipation($eventId, $studentId);
        if ($existing) {
            $this->sessionService()->flashPersist('info', 'You already requested participation for this event.');
            $this->redirect('student/evenements');
            return;
        }

        $user = $this->model('UserRepository')->findById($studentId);
        $studentName = $user['name'] ?? 'Student';

        if ($resRepo->createPendingParticipation($eventId, $studentId, $studentName)) {
            $this->sessionService()->flashPersist('success', 'Participation request sent! Waiting for teacher approval.');
        } else {
            $this->sessionService()->flashPersist('error', 'Failed to send participation request.');
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

        $resRepo = $this->model('EvenementRessourceRepository');
        $existing = $resRepo->findStudentParticipation($eventId, $studentId);
        if (!$existing || $existing['details'] !== 'pending') {
            $this->sessionService()->flashPersist('error', 'Only pending participation requests can be cancelled.');
            $this->redirect('student/evenements');
            return;
        }

        if ($resRepo->cancelPendingParticipation($eventId, $studentId)) {
            $this->sessionService()->flashPersist('success', 'Participation request cancelled.');
        } else {
            $this->sessionService()->flashPersist('error', 'Failed to cancel participation.');
        }

        $this->redirect('student/evenements');
    }

    /**
     * @return array{q: string, sort: string}
     */
    private function parseStudentListQuery(int $qMaxLen): array
    {
        $q = isset($_GET['q']) ? trim((string) $_GET['q']) : '';
        if (strlen($q) > $qMaxLen) {
            $q = substr($q, 0, $qMaxLen);
        }
        $sort = isset($_GET['sort']) ? trim((string) $_GET['sort']) : '';
        return ['q' => $q, 'sort' => $sort];
    }

    private function normalizeGroupListSort(string $sort): string
    {
        $allowed = ['name_asc', 'name_desc', 'newest', 'oldest'];
        return in_array($sort, $allowed, true) ? $sort : 'name_asc';
    }

    private function normalizeDiscussionListSort(string $sort): string
    {
        $allowed = ['title_asc', 'title_desc', 'group_asc', 'group_desc', 'newest', 'oldest'];
        return in_array($sort, $allowed, true) ? $sort : 'newest';
    }

    private function textMatchesQuery(string $value, string $q): bool
    {
        if ($q === '') {
            return true;
        }
        if (function_exists('mb_stripos')) {
            return mb_stripos($value, $q, 0, 'UTF-8') !== false;
        }
        return stripos($value, $q) !== false;
    }

    private function groupRowMatchesQuery(array $g, string $q): bool
    {
        if ($q === '') {
            return true;
        }
        $name = (string) ($g['nom_groupe'] ?? '');
        $desc = (string) ($g['description'] ?? '');
        return $this->textMatchesQuery($name, $q) || $this->textMatchesQuery($desc, $q);
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     */
    private function sortGroupRows(array &$rows, string $sort): void
    {
        $studentQuery = $this->model('StudentQueryService');
        $nameCmp = static function (array $a, array $b): int {
            $ta = (string) ($a['nom_groupe'] ?? '');
            $tb = (string) ($b['nom_groupe'] ?? '');
            if (function_exists('mb_strtolower')) {
                $ta = mb_strtolower($ta, 'UTF-8');
                $tb = mb_strtolower($tb, 'UTF-8');
            } else {
                $ta = strtolower($ta);
                $tb = strtolower($tb);
            }
            return $ta <=> $tb;
        };
        switch ($sort) {
            case 'name_desc':
                usort($rows, static function (array $a, array $b) use ($nameCmp): int {
                    return -$nameCmp($a, $b);
                });
                return;
            case 'newest':
                usort($rows, function (array $a, array $b) use ($studentQuery): int {
                    return $studentQuery->sortKeyGroupId($b) <=> $studentQuery->sortKeyGroupId($a);
                });
                return;
            case 'oldest':
                usort($rows, function (array $a, array $b) use ($studentQuery): int {
                    return $studentQuery->sortKeyGroupId($a) <=> $studentQuery->sortKeyGroupId($b);
                });
                return;
            case 'name_asc':
            default:
                usort($rows, $nameCmp);
        }
    }

    private function discussionRowMatchesQuery(array $d, string $q): bool
    {
        if ($q === '') {
            return true;
        }
        $titre = (string) ($d['titre'] ?? $d['title'] ?? '');
        $contenu = (string) ($d['contenu'] ?? $d['content'] ?? '');
        $groupe = (string) ($d['nom_groupe'] ?? '');
        return $this->textMatchesQuery($titre, $q)
            || $this->textMatchesQuery($contenu, $q)
            || $this->textMatchesQuery($groupe, $q);
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     */
    private function sortDiscussionRows(array &$rows, string $sort): void
    {
        $studentQuery = $this->model('StudentQueryService');
        $titleCmp = static function (array $a, array $b): int {
            $ta = (string) ($a['titre'] ?? $a['title'] ?? '');
            $tb = (string) ($b['titre'] ?? $b['title'] ?? '');
            if (function_exists('mb_strtolower')) {
                $ta = mb_strtolower($ta, 'UTF-8');
                $tb = mb_strtolower($tb, 'UTF-8');
            } else {
                $ta = strtolower($ta);
                $tb = strtolower($tb);
            }
            return $ta <=> $tb;
        };
        $groupCmp = static function (array $a, array $b): int {
            $ga = (string) ($a['nom_groupe'] ?? '');
            $gb = (string) ($b['nom_groupe'] ?? '');
            if (function_exists('mb_strtolower')) {
                $ga = mb_strtolower($ga, 'UTF-8');
                $gb = mb_strtolower($gb, 'UTF-8');
            } else {
                $ga = strtolower($ga);
                $gb = strtolower($gb);
            }
            return $ga <=> $gb;
        };
        switch ($sort) {
            case 'title_asc':
                usort($rows, $titleCmp);
                return;
            case 'title_desc':
                usort($rows, static function (array $a, array $b) use ($titleCmp): int {
                    return -$titleCmp($a, $b);
                });
                return;
            case 'group_asc':
                usort($rows, $groupCmp);
                return;
            case 'group_desc':
                usort($rows, static function (array $a, array $b) use ($groupCmp): int {
                    return -$groupCmp($a, $b);
                });
                return;
            case 'oldest':
                usort($rows, function (array $a, array $b) use ($studentQuery): int {
                    return $studentQuery->sortKeyDiscussionId($a) <=> $studentQuery->sortKeyDiscussionId($b);
                });
                return;
            case 'newest':
            default:
                usort($rows, function (array $a, array $b) use ($studentQuery): int {
                    return $studentQuery->sortKeyDiscussionId($b) <=> $studentQuery->sortKeyDiscussionId($a);
                });
        }
    }

    /** Student query helpers (moved from Model StudentQueryService). */
    public static function layerStudentQuery_approvedOwnedGroupsForUser($repo, int $userId): array
    {
        $groups = $repo->fetchByCreator($userId);

        return array_values(array_filter(
            $groups,
            static function (array $g): bool {
                $a = (string) ($g['approval_statut'] ?? $g['approval_status'] ?? '');

                return $a === 'approuve';
            }
        ));
    }

    /** @return mixed */
    public static function layerStudentQuery_sortKeyGroupId(array $row)
    {
        return (int) ($row['id_groupe'] ?? $row['id'] ?? 0);
    }

    /** @return mixed */
    public static function layerStudentQuery_sortKeyDiscussionId(array $row)
    {
        return (int) ($row['id_discussion'] ?? $row['id'] ?? 0);
    }

    public function downloadTicket($pId) {
        if (!$this->isLoggedIn()) { $this->redirect('auth/login'); return; }
        $resRepo = $this->model('EvenementRessourceRepository');
        if (!$resRepo->ressourcesTableExists()) {
            $this->sessionService()->flashPersist('error', 'Ticket system is not available yet.');
            $this->redirect('student/my-events');
            return;
        }
        $pId = (int)$pId;
        $studentId = (int) $_SESSION['user_id'];
        $ticket = $resRepo->findApprovedTicketForStudent($pId, $studentId);
        if (!$ticket) {
            $this->sessionService()->flashPersist('error', 'Ticket not found or not approved yet.');
            $this->redirect('student/my-participations');
            return;
        }

        $ticketIdPadded = str_pad((string) $ticket['id'], 6, '0', STR_PAD_LEFT);
        $qrData = 'Ticket ID: ' . $ticketIdPadded . "\n"
            . 'Event: ' . (string) ($ticket['event_title'] ?? '') . "\n"
            . 'Attendee: ' . (string) ($ticket['student_name'] ?? '') . "\n"
            . 'Status: Approved by Appolios';
        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . rawurlencode($qrData);

        $eventTs = strtotime((string) ($ticket['event_full_date'] ?? ''));
        $eventDateDisplay = $eventTs !== false
            ? date('M d, Y - H:i', $eventTs)
            : '';
        $loc = trim((string) ($ticket['event_location'] ?? ''));
        $locationDisplay = $loc !== '' ? $loc : 'To be announced';

        header('Content-Type: text/html; charset=utf-8');
        $this->renderStandaloneView('FrontOffice/student/ticket', [
            'ticket' => $ticket,
            'qrUrl' => $qrUrl,
            'eventDateDisplay' => $eventDateDisplay,
            'ticketIdPadded' => $ticketIdPadded,
            'locationDisplay' => $locationDisplay,
        ]);
    }
}