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
require_once __DIR__ . '/AvatarGenerator.php';
require_once __DIR__ . '/CollabHubDelegate.php';
require_once __DIR__ . '/MailService.php';

class TeacherController extends BaseController {
    use ActivityLogger;

    /**
     * Route alias for /teacher/courses
     */
    public function courses() {
        $this->myCourses();
    }

    public function groupes(...$params): void
    {
        $this->requireTeacher();
        CollabHubDelegate::runGroupes($this, 'teacher', $params);
    }

    public function discussions(...$params): void
    {
        $this->requireTeacher();
        CollabHubDelegate::runDiscussions($this, 'teacher', $params);
    }

    public function slGroupes(): void
    {
        $this->requireTeacher();
        $this->redirect('teacher/groupes');
    }

    public function slDiscussions(): void
    {
        $this->requireTeacher();
        $this->redirect('teacher/discussions');
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

        $teacherId = $_SESSION['user_id'];

        $myCourses = $courseModel->getCoursesByTeacher($teacherId);
        $courseIds = array_column($myCourses, 'id');

        $totalEarnings = 0;
        $totalReviews = 0;
        $totalRating = 0;
        $pendingCourses = 0;

        foreach ($myCourses as $course) {
            if ($course['status'] === 'pending') {
                $pendingCourses++;
            }
        }

        if (!empty($courseIds)) {
            $placeholders = implode(',', array_fill(0, count($courseIds), '?'));
            $db = $this->getDb();

            $stmt = $db->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE course_id IN ($placeholders) AND status IN ('succeeded', 'completed')");
            $stmt->execute($courseIds);
            $totalEarnings = (float) ($stmt->fetch()['total'] ?? 0);

            $stmt2 = $db->prepare("SELECT COUNT(*) as cnt FROM course_reviews WHERE course_id IN ($placeholders)");
            $stmt2->execute($courseIds);
            $totalReviews = (int) ($stmt2->fetch()['cnt'] ?? 0);

            $stmt3 = $db->prepare("SELECT AVG(rating) as avg FROM course_reviews WHERE course_id IN ($placeholders)");
            $stmt3->execute($courseIds);
            $totalRating = (float) ($stmt3->fetch()['avg'] ?? 0);
        }

        $stats = [
            'total_courses' => count($myCourses),
            'total_students' => $courseModel->countStudentsByTeacher($teacherId),
            'total_earnings' => $totalEarnings,
            'avg_rating' => round($totalRating, 1),
            'total_reviews' => $totalReviews,
            'pending_courses' => $pendingCourses
        ];

        $range = $_GET['range'] ?? 'year';
        $monthlyEarnings = [];
        $coursePerformance = [];

        if (!empty($courseIds)) {
            $placeholders = implode(',', array_fill(0, count($courseIds), '?'));
            $db = $this->getDb();

            $coursePerfStmt = $db->prepare("
                SELECT c.id, c.title,
                    COUNT(e.id) as students,
                    COALESCE((SELECT SUM(p.amount) FROM payments p WHERE p.course_id = c.id AND p.status IN ('succeeded', 'completed')), 0) as earnings,
                    AVG(r.rating) as avg_rating
                FROM courses c
                LEFT JOIN enrollments e ON c.id = e.course_id
                LEFT JOIN course_reviews r ON c.id = r.course_id
                WHERE c.id IN ($placeholders)
                GROUP BY c.id, c.title
                ORDER BY earnings DESC
                LIMIT 5
            ");
            $coursePerfStmt->execute($courseIds);
            $coursePerformance = $coursePerfStmt->fetchAll();

            if ($range === 'day') {
                $earningsStmt = $db->prepare("
                    SELECT DATE(created_at) as period, SUM(amount) as earnings
                    FROM payments
                    WHERE course_id IN ($placeholders) AND status IN ('succeeded', 'completed') AND created_at >= CURDATE()
                    GROUP BY DATE(created_at)
                    ORDER BY period
                ");
                $earningsStmt->execute($courseIds);
            } elseif ($range === 'month') {
                $earningsStmt = $db->prepare("
                    SELECT DATE_FORMAT(created_at, '%Y-%m') as period, SUM(amount) as earnings
                    FROM payments
                    WHERE course_id IN ($placeholders) AND status IN ('succeeded', 'completed') AND created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                    ORDER BY period
                ");
                $earningsStmt->execute($courseIds);
            } else {
                $earningsStmt = $db->prepare("
                    SELECT DATE_FORMAT(created_at, '%Y-%m') as period, SUM(amount) as earnings
                    FROM payments
                    WHERE course_id IN ($placeholders) AND status IN ('succeeded', 'completed') AND created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                    ORDER BY period
                ");
                $earningsStmt->execute($courseIds);
            }
            $monthlyEarnings = $earningsStmt->fetchAll();
        }

        if (empty($monthlyEarnings)) {
            $monthlyEarnings = [
                ['period' => date('Y-m', strtotime('-5 month')), 'earnings' => rand(200, 800)],
                ['period' => date('Y-m', strtotime('-4 month')), 'earnings' => rand(300, 1000)],
                ['period' => date('Y-m', strtotime('-3 month')), 'earnings' => rand(400, 1200)],
                ['period' => date('Y-m', strtotime('-2 month')), 'earnings' => rand(500, 1500)],
                ['period' => date('Y-m', strtotime('-1 month')), 'earnings' => rand(600, 1800)],
                ['period' => date('Y-m'), 'earnings' => rand(800, 2000)],
            ];
        }

        $data = [
            'title' => 'Teacher Dashboard - APPOLIOS',
            'userName' => $_SESSION['user_name'],
            'courses' => $myCourses,
            'stats' => $stats,
            'monthlyEarnings' => $monthlyEarnings,
            'coursePerformance' => $coursePerformance,
            'teacherSidebarActive' => 'dashboard'
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

        $coursesWithChapters = [];
        foreach ($courses as $course) {
            $fullCourse = $courseModel->getWithChapters($course['id']);
            $coursesWithChapters[] = $fullCourse ?: $course;
        }

        $data = [
            'title' => 'My Courses - APPOLIOS',
            'courses' => $coursesWithChapters,
            'teacherSidebarActive' => 'courses'
        ];

        $this->view('FrontOffice/teacher/courses', $data);
    }

    public function lessons()
    {
        $this->requireTeacher();

        $db = $this->getDb();
        $teacherId = (int) ($_SESSION['user_id'] ?? 0);
        $stmt = $db->prepare(
            "SELECT l.*, ch.title AS chapter_title, c.title AS course_title, c.id AS course_id
             FROM lessons l
             JOIN chapters ch ON ch.id = l.chapter_id
             JOIN courses c ON c.id = ch.course_id
             WHERE c.created_by = ?
             ORDER BY c.id DESC, ch.chapter_order ASC, l.lesson_order ASC"
        );
        $stmt->execute([$teacherId]);
        $lessons = $stmt->fetchAll();

        $data = [
            'title' => 'Lessons - APPOLIOS',
            'lessons' => $lessons,
            'flash' => $this->getFlash(),
        ];

        $this->view('FrontOffice/teacher/lessons', $data);
    }

    /**
     * Show add course form
     */
    public function addCourse() {
        $this->requireTeacher();

        $data = [
            'title' => 'Add Course - APPOLIOS',
            'flash' => $this->getFlash(),
            'teacherSidebarActive' => 'add-course'
        ];

        $this->view('FrontOffice/teacher/add_course', $data);
    }

    public function generateWithAI() {
        header('Content-Type: application/json');
        ob_clean(); // Ensure no previous output
        
        if (!$this->isLoggedIn() || $_SESSION['role'] !== 'teacher') {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid request']);
            exit;
        }
        
        $topic = trim($_POST['topic'] ?? '');
        $audience = $_POST['audience'] ?? 'beginners';
        
        if (empty($topic)) {
            echo json_encode(['success' => false, 'error' => 'Please enter a course topic']);
            exit;
        }
        
        require_once __DIR__ . '/../Service/AICourseGenerator.php';
        $aiGenerator = new AICourseGenerator();
        
        $result = $aiGenerator->generateFullCourse($topic, $audience);
        
        echo json_encode($result);
        exit;
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
        $courseType = $_POST['course_type'] ?? null;
        $categoryId = $_POST['category_id'] ?? null;
        $price = isset($_POST['price']) ? (float) $_POST['price'] : 0.0;

        $image = $_POST['image'] ?? '';
        if (isset($_FILES['course_image']) && !empty($_FILES['course_image']['tmp_name'])) {
            $uploadDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $filename = time() . '_' . basename($_FILES['course_image']['name']);
            if (move_uploaded_file($_FILES['course_image']['tmp_name'], $uploadDir . $filename)) {
                $image = 'uploads/images/' . $filename;
            }
        }

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
        $courseId = $courseModel->create([
            'title' => $title,
            'description' => $description,
            'image' => $image,
            'course_type' => $courseType,
            'category_id' => $categoryId ?: null,
            'price' => $price,
            'status' => 'pending',
            'created_by' => $_SESSION['user_id'],
        ]);

        if ($courseId) {
            $this->saveCourseChaptersLessonsAndBadges((int) $courseId, $_POST, $_FILES);
            $this->setFlash('success', 'Course submitted for admin approval!');
            $this->redirect('teacher/courses');
            return;
        }

        $this->setFlash('error', 'Failed to create course');
        $this->redirect('teacher/add-course');
    }

    /**
     * Show edit course form
     */
    public function editCourse($id) {
        $this->requireTeacher();

        $courseModel = $this->model('Course');
        $course = $courseModel->getWithChapters($id);

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
        $courseType = $_POST['course_type'] ?? null;
        $categoryId = $_POST['category_id'] ?? null;
        $price = isset($_POST['price']) ? (float) $_POST['price'] : 0.0;
        $image = $this->sanitize($_POST['image'] ?? '');

        if (isset($_FILES['course_image']) && !empty($_FILES['course_image']['tmp_name'])) {
            $uploadDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $filename = time() . '_' . basename($_FILES['course_image']['name']);
            if (move_uploaded_file($_FILES['course_image']['tmp_name'], $uploadDir . $filename)) {
                $image = 'uploads/images/' . $filename;
            }
        }

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
            'image' => $image,
            'course_type' => $courseType,
            'category_id' => $categoryId ?: null,
            'price' => $price,
            'status' => 'pending',
        ]);

        if ($result) {
            $this->deleteCourseContent((int) $id);
            $this->saveCourseChaptersLessonsAndBadges((int) $id, $_POST, $_FILES);
            $this->setFlash('success', 'Course updated and pending admin approval!');
            $this->redirect('teacher/courses');
            return;
        }

        $this->setFlash('error', 'Failed to update course');
        $this->redirect('teacher/edit-course/' . $id);
    }

    private function saveCourseChaptersLessonsAndBadges(int $courseId, array $postData, array $fileData): void
    {
        require_once __DIR__ . '/ChapterController.php';
        require_once __DIR__ . '/LessonController.php';

        $chapterModel = new ChapterController();
        $lessonModel = new LessonController();

        $chapters = $postData['chapters'] ?? [];
        if (!is_array($chapters)) {
            $chapters = [];
        }

        foreach ($chapters as $chapterIndex => $chapterData) {
            if (!is_array($chapterData)) continue;
            $chapterTitle = $this->sanitize($chapterData['title'] ?? '');
            if ($chapterTitle === '') continue;

            $chapterId = $chapterModel->create([
                'course_id' => $courseId,
                'title' => $chapterTitle,
                'description' => $this->sanitize($chapterData['description'] ?? ''),
                'chapter_order' => (int) $chapterIndex + 1,
                'sort_order' => (int) $chapterIndex + 1,
            ]);
            if (!$chapterId) {
                continue;
            }

            $lessons = $chapterData['lessons'] ?? [];
            if (!is_array($lessons)) {
                $lessons = [];
            }

            foreach ($lessons as $lessonIndex => $lessonData) {
                if (!is_array($lessonData)) continue;
                $lessonTitle = $this->sanitize($lessonData['title'] ?? '');
                if ($lessonTitle === '') continue;

                $lessonType = $lessonData['lesson_type'] ?? 'text';
                $content = $this->sanitize($lessonData['content'] ?? '');
                $videoUrl = $this->sanitize($lessonData['video_url'] ?? '');

                $pdfPath = '';
                if (isset($fileData['lessons']) && isset($fileData['lessons']['name'][$chapterIndex][$lessonIndex]['pdf_file'])) {
                    $name = $fileData['lessons']['name'][$chapterIndex][$lessonIndex]['pdf_file'] ?? '';
                    $tmp = $fileData['lessons']['tmp_name'][$chapterIndex][$lessonIndex]['pdf_file'] ?? '';
                    $err = $fileData['lessons']['error'][$chapterIndex][$lessonIndex]['pdf_file'] ?? 4;
                    if ($err === 0 && $tmp !== '' && $name !== '') {
                        $uploadDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'pdfs' . DIRECTORY_SEPARATOR;
                        if (!is_dir($uploadDir)) {
                            @mkdir($uploadDir, 0777, true);
                        }
                        $newFilename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', basename((string) $name));
                        $targetPath = $uploadDir . $newFilename;
                        if (move_uploaded_file($tmp, $targetPath)) {
                            $pdfPath = 'View/assets/pdfs/' . $newFilename;
                        }
                    }
                }
                if ($pdfPath === '' && !empty($lessonData['pdf_path'])) {
                    $pdfPath = $this->sanitize($lessonData['pdf_path']);
                }

                $lessonModel->createLesson([
                    'chapter_id' => (int) $chapterId,
                    'title' => $lessonTitle,
                    'content' => $content,
                    'video_url' => $videoUrl !== '' ? $videoUrl : null,
                    'pdf_path' => $pdfPath !== '' ? $pdfPath : null,
                    'lesson_type' => $lessonType,
                    'lesson_order' => (int) $lessonIndex + 1,
                    'sort_order' => (int) $lessonIndex + 1,
                ]);
            }
        }

        $badgeModel = $this->model('CourseBadge');
        $badges = $postData['badges'] ?? [];
        if (!is_array($badges)) {
            $badges = [];
        }
        foreach ($badges as $badgeData) {
            if (!is_array($badgeData)) continue;
            $badgeName = $this->sanitize($badgeData['badge_name'] ?? '');
            if ($badgeName === '') continue;
            $badgeModel->create([
                'course_id' => $courseId,
                'badge_name' => $badgeName,
                'badge_icon' => $badgeData['badge_icon'] ?? 'trophy',
                'badge_condition' => $badgeData['badge_condition'] ?? 'completion',
                'description' => $this->sanitize($badgeData['description'] ?? ''),
            ]);
        }
    }

    private function deleteCourseContent(int $courseId): void
    {
        $db = $this->getDb();

        $st = $db->prepare('SELECT id FROM chapters WHERE course_id = ?');
        $st->execute([$courseId]);
        $chapterIds = $st->fetchAll(PDO::FETCH_COLUMN);
        if (is_array($chapterIds) && !empty($chapterIds)) {
            $in = implode(',', array_fill(0, count($chapterIds), '?'));
            $db->prepare("DELETE FROM lessons WHERE chapter_id IN ($in)")->execute(array_values($chapterIds));
        }
        $db->prepare('DELETE FROM chapters WHERE course_id = ?')->execute([$courseId]);
        $db->prepare('DELETE FROM course_badges WHERE course_id = ?')->execute([$courseId]);
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
        $course = $courseModel->getWithChapters($id);

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
     * Show add evenement form for teacher.
     */
    public function addEvenement() {
        $this->requireTeacher();

        $data = [
            'title' => 'Propose Evenement - APPOLIOS',
            'flash' => $this->getFlash(),
            'teacherSidebarActive' => 'add-evenement'
        ];

        $this->view('FrontOffice/teacher/add_evenement', $data);
    }
    
    public function evenements() {
        $this->requireTeacher();
        $evenementModel = $this->model('Evenement');
        $evenementRessourceModel = $this->model('EvenementRessource');
        $userModel = $this->model('User');
        $userId = $_SESSION['user_id'];

        $evenements = $evenementModel->getByCreator($userId);

        // Calculate stats
        $totalEvents = count($evenements);
        $pendingCount = 0;
        $approvedCount = 0;
        $totalParticipants = 0;
        foreach ($evenements as $ev) {
            if (isset($ev['approval_status']) && $ev['approval_status'] === 'pending') {
                $pendingCount++;
            } elseif (isset($ev['approval_status']) && $ev['approval_status'] === 'approved') {
                $approvedCount++;
            }
            // Get participant count
            $parts = isset($ev['id']) ? $this->getParticipantsForEvent($ev['id']) : array();
            $totalParticipants += count($parts);
        }

        // Get participations by event for the modal functionality
        $participationsByEvent = [];
        foreach ($evenements as $ev) {
            if (isset($ev['id'])) {
                $participationsByEvent[$ev['id']] = $this->getParticipantsForEvent($ev['id']);
            }
        }

        // Get user info for profile card
        $user = $userModel->findById($userId);

        $data = array(
            'title' => 'My Evenements - APPOLIOS',
            'evenements' => $evenements,
            'teacherSidebarActive' => 'evenements',
            'stats' => array(
                'total_events' => $totalEvents,
                'pending' => $pendingCount,
                'approved' => $approvedCount,
                'participants' => $totalParticipants
            ),
            'user' => $user,
            'participationsByEvent' => $participationsByEvent
        );
        $this->view('FrontOffice/teacher/evenements', $data);
    }

    /**
     * Get participants for a specific event
     * @param int $eventId
     * @return array
     */
    private function getParticipantsForEvent($eventId) {
        $sql = "SELECT r.id, r.evenement_id, r.created_by as student_id,
                    r.title as student_name, r.details as status, r.created_at,
                    e.title as event_title, e.date_debut, e.heure_debut,
                    e.created_by as event_creator_id,
                    creator.role as event_creator_role,
                    u.name as student_name_full, u.email as student_email,
                    u.role as student_role, u.created_at as student_registered_at
             FROM evenement_ressources r
             JOIN evenements e ON r.evenement_id = e.id
             JOIN users u ON r.created_by = u.id
             JOIN users creator ON e.created_by = creator.id
             WHERE r.type = 'participation' AND r.evenement_id = ?
             ORDER BY r.created_at DESC";

        $stmt = $this->getDb()->prepare($sql);
        $stmt->execute([$eventId]);
        return $stmt->fetchAll();
    }

    /**
     * Approve a participation — only allowed for admin-created events.
     */
    public function approveParticipation($id) {
        $this->requireTeacher();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('teacher/evenements');
            return;
        }

        // Fetch participation info with student and event details
        $stmt = $this->getDb()->prepare(
            "SELECT r.*, e.title as event_title, e.event_date, e.location as event_location, e.created_by as event_creator_id,
                    u.email as student_email, u.full_name as student_name
             FROM evenement_ressources r
             JOIN evenements e ON r.evenement_id = e.id
             JOIN users u ON r.created_by = u.id
             WHERE r.id = ? AND r.type = 'participation' LIMIT 1"
        );
        $stmt->execute([(int)$id]);
        $participation = $stmt->fetch();

        if (!$participation) {
            $this->setFlash('error', 'Participation not found.');
            $this->redirect('teacher/evenements');
            return;
        }

        if ((int)$participation['event_creator_id'] !== (int)$_SESSION['user_id']) {
            $this->setFlash('error', 'You can only manage participations for events you created.');
            $this->redirect('teacher/evenements');
            return;
        }

        $upd = $this->getDb()->prepare(
            "UPDATE evenement_ressources
             SET details = 'approved', rejection_reason = NULL, updated_at = CURRENT_TIMESTAMP
             WHERE id = ? AND type = 'participation'"
        );
        
        if ($upd->execute([(int)$id])) {
            $this->setFlash('success', 'Participation approved successfully.');
            
            // Send Ticket Email
            if (!empty($participation['student_email'])) {
                $eventDate = $participation['event_date'] ?: 'To be announced';
                $eventLoc = $participation['event_location'] ?: 'Online / TBD';
                
                MailService::sendEventTicket(
                    $participation['student_email'],
                    $participation['student_name'] ?: 'Student',
                    $participation['event_title'] ?: 'Event',
                    $eventDate,
                    $eventLoc
                );
            }
        } else {
            $this->setFlash('error', 'Failed to approve participation.');
        }

        $this->redirect('teacher/evenements');
    }

    /**
     * Reject a participation — only allowed for admin-created events.
     */
    public function rejectParticipation($id) {
        $this->requireTeacher();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('teacher/evenements');
            return;
        }

        $reason = $this->sanitize($_POST['reason'] ?? 'No specific reason provided.');

        $stmt = $this->getDb()->prepare(
            "SELECT r.*, e.created_by as event_creator_id
             FROM evenement_ressources r
             JOIN evenements e ON r.evenement_id = e.id
             WHERE r.id = ? AND r.type = 'participation' LIMIT 1"
        );
        $stmt->execute([(int)$id]);
        $participation = $stmt->fetch();

        if (!$participation) {
            $this->setFlash('error', 'Participation not found.');
            $this->redirect('teacher/evenements');
            return;
        }

        if ((int)$participation['event_creator_id'] !== (int)$_SESSION['user_id']) {
            $this->setFlash('error', 'You can only manage participations for events you created.');
            $this->redirect('teacher/evenements');
            return;
        }

        $upd = $this->getDb()->prepare(
            "UPDATE evenement_ressources
             SET details = 'rejected', rejection_reason = ?, updated_at = CURRENT_TIMESTAMP
             WHERE id = ? AND type = 'participation'"
        );
        $upd->execute([$reason, (int)$id])
            ? $this->setFlash('success', 'Participation rejected.')
            : $this->setFlash('error', 'Failed to reject participation.');

        $this->redirect('teacher/evenements');
    }
    
    public function quiz() {
        $this->requireTeacher();
        $data = [
            'title' => 'Quiz - APPOLIOS',
            'teacherSidebarActive' => 'teacher-quiz'
        ];
        $this->view('FrontOffice/teacher/quiz', $data);
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

        $result = $evenementModel->create(array(
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
        ));

        if ($result) {
            $this->setFlash('success', 'Evenement submitted to admin for approval.');
            if (isset($_POST['action']) && $_POST['action'] === 'save_and_resources') {
                $this->redirect('teacher/evenement-ressources&evenement_id=' . $result);
            } else {
                $this->redirect('teacher/evenements');
            }
            return;
        }

        $detail = method_exists($evenementModel, 'getLastError') ? (string) $evenementModel->getLastError() : '';
        $msg = 'Failed to create evenement request.';
        if ($detail !== '') {
            $msg .= ' ' . $detail;
        }
        $this->setFlash('error', $msg);
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
        $existing = $evenementModel->findByIdAndCreator((int) $id, (int) $_SESSION['user_id']);
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

            $this->setErrors($errors);
            $_SESSION['old'] = $_POST;
            $this->redirect('teacher/evenement-ressources&evenement_id=' . $eventId);
            return;
        }

        // Handle Material Quantity
        if ($type === 'materiel' && !empty($_POST['quantite'])) {
            $qty = $this->sanitize($_POST['quantite']);
            $details = "Quantité: " . $qty . "\n" . $details;
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

        // Handle Material Quantity on Update
        if ($resource['type'] === 'materiel' && !empty($_POST['quantite'])) {
            $qty = $this->sanitize($_POST['quantite']);
            $details = "Quantité: " . $qty . "\n" . $details;
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
            $generator = new AvatarGenerator();
            $result = $generator->generateAvatar($faceData, $_SESSION['user_id']);
            echo json_encode($result);
        } catch (Throwable $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // Quiz Management Methods

    /**
     * Teacher quiz management page
     */
    public function quizzes()
    {
        $this->requireTeacher();

        $quizModel = $this->model('Quiz');
        $quizzes = $quizModel->getByTeacher($_SESSION['user_id']);
        
        // Get quiz usage statistics
        $quizUsage = $quizModel->getUsageStats($quizzes);
        
        // Get top statistics
        $quizTopStats = $quizModel->getTeacherTopStats($_SESSION['user_id']);

        $data = [
            'title' => 'My Quizzes - APPOLIOS',
            'description' => 'Manage your quizzes',
            'quizzes' => $quizzes,
            'quizUsage' => $quizUsage,
            'quizTopStats' => $quizTopStats,
            'flash' => $this->getFlash()
        ];

        $this->view('FrontOffice/teacher/quizzes', $data);
    }

    /**
     * Add new quiz (teacher)
     */
    public function addQuiz()
    {
        $this->requireTeacher();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $quizModel = $this->model('Quiz');
            
            $data = [
                'title' => $_POST['title'] ?? '',
                'course_id' => (int) ($_POST['course_id'] ?? 0),
                'chapter_id' => (int) ($_POST['chapter_id'] ?? 0),
                'difficulty' => $_POST['difficulty'] ?? 'beginner',
                'tags' => $_POST['tags'] ?? '',
                'time_limit_sec' => (int) ($_POST['time_limit_sec'] ?? 0),
                'status' => 'pending',
                'created_by' => $_SESSION['user_id']
            ];

            // Validate that teacher owns the course
            $courseModel = $this->model('Course');
            $course = $courseModel->getById($data['course_id']);
            
            if (!$course || $course['created_by'] != $_SESSION['user_id']) {
                $this->setFlash('error', 'You can only create quizzes for your own courses.');
            } else {
                $quizId = $quizModel->create($data);
                
                if ($quizId) {
                    // Add questions if provided
                    if (!empty($_POST['questions'])) {
                        $questionBankModel = $this->model('QuestionBank');
                        foreach ($_POST['questions'] as $questionId) {
                            $quizModel->addQuestion($quizId, $questionId);
                        }
                    }

                    // Handle AI blueprint if provided
                    if (!empty($_POST['ai_blueprint'])) {
                        $aiService = $this->service('AIService');
                        $aiService->generateFromBlueprint($quizId, $_POST['ai_blueprint']);
                    }

                    $this->logActivity('create_quiz', "Created quiz: {$data['title']}");
                    $this->setFlash('success', 'Quiz created successfully and submitted for approval.');
                    $this->redirect('teacher-quiz/quizzes');
                    return;
                } else {
                    $this->setFlash('error', 'Failed to create quiz.');
                }
            }
        }

        $courseModel = $this->model('Course');
        $chapterModel = $this->model('Chapter');
        $questionBankModel = $this->model('QuestionBank');

        $data = [
            'title' => 'Add Quiz - APPOLIOS',
            'description' => 'Create a new quiz',
            'courses' => $courseModel->getByTeacher($_SESSION['user_id']),
            'chapters' => $chapterModel->getAll(),
            'questions' => $questionBankModel->getAll(),
            'flash' => $this->getFlash()
        ];

        $this->view('FrontOffice/teacher/quiz_form', $data);
    }

    /**
     * Edit quiz (teacher)
     */
    public function editQuiz($id)
    {
        $this->requireTeacher();

        $quizModel = $this->model('Quiz');
        $quiz = $quizModel->getById((int) $id);

        if (!$quiz || $quiz['created_by'] != $_SESSION['user_id']) {
            $this->setFlash('error', 'Quiz not found or access denied.');
            $this->redirect('teacher-quiz/quizzes');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'title' => $_POST['title'] ?? '',
                'course_id' => (int) ($_POST['course_id'] ?? 0),
                'chapter_id' => (int) ($_POST['chapter_id'] ?? 0),
                'difficulty' => $_POST['difficulty'] ?? 'beginner',
                'tags' => $_POST['tags'] ?? '',
                'time_limit_sec' => (int) ($_POST['time_limit_sec'] ?? 0)
            ];

            if ($quizModel->update((int) $id, $data)) {
                // Update questions if provided
                if (isset($_POST['questions'])) {
                    $quizModel->updateQuestions((int) $id, $_POST['questions']);
                }

                $this->logActivity('update_quiz', "Updated quiz: {$data['title']}");
                $this->setFlash('success', 'Quiz updated successfully.');
                $this->redirect('teacher-quiz/quizzes');
                return;
            } else {
                $this->setFlash('error', 'Failed to update quiz.');
            }
        }

        $courseModel = $this->model('Course');
        $chapterModel = $this->model('Chapter');
        $questionBankModel = $this->model('QuestionBank');

        $data = [
            'title' => 'Edit Quiz - APPOLIOS',
            'description' => 'Edit quiz',
            'quiz' => $quiz,
            'courses' => $courseModel->getByTeacher($_SESSION['user_id']),
            'chapters' => $chapterModel->getAll(),
            'questions' => $questionBankModel->getAll(),
            'quizQuestions' => $quizModel->getQuestions((int) $id),
            'flash' => $this->getFlash()
        ];

        $this->view('FrontOffice/teacher/quiz_form', $data);
    }

    /**
     * Delete quiz (teacher)
     */
    public function deleteQuiz($id)
    {
        $this->requireTeacher();

        $quizModel = $this->model('Quiz');
        $quiz = $quizModel->getById((int) $id);

        if (!$quiz || $quiz['created_by'] != $_SESSION['user_id']) {
            $this->setFlash('error', 'Quiz not found or access denied.');
        } elseif ($quizModel->delete((int) $id)) {
            $this->logActivity('delete_quiz', "Deleted quiz: {$quiz['title']}");
            $this->setFlash('success', 'Quiz deleted successfully.');
        } else {
            $this->setFlash('error', 'Failed to delete quiz.');
        }

        $this->redirect('teacher-quiz/quizzes');
    }

    /**
     * Duplicate quiz (teacher)
     */
    public function duplicateQuiz($id)
    {
        $this->requireTeacher();

        $quizModel = $this->model('Quiz');
        $quiz = $quizModel->getById((int) $id);

        if (!$quiz || $quiz['created_by'] != $_SESSION['user_id']) {
            $this->setFlash('error', 'Quiz not found or access denied.');
        } else {
            $newQuizId = $quizModel->duplicate((int) $id, $_SESSION['user_id']);
            
            if ($newQuizId) {
                $this->logActivity('create_quiz', "Duplicated quiz: {$quiz['title']}");
                $this->setFlash('success', 'Quiz duplicated successfully.');
            } else {
                $this->setFlash('error', 'Failed to duplicate quiz.');
            }
        }

        $this->redirect('teacher-quiz/quizzes');
    }

    /**
     * Teacher question bank management
     */
    public function questions()
    {
        $this->requireTeacher();

        $questionBankModel = $this->model('QuestionBank');
        $questions = $questionBankModel->getAllWithStats();
        $stats = $questionBankModel->getTopStats();
        $charts = $questionBankModel->getChartData();
        $questionQa = $questionBankModel->getQualityAssessment();

        $data = [
            'title' => 'Question Bank - APPOLIOS',
            'description' => 'Manage the question bank',
            'questions' => $questions,
            'qbTopStats' => $stats,
            'charts' => $charts,
            'questionQa' => $questionQa,
            'flash' => $this->getFlash(),
            'teacherSidebarActive' => 'teacher-questions'
        ];

        $this->view('FrontOffice/teacher/questions_bank', $data);
    }

    /**
     * Add new question (teacher)
     */
    public function addQuestion()
    {
        $this->requireTeacher();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $questionBankModel = $this->model('QuestionBank');
            
            $data = [
                'title' => $_POST['title'] ?? '',
                'question_text' => $_POST['question_text'] ?? '',
                'options' => $_POST['options'] ?? [],
                'correct_answer' => (int) ($_POST['correct_answer'] ?? 0),
                'difficulty' => $_POST['difficulty'] ?? 'beginner',
                'tags' => $_POST['tags'] ?? '',
                'created_by' => $_SESSION['user_id']
            ];

            $questionId = $questionBankModel->create($data);
            
            if ($questionId) {
                $this->logActivity('create_question', "Created question: {$data['title']}");
                $this->setFlash('success', 'Question created successfully.');
                $this->redirect('teacher-quiz/questions');
                return;
            } else {
                $this->setFlash('error', 'Failed to create question.');
            }
        }

        $data = [
            'title' => 'Add Question - APPOLIOS',
            'description' => 'Create a new question',
            'flash' => $this->getFlash()
        ];

        $this->view('FrontOffice/teacher/question_form', $data);
    }

    /**
     * Edit question (teacher)
     */
    public function editQuestion($id)
    {
        $this->requireTeacher();

        $questionBankModel = $this->model('QuestionBank');
        $question = $questionBankModel->getById((int) $id);

        if (!$question || $question['created_by'] != $_SESSION['user_id']) {
            $this->setFlash('error', 'Question not found or access denied.');
            $this->redirect('teacher-quiz/questions');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'title' => $_POST['title'] ?? '',
                'question_text' => $_POST['question_text'] ?? '',
                'options' => $_POST['options'] ?? [],
                'correct_answer' => (int) ($_POST['correct_answer'] ?? 0),
                'difficulty' => $_POST['difficulty'] ?? 'beginner',
                'tags' => $_POST['tags'] ?? ''
            ];

            if ($questionBankModel->update((int) $id, $data)) {
                $this->logActivity('update_question', "Updated question: {$data['title']}");
                $this->setFlash('success', 'Question updated successfully.');
                $this->redirect('teacher-quiz/questions');
                return;
            } else {
                $this->setFlash('error', 'Failed to update question.');
            }
        }

        $data = [
            'title' => 'Edit Question - APPOLIOS',
            'description' => 'Edit question',
            'question' => $question,
            'flash' => $this->getFlash()
        ];

        $this->view('FrontOffice/teacher/question_form', $data);
    }

