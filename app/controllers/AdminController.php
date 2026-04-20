<?php
/**
 * APPOLIOS Admin Controller
 * Handles admin dashboard and management
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Course.php';
require_once __DIR__ . '/../models/Enrollment.php';
require_once __DIR__ . '/../models/Evenement.php';
require_once __DIR__ . '/../models/EvenementRessource.php';

class AdminController extends Controller {

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

        $this->view('admin/dashboard', $data);
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

        $this->view('admin/users', $data);
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

        $this->view('admin/courses', $data);
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

        $this->view('admin/add_course', $data);
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
            $errors[] = 'Course title is required';
        }

        if (empty($description)) {
            $errors[] = 'Course description is required';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
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

        $this->view('admin/edit_course', $data);
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

        $this->view('admin/teachers', $data);
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

        $this->view('admin/add_teacher', $data);
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
            $errors[] = 'Name is required';
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Valid email is required';
        }

        if (empty($password) || strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters';
        }

        $userModel = $this->model('User');

        if ($userModel->emailExists($email)) {
            $errors[] = 'Email already registered';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
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

        $this->view('admin/evenements', $data);
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

        $this->view('admin/add_evenement', $data);
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
            $errors[] = 'Event title is required';
        }

        if (empty($description)) {
            $errors[] = 'Event description is required';
        }

        if (empty($dateDebut) || strtotime($dateDebut) === false) {
            $errors[] = 'Valid start date is required';
        }

        $minDate = date('Y-m-d', strtotime('+1 day'));
        if (!empty($dateDebut) && strtotime($dateDebut) !== false && $dateDebut < $minDate) {
            $errors[] = 'Start date must be at least tomorrow';
        }

        if (empty($heureDebut)) {
            $errors[] = 'Start time is required';
        }

        if (!empty($dateFin) && strtotime($dateFin) !== false && !empty($dateDebut) && strtotime($dateFin) < strtotime($dateDebut)) {
            $errors[] = 'End date cannot be before start date';
        }

        if ($capaciteMax < 0) {
            $errors[] = 'Capacity must be a positive number';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
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
            $this->redirect('admin/evenements');
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

        $this->view('admin/edit_evenement', $data);
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
            $errors[] = 'Event title is required';
        }

        if (empty($description)) {
            $errors[] = 'Event description is required';
        }

        if (empty($dateDebut) || strtotime($dateDebut) === false) {
            $errors[] = 'Valid start date is required';
        }

        $minDate = date('Y-m-d', strtotime('+1 day'));
        if (!empty($dateDebut) && strtotime($dateDebut) !== false && $dateDebut < $minDate) {
            $errors[] = 'Start date must be at least tomorrow';
        }

        if (empty($heureDebut)) {
            $errors[] = 'Start time is required';
        }

        if (!empty($dateFin) && strtotime($dateFin) !== false && !empty($dateDebut) && strtotime($dateFin) < strtotime($dateDebut)) {
            $errors[] = 'End date cannot be before start date';
        }

        if ($capaciteMax < 0) {
            $errors[] = 'Capacity must be a positive number';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
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

        $this->view('admin/evenement_ressources', $data);
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
            'flash' => $this->getFlash()
        ];

        $this->view('admin/evenement_requests', $data);
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

        $result = $evenementModel->setApprovalStatus((int) $id, 'approved', (int) $_SESSION['user_id']);
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

        $result = $evenementModel->setApprovalStatus((int) $id, 'rejected', (int) $_SESSION['user_id'], $reason ?: null);
        if ($result) {
            $this->setFlash('success', 'Evenement request rejected.');
        } else {
            $this->setFlash('error', 'Failed to reject evenement request.');
        }

        $this->redirect('admin/evenement-requests');
    }

    /* ---------- Chapitres, quiz, banque (rôle admin : accès complet) ---------- */

    public function chapitresAddGlobal() {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        $courseModel = $this->model('Course');
        $this->view('admin/chapter_form', [
            'title' => 'Nouveau chapitre - APPOLIOS',
            'course' => null,
            'chapter' => null,
            'allCourses' => $courseModel->getAllWithCreator(),
            'flash' => $this->getFlash(),
        ]);
    }

    public function chapitresStoreGlobal() {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/chapitres/add');
            return;
        }
        $courseId = (int) ($_POST['course_id'] ?? 0);
        $courseModel = $this->model('Course');
        if (!$courseModel->findById($courseId)) {
            $this->setFlash('error', 'Cours invalide.');
            $this->redirect('admin/chapitres/add');
            return;
        }
        $title = $this->sanitize($_POST['title'] ?? '');
        $content = $this->sanitize($_POST['content'] ?? '');
        $sort = (int) ($_POST['sort_order'] ?? 0);
        if ($title === '') {
            $this->setFlash('error', 'Le titre est obligatoire.');
            $this->redirect('admin/chapitres/add');
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

    public function chapitres() {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        $chapterModel = $this->model('Chapter');
        $chapters = $chapterModel->getAllWithCourseAuthors();
        $this->view('admin/chapitres', [
            'title' => 'Chapitres (admin) - APPOLIOS',
            'chapters' => $chapters,
            'flash' => $this->getFlash(),
        ]);
    }

    public function courseChapitres($courseId) {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        $courseModel = $this->model('Course');
        $course = $courseModel->findById((int) $courseId);
        if (!$course) {
            $this->setFlash('error', 'Cours introuvable.');
            $this->redirect('admin/courses');
            return;
        }
        $chapterModel = $this->model('Chapter');
        $this->view('admin/course_chapitres', [
            'title' => 'Chapitres du cours - APPOLIOS',
            'course' => $course,
            'chapters' => $chapterModel->getByCourseId((int) $courseId),
            'flash' => $this->getFlash(),
        ]);
    }

    public function addChapter($courseId) {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        $courseModel = $this->model('Course');
        $course = $courseModel->findById((int) $courseId);
        if (!$course) {
            $this->setFlash('error', 'Cours introuvable.');
            $this->redirect('admin/courses');
            return;
        }
        $this->view('admin/chapter_form', [
            'title' => 'Nouveau chapitre - APPOLIOS',
            'course' => $course,
            'chapter' => null,
            'flash' => $this->getFlash(),
        ]);
    }

    public function storeChapter($courseId) {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/course/' . (int) $courseId . '/chapitres/add');
            return;
        }
        $courseModel = $this->model('Course');
        if (!$courseModel->findById((int) $courseId)) {
            $this->setFlash('error', 'Cours introuvable.');
            $this->redirect('admin/courses');
            return;
        }
        $title = $this->sanitize($_POST['title'] ?? '');
        $content = $this->sanitize($_POST['content'] ?? '');
        $sort = (int) ($_POST['sort_order'] ?? 0);
        if ($title === '') {
            $this->setFlash('error', 'Le titre est obligatoire.');
            $this->redirect('admin/course/' . (int) $courseId . '/chapitres/add');
            return;
        }
        $chapterModel = $this->model('Chapter');
        $chapterModel->create([
            'course_id' => (int) $courseId,
            'title' => $title,
            'content' => $content,
            'sort_order' => $sort,
        ]);
        $this->setFlash('success', 'Chapitre créé.');
        $this->redirect('admin/chapitres');
    }

    public function editChapter($id) {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        $chapterModel = $this->model('Chapter');
        $row = $chapterModel->findWithCourse((int) $id);
        if (!$row) {
            $this->setFlash('error', 'Chapitre introuvable.');
            $this->redirect('admin/chapitres');
            return;
        }
        $courseModel = $this->model('Course');
        $course = $courseModel->findById((int) $row['course_id']);
        $this->view('admin/chapter_form', [
            'title' => 'Modifier le chapitre - APPOLIOS',
            'course' => $course,
            'chapter' => $row,
            'flash' => $this->getFlash(),
        ]);
    }

    public function updateChapter($id) {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/chapitres');
            return;
        }
        $chapterModel = $this->model('Chapter');
        $row = $chapterModel->findWithCourse((int) $id);
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
            $this->redirect('admin/chapitre/' . (int) $id . '/edit');
            return;
        }
        $chapterModel->update((int) $id, ['title' => $title, 'content' => $content, 'sort_order' => $sort]);
        $this->setFlash('success', 'Chapitre mis à jour.');
        $this->redirect('admin/chapitres');
    }

    public function deleteChapter($id) {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        $chapterModel = $this->model('Chapter');
        $row = $chapterModel->findWithCourse((int) $id);
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
        $quizModel = $this->model('Quiz');
        $this->view('admin/quizzes', [
            'title' => 'Quiz (admin) - APPOLIOS',
            'quizzes' => $quizModel->getAllForAdmin(),
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
        $qb = $this->model('QuestionBank');
        $this->view('admin/quiz_form', [
            'title' => 'Nouveau quiz - APPOLIOS',
            'quiz' => null,
            'chapters' => $chapters,
            'questionBank' => $qb->getAllForAdmin(),
            'flash' => $this->getFlash(),
        ]);
    }

    public function storeQuiz() {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/quiz/add');
            return;
        }
        $chapterId = (int) ($_POST['chapter_id'] ?? 0);
        if (!$this->adminChapterExists($chapterId)) {
            $this->setFlash('error', 'Chapitre invalide.');
            $this->redirect('admin/quiz/add');
            return;
        }
        $quizModel = $this->model('Quiz');
        $qbModel = $this->model('QuestionBank');
        $bankIds = isset($_POST['bank_question_ids']) && is_array($_POST['bank_question_ids']) ? $_POST['bank_question_ids'] : [];
        $questions = $qbModel->appendIdsToQuizQuestions(
            Quiz::normalizeQuestionsFromPost($_POST),
            $bankIds,
            null
        );
        if (count($questions) < 1) {
            $this->setFlash('error', 'Ajoutez au moins une question (saisie manuelle ou depuis la banque).');
            $this->redirect('admin/quiz/add');
            return;
        }
        $title = $this->sanitize($_POST['title'] ?? '');
        if ($title === '') {
            $this->setFlash('error', 'Titre obligatoire.');
            $this->redirect('admin/quiz/add');
            return;
        }
        $quizModel->create([
            'chapter_id' => $chapterId,
            'title' => $title,
            'difficulty' => $this->sanitize($_POST['difficulty'] ?? 'beginner'),
            'tags' => $this->sanitize($_POST['tags'] ?? ''),
            'time_limit_sec' => $_POST['time_limit_sec'] ?? null,
            'questions' => $questions,
            'created_by' => (int) $_SESSION['user_id'],
        ]);
        $this->setFlash('success', 'Quiz enregistré.');
        $this->redirect('admin/quiz');
    }

    public function editQuiz($id) {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        $quizModel = $this->model('Quiz');
        $quiz = $quizModel->findWithChapterCourse((int) $id);
        if (!$quiz) {
            $this->setFlash('error', 'Quiz introuvable.');
            $this->redirect('admin/quiz');
            return;
        }
        $chapterModel = $this->model('Chapter');
        $chapters = $chapterModel->getAllWithCourseTitles();
        $qb = $this->model('QuestionBank');
        $this->view('admin/quiz_form', [
            'title' => 'Modifier le quiz - APPOLIOS',
            'quiz' => $quiz,
            'chapters' => $chapters,
            'questionBank' => $qb->getAllForAdmin(),
            'flash' => $this->getFlash(),
        ]);
    }

    public function updateQuiz($id) {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/quiz');
            return;
        }
        $quizModel = $this->model('Quiz');
        $existing = $quizModel->findWithChapterCourse((int) $id);
        if (!$existing) {
            $this->setFlash('error', 'Quiz introuvable.');
            $this->redirect('admin/quiz');
            return;
        }
        $chapterId = (int) ($_POST['chapter_id'] ?? 0);
        if (!$this->adminChapterExists($chapterId)) {
            $this->setFlash('error', 'Chapitre invalide.');
            $this->redirect('admin/quiz/edit/' . (int) $id);
            return;
        }
        $qbModel = $this->model('QuestionBank');
        $bankIds = isset($_POST['bank_question_ids']) && is_array($_POST['bank_question_ids']) ? $_POST['bank_question_ids'] : [];
        $questions = $qbModel->appendIdsToQuizQuestions(
            Quiz::normalizeQuestionsFromPost($_POST),
            $bankIds,
            null
        );
        if (count($questions) < 1) {
            $this->setFlash('error', 'Au moins une question requise (saisie ou banque).');
            $this->redirect('admin/quiz/edit/' . (int) $id);
            return;
        }
        $title = $this->sanitize($_POST['title'] ?? '');
        if ($title === '') {
            $this->setFlash('error', 'Titre obligatoire.');
            $this->redirect('admin/quiz/edit/' . (int) $id);
            return;
        }
        $quizModel->update((int) $id, [
            'chapter_id' => $chapterId,
            'title' => $title,
            'difficulty' => $this->sanitize($_POST['difficulty'] ?? 'beginner'),
            'tags' => $this->sanitize($_POST['tags'] ?? ''),
            'time_limit_sec' => $_POST['time_limit_sec'] ?? null,
            'questions' => $questions,
        ]);
        $this->setFlash('success', 'Quiz mis à jour.');
        $this->redirect('admin/quiz');
    }

    public function deleteQuiz($id) {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        $quizModel = $this->model('Quiz');
        $existing = $quizModel->findWithChapterCourse((int) $id);
        if (!$existing) {
            $this->setFlash('error', 'Quiz introuvable.');
            $this->redirect('admin/quiz');
            return;
        }
        $quizModel->delete((int) $id);
        $this->setFlash('success', 'Quiz supprimé.');
        $this->redirect('admin/quiz');
    }

    public function questionsBank() {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        $qb = $this->model('QuestionBank');
        $this->view('admin/questions_bank', [
            'title' => 'Banque de questions (admin) - APPOLIOS',
            'questions' => $qb->getAllForAdmin(),
            'flash' => $this->getFlash(),
        ]);
    }

    public function addQuestion() {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        $this->view('admin/question_form', [
            'title' => 'Nouvelle question - APPOLIOS',
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
            $this->redirect('admin/questions/add');
            return;
        }
        $opts = $this->adminNormalizeOptions($_POST['options'] ?? []);
        if (count($opts) < 2) {
            $this->setFlash('error', 'Au moins 2 options.');
            $this->redirect('admin/questions/add');
            return;
        }
        $text = $this->sanitize($_POST['question_text'] ?? '');
        if ($text === '') {
            $this->setFlash('error', 'Texte obligatoire.');
            $this->redirect('admin/questions/add');
            return;
        }
        $ca = (int) ($_POST['correct_answer'] ?? 0);
        if ($ca < 0 || $ca >= count($opts)) {
            $ca = 0;
        }
        $qb = $this->model('QuestionBank');
        $qb->create([
            'title' => $this->sanitize($_POST['title'] ?? ''),
            'question_text' => $text,
            'options' => $opts,
            'correct_answer' => $ca,
            'tags' => $this->sanitize($_POST['tags'] ?? ''),
            'difficulty' => $this->sanitize($_POST['difficulty'] ?? 'beginner'),
            'created_by' => (int) $_SESSION['user_id'],
        ]);
        $this->setFlash('success', 'Question enregistrée.');
        $this->redirect('admin/questions');
    }

    public function editQuestion($id) {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        $qb = $this->model('QuestionBank');
        $q = $qb->findByIdDecoded((int) $id);
        if (!$q) {
            $this->setFlash('error', 'Question introuvable.');
            $this->redirect('admin/questions');
            return;
        }
        $this->view('admin/question_form', [
            'title' => 'Modifier la question - APPOLIOS',
            'question' => $q,
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
        $qb = $this->model('QuestionBank');
        if (!$qb->findById((int) $id)) {
            $this->setFlash('error', 'Question introuvable.');
            $this->redirect('admin/questions');
            return;
        }
        $opts = $this->adminNormalizeOptions($_POST['options'] ?? []);
        if (count($opts) < 2) {
            $this->setFlash('error', 'Au moins 2 options.');
            $this->redirect('admin/questions/edit/' . (int) $id);
            return;
        }
        $text = $this->sanitize($_POST['question_text'] ?? '');
        if ($text === '') {
            $this->setFlash('error', 'Texte obligatoire.');
            $this->redirect('admin/questions/edit/' . (int) $id);
            return;
        }
        $ca = (int) ($_POST['correct_answer'] ?? 0);
        if ($ca < 0 || $ca >= count($opts)) {
            $ca = 0;
        }
        $qb->update((int) $id, [
            'title' => $this->sanitize($_POST['title'] ?? ''),
            'question_text' => $text,
            'options' => $opts,
            'correct_answer' => $ca,
            'tags' => $this->sanitize($_POST['tags'] ?? ''),
            'difficulty' => $this->sanitize($_POST['difficulty'] ?? 'beginner'),
        ]);
        $this->setFlash('success', 'Question mise à jour.');
        $this->redirect('admin/questions');
    }

    public function deleteQuestion($id) {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        $qb = $this->model('QuestionBank');
        if (!$qb->findById((int) $id)) {
            $this->setFlash('error', 'Question introuvable.');
            $this->redirect('admin/questions');
            return;
        }
        $qb->delete((int) $id);
        $this->setFlash('success', 'Question supprimée.');
        $this->redirect('admin/questions');
    }

    private function adminChapterExists($chapterId) {
        if ($chapterId <= 0) {
            return false;
        }
        $m = $this->model('Chapter');
        return (bool) $m->findById($chapterId);
    }

    /**
     * @param mixed $raw
     * @return list<string>
     */
    private function adminNormalizeOptions($raw) {
        if (!is_array($raw)) {
            return [];
        }
        return array_values(array_filter(array_map(function ($o) {
            return trim($this->sanitize((string) $o));
        }, $raw)));
    }
}