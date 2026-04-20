<?php
/**
 * APPOLIOS Teacher Controller
 * Handles teacher-specific functionality
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Course.php';
require_once __DIR__ . '/../models/Evenement.php';
require_once __DIR__ . '/../models/EvenementRessource.php';

class TeacherController extends Controller {

    /**
     * Check if user is teacher
     */
    protected function isTeacher() {
        return $this->isLoggedIn() && $_SESSION['role'] === 'teacher';
    }

    /**
     * Middleware to check teacher access
     */
    protected function requireTeacher() {
        if (!$this->isTeacher()) {
            $this->setFlash('error', 'Access denied. Teachers only.');
            $this->redirect('login');
        }
    }

    /**
     * Teacher Dashboard
     */
    public function dashboard() {
        $this->requireTeacher();

        $userModel = $this->model('User');
        $courseModel = $this->model('Course');
        $evenementModel = $this->model('Evenement');

        // Get stats
        $myCourses = $courseModel->getCoursesByTeacher($_SESSION['user_id']);
        $stats = [
            'total_courses' => count($myCourses),
            'total_students' => $courseModel->countStudentsByTeacher($_SESSION['user_id']),
            'active_enrollments' => $courseModel->countActiveEnrollmentsByTeacher($_SESSION['user_id']),
            'total_evenements' => count($evenementModel->getByCreator($_SESSION['user_id']))
        ];

        $data = [
            'title' => 'Teacher Dashboard - APPOLIOS',
            'userName' => $_SESSION['user_name'],
            'courses' => $myCourses,
            'stats' => $stats
        ];

        $this->view('teacher/dashboard', $data);
    }

    /**
     * List all my courses
     */
    public function myCourses() {
        $this->requireTeacher();

        $courseModel = $this->model('Course');
        $courses = $courseModel->getCoursesByTeacher($_SESSION['user_id']);

        $data = [
            'title' => 'My Courses - APPOLIOS',
            'courses' => $courses
        ];

        $this->view('teacher/courses', $data);
    }

    /**
     * Show add course form
     */
    public function addCourse() {
        $this->requireTeacher();

        $data = [
            'title' => 'Add Course - APPOLIOS',
            'flash' => $this->getFlash()
        ];

        $this->view('teacher/add_course', $data);
    }

    /**
     * Store new course
     */
    public function storeCourse() {
        $this->requireTeacher();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('teacher/add-course');
            return;
        }

        $title = $this->sanitize($_POST['title'] ?? '');
        $description = $this->sanitize($_POST['description'] ?? '');
        $video_url = $this->sanitize($_POST['video_url'] ?? '');

        // Validation
        if (empty($title) || empty($description)) {
            $this->setFlash('error', 'Title and description are required');
            $this->redirect('teacher/add-course');
            return;
        }

        $courseModel = $this->model('Course');
        
        $result = $courseModel->create([
            'title' => $title,
            'description' => $description,
            'video_url' => $video_url,
            'created_by' => $_SESSION['user_id']
        ]);

        if ($result) {
            $this->setFlash('success', 'Course created successfully!');
            $this->redirect('teacher/courses');
        } else {
            $this->setFlash('error', 'Failed to create course');
            $this->redirect('teacher/add-course');
        }
    }

    /**
     * Show edit course form
     */
    public function editCourse($id) {
        $this->requireTeacher();

        $courseModel = $this->model('Course');
        $course = $courseModel->findById($id);

        // Check if course belongs to this teacher
        if (!$course || $course['created_by'] != $_SESSION['user_id']) {
            $this->setFlash('error', 'Course not found or access denied');
            $this->redirect('teacher/courses');
            return;
        }

        $data = [
            'title' => 'Edit Course - APPOLIOS',
            'course' => $course,
            'flash' => $this->getFlash()
        ];

        $this->view('teacher/edit_course', $data);
    }

    /**
     * Update course
     */
    public function updateCourse($id) {
        $this->requireTeacher();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('teacher/edit-course/' . $id);
            return;
        }

        $courseModel = $this->model('Course');
        $course = $courseModel->findById($id);

        // Check if course belongs to this teacher
        if (!$course || $course['created_by'] != $_SESSION['user_id']) {
            $this->setFlash('error', 'Course not found or access denied');
            $this->redirect('teacher/courses');
            return;
        }

        $title = $this->sanitize($_POST['title'] ?? '');
        $description = $this->sanitize($_POST['description'] ?? '');
        $video_url = $this->sanitize($_POST['video_url'] ?? '');

        if (empty($title) || empty($description)) {
            $this->setFlash('error', 'Title and description are required');
            $this->redirect('teacher/edit-course/' . $id);
            return;
        }

        $result = $courseModel->update($id, [
            'title' => $title,
            'description' => $description,
            'video_url' => $video_url
        ]);

        if ($result) {
            $this->setFlash('success', 'Course updated successfully!');
            $this->redirect('teacher/courses');
        } else {
            $this->setFlash('error', 'Failed to update course');
            $this->redirect('teacher/edit-course/' . $id);
        }
    }

    /**
     * Delete course
     */
    public function deleteCourse($id) {
        $this->requireTeacher();

        $courseModel = $this->model('Course');
        $course = $courseModel->findById($id);

        // Check if course belongs to this teacher
        if (!$course || $course['created_by'] != $_SESSION['user_id']) {
            $this->setFlash('error', 'Course not found or access denied');
            $this->redirect('teacher/courses');
            return;
        }

        $result = $courseModel->delete($id);

        if ($result) {
            $this->setFlash('success', 'Course deleted successfully!');
        } else {
            $this->setFlash('error', 'Failed to delete course');
        }

        $this->redirect('teacher/courses');
    }

    /**
     * View course with enrolled students
     */
    public function viewCourse($id) {
        $this->requireTeacher();

        $courseModel = $this->model('Course');
        $course = $courseModel->findById($id);

        // Check if course belongs to this teacher
        if (!$course || $course['created_by'] != $_SESSION['user_id']) {
            $this->setFlash('error', 'Course not found or access denied');
            $this->redirect('teacher/courses');
            return;
        }

        // Get enrolled students
        $students = $courseModel->getEnrolledStudents($id);

        $data = [
            'title' => htmlspecialchars($course['title']) . ' - APPOLIOS',
            'course' => $course,
            'students' => $students
        ];

        $this->view('teacher/course_detail', $data);
    }

    /**
     * Teacher profile
     */
    public function profile() {
        $this->requireTeacher();

        $userModel = $this->model('User');
        $user = $userModel->findById($_SESSION['user_id']);

        $data = [
            'title' => 'My Profile - APPOLIOS',
            'user' => $user
        ];

        $this->view('teacher/profile', $data);
    }

    /**
     * List all teacher evenements.
     */
    public function evenements() {
        $this->requireTeacher();

        $evenementModel = $this->model('Evenement');
        $data = [
            'title' => 'My Evenements - APPOLIOS',
            'evenements' => $evenementModel->getByCreator($_SESSION['user_id']),
            'flash' => $this->getFlash()
        ];

        $this->view('teacher/evenements', $data);
    }

    /**
     * Show add evenement form for teacher.
     */
    public function addEvenement() {
        $this->requireTeacher();

        $data = [
            'title' => 'Propose Evenement - APPOLIOS',
            'flash' => $this->getFlash()
        ];

        $this->view('teacher/add_evenement', $data);
    }

    /**
     * Store teacher evenement request (always pending approval).
     */
    public function storeEvenement() {
        $this->requireTeacher();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('teacher/evenements');
            return;
        }

        $payload = $this->extractEvenementPayload();
        $errors = $this->validateEvenementPayload($payload);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            $this->redirect('teacher/add-evenement');
            return;
        }

        $eventDate = $payload['date_debut'] . ' ' . (!empty($payload['heure_debut']) ? $payload['heure_debut'] : '00:00') . ':00';
        $evenementModel = $this->model('Evenement');

        $result = $evenementModel->create([
            'title' => $payload['title'],
            'titre' => $payload['title'],
            'description' => $payload['description'],
            'date_debut' => $payload['date_debut'],
            'date_fin' => !empty($payload['date_fin']) ? $payload['date_fin'] : null,
            'heure_debut' => !empty($payload['heure_debut']) ? $payload['heure_debut'] : null,
            'heure_fin' => !empty($payload['heure_fin']) ? $payload['heure_fin'] : null,
            'lieu' => $payload['lieu'],
            'capacite_max' => $payload['capacite_max'] > 0 ? $payload['capacite_max'] : null,
            'type' => $payload['type'],
            'statut' => $payload['statut'],
            'approval_status' => 'pending',
            'location' => $payload['lieu'],
            'event_date' => $eventDate,
            'created_by' => $_SESSION['user_id']
        ]);

        if ($result) {
            $this->setFlash('success', 'Evenement submitted to admin for approval.');
            $this->redirect('teacher/evenements');
            return;
        }

        $this->setFlash('error', 'Failed to create evenement request.');
        $this->redirect('teacher/add-evenement');
    }

    /**
     * Show edit teacher evenement form.
     */
    public function editEvenement($id) {
        $this->requireTeacher();

        $evenementModel = $this->model('Evenement');
        $evenement = $evenementModel->findByIdAndCreator((int) $id, (int) $_SESSION['user_id']);

        if (!$evenement) {
            $this->setFlash('error', 'Evenement not found or access denied.');
            $this->redirect('teacher/evenements');
            return;
        }

        $data = [
            'title' => 'Edit Evenement - APPOLIOS',
            'evenement' => $evenement,
            'flash' => $this->getFlash()
        ];

        $this->view('teacher/edit_evenement', $data);
    }

    /**
     * Update teacher evenement.
     * If previously approved, event returns to pending.
     */
    public function updateEvenement($id) {
        $this->requireTeacher();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('teacher/evenements');
            return;
        }

        $evenementModel = $this->model('Evenement');
        $existing = $evenementModel->findByIdAndCreator((int) $id, (int) $_SESSION['user_id']);
        if (!$existing) {
            $this->setFlash('error', 'Evenement not found or access denied.');
            $this->redirect('teacher/evenements');
            return;
        }

        $payload = $this->extractEvenementPayload();
        $errors = $this->validateEvenementPayload($payload);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            $this->redirect('teacher/edit-evenement/' . (int) $id);
            return;
        }

        $eventDate = $payload['date_debut'] . ' ' . (!empty($payload['heure_debut']) ? $payload['heure_debut'] : '00:00') . ':00';
        $result = $evenementModel->update((int) $id, [
            'title' => $payload['title'],
            'titre' => $payload['title'],
            'description' => $payload['description'],
            'date_debut' => $payload['date_debut'],
            'date_fin' => !empty($payload['date_fin']) ? $payload['date_fin'] : null,
            'heure_debut' => !empty($payload['heure_debut']) ? $payload['heure_debut'] : null,
            'heure_fin' => !empty($payload['heure_fin']) ? $payload['heure_fin'] : null,
            'lieu' => $payload['lieu'],
            'capacite_max' => $payload['capacite_max'] > 0 ? $payload['capacite_max'] : null,
            'type' => $payload['type'],
            'statut' => $payload['statut'],
            'location' => $payload['lieu'],
            'event_date' => $eventDate
        ]);

        if (!$result) {
            $this->setFlash('error', 'Failed to update evenement.');
            $this->redirect('teacher/edit-evenement/' . (int) $id);
            return;
        }

        $wasApproved = ($existing['approval_status'] ?? 'approved') === 'approved';
        if ($wasApproved) {
            $evenementModel->markPendingIfApproved((int) $id);
            $this->setFlash('success', 'Evenement updated and sent back to pending approval.');
        } else {
            $this->setFlash('success', 'Evenement updated successfully.');
        }

        $this->redirect('teacher/evenements');
    }

    /**
     * Delete teacher evenement only while pending.
     */
    public function deleteEvenement($id) {
        $this->requireTeacher();

        $evenementModel = $this->model('Evenement');
        $evenement = $evenementModel->findByIdAndCreator((int) $id, (int) $_SESSION['user_id']);

        if (!$evenement) {
            $this->setFlash('error', 'Evenement not found or access denied.');
            $this->redirect('teacher/evenements');
            return;
        }

        if (($evenement['approval_status'] ?? 'approved') !== 'pending') {
            $this->setFlash('error', 'You can only delete pending evenements. Confirmed events cannot be deleted.');
            $this->redirect('teacher/evenements');
            return;
        }

        $result = $evenementModel->delete((int) $id);
        if ($result) {
            $this->setFlash('success', 'Pending evenement deleted successfully.');
        } else {
            $this->setFlash('error', 'Failed to delete evenement.');
        }

        $this->redirect('teacher/evenements');
    }

    /**
     * Teacher evenement resources page (own events only).
     */
    public function evenementRessources() {
        $this->requireTeacher();

        $eventId = (int) ($_GET['evenement_id'] ?? 0);
        if ($eventId <= 0) {
            $this->setFlash('error', 'Please choose an evenement first.');
            $this->redirect('teacher/evenements');
            return;
        }

        $evenementModel = $this->model('Evenement');
        $ressourceModel = $this->model('EvenementRessource');

        $event = $evenementModel->findByIdAndCreator($eventId, (int) $_SESSION['user_id']);
        if (!$event) {
            $this->setFlash('error', 'Evenement not found or access denied.');
            $this->redirect('teacher/evenements');
            return;
        }

        $editId = (int) ($_GET['edit_id'] ?? 0);
        $editResource = null;
        if ($editId > 0) {
            $candidate = $ressourceModel->findById($editId);
            if ($candidate && (int) $candidate['evenement_id'] === $eventId) {
                $editResource = $candidate;
            }
        }

        $data = [
            'title' => 'Evenement Resources - APPOLIOS',
            'selectedEvenementId' => $eventId,
            'selectedEvenement' => $event,
            'editResource' => $editResource,
            'rules' => $ressourceModel->getByTypeAndEvenement('rule', $eventId),
            'materials' => $ressourceModel->getByTypeAndEvenement('materiel', $eventId),
            'plans' => $ressourceModel->getByTypeAndEvenement('plan', $eventId),
            'flash' => $this->getFlash()
        ];

        $this->view('teacher/evenement_ressources', $data);
    }

    /**
     * Store teacher resource and move approved event back to pending.
     */
    public function storeEvenementRessource() {
        $this->requireTeacher();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('teacher/evenements');
            return;
        }

        $type = $this->sanitize($_POST['type'] ?? '');
        $title = $this->sanitize($_POST['title'] ?? '');
        $details = $this->sanitize($_POST['details'] ?? '');
        $eventId = (int) ($_POST['evenement_id'] ?? 0);
        $isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
            || (isset($_POST['batch_mode']) && $_POST['batch_mode'] === '1');

        $errors = [];
        if (!in_array($type, ['rule', 'materiel', 'plan'], true)) {
            $errors[] = 'Invalid resource type.';
        }
        if (empty($title)) {
            $errors[] = 'Title is required.';
        }

        $evenementModel = $this->model('Evenement');
        $event = $evenementModel->findByIdAndCreator($eventId, (int) $_SESSION['user_id']);
        if (!$event) {
            $errors[] = 'Evenement not found or access denied.';
        }

        if (!empty($errors)) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
                exit();
            }

            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            $this->redirect('teacher/evenement-ressources&evenement_id=' . $eventId);
            return;
        }

        $ressourceModel = $this->model('EvenementRessource');
        $createdId = $ressourceModel->create([
            'evenement_id' => $eventId,
            'type' => $type,
            'title' => $title,
            'details' => $details,
            'created_by' => $_SESSION['user_id']
        ]);

        $verified = false;
        if ($createdId) {
            $verified = $ressourceModel->existsInListScope($createdId, $eventId, $type);
        }

        if ($createdId && $verified) {
            $evenementModel->markPendingIfApproved($eventId);
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'verified_in_right_list' => true,
                    'message' => 'Resource saved successfully.'
                ]);
                exit();
            }

            $this->setFlash('success', 'Resource saved successfully. Event approval set to pending if it was previously approved.');
        } else {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'verified_in_right_list' => false,
                    'message' => 'Save verification failed. Check the right list and try again.'
                ]);
                exit();
            }
            $this->setFlash('error', 'Save verification failed. Check the right list and try again.');
        }

        $this->redirect('teacher/evenement-ressources&evenement_id=' . $eventId);
    }

    /**
     * Update teacher resource item.
     */
    public function updateEvenementRessource($id) {
        $this->requireTeacher();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('teacher/evenements');
            return;
        }

        $eventId = (int) ($_POST['evenement_id'] ?? 0);
        $title = $this->sanitize($_POST['title'] ?? '');
        $details = $this->sanitize($_POST['details'] ?? '');

        if ($eventId <= 0 || empty($title)) {
            $this->setFlash('error', 'Please provide valid data before saving.');
            $this->redirect('teacher/evenement-ressources&evenement_id=' . $eventId . '&edit_id=' . (int) $id);
            return;
        }

        $evenementModel = $this->model('Evenement');
        $event = $evenementModel->findByIdAndCreator($eventId, (int) $_SESSION['user_id']);
        if (!$event) {
            $this->setFlash('error', 'Evenement not found or access denied.');
            $this->redirect('teacher/evenements');
            return;
        }

        $ressourceModel = $this->model('EvenementRessource');
        $resource = $ressourceModel->findById((int) $id);
        if (!$resource || (int) $resource['evenement_id'] !== $eventId) {
            $this->setFlash('error', 'Resource not found for this evenement.');
            $this->redirect('teacher/evenement-ressources&evenement_id=' . $eventId);
            return;
        }

        $result = $ressourceModel->update((int) $id, [
            'title' => $title,
            'details' => $details,
            'evenement_id' => $eventId
        ]);

        if ($result) {
            $evenementModel->markPendingIfApproved($eventId);
            $this->setFlash('success', 'Resource updated successfully.');
        } else {
            $this->setFlash('error', 'Failed to update resource.');
        }

        $this->redirect('teacher/evenement-ressources&evenement_id=' . $eventId);
    }

    /**
     * Delete teacher resource item.
     */
    public function deleteEvenementRessource($id) {
        $this->requireTeacher();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('teacher/evenements');
            return;
        }

        $eventId = (int) ($_POST['evenement_id'] ?? 0);
        if ($eventId <= 0) {
            $this->setFlash('error', 'Invalid evenement context.');
            $this->redirect('teacher/evenements');
            return;
        }

        $evenementModel = $this->model('Evenement');
        $event = $evenementModel->findByIdAndCreator($eventId, (int) $_SESSION['user_id']);
        if (!$event) {
            $this->setFlash('error', 'Evenement not found or access denied.');
            $this->redirect('teacher/evenements');
            return;
        }

        $ressourceModel = $this->model('EvenementRessource');
        $result = $ressourceModel->deleteByEvenement((int) $id, $eventId);

        if ($result) {
            $evenementModel->markPendingIfApproved($eventId);
            $this->setFlash('success', 'Resource deleted successfully.');
        } else {
            $this->setFlash('error', 'Failed to delete resource.');
        }

        $this->redirect('teacher/evenement-ressources&evenement_id=' . $eventId);
    }

    /* ---------- Chapitres, quiz, banque de questions (rôle enseignant : création / édition) ---------- */

    public function chapitresAddGlobal() {
        $this->requireTeacher();
        $courseModel = $this->model('Course');
        $this->view('teacher/chapter_form', [
            'title' => 'Nouveau chapitre - APPOLIOS',
            'course' => null,
            'chapter' => null,
            'allCourses' => $courseModel->getCoursesByTeacher((int) $_SESSION['user_id']),
            'flash' => $this->getFlash(),
        ]);
    }

    public function chapitresStoreGlobal() {
        $this->requireTeacher();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('teacher/chapitres/add');
            return;
        }
        $courseId = (int) ($_POST['course_id'] ?? 0);
        if (!$this->teacherCourseOrFail($courseId)) {
            return;
        }
        $title = $this->sanitize($_POST['title'] ?? '');
        $content = $this->sanitize($_POST['content'] ?? '');
        $sort = (int) ($_POST['sort_order'] ?? 0);
        if ($title === '') {
            $this->setFlash('error', 'Le titre est obligatoire.');
            $this->redirect('teacher/chapitres/add');
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
        $this->redirect('teacher/chapitres');
    }

    public function chapitres() {
        $this->requireTeacher();
        $chapterModel = $this->model('Chapter');
        $this->view('teacher/chapitres', [
            'title' => 'Chapitres - APPOLIOS',
            'chapters' => $chapterModel->getAllForTeacher((int) $_SESSION['user_id']),
            'flash' => $this->getFlash(),
        ]);
    }

    public function courseChapitres($courseId) {
        $this->requireTeacher();
        $course = $this->teacherCourseOrFail((int) $courseId);
        if (!$course) {
            return;
        }
        $chapterModel = $this->model('Chapter');
        $this->view('teacher/course_chapitres', [
            'title' => 'Chapitres du cours - APPOLIOS',
            'course' => $course,
            'chapters' => $chapterModel->getByCourseId((int) $courseId),
            'flash' => $this->getFlash(),
        ]);
    }

    public function addChapter($courseId) {
        $this->requireTeacher();
        $course = $this->teacherCourseOrFail((int) $courseId);
        if (!$course) {
            return;
        }
        $this->view('teacher/chapter_form', [
            'title' => 'Nouveau chapitre - APPOLIOS',
            'course' => $course,
            'chapter' => null,
            'flash' => $this->getFlash(),
        ]);
    }

    public function storeChapter($courseId) {
        $this->requireTeacher();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('teacher/course/' . (int) $courseId . '/chapitres/add');
            return;
        }
        $course = $this->teacherCourseOrFail((int) $courseId);
        if (!$course) {
            return;
        }
        $title = $this->sanitize($_POST['title'] ?? '');
        $content = $this->sanitize($_POST['content'] ?? '');
        $sort = (int) ($_POST['sort_order'] ?? 0);
        if ($title === '') {
            $this->setFlash('error', 'Le titre est obligatoire.');
            $this->redirect('teacher/course/' . (int) $courseId . '/chapitres/add');
            return;
        }
        $chapterModel = $this->model('Chapter');
        $id = $chapterModel->create([
            'course_id' => (int) $courseId,
            'title' => $title,
            'content' => $content,
            'sort_order' => $sort,
        ]);
        if ($id) {
            $this->setFlash('success', 'Chapitre créé.');
        } else {
            $this->setFlash('error', 'Échec de la création.');
        }
        $this->redirect('teacher/chapitres');
    }

    public function editChapter($id) {
        $this->requireTeacher();
        $chapterModel = $this->model('Chapter');
        $row = $chapterModel->findWithCourse((int) $id);
        if (!$row || (int) $row['course_owner_id'] !== (int) $_SESSION['user_id']) {
            $this->setFlash('error', 'Chapitre introuvable.');
            $this->redirect('teacher/chapitres');
            return;
        }
        $courseModel = $this->model('Course');
        $course = $courseModel->findById((int) $row['course_id']);
        $this->view('teacher/chapter_form', [
            'title' => 'Modifier le chapitre - APPOLIOS',
            'course' => $course,
            'chapter' => $row,
            'flash' => $this->getFlash(),
        ]);
    }

    public function updateChapter($id) {
        $this->requireTeacher();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('teacher/chapitres');
            return;
        }
        $chapterModel = $this->model('Chapter');
        $row = $chapterModel->findWithCourse((int) $id);
        if (!$row || (int) $row['course_owner_id'] !== (int) $_SESSION['user_id']) {
            $this->setFlash('error', 'Chapitre introuvable.');
            $this->redirect('teacher/chapitres');
            return;
        }
        $title = $this->sanitize($_POST['title'] ?? '');
        $content = $this->sanitize($_POST['content'] ?? '');
        $sort = (int) ($_POST['sort_order'] ?? 0);
        if ($title === '') {
            $this->setFlash('error', 'Le titre est obligatoire.');
            $this->redirect('teacher/chapitre/' . (int) $id . '/edit');
            return;
        }
        $chapterModel->update((int) $id, ['title' => $title, 'content' => $content, 'sort_order' => $sort]);
        $this->setFlash('success', 'Chapitre mis à jour.');
        $this->redirect('teacher/chapitres');
    }

    public function deleteChapter($id) {
        $this->requireTeacher();
        $chapterModel = $this->model('Chapter');
        $row = $chapterModel->findWithCourse((int) $id);
        if (!$row || (int) $row['course_owner_id'] !== (int) $_SESSION['user_id']) {
            $this->setFlash('error', 'Chapitre introuvable.');
            $this->redirect('teacher/chapitres');
            return;
        }
        $chapterModel->delete((int) $id);
        $this->setFlash('success', 'Chapitre supprimé.');
        $this->redirect('teacher/chapitres');
    }

    public function quizzes() {
        $this->requireTeacher();
        $quizModel = $this->model('Quiz');
        $this->view('teacher/quizzes', [
            'title' => 'Quiz - APPOLIOS',
            'quizzes' => $quizModel->getAllForTeacher((int) $_SESSION['user_id']),
            'flash' => $this->getFlash(),
        ]);
    }

    public function addQuiz() {
        $this->requireTeacher();
        $uid = (int) $_SESSION['user_id'];
        $chapterModel = $this->model('Chapter');
        $chapters = $chapterModel->getAllForTeacher($uid);
        $qbModel = $this->model('QuestionBank');
        $this->view('teacher/quiz_form', [
            'title' => 'Nouveau quiz - APPOLIOS',
            'quiz' => null,
            'chapters' => $chapters,
            'questionBank' => $qbModel->getForTeacher($uid),
            'flash' => $this->getFlash(),
        ]);
    }

    public function storeQuiz() {
        $this->requireTeacher();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('teacher/quiz/add');
            return;
        }
        $chapterId = (int) ($_POST['chapter_id'] ?? 0);
        if (!$this->teacherOwnsChapter($chapterId)) {
            $this->setFlash('error', 'Chapitre invalide.');
            $this->redirect('teacher/quiz/add');
            return;
        }
        $quizModel = $this->model('Quiz');
        $qbModel = $this->model('QuestionBank');
        $bankIds = isset($_POST['bank_question_ids']) && is_array($_POST['bank_question_ids']) ? $_POST['bank_question_ids'] : [];
        $questions = $qbModel->appendIdsToQuizQuestions(
            Quiz::normalizeQuestionsFromPost($_POST),
            $bankIds,
            (int) $_SESSION['user_id']
        );
        if (count($questions) < 1) {
            $this->setFlash('error', 'Ajoutez au moins une question (saisie manuelle ou depuis la banque).');
            $this->redirect('teacher/quiz/add');
            return;
        }
        $title = $this->sanitize($_POST['title'] ?? '');
        if ($title === '') {
            $this->setFlash('error', 'Titre du quiz obligatoire.');
            $this->redirect('teacher/quiz/add');
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
        $this->redirect('teacher/quiz');
    }

    public function editQuiz($id) {
        $this->requireTeacher();
        $quizModel = $this->model('Quiz');
        $quiz = $quizModel->findWithChapterCourse((int) $id);
        if (!$quiz || (int) $quiz['course_owner_id'] !== (int) $_SESSION['user_id']) {
            $this->setFlash('error', 'Quiz introuvable.');
            $this->redirect('teacher/quiz');
            return;
        }
        $uid = (int) $_SESSION['user_id'];
        $chapterModel = $this->model('Chapter');
        $chapters = $chapterModel->getAllForTeacher($uid);
        $qbModel = $this->model('QuestionBank');
        $this->view('teacher/quiz_form', [
            'title' => 'Modifier le quiz - APPOLIOS',
            'quiz' => $quiz,
            'chapters' => $chapters,
            'questionBank' => $qbModel->getForTeacher($uid),
            'flash' => $this->getFlash(),
        ]);
    }

    public function updateQuiz($id) {
        $this->requireTeacher();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('teacher/quiz');
            return;
        }
        $quizModel = $this->model('Quiz');
        $existing = $quizModel->findWithChapterCourse((int) $id);
        if (!$existing || (int) $existing['course_owner_id'] !== (int) $_SESSION['user_id']) {
            $this->setFlash('error', 'Quiz introuvable.');
            $this->redirect('teacher/quiz');
            return;
        }
        $chapterId = (int) ($_POST['chapter_id'] ?? 0);
        if (!$this->teacherOwnsChapter($chapterId)) {
            $this->setFlash('error', 'Chapitre invalide.');
            $this->redirect('teacher/quiz/edit/' . (int) $id);
            return;
        }
        $qbModel = $this->model('QuestionBank');
        $bankIds = isset($_POST['bank_question_ids']) && is_array($_POST['bank_question_ids']) ? $_POST['bank_question_ids'] : [];
        $questions = $qbModel->appendIdsToQuizQuestions(
            Quiz::normalizeQuestionsFromPost($_POST),
            $bankIds,
            (int) $_SESSION['user_id']
        );
        if (count($questions) < 1) {
            $this->setFlash('error', 'Au moins une question requise (saisie ou banque).');
            $this->redirect('teacher/quiz/edit/' . (int) $id);
            return;
        }
        $title = $this->sanitize($_POST['title'] ?? '');
        if ($title === '') {
            $this->setFlash('error', 'Titre obligatoire.');
            $this->redirect('teacher/quiz/edit/' . (int) $id);
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
        $this->redirect('teacher/quiz');
    }

    public function deleteQuiz($id) {
        $this->requireTeacher();
        $quizModel = $this->model('Quiz');
        $existing = $quizModel->findWithChapterCourse((int) $id);
        if (!$existing || (int) $existing['course_owner_id'] !== (int) $_SESSION['user_id']) {
            $this->setFlash('error', 'Quiz introuvable.');
            $this->redirect('teacher/quiz');
            return;
        }
        $quizModel->delete((int) $id);
        $this->setFlash('success', 'Quiz supprimé.');
        $this->redirect('teacher/quiz');
    }

    public function questionsBank() {
        $this->requireTeacher();
        $qb = $this->model('QuestionBank');
        $this->view('teacher/questions_bank', [
            'title' => 'Banque de questions - APPOLIOS',
            'questions' => $qb->getForTeacher((int) $_SESSION['user_id']),
            'flash' => $this->getFlash(),
        ]);
    }

    public function addQuestion() {
        $this->requireTeacher();
        $this->view('teacher/question_form', [
            'title' => 'Nouvelle question - APPOLIOS',
            'question' => null,
            'flash' => $this->getFlash(),
        ]);
    }

    public function storeQuestion() {
        $this->requireTeacher();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('teacher/questions/add');
            return;
        }
        $opts = $this->normalizeOptionsArray($_POST['options'] ?? []);
        if (count($opts) < 2) {
            $this->setFlash('error', 'Au moins 2 options.');
            $this->redirect('teacher/questions/add');
            return;
        }
        $text = $this->sanitize($_POST['question_text'] ?? '');
        if ($text === '') {
            $this->setFlash('error', 'Texte de la question obligatoire.');
            $this->redirect('teacher/questions/add');
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
        $this->redirect('teacher/questions');
    }

    public function editQuestion($id) {
        $this->requireTeacher();
        $qb = $this->model('QuestionBank');
        $q = $qb->findOwned((int) $id, (int) $_SESSION['user_id']);
        if (!$q) {
            $this->setFlash('error', 'Question introuvable.');
            $this->redirect('teacher/questions');
            return;
        }
        $this->view('teacher/question_form', [
            'title' => 'Modifier la question - APPOLIOS',
            'question' => $q,
            'flash' => $this->getFlash(),
        ]);
    }

    public function updateQuestion($id) {
        $this->requireTeacher();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('teacher/questions');
            return;
        }
        $qb = $this->model('QuestionBank');
        $existing = $qb->findOwned((int) $id, (int) $_SESSION['user_id']);
        if (!$existing) {
            $this->setFlash('error', 'Question introuvable.');
            $this->redirect('teacher/questions');
            return;
        }
        $opts = $this->normalizeOptionsArray($_POST['options'] ?? []);
        if (count($opts) < 2) {
            $this->setFlash('error', 'Au moins 2 options.');
            $this->redirect('teacher/questions/edit/' . (int) $id);
            return;
        }
        $text = $this->sanitize($_POST['question_text'] ?? '');
        if ($text === '') {
            $this->setFlash('error', 'Texte obligatoire.');
            $this->redirect('teacher/questions/edit/' . (int) $id);
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
        $this->redirect('teacher/questions');
    }

    public function deleteQuestion($id) {
        $this->requireTeacher();
        $qb = $this->model('QuestionBank');
        $existing = $qb->findOwned((int) $id, (int) $_SESSION['user_id']);
        if (!$existing) {
            $this->setFlash('error', 'Question introuvable.');
            $this->redirect('teacher/questions');
            return;
        }
        $qb->delete((int) $id);
        $this->setFlash('success', 'Question supprimée.');
        $this->redirect('teacher/questions');
    }

    /**
     * @return array|null
     */
    private function teacherCourseOrFail($courseId) {
        $courseModel = $this->model('Course');
        $course = $courseModel->findById($courseId);
        if (!$course || (int) $course['created_by'] !== (int) $_SESSION['user_id']) {
            $this->setFlash('error', 'Cours introuvable ou accès refusé.');
            $this->redirect('teacher/courses');
            return null;
        }
        return $course;
    }

    private function teacherOwnsChapter($chapterId) {
        if ($chapterId <= 0) {
            return false;
        }
        $chapterModel = $this->model('Chapter');
        $row = $chapterModel->findWithCourse($chapterId);
        return $row && (int) $row['course_owner_id'] === (int) $_SESSION['user_id'];
    }

    /**
     * @param mixed $raw
     * @return list<string>
     */
    private function normalizeOptionsArray($raw) {
        if (!is_array($raw)) {
            return [];
        }
        return array_values(array_filter(array_map(function ($o) {
            return trim($this->sanitize((string) $o));
        }, $raw)));
    }

    /**
     * Extract evenement fields from request.
     * @return array
     */
    private function extractEvenementPayload() {
        return [
            'title' => $this->sanitize($_POST['title'] ?? ''),
            'description' => $this->sanitize($_POST['description'] ?? ''),
            'date_debut' => $this->sanitize($_POST['date_debut'] ?? ''),
            'date_fin' => $this->sanitize($_POST['date_fin'] ?? ''),
            'heure_debut' => $this->sanitize($_POST['heure_debut'] ?? ''),
            'heure_fin' => $this->sanitize($_POST['heure_fin'] ?? ''),
            'lieu' => $this->sanitize($_POST['lieu'] ?? ''),
            'capacite_max' => (int) ($_POST['capacite_max'] ?? 0),
            'type' => $this->sanitize($_POST['type'] ?? 'general'),
            'statut' => $this->sanitize($_POST['statut'] ?? 'planifie')
        ];
    }

    /**
     * Validate evenement payload.
     * @param array $payload
     * @return array
     */
    private function validateEvenementPayload($payload) {
        $errors = [];

        if (empty($payload['title'])) {
            $errors[] = 'Event title is required';
        }
        if (empty($payload['description'])) {
            $errors[] = 'Event description is required';
        }
        if (empty($payload['date_debut']) || strtotime($payload['date_debut']) === false) {
            $errors[] = 'Valid start date is required';
        }
        $minDate = date('Y-m-d', strtotime('+1 day'));
        if (!empty($payload['date_debut']) && strtotime($payload['date_debut']) !== false && $payload['date_debut'] < $minDate) {
            $errors[] = 'Start date must be at least tomorrow';
        }
        if (empty($payload['heure_debut'])) {
            $errors[] = 'Start time is required';
        }
        if (!empty($payload['date_fin']) && strtotime($payload['date_fin']) !== false && !empty($payload['date_debut']) && strtotime($payload['date_fin']) < strtotime($payload['date_debut'])) {
            $errors[] = 'End date cannot be before start date';
        }
        if ($payload['capacite_max'] < 0) {
            $errors[] = 'Capacity must be a positive number';
        }

        return $errors;
    }
}