    /**
     * Delete question (teacher)
     */
    public function deleteQuestion($id)
    {
        $this->requireTeacher();

        $questionBankModel = $this->model('QuestionBank');
        $question = $questionBankModel->getById((int) $id);

        if (!$question || $question['created_by'] != $_SESSION['user_id']) {
            $this->setFlash('error', 'Question not found or access denied.');
        } elseif ($questionBankModel->delete((int) $id)) {
            $this->logActivity('delete_question', "Deleted question: {$question['title']}");
            $this->setFlash('success', 'Question deleted successfully.');
        } else {
            $this->setFlash('error', 'Failed to delete question.');
        }

        $this->redirect('teacher-quiz/questions');
    }

    /**
     * Quiz statistics (teacher)
     */
    public function quizStats()
    {
        $this->requireTeacher();

        $quizModel = $this->model('Quiz');
        $stats = $quizModel->getTeacherStatistics($_SESSION['user_id']);

        $data = [
            'title' => 'Quiz Statistics - APPOLIOS',
            'description' => 'View your quiz performance statistics',
            'stats' => $stats,
            'flash' => $this->getFlash()
        ];

        $this->view('FrontOffice/teacher/quiz_stats', $data);
    }

    /**
     * Exam builder (teacher)
     */
    public function examBuilder()
    {
        $this->requireTeacher();

        $courseModel = $this->model('Course');
        $questionBankModel = $this->model('QuestionBank');

        $data = [
            'title' => 'Exam Builder - APPOLIOS',
            'description' => 'Build comprehensive exams from question bank',
            'courses' => $courseModel->getByTeacher($_SESSION['user_id']),
            'questions' => $questionBankModel->getAll(),
            'flash' => $this->getFlash(),
            'teacherSidebarActive' => 'exam-builder'
        ];

        $this->view('FrontOffice/teacher/exam_builder', $data);
    }

