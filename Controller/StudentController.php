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
require_once __DIR__ . '/../Controller/ActivityLogger.php';
// generateAvatar() is inherited from BaseController

class StudentController extends BaseController {
    use ActivityLogger;

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
        $evenements = $this->queryApprovedUpcoming();
        $participations = $this->queryParticipationsByUser($_SESSION['user_id']);
        $participationMap = $this->queryParticipationMap($_SESSION['user_id']);

        $data = [
            'title' => 'My Dashboard - APPOLIOS',
            'description' => 'Student evenement dashboard',
            'userName' => $_SESSION['user_name'],
            'evenements' => $evenements,
            'participations' => $participations,
            'participationMap' => $participationMap,
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
            'evenements' => $this->queryApprovedUpcoming(),
            'participations' => $this->queryParticipationsByUser($_SESSION['user_id']),
            'participationMap' => $this->queryParticipationMap($_SESSION['user_id']),
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

        $evenement = $this->queryEventByIdWithCreator($id);
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

        $grouped = $this->queryGroupedRessources($id);
        $participation = $this->queryFindParticipation($id, $_SESSION['user_id']);

        $data = [
            'title' => (($evenement['titre'] ?? '') ?: ($evenement['title'] ?? 'Evenement')) . ' - APPOLIOS',
            'description' => $evenement['description'] ?? 'Evenement details',
            'evenement' => $evenement,
            'rules' => $grouped['rules'],
            'materiels' => $grouped['materiels'],
            'plans' => $grouped['plans'],
            'participation' => $participation,
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

        // Only students or admins can access this page
        if ($_SESSION['role'] !== 'student' && !$this->isAdmin()) {
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

        $user = $this->findUserById($_SESSION['user_id']);

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

        $user = $this->findUserById($_SESSION['user_id']);

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

        if ($this->updateUser($_SESSION['user_id'], $updateData)) {
            // Update session data
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;

            $this->setFlash('success', 'Profile updated successfully!');
        } else {
            $this->setFlash('error', 'Failed to update profile. Please try again.');
        }

        $this->redirect('student/profile');
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

    public function getUsersWithFaceDescriptors()
    {
        $sql = "SELECT id, name, email, face_descriptor FROM users WHERE face_descriptor IS NOT NULL AND face_descriptor != ''";
        $stmt = $this->getDb()->query($sql);
        return $stmt ? $stmt->fetchAll() : [];
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
        // Set JSON header
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

        // Check file type
        if (!in_array($file['type'], $allowedTypes)) {
            echo json_encode(['success' => false, 'error' => 'Invalid file type. Only JPG, PNG, and WEBP are allowed.']);
            return;
        }

        // Check file size (max 10MB)
        if ($file['size'] > 10 * 1024 * 1024) {
            echo json_encode(['success' => false, 'error' => 'File size must be less than 10MB']);
            return;
        }

        // Get face data from POST
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
            header('Location: ' . APP_ENTRY . '?url=student/edit-profile');
            exit;
        }

        $userId = $_SESSION['user_id'];
        
        try {
            // Because relationships are CASCADE in DB, deleting user deletes participations.
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
            header('Location: ' . APP_ENTRY . '?url=student/edit-profile&error=failed');
            exit;
        }
    }

    // ==========================================
    // EVENT PARTICIPATION LOGIC
    // ==========================================

    public function participate($eventId) {
        if (!$this->isLoggedIn()) { $this->redirect('login'); return; }
        
        $evenementModel = $this->model('Evenement');
        $userId = $_SESSION['user_id'];
        
        if (!$this->queryFindParticipation($eventId, $userId)) {
            $this->queryAddParticipation($eventId, $userId);
            $this->setFlash('success', 'Participation request sent successfully.');
        } else {
            $this->setFlash('error', 'You have already requested participation.');
        }
        $this->redirect('student/evenements');
    }

    public function cancelParticipation($eventId) {
        if (!$this->isLoggedIn()) { $this->redirect('login'); return; }
        
        $evenementModel = $this->model('Evenement');
        $this->queryRemoveParticipation($eventId, $_SESSION['user_id']);
        $this->setFlash('success', 'Participation request cancelled.');
        $this->redirect('student/evenements');
    }

    public function downloadTicket($participationId) {
        if (!$this->isLoggedIn()) { $this->redirect('login'); return; }
        
        $evenementModel = $this->model('Evenement');
        $participation = $this->queryApprovedParticipationTicket($participationId, $_SESSION['user_id']);
        
        if (!$participation) {
            $this->setFlash('error', 'Valid ticket not found.');
            $this->redirect('student/evenements');
            return;
        }
        
        $id = $participationId;
        $studentName = $participation['student_name'] ?? 'Student';
        $eventTitle = $participation['event_title'] ?? 'Event';
        
        $dateVal = !empty($participation['date_debut']) ? $participation['date_debut'] : null;
        $date = $dateVal ? date('M d, Y', strtotime($dateVal)) : 'TBA';
        if (!empty($participation['heure_debut'])) {
            $date .= ' ' . substr((string)$participation['heure_debut'], 0, 5);
        }
        $location = !empty($participation['lieu']) ? $participation['lieu'] : 'TBA';

        // Simple HTML output that automatically prints using window.print() or just views the ticket
        echo "
        <html>
        <head>
            <title>Ticket: " . htmlspecialchars($eventTitle) . "</title>
            <style>
                @media print { 
                    body { background: white !important; margin: 0; padding: 0; display: flex; justify-content: center; align-items: flex-start; } 
                    .ticket-container { box-shadow: none !important; border: 2px solid #000 !important; margin-top: 20px; } 
                }
            </style>
        </head>
        <body style='background-color: #f1f5f9; margin: 0; padding: 20px; font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh;'>
            <table width='100%' cellpadding='0' cellspacing='0' border='0'>
                <tr>
                    <td align='center'>
                        <table class='ticket-container' width='600' cellpadding='0' cellspacing='0' border='0' style='background-color: #ffffff; border-radius: 16px; overflow: hidden; border-collapse: collapse; box-shadow: 0 10px 25px rgba(0,0,0,0.1);'>
                            <tr>
                                <!-- LEFT SIDE -->
                                <td width='420' valign='top' style='padding: 30px; border-right: 2px dashed #e2e8f0;'>
                                    <div style='color: #548CA8; font-weight: bold; font-size: 16px; margin-bottom: 20px;'>APPOLIOS</div>
                                    <div style='background-color: #e0f2fe; color: #0369a1; padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; display: inline-block; margin-bottom: 15px;'>Official Event Pass</div>
                                    <h1 style='color: #1e293b; font-size: 24px; margin: 0 0 25px 0; line-height: 1.3;'>" . htmlspecialchars($eventTitle) . "</h1>
                                    
                                    <table width='100%' cellpadding='0' cellspacing='0' border='0'>
                                        <tr>
                                            <td width='50%' valign='top' style='padding-bottom: 20px;'>
                                                <div style='font-size: 10px; color: #94a3b8; font-weight: bold; text-transform: uppercase; margin-bottom: 4px;'>Attendee</div>
                                                <div style='font-size: 14px; color: #334155; font-weight: bold;'>" . htmlspecialchars($studentName) . "</div>
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
                                        <img src='https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=TICKET-{$id}-" . urlencode($studentName) . "' width='120' height='120' style='display: block; border: 0;' alt='QR Code'>
                                    </div>
                                    <div style='font-size: 10px; font-weight: bold; color: #94a3b8; letter-spacing: 1px; margin-bottom: 20px;'>SCAN TO VALIDATE</div>
                                    <div style='color: #10b981; font-weight: bold; font-size: 16px; border: 2px solid #10b981; padding: 6px 12px; border-radius: 6px; text-transform: uppercase; display: inline-block;'>APPROVED</div>
                                    <div style='font-size: 10px; color: rgba(255,255,255,0.5); margin-top: 30px;'>#ID-" . str_pad((string)$id, 6, '0', STR_PAD_LEFT) . "</div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <script>window.onload = function() { window.print(); }</script>
        </body>
        </html>
        ";
    }

    public function recommendEvents() {
        if (!$this->isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Please login']);
            return;
        }

        header('Content-Type: application/json');
        
        $evenementModel = $this->model('Evenement');
        $allEvents = $this->queryApprovedUpcoming();
        $participations = $this->queryParticipationsByUser($_SESSION['user_id']);
        
        if (empty($allEvents)) {
            echo json_encode(['success' => true, 'recommendations' => []]);
            return;
        }
        
        $participatedIds = array_column($participations, 'evenement_id');
        $availableEvents = array_values(array_filter($allEvents, fn($e) => !in_array($e['id'], $participatedIds)));
        
        if (empty($availableEvents)) {
            echo json_encode(['success' => true, 'recommendations' => []]);
            return;
        }

        // Format data for AI prompt
        $eventsList = array_map(function($e) {
            return [
                'id' => $e['id'],
                'title' => $e['titre'] ?: $e['title'],
                'description' => $e['description'],
                'type' => $e['type'],
                'date' => $e['date_debut'] ?? $e['event_date']
            ];
        }, $availableEvents);

        $historyList = array_map(function($p) {
            return [
                'title' => $p['titre'] ?: $p['title'],
                'type' => $p['type']
            ];
        }, $participations);

        $prompt = "You are an AI event recommender for students.
Based on the user's past participation history, recommend the top 3 events they should attend from the available events list.
Return ONLY a valid JSON array of objects with these keys: 'id' (the event ID), 'title' (event title), 'reason' (a 1-sentence personalized reason), and 'match_score' (integer 0-100).

Available Events:
" . json_encode($eventsList) . "

User History:
" . json_encode($historyList);

        $apiKey = defined('GEMINI_API_KEY') ? GEMINI_API_KEY : (getenv('GEMINI_API_KEY') ?: '');
        
        if (empty($apiKey)) {
            // Fallback if no API key
            $recommendations = array_slice(array_map(function($e) {
                return [
                    'id' => $e['id'],
                    'title' => $e['title'],
                    'reason' => 'Great event matching your profile.',
                    'match_score' => rand(80, 99)
                ];
            }, $eventsList), 0, 3);
            
            echo json_encode(['success' => true, 'recommendations' => $recommendations]);
            return;
        }

        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=' . $apiKey;
        $data = [
            'contents' => [
                ['parts' => [['text' => $prompt]]]
            ],
            'generationConfig' => [
                'responseMimeType' => 'application/json'
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err || !$response) {
            echo json_encode(['success' => false, 'message' => 'API Request failed']);
            return;
        }

        $result = json_decode($response, true);

        // Check if Gemini API returned an error
        if (isset($result['error'])) {
            $apiErrorMsg = isset($result['error']['message']) ? $result['error']['message'] : 'Unknown API error';
            echo json_encode(['success' => false, 'message' => 'API Error: ' . $apiErrorMsg, 'response' => $result]);
            return;
        }

        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            $jsonText = $result['candidates'][0]['content']['parts'][0]['text'];
            
            // Clean up potential markdown formatting from AI response
            $jsonText = preg_replace('/```json\s*/i', '', $jsonText);
            $jsonText = preg_replace('/```\s*/i', '', $jsonText);
            $jsonText = trim($jsonText);

            $recommendations = json_decode($jsonText, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($recommendations)) {
                echo json_encode(['success' => true, 'recommendations' => $recommendations]);
                return;
            } else {
                echo json_encode(['success' => false, 'message' => 'JSON parse error: ' . json_last_error_msg(), 'raw' => $jsonText]);
                return;
            }
        }

        echo json_encode(['success' => false, 'message' => 'Invalid AI response format', 'response' => $result]);
    }

    // ==========================================
    // DATABASE METHODS (Moved from Model)
    // ==========================================

    private function queryApprovedUpcoming(): array {
        return $this->getDb()->query(
            "SELECT e.*, u.name as creator_name
             FROM evenements e
             JOIN users u ON e.created_by = u.id
             WHERE e.approval_status = 'approved'
             ORDER BY COALESCE(CONCAT(e.date_debut,' ',e.heure_debut), e.event_date) ASC"
        )->fetchAll();
    }

    private function queryEventByIdWithCreator(int $id): array|false {
        $st = $this->getDb()->prepare(
            "SELECT e.*, u.name as creator_name, u.role as creator_role
             FROM evenements e
             JOIN users u ON u.id = e.created_by
             WHERE e.id = ? LIMIT 1"
        );
        $st->execute([$id]);
        return $st->fetch();
    }

    private function queryParticipationsByUser(int $userId): array {
        $st = $this->getDb()->prepare(
            "SELECT r.id as p_id, r.details as p_status, r.updated_at as p_update_date, r.rejection_reason,
                    e.*
             FROM evenement_ressources r
             JOIN evenements e ON r.evenement_id = e.id
             WHERE r.created_by = ? AND r.type = 'participation'
             ORDER BY r.created_at DESC"
        );
        $st->execute([$userId]);
        return $st->fetchAll();
    }

    private function queryParticipationMap(int $userId): array {
        $st = $this->getDb()->prepare(
            "SELECT evenement_id, details as p_status
             FROM evenement_ressources
             WHERE created_by = ? AND type = 'participation'"
        );
        $st->execute([$userId]);
        $map = [];
        foreach ($st->fetchAll() as $row) {
            $map[$row['evenement_id']] = strtolower($row['p_status']);
        }
        return $map;
    }

    private function queryFindParticipation(int $eventId, int $userId): array|false {
        $st = $this->getDb()->prepare(
            "SELECT * FROM evenement_ressources 
             WHERE evenement_id = ? AND created_by = ? AND type = 'participation'"
        );
        $st->execute([$eventId, $userId]);
        return $st->fetch();
    }

    private function queryAddParticipation(int $eventId, int $userId): bool {
        $st = $this->getDb()->prepare(
            "INSERT INTO evenement_ressources (evenement_id, created_by, type, details, title, created_at) 
             VALUES (?, ?, 'participation', 'pending', 'Student Participation', NOW())"
        );
        return $st->execute([$eventId, $userId]);
    }

    private function queryRemoveParticipation(int $eventId, int $userId): bool {
        $st = $this->getDb()->prepare(
            "DELETE FROM evenement_ressources 
             WHERE evenement_id = ? AND created_by = ? AND type = 'participation'"
        );
        return $st->execute([$eventId, $userId]);
    }

    private function queryApprovedParticipationTicket(int $participationId, int $userId): array|false {
        $st = $this->getDb()->prepare(
            "SELECT r.*, e.title as event_title, e.date_debut, e.heure_debut, e.lieu, u.name as student_name 
             FROM evenement_ressources r 
             JOIN evenements e ON r.evenement_id = e.id 
             JOIN users u ON r.created_by = u.id 
             WHERE r.id = ? AND r.created_by = ? AND r.type = 'participation' AND r.details = 'approved'"
        );
        $st->execute([$participationId, $userId]);
        return $st->fetch();
    }

    private function queryGroupedRessources(int $evenementId): array {
        $st = $this->getDb()->prepare(
            "SELECT r.*, u.name as creator_name
             FROM evenement_ressources r
             JOIN users u ON r.created_by = u.id
             WHERE r.evenement_id = ? AND r.type IN ('rule', 'materiel', 'plan')
             ORDER BY r.created_at DESC"
        );
        $st->execute([$evenementId]);
        
        $rules = [];
        $materiels = [];
        $plans = [];
        
        foreach ($st->fetchAll() as $row) {
            if ($row['type'] === 'rule') $rules[] = $row;
            if ($row['type'] === 'materiel') $materiels[] = $row;
            if ($row['type'] === 'plan') $plans[] = $row;
        }
        
        return [
            'rules' => $rules,
            'materiels' => $materiels,
            'plans' => $plans
        ];
    }
}
