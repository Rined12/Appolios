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
require_once __DIR__ . '/../Model/Chapter.php';
require_once __DIR__ . '/QuizQuestionValidation.php';

class AdminController extends BaseController {

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

        $title = $this->sanitize($_POST['title'] ?? '');
        $description = $this->sanitize($_POST['description'] ?? '');
        $videoUrl = $this->sanitize($_POST['video_url'] ?? '');

        // Validation
        $errors = [];

        if (empty($title)) {
            $errors['title'] = 'Course title is required';
        }

        if (empty($description)) {
            $errors['description'] = 'Course description is required';
        }

        if (!empty($errors)) {
            $this->setErrors($errors);
            $_SESSION['old'] = $_POST;
            $this->redirect('admin/add-course');
            return;
        }

        $courseModel = $this->model('Course');

        $result = $courseModel->create([
            'title' => $title,
            'description' => $description,
            'video_url' => $videoUrl,
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

        $data = [
            'title' => 'Edit Course - APPOLIOS',
            'description' => 'Update course details',
            'course' => $course,
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

        $title = $this->sanitize($_POST['title'] ?? '');
        $description = $this->sanitize($_POST['description'] ?? '');
        $videoUrl = $this->sanitize($_POST['video_url'] ?? '');

        $courseModel = $this->model('Course');

        $result = $courseModel->update($id, [
            'title' => $title,
            'description' => $description,
            'video_url' => $videoUrl
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

    /* ---------- Chapitres / Quiz / Banque de questions (admin) ---------- */

    public function chapitres() {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        $chapterModel = $this->model('Chapter');
        $this->view('BackOffice/admin/chapitres', [
            'title' => 'Chapitres (admin) - ' . APP_NAME,
            'chapters' => $chapterModel->getAllWithCourseAuthors(),
            'flash' => $this->getFlash(),
        ]);
    }

    public function addChapitre() {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        $courseModel = $this->model('Course');
        $this->view('BackOffice/admin/chapter_form', [
            'title' => 'Nouveau chapitre - ' . APP_NAME,
            'course' => null,
            'chapter' => null,
            'allCourses' => $courseModel->getAllWithCreator(),
            'flash' => $this->getFlash(),
        ]);
    }

    public function storeChapitre() {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/add-chapitre');
            return;
        }
        $courseId = (int) ($_POST['course_id'] ?? 0);
        $title = $this->sanitize($_POST['title'] ?? '');
        $content = $this->sanitize($_POST['content'] ?? '');
        $sort = (int) ($_POST['sort_order'] ?? 0);

        if ($courseId <= 0 || $title === '') {
            $this->setFlash('error', 'Veuillez sélectionner un cours et saisir un titre.');
            $this->redirect('admin/add-chapitre');
            return;
        }

        $chapterModel = $this->model('Chapter');
        $chapterModel->create([
            'course_id' => $courseId,
            'title' => $title,
            'content' => $content,
            'sort_order' => $sort,
        ]);
        $this->setFlash('success', 'Chapitre créé.');
        $this->redirect('admin/chapitres');
    }

    public function editChapitre($id) {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        $chapterModel = $this->model('Chapter');
        $row = $chapterModel->findById((int) $id);
        if (!$row) {
            $this->setFlash('error', 'Chapitre introuvable.');
            $this->redirect('admin/chapitres');
            return;
        }
        $courseModel = $this->model('Course');
        $course = $courseModel->findById((int) ($row['course_id'] ?? 0));
        $this->view('BackOffice/admin/chapter_form', [
            'title' => 'Modifier le chapitre - ' . APP_NAME,
            'course' => $course,
            'chapter' => $row,
            'flash' => $this->getFlash(),
        ]);
    }

    public function updateChapitre($id) {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/chapitres');
            return;
        }
        $chapterModel = $this->model('Chapter');
        $row = $chapterModel->findById((int) $id);
        if (!$row) {
            $this->setFlash('error', 'Chapitre introuvable.');
            $this->redirect('admin/chapitres');
            return;
        }
        $title = $this->sanitize($_POST['title'] ?? '');
        $content = $this->sanitize($_POST['content'] ?? '');
        $sort = (int) ($_POST['sort_order'] ?? 0);
        if ($title === '') {
            $this->setFlash('error', 'Le titre est obligatoire.');
            $this->redirect('admin/edit-chapitre/' . (int) $id);
            return;
        }
        $chapterModel->update((int) $id, ['title' => $title, 'content' => $content, 'sort_order' => $sort]);
        $this->setFlash('success', 'Chapitre mis à jour.');
        $this->redirect('admin/chapitres');
    }

    public function deleteChapitre($id) {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        $chapterModel = $this->model('Chapter');
        $row = $chapterModel->findById((int) $id);
        if (!$row) {
            $this->setFlash('error', 'Chapitre introuvable.');
            $this->redirect('admin/chapitres');
            return;
        }
        $chapterModel->delete((int) $id);
        $this->setFlash('success', 'Chapitre supprimé.');
        $this->redirect('admin/chapitres');
    }

    public function quizzes() {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        $statsRows = $this->getQuizStatsForAdmin();
        $attemptsTotal = 0;
        $wSum = 0.0;
        foreach ($statsRows as $r) {
            $att = (int) ($r['attempts_count'] ?? 0);
            $avg = (float) ($r['avg_percentage'] ?? 0);
            $attemptsTotal += $att;
            $wSum += ($avg * $att);
        }
        $top = [
            'attempts_total' => $attemptsTotal,
            'avg_percentage' => $attemptsTotal > 0 ? round($wSum / (float) $attemptsTotal, 1) : 0.0,
        ];

        $this->view('BackOffice/admin/quizzes', [
            'title' => 'Quiz (admin) - ' . APP_NAME,
            'quizzes' => $this->getAllQuizzesForAdmin(),
            'quizTopStats' => $top,
            'flash' => $this->getFlash(),
        ]);
    }

    public function quizHistory() {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        $this->view('BackOffice/admin/quiz_history', [
            'title' => 'Historique des quiz - ' . APP_NAME,
            'quizzes' => $this->getQuizHistoryForAdmin(),
            'flash' => $this->getFlash(),
        ]);
    }

    public function quizStats() {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        $rows = $this->getQuizStatsForAdmin();
        $series = $this->getQuizAttemptSeriesMapForAdmin(120);

        $diffDist = [
            'beginner' => 0,
            'intermediate' => 0,
            'advanced' => 0,
        ];
        $statusDist = [
            'approved' => 0,
            'pending' => 0,
            'rejected' => 0,
        ];

        $totalQuizzes = count($rows);
        $totalAttempts = 0;
        $sumAvg = 0.0;
        $avgCount = 0;
        $approved = 0;

        foreach ($rows as $r) {
            $totalAttempts += (int) ($r['attempts_count'] ?? 0);
            if ((int) ($r['attempts_count'] ?? 0) > 0) {
                $sumAvg += (float) ($r['avg_percentage'] ?? 0);
                $avgCount++;
            }
            if ((string) ($r['status'] ?? '') === 'approved') {
                $approved++;
            }

            $d = (string) ($r['difficulty'] ?? 'beginner');
            if (!isset($diffDist[$d])) {
                $diffDist[$d] = 0;
            }
            $diffDist[$d]++;

            $st = (string) ($r['status'] ?? 'approved');
            if (!isset($statusDist[$st])) {
                $statusDist[$st] = 0;
            }
            $statusDist[$st]++;
        }

        $overallAvg = $avgCount > 0 ? round($sumAvg / $avgCount, 1) : 0.0;

        $trendRows = $this->getQuizAttemptsTrendForAdmin(21);
        $trendMap = [];
        foreach ($trendRows as $qid => $list) {
            foreach (($list ?? []) as $p) {
                $day = (string) ($p['day'] ?? '');
                if ($day === '') {
                    continue;
                }
                if (!isset($trendMap[$day])) {
                    $trendMap[$day] = ['day' => $day, 'count' => 0, 'avg_sum' => 0.0, 'avg_n' => 0];
                }
                $trendMap[$day]['count'] += (int) ($p['count'] ?? 0);
                $trendMap[$day]['avg_sum'] += (float) ($p['avg'] ?? 0);
                $trendMap[$day]['avg_n'] += 1;
            }
        }
        ksort($trendMap);
        $trend = [];
        foreach ($trendMap as $day => $info) {
            $n = (int) ($info['avg_n'] ?? 0);
            $trend[] = [
                'day' => (string) ($info['day'] ?? ''),
                'count' => (int) ($info['count'] ?? 0),
                'avg' => $n > 0 ? round(((float) ($info['avg_sum'] ?? 0)) / $n, 1) : 0.0,
            ];
        }

        $this->view('BackOffice/admin/quiz_stats', [
            'title' => 'Statistiques quiz - ' . APP_NAME,
            'rows' => $rows,
            'series' => $series,
            'charts' => [
                'difficulty' => $diffDist,
                'status' => $statusDist,
                'trend' => $trend,
            ],
            'kpis' => [
                'total_quizzes' => $totalQuizzes,
                'total_attempts' => $totalAttempts,
                'overall_avg' => $overallAvg,
                'approved_quizzes' => $approved,
            ],
            'flash' => $this->getFlash(),
        ]);
    }

    public function addQuiz() {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        $chapterModel = $this->model('Chapter');
        $chapters = $chapterModel->getAllWithCourseTitles();
        $this->view('BackOffice/admin/quiz_form', [
            'title' => 'Nouveau quiz - ' . APP_NAME,
            'quiz' => null,
            'chapters' => $chapters,
            'questionBank' => $this->getAllQuestionBankForAdmin(),
            'flash' => $this->getFlash(),
        ]);
    }

    public function storeQuiz() {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/add-quiz');
            return;
        }

        $chapterId = (int) ($_POST['chapter_id'] ?? 0);
        $chapterModel = $this->model('Chapter');
        if (!$chapterModel->findById($chapterId)) {
            $this->setFlash('error', 'Chapitre invalide.');
            $this->redirect('admin/add-quiz');
            return;
        }

        $meta = QuizQuestionValidation::validateQuizMeta($_POST);
        if (!empty($meta['errors'])) {
            $this->setFlash('error', $meta['errors'][0]);
            $this->redirect('admin/add-quiz');
            return;
        }

        $bankIds = isset($_POST['bank_question_ids']) && is_array($_POST['bank_question_ids']) ? $_POST['bank_question_ids'] : [];
        $questions = $this->appendBankQuestionsToQuiz(
            $this->normalizeQuizQuestionsFromPost($_POST),
            $bankIds,
            null
        );

        if (count($questions) < 1) {
            $this->setFlash('error', 'Ajoutez au moins une question (saisie manuelle ou depuis la banque).');
            $this->redirect('admin/add-quiz');
            return;
        }

        $qErr = QuizQuestionValidation::validateNormalizedQuestions($questions);
        if (!empty($qErr)) {
            $this->setFlash('error', $qErr[0]);
            $this->redirect('admin/add-quiz');
            return;
        }

        $createdId = $this->createAdminQuiz(
            (int) $_SESSION['user_id'],
            $chapterId,
            $this->sanitize($meta['title']),
            (string) $meta['difficulty'],
            $meta['tags'] !== null ? $this->sanitize($meta['tags']) : null,
            $meta['time_limit_sec'],
            $questions,
            'approved'
        );

        if ($createdId === false) {
            $this->setFlash('error', 'Impossible d’enregistrer le quiz (vérifiez la base de données).');
            $this->redirect('admin/add-quiz');
            return;
        }

        $this->setFlash('success', 'Quiz enregistré.');
        $this->redirect('admin/quizzes');
    }

    public function approveQuiz($id) {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        $ok = $this->setQuizStatus((int) $id, 'approved');
        $this->setFlash($ok ? 'success' : 'error', $ok ? 'Quiz approuvé.' : 'Impossible d’approuver ce quiz.');
        $this->redirect('admin/quizzes');
    }

    public function rejectQuiz($id) {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        $ok = $this->setQuizStatus((int) $id, 'rejected');
        $this->setFlash($ok ? 'success' : 'error', $ok ? 'Quiz refusé.' : 'Impossible de refuser ce quiz.');
        $this->redirect('admin/quizzes');
    }

    public function editQuiz($id) {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        $quiz = $this->findQuizWithChapterCourse((int) $id);
        if (!$quiz) {
            $this->setFlash('error', 'Quiz introuvable.');
            $this->redirect('admin/quizzes');
            return;
        }
        $chapterModel = $this->model('Chapter');
        $chapters = $chapterModel->getAllWithCourseTitles();
        $this->view('BackOffice/admin/quiz_form', [
            'title' => 'Modifier le quiz - ' . APP_NAME,
            'quiz' => $quiz,
            'chapters' => $chapters,
            'questionBank' => $this->getAllQuestionBankForAdmin(),
            'flash' => $this->getFlash(),
        ]);
    }

    public function updateQuiz($id) {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        $chapterId = (int) ($_POST['chapter_id'] ?? 0);
        $chapterModel = $this->model('Chapter');
        if (!$chapterModel->findById($chapterId)) {
            $this->setFlash('error', 'Chapitre invalide.');
            $this->redirect('admin/edit-quiz/' . (int) $id);
            return;
        }

        $meta = QuizQuestionValidation::validateQuizMeta($_POST);
        if (!empty($meta['errors'])) {
            $this->setFlash('error', $meta['errors'][0]);
            $this->redirect('admin/edit-quiz/' . (int) $id);
            return;
        }

        $bankIds = isset($_POST['bank_question_ids']) && is_array($_POST['bank_question_ids']) ? $_POST['bank_question_ids'] : [];
        $questions = $this->appendBankQuestionsToQuiz(
            $this->normalizeQuizQuestionsFromPost($_POST),
            $bankIds,
            null
        );

        if (count($questions) < 1) {
            $this->setFlash('error', 'Au moins une question requise (saisie ou banque).');
            $this->redirect('admin/edit-quiz/' . (int) $id);
            return;
        }

        $qErr = QuizQuestionValidation::validateNormalizedQuestions($questions);
        if (!empty($qErr)) {
            $this->setFlash('error', $qErr[0]);
            $this->redirect('admin/edit-quiz/' . (int) $id);
            return;
        }

        $updated = $this->updateAdminQuiz(
            (int) $id,
            $chapterId,
            $this->sanitize($meta['title']),
            (string) $meta['difficulty'],
            $meta['tags'] !== null ? $this->sanitize($meta['tags']) : null,
            $meta['time_limit_sec'],
            $questions
        );

        if ($updated === false) {
            $this->setFlash('error', 'Impossible de mettre à jour le quiz (vérifiez la base de données).');
            $this->redirect('admin/edit-quiz/' . (int) $id);
            return;
        }

        $this->setFlash('success', 'Quiz mis à jour.');
        $this->redirect('admin/quizzes');
    }

    public function deleteQuiz($id) {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        $existing = $this->findQuizWithChapterCourse((int) $id);
        if (!$existing) {
            $this->setFlash('error', 'Quiz introuvable.');
            $this->redirect('admin/quizzes');
            return;
        }
        $this->deleteAdminQuiz((int) $id);
        $this->setFlash('success', 'Quiz supprimé.');
        $this->redirect('admin/quizzes');
    }

    public function questions() {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        $questions = $this->getAllQuestionBankForAdmin();
        $usage = $this->getQuestionBankUsageStatsMapForAdmin();

        $top = [
            'questions_total' => count($questions),
            'used_questions' => 0,
            'attempts_total' => 0,
            'avg_percentage' => 0.0,
        ];
        $wSum = 0.0;
        foreach ($usage as $id => $u) {
            $qz = (int) ($u['quizzes'] ?? 0);
            $att = (int) ($u['attempts'] ?? 0);
            $avg = (float) ($u['avg'] ?? 0);
            if ($qz > 0) {
                $top['used_questions']++;
            }
            $top['attempts_total'] += $att;
            $wSum += ($avg * $att);
        }
        if ($top['attempts_total'] > 0) {
            $top['avg_percentage'] = round($wSum / (float) $top['attempts_total'], 1);
        }

        $this->view('BackOffice/admin/questions_bank', [
            'title' => 'Banque de questions (admin) - ' . APP_NAME,
            'questions' => $questions,
            'qbTopStats' => $top,
            'charts' => [
                'difficulty' => [
                    'beginner' => count(array_filter($questions, static fn($q) => (string) ($q['difficulty'] ?? 'beginner') === 'beginner')),
                    'intermediate' => count(array_filter($questions, static fn($q) => (string) ($q['difficulty'] ?? 'beginner') === 'intermediate')),
                    'advanced' => count(array_filter($questions, static fn($q) => (string) ($q['difficulty'] ?? 'beginner') === 'advanced')),
                ],
            ],
            'flash' => $this->getFlash(),
        ]);
    }

    public function addQuestion() {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        $this->view('BackOffice/admin/question_form', [
            'title' => 'Nouvelle question - ' . APP_NAME,
            'question' => null,
            'flash' => $this->getFlash(),
        ]);
    }

    public function storeQuestion() {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/add-question');
            return;
        }

        $title = $this->sanitize($_POST['title'] ?? '');
        $questionText = $this->sanitize($_POST['question_text'] ?? '');
        $optionsRaw = $_POST['options'] ?? [];
        $opts = is_array($optionsRaw) ? array_values(array_filter(array_map([$this, 'sanitize'], $optionsRaw))) : [];
        $correct = (int) ($_POST['correct_answer'] ?? 0);
        $tags = $this->sanitize($_POST['tags'] ?? '');
        $difficulty = QuizQuestionValidation::normalizeDifficulty($_POST['difficulty'] ?? 'beginner');

        $errs = QuizQuestionValidation::validateQuestionBankFields($title, $questionText, $opts, $correct, $tags);
        if (!empty($errs)) {
            $this->setFlash('error', $errs[0]);
            $this->redirect('admin/add-question');
            return;
        }

        $this->createQuestionBankQuestion(
            (int) $_SESSION['user_id'],
            $title !== '' ? $title : null,
            $questionText,
            $opts,
            $correct,
            $tags !== '' ? $tags : null,
            $difficulty
        );

        $this->setFlash('success', 'Question enregistrée.');
        $this->redirect('admin/questions');
    }

    public function editQuestion($id) {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        $row = $this->findQuestionByIdDecoded((int) $id);
        if (!$row) {
            $this->setFlash('error', 'Question introuvable.');
            $this->redirect('admin/questions');
            return;
        }
        $this->view('BackOffice/admin/question_form', [
            'title' => 'Modifier la question - ' . APP_NAME,
            'question' => $row,
            'flash' => $this->getFlash(),
        ]);
    }

    public function updateQuestion($id) {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/questions');
            return;
        }

        $existing = $this->findQuestionByIdDecoded((int) $id);
        if (!$existing) {
            $this->setFlash('error', 'Question introuvable.');
            $this->redirect('admin/questions');
            return;
        }

        $title = $this->sanitize($_POST['title'] ?? '');
        $questionText = $this->sanitize($_POST['question_text'] ?? '');
        $optionsRaw = $_POST['options'] ?? [];
        $opts = is_array($optionsRaw) ? array_values(array_filter(array_map([$this, 'sanitize'], $optionsRaw))) : [];
        $correct = (int) ($_POST['correct_answer'] ?? 0);
        $tags = $this->sanitize($_POST['tags'] ?? '');
        $difficulty = QuizQuestionValidation::normalizeDifficulty($_POST['difficulty'] ?? 'beginner');

        $errs = QuizQuestionValidation::validateQuestionBankFields($title, $questionText, $opts, $correct, $tags);
        if (!empty($errs)) {
            $this->setFlash('error', $errs[0]);
            $this->redirect('admin/edit-question/' . (int) $id);
            return;
        }

        $this->updateQuestionBank(
            (int) $id,
            $title !== '' ? $title : null,
            $questionText,
            $opts,
            $correct,
            $tags !== '' ? $tags : null,
            $difficulty
        );

        $this->setFlash('success', 'Question mise à jour.');
        $this->redirect('admin/questions');
    }