    /**
     * Remediation plan (teacher)
     */
    public function remediationPlan()
    {
        $this->requireTeacher();

        $quizModel = $this->model('Quiz');
        $plans = $quizModel->getRemediationPlans($_SESSION['user_id']);

        $data = [
            'title' => 'Remediation Plans - APPOLIOS',
            'description' => 'View and manage student remediation plans',
            'plans' => $plans,
            'flash' => $this->getFlash(),
            'teacherSidebarActive' => 'remediation-plan'
        ];

        $this->view('FrontOffice/teacher/remediation_plan', $data);
    }

    // ==========================================
    // AI RESOURCE GENERATION
    // ==========================================

    /**
     * POST teacher/generate-ai-resources
     * Calls Gemini API and inserts generated rules/materials/plans into DB.
     */
    public function generateAiResources()
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

        $evenementModel = $this->model('Evenement');
        $event = $evenementModel->findByIdAndCreator($eventId, $teacherId);
        
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
                . "  \"materials\": [{\"title\": \"...\", \"details\": \"...\", \"quantity\": 1}],\n"
                . "  \"plans\": [{\"title\": \"...\", \"details\": \"...\"}]\n"
                . "}\n"
                . "Génère 3 règles, 3 matériels nécessaires et 3 éléments de plan journée. "
                . "IMPORTANT: Pour chaque matériel, indique la quantité nécessaire PAR PARTICIPANT (ex: 1 pour une bouteille d'eau par personne). "
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
            $curlError = curl_error($ch);
            $errBody = is_string($response) ? json_decode($response, true) : null;
            $errMsg  = $errBody['error']['message'] ?? ($curlError ?: "Erreur API Gemini (HTTP $httpCode).");
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
        $ressourceModel = $this->model('EvenementRessource');
        
        $typeMap = ['rules' => 'rule', 'materials' => 'materiel', 'plans' => 'plan'];
        foreach ($typeMap as $key => $dbType) {
            foreach (($resources[$key] ?? []) as $item) {
                $ressourceModel->create([
                    'evenement_id' => $eventId,
                    'type' => $dbType,
                    'title' => trim($item['title'] ?? ''),
                    'details' => ($dbType === 'materiel' && !empty($item['quantity'])) 
                                 ? "Quantité: " . $item['quantity'] . "\n" . trim($item['details'] ?? '') 
                                 : trim($item['details'] ?? ''),
                    'created_by' => $teacherId
                ]);
            }
        }
        
        if (method_exists($evenementModel, 'markPendingIfApproved')) {
            $evenementModel->markPendingIfApproved($eventId);
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
