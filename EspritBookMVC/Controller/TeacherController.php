<?php
/**
 * APPOLIOS Teacher Controller
 * Handles teacher-specific functionality
 */

require_once __DIR__ . '/../Controller/BaseController.php';
require_once __DIR__ . '/../Repository/UserRepository.php';
require_once __DIR__ . '/../Repository/CourseRepository.php';
require_once __DIR__ . '/../Model/Entity/EvenementEntity.php';
require_once __DIR__ . '/../Model/Entity/EvenementRessourceEntity.php';
require_once __DIR__ . '/../Presentation/bootstrap.php';
require_once __DIR__ . '/../Model/Entity/UserEntity.php';

class TeacherController extends BaseController {

    /**
     * Route alias for /teacher/courses
     */
    public function courses() {
        $this->myCourses();
    }

    /**
     * Route alias for /teacher/course/{id}
     */
    public function course($id) {
        $this->viewCourse($id);
    }

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

        $userRepository = $this->model('UserRepository');
        $courseRepository = $this->model('CourseRepository');

        // Get stats
        $myCourses = $courseRepository->getCoursesByTeacher($_SESSION['user_id']);
        $stats = [
            'total_courses' => count($myCourses),
            'total_students' => $courseRepository->countStudentsByTeacher($_SESSION['user_id']),
            'active_enrollments' => $courseRepository->countActiveEnrollmentsByTeacher($_SESSION['user_id']),
            'total_evenements' => count($this->model('EvenementRepository')->findByCreator((int)$_SESSION['user_id']))
        ];

        $data = [
            'title' => 'Teacher Dashboard - APPOLIOS',
            'userName' => $_SESSION['user_name'],
            'courses' => $myCourses,
            'stats' => $stats
        ];

