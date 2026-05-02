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
require_once __DIR__ . '/../Model/Lesson.php';

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
            'statusDistribution' => $courseModel->getStatusDistribution(),
            'monthlyCourseStats' => $courseModel->getMonthlyCourseStats(),
            'recentCourses' => $courseModel->getAllWithCreator(),
            'recentEvenements' => $evenementModel->getRecent(3),
            'recentUsers' => $userModel->getStudents(),
            'pendingTeacherApps' => $teacherAppModel->countPending(),
            'pendingCourses' => $courseModel->countByStatus('pending'),
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
     * Manage categories page
     */
    public function categories() {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied. Admin privileges required.');
            $this->redirect('admin/login');
            return;
        }

        $categoryModel = $this->model('Category');
        $categories = $categoryModel->getAll();

        $data = [
            'title' => 'Manage Categories - APPOLIOS',
            'description' => 'Category management panel',
            'categories' => $categories,
            'flash' => $this->getFlash()
        ];

        $this->view('BackOffice/admin/categories', $data);
    }

    /**
     * Add new category
     */
    public function storeCategory() {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied.');
            $this->redirect('admin/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/categories');
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $icon = $_POST['icon'] ?? 'folder';
        $types = trim($_POST['types'] ?? '');

        if (empty($name)) {
            $this->setFlash('error', 'Category name is required');
            $this->redirect('admin/categories');
            return;
        }

        $categoryModel = $this->model('Category');
        $result = $categoryModel->create([
            'name' => $name,
            'description' => $description,
            'icon' => $icon,
            'types' => $types
        ]);

        if ($result) {
            $this->setFlash('success', 'Category created successfully!');
        } else {
            $this->setFlash('error', 'Failed to create category. Name may already exist.');
        }

        $this->redirect('admin/categories');
    }

    /**
     * Delete category
     */
    public function deleteCategory($id) {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied.');
            $this->redirect('admin/login');
            return;
        }

        $categoryModel = $this->model('Category');
        $categoryModel->delete($id);

        $this->setFlash('success', 'Category deleted successfully!');
        $this->redirect('admin/categories');
    }

    /**
     * Student progress page
     */
    public function studentProgress() {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied. Admin privileges required.');
            $this->redirect('admin/login');
            return;
        }

        $courseModel = $this->model('Course');
        $enrollmentModel = $this->model('Enrollment');
        
        // Get all courses
        $courses = $courseModel->findAll();
        
        // Get all enrollments with student and course info
        $db = $this->model('Course')->getDb();
        $sql = "SELECT e.*, u.name as student_name, u.email, c.title as course_title,
                (SELECT COUNT(*) FROM lessons l JOIN chapters ch ON l.chapter_id = ch.id WHERE ch.course_id = c.id) as total_lessons,
                (SELECT COUNT(*) FROM lesson_completions lc WHERE lc.user_id = e.user_id AND lc.course_id = c.id) as completed_lessons
                FROM enrollments e
                JOIN users u ON e.user_id = u.id
                JOIN courses c ON e.course_id = c.id
                ORDER BY e.enrolled_at DESC";
        $stmt = $db->query($sql);
        $enrollments = $stmt->fetchAll();

        $data = [
            'title' => 'Student Progress - APPOLIOS',
            'description' => 'Track student enrollments and progress',
            'courses' => $courses,
            'enrollments' => $enrollments,
            'flash' => $this->getFlash()
        ];

        $this->view('BackOffice/admin/student_progress', $data);
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
     * Manage courses page (approved courses only)
     */
    public function courses() {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied. Admin privileges required.');
            $this->redirect('admin/login');
            return;
        }

        $status = $_GET['status'] ?? 'all';
        
        $courseModel = $this->model('Course');
        
        if ($status === 'all') {
            $courses = $courseModel->getAllWithCreator();
        } else {
            $courses = $courseModel->getByStatus($status);
        }
        
        $pendingCount = $courseModel->countByStatus('pending');
        $approvedCount = $courseModel->countByStatus('approved');
        $rejectedCount = $courseModel->countByStatus('rejected');
        $totalEarnings = $courseModel->getTotalEarnings();
        $earningsByStatus = $courseModel->getEarningsByStatus();

        $data = [
            'title' => 'Manage Courses - APPOLIOS',
            'description' => 'Course management panel',
            'courses' => $courses,
            'filterStatus' => $status,
            'totalCourses' => count($courses),
            'pendingCount' => $pendingCount,
            'approvedCount' => $approvedCount,
            'rejectedCount' => $rejectedCount,
            'totalEarnings' => $totalEarnings,
            'earningsByStatus' => $earningsByStatus,
            'flash' => $this->getFlash()
        ];

        $this->view('BackOffice/admin/manage_courses', $data);
    }

    /**
     * Manage courses page (alias for courses)
     */
    public function manageCourses() {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied. Admin privileges required.');
            $this->redirect('admin/login');
            return;
        }

        $status = $_GET['status'] ?? 'approved';

        $courseModel = $this->model('Course');

        if ($status === 'all') {
            $courses = $courseModel->getAllWithCreator();
        } else {
            $courses = $courseModel->getByStatus($status);
        }
        
        $pendingCount = $courseModel->countByStatus('pending');
        $approvedCount = $courseModel->countByStatus('approved');
        $rejectedCount = $courseModel->countByStatus('rejected');
        $totalEarnings = $courseModel->getTotalEarnings();
        $earningsByStatus = $courseModel->getEarningsByStatus();

        $data = [
            'title' => 'Manage Courses - APPOLIOS',
            'description' => 'Course management panel',
            'courses' => $courses,
            'filterStatus' => $status,
            'totalCourses' => count($courses),
            'pendingCount' => $pendingCount,
            'approvedCount' => $approvedCount,
            'rejectedCount' => $rejectedCount,
            'totalEarnings' => $totalEarnings,
            'earningsByStatus' => $earningsByStatus,
            'flash' => $this->getFlash()
        ];

        $this->view('BackOffice/admin/manage_courses', $data);
    }

    /**
     * Course requests page (all courses with status)
     */
    public function courseRequests() {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied. Admin privileges required.');
            $this->redirect('admin/login');
            return;
        }

        $status = $_GET['status'] ?? 'all';
        
        $courseModel = $this->model('Course');
        $adminId = $_SESSION['user_id'] ?? 0;
        
        if ($status === 'all') {
            $courses = $courseModel->getAllWithCreator();
            // Filter out admin's own courses
            $courses = array_filter($courses, function($c) use ($adminId) {
                return $c['created_by'] != $adminId;
            });
        } else {
            $courses = $courseModel->getByStatus($status);
            // Filter out admin's own courses
            $courses = array_filter($courses, function($c) use ($adminId) {
                return $c['created_by'] != $adminId;
            });
        }
        
        $pendingCount = $courseModel->countByStatus('pending');
        $approvedCount = $courseModel->countByStatus('approved');
        $rejectedCount = $courseModel->countByStatus('rejected');

        $data = [
            'title' => 'Course Requests - APPOLIOS',
            'description' => 'Review course submissions',
            'courses' => $courses,
            'filterStatus' => $status,
            'pendingCount' => $pendingCount,
            'approvedCount' => $approvedCount,
            'rejectedCount' => $rejectedCount,
            'flash' => $this->getFlash()
        ];

        $this->view('BackOffice/admin/course_requests', $data);
    }

    /**
     * Approve or reject a course
     */
    public function approveCourse() {
        if (!$this->isAdmin()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            return;
        }

        $courseId = (int) ($_POST['course_id'] ?? 0);
        $action = $_POST['action'] ?? '';
        $adminMessage = $this->sanitize($_POST['admin_message'] ?? '');

        if (!$courseId || !in_array($action, ['approve', 'reject'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            return;
        }

        $status = $action === 'approve' ? 'approved' : 'rejected';
        
        $courseModel = $this->model('Course');
        $success = $courseModel->updateStatus($courseId, $status, $adminMessage);

        header('Content-Type: application/json');
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Course ' . $status . ' successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update course']);
        }
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
            'errors' => $_SESSION['errors'] ?? [],
            'old' => $_SESSION['old'] ?? [],
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
            $this->redirect('admin/add-course');
            return;
        }

        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $image = $_POST['image'] ?? '';
        $courseType = $_POST['course_type'] ?? '';
        $categoryId = $_POST['category_id'] ?? null;
        $price = $_POST['price'] ?? 0.0;
        
        // Validate price
        if (!is_numeric($price) || $price < 0) {
            $_SESSION['errors'] = ['price' => 'Invalid price. Must be a non-negative number.'];
            $_SESSION['old'] = $_POST;
            $this->redirect('admin/add-course');
            return;
        }
        
        // Handle course image upload
        if (isset($_FILES['course_image']) && !empty($_FILES['course_image']['tmp_name'])) {
            $uploadDir = 'C:/xampp/htdocs/Appolios-feature-user/Appolios-feature-user/uploads/images/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $filename = time() . '_' . basename($_FILES['course_image']['name']);
            if (move_uploaded_file($_FILES['course_image']['tmp_name'], $uploadDir . $filename)) {
                $image = 'uploads/images/' . $filename;
            }
        }
        
        // AI Image Generation (if no image provided)
        if (empty($image)) {
            require_once __DIR__ . '/../Service/ImageGenerator.php';
            $imageGen = new ImageGenerator();
            $categoryName = '';
            if (!empty($categoryId)) {
                $categoryModel = $this->model('Category');
                $cat = $categoryModel->getById($categoryId);
                $categoryName = $cat['name'] ?? '';
            }
            $generatedImage = $imageGen->generateAIPrompt($title, $categoryName);
            if ($generatedImage) {
                $image = $generatedImage;
            }
        }

        if (empty($title)) {
            $_SESSION['errors'] = ['title' => 'Course title is required'];
            $_SESSION['old'] = $_POST;
            $this->redirect('admin/add-course');
            return;
        }

        if (empty($description)) {
            $_SESSION['errors'] = ['description' => 'Course description is required'];
            $_SESSION['old'] = $_POST;
            $this->redirect('admin/add-course');
            return;
        }

        $courseModel = $this->model('Course');
        $courseId = $courseModel->create([
            'title' => $title,
            'description' => $description,
            'status' => 'approved',
            'image' => $image,
            'course_type' => $courseType,
            'category_id' => $categoryId ?: null,
            'price' => $price,
            'created_by' => $_SESSION['user_id']
        ]);

        if ($courseId) {
            $this->saveCourseChapters($courseId, $_POST);
            $this->saveCourseBadges($courseId, $_POST);
            $this->setFlash('success', 'Course created successfully!');
        } else {
            $this->setFlash('error', 'Failed to create course');
        }
        
        $this->redirect('admin/manage-courses');
    }

    /**
     * Save chapters and lessons for a course
     */
    private function saveCourseChapters($courseId, $postData) {
        $chapterModel = $this->model('Chapter');
        $lessonModel = $this->model('Lesson');

        $this->deleteCourseChapters($courseId);

        $chapters = $postData['chapters'] ?? [];

        foreach ($chapters as $chapterIndex => $chapterData) {
            $chapterTitle = $this->sanitize($chapterData['title'] ?? '');
            if (empty($chapterTitle)) continue;

            $chapterId = $chapterModel->create([
                'course_id' => $courseId,
                'title' => $chapterTitle,
                'description' => $this->sanitize($chapterData['description'] ?? ''),
                'chapter_order' => $chapterIndex + 1
            ]);

            if (!$chapterId) continue;

            $lessons = $chapterData['lessons'] ?? [];
            $lessonOrder = 1;

            foreach ($lessons as $lessonIndex => $lessonData) {
                $lessonTitle = $this->sanitize($lessonData['title'] ?? '');
                if (empty($lessonTitle)) continue;

                $lessonType = $lessonData['lesson_type'] ?? 'text';
                $pdfPath = $lessonData['pdf_path'] ?? '';
                $videoUrl = $this->sanitize($lessonData['video_url'] ?? '');

                // Handle PDF upload
                $fileName = $_FILES['chapters']['name'][$chapterIndex]['lessons'][$lessonIndex]['pdf_file'] ?? '';
                $fileTmpName = $_FILES['chapters']['tmp_name'][$chapterIndex]['lessons'][$lessonIndex]['pdf_file'] ?? '';
                $fileError = $_FILES['chapters']['error'][$chapterIndex]['lessons'][$lessonIndex]['pdf_file'] ?? 4;
                
                if (!empty($fileName) && $fileError === 0 && !empty($fileTmpName)) {
                    $uploadDir = __DIR__ . '/../View/assets/pdfs/';
                    @mkdir($uploadDir, 0777, true);
                    $newFilename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($fileName));
                    $targetPath = $uploadDir . $newFilename;

                    if (move_uploaded_file($fileTmpName, $targetPath)) {
                        $pdfPath = 'View/assets/pdfs/' . $newFilename;
                    }
                }

                if (empty($pdfPath) && !empty($lessonData['pdf_path'])) {
                    $pdfPath = $lessonData['pdf_path'];
                }

                if ($lessonType === 'video') {
                    $videoUrl = $this->sanitize($lessonData['video_url'] ?? '');
                }

                $lessonId = $lessonModel->createLesson([
                    'chapter_id' => $chapterId,
                    'title' => $lessonTitle,
                    'content' => $this->sanitize($lessonData['content'] ?? ''),
                    'video_url' => $videoUrl,
                    'pdf_path' => $pdfPath,
                    'lesson_type' => $lessonType,
                    'lesson_order' => $lessonOrder++,
                    'duration' => (int)($lessonData['duration'] ?? 0)
                ]);
            }
        }
    }

    /**
     * Delete all chapters and lessons for a course
     */
    private function deleteCourseChapters($courseId) {
        $chapterModel = $this->model('Chapter');
        $chapters = $chapterModel->getByCourseId($courseId);

        foreach ($chapters as $chapter) {
            $chapterModel->delete($chapter['id']);
        }
    }

    /**
     * Save course badges
     */
    private function saveCourseBadges($courseId, $postData) {
        $badgeModel = $this->model('CourseBadge');

        $badges = $postData['badges'] ?? [];
        foreach ($badges as $badgeData) {
            $badgeName = $this->sanitize($badgeData['badge_name'] ?? '');
            if (empty($badgeName)) continue;

            $badgeModel->create([
                'course_id' => $courseId,
                'badge_name' => $badgeName,
                'badge_icon' => $badgeData['badge_icon'] ?? 'trophy',
                'badge_condition' => $badgeData['badge_condition'] ?? 'completion',
                'description' => $badgeData['description'] ?? ''
            ]);
        }
    }

    /**
     * Get course content for modal (chapters & lessons)
     */
    public function getCourseContent($id) {
        if (!$this->isAdmin()) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Access denied']);
            return;
        }

        $courseModel = $this->model('Course');
        $course = $courseModel->getWithChapters($id);

        if (!$course) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Course not found']);
            return;
        }

        header('Content-Type: text/html');
        ?>
        <div style="margin-bottom: 1.5rem;">
            <h3 style="margin: 0 0 0.5rem; color: #1e293b;"><?= htmlspecialchars($course['title']) ?></h3>
            <p style="margin: 0; color: #64748b;"><?= htmlspecialchars($course['description'] ?? 'No description') ?></p>
            <?php if (!empty($course['course_type'])): ?>
                <span style="background: #dbeafe; color: #1e40af; padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 600; display: inline-block; margin-top: 0.5rem;">
                    <?= htmlspecialchars($course['course_type']) ?>
                </span>
            <?php endif; ?>
        </div>

        <?php if (!empty($course['chapters'])): ?>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <?php foreach ($course['chapters'] as $chapterIndex => $chapter): ?>
                    <div style="background: #f8fafc; border-radius: 12px; padding: 1rem; border: 1px solid #e2e8f0;">
                        <h4 style="margin: 0 0 0.5rem; color: #1e293b; font-size: 1rem;">Chapter <?= $chapterIndex + 1 ?>: <?= htmlspecialchars($chapter['title']) ?></h4>
                        <?php if (!empty($chapter['description'])): ?>
                            <p style="margin: 0 0 0.75rem; color: #64748b; font-size: 0.85rem;"><?= htmlspecialchars($chapter['description']) ?></p>
                        <?php endif; ?>

                        <?php if (!empty($chapter['lessons'])): ?>
                            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                <?php foreach ($chapter['lessons'] as $lessonIndex => $lesson): ?>
                                    <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; background: white; border-radius: 8px;">
                                        <div style="width: 32px; height: 32px; background: <?= $lesson['lesson_type'] === 'video' ? '#eff6ff' : ($lesson['lesson_type'] === 'pdf' ? '#fef3c7' : '#f0fdf4') ?>; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                            <?php if ($lesson['lesson_type'] === 'video'): ?>
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                                            <?php elseif ($lesson['lesson_type'] === 'pdf'): ?>
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                            <?php else: ?>
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                                            <?php endif; ?>
                                        </div>
                                        <div style="flex: 1;">
                                            <strong style="font-size: 0.9rem; color: #1e293b;"><?= htmlspecialchars($lesson['title']) ?></strong>
                                            <span style="font-size: 0.75rem; color: #64748b; margin-left: 0.5rem;"><?= ucfirst($lesson['lesson_type']) ?></span>
                                        </div>
                                        <?php if (!empty($lesson['pdf_path'])): ?>
                                            <a href="<?= APP_ENTRY ?>../<?= htmlspecialchars($lesson['pdf_path']) ?>" target="_blank" style="background: #dc2626; color: white; padding: 4px 8px; border-radius: 6px; text-decoration: none; font-size: 0.75rem; font-weight: 600;">PDF</a>
                                        <?php endif; ?>
                                        <?php if (!empty($lesson['video_url'])): ?>
                                            <a href="<?= htmlspecialchars($lesson['video_url']) ?>" target="_blank" style="background: #3b82f6; color: white; padding: 4px 8px; border-radius: 6px; text-decoration: none; font-size: 0.75rem; font-weight: 600;">Video</a>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p style="color: #94a3b8; font-size: 0.85rem;">No lessons in this chapter</p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="color: #64748b; text-align: center;">No chapters or lessons yet</p>
        <?php endif; ?>
        <?php
        exit;
    }

    /**
     * View course details
     */
    public function viewCourse($id) {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        $courseModel = $this->model('Course');
        $course = $courseModel->getWithChapters($id);

        if (!$course) {
            $this->setFlash('error', 'Course not found');
            $this->redirect('admin/manage-courses');
            return;
        }

        $data = [
            'title' => $course['title'] . ' - APPOLIOS',
            'course' => $course,
            'flash' => $this->getFlash()
        ];

        $this->view('BackOffice/admin/view_course', $data);
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
        $course = $courseModel->getWithChapters($id);

        // Check if course belongs to this admin
        if (!$course || $course['created_by'] != $_SESSION['user_id']) {
            $this->setFlash('error', 'Course not found or access denied');
            $this->redirect('admin/manage-courses');
            return;
        }

        $data = [
            'title' => 'Edit Course - APPOLIOS',
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
            $this->redirect('admin/manage-courses');
            return;
        }

        $courseModel = $this->model('Course');
        $course = $courseModel->findById($id);

        // Check if course belongs to this admin
        if (!$course || $course['created_by'] != $_SESSION['user_id']) {
            $this->setFlash('error', 'Course not found or access denied');
            $this->redirect('admin/manage-courses');
            return;
        }

        $title = $this->sanitize($_POST['title'] ?? '');
        $description = $this->sanitize($_POST['description'] ?? '');
        $status = $_POST['status'] ?? $course['status'] ?? 'approved';
        $image = $this->sanitize($_POST['image'] ?? '');
        $categoryId = $_POST['category_id'] ?? null;
        $price = $_POST['price'] ?? 0.0;
        
        // Handle course image upload
        if (isset($_FILES['course_image']) && !empty($_FILES['course_image']['tmp_name'])) {
            $uploadDir = 'C:/xampp/htdocs/Appolios-feature-user/Appolios-feature-user/uploads/images/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $filename = time() . '_' . basename($_FILES['course_image']['name']);
            if (move_uploaded_file($_FILES['course_image']['tmp_name'], $uploadDir . $filename)) {
                $image = 'uploads/images/' . $filename;
            }
        }
        
        $result = $courseModel->update($id, [
            'title' => $title,
            'description' => $description,
            'image' => $image,
            'category_id' => $categoryId ?: null,
            'course_type' => $this->sanitize($_POST['course_type'] ?? ''),
            'status' => $status,
            'price' => (float)$price
        ]);
        
        if ($result) {
            $this->saveCourseChapters($id, $_POST);
            $this->setFlash('success', 'Course updated successfully!');
        } else {
            $this->setFlash('error', 'Failed to update course.');
        }
        
        $this->redirect('admin/manage-courses');
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
        $course = $courseModel->findById($id);

        // Check if course belongs to this admin
        if (!$course || $course['created_by'] != $_SESSION['user_id']) {
            $this->setFlash('error', 'Course not found or access denied');
            $this->redirect('admin/manage-courses');
            return;
        }

        if ($courseModel->delete($id)) {
            $this->setFlash('success', 'Course deleted successfully!');
        } else {
            $this->setFlash('error', 'Failed to delete course.');
        }

        $this->redirect('admin/manage-courses');
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