    public function deleteQuestion($id) {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        $existing = $this->findQuestionByIdDecoded((int) $id);
        if (!$existing) {
            $this->setFlash('error', 'Question introuvable.');
            $this->redirect('admin/questions');
            return;
        }
        $this->deleteQuestionBank((int) $id);
        $this->setFlash('success', 'Question supprimée.');
        $this->redirect('admin/questions');
    }

    private function decodeQuestionsJson(string $json): array
    {
        $d = json_decode($json !== '' ? $json : '[]', true);
        return is_array($d) ? $d : [];
    }

    private function getAllQuizzesForAdmin(): array
    {
        $db = $this->db();
        $sql = "SELECT q.*, ch.title AS chapter_title, c.title AS course_title, u.name AS author_name
                FROM quizzes q
                JOIN chapters ch ON ch.id = q.chapter_id
                JOIN courses c ON c.id = ch.course_id
                JOIN users u ON u.id = q.created_by
                ORDER BY q.created_at DESC";
        $stmt = $db->query($sql);
        $rows = $stmt ? $stmt->fetchAll() : [];
        foreach ($rows as &$r) {
            $r['questions'] = $this->decodeQuestionsJson((string) ($r['questions_json'] ?? '[]'));
        }
        unset($r);
        return $rows;
    }

