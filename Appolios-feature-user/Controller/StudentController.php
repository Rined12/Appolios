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

class StudentController extends BaseController {

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
     * Route alias for /student/lesson/{id}
     */
    public function lesson($id) {
        $this->viewLesson($id);
    }

    /**
     * Complete a lesson and update progress
     */
    public function completeLesson($lessonId = null, $courseId = null) {
        header('Content-Type: application/json');
        
        if (!$this->isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Please login to continue.']);
            exit;
        }
        
        $lessonId = $lessonId ?? ($_POST['lessonId'] ?? null);
        $courseId = $courseId ?? ($_POST['courseId'] ?? null);

        if (!$lessonId || !$courseId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing parameters.']);
            exit;
        }

        require_once __DIR__ . '/../Model/Lesson.php';
        require_once __DIR__ . '/../Model/Chapter.php';
        
        $lessonModel = new Lesson();
        $lesson = $lessonModel->findById($lessonId);
        
        if (!$lesson) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Lesson not found.']);
            exit;
        }

        $chapterModel = new Chapter();
        $chapter = $chapterModel->findById($lesson['chapter_id']);
        
        if (!$chapter) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Chapter not found.']);
            exit;
        }

        $actualCourseId = $chapter['course_id'];

        $enrollmentModel = $this->model('Enrollment');
        
        if (!$enrollmentModel->isEnrolled($_SESSION['user_id'], $actualCourseId)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'You must enroll first.']);
            exit;
        }

        require_once __DIR__ . '/../Model/LessonProgress.php';
        $progressModel = new LessonProgress();
        $progressModel->markCompleteNoDuplicate($_SESSION['user_id'], $lessonId);

        $allLessons = $lessonModel->getByCourseId($actualCourseId);
        $completedFromDb = $progressModel->getCompletedLessons($_SESSION['user_id'], $actualCourseId);
        $completedLessons = array_column($completedFromDb, 'lesson_id');
        
        if (!in_array($lessonId, $completedLessons)) {
            $completedLessons[] = $lessonId;
        }
        
        $_SESSION['completed_lessons'][$actualCourseId] = $completedLessons;

        $totalLessons = count($allLessons);
        $completedCount = count($completedLessons);
        $progress = $totalLessons > 0 ? round(($completedCount / $totalLessons) * 100) : 0;

        $enrollmentModel->updateProgress($_SESSION['user_id'], $actualCourseId, $progress);

        $this->awardXP($_SESSION['user_id'], 15, 'Completed a lesson');
        $this->checkAndAwardBadges($_SESSION['user_id'], $actualCourseId, $completedCount, $totalLessons, $progress);

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'progress' => $progress]);
        exit;
    }
    
    /**
     * Award XP to user
     */
    private function awardXP($userId, $amount, $reason) {
        require_once __DIR__ . '/../Model/UserXP.php';
        $xpModel = new UserXP();
        return $xpModel->addXP($userId, $amount, $reason);
    }

    /**
     * Check and award badges based on progress
     */
    private function checkAndAwardBadges($userId, $courseId, $completedCount, $totalLessons, $progress) {
        require_once __DIR__ . '/../Model/CourseBadge.php';
        require_once __DIR__ . '/../Model/Badge.php';
        
        $courseBadgeModel = new CourseBadge();
        $badgeModel = new Badge();
        
        $courseBadges = $courseBadgeModel->getByCourseId($courseId);
        
        // Check for course-specific badges
        foreach ($courseBadges as $courseBadge) {
            $condition = $courseBadge['badge_condition'];
            $award = false;
            
            switch ($condition) {
                case 'first_lesson':
                    $award = ($completedCount >= 1);
                    break;
                case 'chapter_complete':
                    $award = ($completedCount >= 1);
                    break;
                case 'all_lessons':
                    $award = ($completedCount >= $totalLessons);
                    break;
                case 'completion':
                    $award = ($progress >= 100);
                    break;
            }
            
            if ($award && !$badgeModel->hasBadge($userId, $courseBadge['badge_name'])) {
                $badgeModel->awardBadge(
                    $userId,
                    $courseBadge['badge_name'],
                    $courseBadge['badge_icon'],
                    $courseBadge['description']
                );
                
                $this->createNotification(
                    $userId,
                    'badge',
                    'Badge Earned!',
                    'You earned the "' . $courseBadge['badge_name'] . '" badge!',
                    'student/course/' . $courseId
                );
            }
        }
        
        // AI Badge: Generate creative badge on course completion (100%)
        if ($progress >= 100) {
            require_once __DIR__ . '/../Service/BadgeGenerator.php';
            $badgeGen = new BadgeGenerator();
            
            $courseModel = $this->model('Course');
            $course = $courseModel->findById($courseId);
            
            $completionTime = $this->getCompletionTime($userId, $courseId);
            
            $badge = $badgeGen->generateBadge(
                $course['title'] ?? 'Course',
                $course['category'] ?? 'General',
                $completionTime,
                $totalLessons,
                $progress
            );
            
            if (!$badgeModel->hasBadge($userId, $badge['name'])) {
                $badgeModel->awardBadge($userId, $badge['name'], $badge['icon'], $badge['description']);
                
                $this->createNotification(
                    $userId,
                    'badge',
                    '🎉 New Badge Earned!',
                    'You earned: ' . $badge['name'] . ' - ' . $badge['description'],
                    'student/badges'
                );
            }
        }
    }

    /**
     * Create notification for user
     */
    private function createNotification($userId, $type, $title, $message, $link = '') {
        require_once __DIR__ . '/../Model/Notification.php';
        
        $notificationModel = new Notification();
        return $notificationModel->create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $link
        ]);
    }

    /**
     * View a single lesson
     */
    public function viewLesson($id) {
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Please login to view lessons.');
            $this->redirect('login');
            return;
        }

        require_once __DIR__ . '/../Model/Lesson.php';
        require_once __DIR__ . '/../Model/Chapter.php';
        
        $lessonModel = new Lesson();
        $lesson = $lessonModel->findById($id);
        
        if (!$lesson) {
            $this->setFlash('error', 'Lesson not found.');
            $this->redirect('student/courses');
            return;
        }

        $chapterModel = new Chapter();
        $chapter = $chapterModel->findById($lesson['chapter_id']);
        
        if (!$chapter) {
            $this->setFlash('error', 'Chapter not found.');
            $this->redirect('student/courses');
            return;
        }

        $courseId = $chapter['course_id'];

        $enrollmentModel = $this->model('Enrollment');
        $isEnrolled = $enrollmentModel->isEnrolled($_SESSION['user_id'], $courseId);

        if (!$isEnrolled) {
            $this->setFlash('error', 'Please enroll first.');
            $this->redirect('student/course/' . $courseId);
            return;
        }

        $enrollment = $enrollmentModel->getEnrollment($_SESSION['user_id'], $courseId);

        $courseModel = $this->model('Course');
        $courseWithChapters = $courseModel->getWithChapters($courseId);
        
        // Prepare lesson data for JS
        $lessonData = [];
        if (!empty($courseWithChapters['chapters'])) {
            foreach ($courseWithChapters['chapters'] as $ch) {
                if (!empty($ch['lessons'])) {
                    foreach ($ch['lessons'] as $les) {
                        $lessonData[$les['id']] = $les;
                    }
                }
            }
        }

        $completedLessons = $this->getCompletedLessons($courseId, $_SESSION['user_id']);
        
        require_once __DIR__ . '/../Model/LessonProgress.php';
        $progressModel = new LessonProgress();
        $completedFromDb = $progressModel->getCompletedLessons($_SESSION['user_id'], $courseId);
        $completedLessons = array_column($completedFromDb, 'lesson_id');
        $_SESSION['completed_lessons'][$courseId] = $completedLessons;

        $data = [
            'title' => $lesson['title'] . ' - APPOLIOS',
            'description' => 'Lesson: ' . $lesson['title'],
            'lesson' => $lesson,
            'chapter' => $chapter,
            'course' => $courseWithChapters,
            'courseId' => $courseId,
            'isEnrolled' => $isEnrolled,
            'progress' => $enrollment['progress'] ?? 0,
            'lessonData' => $lessonData,
            'completedLessons' => $completedLessons,
            'flash' => $this->getFlash()
        ];

        $this->view('FrontOffice/student/lesson', $data);
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

        $courseModel = $this->model('Course');
        $enrollmentModel = $this->model('Enrollment');
        
        $allCourses = $courseModel->getAllWithCreator();
        $enrollments = $enrollmentModel->getUserEnrollments($_SESSION['user_id']);
        $enrolledIds = array_column($enrollments, 'course_id');

        // Get student statistics
        $enrolledCount = $enrollmentModel->getStudentEnrolledCount($_SESSION['user_id']);
        $completedCount = $enrollmentModel->getStudentCompletedCount($_SESSION['user_id']);
        $inProgressCount = $enrollmentModel->getStudentInProgressCount($_SESSION['user_id']);
        $averageProgress = $enrollmentModel->getStudentAverageProgress($_SESSION['user_id']);
        $enrollmentHistory = $enrollmentModel->getStudentEnrollmentHistory($_SESSION['user_id']);
        $progressDetails = $enrollmentModel->getStudentProgressDetails($_SESSION['user_id']);

        // Get badges
        $badgeModel = $this->model('Badge');
        $userBadges = $badgeModel->getByUserId($_SESSION['user_id']);
        
        // Get XP and level
        require_once __DIR__ . '/../Model/UserXP.php';
        $xpModel = new UserXP();
        $xpData = $xpModel->getByUserId($_SESSION['user_id']);
        $totalXP = $xpData['total_xp'] ?? 0;
        $levelInfo = $xpModel->getLevel($totalXP);
        
        // Get course recommendations
        require_once __DIR__ . '/../Service/CourseRecommendation.php';
        $recommendationModel = new CourseRecommendation();
        $recommendations = $recommendationModel->getRecommendations($_SESSION['user_id'], 4);
        
        $data = [
            'title' => 'My Dashboard - APPOLIOS',
            'description' => 'Student dashboard',
            'userName' => $_SESSION['user_name'],
            'allCourses' => $allCourses,
            'enrollments' => $enrollments,
            'enrolledIds' => $enrolledIds,
            'badges' => $userBadges,
            'enrolledCount' => $enrolledCount,
            'completedCount' => $completedCount,
            'inProgressCount' => $inProgressCount,
            'averageProgress' => $averageProgress,
            'enrollmentHistory' => $enrollmentHistory,
            'progressDetails' => $progressDetails,
            'totalXP' => $totalXP,
            'levelInfo' => $levelInfo,
            'recommendations' => $recommendations,
            'flash' => $this->getFlash()
        ];

        $this->view('FrontOffice/student/dashboard', $data);
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
            'evenements' => $evenementModel->findApprovedUpcoming(),
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

        $evenement = $evenementModel->findByIdWithCreator($id);
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

        $grouped = $ressourceModel->getGroupedByEvenement($id);

        $data = [
            'title' => (($evenement['titre'] ?? '') ?: ($evenement['title'] ?? 'Evenement')) . ' - APPOLIOS',
            'description' => $evenement['description'] ?? 'Evenement details',
            'evenement' => $evenement,
            'rules' => $grouped['rules'],
            'materiels' => $grouped['materiels'],
            'plans' => $grouped['plans'],
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

        // Only students can access this page
        if ($_SESSION['role'] !== 'student') {
            $this->setFlash('error', 'Access denied.');
            $this->redirect('login');
            return;
        }

        $courseModel = $this->model('Course');
        $enrollmentModel = $this->model('Enrollment');

        // Get all courses
        $allCourses = $courseModel->getAllWithCreator();

        // Filter only approved courses for students
        $allCourses = array_filter($allCourses, function($course) {
            return ($course['status'] ?? '') === 'approved';
        });

        // Re-index array
        $allCourses = array_values($allCourses);

        // Filter by search query
        $searchQuery = $_GET['search'] ?? '';
        if (!empty($searchQuery)) {
            $search = strtolower($searchQuery);
            $allCourses = array_filter($allCourses, function($course) use ($search) {
                return strpos(strtolower($course['title'] ?? ''), $search) !== false
                    || strpos(strtolower($course['description'] ?? ''), $search) !== false
                    || strpos(strtolower($course['course_type'] ?? ''), $search) !== false;
            });
        }

        // Filter by category if selected
        $categoryId = $_GET['category'] ?? '';
        if (!empty($categoryId)) {
            $allCourses = array_filter($allCourses, function($course) use ($categoryId) {
                return isset($course['category_id']) && $course['category_id'] == $categoryId;
            });
        }

        // Get enrolled course IDs to mark them
        $enrollments = $enrollmentModel->getUserEnrollments($_SESSION['user_id']);
        $enrolledIds = array_column($enrollments, 'course_id');
        
        // Get course recommendations
        require_once __DIR__ . '/../Service/CourseRecommendation.php';
        $recommendationModel = new CourseRecommendation();
        $recommendations = $recommendationModel->getRecommendations($_SESSION['user_id'], 4);
        
        $data = [
            'title' => 'Browse Courses - APPOLIOS',
            'description' => 'Explore all available courses',
            'courses' => array_values($allCourses),
            'enrolledIds' => $enrolledIds,
            'recommendations' => $recommendations,
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

        $course = $courseModel->getWithChapters($id);

        if (!$course) {
            $this->setFlash('error', 'Course not found.');
            $this->redirect('student/dashboard');
            return;
        }

        $isEnrolled = $enrollmentModel->isEnrolled($_SESSION['user_id'], $id);
        
        require_once __DIR__ . '/../Model/LessonProgress.php';
        $progressModel = new LessonProgress();
        $completedFromDb = $progressModel->getCompletedLessons($_SESSION['user_id'], $id);
        $completedLessons = array_column($completedFromDb, 'lesson_id');
        $_SESSION['completed_lessons'][$id] = $completedLessons;
        
        $enrollment = $isEnrolled ? $enrollmentModel->getEnrollment($_SESSION['user_id'], $id) : null;
        $progress = $enrollment['progress'] ?? 0;

        require_once __DIR__ . '/../Model/Review.php';
        $reviewModel = new Review();
        $reviews = $reviewModel->getByCourseId($id);
        $hasReview = $reviewModel->hasUserReviewed($_SESSION['user_id'], $id);

        $data = [
            'title' => $course['title'] . ' - APPOLIOS',
            'description' => $course['description'],
            'course' => $course,
            'isEnrolled' => $isEnrolled,
            'progress' => $progress,
            'completedLessons' => $completedLessons,
            'reviews' => $reviews,
            'hasReview' => $hasReview,
            'flash' => $this->getFlash()
        ];

        $this->view('FrontOffice/student/course', $data);
    }

    /**
     * Submit a review
     */
    public function review($courseId) {
        if (!$this->isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Please login to leave a review.']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            exit;
        }

        header('Content-Type: application/json');
        
        $rating = (int) ($_POST['rating'] ?? 0);
        $reviewText = $this->sanitize($_POST['comment'] ?? '');

        if ($rating < 1 || $rating > 5) {
            echo json_encode(['success' => false, 'message' => 'Please select a rating.']);
            exit;
        }

        $enrollmentModel = $this->model('Enrollment');
        if (!$enrollmentModel->isEnrolled($_SESSION['user_id'], $courseId)) {
            echo json_encode(['success' => false, 'message' => 'You must enroll first.']);
            exit;
        }

        require_once __DIR__ . '/../Model/Review.php';
        $reviewModel = new Review();

        if ($reviewModel->hasUserReviewed($_SESSION['user_id'], $courseId)) {
            echo json_encode(['success' => false, 'message' => 'You have already reviewed this course.']);
            exit;
        }

        $reviewModel->create([
            'user_id' => $_SESSION['user_id'],
            'course_id' => $courseId,
            'rating' => $rating,
            'comment' => $reviewText
        ]);
        
        // Award XP for review
        $this->awardXP($_SESSION['user_id'], 20, 'Wrote a course review');

        echo json_encode(['success' => true, 'message' => 'Thank you for your review!']);
        exit;
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
        
        // Get course recommendations
        require_once __DIR__ . '/../Service/CourseRecommendation.php';
        $recommendationModel = new CourseRecommendation();
        $recommendations = $recommendationModel->getRecommendations($_SESSION['user_id'], 4);

        $data = [
            'title' => 'My Courses - APPOLIOS',
            'description' => 'Your enrolled courses',
            'enrollments' => $enrollments,
            'recommendations' => $recommendations,
            'flash' => $this->getFlash()
        ];

        $this->view('FrontOffice/student/my_courses', $data);
    }

    /**
     * Toggle course bookmark
     */
    public function toggleBookmark() {
        header('Content-Type: application/json');
        
        if (!$this->isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Please login to bookmark courses.']);
            exit;
        }

        $courseId = $_POST['course_id'] ?? null;
        if (!$courseId) {
            echo json_encode(['success' => false, 'message' => 'Course ID required.']);
            exit;
        }

        require_once __DIR__ . '/../Model/CourseBookmark.php';
        $bookmarkModel = new CourseBookmark();
        
        $isBookmarked = $bookmarkModel->isBookmarked($_SESSION['user_id'], $courseId);
        
        if ($isBookmarked) {
            $bookmarkModel->removeBookmark($_SESSION['user_id'], $courseId);
            echo json_encode(['success' => true, 'bookmarked' => false]);
        } else {
            $bookmarkModel->addBookmark($_SESSION['user_id'], $courseId);
            echo json_encode(['success' => true, 'bookmarked' => true]);
        }
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

        require_once __DIR__ . '/../Model/User.php';
        $userModel = $this->model('User');
        $user = $userModel->findById($_SESSION['user_id']);

        $data = [
            'title' => 'My Profile - APPOLIOS',
            'description' => 'Student profile',
            'user' => $user,
            'flash' => $this->getFlash()
        ];

        $this->view('FrontOffice/student/profile', $data);
    }

    /**
     * Notifications page
     */
    public function notifications() {
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Please login to view notifications.');
            $this->redirect('login');
            return;
        }

        if ($_SESSION['role'] !== 'student') {
            $this->setFlash('error', 'Access denied.');
            $this->redirect('login');
            return;
        }

        require_once __DIR__ . '/../Model/Notification.php';
        $notificationModel = new Notification();
        $notifications = $notificationModel->getByUserId($_SESSION['user_id']);
        
        $notificationModel->markAllAsRead($_SESSION['user_id']);

        $data = [
            'title' => 'Notifications - APPOLIOS',
            'description' => 'Your notifications',
            'notifications' => $notifications,
            'flash' => $this->getFlash()
        ];

        $this->view('FrontOffice/student/notifications', $data);
    }

    /**
     * Badges page
     */
    public function badges() {
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Please login to view badges.');
            $this->redirect('login');
            return;
        }

        if ($_SESSION['role'] !== 'student') {
            $this->setFlash('error', 'Access denied.');
            $this->redirect('login');
            return;
        }

        $data = [
            'title' => 'My Badges - APPOLIOS',
            'description' => 'Your earned badges',
            'flash' => $this->getFlash()
        ];

