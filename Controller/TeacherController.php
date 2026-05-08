<?php
/**
 * APPOLIOS Teacher Controller
 * Handles teacher-specific functionality
 */

require_once __DIR__ . '/../Controller/BaseController.php';
require_once __DIR__ . '/../Model/User.php';
require_once __DIR__ . '/../Model/Course.php';
require_once __DIR__ . '/../Model/Evenement.php';
require_once __DIR__ . '/../Model/EvenementRessource.php';
require_once __DIR__ . '/../Controller/ActivityLogger.php';
// generateAvatar() is inherited from BaseController

class TeacherController extends BaseController {
    use ActivityLogger;

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
     * Check if user is teacher or admin
     */
    protected function isTeacher() {
        return $this->isLoggedIn() && ($_SESSION['role'] === 'teacher' || $_SESSION['role'] === 'admin');
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
            'total_evenements' => count($this->getEvenementsByCreator($_SESSION['user_id']))
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

        $courseModel = $this->model('Course');
        $courses = $courseModel->getCoursesByTeacher($_SESSION['user_id']);

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

        $this->view('FrontOffice/teacher/course_detail', $data);
    }

    /**
     * Teacher profile
     */
    public function profile() {
        $this->requireTeacher();

        $user = $this->findUserById($_SESSION['user_id']);

        $data = [
            'title' => 'My Profile - APPOLIOS',
            'user' => $user,
            'flash' => $this->getFlash()
        ];

        $this->view('FrontOffice/teacher/profile', $data);
    }