    private function getQuizHistoryForAdmin(): array
    {
        $db = $this->db();
        $sql = "SELECT q.id, q.title, q.status, q.created_at, q.updated_at,
                       ch.title AS chapter_title, c.title AS course_title,
                       u.name AS author_name
                FROM quizzes q
                JOIN chapters ch ON ch.id = q.chapter_id
                JOIN courses c ON c.id = ch.course_id
                JOIN users u ON u.id = q.created_by
                ORDER BY q.updated_at DESC, q.created_at DESC";
        $stmt = $db->query($sql);
        return $stmt ? $stmt->fetchAll() : [];
    }

    private function getQuizStatsForAdmin(): array
    {
        $db = $this->db();
        $sql = "SELECT q.id, q.title, q.difficulty, q.status, q.created_at,
                       ch.title AS chapter_title, c.title AS course_title,
                       COUNT(a.id) AS attempts_count,
                       COALESCE(ROUND(AVG(a.percentage), 1), 0) AS avg_percentage,
                       COALESCE(MAX(a.percentage), 0) AS best_percentage,
                       COALESCE(MAX(a.score), 0) AS best_score,
                       COALESCE(MAX(a.total), 0) AS best_total,
                       MAX(a.submitted_at) AS last_attempt_at
                FROM quizzes q
                JOIN chapters ch ON ch.id = q.chapter_id
                JOIN courses c ON c.id = ch.course_id
                LEFT JOIN quiz_attempts a ON a.quiz_id = q.id
                GROUP BY q.id
                ORDER BY attempts_count DESC, q.created_at DESC";
        $stmt = $db->query($sql);
        return $stmt ? $stmt->fetchAll() : [];
    }