$this->view('FrontOffice/student/badges', $data);
    }
    
    /**
     * Certificates page
     */
    public function certificates() {
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Please login to view certificates.');
            $this->redirect('login');
            return;
        }
        
        if ($_SESSION['role'] !== 'student') {
            $this->setFlash('error', 'Access denied.');
            $this->redirect('login');
            return;
        }
        
        require_once __DIR__ . '/../Service/CertificateService.php';
        $certService = new CertificateService();
        $certificates = $certService->getUserCertificates($_SESSION['user_id']);
        
        $data = [
            'title' => 'My Certificates - APPOLIOS',
            'description' => 'Your earned certificates',
            'certificates' => $certificates,
            'flash' => $this->getFlash()
        ];
        
        $this->view('FrontOffice/student/certificates', $data);
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

        require_once __DIR__ . '/../Model/User.php';
        $userModel = $this->model('User');
        $user = $userModel->findById($_SESSION['user_id']);

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

        require_once __DIR__ . '/../Model/User.php';
        $userModel = $this->model('User');
        $currentUser = $userModel->findById($_SESSION['user_id']);

        // Check if email is taken by another user
        if ($email !== $currentUser['email']) {
            if ($userModel->emailExists($email)) {
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

        if ($userModel->update($_SESSION['user_id'], $updateData)) {
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
     * Mark a lesson as complete
     */
    public function markLessonComplete() {
        if (!$this->isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Please login first']);
            return;
        }

        $lessonId = $_POST['lesson_id'] ?? 0;
        if (!$lessonId) {
            echo json_encode(['success' => false, 'message' => 'Invalid lesson']);
            return;
        }

        require_once __DIR__ . '/../Model/Lesson.php';
        require_once __DIR__ . '/../Model/Chapter.php';
        
        $lessonModel = new Lesson();
        $lesson = $lessonModel->findById($lessonId);
        
        if (!$lesson) {
            echo json_encode(['success' => false, 'message' => 'Lesson not found']);
            return;
        }

        $chapterModel = new Chapter();
        $chapter = $chapterModel->findById($lesson['chapter_id']);
        $courseId = $chapter['course_id'];

        $enrollmentModel = $this->model('Enrollment');
        if (!$enrollmentModel->isEnrolled($_SESSION['user_id'], $courseId)) {
            echo json_encode(['success' => false, 'message' => 'Not enrolled']);
            return;
        }

        require_once __DIR__ . '/../Model/LessonProgress.php';
        $progressModel = new LessonProgress();
        $progressModel->markCompleteNoDuplicate($_SESSION['user_id'], $lessonId);

        $totalLessons = $progressModel->getTotalLessons($courseId);
        $completedCount = $progressModel->getCompletedCount($_SESSION['user_id'], $courseId);
        $progress = $totalLessons > 0 ? round(($completedCount / $totalLessons) * 100) : 0;
        
        $enrollmentModel->updateProgress($_SESSION['user_id'], $courseId, $progress);

        if ($progress >= 100) {
            // Award 150 XP for completing course
            $newLevel = $this->awardXP($_SESSION['user_id'], 150, 'Completed a course: ' . ($course['title'] ?? 'Course'));
            
            require_once __DIR__ . '/../Service/BadgeGenerator.php';
            $badgeGen = new BadgeGenerator();
            
            $courseModel = $this->model('Course');
            $course = $courseModel->findById($courseId);
            
            $completionTime = $this->getCompletionTime($_SESSION['user_id'], $courseId);
            
            $badge = $badgeGen->generateBadge(
                $course['title'] ?? 'Course',
                $course['category'] ?? 'General',
                $completionTime,
                $totalLessons,
                $progress
            );
            
            $badgeModel = $this->model('Badge');
            if (!$badgeModel->hasBadge($_SESSION['user_id'], $badge['name'])) {
                $badgeModel->awardBadge($_SESSION['user_id'], $badge['name'], $badge['icon'], $badge['description']);
            }
        }

        echo json_encode(['success' => true, 'progress' => $progress, 'badge' => $badge ?? null]);
    }
    
    /**
     * Get time since enrollment
     */
    private function getCompletionTime($userId, $courseId) {
        $enrollmentModel = $this->model('Enrollment');
        $enrollment = $enrollmentModel->getEnrollment($userId, $courseId);
        
        if (!$enrollment || empty($enrollment['enrolled_at'])) {
            return 'recently';
        }
        
        $enrolled = new DateTime($enrollment['enrolled_at']);
        $now = new DateTime();
        $diff = $enrolled->diff($now);
        
        if ($diff->d > 0) {
            return $diff->d . ' day' . ($diff->d > 1 ? 's' : '');
        } elseif ($diff->h > 0) {
            return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '');
        } else {
            return 'today';
        }
    }

    /**
     * Get completed lessons for a course
     */
    private function getCompletedLessons($courseId, $userId) {
        require_once __DIR__ . '/../Model/LessonProgress.php';
        $progressModel = new LessonProgress();
        return $progressModel->getCompletedLessons($userId, $courseId);
    }
}