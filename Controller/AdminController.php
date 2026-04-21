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
        $teacherAppModel = $this->model('TeacherApplication');

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
            'pendingTeacherApps' => $teacherAppModel->countPending(),
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
}