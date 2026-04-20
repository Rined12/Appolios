<?php
/**
 * APPOLIOS Admin Controller
 * Handles admin dashboard and management
 */

require_once __DIR__ . '/../Controller/BaseController.php';
require_once __DIR__ . '/../Model/User.php';
require_once __DIR__ . '/../Model/Course.php';
require_once __DIR__ . '/../Model/Enrollment.php';
require_once __DIR__ . '/../Model/Evenement.php';
require_once __DIR__ . '/../Model/EvenementRessource.php';
require_once __DIR__ . '/../Model/Groupe.php';
require_once __DIR__ . '/../Model/Discussion.php';

class AdminController extends BaseController {
    public function slDiscussions(...$params) {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied. Admin privileges required.');
            $this->redirect('admin/login');
            return;
        }

        $discussionModel = $this->model('Discussion');
        $first = $params[0] ?? null;
        $second = $params[1] ?? null;

        $id = (int) ($first ?? 0);
        if ($id > 0 && $second === 'approve' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $ok = $discussionModel->setApprovalStatus($id, 'approuve');
            $this->setFlash($ok ? 'success' : 'error', $ok ? 'Discussion approuvee.' : 'Echec de l approbation.');
            $this->redirect('admin/sl-discussions');
            return;
        }
        if ($id > 0 && $second === 'reject' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $ok = $discussionModel->setApprovalStatus($id, 'rejete');
            $this->setFlash($ok ? 'success' : 'error', $ok ? 'Discussion rejetee.' : 'Echec du rejet.');
            $this->redirect('admin/sl-discussions');
            return;
        }