        $this->view('FrontOffice/teacher/dashboard', $data);
    }

    /**
     * List all my courses
     */
    public function myCourses() {
        $this->requireTeacher();

        $courseRepository = $this->model('CourseRepository');
        $courses = $courseRepository->getCoursesByTeacher($_SESSION['user_id']);

        $data = [
            'title' => 'My Courses - APPOLIOS',
            'courses' => $courses
        ];

        $this->view('FrontOffice/teacher/courses', $data);
    }

    /**
     * Show add course form
     */
    public function addCourse() {
        $this->requireTeacher();

        $data = [
            'title' => 'Add Course - APPOLIOS',
            'course_old' => $this->consumeSessionOld(),
            'flash' => $this->getFlash()
        ];

        $this->view('FrontOffice/teacher/add_course', $data);
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
        $errors = [];
        if (empty($title)) $errors['title'] = 'Title is required';
        if (empty($description)) $errors['description'] = 'Description is required';

        if (!empty($errors)) {
            $this->setErrors($errors);
            $_SESSION['old'] = $_POST;
            $this->setFlash('error', 'Please fix the errors below');
            $this->redirect('teacher/add-course');
            return;
        }

        $courseRepository = $this->model('CourseRepository');
        
        $result = $courseRepository->create([
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

        $courseRepository = $this->model('CourseRepository');
        $course = $courseRepository->findById($id);

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

        $this->view('FrontOffice/teacher/edit_course', $data);
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

        $courseRepository = $this->model('CourseRepository');
        $course = $courseRepository->findById($id);

        // Check if course belongs to this teacher
        if (!$course || $course['created_by'] != $_SESSION['user_id']) {
            $this->setFlash('error', 'Course not found or access denied');
            $this->redirect('teacher/courses');
            return;
        }

        $title = $this->sanitize($_POST['title'] ?? '');
        $description = $this->sanitize($_POST['description'] ?? '');
        $video_url = $this->sanitize($_POST['video_url'] ?? '');

        $errors = [];
        if (empty($title)) $errors['title'] = 'Title is required';
        if (empty($description)) $errors['description'] = 'Description is required';

        if (!empty($errors)) {
            $this->setErrors($errors);
            $_SESSION['old'] = $_POST;
            $this->setFlash('error', 'Please fix the errors below');
            $this->redirect('teacher/edit-course/' . $id);
            return;
        }

        $result = $courseRepository->update($id, [
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

        $courseRepository = $this->model('CourseRepository');
        $course = $courseRepository->findById($id);

        // Check if course belongs to this teacher
        if (!$course || $course['created_by'] != $_SESSION['user_id']) {
            $this->setFlash('error', 'Course not found or access denied');
            $this->redirect('teacher/courses');
            return;
        }

        $result = $courseRepository->delete($id);

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

        $courseRepository = $this->model('CourseRepository');
        $course = $courseRepository->findById($id);

        // Check if course belongs to this teacher
        if (!$course || $course['created_by'] != $_SESSION['user_id']) {
            $this->setFlash('error', 'Course not found or access denied');
            $this->redirect('teacher/courses');
            return;
        }

        // Get enrolled students
        $students = $courseRepository->getEnrolledStudents($id);

        $data = [
            'title' => htmlspecialchars($course['title']) . ' - APPOLIOS',
            'course' => $course,
            'students' => $students
        ];

        $this->view('FrontOffice/teacher/course_detail', $data);
    }

    /**
     * Teacher profile
     */
    public function profile() {
        $this->requireTeacher();

        $userRepository = $this->model('UserRepository');
        $userRow = $userRepository->findById($_SESSION['user_id']);
        if (!is_array($userRow)) {
            $this->setFlash('error', 'User not found.');
            $this->redirect('login');
            return;
        }
        $profileUser = UserEntity::fromPersistedRow($userRow);

        $data = [
            'title' => 'My Profile - APPOLIOS',
            'profile_user' => $profileUser,
            'flash_banner' => FlashBannerPresenter::fromSessionFlash($this->getFlash()),
        ];

        $this->view('FrontOffice/teacher/edit_profile', $data);
    }

    /**
     * Update profile
     */
    public function updateProfile() {
        $this->requireTeacher();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('teacher/edit-profile');
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
            $this->setFlash('error', implode("\n", $errors));
            $this->redirect('teacher/edit-profile');
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

            $this->setFlash('success', 'Profile updated successfully!');
        } else {
            $this->setFlash('error', 'Failed to update profile. Please try again.');
        }

        $this->redirect('teacher/profile');
    }

    /**
     * List all my evenements
     */
    public function evenements() {
        $this->requireTeacher();

        $evenements = EventCardPresenter::decorateList(
            $this->model('EvenementRepository')->findByCreator((int) $_SESSION['user_id'])
        );

        $data = [
            'title' => 'My Evenements - APPOLIOS',
            'evenements' => $evenements,
            'flash' => $this->getFlash()
        ];

        $this->view('FrontOffice/teacher/evenements', $data);
    }

    /**
     * Route alias for /teacher/evenement
     */
    public function evenement() {
        $this->evenements();
    }

    /**
     * Show add evenement form for teacher.
     */
    public function addEvenement() {
        $this->requireTeacher();

        $data = [
            'title' => 'Propose Evenement - APPOLIOS',
            'flash' => $this->getFlash(),
            'evenement_old' => $this->consumeSessionOld(),
            'evenement_min_date' => DisplayFormatter::formMinDateTomorrow(),
        ];

        $this->view('FrontOffice/teacher/add_evenement', $data);
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
            $this->setErrors($errors);
            $_SESSION['old'] = $_POST;
            $this->redirect('teacher/add-evenement');
            return;
        }

        $eventDate = $payload['date_debut'] . ' ' . (!empty($payload['heure_debut']) ? $payload['heure_debut'] : '00:00') . ':00';

        $result = $this->model('EvenementRepository')->create([
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
            if (isset($_POST['action']) && $_POST['action'] === 'save_and_resources') {
                $this->redirect('teacher/evenement-ressources&evenement_id=' . $result);
            } else {
                $this->redirect('teacher/evenements');
            }
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

        $evenement = $this->model('EvenementRepository')->findByIdAndCreator((int) $id, (int) $_SESSION['user_id']);

        if (!$evenement) {
            $this->setFlash('error', 'Evenement not found or access denied.');
            $this->redirect('teacher/evenements');
            return;
        }

        $old = $this->consumeSessionOld();
        $form = [
            'title' => $old['title'] ?? ($evenement['titre'] ?? $evenement['title'] ?? ''),
            'description' => $old['description'] ?? ($evenement['description'] ?? ''),
            'date_debut' => $old['date_debut'] ?? ($evenement['date_debut'] ?? ''),
            'date_fin' => $old['date_fin'] ?? ($evenement['date_fin'] ?? ''),
            'heure_debut' => $old['heure_debut'] ?? (isset($evenement['heure_debut']) ? substr((string) $evenement['heure_debut'], 0, 5) : ''),
            'heure_fin' => $old['heure_fin'] ?? (isset($evenement['heure_fin']) ? substr((string) $evenement['heure_fin'], 0, 5) : ''),
            'lieu' => $old['lieu'] ?? (($evenement['lieu'] ?? '') ?: ($evenement['location'] ?? '')),
            'capacite_max' => $old['capacite_max'] ?? ($evenement['capacite_max'] ?? ''),
            'type' => $old['type'] ?? ($evenement['type'] ?? 'general'),
            'statut' => $old['statut'] ?? ($evenement['statut'] ?? 'planifie'),
        ];
        $approval = (string) ($evenement['approval_status'] ?? 'approved');
        $approvalHeadingColor = $approval === 'approved' ? '#22c55e' : ($approval === 'rejected' ? '#ef4444' : '#f97316');

        $data = [
            'title' => 'Edit Evenement - APPOLIOS',
            'evenement' => $evenement,
            'form' => $form,
            'evenement_min_date' => DisplayFormatter::formMinDateTomorrow(),
            'evenement_approval_label' => $approval,
            'evenement_approval_heading_color' => $approvalHeadingColor,
            'flash' => $this->getFlash()
        ];

        $this->view('FrontOffice/teacher/edit_evenement', $data);
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

        $existing = $this->model('EvenementRepository')->findByIdAndCreator((int) $id, (int) $_SESSION['user_id']);
        if (!$existing) {
            $this->setFlash('error', 'Evenement not found or access denied.');
            $this->redirect('teacher/evenements');
            return;
        }

        $payload = $this->extractEvenementPayload();
        $errors = $this->validateEvenementPayload($payload);

        if (!empty($errors)) {
            $this->setErrors($errors);
            $_SESSION['old'] = $_POST;
            $this->redirect('teacher/edit-evenement/' . (int) $id);
            return;
        }

        $eventDate = $payload['date_debut'] . ' ' . (!empty($payload['heure_debut']) ? $payload['heure_debut'] : '00:00') . ':00';
        $result = $this->model('EvenementRepository')->update((int) $id, [
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

        $status = $existing['approval_status'] ?? 'approved';
        $needsReview = in_array($status, ['approved', 'rejected']);
        if ($needsReview) {
            $this->model('EvenementRepository')->markNonPendingAsPending((int) $id);
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

        $evenement = $this->model('EvenementRepository')->findByIdAndCreator((int) $id, (int) $_SESSION['user_id']);

        if (!$evenement) {
            $this->setFlash('error', 'Evenement not found or access denied.');
            $this->redirect('teacher/evenements');
            return;
        }

        $status = $evenement['approval_status'] ?? 'approved';
        if ($status !== 'pending' && $status !== 'rejected') {
            $this->setFlash('error', 'You can only delete pending or rejected evenements. Approved events cannot be deleted.');
            $this->redirect('teacher/evenements');
            return;
        }

        $result = $this->model('EvenementRepository')->delete((int) $id);
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

        $event = $this->model('EvenementRepository')->findByIdAndCreator($eventId, (int) $_SESSION['user_id']);
        if (!$event) {
            $this->setFlash('error', 'Evenement not found or access denied.');
            $this->redirect('teacher/evenements');
            return;
        }

        $editId = (int) ($_GET['edit_id'] ?? 0);
        $editResource = null;
        if ($editId > 0) {
            $candidate = $this->model('EvenementRessourceRepository')->findById($editId);
            if ($candidate && (int) $candidate['evenement_id'] === $eventId) {
                $editResource = $candidate;
            }
        }

        $resRepo = $this->model('EvenementRessourceRepository');
        $participationsList = $resRepo->findParticipationsByEvent($eventId);
        $flash = $this->getFlash();

        $data = [
            'title' => 'Evenement Resources - APPOLIOS',
            'selectedEvenementId' => $eventId,
            'selectedEvenement' => $event,
            'editResource' => $editResource,
            'rules' => $resRepo->findByTypeAndEvent('rule', $eventId),
            'materials' => ResourceMaterielPresenter::decorateItems($resRepo->findByTypeAndEvent('materiel', $eventId)),
            'plans' => $resRepo->findByTypeAndEvent('plan', $eventId),
            'participations' => ParticipationRequestRowPresenter::decorateList($participationsList),
            'participation_rollup' => ParticipationRollupPresenter::rollup($participationsList),
            'ressource_old' => $this->consumeSessionOld(),
            'flash' => $flash,
            'participation_modal_open' => $flash !== null,
        ];

        $this->view('FrontOffice/teacher/evenement_ressources', $data);
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

        $event = $this->model('EvenementRepository')->findByIdAndCreator($eventId, (int) $_SESSION['user_id']);
        if (!$event) {
            $errors[] = 'Evenement not found or access denied.';
        }

        if (!empty($errors)) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
                exit();
            }

            $this->setErrors($errors);
            $_SESSION['old'] = $_POST;
            $this->redirect('teacher/evenement-ressources&evenement_id=' . $eventId);
            return;
        }

        $createdId = $this->model('EvenementRessourceRepository')->create([
            'evenement_id' => $eventId,
            'type' => $type,
            'title' => $title,
            'details' => $details,
            'created_by' => $_SESSION['user_id']
        ]);

        $verified = false;
        if ($createdId) {
            $verified = $this->model('EvenementRessourceRepository')->existsInScope($createdId, $eventId, $type);
        }

        if ($createdId && $verified) {
            $this->model('EvenementRepository')->markNonPendingAsPending($eventId);
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'verified_in_right_list' => true,
                    'message' => 'Resource saved successfully.'
                ]);
                exit();
            }

            $this->setFlash('success', 'Resource saved successfully. Event approval set to pending if it was previously approved or rejected.');
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

        $event = $this->model('EvenementRepository')->findByIdAndCreator($eventId, (int) $_SESSION['user_id']);
        if (!$event) {
            $this->setFlash('error', 'Evenement not found or access denied.');
            $this->redirect('teacher/evenements');
            return;
        }

        $resource = $this->model('EvenementRessourceRepository')->findById((int) $id);
        if (!$resource || (int) $resource['evenement_id'] !== $eventId) {
            $this->setFlash('error', 'Resource not found for this evenement.');
            $this->redirect('teacher/evenement-ressources&evenement_id=' . $eventId);
            return;
        }

        $result = $this->model('EvenementRessourceRepository')->update((int) $id, [
            'title' => $title,
            'details' => $details,
            'evenement_id' => $eventId
        ]);

        if ($result) {
            $this->model('EvenementRepository')->markNonPendingAsPending($eventId);
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

        $event = $this->model('EvenementRepository')->findByIdAndCreator($eventId, (int) $_SESSION['user_id']);
        if (!$event) {
            $this->setFlash('error', 'Evenement not found or access denied.');
            $this->redirect('teacher/evenements');
            return;
        }

        $result = $this->model('EvenementRessourceRepository')->delete((int) $id, $eventId);

        if ($result) {
            $this->model('EvenementRepository')->markNonPendingAsPending($eventId);
            $this->setFlash('success', 'Resource deleted successfully.');
        } else {
            $this->setFlash('error', 'Failed to delete resource.');
        }

        $this->redirect('teacher/evenement-ressources&evenement_id=' . $eventId);
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
            $errors['title'] = 'Event title is required';
        }
        if (empty($payload['description'])) {
            $errors['description'] = 'Event description is required';
        }
        if (empty($payload['date_debut']) || strtotime($payload['date_debut']) === false) {
            $errors['date_debut'] = 'Valid start date is required';
        }
        $minDate = date('Y-m-d', strtotime('+1 day'));
        if (!empty($payload['date_debut']) && strtotime($payload['date_debut']) !== false && $payload['date_debut'] < $minDate) {
            $errors['date_debut'] = 'Start date must be at least tomorrow';
        }
        if (empty($payload['heure_debut'])) {
            $errors['heure_debut'] = 'Start time is required';
        }
        if (!empty($payload['date_fin']) && strtotime($payload['date_fin']) !== false && !empty($payload['date_debut']) && strtotime($payload['date_fin']) < strtotime($payload['date_debut'])) {
            $errors['date_fin'] = 'End date cannot be before start date';
        }
        if ($payload['capacite_max'] < 0) {
            $errors['capacite_max'] = 'Capacity must be a positive number';
        }

        return $errors;
    }

    /**
     * Show all participation requests for teacher's events.
     */
    public function participationRequests() {
        $this->requireTeacher();

        $requests = $this->model('EvenementRessourceRepository')->findParticipationsByCreator((int) $_SESSION['user_id']);

        $data = [
            'title' => 'Participation Requests - APPOLIOS',
            'participation_request_cards' => ParticipationRequestRowPresenter::decorateList($requests),
            'participation_counts' => ParticipationRollupPresenter::rollup($requests),
            'flash_strip' => FlashBannerPresenter::fromSessionFlash($this->getFlash()),
        ];

        $this->view('FrontOffice/teacher/participation_requests', $data);
    }

    /**
     * Approve a participation request.
     */
    public function approveParticipation($id) {
        $this->requireTeacher();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('teacher/participation-requests');
            return;
        }

        $participation = $this->model('EvenementRessourceRepository')->findParticipationById((int) $id);
        if (!$participation || !$this->model('EvenementRepository')->isCreatedBy((int) $participation['evenement_id'], (int) $_SESSION['user_id'])) {
            $this->setFlash('error', 'Participation request not found or access denied.');
            $this->redirect('teacher/participation-requests');
            return;
        }

        if ($this->model('EvenementRessourceRepository')->updateParticipationStatusTeacher((int) $id, 'approved')) {
            $this->setFlash('success', 'Participation approved.');
        } else {
            $this->setFlash('error', 'Failed to approve participation.');
        }

        $fromEventId = (int)($_POST['from_evenement_id'] ?? 0);
        if ($fromEventId > 0) {
            $this->redirect('teacher/evenement-ressources&evenement_id=' . $fromEventId);
        } else {
            $this->redirect('teacher/participation-requests');
        }
    }

    /**
     * Reject a participation request.
     */
    public function rejectParticipation($id) {
        $this->requireTeacher();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('teacher/participation-requests');
            return;
        }

        $participation = $this->model('EvenementRessourceRepository')->findParticipationById((int) $id);
        if (!$participation || !$this->model('EvenementRepository')->isCreatedBy((int) $participation['evenement_id'], (int) $_SESSION['user_id'])) {
            $this->setFlash('error', 'Participation request not found or access denied.');
            $this->redirect('teacher/participation-requests');
            return;
        }

        $reason = $this->sanitize($_POST['reason'] ?? 'No specific reason provided.');

        if ($this->model('EvenementRessourceRepository')->updateParticipationStatusTeacher((int) $id, 'rejected', $reason)) {
            $this->setFlash('success', 'Participation rejected with reason.');
        } else {
            $this->setFlash('error', 'Failed to reject participation.');
        }

        $fromEventId = (int)($_POST['from_evenement_id'] ?? 0);
        if ($fromEventId > 0) {
            $this->redirect('teacher/evenement-ressources&evenement_id=' . $fromEventId);
        } else {
            $this->redirect('teacher/participation-requests');
        }
    }

    /**
     * Delete a participation record (teacher-owned events only).
     */
    public function deleteParticipation($id) {
        $this->requireTeacher();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('teacher/participation-requests');
            return;
        }

        $participation = $this->model('EvenementRessourceRepository')->findParticipationById((int) $id);
        if (!$participation || !$this->model('EvenementRepository')->isCreatedBy((int) $participation['evenement_id'], (int) $_SESSION['user_id'])) {
            $this->setFlash('error', 'Access denied. You can only delete participations for events you created.');
            $this->redirect('teacher/participation-requests');
            return;
        }

        $deleted = $this->model('EvenementRessourceRepository')->deleteParticipationById((int) $id);

        if ($deleted > 0) {
            $this->setFlash('success', 'Participation removed successfully.');
        } else {
            $this->setFlash('error', 'Participation not found.');
        }

        $fromEventId = (int)($_POST['from_evenement_id'] ?? 0);
        if ($fromEventId > 0) {
            $this->redirect('teacher/evenement-ressources&evenement_id=' . $fromEventId);
        } else {
            $this->redirect('teacher/participation-requests');
        }
    }
}