    private function getQuizAttemptSeriesMapForAdmin(int $limitPerQuiz = 120): array
    {
        $limitPerQuiz = max(10, min(300, $limitPerQuiz));
        $db = $this->db();

        $sql = "SELECT a.quiz_id, a.submitted_at, a.percentage, a.score, a.total
                FROM quiz_attempts a
                JOIN (
                    SELECT x.id
                    FROM (
                        SELECT a2.id,
                               ROW_NUMBER() OVER (PARTITION BY a2.quiz_id ORDER BY a2.submitted_at ASC) AS rn
                        FROM quiz_attempts a2
                    ) x
                ) keep ON keep.id = a.id
                ORDER BY a.quiz_id ASC, a.submitted_at ASC";

        $rows = [];
        try {
            $stmt = $db->query($sql);
            $rows = $stmt ? $stmt->fetchAll() : [];
        } catch (Throwable $e) {
            $rows = [];
        }

        $out = [];
        foreach ($rows as $r) {
            $qid = (int) ($r['quiz_id'] ?? 0);
            if ($qid <= 0) {
                continue;
            }
            if (!isset($out[$qid])) {
                $out[$qid] = [];
            }
            $out[$qid][] = [
                'at' => isset($r['submitted_at']) ? (string) $r['submitted_at'] : null,
                'percentage' => (int) ($r['percentage'] ?? 0),
                'score' => (int) ($r['score'] ?? 0),
                'total' => (int) ($r['total'] ?? 0),
            ];
            if (count($out[$qid]) > $limitPerQuiz) {
                array_shift($out[$qid]);
            }
        }
        return $out;
    }