        $this->adminDiscussionsIndex($discussionModel);
    }

    public function slGroupes(...$params) {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied. Admin privileges required.');
            $this->redirect('admin/login');
            return;
        }

        $groupeModel = $this->model('Groupe');
        $first = $params[0] ?? null;
        $second = $params[1] ?? null;

        if ($first === null) {
            $this->adminGroupesIndex($groupeModel);
            return;
        }

        if ($first === 'create') {
            $this->adminGroupesCreate();
            return;
        }

        if ($first === 'store' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->adminGroupesStore($groupeModel);
            return;
        }

        $id = (int) $first;
        if ($id <= 0) {
            $this->setFlash('error', 'Identifiant groupe invalide.');
            $this->redirect('admin/sl-groupes');
            return;
        }

        if ($second === 'edit') {
            $this->adminGroupesEdit($groupeModel, $id);
            return;
        }

        if ($second === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->adminGroupesUpdate($groupeModel, $id);
            return;
        }

        if ($second === 'delete') {
            $this->adminGroupesDelete($groupeModel, $id);
            return;
        }

        if ($second === 'approve' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->adminGroupesApprove($groupeModel, $id);
            return;
        }

        if ($second === 'reject' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->adminGroupesReject($groupeModel, $id);
            return;
        }

        $this->setFlash('error', 'Route introuvable.');
        $this->redirect('admin/sl-groupes');
    }

    /**
     * Admin dashboard
     */
    public function dashboard() {
        // Check if admin
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied. Admin privileges required.');
            $this->redirect('admin/login');
            return;
        }

        $userModel = $this->model('User');
        $courseModel = $this->model('Course');
        $enrollmentModel = $this->model('Enrollment');
        $evenementModel = $this->model('Evenement');

        $data = [
            'title' => 'Admin Dashboard - APPOLIOS',
            'description' => 'Administrator control panel',
            'totalUsers' => $userModel->count(),
            'totalStudents' => $userModel->countStudents(),
            'totalCourses' => $courseModel->count(),
            'totalEnrollments' => $enrollmentModel->countAll(),
            'totalEvenements' => $evenementModel->count(),
            'recentCourses' => $courseModel->getAllWithCreator(),
            'recentEvenements' => $evenementModel->getRecent(3),
            'recentUsers' => $userModel->getStudents(),
            'flash' => $this->getFlash()
        ];

        $this->view('BackOffice/admin/dashboard', $data);
    }

    /**
     * Manage users page
     */
    public function users() {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied. Admin privileges required.');
            $this->redirect('admin/login');
            return;
        }

        $userModel = $this->model('User');
        $users = $userModel->findAll();

        $data = [
            'title' => 'Manage Users - APPOLIOS',
            'description' => 'User management panel',
            'users' => $users,
            'flash' => $this->getFlash()
        ];

        $this->view('BackOffice/admin/users', $data);
    }

    /**
     * Manage courses page
     */
    public function courses() {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied. Admin privileges required.');
            $this->redirect('admin/login');
            return;
        }

        $courseModel = $this->model('Course');
        $courses = $courseModel->getAllWithCreator();

        $data = [
            'title' => 'Manage Courses - APPOLIOS',
            'description' => 'Course management panel',
            'courses' => $courses,
            'flash' => $this->getFlash()
        ];

        $this->view('BackOffice/admin/courses', $data);
    }

    /**
     * Add course page
     */
    public function addCourse() {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied. Admin privileges required.');
            $this->redirect('admin/login');
            return;
        }

        $data = [
            'title' => 'Add Course - APPOLIOS',
            'errors' => $this->getErrors(),
            'description' => 'Create a new course',
            'flash' => $this->getFlash()
        ];

        $this->view('BackOffice/admin/add_course', $data);
    }

    /**
     * Store new course
     */
    public function storeCourse() {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/courses');
            return;
        }

        $payload = $this->extractCoursePayload();
        $errors = $this->validateCoursePayload($payload);

        if (!empty($errors)) {
            $this->setErrors($errors);
            $_SESSION['old'] = $_POST;
            $this->redirect('admin/add-course');
            return;
        }

        $courseModel = $this->model('Course');

        $result = $courseModel->create([
            'title' => $payload['title'],
            'description' => $payload['description'],
            'video_url' => $payload['video_url'],
            'created_by' => $_SESSION['user_id']
        ]);

        if ($result) {
            $this->setFlash('success', 'Course created successfully!');
            $this->redirect('admin/courses');
        } else {
            $this->setFlash('error', 'Failed to create course. Please try again.');
            $this->redirect('admin/add-course');
        }
    }

    /**
     * Edit course page
     */
    public function editCourse($id) {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        $courseModel = $this->model('Course');
        $course = $courseModel->findById($id);

        if (!$course) {
            $this->setFlash('error', 'Course not found');
            $this->redirect('admin/courses');
            return;
        }

        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['old']);

        $data = [
            'title' => 'Edit Course - APPOLIOS',
            'description' => 'Update course details',
            'course' => $course,
            'old' => $old,
            'errors' => $this->getErrors(),
            'flash' => $this->getFlash()
        ];

        $this->view('BackOffice/admin/edit_course', $data);
    }

    /**
     * Update course
     */
    public function updateCourse($id) {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/courses');
            return;
        }

        $payload = $this->extractCoursePayload();
        $errors = $this->validateCoursePayload($payload);

        if (!empty($errors)) {
            $this->setErrors($errors);
            $_SESSION['old'] = $_POST;
            $this->setFlash('error', 'Please fix the errors below.');
            $this->redirect('admin/edit-course/' . (int) $id);
            return;
        }

        $courseModel = $this->model('Course');

        $result = $courseModel->update($id, [
            'title' => $payload['title'],
            'description' => $payload['description'],
            'video_url' => $payload['video_url']
        ]);

        if ($result) {
            $this->setFlash('success', 'Course updated successfully!');
        } else {
            $this->setFlash('error', 'Failed to update course.');
        }

        $this->redirect('admin/courses');
    }

    /**
     * Delete course
     */
    public function deleteCourse($id) {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        $courseModel = $this->model('Course');

        if ($courseModel->delete($id)) {
            $this->setFlash('success', 'Course deleted successfully!');
        } else {
            $this->setFlash('error', 'Failed to delete course.');
        }

        $this->redirect('admin/courses');
    }

    /**
     * Delete user
     */
    public function deleteUser($id) {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        // Prevent admin from deleting themselves
        if ($id == $_SESSION['user_id']) {
            $this->setFlash('error', 'You cannot delete your own account.');
            $this->redirect('admin/users');
            return;
        }

        $userModel = $this->model('User');

        if ($userModel->delete($id)) {
            $this->setFlash('success', 'User deleted successfully!');
        } else {
            $this->setFlash('error', 'Failed to delete user.');
        }

        $this->redirect('admin/users');
    }

    /**
     * Manage teachers page
     */
    public function teachers() {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied. Admin privileges required.');
            $this->redirect('admin/login');
            return;
        }

        $userModel = $this->model('User');
        $teachers = $userModel->getTeachers();

        $data = [
            'title' => 'Manage Teachers - APPOLIOS',
            'description' => 'Teacher management panel',
            'teachers' => $teachers,
            'flash' => $this->getFlash()
        ];

        $this->view('BackOffice/admin/teachers', $data);
    }

    /**
     * Add teacher page
     */
    public function addTeacher() {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied. Admin privileges required.');
            $this->redirect('admin/login');
            return;
        }

        $data = [
            'title' => 'Add Teacher - APPOLIOS',
            'description' => 'Create a new teacher account',
            'flash' => $this->getFlash()
        ];

        $this->view('BackOffice/admin/add_teacher', $data);
    }

    /**
     * Store new teacher
     */
    public function storeTeacher() {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/teachers');
            return;
        }

        $name = $this->sanitize($_POST['name'] ?? '');
        $email = $this->sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validation
        $errors = [];

        if (empty($name)) {
            $errors['name'] = 'Name is required';
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Valid email is required';
        }

        if (empty($password) || strlen($password) < 6) {
            $errors['password'] = 'Password must be at least 6 characters';
        }

        $userModel = $this->model('User');

        if ($userModel->emailExists($email)) {
            $errors['email'] = 'Email already registered';
        }

        if (!empty($errors)) {
            $this->setErrors($errors);
            $_SESSION['old'] = $_POST;
            $this->redirect('admin/add-teacher');
            return;
        }

        $result = $userModel->create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => 'teacher'
        ]);

        if ($result) {
            $this->setFlash('success', 'Teacher account created successfully!');
            $this->redirect('admin/teachers');
        } else {
            $this->setFlash('error', 'Failed to create teacher account. Please try again.');
            $this->redirect('admin/add-teacher');
        }
    }

    /**
     * Manage evenements page
     */
    public function evenements() {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied. Admin privileges required.');
            $this->redirect('admin/login');
            return;
        }

        $evenementModel = $this->model('Evenement');

        $data = [
            'title' => 'Manage Evenements - APPOLIOS',
            'description' => 'Evenement management panel',
            'evenements' => $evenementModel->findAllUpcoming(),
            'flash' => $this->getFlash()
        ];

        $this->view('BackOffice/admin/evenements', $data);
    }

    /**
     * Add evenement page
     */
    public function addEvenement() {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied. Admin privileges required.');
            $this->redirect('admin/login');
            return;
        }

        $data = [
            'title' => 'Add Evenement - APPOLIOS',
            'description' => 'Create a new evenement',
            'flash' => $this->getFlash()
        ];

        $this->view('BackOffice/admin/add_evenement', $data);
    }

    /**
     * Store new evenement
     */
    public function storeEvenement() {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/evenements');
            return;
        }

        $title = $this->sanitize($_POST['title'] ?? '');
        $description = $this->sanitize($_POST['description'] ?? '');
        $dateDebut = $this->sanitize($_POST['date_debut'] ?? '');
        $dateFin = $this->sanitize($_POST['date_fin'] ?? '');
        $heureDebut = $this->sanitize($_POST['heure_debut'] ?? '');
        $heureFin = $this->sanitize($_POST['heure_fin'] ?? '');
        $lieu = $this->sanitize($_POST['lieu'] ?? '');
        $capaciteMax = (int) ($_POST['capacite_max'] ?? 0);
        $type = $this->sanitize($_POST['type'] ?? 'general');
        $statut = $this->sanitize($_POST['statut'] ?? 'planifie');

        $errors = [];

        if (empty($title)) {
            $errors['title'] = 'Event title is required';
        }

        if (empty($description)) {
            $errors['description'] = 'Event description is required';
        }

        if (empty($dateDebut) || strtotime($dateDebut) === false) {
            $errors['date_debut'] = 'Valid start date is required';
        }

        $minDate = date('Y-m-d', strtotime('+1 day'));
        if (!empty($dateDebut) && strtotime($dateDebut) !== false && $dateDebut < $minDate) {
            $errors['date_debut'] = 'Start date must be at least tomorrow';
        }

        if (empty($heureDebut)) {
            $errors['heure_debut'] = 'Start time is required';
        }

        if (!empty($dateFin) && strtotime($dateFin) !== false && !empty($dateDebut) && strtotime($dateFin) < strtotime($dateDebut)) {
            $errors['date_fin'] = 'End date cannot be before start date';
        }

        if ($capaciteMax < 0) {
            $errors['capacite_max'] = 'Capacity must be a positive number';
        }

        if (!empty($errors)) {
            $this->setErrors($errors);
            $_SESSION['old'] = $_POST;
            $this->redirect('admin/add-evenement');
            return;
        }

        $eventDate = $dateDebut . ' ' . (!empty($heureDebut) ? $heureDebut : '00:00') . ':00';

        $evenementModel = $this->model('Evenement');
        $result = $evenementModel->create([
            'title' => $title,
            'titre' => $title,
            'description' => $description,
            'date_debut' => $dateDebut,
            'date_fin' => !empty($dateFin) ? $dateFin : null,
            'heure_debut' => !empty($heureDebut) ? $heureDebut : null,
            'heure_fin' => !empty($heureFin) ? $heureFin : null,
            'lieu' => $lieu,
            'capacite_max' => $capaciteMax > 0 ? $capaciteMax : null,
            'type' => $type,
            'statut' => $statut,
            'location' => $lieu,
            'event_date' => $eventDate,
            'created_by' => $_SESSION['user_id']
        ]);

        if ($result) {
            $this->setFlash('success', 'Evenement created successfully!');
            if (isset($_POST['action']) && $_POST['action'] === 'save_and_resources') {
                $this->redirect('admin/evenement-ressources&evenement_id=' . $result);
            } else {
                $this->redirect('admin/evenements');
            }
        } else {
            $this->setFlash('error', 'Failed to create evenement. Please try again.');
            $this->redirect('admin/add-evenement');
        }
    }

    /**
     * Edit evenement page
     */
    public function editEvenement($id) {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        $evenementModel = $this->model('Evenement');
        $evenement = $evenementModel->findById($id);

        if (!$evenement) {
            $this->setFlash('error', 'Evenement not found.');
            $this->redirect('admin/evenements');
            return;
        }

        $data = [
            'title' => 'Edit Evenement - APPOLIOS',
            'description' => 'Update evenement details',
            'evenement' => $evenement,
            'flash' => $this->getFlash()
        ];

        $this->view('BackOffice/admin/edit_evenement', $data);
    }

    /**
     * Update evenement
     */
    public function updateEvenement($id) {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/evenements');
            return;
        }

        $title = $this->sanitize($_POST['title'] ?? '');
        $description = $this->sanitize($_POST['description'] ?? '');
        $dateDebut = $this->sanitize($_POST['date_debut'] ?? '');
        $dateFin = $this->sanitize($_POST['date_fin'] ?? '');
        $heureDebut = $this->sanitize($_POST['heure_debut'] ?? '');
        $heureFin = $this->sanitize($_POST['heure_fin'] ?? '');
        $lieu = $this->sanitize($_POST['lieu'] ?? '');
        $capaciteMax = (int) ($_POST['capacite_max'] ?? 0);
        $type = $this->sanitize($_POST['type'] ?? 'general');
        $statut = $this->sanitize($_POST['statut'] ?? 'planifie');

        $errors = [];

        if (empty($title)) {
            $errors['title'] = 'Event title is required';
        }

        if (empty($description)) {
            $errors['description'] = 'Event description is required';
        }

        if (empty($dateDebut) || strtotime($dateDebut) === false) {
            $errors['date_debut'] = 'Valid start date is required';
        }

        $minDate = date('Y-m-d', strtotime('+1 day'));
        if (!empty($dateDebut) && strtotime($dateDebut) !== false && $dateDebut < $minDate) {
            $errors['date_debut'] = 'Start date must be at least tomorrow';
        }

        if (empty($heureDebut)) {
            $errors['heure_debut'] = 'Start time is required';
        }

        if (!empty($dateFin) && strtotime($dateFin) !== false && !empty($dateDebut) && strtotime($dateFin) < strtotime($dateDebut)) {
            $errors['date_fin'] = 'End date cannot be before start date';
        }

        if ($capaciteMax < 0) {
            $errors['capacite_max'] = 'Capacity must be a positive number';
        }

        if (!empty($errors)) {
            $this->setErrors($errors);
            $_SESSION['old'] = $_POST;
            $this->redirect('admin/edit-evenement/' . (int) $id);
            return;
        }

        $eventDate = $dateDebut . ' ' . (!empty($heureDebut) ? $heureDebut : '00:00') . ':00';

        $evenementModel = $this->model('Evenement');
        $result = $evenementModel->update($id, [
            'title' => $title,
            'titre' => $title,
            'description' => $description,
            'date_debut' => $dateDebut,
            'date_fin' => !empty($dateFin) ? $dateFin : null,
            'heure_debut' => !empty($heureDebut) ? $heureDebut : null,
            'heure_fin' => !empty($heureFin) ? $heureFin : null,
            'lieu' => $lieu,
            'capacite_max' => $capaciteMax > 0 ? $capaciteMax : null,
            'type' => $type,
            'statut' => $statut,
            'location' => $lieu,
            'event_date' => $eventDate
        ]);

        if ($result) {
            $this->setFlash('success', 'Evenement updated successfully!');
            $this->redirect('admin/evenements');
        } else {
            $this->setFlash('error', 'Failed to update evenement. Please try again.');
            $this->redirect('admin/edit-evenement/' . (int) $id);
        }
    }

    /**
     * Delete evenement
     */
    public function deleteEvenement($id) {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        $evenementModel = $this->model('Evenement');
        $evenement = $evenementModel->findById($id);

        if (!$evenement) {
            $this->setFlash('error', 'Evenement not found.');
            $this->redirect('admin/evenements');
            return;
        }

        if ($evenement['created_by'] != $_SESSION['user_id']) {
            $this->setFlash('error', 'You can only delete events that you have created.');
            $this->redirect('admin/evenements');
            return;
        }

        $result = $evenementModel->delete($id);

        if ($result) {
            $this->setFlash('success', 'Evenement deleted successfully!');
        } else {
            $this->setFlash('error', 'Failed to delete evenement.');
        }

        $this->redirect('admin/evenements');
    }

    /**
     * Evenement resources workspace page
     */
    public function evenementRessources() {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied. Admin privileges required.');
            $this->redirect('admin/login');
            return;
        }

        $ressourceModel = $this->model('EvenementRessource');
        $evenementModel = $this->model('Evenement');
        $selectedEvenementId = (int) ($_GET['evenement_id'] ?? 0);

        if ($selectedEvenementId <= 0) {
            $this->setFlash('error', 'Please choose an evenement first.');
            $this->redirect('admin/evenements');
            return;
        }

        $selectedEvenement = $evenementModel->findById($selectedEvenementId);
        if (!$selectedEvenement) {
            $this->setFlash('error', 'Selected evenement was not found.');
            $this->redirect('admin/evenements');
            return;
        }

        $editId = (int) ($_GET['edit_id'] ?? 0);
        $editResource = null;
        if ($editId > 0) {
            $candidate = $ressourceModel->findById($editId);
            if ($candidate && (int) $candidate['evenement_id'] === $selectedEvenementId) {
                $editResource = $candidate;
            }
        }

        $rules = $ressourceModel->getByTypeAndEvenement('rule', $selectedEvenementId);
        $materials = $ressourceModel->getByTypeAndEvenement('materiel', $selectedEvenementId);
        $plans = $ressourceModel->getByTypeAndEvenement('plan', $selectedEvenementId);

        $data = [
            'title' => 'Evenement Resources - APPOLIOS',
            'description' => 'Manage evenement rules, materiel, and day plans',
            'selectedEvenementId' => $selectedEvenementId,
            'selectedEvenement' => $selectedEvenement,
            'editResource' => $editResource,
            'rules' => $rules,
            'materials' => $materials,
            'plans' => $plans,
            'flash' => $this->getFlash()
        ];

        $this->view('BackOffice/admin/evenement_ressources', $data);
    }

    /**
     * Store one evenement resource item
     */
    public function storeEvenementRessource() {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/evenement-ressources');
            return;
        }

        $type = $this->sanitize($_POST['type'] ?? '');
        $title = $this->sanitize($_POST['title'] ?? '');
        $details = $this->sanitize($_POST['details'] ?? '');
        $evenementId = (int) ($_POST['evenement_id'] ?? 0);
        $isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
            || (isset($_POST['batch_mode']) && $_POST['batch_mode'] === '1');

        $errors = [];

        if (!in_array($type, ['rule', 'materiel', 'plan'], true)) {
            $errors[] = 'Invalid resource type.';
        }

        if (empty($title)) {
            $errors[] = 'Title is required.';
        }

        if ($evenementId <= 0) {
            $errors[] = 'Please select an evenement.';
        } else {
            $evenementModel = $this->model('Evenement');
            if (!$evenementModel->findById($evenementId)) {
                $errors[] = 'Selected evenement was not found.';
            }
        }

        if (!empty($errors)) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => implode(' ', $errors)
                ]);
                exit();
            }

            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            $this->redirect('admin/evenement-ressources&evenement_id=' . $evenementId);
            return;
        }

        $ressourceModel = $this->model('EvenementRessource');
        $createdId = $ressourceModel->create([
            'evenement_id' => $evenementId,
            'type' => $type,
            'title' => $title,
            'details' => $details,
            'created_by' => $_SESSION['user_id']
        ]);

        $isVerifiedInRightList = false;
        if ($createdId) {
            $isVerifiedInRightList = $ressourceModel->existsInListScope($createdId, $evenementId, $type);
        }

        if ($createdId && $isVerifiedInRightList) {
            $labels = [
                'rule' => 'Rule',
                'materiel' => 'Materiel',
                'plan' => 'Plan'
            ];

            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => $labels[$type] . ' saved and verified in list successfully.',
                    'verified_in_right_list' => true,
                    'resource_id' => (int) $createdId
                ]);
                exit();
            }

            $this->setFlash('success', $labels[$type] . ' added and verified in list successfully.');
        } else {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Save verification failed. Check the right list and try again.',
                    'verified_in_right_list' => false
                ]);
                exit();
            }

            $this->setFlash('error', 'Save verification failed. Check the right list and try again.');
        }

        $this->redirect('admin/evenement-ressources&evenement_id=' . $evenementId);
    }

    /**
     * Update one evenement resource item.
     */
    public function updateEvenementRessource($id) {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/evenements');
            return;
        }

        $evenementId = (int) ($_POST['evenement_id'] ?? 0);
        $title = $this->sanitize($_POST['title'] ?? '');
        $details = $this->sanitize($_POST['details'] ?? '');

        if ($evenementId <= 0 || empty($title)) {
            $this->setFlash('error', 'Please provide valid data before saving.');
            $this->redirect('admin/evenement-ressources&evenement_id=' . $evenementId . '&edit_id=' . (int) $id);
            return;
        }

        $ressourceModel = $this->model('EvenementRessource');
        $resource = $ressourceModel->findById($id);

        if (!$resource || (int) $resource['evenement_id'] !== $evenementId) {
            $this->setFlash('error', 'Resource not found for this evenement.');
            $this->redirect('admin/evenement-ressources&evenement_id=' . $evenementId);
            return;
        }

        $result = $ressourceModel->update($id, [
            'title' => $title,
            'details' => $details,
            'evenement_id' => $evenementId
        ]);

        if ($result) {
            $this->setFlash('success', 'Ressource updated successfully.');
        } else {
            $this->setFlash('error', 'Failed to update ressource.');
        }

        $this->redirect('admin/evenement-ressources&evenement_id=' . $evenementId);
    }

    /**
     * Delete one evenement resource item.
     */
    public function deleteEvenementRessource($id) {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/evenements');
            return;
        }

        $evenementId = (int) ($_POST['evenement_id'] ?? 0);
        if ($evenementId <= 0) {
            $this->setFlash('error', 'Invalid evenement context.');
            $this->redirect('admin/evenements');
            return;
        }

        $ressourceModel = $this->model('EvenementRessource');
        $result = $ressourceModel->deleteByEvenement($id, $evenementId);

        if ($result) {
            $this->setFlash('success', 'Ressource deleted successfully.');
        } else {
            $this->setFlash('error', 'Failed to delete ressource.');
        }

        $this->redirect('admin/evenement-ressources&evenement_id=' . $evenementId);
    }

    /**
     * List teacher evenement requests awaiting admin review.
     */
    public function evenementRequests() {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        $evenementModel = $this->model('Evenement');
        $data = [
            'title' => 'Evenement Requests - APPOLIOS',
            'description' => 'Review pending evenement requests from teachers',
            'requests' => $evenementModel->getPendingTeacherRequests(),
            'rejectedRequests' => $evenementModel->getRejectedTeacherRequests(),
            'flash' => $this->getFlash()
        ];

        $this->view('BackOffice/admin/evenement_requests', $data);
    }

    /**
     * Approve teacher evenement request.
     */
    public function approveEvenement($id) {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/evenement-requests');
            return;
        }

        $evenementModel = $this->model('Evenement');
        $event = $evenementModel->findById((int) $id);
        if (!$event) {
            $this->setFlash('error', 'Evenement request not found.');
            $this->redirect('admin/evenement-requests');
            return;
        }

        $result = $evenementModel->updateApprovalStatus((int) $id, 'approved', (int) $_SESSION['user_id']);
        if ($result) {
            $this->setFlash('success', 'Evenement request approved successfully.');
        } else {
            $this->setFlash('error', 'Failed to approve evenement request.');
        }

        $this->redirect('admin/evenement-requests');
    }

    /**
     * Reject teacher evenement request.
     */
    public function rejectEvenement($id) {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/evenement-requests');
            return;
        }

        $reason = $this->sanitize($_POST['rejection_reason'] ?? '');
        $evenementModel = $this->model('Evenement');
        $event = $evenementModel->findById((int) $id);
        if (!$event) {
            $this->setFlash('error', 'Evenement request not found.');
            $this->redirect('admin/evenement-requests');
            return;
        }

        $result = $evenementModel->updateApprovalStatus((int) $id, 'rejected', (int) $_SESSION['user_id'], $reason ?: null);
        if ($result) {
            $this->setFlash('success', 'Evenement request rejected.');
        } else {
            $this->setFlash('error', 'Failed to reject evenement request.');
        }

        $this->redirect('admin/evenement-requests');
    }

    /**
     * Extract course fields from request.
     * @return array
     */
    private function extractCoursePayload() {
        return [
            'title' => trim((string) ($_POST['title'] ?? '')),
            'description' => trim((string) ($_POST['description'] ?? '')),
            'video_url' => trim((string) ($_POST['video_url'] ?? ''))
        ];
    }

    /**
     * Validate course payload.
     * @param array $payload
     * @return array
     */
    private function validateCoursePayload($payload) {
        $errors = [];

        if (!isset($payload['title']) || strlen($payload['title']) < 3) {
            $errors['title'] = 'Title must contain at least 3 characters.';
        } elseif (strlen($payload['title']) > 120) {
            $errors['title'] = 'Title must not exceed 120 characters.';
        }

        if (!isset($payload['description']) || strlen($payload['description']) < 10) {
            $errors['description'] = 'Description must contain at least 10 characters.';
        } elseif (strlen($payload['description']) > 3000) {
            $errors['description'] = 'Description must not exceed 3000 characters.';
        }

        if ($payload['video_url'] !== '' && filter_var($payload['video_url'], FILTER_VALIDATE_URL) === false) {
            $errors['video_url'] = 'Video URL must be a valid URL.';
        }

        return $errors;
    }

    private function adminGroupesIndex($groupeModel): void
    {
        $data = [
            'title' => 'Admin Groupes - APPOLIOS',
            'groupes' => $groupeModel->getAllWithCreator(200, 0),
            'flash' => $this->getFlash(),
            'adminSidebarActive' => 'sl-groupes',
        ];
        $this->view('BackOffice/admin/sl_groupes/index', $data);
    }

    private function adminGroupesCreate(): void
    {
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['old']);
        $data = [
            'title' => 'Creer Groupe - APPOLIOS',
            'old' => $old,
            'errors' => $this->getErrors(),
            'flash' => $this->getFlash(),
            'adminSidebarActive' => 'sl-groupes',
        ];
        $this->view('BackOffice/admin/sl_groupes/create', $data);
    }

    private function adminGroupesStore($groupeModel): void
    {
        $payload = $this->extractGroupePayload();
        $errors = $this->validateGroupePayload($payload);
        if (!empty($errors)) {
            $this->setErrors($errors);
            $_SESSION['old'] = $_POST;
            $this->redirect('admin/sl-groupes/create');
            return;
        }

        $canStoreImg = $groupeModel->supportsStoredImage();
        $photo = ['url' => null, 'error' => null];
        $photoErr = [
            'upload_failed' => 'Echec du telechargement du fichier.',
            'invalid_type' => 'Utilisez une image JPEG, PNG, GIF ou WebP.',
            'too_large' => 'L image ne doit pas depasser 2 Mo.',
            'save_failed' => 'Impossible d enregistrer l image.',
        ];
        if ($canStoreImg) {
            $photo = $this->handleGroupPhotoUpload('group_photo', $photoErr);
        } elseif ((int) ($_FILES['group_photo']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $photo['error'] = 'Stockage image indisponible (base de donnees).';
        }
        if ($photo['error']) {
            $this->setErrors(['group_photo' => $photo['error']]);
            $_SESSION['old'] = $_POST;
            $this->redirect('admin/sl-groupes/create');
            return;
        }

        $createData = [
            'nom_groupe' => htmlspecialchars($payload['nom_groupe'], ENT_QUOTES, 'UTF-8'),
            'description' => htmlspecialchars($payload['description'], ENT_QUOTES, 'UTF-8'),
            'statut' => $payload['statut'],
            'id_createur' => (int) $_SESSION['user_id'],
            'approval_statut' => $payload['approval_statut'],
        ];
        if ($photo['url'] !== null) {
            $createData['image_url'] = $photo['url'];
        }

        $createdId = $groupeModel->create($createData);

        if ($createdId) {
            $groupeModel->ajouterMembre((int) $createdId, (int) $_SESSION['user_id'], 'admin');
            $this->setFlash('success', 'Groupe cree avec succes.');
        } else {
            if ($photo['url'] !== null) {
                $this->deleteGroupPhotoFileIfManaged($photo['url']);
            }
            $this->setFlash('error', 'Erreur lors de la creation du groupe.');
        }
        $this->redirect('admin/sl-groupes');
    }

    private function adminGroupesEdit($groupeModel, int $id): void
    {
        $groupe = $groupeModel->findById($id);
        if (!$groupe) {
            $this->setFlash('error', 'Groupe introuvable.');
            $this->redirect('admin/sl-groupes');
            return;
        }

        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['old']);
        $data = [
            'title' => 'Modifier Groupe - APPOLIOS',
            'groupe' => $groupe,
            'old' => $old,
            'errors' => $this->getErrors(),
            'flash' => $this->getFlash(),
            'adminSidebarActive' => 'sl-groupes',
        ];
        $this->view('BackOffice/admin/sl_groupes/edit', $data);
    }

    private function adminGroupesUpdate($groupeModel, int $id): void
    {
        $groupe = $groupeModel->findById($id);
        if (!$groupe) {
            $this->setFlash('error', 'Groupe introuvable.');
            $this->redirect('admin/sl-groupes');
            return;
        }

        $payload = $this->extractGroupePayload();
        $errors = $this->validateGroupePayload($payload);
        if (!empty($errors)) {
            $this->setErrors($errors);
            $_SESSION['old'] = $_POST;
            $this->redirect('admin/sl-groupes/' . $id . '/edit');
            return;
        }

        $canStoreImg = $groupeModel->supportsStoredImage();
        $photo = ['url' => null, 'error' => null];
        $photoErr = [
            'upload_failed' => 'Echec du telechargement du fichier.',
            'invalid_type' => 'Utilisez une image JPEG, PNG, GIF ou WebP.',
            'too_large' => 'L image ne doit pas depasser 2 Mo.',
            'save_failed' => 'Impossible d enregistrer l image.',
        ];
        if ($canStoreImg) {
            $photo = $this->handleGroupPhotoUpload('group_photo', $photoErr);
        } elseif ((int) ($_FILES['group_photo']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $photo['error'] = 'Stockage image indisponible (base de donnees).';
        }
        if ($photo['error']) {
            $this->setErrors(['group_photo' => $photo['error']]);
            $_SESSION['old'] = $_POST;
            $this->redirect('admin/sl-groupes/' . $id . '/edit');
            return;
        }

        $existingImg = $this->groupeImageUrlFromRow($groupe);
        $updateData = [
            'nom_groupe' => htmlspecialchars($payload['nom_groupe'], ENT_QUOTES, 'UTF-8'),
            'description' => htmlspecialchars($payload['description'], ENT_QUOTES, 'UTF-8'),
            'statut' => $payload['statut'],
            'approval_statut' => $payload['approval_statut'],
        ];
        if ($photo['url'] !== null) {
            $this->deleteGroupPhotoFileIfManaged($existingImg);
            $updateData['image_url'] = $photo['url'];
        }

        $ok = $groupeModel->updateGroupe($id, $updateData);
        $this->setFlash($ok ? 'success' : 'error', $ok ? 'Groupe mis a jour.' : 'Echec de mise a jour.');
        $this->redirect('admin/sl-groupes');
    }

    private function adminGroupesDelete($groupeModel, int $id): void
    {
        $ok = $groupeModel->delete($id);
        $this->setFlash($ok ? 'success' : 'error', $ok ? 'Groupe supprime.' : 'Suppression impossible.');
        $this->redirect('admin/sl-groupes');
    }

    private function adminGroupesApprove($groupeModel, int $id): void
    {
        $group = $groupeModel->findById($id);
        if (!$group) {
            $this->setFlash('error', 'Groupe introuvable.');
            $this->redirect('admin/sl-groupes');
            return;
        }

        $ok = $groupeModel->updateGroupe($id, [
            'nom_groupe' => $group['nom_groupe'],
            'description' => $group['description'],
            'statut' => $group['statut'],
            'approval_statut' => 'approuve',
        ]);
        $this->setFlash($ok ? 'success' : 'error', $ok ? 'Groupe approuve.' : 'Approbation echouee.');
        $this->redirect('admin/sl-groupes');
    }

    private function adminGroupesReject($groupeModel, int $id): void
    {
        $group = $groupeModel->findById($id);
        if (!$group) {
            $this->setFlash('error', 'Groupe introuvable.');
            $this->redirect('admin/sl-groupes');
            return;
        }

        $ok = $groupeModel->updateGroupe($id, [
            'nom_groupe' => $group['nom_groupe'],
            'description' => $group['description'],
            'statut' => $group['statut'],
            'approval_statut' => 'rejete',
        ]);
        $this->setFlash($ok ? 'success' : 'error', $ok ? 'Groupe rejete.' : 'Rejet echoue.');
        $this->redirect('admin/sl-groupes');
    }

    private function extractGroupePayload(): array
    {
        return [
            'nom_groupe' => trim((string) ($_POST['nom_groupe'] ?? '')),
            'description' => trim((string) ($_POST['description'] ?? '')),
            'statut' => trim((string) ($_POST['statut'] ?? 'actif')),
            'approval_statut' => trim((string) ($_POST['approval_statut'] ?? 'en_cours')),
        ];
    }

    private function validateGroupePayload(array $payload): array
    {
        $errors = [];
        if (!isset($payload['nom_groupe']) || strlen($payload['nom_groupe']) < 3) {
            $errors['nom_groupe'] = 'Le nom doit contenir au moins 3 caracteres.';
        } elseif (strlen($payload['nom_groupe']) > 100) {
            $errors['nom_groupe'] = 'Le nom ne doit pas depasser 100 caracteres.';
        }

        if (!isset($payload['description']) || strlen($payload['description']) < 10) {
            $errors['description'] = 'La description doit contenir au moins 10 caracteres.';
        } elseif (strlen($payload['description']) > 3000) {
            $errors['description'] = 'La description ne doit pas depasser 3000 caracteres.';
        }

        if (!in_array($payload['statut'], ['actif', 'archivé'], true)) {
            $errors['statut'] = 'Statut invalide.';
        }

        if (!in_array($payload['approval_statut'], ['en_cours', 'en_attente', 'approuve', 'rejete'], true)) {
            $errors['approval_statut'] = 'Statut d approbation invalide.';
        }

        return $errors;
    }

    private function adminDiscussionsIndex($discussionModel): void
    {
        $data = [
            'title' => 'SL Discussions - APPOLIOS',
            'discussions' => $discussionModel->getAllForAdmin(200),
            'flash' => $this->getFlash(),
            'adminSidebarActive' => 'sl-discussions',
        ];
        $this->view('BackOffice/admin/sl_discussions/index', $data);
    }
}