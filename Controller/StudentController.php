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
require_once __DIR__ . '/UserXPController.php';
require_once __DIR__ . '/AvatarGenerator.php';
require_once __DIR__ . '/EvenementController.php';
require_once __DIR__ . '/CollabHubDelegate.php';

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

        $enrolledCount = $enrollmentModel->getStudentEnrolledCount($_SESSION['user_id']);
        $completedCount = $enrollmentModel->getStudentCompletedCount($_SESSION['user_id']);
        $inProgressCount = $enrollmentModel->getStudentInProgressCount($_SESSION['user_id']);
        $averageProgress = $enrollmentModel->getStudentAverageProgress($_SESSION['user_id']);
        $enrollmentHistory = $enrollmentModel->getStudentEnrollmentHistory($_SESSION['user_id']);
        $progressDetails = $enrollmentModel->getStudentProgressDetails($_SESSION['user_id']);

        $badgeModel = $this->model('Badge');
        $userBadges = $badgeModel->getByUserId($_SESSION['user_id']);
        
        $xpController = new UserXPController();
        $xpData = $xpController->getByUserId($_SESSION['user_id']);
        $totalXP = $xpData['total_xp'] ?? 0;
        $levelInfo = $xpController->getLevel($totalXP);
        
        require_once __DIR__ . '/../Service/CourseRecommendation.php';
        $recommendationModel = new CourseRecommendation();
        $recommendations = $recommendationModel->getRecommendations($_SESSION['user_id'], 4);
        
        $data = [
            'title' => 'My Dashboard - APPOLIOS',
            'description' => 'Student dashboard',
            'userName' => $_SESSION['user_name'],
            'allCourses' => $allCourses,
            'availableCoursesCount' => count($allCourses),
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

    public function groupes(...$params): void
    {
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Please login to access groups.');
            $this->redirect('login');
            return;
        }
        CollabHubDelegate::runGroupes($this, 'student', $params);
    }

    public function discussions(...$params): void
    {
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Please login to access discussions.');
            $this->redirect('login');
            return;
        }
        CollabHubDelegate::runDiscussions($this, 'student', $params);
    }

    public function uploadChatAttachment($discussionId = null): void
    {
        if (!$this->isLoggedIn()) {
            $this->jsonResponse(['ok' => false, 'error' => 'Unauthorized.'], 401);
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['ok' => false, 'error' => 'Method not allowed.'], 405);
        }
        if (empty($_FILES['attachment']) || !is_array($_FILES['attachment'])) {
            $this->jsonResponse(['ok' => false, 'error' => 'No file uploaded.'], 422);
        }

        $result = $this->storeUploadedFile($_FILES['attachment'], __DIR__ . '/../uploads/chat');
        if (!$result['ok']) {
            $this->jsonResponse(['ok' => false, 'error' => $result['error']], 422);
        }

        $url = APP_URL . '/uploads/chat/' . $result['fileName'];
        $mime = (string) ($result['mime'] ?? '');
        $type = 'file';
        if (strpos($mime, 'image/') === 0) {
            $type = 'image';
        }
        if (strpos($mime, 'audio/') === 0) {
            $type = 'audio';
        }
        $ext = strtolower(pathinfo((string) $result['fileName'], PATHINFO_EXTENSION));
        if ($type === 'file' && in_array($ext, ['webm', 'ogg', 'oga', 'opus', 'mp3', 'wav', 'm4a', 'aac', 'flac'], true)) {
            $type = 'audio';
        }

        $this->jsonResponse([
            'ok' => true,
            'data' => [
                'url' => $url,
                'fileName' => $result['originalName'],
                'messageType' => $type,
            ],
        ]);
    }

    public function summarizeText(): void
    {
        if (!$this->isLoggedIn()) {
            $this->jsonResponse(['ok' => false, 'error' => 'Unauthorized.'], 401);
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['ok' => false, 'error' => 'Method not allowed.'], 405);
        }
        $raw = file_get_contents('php://input');
        $body = json_decode((string) $raw, true);
        $text = trim((string) ($body['text'] ?? ''));
        $mode = trim((string) ($body['mode'] ?? ''));
        if ($text === '') {
            $this->jsonResponse(['ok' => false, 'error' => 'text is required.'], 422);
        }

        $text = preg_replace('/\s+/u', ' ', $text);

        $corrected = null;
        $ltPayload = http_build_query([
            'text' => $text,
            'language' => 'fr',
        ], '', '&');
        $ch = curl_init('https://api.languagetool.org/v2/check');
        if ($ch !== false) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $ltPayload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
            curl_setopt($ch, CURLOPT_TIMEOUT, 4);
            $resp = curl_exec($ch);
            $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($resp !== false && $code >= 200 && $code < 300) {
                $json = json_decode((string) $resp, true);
                $matches = is_array($json['matches'] ?? null) ? $json['matches'] : [];
                $corrected = $text;
                usort($matches, function ($a, $b) {
                    return ((int) ($b['offset'] ?? 0)) <=> ((int) ($a['offset'] ?? 0));
                });
                foreach ($matches as $m) {
                    $offset = (int) ($m['offset'] ?? 0);
                    $length = (int) ($m['length'] ?? 0);
                    $repl = (string) (($m['replacements'][0]['value'] ?? '') ?: '');
                    if ($offset < 0 || $length <= 0 || $repl === '') {
                        continue;
                    }
                    $corrected = mb_substr($corrected, 0, $offset) . $repl . mb_substr($corrected, $offset + $length);
                }
            }
        }

        if (is_string($corrected) && trim($corrected) !== '') {
            $text = trim($corrected);
        }

        $sentences = preg_split('/(?<=[.!?])\s+/u', $text);
        $sentences = array_values(array_filter(array_map('trim', $sentences), function ($s) { return $s !== ''; }));

        if (count($sentences) <= 2) {
            $clean = trim(implode(' ', $sentences));
            $clean = preg_replace('/[\s\t]+/u', ' ', $clean);
            $clean = preg_replace('/[!]{2,}/u', '!', $clean);
            $clean = preg_replace('/[?]{2,}/u', '?', $clean);
            $clean = preg_replace('/[.]{3,}/u', '…', $clean);

            $lower = mb_strtolower($clean);
            $summary = $clean;

            $startsWith = function (string $prefix) use ($lower): bool {
                return mb_substr($lower, 0, mb_strlen($prefix)) === $prefix;
            };

            if ($startsWith('comment ')) {
                $rest = trim(mb_substr($clean, mb_strlen('comment ')));
                $rest = rtrim($rest, "?!. ");
                if ($rest !== '') {
                    $summary = 'Comment ' . $rest . '.';
                }
            } elseif ($startsWith('pourquoi ')) {
                $rest = trim(mb_substr($clean, mb_strlen('pourquoi ')));
                $rest = rtrim($rest, "?!. ");
                if ($rest !== '') {
                    $summary = 'Pourquoi ' . $rest . '.';
                }
            } elseif ($startsWith('c\'est quoi ') || $startsWith('c’est quoi ')) {
                $rest = $startsWith('c\'est quoi ') ? trim(mb_substr($clean, mb_strlen("c'est quoi "))) : trim(mb_substr($clean, mb_strlen('c’est quoi ')));
                $rest = rtrim($rest, "?!. ");
                if ($rest !== '') {
                    $summary = $rest . '.';
                }
            }

            $maxLen = 140;
            if (mb_strlen($summary) > $maxLen) {
                $summary = rtrim(mb_substr($summary, 0, $maxLen), " .");
                $summary .= '…';
            }

            $words = preg_split('/\s+/u', trim($summary));
            $words = array_values(array_filter($words, function ($w) { return $w !== ''; }));
            if (count($words) > 10) {
                $summary = implode(' ', array_slice($words, 0, 10));
                $summary = rtrim($summary, " .");
                $summary .= '…';
            }

            $this->jsonResponse(['ok' => true, 'data' => ['summary' => $summary, 'mode' => $mode !== '' ? $mode : 'Short text']]);
        }

        $stop = [
            'the'=>1,'a'=>1,'an'=>1,'and'=>1,'or'=>1,'but'=>1,'for'=>1,'nor'=>1,'so'=>1,'yet'=>1,'of'=>1,'to'=>1,'in'=>1,'on'=>1,'at'=>1,'by'=>1,'with'=>1,
            'is'=>1,'are'=>1,'was'=>1,'were'=>1,'be'=>1,'been'=>1,'being'=>1,'that'=>1,'this'=>1,'these'=>1,'those'=>1,'as'=>1,'it'=>1,'its'=>1,'from'=>1,
            'into'=>1,'about'=>1,'au'=>1,'aux'=>1,'et'=>1,'ou'=>1,'de'=>1,'du'=>1,'des'=>1,'la'=>1,'le'=>1,'les'=>1,'un'=>1,'une'=>1,'est'=>1,'sont'=>1,'pour'=>1,
            'sur'=>1,'dans'=>1,
        ];

        $freq = [];
        foreach ($sentences as $s) {
            preg_match_all("/[a-zA-Z\x{00C0}-\x{017F}']+/u", mb_strtolower($s), $m);
            foreach (($m[0] ?? []) as $w) {
                if (mb_strlen($w) < 3) continue;
                if (isset($stop[$w])) continue;
                $freq[$w] = ($freq[$w] ?? 0) + 1;
            }
        }

        $scored = [];
        foreach ($sentences as $idx => $s) {
            $score = 0;
            preg_match_all("/[a-zA-Z\x{00C0}-\x{017F}']+/u", mb_strtolower($s), $m);
            foreach (($m[0] ?? []) as $w) {
                if (isset($freq[$w])) $score += (int) $freq[$w];
            }
            $scored[] = ['idx' => $idx, 'text' => $s, 'score' => $score];
        }

        usort($scored, function ($a, $b) { return $b['score'] <=> $a['score']; });
        $keep = max(2, min(5, (int) round(count($sentences) * 0.35)));
        $best = array_slice($scored, 0, $keep);
        usort($best, function ($a, $b) { return $a['idx'] <=> $b['idx']; });

        $summary = implode(' ', array_map(function ($x) { return $x['text']; }, $best));
        $this->jsonResponse(['ok' => true, 'data' => ['summary' => $summary, 'mode' => $mode !== '' ? $mode : 'Offline summary']]);
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
        $evenementCtrl = new EvenementController();
        
        $participationMap = $evenementCtrl->getParticipationMap((int) $_SESSION['user_id']);

        $data = [
            'title' => 'Evenements - APPOLIOS',
            'description' => 'Browse upcoming evenements',
            'userName' => $_SESSION['user_name'],
            'evenements' => $evenementModel->findApprovedUpcoming(),
            'participationMap' => $participationMap,
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
        
        $evenementCtrl = new EvenementController();
        $participationMap = $evenementCtrl->getParticipationMap((int) $_SESSION['user_id']);
        $participationStatus = isset($participationMap[$id]) 
            ? (json_decode($participationMap[$id]['details'] ?? '{}', true)['status'] ?? null) 
            : null;

        $data = [
            'title' => (($evenement['titre'] ?? '') ?: ($evenement['title'] ?? 'Evenement')) . ' - APPOLIOS',
            'description' => $evenement['description'] ?? 'Evenement details',
            'evenement' => $evenement,
            'rules' => $grouped['rules'],
            'materiels' => $grouped['materiels'],
            'plans' => $grouped['plans'],
            'participationStatus' => $participationStatus,
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
        
        $allCourses = $courseModel->getAllWithCreator();

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
        
        // Get bookmarked course IDs
        require_once __DIR__ . '/CourseBookmarkController.php';
        $bookmarkController = new CourseBookmarkController();
        $bookmarks = $bookmarkController->getUserBookmarks($_SESSION['user_id']);
        $bookmarkedCourseIds = array_flip(array_column($bookmarks, 'course_id'));
        
        // Get course recommendations
        require_once __DIR__ . '/../Service/CourseRecommendation.php';
        $recommendationModel = new CourseRecommendation();
        $recommendations = $recommendationModel->getRecommendations($_SESSION['user_id'], 4);
        
        $data = [
            'title' => 'Browse Courses - APPOLIOS',
            'description' => 'Explore all available courses',
            'courses' => array_values($allCourses),
            'enrolledIds' => $enrolledIds,
            'bookmarkedCourseIds' => $bookmarkedCourseIds,
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
        
        require_once __DIR__ . '/LessonProgressController.php';
        $progressModel = new LessonProgressController();
        $completedFromDb = $progressModel->getCompletedLessons($_SESSION['user_id'], $id);
        $completedLessons = array_column($completedFromDb, 'lesson_id');
        $_SESSION['completed_lessons'][$id] = $completedLessons;
        
        $enrollment = $isEnrolled ? $enrollmentModel->getEnrollment($_SESSION['user_id'], $id) : null;
        $progress = $enrollment['progress'] ?? 0;

        require_once __DIR__ . '/ReviewController.php';
        $reviewModel = new ReviewController();
        $reviews = $reviewModel->getByCourseId($id);
        $hasReview = $reviewModel->hasUserReviewed($_SESSION['user_id'], $id);
        
        require_once __DIR__ . '/CourseBookmarkController.php';
        $bookmarkController = new CourseBookmarkController();
        $isBookmarked = $bookmarkController->isBookmarked($_SESSION['user_id'], $id);

        $certificate = null;
        if ($progress >= 100) {
            require_once __DIR__ . '/../Service/CertificateService.php';
            $certService = new CertificateService();
            $certificate = $certService->getCertificate($_SESSION['user_id'], $id);
            
            if (!$certificate) {
                // Auto-generate if missing (e.g. if they completed it before certs were added)
                $certificate = $certService->generateCertificate($_SESSION['user_id'], $id);
            }
            
            // Auto-award badge if missing
            require_once __DIR__ . '/../Service/BadgeGenerator.php';
            require_once __DIR__ . '/BadgeController.php';
            $badgeGen = new BadgeGenerator();
            $badgeModel = new BadgeController();
            
            $courseCompletionTime = $this->getCompletionTime($_SESSION['user_id'], $id);
            $totalLessons = $course['lesson_count'] ?? 0;
            
            $badge = $badgeGen->generateBadge(
                $course['title'] ?? 'Course',
                $course['category'] ?? 'General',
                $courseCompletionTime,
                $totalLessons,
                $progress
            );
            
            if ($badge && !$badgeModel->hasBadge($_SESSION['user_id'], $badge['name'])) {
                $badgeModel->awardBadge($_SESSION['user_id'], $badge['name'], $badge['icon'], $badge['description']);
            }
        }

        $data = [
            'title' => $course['title'] . ' - APPOLIOS',
            'description' => $course['description'],
            'course' => $course,
            'isEnrolled' => $isEnrolled,
            'progress' => $progress,
            'completedLessons' => $completedLessons,
            'reviews' => $reviews,
            'hasReview' => $hasReview,
            'isBookmarked' => $isBookmarked,
            'certificate' => $certificate,
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
            // Generate avatar
            $generator = new AvatarGenerator();
            $result = $generator->generateAvatar($faceData, $_SESSION['user_id']);
            echo json_encode($result);
        } catch (Throwable $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    
    /**
     * Get avatar by ID (for display)
     */
    public function getAvatar($avatarId) {
        header('Content-Type: image/svg+xml');
        
        $generator = new AvatarGenerator();
        $result = $generator->getAvatarById($avatarId);
        
        if ($result['success']) {
            echo base64_decode($result['data']);
        } else {
            http_response_code(404);
            echo '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200">
                <rect width="200" height="200" fill="#ccc" rx="20"/>
                <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="white" font-size="72" font-weight="bold" dy=".1em">?</text>
            </svg>';
        }
        exit;
    }

    // Quiz Management Methods

    /**
     * Student quiz list page
     */
    public function quiz()
    {
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Please login to access quizzes.');
            $this->redirect('login');
            return;
        }

        $quizModel = $this->model('Quiz');
        $enrollmentModel = $this->model('Enrollment');
        
        // Get student's enrolled courses
        $enrolledCourses = $enrollmentModel->getStudentCourses($_SESSION['user_id']);
        $courseIds = array_column($enrolledCourses, 'id');
        
        // Get quizzes for enrolled courses
        $quizzes = $quizModel->getByCourseIds($courseIds);
        
        // Get student's quiz flags (favorites, redo)
        $flags = $quizModel->getStudentFlags($_SESSION['user_id']);
        
        // Get student rank and progress
        $rankModel = $this->model('UserRank');
        $rank = $rankModel->getUserRank($_SESSION['user_id']);
        $rankProgress = $rankModel->getRankProgress($_SESSION['user_id']);
        $rankSpark = $rankModel->getRankSpark($_SESSION['user_id']);

        // Apply filter
        $filter = $_GET['filter'] ?? '';
        if ($filter === 'favorites') {
            $quizzes = array_filter($quizzes, function($quiz) use ($flags) {
                return !empty($flags[$quiz['id']]['favorite']);
            });
        } elseif ($filter === 'redo') {
            $quizzes = array_filter($quizzes, function($quiz) use ($flags) {
                return !empty($flags[$quiz['id']]['redo']);
            });
        }

        $data = [
            'title' => 'My Quizzes - APPOLIOS',
            'description' => 'Available quizzes for your courses',
            'quizzes' => $quizzes,
            'flags' => $flags,
            'filter' => $filter,
            'rank' => $rank,
            'rankProgress' => $rankProgress,
            'rankSpark' => $rankSpark,
            'flash' => $this->getFlash()
        ];

        $this->view('FrontOffice/student/quiz_list', $data);
    }

    /**
     * Take quiz page
     */
    public function takeQuiz($quizId)
    {
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Please login to take quizzes.');
            $this->redirect('login');
            return;
        }

        $quizModel = $this->model('Quiz');
        $quiz = $quizModel->getByIdWithQuestions((int) $quizId);

        if (!$quiz) {
            $this->setFlash('error', 'Quiz not found.');
            $this->redirect('student-quiz/quiz');
            return;
        }

        // Check if student is enrolled in the course
        $enrollmentModel = $this->model('Enrollment');
        if (!$enrollmentModel->isEnrolled($_SESSION['user_id'], $quiz['course_id'])) {
            $this->setFlash('error', 'You must be enrolled in this course to take the quiz.');
            $this->redirect('student-quiz/quiz');
            return;
        }

        $data = [
            'title' => 'Take Quiz - APPOLIOS',
            'description' => 'Take quiz: ' . htmlspecialchars($quiz['title']),
            'quiz' => $quiz,
            'flash' => $this->getFlash()
        ];

        $this->view('FrontOffice/student/take_quiz', $data);
    }

    /**
     * Submit quiz attempt
     */
    public function submitQuiz($quizId)
    {
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Please login to submit quizzes.');
            $this->redirect('login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('student-quiz/quiz');
            return;
        }

        $quizModel = $this->model('Quiz');
        $quizAttemptModel = $this->model('QuizAttempt');
        
        $quiz = $quizModel->getById((int) $quizId);
        if (!$quiz) {
            $this->setFlash('error', 'Quiz not found.');
            $this->redirect('student-quiz/quiz');
            return;
        }

        // Check if student is enrolled
        $enrollmentModel = $this->model('Enrollment');
        if (!$enrollmentModel->isEnrolled($_SESSION['user_id'], $quiz['course_id'])) {
            $this->setFlash('error', 'You must be enrolled in this course to submit the quiz.');
            $this->redirect('student-quiz/quiz');
            return;
        }

        $answers = $_POST['answers'] ?? [];
        $timedOut = (int) ($_POST['timed_out'] ?? 0);

        // Submit attempt
        $attempt = $quizAttemptModel->createAttempt($_SESSION['user_id'], (int) $quizId, $answers, $timedOut);

        if ($attempt) {
            $this->logActivity('submit_quiz', "Submitted quiz: {$quiz['title']}");
            $this->redirect('student-quiz/quiz-result/' . $attempt['id']);
        } else {
            $this->setFlash('error', 'Failed to submit quiz.');
            $this->redirect('student-quiz/quiz/' . $quizId);
        }
    }

    /**
     * Quiz result page
     */
    public function quizResult($attemptId)
    {
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Please login to view quiz results.');
            $this->redirect('login');
            return;
        }

        $quizAttemptModel = $this->model('QuizAttempt');
        $attempt = $quizAttemptModel->getAttemptWithDetails((int) $attemptId, $_SESSION['user_id']);

        if (!$attempt) {
            $this->setFlash('error', 'Quiz result not found.');
            $this->redirect('student-quiz/quiz');
            return;
        }

        // Get recommendations
        $quizModel = $this->model('Quiz');
        $recommendations = $quizModel->getRecommendations($_SESSION['user_id'], $attempt['quiz_id'], $attempt['percentage']);

        // Get rank updates
        $rankModel = $this->model('UserRank');
        $rankUpdate = $rankModel->getRankUpdate($_SESSION['user_id'], (int) $attemptId);
        $rankProgress = $rankModel->getRankProgress($_SESSION['user_id']);
        $rankSpark = $rankModel->getRankSpark($_SESSION['user_id']);

        // Get coach insights
        $coachService = $this->service('CoachService');
        $coach = $coachService->getInsights($_SESSION['user_id'], $attempt);

        // Get certificate info if applicable
        $cert = null;
        if ($attempt['percentage'] >= 70) {
            $certModel = $this->model('Certificate');
            $cert = $certModel->generateCertificate($_SESSION['user_id'], (int) $attemptId);
        }

        // Get weak chapters analysis
        $weakChapters = $quizModel->getWeakChapters($_SESSION['user_id']);

        $data = [
            'title' => 'Quiz Result - APPOLIOS',
            'description' => 'Your quiz results',
            'quiz' => $attempt,
            'score' => $attempt['score'],
            'total' => $attempt['total_questions'],
            'percentage' => $attempt['percentage'],
            'timed_out' => $attempt['timed_out'],
            'recommendations' => $recommendations,
            'rank_update' => $rankUpdate,
            'rank_progress' => $rankProgress,
            'rank_spark' => $rankSpark,
            'coach' => $coach,
            'cert' => $cert,
            'weakChapters' => $weakChapters,
            'flash' => $this->getFlash()
        ];

        $this->view('FrontOffice/student/quiz_result', $data);
    }

    /**
     * Quiz history page
     */
    public function quizHistory()
    {
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Please login to view quiz history.');
            $this->redirect('login');
            return;
        }

        $quizAttemptModel = $this->model('QuizAttempt');
        $attempts = $quizAttemptModel->getStudentAttempts($_SESSION['user_id']);

        $data = [
            'title' => 'Quiz History - APPOLIOS',
            'description' => 'Your quiz attempt history',
            'attempts' => $attempts,
            'flash' => $this->getFlash()
        ];

        $this->view('FrontOffice/student/quiz_history', $data);
    }

    /**
     * Toggle quiz favorite
     */
    public function toggleFavoriteQuiz($quizId)
    {
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Please login.');
            $this->redirect('login');
            return;
        }

        $quizModel = $this->model('Quiz');
        $quizModel->toggleFavorite($_SESSION['user_id'], (int) $quizId);
        
        $this->redirect('student-quiz/quiz');
    }

    /**
     * Toggle quiz redo flag
     */
    public function toggleRedoQuiz($quizId)
    {
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Please login.');
            $this->redirect('login');
            return;
        }

        $quizModel = $this->model('Quiz');
        $quizModel->toggleRedo($_SESSION['user_id'], (int) $quizId);
        
        $this->redirect('student-quiz/quiz');
    }

    /**
     * Generate certificate QR code
     */
    public function certQr($attemptId)
    {
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Please login.');
            $this->redirect('login');
            return;
        }

        $certModel = $this->model('Certificate');
        $cert = $certModel->getByAttempt((int) $attemptId, $_SESSION['user_id']);

        if (!$cert) {
            $this->setFlash('error', 'Certificate not found.');
            $this->redirect('student-quiz/quiz');
            return;
        }

        $data = [
            'title' => 'Certificate QR - APPOLIOS',
            'description' => 'Certificate verification QR code',
            'cert' => $cert,
            'flash' => $this->getFlash()
        ];

        $this->view('FrontOffice/student/cert_qr', $data);
    }

    /**
     * View all user certificates
     */
    public function certificates()
    {
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Please login to view your certificates.');
            $this->redirect('login');
            return;
        }

        require_once __DIR__ . '/CertificateController.php';
        $certController = new CertificateController();
        $certificates = $certController->getUserCertificates($_SESSION['user_id']);

        $data = [
            'title' => 'My Certificates - APPOLIOS',
            'description' => 'Your earned certificates',
            'certificates' => $certificates,
            'flash' => $this->getFlash(),
            'studentSidebarActive' => 'certificates'
        ];

        $this->view('FrontOffice/student/certificates', $data);
    }

    public function downloadCertificate($certificateCode = null) {
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Please login first.');
            $this->redirect('login');
            return;
        }
        
        if (!$certificateCode) {
            $certificateCode = $_GET['code'] ?? ($params[0] ?? null);
        }
        
        require_once __DIR__ . '/../Service/CertificateService.php';
        $certService = new CertificateService();
        $certificate = $certService->verifyCertificate($certificateCode);
        
        if (!$certificate) {
            $this->setFlash('error', 'Certificate not found.');
            $this->redirect('student/certificates');
            return;
        }
        
        $studentName = $certificate['student_name'] ?? 'Student';
        $courseName = $certificate['course_title'] ?? 'Course';
        $issuedAt = date('F d, Y', strtotime($certificate['issued_at'] ?? time()));
        
        $html = '<!DOCTYPE html>