    /**
     * Edit profile page
     */
    public function editProfile() {
        $this->requireTeacher();

        $user = $this->findUserById($_SESSION['user_id']);

        $data = [
            'title' => 'Edit Profile - APPOLIOS',
            'user' => $user,
            'flash' => $this->getFlash()
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

        $currentUser = $this->findUserById($_SESSION['user_id']);

        // Check if email is taken by another user
        if ($email !== $currentUser['email']) {
            if ($this->emailExists($email)) {
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

        if ($this->updateUser($_SESSION['user_id'], $updateData)) {
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
     * Get all participations for the events created by this teacher
     */
    private function getParticipationsForTeacher(int $teacherId): array {
        $st = $this->getDb()->prepare(
            "SELECT r.id, r.evenement_id, r.created_by as student_id,
                    r.title as student_name, r.details as status, r.created_at,
                    e.title as event_title, e.date_debut, e.heure_debut,
                    u.name as student_name_full, u.email as student_email,
                    u.role as student_role, u.created_at as student_registered_at
             FROM evenement_ressources r
             JOIN evenements e ON r.evenement_id = e.id
             JOIN users u ON r.created_by = u.id
             WHERE r.type = 'participation' AND e.created_by = ?
             ORDER BY r.created_at DESC"
        );
        $st->execute([$teacherId]);
        return $st->fetchAll();
    }

    /**
     * List all evenements created by this teacher.
     */
    public function evenements() {
        $this->requireTeacher();

        $evenements = $this->getEvenementsByCreator((int)$_SESSION['user_id']);

        $allParticipations = $this->getParticipationsForTeacher((int)$_SESSION['user_id']);
        $participationsByEvent = [];
        foreach ($allParticipations as $p) {
            $eventId = (int)$p['evenement_id'];
            if (!isset($participationsByEvent[$eventId])) {
                $participationsByEvent[$eventId] = [];
            }
            $participationsByEvent[$eventId][] = $p;
        }

        $data = [
            'title' => 'Mes Evenements - APPOLIOS',
            'evenements' => $evenements,
            'participationsByEvent' => $participationsByEvent,
            'teacherSidebarActive' => 'evenements',
            'flash' => $this->getFlash()
        ];

        $this->view('FrontOffice/teacher/evenements', $data);
    }

    /**
     * Approve student participation (Teacher specific)
     */
    public function approveParticipation($id) {
        $this->requireTeacher();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect('teacher/evenements'); return; }

        $participation = $this->queryFindParticipationById((int) $id);
        if (!$participation) {
            $this->setFlash('error', 'Participation not found.');
            $this->redirect('teacher/evenements');
            return;
        }

        // Verify the teacher owns this event
        $event = $this->findEvenementByIdAndCreator((int)$participation['evenement_id'], (int)$_SESSION['user_id']);
        if (!$event) {
            $this->setFlash('error', 'Access denied.');
            $this->redirect('teacher/evenements');
            return;
        }

        $this->queryUpdateParticipationStatus((int) $id, 'approved');
        $this->setFlash('success', 'Participation approved. A ticket has been sent to the student.');

        // SEND TICKET VIA EMAIL
        $stUser = $this->getDb()->prepare('SELECT name, email FROM users WHERE id = ?');
        $stUser->execute([(int)$participation['created_by']]);
        $student = $stUser->fetch();

        if ($student && !empty($student['email'])) {
            $to = $student['email'];
            $subject = "Your Official Ticket: " . $event['title'];
            
            $dateVal = !empty($event['event_date']) ? $event['event_date'] : (!empty($event['date_debut']) ? $event['date_debut'] : null);
            $date = $dateVal ? date('M d, Y', strtotime($dateVal)) : 'TBA';
            $location = !empty($event['lieu']) ? $event['lieu'] : 'TBA';
            
            $message = "
            <html>
            <body style='background-color: #f1f5f9; margin: 0; padding: 20px; font-family: Arial, sans-serif;'>
                <table width='100%' cellpadding='0' cellspacing='0' border='0'>
                    <tr>
                        <td align='center'>
                            <table width='600' cellpadding='0' cellspacing='0' border='0' style='background-color: #ffffff; border-radius: 16px; overflow: hidden; border-collapse: collapse; box-shadow: 0 10px 25px rgba(0,0,0,0.1);'>
                                <tr>
                                    <!-- LEFT SIDE -->
                                    <td width='420' valign='top' style='padding: 30px; border-right: 2px dashed #e2e8f0;'>
                                        <div style='color: #548CA8; font-weight: bold; font-size: 16px; margin-bottom: 20px;'>APPOLIOS</div>
                                        <div style='background-color: #e0f2fe; color: #0369a1; padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; display: inline-block; margin-bottom: 15px;'>Official Event Pass</div>
                                        <h1 style='color: #1e293b; font-size: 24px; margin: 0 0 25px 0; line-height: 1.3;'>" . htmlspecialchars($event['title']) . "</h1>
                                        
                                        <table width='100%' cellpadding='0' cellspacing='0' border='0'>
                                            <tr>
                                                <td width='50%' valign='top' style='padding-bottom: 20px;'>
                                                    <div style='font-size: 10px; color: #94a3b8; font-weight: bold; text-transform: uppercase; margin-bottom: 4px;'>Attendee</div>
                                                    <div style='font-size: 14px; color: #334155; font-weight: bold;'>" . htmlspecialchars($student['name']) . "</div>
                                                </td>
                                                <td width='50%' valign='top' style='padding-bottom: 20px;'>
                                                    <div style='font-size: 10px; color: #94a3b8; font-weight: bold; text-transform: uppercase; margin-bottom: 4px;'>Date & Time</div>
                                                    <div style='font-size: 14px; color: #334155; font-weight: bold;'>{$date}</div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width='50%' valign='top'>
                                                    <div style='font-size: 10px; color: #94a3b8; font-weight: bold; text-transform: uppercase; margin-bottom: 4px;'>Location</div>
                                                    <div style='font-size: 14px; color: #334155; font-weight: bold;'>" . htmlspecialchars($location) . "</div>
                                                </td>
                                                <td width='50%' valign='top'>
                                                    <div style='font-size: 10px; color: #94a3b8; font-weight: bold; text-transform: uppercase; margin-bottom: 4px;'>Ticket Type</div>
                                                    <div style='font-size: 14px; color: #334155; font-weight: bold;'>Student Pass</div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <!-- RIGHT SIDE -->
                                    <td width='180' valign='middle' align='center' style='background-color: #2B4865; padding: 20px;'>
                                        <div style='background-color: #ffffff; padding: 10px; border-radius: 8px; display: inline-block; margin-bottom: 10px;'>
                                            <img src='https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=TICKET-{$id}-" . urlencode($student['name']) . "' width='120' height='120' style='display: block; border: 0;' alt='QR Code'>
                                        </div>
                                        <div style='font-size: 10px; font-weight: bold; color: #94a3b8; letter-spacing: 1px; margin-bottom: 20px;'>SCAN TO VALIDATE</div>
                                        <div style='color: #10b981; font-weight: bold; font-size: 16px; border: 2px solid #10b981; padding: 6px 12px; border-radius: 6px; text-transform: uppercase; display: inline-block;'>APPROVED</div>
                                        <div style='font-size: 10px; color: rgba(255,255,255,0.5); margin-top: 30px;'>#ID-" . str_pad($id, 6, '0', STR_PAD_LEFT) . "</div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </body>
            </html>";

            $this->sendEmail($to, $subject, $message);
        }

        $this->redirect('teacher/evenements');
    }

    /**
     * Reject student participation (Teacher specific)
     */
    public function rejectParticipation($id) {
        $this->requireTeacher();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect('teacher/evenements'); return; }

        $reason = $this->sanitize($_POST['reason'] ?? 'No specific reason provided.');
        $participation = $this->queryFindParticipationById((int) $id);
        
        if (!$participation) {
            $this->setFlash('error', 'Participation not found.');
        } else {
            // Verify the teacher owns this event
            $event = $this->findEvenementByIdAndCreator((int)$participation['evenement_id'], (int)$_SESSION['user_id']);
            if (!$event) {
                $this->setFlash('error', 'Access denied.');
                $this->redirect('teacher/evenements');
                return;
            }
            $this->queryUpdateParticipationStatus((int) $id, 'rejected', $reason);
            $this->setFlash('success', 'Participation rejected.');
        }
        $this->redirect('teacher/evenements');
    }

    /**
     * Send an HTML email using MailService (PHPMailer)
     */
    private function sendEmail($to, $subject, $message) {
        require_once __DIR__ . '/EventController.php';
        EventController::sendRaw($to, $subject, $message);
    }

    private function queryFindParticipationById(int $id): array|false {
        $st = $this->getDb()->prepare(
            "SELECT * FROM evenement_ressources WHERE id = ? AND type = 'participation' LIMIT 1"
        );
        $st->execute([$id]);
        return $st->fetch();
    }

    private function queryUpdateParticipationStatus(int $id, string $status, string $reason = null): bool {
        $st = $this->getDb()->prepare(
            "UPDATE evenement_ressources
             SET details = ?, rejection_reason = ?, updated_at = CURRENT_TIMESTAMP
             WHERE id = ? AND type = 'participation'"
        );
        return $st->execute([$status, $reason, $id]);
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
        $evenementModel = $this->model('Evenement');

        $result = $this->createEvenementRecord([
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

        $evenementModel = $this->model('Evenement');
        $evenement = $this->findEvenementByIdAndCreator((int) $id, (int) $_SESSION['user_id']);

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

        $evenementModel = $this->model('Evenement');
        $existing = $this->findEvenementByIdAndCreator((int) $id, (int) $_SESSION['user_id']);
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
        $result = $this->updateEvenementRecord((int) $id, [
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
            $this->markPendingIfApproved((int) $id);
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
        $evenement = $this->findEvenementByIdAndCreator((int) $id, (int) $_SESSION['user_id']);

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

        $result = $this->deleteEvenementRecord((int) $id);
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

        $event = $this->findEvenementByIdAndCreator($eventId, (int) $_SESSION['user_id']);
        if (!$event) {
            $this->setFlash('error', 'Evenement not found or access denied.');
            $this->redirect('teacher/evenements');
            return;
        }

        $db = $this->getDb();

        // Find resource to edit (if any)
        $editId = (int) ($_GET['edit_id'] ?? 0);
        $editResource = null;
        if ($editId > 0) {
            $stmt = $db->prepare(
                "SELECT * FROM evenement_ressources WHERE id = ? AND evenement_id = ?"
            );
            $stmt->execute([$editId, $eventId]);
            $editResource = $stmt->fetch() ?: null;
        }

        // Fetch resources by type using direct PDO queries
        $stmtType = $db->prepare(
            "SELECT * FROM evenement_ressources WHERE type = ? AND evenement_id = ? ORDER BY created_at ASC"
        );

        $stmtType->execute(['rule', $eventId]);
        $rules = $stmtType->fetchAll();

        $stmtType->execute(['materiel', $eventId]);
        $materials = $stmtType->fetchAll();

        $stmtType->execute(['plan', $eventId]);
        $plans = $stmtType->fetchAll();

        $data = [
            'title'               => 'Evenement Resources - APPOLIOS',
            'selectedEvenementId' => $eventId,
            'selectedEvenement'   => $event,
            'editResource'        => $editResource,
            'rules'               => $rules,
            'materials'           => $materials,
            'plans'               => $plans,
            'flash'               => $this->getFlash()
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

        $evenementModel = $this->model('Evenement');
        $event = $this->findEvenementByIdAndCreator($eventId, (int) $_SESSION['user_id']);
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
            $this->markPendingIfApproved($eventId);
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
        $event = $this->findEvenementByIdAndCreator($eventId, (int) $_SESSION['user_id']);
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
            $this->markPendingIfApproved($eventId);
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
        $event = $this->findEvenementByIdAndCreator($eventId, (int) $_SESSION['user_id']);
        if (!$event) {
            $this->setFlash('error', 'Evenement not found or access denied.');
            $this->redirect('teacher/evenements');
            return;
        }

        $ressourceModel = $this->model('EvenementRessource');
        $result = $ressourceModel->deleteByEvenement((int) $id, $eventId);

        if ($result) {
            $this->markPendingIfApproved($eventId);
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
     * Upload avatar image
     */
    public function uploadAvatar() {
        if (!$this->isLoggedIn()) {
            echo json_encode(['success' => false, 'error' => 'Please login']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid request']);
            return;
        }

        if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'error' => 'No file uploaded or upload error']);
            return;
        }

        $file = $_FILES['avatar'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        // Check file type
        if (!in_array($file['type'], $allowedTypes)) {
            echo json_encode(['success' => false, 'error' => 'Invalid file type. Only JPG, PNG, GIF, and WEBP are allowed.']);
            return;
        }

        // Check file size (max 10MB)
        if ($file['size'] > 10 * 1024 * 1024) {
            echo json_encode(['success' => false, 'error' => 'File size must be less than 10MB']);
            return;
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        $uploadDir = __DIR__ . '/../uploads/avatars/';

        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
            // Update user avatar in database
            // Delete old avatar if exists
            $currentUser = $this->findUserById($_SESSION['user_id']);
            if (!empty($currentUser['avatar']) && file_exists($uploadDir . $currentUser['avatar'])) {
                unlink($uploadDir . $currentUser['avatar']);
            }

            if ($this->updateUser($_SESSION['user_id'], ['avatar' => $filename])) {
                $_SESSION['user_avatar'] = $filename;
                echo json_encode(['success' => true, 'avatar' => $filename]);
            } else {
                unlink($uploadDir . $filename);
                echo json_encode(['success' => false, 'error' => 'Failed to save avatar']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to upload file']);
        }
    }

    // ==========================================
    // DATABASE METHODS - For User operations
    // ==========================================

    public function findUserById($id)
    {
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->getDb()->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function emailExists($email)
    {
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = $this->getDb()->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch() !== false;
    }

    public function updateUser($id, $data)
    {
        // Get old data for audit trail
        $oldUser = $this->findUserById($id);

        $fields = [];
        $values = [];
        foreach ($data as $key => $value) {
            $fields[] = "{$key} = ?";
            $values[] = $value;
        }
        $values[] = $id;
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->getDb()->prepare($sql);
        
        $result = $stmt->execute($values);
        
        if ($result && $oldUser) {
            $this->logDiff('update_user', $oldUser, $data, "User updated profile:");
        }

        return $result;
    }

    public function getUsersWithFaceDescriptors()
    {
        $sql = "SELECT id, name, email, face_descriptor FROM users WHERE face_descriptor IS NOT NULL AND face_descriptor != ''";
        $stmt = $this->getDb()->query($sql);
        return $stmt ? $stmt->fetchAll() : [];
    }

    /**
     * Update Face ID descriptor
     */
    public function updateFaceId() {
        if (!$this->isLoggedIn()) {
            echo json_encode(['success' => false, 'error' => 'Please login']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid request']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $faceDescriptor = $data['face_descriptor'] ?? null;

        if (!$faceDescriptor) {
            echo json_encode(['success' => false, 'error' => 'No face descriptor provided']);
            return;
        }

        $sql = "UPDATE users SET face_descriptor = ? WHERE id = ?";
        $stmt = $this->getDb()->prepare($sql);
        
        if ($stmt->execute([$faceDescriptor, $_SESSION['user_id']])) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to update face descriptor']);
        }
    }

    /**
     * Remove Face ID descriptor
     */
    public function removeFaceId() {
        if (!$this->isLoggedIn()) {
            echo json_encode(['success' => false, 'error' => 'Please login']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid request']);
            return;
        }

        $sql = "UPDATE users SET face_descriptor = NULL WHERE id = ?";
        $stmt = $this->getDb()->prepare($sql);
        
        if ($stmt->execute([$_SESSION['user_id']])) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to remove face descriptor']);
        }
    }

    /**
     * Check if face is unique - returns other users with face descriptors
     */
    public function checkFaceUnique() {
        if (!$this->isLoggedIn()) {
            echo json_encode(['success' => false, 'error' => 'Please login']);
            return;
        }

        $users = $this->getUsersWithFaceDescriptors();
        
        // Filter out current user
        $otherUsers = array_filter($users, function($user) {
            return $user['id'] != $_SESSION['user_id'];
        });
        
        echo json_encode(['success' => true, 'users' => array_values($otherUsers)]);
    }

    /**
     * Generate avatar from face photo
     */
    public function generateAvatar() {
        header('Content-Type: application/json');

        if (!$this->isLoggedIn()) {
            echo json_encode(['success' => false, 'error' => 'Please login']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid request']);
            return;
        }

        if (!isset($_FILES['faceImage']) || $_FILES['faceImage']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'error' => 'No image uploaded or upload error']);
            return;
        }

        $file = $_FILES['faceImage'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

        if (!in_array($file['type'], $allowedTypes)) {
            echo json_encode(['success' => false, 'error' => 'Invalid file type. Only JPG, PNG, and WEBP are allowed.']);
            return;
        }

        if ($file['size'] > 10 * 1024 * 1024) {
            echo json_encode(['success' => false, 'error' => 'File size must be less than 10MB']);
            return;
        }

        $faceData = json_decode($_POST['faceData'] ?? '{}', true);

        try {
            // Generate avatar via BaseController method
            $result = $this->buildAvatarFromFace($faceData, $_SESSION['user_id']);
            echo json_encode($result);
        } catch (Throwable $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function deleteAccount()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_ENTRY . '?url=teacher/edit-profile');
            exit;
        }

        $userId = $_SESSION['user_id'];
        
        try {
            $sql = "DELETE FROM users WHERE id = ?";
            $stmt = $this->getDb()->prepare($sql);
            $stmt->execute([$userId]);

            // Clear session and logout
            session_unset();
            session_destroy();
            
            if (isset($_COOKIE['remember_token'])) {
                setcookie('remember_token', '', time() - 3600, '/');
            }

            header('Location: ' . APP_ENTRY . '?url=auth/login&deleted=1');
            exit;
        } catch (PDOException $e) {
            error_log("Failed to delete account: " . $e->getMessage());
            header('Location: ' . APP_ENTRY . '?url=teacher/edit-profile&error=failed');
            exit;
        }
    }

    // ==========================================
    // DATABASE METHODS (Moved from Models)
    // ==========================================

    private function getEvenementsByCreator(int $creatorId): array {
        $st = $this->getDb()->prepare(
            "SELECT e.*, u.name as creator_name,
                    COALESCE(rc.resource_count, 0) AS resource_count
             FROM evenements e
             JOIN users u ON u.id = e.created_by
             LEFT JOIN (
                 SELECT evenement_id, COUNT(*) AS resource_count
                 FROM evenement_ressources
                 WHERE type != 'participation'
                 GROUP BY evenement_id
             ) rc ON rc.evenement_id = e.id
             WHERE e.created_by = ?
             ORDER BY e.created_at DESC"
        );
        $st->execute([$creatorId]);
        return $st->fetchAll();
    }

    private function createEvenementRecord(array $data): int|false {
        $st = $this->getDb()->prepare(
            "INSERT INTO evenements (
                title, titre, description, date_debut, date_fin,
                heure_debut, heure_fin, lieu, capacite_max, type,
                statut, approval_status, location, event_date, created_by, created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())"
        );
        
        $result = $st->execute([
            $data['title'] ?? '', $data['titre'] ?? '', $data['description'] ?? '',
            $data['date_debut'] ?? '', $data['date_fin'] ?? null,
            $data['heure_debut'] ?? null, $data['heure_fin'] ?? null,
            $data['lieu'] ?? '', $data['capacite_max'] ?? null,
            $data['type'] ?? 'general', $data['statut'] ?? 'planifie',
            $data['approval_status'] ?? 'pending', $data['location'] ?? '',
            $data['event_date'] ?? null, $data['created_by'] ?? 0
        ]);
        
        return $result ? (int) $this->getDb()->lastInsertId() : false;
    }

    private function findEvenementByIdAndCreator(int $id, int $creatorId): array|false {
        $st = $this->getDb()->prepare("SELECT * FROM evenements WHERE id = ? AND created_by = ? LIMIT 1");
        $st->execute([$id, $creatorId]);
        return $st->fetch();
    }

    private function updateEvenementRecord(int $id, array $data): bool {
        $st = $this->getDb()->prepare(
            "UPDATE evenements
             SET title=?, titre=?, description=?, date_debut=?, date_fin=?,
                 heure_debut=?, heure_fin=?, lieu=?, capacite_max=?, type=?,
                 statut=?, location=?, event_date=?, updated_at=NOW()
             WHERE id=?"
        );
        return $st->execute([
            $data['title'], $data['titre'], $data['description'], $data['date_debut'], $data['date_fin'],
            $data['heure_debut'], $data['heure_fin'], $data['lieu'], $data['capacite_max'], $data['type'],
            $data['statut'], $data['location'], $data['event_date'], $id
        ]);
    }

    private function markPendingIfApproved(int $id): bool {
        $st = $this->getDb()->prepare(
            "UPDATE evenements SET approval_status = 'pending', updated_at = NOW() WHERE id = ? AND approval_status = 'approved'"
        );
        return $st->execute([$id]);
    }

    private function deleteEvenementRecord(int $id): bool {
        $st = $this->getDb()->prepare("DELETE FROM evenements WHERE id = ?");
        return $st->execute([$id]);
    }

    // ==========================================
    // AI RESOURCE GENERATION
    // ==========================================

    /**
     * POST teacher/generate-ai-resources
     * Calls Gemini API and inserts generated rules/materials/plans into DB.
     */
    public function generateAiResources(): void
    {
        $this->requireTeacher();
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        $eventId   = (int) ($_POST['evenement_id'] ?? 0);
        $teacherId = (int) $_SESSION['user_id'];

        if ($eventId <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID événement invalide.']);
            return;
        }

        $event = $this->findEvenementByIdAndCreator($eventId, $teacherId);
        if (!$event) {
            echo json_encode(['success' => false, 'message' => 'Événement introuvable ou accès refusé.']);
            return;
        }

        $apiKey = defined('GEMINI_API_KEY') ? GEMINI_API_KEY : '';
        if (empty($apiKey)) {
            echo json_encode(['success' => false, 'message' => 'La clé API Gemini n\'est pas configurée dans config.php.']);
            return;
        }

        $title       = $event['title'] ?? $event['titre'] ?? 'Événement';
        $description = $event['description'] ?? '';
        $type        = $event['type'] ?? '';

        $prompt = "Pour un événement intitulé \"$title\" de type \"$type\" avec la description: \"$description\", "
                . "génère des ressources structurées en JSON avec exactement ce format:\n"
                . "{\n"
                . "  \"rules\": [{\"title\": \"...\", \"details\": \"...\"}],\n"
                . "  \"materials\": [{\"title\": \"...\", \"details\": \"...\"}],\n"
                . "  \"plans\": [{\"title\": \"...\", \"details\": \"...\"}]\n"
                . "}\n"
                . "Génère 3 règles, 3 matériels nécessaires et 3 éléments de plan journée. "
                . "Réponds UNIQUEMENT avec le JSON, sans texte supplémentaire.";

        $url     = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=' . $apiKey;
        $payload = json_encode(['contents' => [['parts' => [['text' => $prompt]]]]]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $httpCode !== 200) {
            $errBody = is_string($response) ? json_decode($response, true) : null;
            $errMsg  = $errBody['error']['message'] ?? "Erreur API Gemini (HTTP $httpCode).";
            echo json_encode(['success' => false, 'message' => $errMsg]);
            return;
        }

        $responseData = json_decode($response, true);
        $text = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? '';

        // Extract JSON block from response
        if (preg_match('/\{.*\}/s', $text, $match)) {
            $text = $match[0];
        }

        $resources = json_decode($text, true);
        if (!$resources || !isset($resources['rules'], $resources['materials'], $resources['plans'])) {
            echo json_encode(['success' => false, 'message' => 'Format de réponse invalide de l\'API Gemini.']);
            return;
        }

        // Insert generated resources into DB
        $db   = $this->getDb();
        $stmt = $db->prepare(
            "INSERT INTO evenement_ressources (evenement_id, type, title, details, created_by) VALUES (?, ?, ?, ?, ?)"
        );

        $typeMap = ['rules' => 'rule', 'materials' => 'materiel', 'plans' => 'plan'];
        foreach ($typeMap as $key => $dbType) {
            foreach (($resources[$key] ?? []) as $item) {
                $stmt->execute([
                    $eventId,
                    $dbType,
                    trim($item['title'] ?? ''),
                    trim($item['details'] ?? ''),
                    $teacherId,
                ]);
            }
        }

        echo json_encode(['success' => true, 'message' => 'Ressources générées avec succès.']);
    }

    // ==========================================
    // EVENT STATISTICS
    // ==========================================

    /**
     * Teacher event statistics page
     */
    public function statEvenements()
    {
        if (!$this->isLoggedIn() || $_SESSION['role'] !== 'teacher') {
            $this->redirect('login');
            return;
        }

        $teacherId = (int) $_SESSION['user_id'];
        $db = $this->getDb();

        // Per-event participation counts for THIS teacher's events
        $stmt = $db->prepare(
            "SELECT e.id, e.title, e.capacite_max, e.statut, e.type,
                    COUNT(r.id) AS participant_count
             FROM evenements e
             LEFT JOIN evenement_ressources r
                    ON r.evenement_id = e.id AND r.type = 'participation'
             WHERE e.created_by = ?
             GROUP BY e.id, e.title, e.capacite_max, e.statut, e.type
             ORDER BY e.created_at DESC"
        );
        $stmt->execute([$teacherId]);
        $eventStats = $stmt->fetchAll();

        // Event type distribution for this teacher
        $stmt2 = $db->prepare(
            "SELECT COALESCE(type, 'Autre') AS type, COUNT(*) AS count
             FROM evenements
             WHERE created_by = ?
             GROUP BY type
             ORDER BY count DESC"
        );
        $stmt2->execute([$teacherId]);
        $typeStats = $stmt2->fetchAll();

        // Participations by event type for this teacher
        $stmt3 = $db->prepare(
            "SELECT COALESCE(e.type, 'Autre') AS type,
                    COUNT(r.id) AS participant_count
             FROM evenement_ressources r
             JOIN evenements e ON e.id = r.evenement_id
             WHERE e.created_by = ? AND r.type = 'participation'
             GROUP BY e.type
             ORDER BY participant_count DESC"
        );
        $stmt3->execute([$teacherId]);
        $participantTypeStats = $stmt3->fetchAll();

        $this->view('FrontOffice/teacher/stat_evenement', [
            'title'               => 'Mes Statistiques Événements',
            'teacherSidebarActive'=> 'stat-evenements',
            'eventStats'          => $eventStats,
            'typeStats'           => $typeStats,
            'participantTypeStats'=> $participantTypeStats,
            'flash'               => $this->getFlash(),
        ]);
    }

    /**
     * Export teacher event statistics to PDF
     */
    public function exportStatsPdf()
    {
        $this->requireTeacher();
        $teacherId = (int) $_SESSION['user_id'];
        $db = $this->getDb();

        // Get Event Stats for this teacher
        $st = $db->prepare(
            "SELECT e.title, e.capacite_max, e.event_date, e.date_debut,
                    (SELECT COUNT(*) FROM evenement_ressources r WHERE r.evenement_id = e.id AND r.type = 'participation' AND r.details = 'approved') as participant_count
             FROM evenements e
             WHERE e.created_by = ?
             ORDER BY COALESCE(e.date_debut, e.event_date, e.created_at) ASC"
        );
        $st->execute([$teacherId]);
        $eventStats = $st->fetchAll();

        // Get Type Stats for this teacher
        $stTypes = $db->prepare(
            "SELECT type, COUNT(*) as count 
             FROM evenements 
             WHERE created_by = ? 
             GROUP BY type"
        );
        $stTypes->execute([$teacherId]);
        $typeStats = $stTypes->fetchAll();

        // Generate Simple PDF/Printable HTML
        header('Content-Type: text/html; charset=utf-8');
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Event Statistics Export - APPOLIOS</title>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body {
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    font-size: 14px;
                    line-height: 1.6;
                    color: #333;
                    padding: 40px;
                }
                .header {
                    text-align: center;
                    margin-bottom: 40px;
                    padding-bottom: 20px;
                    border-bottom: 3px solid #548CA8;
                }
                .header h1 {
                    color: #2B4865;
                    font-size: 28px;
                    margin-bottom: 10px;
                }
                .header p {
                    color: #666;
                }
                h2 {
                    color: #548CA8;
                    font-size: 20px;
                    margin-top: 30px;
                    margin-bottom: 15px;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 30px;
                }
                th, td {
                    border: 1px solid #ccc;
                    padding: 12px;
                    text-align: left;
                }
                th {
                    background-color: #f8fafc;
                    color: #2B4865;
                    font-weight: bold;
                }
                tr:nth-child(even) {
                    background-color: #fafafa;
                }
                .text-center { text-align: center; }
                .text-right { text-align: right; }
                .footer {
                    text-align: center;
                    margin-top: 50px;
                    font-size: 12px;
                    color: #999;
                    border-top: 1px solid #eee;
                    padding-top: 20px;
                }
                @media print {
                    body { padding: 0; }
                    .no-print { display: none; }
                }
            </style>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
        </head>
        <body>
            <div class="no-print" style="margin-bottom: 20px; text-align: center;">
                <p style="font-size: 16px; color: #548CA8; font-weight: bold;">Generating your PDF, please wait...</p>
                <p style="font-size: 12px; color: #666;">This tab will automatically close once the download begins.</p>
            </div>

            <div id="pdf-content" style="padding: 20px;">
                <div class="header">
                    <h1>APPOLIOS - My Event Statistics Export</h1>
                    <p>Generated on <?= date('Y-m-d H:i:s') ?></p>
                    <p>Teacher: <?= htmlspecialchars($_SESSION['user_name'] ?? 'Teacher') ?></p>
                </div>

                <h2>1. Participation by Scheduled Event</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Event Title</th>
                            <th>Date</th>
                            <th class="text-right">Max Capacity</th>
                            <th class="text-right">Total Participants</th>
                            <th class="text-right">Fill Rate (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($eventStats)): ?>
                            <tr><td colspan="5" class="text-center">No event data available.</td></tr>
                        <?php else: ?>
                            <?php foreach ($eventStats as $stat): 
                                $date = !empty($stat['event_date']) ? $stat['event_date'] : $stat['date_debut'];
                                $capMax = (int)$stat['capacite_max'];
                                $parts = (int)$stat['participant_count'];
                                $fillRate = $capMax > 0 ? round(($parts / $capMax) * 100, 2) : 0;
                            ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($stat['title']) ?></strong></td>
                                <td><?= $date ? date('M d, Y', strtotime($date)) : 'N/A' ?></td>
                                <td class="text-right"><?= $capMax > 0 ? $capMax : 'Unlimited' ?></td>
                                <td class="text-right"><?= $parts ?></td>
                                <td class="text-right">
                                    <?php if ($fillRate >= 100): ?>
                                        <span style="color: #dc3545; font-weight: bold;"><?= $fillRate ?>% (Full)</span>
                                    <?php elseif ($fillRate >= 75): ?>
                                        <span style="color: #fd7e14;"><?= $fillRate ?>%</span>
                                    <?php else: ?>
                                        <span style="color: #28a745;"><?= $fillRate ?>%</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

                <h2>2. Events Distribution by Category (Type)</h2>
                <table style="width: 50%;">
                    <thead>
                        <tr>
                            <th>Event Type</th>
                            <th class="text-right">Total Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($typeStats)): ?>
                            <tr><td colspan="2" class="text-center">No data available.</td></tr>
                        <?php else: ?>
                            <?php foreach ($typeStats as $ts): ?>
                            <tr>
                                <td><?= htmlspecialchars(ucfirst($ts['type'])) ?></td>
                                <td class="text-right"><?= (int)$ts['count'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

                <div class="footer">
                    &copy; <?= date('Y') ?> APPOLIOS Educational Platform. All rights reserved.
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var element = document.getElementById('pdf-content');
                    var opt = {
                        margin:       0.5,
                        filename:     'APPOLIOS_Teacher_Stats.pdf',
                        image:        { type: 'jpeg', quality: 0.98 },
                        html2canvas:  { scale: 2 },
                        jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
                    };

                    // Generate PDF and then close the window
                    html2pdf().set(opt).from(element).save().then(function() {
                        setTimeout(function() {
                            window.close();
                        }, 1000);
                    });
                });
            </script>
        </body>
        </html>
        <?php
    }
}

