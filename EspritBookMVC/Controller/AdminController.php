<?php
/**
 * APPOLIOS Admin Controller
 * Handles admin dashboard and management
 */

require_once __DIR__ . '/../Controller/BaseController.php';
require_once __DIR__ . '/../Model/User.php';
require_once __DIR__ . '/../Model/Course.php';
require_once __DIR__ . '/../Model/Enrollment.php';
require_once __DIR__ . '/../Model/Groupe.php';
require_once __DIR__ . '/../Model/Discussion.php';


class AdminController extends BaseController {

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

    private function countEvenements(): int {
        return (int) $this->getDb()->query("SELECT COUNT(*) FROM evenements")->fetchColumn();
    }

    private function getRecentEvenements(int $limit = 3): array {
        $st = $this->getDb()->prepare(
            "SELECT * FROM evenements
             ORDER BY COALESCE(CONCAT(date_debut,' ',heure_debut), event_date) ASC
             LIMIT ?"
        );
        $st->bindValue(1, $limit, PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll();
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

        $userModel       = $this->model('User');
        $courseModel     = $this->model('Course');
        $enrollmentModel = $this->model('Enrollment');
        $teacherAppModel = $this->model('TeacherApplication');

        $data = [
            'title'             => 'Admin Dashboard - APPOLIOS',
            'description'       => 'Administrator control panel',
            'totalUsers'        => $userModel->count(),
            'totalStudents'     => $userModel->countStudents(),
            'totalCourses'      => $courseModel->count(),
            'totalEnrollments'  => $enrollmentModel->countAll(),
            'totalEvenements'   => $this->countEvenements(),
            'recentCourses'     => $courseModel->getAllWithCreator(),
            'recentEvenements'  => $this->getRecentEvenements(3),
            'recentUsers'       => $userModel->getStudents(),
            'pendingTeacherApps'=> $teacherAppModel->countPending(),
            'flash'             => $this->getFlash()
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
     * Export users to PDF
     */
    public function exportUsersPDF() {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied.');
            $this->redirect('admin/login');
            return;
        }

        $userModel = $this->model('User');
        $users = $userModel->findAll();

        // Generate PDF using simple HTML output optimized for printing
        header('Content-Type: text/html; charset=utf-8');
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Users Export - APPOLIOS</title>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body {
                    font-family: 'Segoe UI', Arial, sans-serif;
                    font-size: 12px;
                    line-height: 1.5;
                    color: #333;
                    padding: 20px;
                }
                .header {
                    text-align: center;
                    margin-bottom: 30px;
                    padding-bottom: 20px;
                    border-bottom: 3px solid #548CA8;
                }
                .header h1 {
                    color: #2B4865;
                    font-size: 24px;
                    margin-bottom: 5px;
                }
                .header p {
                    color: #666;
                    font-size: 12px;
                }
                .info {
                    margin-bottom: 20px;
                    color: #666;
                    font-size: 11px;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 10px;
                }
                th {
                    background: #548CA8;
                    color: white;
                    padding: 10px 8px;
                    text-align: left;
                    font-weight: 600;
                    font-size: 11px;
                }
                td {
                    padding: 8px;
                    border-bottom: 1px solid #ddd;
                    font-size: 11px;
                }
                tr:nth-child(even) {
                    background: #f8f9fa;
                }
                .badge {
                    padding: 2px 8px;
                    border-radius: 12px;
                    font-size: 10px;
                    color: white;
                    display: inline-block;
                }
                .badge-admin { background: #E19864; }
                .badge-teacher { background: #548CA8; }
                .badge-student { background: #28a745; }
                .badge-blocked { background: #dc3545; }
                .footer {
                    margin-top: 30px;
                    text-align: center;
                    font-size: 10px;
                    color: #999;
                    border-top: 1px solid #ddd;
                    padding-top: 15px;
                }
                @media print {
                    body { padding: 0; }
                    .no-print { display: none; }
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>APPOLIOS - Users Report</h1>
                <p>Complete list of registered users</p>
            </div>

            <div class="info">
                <strong>Generated:</strong> <?= date('F d, Y H:i:s') ?><br>
                <strong>Total Users:</strong> <?= count($users) ?>
            </div>

            <div class="no-print" style="margin-bottom: 20px;">
                <button onclick="window.print()" style="padding: 10px 20px; background: #548CA8; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px;">
                    Print / Save as PDF
                </button>
                <a href="<?= APP_ENTRY ?>?url=admin/users" style="display: inline-block; padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; margin-left: 10px; text-decoration: none;">
                    Back to Users
                </a>
            </div>

            <table>
                <thead>
                    <tr>
                        <th style="width: 8%;">ID</th>
                        <th style="width: 20%;">Full Name</th>
                        <th style="width: 25%;">Email Address</th>
                        <th style="width: 12%;">Role</th>
                        <th style="width: 15%;">Status</th>
                        <th style="width: 20%;">Registered Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['id']) ?></td>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td>
                            <span class="badge badge-<?= $user['role'] ?>">
                                <?= ucfirst(htmlspecialchars($user['role'])) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($user['is_blocked'] ?? 0): ?>
                                <span class="badge badge-blocked">Blocked</span>
                            <?php else: ?>
                                <span style="color: #28a745;">Active</span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('M d, Y H:i', strtotime($user['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="footer">
                <p>APPOLIOS E-Learning Platform - User Management Report</p>
                <p>This document is confidential and intended for authorized personnel only.</p>
            </div>

            <script>
                // Auto-trigger print dialog when page loads
                window.onload = function() {
                    setTimeout(function() {
                        window.print();
                    }, 500);
                };
            </script>
        </body>
        </html>
        <?php
        exit;
    }

    /**
     * Block a user
     */
    public function blockUser($id) {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied.');
            $this->redirect('admin/login');
            return;
        }

        // Prevent blocking self
        if ((int) $id === (int) $_SESSION['user_id']) {
            $this->setFlash('error', 'You cannot block yourself.');
            $this->redirect('admin/users');
            return;
        }

        $userModel = $this->model('User');
        $user = $userModel->findById((int) $id);

        if (!$user) {
            $this->setFlash('error', 'User not found.');
            $this->redirect('admin/users');
            return;
        }

        if ($userModel->block((int) $id)) {
            $this->setFlash('success', 'User ' . htmlspecialchars($user['name']) . ' has been blocked successfully.');
        } else {
            $this->setFlash('error', 'Failed to block user.');
        }

        $this->redirect('admin/users');
    }

    /**
     * Unblock a user
     */
    public function unblockUser($id) {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied.');
            $this->redirect('admin/login');
            return;
        }

        $userModel = $this->model('User');
        $user = $userModel->findById((int) $id);

        if (!$user) {
            $this->setFlash('error', 'User not found.');
            $this->redirect('admin/users');
            return;
        }

        if ($userModel->unblock((int) $id)) {
            $this->setFlash('success', 'User ' . htmlspecialchars($user['name']) . ' has been unblocked successfully.');
        } else {
            $this->setFlash('error', 'Failed to unblock user.');
        }

        $this->redirect('admin/users');
    }

    /**
     * Contact Messages Inbox - List all messages
     */
    public function contactMessages() {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied. Admin privileges required.');
            $this->redirect('admin/login');
            return;
        }

        require_once __DIR__ . '/../Model/ContactMessage.php';
        $contactModel = $this->model('ContactMessage');

        $messages = $contactModel->getAllMessages(100, 0);
        $unreadCount = $contactModel->getUnreadCount();

        $data = [
            'title' => 'Contact Messages Inbox - APPOLIOS',
            'description' => 'View and manage contact us messages',
            'messages' => $messages,
            'unreadCount' => $unreadCount,
            'flash' => $this->getFlash()
        ];

        $this->view('BackOffice/admin/contact_messages', $data);
    }

    /**
     * View single contact message
     */
    public function viewContactMessage($id) {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied. Admin privileges required.');
            $this->redirect('admin/login');
            return;
        }

        require_once __DIR__ . '/../Model/ContactMessage.php';
        $contactModel = $this->model('ContactMessage');

        $message = $contactModel->getById((int) $id);

        if (!$message) {
            $this->setFlash('error', 'Message not found.');
            $this->redirect('admin/contact-messages');
            return;
        }

        // Auto-mark as read when viewing
        if (!$message['is_read']) {
            $contactModel->markAsRead((int) $id, (int) $_SESSION['user_id']);
            $message = $contactModel->getById((int) $id);
        }

        $data = [
            'title' => 'View Message - APPOLIOS',
            'description' => 'Contact message details',
            'message' => $message,
            'flash' => $this->getFlash()
        ];

        $this->view('BackOffice/admin/view_contact_message', $data);
    }

    /**
     * Mark message as unread
     */
    public function markMessageUnread($id) {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied.');
            $this->redirect('admin/login');
            return;
        }

        require_once __DIR__ . '/../Model/ContactMessage.php';
        $contactModel = $this->model('ContactMessage');

        if ($contactModel->markAsUnread((int) $id)) {
            $this->setFlash('success', 'Message marked as unread.');
        } else {
            $this->setFlash('error', 'Failed to mark message as unread.');
        }

        $this->redirect('admin/contact-messages');
    }

    /**
     * Delete contact message
     */
    public function deleteContactMessage($id) {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied.');
            $this->redirect('admin/login');
            return;
        }

        require_once __DIR__ . '/../Model/ContactMessage.php';
        $contactModel = $this->model('ContactMessage');

        $message = $contactModel->getById((int) $id);
        if (!$message) {
            $this->setFlash('error', 'Message not found.');
            $this->redirect('admin/contact-messages');
            return;
        }

        if ($contactModel->delete((int) $id)) {
            $this->setFlash('success', 'Message deleted successfully.');
        } else {
            $this->setFlash('error', 'Failed to delete message.');
        }

        $this->redirect('admin/contact-messages');
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
            $this->setFlash('error', 'Please fix the errors below.');
            $this->redirect('admin/edit-course/' . (int) $id);
            return;
        }

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
     * Export teachers to PDF
     */
    public function exportTeachersPDF() {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied.');
            $this->redirect('admin/login');
            return;
        }

        $userModel = $this->model('User');
        $teachers = $userModel->getTeachers();

        // Generate PDF using simple HTML output optimized for printing
        header('Content-Type: text/html; charset=utf-8');
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Teachers Export - APPOLIOS</title>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body {
                    font-family: 'Segoe UI', Arial, sans-serif;
                    font-size: 12px;
                    line-height: 1.5;
                    color: #333;
                    padding: 20px;
                }
                .header {
                    text-align: center;
                    margin-bottom: 30px;
                    padding-bottom: 20px;
                    border-bottom: 3px solid #548CA8;
                }
                .header h1 {
                    color: #2B4865;
                    font-size: 24px;
                    margin-bottom: 5px;
                }
                .header p {
                    color: #666;
                    font-size: 12px;
                }
                .info {
                    margin-bottom: 20px;
                    color: #666;
                    font-size: 11px;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 10px;
                }
                th {
                    background: #548CA8;
                    color: white;
                    padding: 10px 8px;
                    text-align: left;
                    font-weight: 600;
                    font-size: 11px;
                }
                td {
                    padding: 8px;
                    border-bottom: 1px solid #ddd;
                    font-size: 11px;
                }
                tr:nth-child(even) {
                    background: #f8f9fa;
                }
                .badge {
                    padding: 2px 8px;
                    border-radius: 12px;
                    font-size: 10px;
                    color: white;
                    display: inline-block;
                }
                .badge-teacher { background: #548CA8; }
                .badge-blocked { background: #dc3545; }
                .footer {
                    margin-top: 30px;
                    text-align: center;
                    font-size: 10px;
                    color: #999;
                    border-top: 1px solid #ddd;
                    padding-top: 15px;
                }
                @media print {
                    body { padding: 0; }
                    .no-print { display: none; }
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>APPOLIOS - Teachers Report</h1>
                <p>Complete list of registered teachers</p>
            </div>

            <div class="info">
                <strong>Generated:</strong> <?= date('F d, Y H:i:s') ?><br>
                <strong>Total Teachers:</strong> <?= count($teachers) ?>
            </div>

            <div class="no-print" style="margin-bottom: 20px;">
                <button onclick="window.print()" style="padding: 10px 20px; background: #548CA8; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px;">
                    Print / Save as PDF
                </button>
                <a href="<?= APP_ENTRY ?>?url=admin/teachers" style="display: inline-block; padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; margin-left: 10px; text-decoration: none;">
                    Back to Teachers
                </a>
            </div>

            <table>
                <thead>
                    <tr>
                        <th style="width: 8%;">ID</th>
                        <th style="width: 25%;">Full Name</th>
                        <th style="width: 30%;">Email Address</th>
                        <th style="width: 15%;">Status</th>
                        <th style="width: 22%;">Registered Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($teachers as $teacher): ?>
                    <tr>
                        <td><?= htmlspecialchars($teacher['id']) ?></td>
                        <td><?= htmlspecialchars($teacher['name']) ?></td>
                        <td><?= htmlspecialchars($teacher['email']) ?></td>
                        <td>
                            <span class="badge badge-teacher">Teacher</span>
                            <?php if ($teacher['is_blocked'] ?? 0): ?>
                                <span class="badge badge-blocked" style="margin-left: 5px;">Blocked</span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('M d, Y H:i', strtotime($teacher['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="footer">
                <p>APPOLIOS E-Learning Platform - Teachers Management Report</p>
                <p>This document is confidential and intended for authorized personnel only.</p>
            </div>

            <script>
                // Auto-trigger print dialog when page loads
                window.onload = function() {
                    setTimeout(function() {
                        window.print();
                    }, 500);
                };
            </script>
        </body>
        </html>
        <?php
        exit;
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
     * Teacher applications management page
     */
    public function teacherApplications() {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied. Admin privileges required.');
            $this->redirect('admin/login');
            return;
        }

        $teacherAppModel = $this->model('TeacherApplication');
        $userModel = $this->model('User');

        $data = [
            'title' => 'Teacher Applications - APPOLIOS',
            'description' => 'Manage teacher registration requests',
            'applications' => $teacherAppModel->getPendingApplications(),
            'pendingCount' => $teacherAppModel->countPending(),
            'pendingTeacherApps' => $teacherAppModel->countPending(),
            'adminSidebarActive' => 'teacher-applications',
            'flash' => $this->getFlash()
        ];

        $this->view('BackOffice/admin/teacher_applications', $data);
    }

    /**
     * Approve teacher application
     */
    public function approveTeacher() {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/teacher-applications');
            return;
        }

        $applicationId = (int) ($_POST['application_id'] ?? 0);
        $adminNotes = $this->sanitize($_POST['admin_notes'] ?? '');

        if ($applicationId <= 0) {
            $this->setFlash('error', 'Invalid application id.');
            $this->redirect('admin/teacher-applications');
            return;
        }

        $teacherAppModel = $this->model('TeacherApplication');
        $userModel = $this->model('User');

        // Get application details
        $application = $teacherAppModel->getById($applicationId);
        if (!$application) {
            $this->setFlash('error', 'Application not found.');
            $this->redirect('admin/teacher-applications');
            return;
        }

        // Create user account for teacher using the original password
        $userId = $userModel->create([
            'name' => $application['name'],
            'email' => $application['email'],
            'password' => $application['password'], // Plain password - will be hashed by create()
            'role' => 'teacher'
        ]);

        if ($userId) {
            // Update application status
            $teacherAppModel->approve($applicationId, (int) $_SESSION['user_id'], $adminNotes);
            $this->setFlash('success', 'Teacher application approved! The teacher can now login with their email and the password they registered with.');
        } else {
            $this->setFlash('error', 'Failed to create teacher account.');
        }

        $this->redirect('admin/teacher-applications');
    }

    /**
     * Reject teacher application
     */
    public function rejectTeacher() {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/teacher-applications');
            return;
        }

        $applicationId = (int) ($_POST['application_id'] ?? 0);
        $adminNotes = $this->sanitize($_POST['admin_notes'] ?? '');

        if ($applicationId <= 0) {
            $this->setFlash('error', 'Invalid application id.');
            $this->redirect('admin/teacher-applications');
            return;
        }

        if (empty($adminNotes)) {
            $this->setFlash('error', 'Rejection reason is required.');
            $this->redirect('admin/teacher-applications');
            return;
        }

        $teacherAppModel = $this->model('TeacherApplication');
        $application = $teacherAppModel->getById($applicationId);

        if (!$application) {
            $this->setFlash('error', 'Application not found.');
            $this->redirect('admin/teacher-applications');
            return;
        }

        // Delete CV file
        $cvPath = __DIR__ . '/../' . $application['cv_path'];
        if (file_exists($cvPath)) {
            unlink($cvPath);
        }

        // Update application status
        $result = $teacherAppModel->reject($applicationId, (int) $_SESSION['user_id'], $adminNotes);

        if ($result) {
            $this->setFlash('success', 'Teacher application rejected.');
        } else {
            $this->setFlash('error', 'Failed to reject application.');
        }

        $this->redirect('admin/teacher-applications');
    }

    public function slGroupes(...$params): void
    {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        $groupeModel = $this->model('Groupe');
        $first = $params[0] ?? null;
        $second = $params[1] ?? null;
        $id = is_numeric($first) ? (int) $first : 0;

        if ($first === 'create') {
            $this->view('BackOffice/admin/sl_groupes_create', [
                'title' => 'Create Group - APPOLIOS',
                'description' => 'Create social learning group',
                'adminSidebarActive' => 'sl-groupes',
                'old' => $_SESSION['sl_group_old'] ?? [],
                'errors' => $_SESSION['sl_group_errors'] ?? [],
                'flash' => $this->getFlash(),
            ]);
            unset($_SESSION['sl_group_old'], $_SESSION['sl_group_errors']);
            return;
        }
        if ($first === 'store' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim((string) ($_POST['nom_groupe'] ?? ''));
            $desc = trim((string) ($_POST['description'] ?? ''));
            $approval = trim((string) ($_POST['approval_statut'] ?? 'en_cours'));
            $errors = [];
            if ($nom === '') {
                $errors['nom_groupe'] = 'Group name should not be empty.';
            } elseif (strlen($nom) < 3 || strlen($nom) > 100) {
                $errors['nom_groupe'] = 'Group name must be between 3 and 100 characters.';
            }
            if ($desc === '') {
                $errors['description'] = 'Description should not be empty.';
            } elseif (strlen($desc) < 10 || strlen($desc) > 3000) {
                $errors['description'] = 'Description must be between 10 and 3000 characters.';
            }
            if (!in_array($approval, ['en_cours', 'approuve', 'rejete'], true)) {
                $errors['approval_statut'] = 'Invalid approval status.';
            }
            if (!empty($errors)) {
                $_SESSION['sl_group_old'] = $_POST;
                $_SESSION['sl_group_errors'] = $errors;
                $this->redirect('admin/sl-groupes/create');
                return;
            }

            $createData = [
                'nom_groupe' => htmlspecialchars($nom, ENT_QUOTES, 'UTF-8'),
                'description' => htmlspecialchars($desc, ENT_QUOTES, 'UTF-8'),
                'statut' => 'actif',
                'id_createur' => (int) $_SESSION['user_id'],
                'approval_statut' => $approval,
            ];
            $createdId = $groupeModel->create($createData);
            if ($createdId) {
                $groupeModel->ajouterMembre((int) $createdId, (int) $_SESSION['user_id'], 'admin');
                $this->setFlash('success', 'Group created successfully.');
                $this->redirect('admin/sl-groupes');
                return;
            }
            $this->setFlash('error', 'Failed to create group.');
            $this->redirect('admin/sl-groupes/create');
            return;
        }

        if ($id > 0 && $second === 'edit') {
            $row = $groupeModel->findById($id);
            if (!$row) {
                $this->setFlash('error', 'Group not found.');
                $this->redirect('admin/sl-groupes');
                return;
            }
            $this->view('BackOffice/admin/sl_groupes_edit', [
                'title' => 'Edit Group - APPOLIOS',
                'description' => 'Update group',
                'adminSidebarActive' => 'sl-groupes',
                'groupe' => $row,
                'old' => $_SESSION['sl_group_old'] ?? [],
                'errors' => $_SESSION['sl_group_errors'] ?? [],
                'flash' => $this->getFlash(),
            ]);
            unset($_SESSION['sl_group_old'], $_SESSION['sl_group_errors']);
            return;
        }

        if ($id > 0 && $second === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $row = $groupeModel->findById($id);
            if (!$row) {
                $this->setFlash('error', 'Group not found.');
                $this->redirect('admin/sl-groupes');
                return;
            }
            $nom = trim((string) ($_POST['nom_groupe'] ?? ''));
            $desc = trim((string) ($_POST['description'] ?? ''));
            $approval = trim((string) ($_POST['approval_statut'] ?? 'en_cours'));
            $errors = [];
            if ($nom === '') {
                $errors['nom_groupe'] = 'Group name should not be empty.';
            } elseif (strlen($nom) < 3 || strlen($nom) > 100) {
                $errors['nom_groupe'] = 'Group name must be between 3 and 100 characters.';
            }
            if ($desc === '') {
                $errors['description'] = 'Description should not be empty.';
            } elseif (strlen($desc) < 10 || strlen($desc) > 3000) {
                $errors['description'] = 'Description must be between 10 and 3000 characters.';
            }
            if (!in_array($approval, ['en_cours', 'approuve', 'rejete'], true)) {
                $errors['approval_statut'] = 'Invalid approval status.';
            }
            if (!empty($errors)) {
                $_SESSION['sl_group_old'] = $_POST;
                $_SESSION['sl_group_errors'] = $errors;
                $this->redirect('admin/sl-groupes/' . $id . '/edit');
                return;
            }

            $ok = $groupeModel->updateGroupe($id, [
                'nom_groupe' => htmlspecialchars($nom, ENT_QUOTES, 'UTF-8'),
                'description' => htmlspecialchars($desc, ENT_QUOTES, 'UTF-8'),
                'statut' => (string) ($row['statut'] ?? 'actif'),
                'approval_statut' => $approval,
            ]);
            $this->setFlash($ok ? 'success' : 'error', $ok ? 'Group updated successfully.' : 'Failed to update group.');
            $this->redirect('admin/sl-groupes');
            return;
        }

        if ($id > 0 && $second === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $discussionModel = $this->model('Discussion');
            $discussionModel->deleteAllForGroup($id);
            $groupeModel->deleteMembresForGroup($id);
            $ok = $groupeModel->delete($id);
            $this->setFlash($ok ? 'success' : 'error', $ok ? 'Group deleted.' : 'Failed to delete group.');
            $this->redirect('admin/sl-groupes');
            return;
        }

        if ($id > 0 && $second === 'approve' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $row = $groupeModel->findById($id);
            $ok = $row ? $groupeModel->updateGroupe($id, ['nom_groupe' => $row['nom_groupe'], 'description' => $row['description'], 'statut' => $row['statut'] ?? 'actif', 'approval_statut' => 'approuve']) : false;
            $this->setFlash($ok ? 'success' : 'error', $ok ? 'Groupe approuve.' : 'Approbation echouee.');
            $this->redirect('admin/sl-groupes');
            return;
        }
        if ($id > 0 && $second === 'reject' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $row = $groupeModel->findById($id);
            $ok = $row ? $groupeModel->updateGroupe($id, ['nom_groupe' => $row['nom_groupe'], 'description' => $row['description'], 'statut' => $row['statut'] ?? 'actif', 'approval_statut' => 'rejete']) : false;
            $this->setFlash($ok ? 'success' : 'error', $ok ? 'Groupe rejete.' : 'Rejet echoue.');
            $this->redirect('admin/sl-groupes');
            return;
        }

        $this->view('BackOffice/admin/sl_groupes', [
            'title' => 'Admin Groupes - APPOLIOS',
            'description' => 'Validation des groupes',
            'groupes' => $groupeModel->getAllWithCreator(300, 0),
            'adminSidebarActive' => 'sl-groupes',
            'flash' => $this->getFlash(),
        ]);
    }

    public function slDiscussions(...$params): void
    {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        $discussionModel = $this->model('Discussion');
        $groupeModel = $this->model('Groupe');
        $first = $params[0] ?? null;
        $second = $params[1] ?? null;
        $id = is_numeric($first) ? (int) $first : 0;

        if ($first === 'create') {
            $groups = array_values(array_filter(
                $groupeModel->getAllWithCreator(500, 0),
                static fn(array $g): bool => (string) ($g['approval_statut'] ?? '') === 'approuve'
            ));
            $this->view('BackOffice/admin/sl_discussions_create', [
                'title' => 'Create Discussion - APPOLIOS',
                'description' => 'Create discussion in approved group',
                'adminSidebarActive' => 'sl-discussions',
                'groups' => $groups,
                'old' => $_SESSION['sl_disc_old'] ?? [],
                'errors' => $_SESSION['sl_disc_errors'] ?? [],
                'flash' => $this->getFlash(),
            ]);
            unset($_SESSION['sl_disc_old'], $_SESSION['sl_disc_errors']);
            return;
        }
        if ($first === 'store' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = trim((string) ($_POST['titre'] ?? ''));
            $contenu = trim((string) ($_POST['contenu'] ?? ''));
            $groupId = (int) ($_POST['id_groupe'] ?? 0);
            $approval = trim((string) ($_POST['approval_statut'] ?? 'en_cours'));
            $errors = [];
            if ($groupId <= 0) {
                $errors['id_groupe'] = 'Group is required.';
            }
            if ($titre === '') {
                $errors['titre'] = 'Discussion title should not be empty.';
            } elseif (strlen($titre) < 3 || strlen($titre) > 200) {
                $errors['titre'] = 'Title must be between 3 and 200 characters.';
            }
            if ($contenu === '') {
                $errors['contenu'] = 'Discussion content should not be empty.';
            } elseif (strlen($contenu) < 5 || strlen($contenu) > 5000) {
                $errors['contenu'] = 'Content must be between 5 and 5000 characters.';
            }
            if (!in_array($approval, ['en_cours', 'approuve', 'rejete'], true)) {
                $errors['approval_statut'] = 'Invalid approval status.';
            }
            if (!empty($errors)) {
                $_SESSION['sl_disc_old'] = $_POST;
                $_SESSION['sl_disc_errors'] = $errors;
                $this->redirect('admin/sl-discussions/create');
                return;
            }

            $ok = $discussionModel->createForGroup(
                $groupId,
                (int) $_SESSION['user_id'],
                htmlspecialchars($titre, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($contenu, ENT_QUOTES, 'UTF-8')
            );
            if ($ok && $approval !== 'en_cours') {
                $inserted = $discussionModel->getByAuthor((int) $_SESSION['user_id']);
                if (!empty($inserted)) {
                    $latestId = (int) ($inserted[0]['id_discussion'] ?? $inserted[0]['id'] ?? 0);
                    if ($latestId > 0) {
                        $discussionModel->setApprovalStatus($latestId, $approval);
                    }
                }
            }
            $this->setFlash($ok ? 'success' : 'error', $ok ? 'Discussion created successfully.' : 'Failed to create discussion.');
            $this->redirect('admin/sl-discussions');
            return;
        }

        if ($id > 0 && $second === 'edit') {
            $discussion = $discussionModel->getRowByPk($id);
            if (!$discussion) {
                $this->setFlash('error', 'Discussion not found.');
                $this->redirect('admin/sl-discussions');
                return;
            }
            $groups = array_values(array_filter(
                $groupeModel->getAllWithCreator(500, 0),
                static fn(array $g): bool => (string) ($g['approval_statut'] ?? '') === 'approuve'
            ));
            $this->view('BackOffice/admin/sl_discussions_edit', [
                'title' => 'Edit Discussion - APPOLIOS',
                'description' => 'Update discussion',
                'adminSidebarActive' => 'sl-discussions',
                'discussion' => $discussion,
                'groups' => $groups,
                'old' => $_SESSION['sl_disc_old'] ?? [],
                'errors' => $_SESSION['sl_disc_errors'] ?? [],
                'flash' => $this->getFlash(),
            ]);
            unset($_SESSION['sl_disc_old'], $_SESSION['sl_disc_errors']);
            return;
        }

        if ($id > 0 && $second === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $discussion = $discussionModel->getRowByPk($id);
            if (!$discussion) {
                $this->setFlash('error', 'Discussion not found.');
                $this->redirect('admin/sl-discussions');
                return;
            }
            $titre = trim((string) ($_POST['titre'] ?? ''));
            $contenu = trim((string) ($_POST['contenu'] ?? ''));
            $groupId = (int) ($_POST['id_groupe'] ?? 0);
            $approval = trim((string) ($_POST['approval_statut'] ?? 'en_cours'));
            $errors = [];
            if ($groupId <= 0) {
                $errors['id_groupe'] = 'Group is required.';
            }
            if ($titre === '') {
                $errors['titre'] = 'Discussion title should not be empty.';
            } elseif (strlen($titre) < 3 || strlen($titre) > 200) {
                $errors['titre'] = 'Title must be between 3 and 200 characters.';
            }
            if ($contenu === '') {
                $errors['contenu'] = 'Discussion content should not be empty.';
            } elseif (strlen($contenu) < 5 || strlen($contenu) > 5000) {
                $errors['contenu'] = 'Content must be between 5 and 5000 characters.';
            }
            if (!in_array($approval, ['en_cours', 'approuve', 'rejete'], true)) {
                $errors['approval_statut'] = 'Invalid approval status.';
            }
            if (!empty($errors)) {
                $_SESSION['sl_disc_old'] = $_POST;
                $_SESSION['sl_disc_errors'] = $errors;
                $this->redirect('admin/sl-discussions/' . $id . '/edit');
                return;
            }

            $authorId = (int) ($discussion['id_auteur'] ?? $discussion['created_by'] ?? 0);
            $ok = $discussionModel->updateOwned(
                $id,
                $authorId,
                htmlspecialchars($titre, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($contenu, ENT_QUOTES, 'UTF-8'),
                $groupId
            );
            if ($ok) {
                $discussionModel->setApprovalStatus($id, $approval);
            }
            $this->setFlash($ok ? 'success' : 'error', $ok ? 'Discussion updated successfully.' : 'Failed to update discussion.');
            $this->redirect('admin/sl-discussions');
            return;
        }

        if ($id > 0 && $second === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $ok = $discussionModel->deleteByPrimaryKey($id);
            $this->setFlash($ok ? 'success' : 'error', $ok ? 'Discussion deleted.' : 'Failed to delete discussion.');
            $this->redirect('admin/sl-discussions');
            return;
        }

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
        if ($id > 0 && $second === 'chat') {
            $discussion = $discussionModel->getRowByPk($id);
            if (!$discussion) {
                $this->setFlash('error', 'Discussion not found.');
                $this->redirect('admin/sl-discussions');
                return;
            }
            $approval = (string) ($discussion['approval_statut'] ?? $discussion['approval_status'] ?? 'en_cours');
            if ($approval !== 'approuve') {
                $this->setFlash('error', 'Live chat is available only for approved discussions.');
                $this->redirect('admin/sl-discussions');
                return;
            }
            $this->view('BackOffice/admin/sl_discussions_chat', [
                'title' => 'Admin Live Chat - APPOLIOS',
                'description' => 'Live discussion room',
                'discussion' => $discussion,
                'socketUrl' => SOCKET_IO_URL,
                'chatRoom' => 'discussion_' . $id,
                'currentUserId' => (int) ($_SESSION['user_id'] ?? 0),
                'currentUserName' => (string) ($_SESSION['user_name'] ?? 'Admin'),
                'adminSidebarActive' => 'sl-discussions',
                'flash' => $this->getFlash(),
            ]);
            return;
        }
        if ($id > 0 && $second === 'upload' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->adminDiscussionUploadAttachment($discussionModel, $id);
            return;
        }

        $this->view('BackOffice/admin/sl_discussions', [
            'title' => 'Admin Discussions - APPOLIOS',
            'description' => 'Validation des discussions',
            'discussions' => $discussionModel->getAllForAdmin(400),
            'adminSidebarActive' => 'sl-discussions',
            'flash' => $this->getFlash(),
        ]);
    }

    private function adminDiscussionUploadAttachment($discussionModel, int $discussionId): void
    {
        $discussion = $discussionModel->getRowByPk($discussionId);
        if (!$discussion) {
            $this->jsonResponse(['ok' => false, 'error' => 'Discussion not found.'], 404);
        }
        $approval = (string) ($discussion['approval_statut'] ?? $discussion['approval_status'] ?? 'en_cours');
        if ($approval !== 'approuve') {
            $this->jsonResponse(['ok' => false, 'error' => 'Discussion must be approved first.'], 403);
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
}