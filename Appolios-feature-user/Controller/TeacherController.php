<?php
/**
 * APPOLIOS Teacher Controller
 * Handles teacher-specific functionality
 */

require_once __DIR__ . '/../Controller/BaseController.php';
require_once __DIR__ . '/../Model/User.php';
require_once __DIR__ . '/../Model/Course.php';
require_once __DIR__ . '/../Model/Chapter.php';
require_once __DIR__ . '/../Model/Lesson.php';
require_once __DIR__ . '/../Model/Evenement.php';
require_once __DIR__ . '/../Model/EvenementRessource.php';

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

        $userModel = $this->model('User');
        $courseModel = $this->model('Course');
        $evenementModel = $this->model('Evenement');

        // Get teacher analytics
        $teacherId = $_SESSION['user_id'];
        $myCourses = $courseModel->getCoursesByTeacher($teacherId);
        
        $totalCourses = $courseModel->getTeacherTotalCourses($teacherId);
        $publishedCourses = $courseModel->getTeacherPublishedCourses($teacherId);
        $pendingCourses = $courseModel->getTeacherPendingCourses($teacherId);
        $totalStudents = $courseModel->getTeacherTotalStudents($teacherId);
        $totalEarnings = $courseModel->getTeacherTotalEarnings($teacherId);
        $coursePerformance = $courseModel->getTeacherCoursePerformance($teacherId);
        $monthlyEnrollments = $courseModel->getTeacherMonthlyEnrollments($teacherId);
        
        // Calculate average rating
        $totalReviews = 0;
        $totalRating = 0;
        foreach ($myCourses as $course) {
            require_once __DIR__ . '/../Model/Review.php';
            $reviewModel = new Review();
            $reviews = $reviewModel->getByCourseId($course['id']);
            $totalReviews += count($reviews);
            if (!empty($reviews)) {
                $ratingSum = array_sum(array_column($reviews, 'rating'));
                $totalRating += $ratingSum / count($reviews);
            }
        }
        $avgRating = count($myCourses) > 0 ? round($totalRating / count($myCourses), 1) : 0;

        $stats = [
            'total_courses' => $totalCourses,
            'published_courses' => $publishedCourses,
            'pending_courses' => $pendingCourses,
            'total_students' => $totalStudents,
            'total_earnings' => $totalEarnings,
            'total_reviews' => $totalReviews,
            'avg_rating' => $avgRating,
            'active_enrollments' => 0,
            'total_evenements' => count($evenementModel->getByCreator($teacherId))
        ];

        $data = [
            'title' => 'Teacher Dashboard - APPOLIOS',
            'userName' => $_SESSION['user_name'],
            'courses' => $myCourses,
            'stats' => $stats,
            'coursePerformance' => $coursePerformance,
            'monthlyEnrollments' => $monthlyEnrollments
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
        
        // Get full course data with chapters for each course
        $coursesWithChapters = [];
        foreach ($courses as $course) {
            $fullCourse = $courseModel->getWithChapters($course['id']);
            $coursesWithChapters[] = $fullCourse;
        }

        $data = [
            'title' => 'My Courses - APPOLIOS',
            'courses' => $coursesWithChapters
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
            'errors' => $_SESSION['errors'] ?? [],
            'old' => $_SESSION['old'] ?? [],
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

        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $courseType = $_POST['course_type'] ?? null;
        $categoryId = $_POST['category_id'] ?? null;
        
        // Handle course image upload
        $image = $_POST['image'] ?? '';
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
        
        // Generate AI image if no image provided
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
            $_SESSION['errors'] = ['title' => 'Title is required'];
            $_SESSION['old'] = $_POST;
            $this->redirect('teacher/add-course');
            return;
        }

        if (empty($description)) {
            $_SESSION['errors'] = ['description' => 'Description is required'];
            $_SESSION['old'] = $_POST;
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
            'status' => 'pending',
            'created_by' => $_SESSION['user_id']
        ]);

        if ($courseId) {
            $this->saveCourseChapters($courseId, $_POST);
            $this->saveCourseBadges($courseId, $_POST);
            $this->setFlash('success', 'Course submitted for admin approval!');
        } else {
            $this->setFlash('error', 'Failed to create course');
        }

        $this->redirect('teacher/courses');
    }

    /**
     * Save chapters and lessons for a course
     */
    private function saveCourseChapters($courseId, $postData) {
        $chapterModel = $this->model('Chapter');
        $lessonModel = $this->model('Lesson');

        $chapters = $postData['chapters'] ?? [];

        if (empty($chapters)) {
            return;
        }

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

            // Save lessons for this chapter
            $lessons = $chapterData['lessons'] ?? [];
            foreach ($lessons as $lessonIndex => $lessonData) {
                $lessonTitle = $this->sanitize($lessonData['title'] ?? '');
                if (empty($lessonTitle)) continue;

                $lessonType = $lessonData['lesson_type'] ?? 'text';
                $content = $this->sanitize($lessonData['content'] ?? '');
                $videoUrl = '';
                $pdfPath = '';
                
                // Handle PDF upload
                $chapterKey = $chapterIndex;
                $lessonKey = $lessonIndex;
                
                if (isset($_FILES['lessons'])) {
                    // Check for new PDF uploads
                    $pdfFiles = $_FILES['lessons'][$chapterKey] ?? [];
                    if (isset($pdfFiles[$lessonKey]['pdf_file'])) {
                        $fileData = $pdfFiles[$lessonKey]['pdf_file'];
                        $filename = $fileData['name'] ?? '';
                        $tmpName = $fileData['tmp_name'] ?? '';
                        $error = $fileData['error'] ?? 4;
                        
                        if (!empty($filename) && $error === 0 && !empty($tmpName)) {
                            $uploadDir = __DIR__ . '/../View/assets/pdfs/';
                            @mkdir($uploadDir, 0777, true);
                            $newFilename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($filename));
                            $targetPath = $uploadDir . $newFilename;
                            
                            if (move_uploaded_file($tmpName, $targetPath)) {
                                $pdfPath = 'View/assets/pdfs/' . $newFilename;
                            }
                        }
                    }
                }
                
                // Use existing PDF path if no new upload
                if (empty($pdfPath) && !empty($lessonData['pdf_path'])) {
                    $pdfPath = $lessonData['pdf_path'];
                }
                
                if ($lessonType === 'video') {
                    $videoUrl = $this->sanitize($lessonData['video_url'] ?? '');
                }

                $lessonModel->createLesson([
                    'chapter_id' => $chapterId,
                    'title' => $lessonTitle,
                    'content' => $content,
                    'video_url' => $videoUrl,
                    'pdf_path' => $pdfPath,
                    'lesson_type' => $lessonType,
                    'lesson_order' => $lessonIndex + 1
                ]);
            }
        }
    }

    /**
     * Delete all chapters and lessons for a course
     */
    private function deleteCourseChapters($courseId) {
        $chapterModel = $this->model('Chapter');
        $lessonModel = $this->model('Lesson');
        $chapters = $chapterModel->getByCourseId($courseId);

        foreach ($chapters as $chapter) {
            $lessonModel->deleteByChapterId($chapter['id']);
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
        $image = $this->sanitize($_POST['image'] ?? '');
        $courseType = $_POST['course_type'] ?? null;
        $categoryId = $_POST['category_id'] ?? null;
        
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
        
        $status = $_POST['status'] ?? $course['status'] ?? 'approved';

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
            'status' => $status
        ]);

        if ($result) {
            // Delete existing chapters and lessons, then save new ones
            $this->deleteCourseChapters($id);
            $this->saveCourseChapters($id, $_POST);

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
        $course = $courseModel->getWithChapters($id);

        // Check if course belongs to this teacher
        if (!$course || $course['created_by'] != $_SESSION['user_id']) {
            $this->setFlash('error', 'Course not found or access denied');
            $this->redirect('teacher/courses');
            return;
        }

        // Get enrolled students with progress
        $enrolledStudents = $courseModel->getEnrolledStudents($id);

        $data = [
            'title' => htmlspecialchars($course['title']) . ' - APPOLIOS',
            'course' => $course,
            'enrolledStudents' => $enrolledStudents
        ];

        $this->view('FrontOffice/teacher/course_detail', $data);
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

        if ($userModel->update($_SESSION['user_id'], $updateData)) {
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
}