<html><head>
<meta charset="UTF-8">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Great+Vibes&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:Georgia,serif;background:#f0f0f0;display:flex;justify-content:center;align-items:center;min-height:100vh;padding:20px}
.certificate{width:800px;background:linear-gradient(135deg,#fff 0%,#fafafa 100%);border:8px double #1a1a2e;border-radius:4px;padding:50px 40px;text-align:center;position:relative}
.corner{position:absolute;font-size:40px;color:#1a1a2e}.corner-tl{top:15px;left:15px}.corner-tr{top:15px;right:15px;transform:rotate(90deg)}.corner-bl{bottom:15px;left:15px;transform:rotate(270deg)}.corner-br{bottom:15px;right:15px;transform:rotate(180deg)}
.platform{color:#666;font-size:14px;letter-spacing:3px;text-transform:uppercase;margin-bottom:10px}
.cert-title{color:#1a1a2e;font-size:42px;font-weight:700;margin-bottom:5px;font-family:Playfair Display,serif}
.cert-subtitle{color:#8b5cf6;font-size:18px;letter-spacing:4px;text-transform:uppercase;font-weight:600}
.divider{width:60%;height:2px;background:linear-gradient(90deg,transparent,#1a1a2e,transparent);margin:30px auto}
.certify-text{color:#666;font-size:16px;font-style:italic;margin-bottom:10px}
.student-name{color:#1a1a2e;font-size:36px;border-bottom:3px solid #eab308;padding-bottom:10px;margin:10px auto;display:inline-block;font-family:Great Vibes,cursive}
.completed-text{color:#666;font-size:16px;margin:20px 0 10px;font-style:italic}
.course-name{color:#8b5cf6;font-size:24px;font-weight:700;margin:10px 0}
.small-divider{width:60%;height:1px;background:#ddd;margin:30px auto}
.footer-area{display:flex;justify-content:space-between;align-items:center;margin-top:20px}
.footer-item{text-align:center}.footer-line{width:120px;border-bottom:2px solid #1a1a2e;margin-bottom:5px}
.footer-label{color:#666;font-size:12px}.footer-value{color:#1a1a2e;font-weight:600}.footer-value.mono{font-family:monospace;font-size:11px}
.trophy{font-size:50px;color:#eab308}
</style></head><body>
<div class="certificate">
<span class="corner corner-tl">❧</span><span class="corner corner-tr">❧</span><span class="corner corner-bl">❧</span><span class="corner corner-br">❧</span>
<div class="platform">APPOLIOS Learning Platform</div>
<h1 class="cert-title">CERTIFICATE</h1>
<div class="cert-subtitle">of Completion</div>
<div class="divider"></div>
<p class="certify-text">This is to certify that</p>
<h2 class="student-name">'.htmlspecialchars($studentName).'</h2>
<p class="completed-text">has successfully completed the course</p>
<h3 class="course-name">'.htmlspecialchars($courseName).'</h3>
<div class="small-divider"></div>
<div class="footer-area">
<div class="footer-item"><div class="footer-line"></div><div class="footer-label">Date of Issue</div><div class="footer-value">'.htmlspecialchars($issuedAt).'</div></div>
<div class="trophy">🏆</div>
<div class="footer-item"><div class="footer-line"></div><div class="footer-label">Certificate ID</div><div class="footer-value mono">'.htmlspecialchars($certificateCode).'</div></div>
</div></div></body></html>';
        
        header('Content-Type: text/html; charset=utf-8');
        header('Content-Disposition: attachment; filename="certificate-'.$certificateCode.'.html"');
        header('Content-Length: ' . strlen($html));
        echo $html;
        exit;
    }

    /**
     * View all user badges
     */
    public function badges()
    {
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Please login to view your badges.');
            $this->redirect('login');
            return;
        }

        require_once __DIR__ . '/BadgeController.php';
        $badgeController = new BadgeController();
        $badges = $badgeController->getByUserId($_SESSION['user_id']);

        $data = [
            'title' => 'My Badges - APPOLIOS',
            'description' => 'Your earned badges',
            'badges' => $badges,
            'flash' => $this->getFlash(),
            'studentSidebarActive' => 'badges'
        ];

        $this->view('FrontOffice/student/badges', $data);
    }

    public function toggleBookmark() {
        header('Content-Type: application/json');
        
        if (!$this->isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Please login.']);
            exit;
        }

        $courseId = $_POST['course_id'] ?? null;
        if (!$courseId) {
            echo json_encode(['success' => false, 'message' => 'Course ID required.']);
            exit;
        }

        require_once __DIR__ . '/CourseBookmarkController.php';
        $bookmarkController = new CourseBookmarkController();
        
        if ($bookmarkController->isBookmarked($_SESSION['user_id'], $courseId)) {
            $bookmarkController->removeBookmark($_SESSION['user_id'], $courseId);
            echo json_encode(['success' => true, 'bookmarked' => false]);
        } else {
            $bookmarkController->addBookmark($_SESSION['user_id'], $courseId);
            echo json_encode(['success' => true, 'bookmarked' => true]);
        }
    }

    public function leaderboard() {
        if (!$this->isLoggedIn()) {
            $this->redirect('login');
            return;
        }

        require_once __DIR__ . '/UserXPController.php';
        $xpController = new UserXPController();

        $db = getConnection();
        $stmt = $db->query("SELECT u.id, u.name, COALESCE(ux.total_xp, COALESCE(ux.xp, 0)) as xp FROM users u LEFT JOIN user_xp ux ON u.id = ux.user_id WHERE u.role = 'student' AND u.is_blocked = 0 ORDER BY xp DESC");
        $students = $stmt->fetchAll();

        $data = [
            'title' => 'Leaderboard - APPOLIOS',
            'leaderboard' => array_slice($students, 0, 20),
            'studentSidebarActive' => 'leaderboard',
            'userName' => $_SESSION['user_name']
        ];

        $this->view('FrontOffice/student/leaderboard', $data);
    }

    public function ranks() {
        if (!$this->isLoggedIn()) {
            $this->redirect('login');
            return;
        }

        require_once __DIR__ . '/UserXPController.php';
        $xpController = new UserXPController();
        $xpData = $xpController->getByUserId($_SESSION['user_id']);
        
        $data = [
            'title' => 'Ranks - APPOLIOS',
            'totalXP' => $xpData['total_xp'] ?? 0,
            'levelInfo' => $xpController->getLevel($xpData['total_xp'] ?? 0),
            'studentSidebarActive' => 'ranks',
            'userName' => $_SESSION['user_name']
        ];

        $this->view('FrontOffice/student/ranks', $data);
    }

    public function notifications() {
        if (!$this->isLoggedIn()) {
            $this->redirect('login');
            return;
        }

        require_once __DIR__ . '/NotificationController.php';
        $notificationController = new NotificationController();
        $notifications = $notificationController->getByUserId($_SESSION['user_id']);
        $notificationController->markAllAsRead($_SESSION['user_id']);

        $data = [
            'title' => 'Notifications - APPOLIOS',
            'notifications' => $notifications,
            'studentSidebarActive' => 'notifications',
            'flash' => $this->getFlash()
        ];

        $this->view('FrontOffice/student/notifications', $data);
    }

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

        require_once __DIR__ . '/ChapterController.php';
        require_once __DIR__ . '/LessonProgressController.php';
        require_once __DIR__ . '/LessonController.php';
        
        $lessonModel = new LessonController();
        $lesson = $lessonModel->findById($lessonId);
        
        if (!$lesson) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Lesson not found.']);
            exit;
        }

        $chapterModel = new ChapterController();
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

        $progressModel = new LessonProgressController();
        $progressModel->markComplete($_SESSION['user_id'], $lessonId);

        $lessonController = new LessonController();
        $allLessons = $lessonController->getByCourseId($actualCourseId);
        $completedFromDb = $progressModel->getCompletedLessons($_SESSION['user_id'], $actualCourseId);
        $completedLessons = array_column($completedFromDb, 'lesson_id');
        
        if (!in_array($lessonId, $completedLessons)) {
            $completedLessons[] = (int)$lessonId;
        }
        
        $_SESSION['completed_lessons'][$actualCourseId] = $completedLessons;

        $totalLessons = count($allLessons);
        $completedCount = count($completedLessons);
        $progress = $totalLessons > 0 ? round(($completedCount / $totalLessons) * 100) : 0;

        $enrollmentModel->updateProgress($_SESSION['user_id'], $actualCourseId, $progress);
        
        require_once __DIR__ . '/UserXPController.php';
        $xpController = new UserXPController();
        $xpController->addXP($_SESSION['user_id'], 15, 'Completed a lesson');

        $badge = null;
        $courseCompletionTime = $this->getCompletionTime($_SESSION['user_id'], $actualCourseId);

        if ($progress >= 100) {
            require_once __DIR__ . '/../Service/BadgeGenerator.php';
            $badgeGen = new BadgeGenerator();
            
            $courseModel = $this->model('Course');
            $course = $courseModel->findById($actualCourseId);
            
            $badge = $badgeGen->generateBadge(
                $course['title'] ?? 'Course',
                $course['category'] ?? 'General',
                $courseCompletionTime,
                $totalLessons,
                $progress
            );
            
            require_once __DIR__ . '/BadgeController.php';
            $badgeModel = new BadgeController();
            if ($badge && !$badgeModel->hasBadge($_SESSION['user_id'], $badge['name'])) {
                $badgeModel->awardBadge($_SESSION['user_id'], $badge['name'], $badge['icon'], $badge['description']);
            }
            
            require_once __DIR__ . '/../Service/CertificateService.php';
            $certService = new CertificateService();
            $certService->generateCertificate($_SESSION['user_id'], $actualCourseId);
            
            $xpController->addXP($_SESSION['user_id'], 150, 'Completed course: ' . ($course['title'] ?? 'Course'));
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true, 
            'progress' => $progress, 
            'total' => $totalLessons, 
            'completed' => $completedCount,
            'badge' => $badge
        ]);
        exit;
    }

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

    public function markLessonComplete() {
        header('Content-Type: application/json');
        
        if (!$this->isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Please login.']);
            exit;
        }

        $lessonId = (int) ($_POST['lesson_id'] ?? 0);
        if (!$lessonId) {
            echo json_encode(['success' => false, 'message' => 'Lesson ID required.']);
            exit;
        }

        require_once __DIR__ . '/LessonProgressController.php';
        $progressModel = new LessonProgressController();
        
        if ($progressModel->markComplete($_SESSION['user_id'], $lessonId)) {
            require_once __DIR__ . '/UserXPController.php';
            $xpController = new UserXPController();
            $xpController->addXP($_SESSION['user_id'], 15, 'Completed a lesson');
            
            echo json_encode(['success' => true, 'message' => 'Lesson completed!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to mark complete.']);
        }
    }

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
        $isEnrolled = $enrollmentModel->isEnrolled($_SESSION['user_id'], $courseId);
        
        if (!$isEnrolled) {
            echo json_encode(['success' => false, 'message' => 'You must enroll first.']);
            exit;
        }

        require_once __DIR__ . '/ReviewController.php';
        $reviewController = new ReviewController();

        if ($reviewController->hasUserReviewed($_SESSION['user_id'], $courseId)) {
            echo json_encode(['success' => false, 'message' => 'You have already reviewed this course.']);
            exit;
        }

        $result = $reviewController->create([
            'user_id' => $_SESSION['user_id'],
            'course_id' => $courseId,
            'rating' => $rating,
            'comment' => $reviewText
        ]);
        
        if (!$result) {
            echo json_encode(['success' => false, 'message' => 'Failed to save review.']);
            exit;
        }
        
        require_once __DIR__ . '/UserXPController.php';
        $xpController = new UserXPController();
        $xpController->addXP($_SESSION['user_id'], 20, 'Wrote a course review');

        echo json_encode(['success' => true, 'message' => 'Thank you for your review!']);
        exit;
    }

    public function getCourseRating() {
        header('Content-Type: application/json');
        
        if (!$this->isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Please login.']);
            exit;
        }

        $courseId = (int) ($_POST['course_id'] ?? 0);
        $rating = (int) ($_POST['rating'] ?? 0);
        $comment = trim($_POST['comment'] ?? '');

        if (!$courseId || $rating < 1 || $rating > 5) {
            echo json_encode(['success' => false, 'message' => 'Invalid data.']);
            exit;
        }

        require_once __DIR__ . '/CourseReviewController.php';
        $reviewModel = new CourseReviewController();
        
        if ($reviewModel->hasUserReviewed($_SESSION['user_id'], $courseId)) {
            echo json_encode(['success' => false, 'message' => 'You already reviewed this course.']);
            exit;
        }

        $result = $reviewModel->create([
            'user_id' => $_SESSION['user_id'],
            'course_id' => $courseId,
            'rating' => $rating,
            'comment' => $comment
        ]);
        
        if ($result) {
            require_once __DIR__ . '/UserXPController.php';
            $xpController = new UserXPController();
            $xpController->addXP($_SESSION['user_id'], 20, 'Reviewed a course');
            
            echo json_encode(['success' => true, 'message' => 'Thank you for your review!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save review.']);
        }
    }

    public function verifyCertificate($certificateCode = null) {
        if (!$certificateCode) {
            $certificateCode = $_GET['code'] ?? ($params[0] ?? null);
        }
        
        if (!$certificateCode) {
            $this->setFlash('error', 'Certificate code is required.');
            $this->redirect('home/index');
            return;
        }
        
        require_once __DIR__ . '/../Service/CertificateService.php';
        $certService = new CertificateService();
        $certificate = $certService->verifyCertificate($certificateCode);
        
        if (!$certificate) {
            $this->setFlash('error', 'Certificate not found or invalid.');
            $this->redirect('home/index');
            return;
        }
        
        $data = [
            'title' => 'Verify Certificate - APPOLIOS',
            'certificate' => $certificate,
            'isValid' => true
        ];
        
        $this->view('FrontOffice/student/certificates', $data);
    }

    // ===== EVENT PARTICIPATION =====

    /**
     * Participate in an event.
     */
    public function participate($eventId) {
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Please login first.');
            $this->redirect('login');
            return;
        }

        $evenementCtrl = new EvenementController();
        $event = $evenementCtrl->findByIdWithCreator((int) $eventId);

        if (!$event || $event['approval_status'] !== 'approved') {
            $this->setFlash('error', 'Event not found or not available.');
            $this->redirect('student/evenements');
            return;
        }

        $result = $evenementCtrl->participate((int) $eventId, (int) $_SESSION['user_id']);

        if ($result) {
            $this->setFlash('success', 'Your participation request has been submitted!');
        } else {
            $this->setFlash('error', 'Failed to register. You may already be registered.');
        }

        $this->redirect('student/evenements');
    }

    /**
     * Cancel participation in an event.
     */
    public function cancelParticipation($eventId) {
        if (!$this->isLoggedIn()) {
            $this->redirect('login');
            return;
        }

        $evenementCtrl = new EvenementController();
        $evenementCtrl->cancelParticipation((int) $eventId, (int) $_SESSION['user_id']);

        $this->setFlash('success', 'Participation cancelled.');
        $this->redirect('student/evenements');
    }

    /**
     * Download/print participation ticket.
     */
    public function downloadTicket($eventId) {
        if (!$this->isLoggedIn()) {
            $this->redirect('login');
            return;
        }

        $evenementCtrl = new EvenementController();
        $event = $evenementCtrl->findByIdWithCreator((int) $eventId);
        $user = $this->findUserById($_SESSION['user_id']);

        $data = [
            'title' => 'Event Ticket - ' . ($event['title'] ?? 'Event'),
            'event' => $event,
            'user' => $user,
            'studentSidebarActive' => 'my-events'
        ];

        $this->view('FrontOffice/student/ticket', $data);
    }

    /**
     * Student's my events page.
     */
    public function myEvents() {
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Please login first.');
            $this->redirect('login');
            return;
        }

        $evenementCtrl = new EvenementController();
        $participations = $evenementCtrl->getParticipationsByUser((int) $_SESSION['user_id']);

        $data = [
            'title' => 'My Events - APPOLIOS',
            'participations' => $participations,
            'studentSidebarActive' => 'my-events',
            'flash' => $this->getFlash()
        ];

        $this->view('FrontOffice/student/my_events', $data);
    }

    /**
     * AI-powered event recommendations.
     */
    public function recommendEvents() {
        if (!$this->isLoggedIn()) {
            echo json_encode(['success' => false, 'error' => 'Login required']);
            return;
        }

        header('Content-Type: application/json');

        $geminiKey = defined('GEMINI_API_KEY') ? GEMINI_API_KEY : '';
        if (empty($geminiKey)) {
            echo json_encode(['success' => false, 'error' => 'AI not configured']);
            return;
        }

        $evenementCtrl = new EvenementController();
        $events = $evenementCtrl->getAllApprovedForStudent();
        $user = $this->findUserById($_SESSION['user_id']);

        $availableEvents = array_map(function($e) {
            return [
                'id' => $e['id'],
                'title' => $e['titre'] ?? ($e['title'] ?? 'Unknown Event')
            ];
        }, $events);

        if (empty($availableEvents)) {
            echo json_encode(['success' => false, 'error' => 'No upcoming events available for recommendation.']);
            return;
        }

        $eventsJson = json_encode($availableEvents);
        
        $prompt = "A student has these events available (with their real IDs): {$eventsJson}.
                   Recommend the best 3 events for this user based on typical interests.
                   You MUST return ONLY a raw JSON array of objects. Do not include markdown formatting like ```json.
                   Format: [{\"id\": <real_id_from_list>, \"title\": \"<event_title>\", \"reason\": \"<short_reason>\"}]";

        $ch = curl_init('https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=' . $geminiKey);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => json_encode([
                'contents' => [['parts' => [['text' => $prompt]]]]
            ])
        ]);
        $response = curl_exec($ch);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            echo json_encode(['success' => false, 'error' => 'cURL Error: ' . $curlError]);
            return;
        }

        $data = json_decode($response, true);
        
        if (isset($data['error'])) {
            echo json_encode(['success' => false, 'error' => 'Gemini API Error: ' . ($data['error']['message'] ?? 'Unknown error')]);
            return;
        }

        $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

        // Clean up any potential markdown code blocks
        $text = preg_replace('/```json\s*/', '', $text);
        $text = preg_replace('/```\s*/', '', $text);
        $text = trim($text);

        if (preg_match('/\[.*\]/s', $text, $match)) {
            $parsed = json_decode($match[0], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                echo json_encode(['success' => true, 'recommendations' => $parsed]);
                return;
            }
        }
        
        echo json_encode(['success' => false, 'error' => 'AI response parse failed: ' . json_last_error_msg() . ' | Raw: ' . substr($response, 0, 300)]);
    }

    /**
     * AI-powered course recommendations.
     */
    public function recommendCourses() {
        if (!$this->isLoggedIn()) {
            echo json_encode(['success' => false, 'error' => 'Login required']);
            return;
        }

        header('Content-Type: application/json');

        $geminiKey = defined('GEMINI_API_KEY') ? GEMINI_API_KEY : '';
        if (empty($geminiKey)) {
            echo json_encode(['success' => false, 'error' => 'AI not configured']);
            return;
        }

        $courseModel = $this->model('Course');
        $allCourses = $courseModel->getAllWithCreator();
        $user = $this->findUserById($_SESSION['user_id']);

        $availableCourses = array_map(function($c) {
            return [
                'id' => $c['id'],
                'title' => $c['title'] ?? 'Unknown Course'
            ];
        }, $allCourses);

        if (empty($availableCourses)) {
            echo json_encode(['success' => false, 'error' => 'No courses available for recommendation.']);
            return;
        }

        // To prevent the prompt from being too large, let's limit the courses we send to max 50
        $availableCourses = array_slice($availableCourses, 0, 50);
        $coursesJson = json_encode($availableCourses);
        
        $prompt = "A student has these educational courses available (with their real IDs): {$coursesJson}.
                   Recommend the best 3 courses for this student based on typical learning interests.
                   You MUST return ONLY a raw JSON array of objects. Do not include markdown formatting like ```json.
                   Format: [{\"id\": <real_id_from_list>, \"title\": \"<course_title>\", \"reason\": \"<short_reason_why_recommended>\"}]";

        $ch = curl_init('https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=' . $geminiKey);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => json_encode([
                'contents' => [['parts' => [['text' => $prompt]]]]
            ])
        ]);
        $response = curl_exec($ch);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            echo json_encode(['success' => false, 'error' => 'cURL Error: ' . $curlError]);
            return;
        }

        $data = json_decode($response, true);
        
        if (isset($data['error'])) {
            echo json_encode(['success' => false, 'error' => 'Gemini API Error: ' . ($data['error']['message'] ?? 'Unknown error')]);
            return;
        }

        $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

        // Clean up any potential markdown code blocks
        $text = preg_replace('/```json\s*/', '', $text);
        $text = preg_replace('/```\s*/', '', $text);
        $text = trim($text);

        if (preg_match('/\[.*\]/s', $text, $match)) {
            $parsed = json_decode($match[0], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                echo json_encode(['success' => true, 'recommendations' => $parsed]);
                return;
            }
        }
        
        echo json_encode(['success' => false, 'error' => 'AI response parse failed: ' . json_last_error_msg() . ' | Raw: ' . substr($response, 0, 300)]);
    }
}