    private function getQuizAttemptsTrendForAdmin(int $days = 21): array
    {
        $days = max(1, min(90, $days));
        $db = $this->db();
        $sql = "SELECT a.quiz_id, DATE(a.submitted_at) AS day,
                       COUNT(*) AS count,
                       ROUND(AVG(a.percentage), 1) AS avg
                FROM quiz_attempts a
                WHERE a.submitted_at >= DATE_SUB(NOW(), INTERVAL {$days} DAY)
                GROUP BY a.quiz_id, DATE(a.submitted_at)
                ORDER BY a.quiz_id ASC, day ASC";
        $stmt = $db->query($sql);
        $rows = $stmt ? $stmt->fetchAll() : [];
        $out = [];
        foreach ($rows as $r) {
            $qid = (int) ($r['quiz_id'] ?? 0);
            if ($qid <= 0) {
                continue;
            }
            if (!isset($out[$qid])) {
                $out[$qid] = [];
            }
            $out[$qid][] = [
                'day' => (string) ($r['day'] ?? ''),
                'count' => (int) ($r['count'] ?? 0),
                'avg' => (float) ($r['avg'] ?? 0),
            ];
        }
        return $out;
    }

    private function findQuizWithChapterCourse(int $id): ?array
    {
        $db = $this->db();
        $sql = "SELECT q.*, ch.course_id, ch.title AS chapter_title, c.title AS course_title,
                       c.created_by AS course_owner_id
                FROM quizzes q
                JOIN chapters ch ON ch.id = q.chapter_id
                JOIN courses c ON c.id = ch.course_id
                WHERE q.id = ?
                LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([(int) $id]);
        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }
        $row['questions'] = $this->decodeQuestionsJson((string) ($row['questions_json'] ?? '[]'));
        return $row;
    }

    private function ensureQuizLinkTable(): void
    {
        $db = $this->db();
        $sql = "CREATE TABLE IF NOT EXISTS quiz_question_bank (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    quiz_id INT NOT NULL,
                    question_bank_id INT NOT NULL,
                    sort_order INT NOT NULL DEFAULT 0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    UNIQUE KEY uq_quiz_question (quiz_id, question_bank_id),
                    INDEX idx_quiz_sort (quiz_id, sort_order)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        try {
            $db->exec($sql);
        } catch (Throwable $e) {
        }
    }

    private function ensureQuizStatusColumn(): void
    {
        $db = $this->db();
        try {
            $db->exec("ALTER TABLE quizzes ADD COLUMN status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'approved'");
        } catch (Throwable $e) {
        }
    }

    private function createAdminQuiz(int $adminId, int $chapterId, string $title, string $difficulty, ?string $tags, $timeLimitSec, array $questions, string $status)
    {
        $this->ensureQuizLinkTable();
        $this->ensureQuizStatusColumn();
        $db = $this->db();
        $sql = "INSERT INTO quizzes (chapter_id, title, difficulty, tags, time_limit_sec, questions_json, created_by, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $json = json_encode($questions);
        $ok = $stmt->execute([
            (int) $chapterId,
            (string) $title,
            (string) $difficulty,
            $tags,
            $timeLimitSec !== null && $timeLimitSec !== '' ? (int) $timeLimitSec : null,
            $json !== false ? $json : '[]',
            (int) $adminId,
            (string) $status,
        ]);
        if (!$ok) {
            return false;
        }
        $quizId = (int) $db->lastInsertId();

        $bankIds = [];
        foreach ($questions as $q) {
            if (is_array($q) && isset($q['question_bank_id'])) {
                $bid = (int) $q['question_bank_id'];
                if ($bid > 0) {
                    $bankIds[] = $bid;
                }
            }
        }
        $bankIds = array_values(array_unique($bankIds));
        if (!empty($bankIds)) {
            $ins = $db->prepare("INSERT IGNORE INTO quiz_question_bank (quiz_id, question_bank_id, sort_order) VALUES (?, ?, ?)");
            foreach ($bankIds as $so => $bid) {
                $ins->execute([$quizId, $bid, (int) $so]);
            }
        }

        return $quizId;
    }

    private function updateAdminQuiz(int $quizId, int $chapterId, string $title, string $difficulty, ?string $tags, $timeLimitSec, array $questions): bool
    {
        $this->ensureQuizLinkTable();
        $this->ensureQuizStatusColumn();
        $db = $this->db();
        $sql = "UPDATE quizzes
                SET chapter_id = ?, title = ?, difficulty = ?, tags = ?, time_limit_sec = ?, questions_json = ?, updated_at = CURRENT_TIMESTAMP
                WHERE id = ?";
        $json = json_encode($questions);
        $stmt = $db->prepare($sql);
        $ok = $stmt->execute([
            (int) $chapterId,
            (string) $title,
            (string) $difficulty,
            $tags,
            $timeLimitSec !== null && $timeLimitSec !== '' ? (int) $timeLimitSec : null,
            $json !== false ? $json : '[]',
            (int) $quizId,
        ]);
        if (!$ok) {
            return false;
        }

        $db->prepare("DELETE FROM quiz_question_bank WHERE quiz_id = ?")->execute([(int) $quizId]);
        $bankIds = [];
        foreach ($questions as $q) {
            if (is_array($q) && isset($q['question_bank_id'])) {
                $bid = (int) $q['question_bank_id'];
                if ($bid > 0) {
                    $bankIds[] = $bid;
                }
            }
        }
        $bankIds = array_values(array_unique($bankIds));
        if (!empty($bankIds)) {
            $ins = $db->prepare("INSERT IGNORE INTO quiz_question_bank (quiz_id, question_bank_id, sort_order) VALUES (?, ?, ?)");
            foreach ($bankIds as $so => $bid) {
                $ins->execute([(int) $quizId, (int) $bid, (int) $so]);
            }
        }
        return true;
    }

    private function deleteAdminQuiz(int $quizId): bool
    {
        $db = $this->db();
        try {
            $db->prepare("DELETE FROM quiz_question_bank WHERE quiz_id = ?")->execute([(int) $quizId]);
        } catch (Throwable $e) {
        }
        $stmt = $db->prepare("DELETE FROM quizzes WHERE id = ?");
        return $stmt->execute([(int) $quizId]);
    }

    private function setQuizStatus(int $quizId, string $status): bool
    {
        $this->ensureQuizStatusColumn();
        $status = in_array($status, ['pending', 'approved', 'rejected'], true) ? $status : 'approved';
        $db = $this->db();
        $stmt = $db->prepare("UPDATE quizzes SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        return $stmt->execute([(string) $status, (int) $quizId]);
    }

    private function normalizeQuizQuestionsFromPost(array $post): array
    {
        $raw = $post['questions'] ?? [];
        if (!is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $q) {
            if (!is_array($q)) {
                continue;
            }
            $bankId = isset($q['question_bank_id']) ? (int) $q['question_bank_id'] : 0;
            $text = isset($q['question']) ? trim((string) $q['question']) : '';
            if ($text === '' && isset($q['question_text'])) {
                $text = trim((string) $q['question_text']);
            }
            $opts = $q['options'] ?? [];
            if (!is_array($opts)) {
                $opts = [];
            }
            $opts = array_values(array_filter(array_map(static function ($o) {
                return trim((string) $o);
            }, $opts)));
            $ca = isset($q['correctAnswer']) ? (int) $q['correctAnswer'] : (isset($q['correct_answer']) ? (int) $q['correct_answer'] : 0);
            if ($text === '' || count($opts) < 2) {
                continue;
            }
            if ($ca < 0 || $ca >= count($opts)) {
                $ca = 0;
            }

            $item = [
                'question' => $text,
                'question_text' => $text,
                'options' => $opts,
                'correctAnswer' => $ca,
                'correct_answer' => $ca,
            ];
            if ($bankId > 0) {
                $item['question_bank_id'] = $bankId;
            }
            $out[] = $item;
        }
        return $out;
    }

    private function appendBankQuestionsToQuiz(array $baseQuestions, array $bankIds, ?int $restrictToUserId): array
    {
        $bankIds = array_map('intval', $bankIds);
        $bankIds = array_values(array_filter($bankIds, static fn($v) => $v > 0));
        if (empty($bankIds)) {
            return $baseQuestions;
        }

        $db = $this->db();
        $placeholders = implode(',', array_fill(0, count($bankIds), '?'));
        $params = $bankIds;
        $sql = "SELECT * FROM question_bank WHERE id IN ({$placeholders})";
        if ($restrictToUserId !== null) {
            $sql .= " AND created_by = ?";
            $params[] = (int) $restrictToUserId;
        }
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();
        foreach ($rows as &$r) {
            $d = json_decode($r['options_json'] ?? '[]', true);
            $r['options'] = is_array($d) ? $d : [];
        }
        unset($r);

        $out = $baseQuestions;
        foreach ($rows as $r) {
            $opts = $r['options'] ?? [];
            if (!is_array($opts)) {
                $opts = [];
            }
            $out[] = [
                'question_bank_id' => (int) ($r['id'] ?? 0),
                'question' => (string) ($r['question_text'] ?? ''),
                'question_text' => (string) ($r['question_text'] ?? ''),
                'options' => array_values($opts),
                'correctAnswer' => (int) ($r['correct_answer'] ?? 0),
                'correct_answer' => (int) ($r['correct_answer'] ?? 0),
            ];
        }
        return $out;
    }

    private function getAllQuestionBankForAdmin(): array
    {
        $db = $this->db();
        $sql = "SELECT qb.*, u.name AS author_name
                FROM question_bank qb
                JOIN users u ON u.id = qb.created_by
                ORDER BY qb.created_at DESC";
        $stmt = $db->query($sql);
        $rows = $stmt ? $stmt->fetchAll() : [];
        foreach ($rows as &$r) {
            $d = json_decode($r['options_json'] ?? '[]', true);
            $r['options'] = is_array($d) ? $d : [];
        }
        unset($r);
        return $rows;
    }

    private function getQuestionBankUsageStatsMapForAdmin(): array
    {
        $db = $this->db();
        $sql = "SELECT qb.id AS question_bank_id,
                       COUNT(DISTINCT qqb.quiz_id) AS quizzes_count,
                       COUNT(a.id) AS attempts_count,
                       COALESCE(ROUND(AVG(a.percentage), 1), 0) AS avg_percentage,
                       MAX(a.submitted_at) AS last_attempt_at
                FROM question_bank qb
                LEFT JOIN quiz_question_bank qqb ON qqb.question_bank_id = qb.id
                LEFT JOIN quiz_attempts a ON a.quiz_id = qqb.quiz_id
                GROUP BY qb.id";
        $stmt = $db->query($sql);
        $rows = $stmt ? $stmt->fetchAll() : [];
        $out = [];
        foreach ($rows as $r) {
            $id = (int) ($r['question_bank_id'] ?? 0);
            if ($id <= 0) {
                continue;
            }
            $out[$id] = [
                'quizzes' => (int) ($r['quizzes_count'] ?? 0),
                'attempts' => (int) ($r['attempts_count'] ?? 0),
                'avg' => (float) ($r['avg_percentage'] ?? 0),
                'last_attempt_at' => isset($r['last_attempt_at']) ? (string) $r['last_attempt_at'] : null,
            ];
        }
        return $out;
    }

    private function createQuestionBankQuestion(int $adminId, ?string $title, string $questionText, array $options, int $correctAnswer, ?string $tags, string $difficulty)
    {
        $db = $this->db();
        $sql = "INSERT INTO question_bank (title, question_text, options_json, correct_answer, tags, difficulty, created_by)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $json = json_encode(is_array($options) ? array_values($options) : []);
        $ok = $stmt->execute([
            $title,
            (string) $questionText,
            $json !== false ? $json : '[]',
            (int) $correctAnswer,
            $tags,
            (string) $difficulty,
            (int) $adminId,
        ]);
        if (!$ok) {
            return false;
        }
        return (int) $db->lastInsertId();
    }

    private function findQuestionByIdDecoded(int $questionId): ?array
    {
        $db = $this->db();
        $stmt = $db->prepare("SELECT * FROM question_bank WHERE id = ? LIMIT 1");
        $stmt->execute([(int) $questionId]);
        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }
        $d = json_decode($row['options_json'] ?? '[]', true);
        $row['options'] = is_array($d) ? $d : [];
        return $row;
    }

    private function updateQuestionBank(int $questionId, ?string $title, string $questionText, array $options, int $correctAnswer, ?string $tags, string $difficulty): bool
    {
        $db = $this->db();
        $sql = "UPDATE question_bank
                SET title = ?, question_text = ?, options_json = ?, correct_answer = ?, tags = ?, difficulty = ?, updated_at = CURRENT_TIMESTAMP
                WHERE id = ?";
        $stmt = $db->prepare($sql);
        $json = json_encode(is_array($options) ? array_values($options) : []);
        return $stmt->execute([
            $title,
            (string) $questionText,
            $json !== false ? $json : '[]',
            (int) $correctAnswer,
            $tags,
            (string) $difficulty,
            (int) $questionId,
        ]);
    }

    private function deleteQuestionBank(int $questionId): bool
    {
        $db = $this->db();
        $stmt = $db->prepare("DELETE FROM question_bank WHERE id = ?");
        return $stmt->execute([(int) $questionId]);
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
}