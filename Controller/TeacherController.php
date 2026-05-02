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

        // Get stats
        $myCourses = $courseModel->getCoursesByTeacher($_SESSION['user_id']);
        $stats = [
            'total_courses' => count($myCourses),
            'total_students' => $courseModel->countStudentsByTeacher($_SESSION['user_id']),
            'active_enrollments' => $courseModel->countActiveEnrollmentsByTeacher($_SESSION['user_id']),
            'total_evenements' => count($this->queryEventsByCreator((int)$_SESSION['user_id']))
        ];

        $data = [
            'title' => 'Teacher Dashboard - APPOLIOS',
            'userName' => $_SESSION['user_name'],
            'courses' => $myCourses,
            'stats' => $stats,
            'evenementsStats' => $this->getEvenementsStats((int)$_SESSION['user_id'])
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
     * List all my evenements
     */
    public function evenements() {
        $this->requireTeacher();

        $evenements = $this->queryEventsByCreator((int)$_SESSION['user_id']);
        
        $participationsRaw = $this->queryParticipationsByCreator((int)$_SESSION['user_id']);
        $participationsByEvent = [];
        foreach ($participationsRaw as $p) {
            $participationsByEvent[$p['evenement_id']][] = $p;
        }

        $data = [
            'title' => 'My Evenements - APPOLIOS',
            'evenements' => $evenements,
            'participationsByEvent' => $participationsByEvent,
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
     * Statistics for Teacher's Evenements
     */
    public function statsEvenements() {
        $this->requireTeacher();

        $st = $this->getDb()->prepare(
            "SELECT e.title, e.capacite_max, e.event_date, e.date_debut,
                    (SELECT COUNT(*) FROM evenement_ressources r WHERE r.evenement_id = e.id AND r.type = 'participation' AND r.details = 'approved') as participant_count
             FROM evenements e
             WHERE e.created_by = ?
             ORDER BY COALESCE(e.date_debut, e.event_date, e.created_at) ASC"
        );
        $st->execute([(int)$_SESSION['user_id']]);
        $eventStats = $st->fetchAll();

        // Get type counts for pie/doughnut/bar
        $stTypes = $this->getDb()->prepare("SELECT type, COUNT(*) as count FROM evenements WHERE created_by = ? GROUP BY type");
        $stTypes->execute([(int)$_SESSION['user_id']]);
        $typeStats = $stTypes->fetchAll();

        $data = [
            'title' => 'My Event Statistics - APPOLIOS',
            'eventStats' => $eventStats,
            'typeStats' => $typeStats
        ];

        $this->view('FrontOffice/teacher/stat_evenement', $data);
    }

    /**
     * Export Statistics for Teacher's Evenements as PDF
     */
    public function exportStatsPdf() {
        $this->requireTeacher();

        $st = $this->getDb()->prepare(
            "SELECT e.title, e.capacite_max, e.event_date, e.date_debut,
                    (SELECT COUNT(*) FROM evenement_ressources r WHERE r.evenement_id = e.id AND r.type = 'participation' AND r.details = 'approved') as participant_count
             FROM evenements e
             WHERE e.created_by = ?
             ORDER BY COALESCE(e.date_debut, e.event_date, e.created_at) ASC"
        );
        $st->execute([(int)$_SESSION['user_id']]);
        $eventStats = $st->fetchAll();

        // Get Type Stats
        $stTypes = $this->getDb()->prepare("SELECT type, COUNT(*) as count FROM evenements WHERE created_by = ? GROUP BY type");
        $stTypes->execute([(int)$_SESSION['user_id']]);
        $typeStats = $stTypes->fetchAll();

        // Generate Simple PDF/Printable HTML
        header('Content-Type: text/html; charset=utf-8');
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>My Event Statistics Export - APPOLIOS</title>
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
                    <h1>APPOLIOS - My Event Statistics</h1>
                    <p>Generated by <?= htmlspecialchars($_SESSION['user_name']) ?> on <?= date('Y-m-d H:i:s') ?></p>
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
                        filename:     'APPOLIOS_My_Event_Statistics.pdf',
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

        $result = $this->queryCreateEvent([
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

        $evenement = $this->queryFindEventByIdAndCreator((int) $id, (int) $_SESSION['user_id']);

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

        $existing = $this->queryFindEventByIdAndCreator((int) $id, (int) $_SESSION['user_id']);
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
        $result = $this->queryUpdateEvent((int) $id, [
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
            $this->queryMarkEventPending((int) $id);
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

        $evenement = $this->queryFindEventByIdAndCreator((int) $id, (int) $_SESSION['user_id']);

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

        $result = $this->queryDeleteEvent((int) $id);
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

        $event = $this->queryFindEventByIdAndCreator($eventId, (int) $_SESSION['user_id']);
        if (!$event) {
            $this->setFlash('error', 'Evenement not found or access denied.');
            $this->redirect('teacher/evenements');
            return;
        }

        $editId = (int) ($_GET['edit_id'] ?? 0);
        $editResource = null;
        if ($editId > 0) {
            $candidate = $this->queryFindRessource($editId);
            if ($candidate && (int) $candidate['evenement_id'] === $eventId) {
                $editResource = $candidate;
            }
        }

        $data = [
            'title' => 'Evenement Resources - APPOLIOS',
            'selectedEvenementId' => $eventId,
            'selectedEvenement' => $event,
            'editResource' => $editResource,
            'rules' => $this->queryRessourcesByTypeAndEvent('rule', $eventId),
            'materials' => $this->queryRessourcesByTypeAndEvent('materiel', $eventId),
            'plans' => $this->queryRessourcesByTypeAndEvent('plan', $eventId),
            'participations' => $this->queryParticipationsByEvent($eventId),
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

        // Prepend quantity to details for materiel
        if ($type === 'materiel') {
            $qty = (int) ($_POST['quantite'] ?? 0);
            if ($qty > 0) {
                $details = 'Quantité: ' . $qty . ($details !== '' ? "\n" . $details : '');
            }
        }

        $errors = [];
        if (!in_array($type, ['rule', 'materiel', 'plan'], true)) {
            $errors[] = 'Invalid resource type.';
        }
        if (empty($title)) {
            $errors[] = 'Title is required.';
        }

        $event = $this->queryFindEventByIdAndCreator($eventId, (int) $_SESSION['user_id']);
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

        $createdId = $this->queryCreateRessource([
            'evenement_id' => $eventId,
            'type' => $type,
            'title' => $title,
            'details' => $details,
            'created_by' => $_SESSION['user_id']
        ]);

        $verified = false;
        if ($createdId) {
            $verified = $this->queryRessourceExistsInScope($createdId, $eventId, $type);
        }

        if ($createdId && $verified) {
            $this->queryMarkEventPending($eventId);
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

        $event = $this->queryFindEventByIdAndCreator($eventId, (int) $_SESSION['user_id']);
        if (!$event) {
            $this->setFlash('error', 'Evenement not found or access denied.');
            $this->redirect('teacher/evenements');
            return;
        }

        $resource = $this->queryFindRessource((int) $id);
        if (!$resource || (int) $resource['evenement_id'] !== $eventId) {
            $this->setFlash('error', 'Resource not found for this evenement.');
            $this->redirect('teacher/evenement-ressources&evenement_id=' . $eventId);
            return;
        }

        // Prepend quantity to details for materiel
        if ($resource['type'] === 'materiel') {
            $qty = (int) ($_POST['quantite'] ?? 0);
            if ($qty > 0) {
                $details = 'Quantité: ' . $qty . ($details !== '' ? "\n" . $details : '');
            }
        }

        $result = $this->queryUpdateRessource((int) $id, [
            'title' => $title,
            'details' => $details,
            'evenement_id' => $eventId
        ]);

        if ($result) {
            $this->queryMarkEventPending($eventId);
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

        $event = $this->queryFindEventByIdAndCreator($eventId, (int) $_SESSION['user_id']);
        if (!$event) {
            $this->setFlash('error', 'Evenement not found or access denied.');
            $this->redirect('teacher/evenements');
            return;
        }

        $result = $this->queryDeleteRessource((int) $id, $eventId);

        if ($result) {
            $this->queryMarkEventPending($eventId);
            $this->setFlash('success', 'Resource deleted successfully.');
        } else {
            $this->setFlash('error', 'Failed to delete resource.');
        }

        $this->redirect('teacher/evenement-ressources&evenement_id=' . $eventId);
    }

    // =========================================================================
    // PRIVATE DB QUERY METHODS — Evenements
    // =========================================================================

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

    private function queryEventsByCreator(int $userId): array {
        $st = $this->getDb()->prepare(
            "SELECT e.*, COUNT(r.id) as resource_count
             FROM evenements e
             LEFT JOIN evenement_ressources r ON r.evenement_id = e.id
             WHERE e.created_by = ?
             GROUP BY e.id
             ORDER BY COALESCE(CONCAT(e.date_debut,' ',e.heure_debut), e.event_date) ASC"
        );
        $st->execute([$userId]);
        return $st->fetchAll();
    }

    private function getEvenementsStats(int $userId): array {
        $st = $this->getDb()->prepare(
            "SELECT e.id, e.title, e.titre, e.event_date, e.location,
                    SUM(CASE WHEN r.type = 'participation' AND r.details = 'approved' THEN 1 ELSE 0 END) as participant_count
             FROM evenements e
             LEFT JOIN evenement_ressources r ON r.evenement_id = e.id
             WHERE e.created_by = ?
             GROUP BY e.id
             ORDER BY e.created_at DESC"
        );
        $st->execute([$userId]);
        return $st->fetchAll();
    }

    private function queryFindEventByIdAndCreator(int $id, int $userId): array|false {
        $st = $this->getDb()->prepare(
            "SELECT * FROM evenements WHERE id = ? AND created_by = ? LIMIT 1"
        );
        $st->execute([$id, $userId]);
        return $st->fetch();
    }

    private function queryCreateEvent(array $d): int|false {
        try {
            $st = $this->getDb()->prepare(
                "INSERT INTO evenements
                 (title,titre,description,date_debut,date_fin,heure_debut,heure_fin,
                  lieu,capacite_max,type,statut,approval_status,location,event_date,created_by,created_at)
                 VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW())"
            );
            $st->execute([
                $d['title'],$d['titre'],$d['description'],$d['date_debut'],$d['date_fin'],
                $d['heure_debut'],$d['heure_fin'],$d['lieu'],$d['capacite_max'],
                $d['type'],$d['statut'],$d['approval_status']??'pending',
                $d['location'],$d['event_date'],$d['created_by']
            ]);
            return (int)$this->getDb()->lastInsertId();
        } catch (PDOException $e) { return false; }
    }

    private function queryUpdateEvent(int $id, array $d): bool {
        $st = $this->getDb()->prepare(
            "UPDATE evenements
             SET title=?,titre=?,description=?,date_debut=?,date_fin=?,
                 heure_debut=?,heure_fin=?,lieu=?,capacite_max=?,type=?,
                 statut=?,location=?,event_date=?,updated_at=CURRENT_TIMESTAMP
             WHERE id=?"
        );
        return $st->execute([
            $d['title'],$d['titre'],$d['description'],$d['date_debut'],$d['date_fin'],
            $d['heure_debut'],$d['heure_fin'],$d['lieu'],$d['capacite_max'],
            $d['type'],$d['statut'],$d['location'],$d['event_date'],$id
        ]);
    }

    private function queryDeleteEvent(int $id): bool {
        $st = $this->getDb()->prepare("DELETE FROM evenements WHERE id=?");
        return $st->execute([$id]);
    }

    private function queryMarkEventPending(int $id): void {
        $st = $this->getDb()->prepare(
            "UPDATE evenements SET approval_status='pending', updated_at=CURRENT_TIMESTAMP
             WHERE id=? AND approval_status != 'pending'"
        );
        $st->execute([$id]);
    }

    // =========================================================================
    // PRIVATE DB QUERY METHODS — Ressources
    // =========================================================================

    private function queryFindRessource(int $id): array|false {
        $st = $this->getDb()->prepare("SELECT * FROM evenement_ressources WHERE id = ? LIMIT 1");
        $st->execute([$id]);
        return $st->fetch();
    }

    private function queryRessourcesByTypeAndEvent(string $type, int $eventId): array {
        $st = $this->getDb()->prepare(
            "SELECT r.*, u.name as creator_name, e.title as evenement_title
             FROM evenement_ressources r
             JOIN users u ON r.created_by = u.id
             JOIN evenements e ON r.evenement_id = e.id
             WHERE r.type = ? AND r.evenement_id = ?
             ORDER BY r.created_at DESC"
        );
        $st->execute([$type, $eventId]);
        return $st->fetchAll();
    }

    private function queryCreateRessource(array $d): int|false {
        try {
            $st = $this->getDb()->prepare(
                "INSERT INTO evenement_ressources (evenement_id,type,title,details,created_by,created_at)
                 VALUES (?,?,?,?,?,NOW())"
            );
            $st->execute([$d['evenement_id'],$d['type'],$d['title'],$d['details'],$d['created_by']]);
            return (int)$this->getDb()->lastInsertId();
        } catch (PDOException $e) { return false; }
    }

    private function queryRessourceExistsInScope(int $id, int $eventId, string $type): bool {
        $st = $this->getDb()->prepare(
            "SELECT id FROM evenement_ressources WHERE id=? AND evenement_id=? AND type=? LIMIT 1"
        );
        $st->execute([$id, $eventId, $type]);
        return (bool)$st->fetch();
    }

    private function queryUpdateRessource(int $id, array $d): bool {
        $st = $this->getDb()->prepare(
            "UPDATE evenement_ressources SET title=?,details=?,updated_at=CURRENT_TIMESTAMP
             WHERE id=? AND evenement_id=?"
        );
        return $st->execute([$d['title'],$d['details'],$id,$d['evenement_id']]);
    }

    private function queryDeleteRessource(int $id, int $eventId): bool {
        $st = $this->getDb()->prepare(
            "DELETE FROM evenement_ressources WHERE id=? AND evenement_id=?"
        );
        return $st->execute([$id, $eventId]);
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

    // =========================================================================
    // PUBLIC PARTICIPATION ACTIONS — Teacher approves/rejects
    // =========================================================================

    /**
     * Show all participation requests for teacher's events.
     */
    public function participationRequests() {
        $this->requireTeacher();

        $requests = $this->queryParticipationsByCreator((int) $_SESSION['user_id']);

        $data = [
            'title'    => 'Participation Requests - APPOLIOS',
            'requests' => $requests,
            'flash'    => $this->getFlash()
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

        $participation = $this->queryFindParticipationById((int) $id);
        if (!$participation || !$this->queryEventBelongsToTeacher((int) $participation['evenement_id'], (int) $_SESSION['user_id'])) {
            $this->setFlash('error', 'Participation request not found or access denied.');
            $this->redirect('teacher/participation-requests');
            return;
        }

        if ($this->queryUpdateParticipationStatus((int) $id, 'approved')) {
            $this->setFlash('success', 'Participation approved. A ticket has been sent to the student.');

            // ---- SEND TICKET VIA EMAIL ----
            $stUser = $this->getDb()->prepare('SELECT name, email FROM users WHERE id = ?');
            $stUser->execute([(int)$participation['created_by']]);
            $student = $stUser->fetch();

            $event = $this->queryFindEventByIdAndCreator((int)$participation['evenement_id'], (int) $_SESSION['user_id']);

            if ($student && $event && !empty($student['email'])) {
                $to = $student['email'];
                $subject = "Your Official Ticket: " . $event['title'];
                $date = !empty($event['date_debut']) ? date('M d, Y', strtotime($event['date_debut'])) : 'TBA';
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

                // ---- SEND EMAIL ----
                $this->sendEmail($to, $subject, $message);
                // -------------------------------
            }
        } else {
            $this->setFlash('error', 'Failed to approve participation.');
        }

        $fromEventId = (int)($_POST['from_evenement_id'] ?? 0);
        if (!empty($_POST['from_evenement_list'])) {
            $this->redirect('teacher/evenements');
        } elseif ($fromEventId > 0) {
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

        $participation = $this->queryFindParticipationById((int) $id);
        if (!$participation || !$this->queryEventBelongsToTeacher((int) $participation['evenement_id'], (int) $_SESSION['user_id'])) {
            $this->setFlash('error', 'Participation request not found or access denied.');
            $this->redirect('teacher/participation-requests');
            return;
        }

        $reason = $this->sanitize($_POST['reason'] ?? 'No specific reason provided.');

        if ($this->queryUpdateParticipationStatus((int) $id, 'rejected', $reason)) {
            $this->setFlash('success', 'Participation rejected with reason.');
        } else {
            $this->setFlash('error', 'Failed to reject participation.');
        }

        $fromEventId = (int)($_POST['from_evenement_id'] ?? 0);
        if (!empty($_POST['from_evenement_list'])) {
            $this->redirect('teacher/evenements');
        } elseif ($fromEventId > 0) {
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

        $participation = $this->queryFindParticipationById((int) $id);
        if (!$participation || !$this->queryEventBelongsToTeacher((int) $participation['evenement_id'], (int) $_SESSION['user_id'])) {
            $this->setFlash('error', 'Access denied. You can only delete participations for events you created.');
            $this->redirect('teacher/participation-requests');
            return;
        }

        $st = $this->getDb()->prepare(
            "DELETE FROM evenement_ressources WHERE id = ? AND type = 'participation'"
        );
        $st->execute([(int) $id]);

        if ($st->rowCount() > 0) {
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

    // =========================================================================
    // PRIVATE DB QUERY METHODS — Participation (via evenement_ressources)
    // =========================================================================

    private function queryParticipationsByEvent(int $eventId): array {
        $st = $this->getDb()->prepare(
            "SELECT r.id, r.evenement_id, r.created_by as student_id,
                    r.title as student_name, r.details as status, r.created_at,
                    (SELECT u.email FROM users u WHERE u.id = r.created_by LIMIT 1) as student_email
             FROM evenement_ressources r
             WHERE r.evenement_id = ? AND r.type = 'participation'
             ORDER BY r.created_at DESC"
        );
        $st->execute([$eventId]);
        return $st->fetchAll();
    }

    private function queryParticipationsByCreator(int $teacherId): array {
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

    private function queryFindParticipationById(int $id): array|false {
        $st = $this->getDb()->prepare(
            "SELECT * FROM evenement_ressources WHERE id = ? AND type = 'participation' LIMIT 1"
        );
        $st->execute([$id]);
        return $st->fetch();
    }

    private function queryEventBelongsToTeacher(int $eventId, int $teacherId): bool {
        $st = $this->getDb()->prepare(
            "SELECT id FROM evenements WHERE id = ? AND created_by = ? LIMIT 1"
        );
        $st->execute([$eventId, $teacherId]);
        return (bool) $st->fetch();
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
     * Generate AI dummy resources based on event details
     */
    public function generateAiResources() {
        if (!$this->isTeacher()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid method']);
            exit;
        }

        $evenementId = (int)($_POST['evenement_id'] ?? 0);
        if ($evenementId <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid evenement']);
            exit;
        }

        // Verify ownership
        if (!$this->queryEventBelongsToTeacher($evenementId, (int)$_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Event not found or access denied']);
            exit;
        }

        $evenement = $this->queryFindEventByIdAndCreator($evenementId, (int)$_SESSION['user_id']);
        if (!$evenement) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Event not found']);
            exit;
        }

        $title = $evenement['title'] ?? 'Evénement';
        $type = strtolower($evenement['type'] ?? 'general');
        $heureDebut = $evenement['heure_debut'] ?? '09:00:00';
        $heureFin = $evenement['heure_fin'] ?? '15:00:00';
        $capacite = (int)($evenement['capacite_max'] > 0 ? $evenement['capacite_max'] : rand(20, 100));

        // Attempt to use Gemini API if available
        if (!defined('GEMINI_API_KEY') || empty(GEMINI_API_KEY)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'La clé API Gemini n\'est pas configurée dans config.php.']);
            exit;
        }

        $prompt = "Génère des ressources pour un événement nommé '$title' (Type: $type, Capacité: $capacite, de $heureDebut à $heureFin). Pour la liste des 'materiels', tu dois lister UNIQUEMENT les équipements que l'étudiant/participant doit apporter avec lui (ex: Ordinateur portable, Multiprise, Rallonge, etc.), et non ce que l'événement fournit. Renvoie un JSON strict avec ce format : {\"rules\":[{\"title\":\"...\",\"details\":\"...\"}],\"materiels\":[{\"title\":\"...\",\"quantite\":... ,\"details\":\"...\"}],\"plan\":[{\"title\":\"...\",\"details\":\"...\"}]}. Ne renvoie que le JSON, pas de markdown.";
        
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=' . GEMINI_API_KEY;
        $data = ['contents' => [['parts' => [['text' => $prompt]]]]];
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Erreur de connexion à l\'API: ' . $error_msg]);
            exit;
        }
        curl_close($ch);

        $apiResponse = null;
        if ($response) {
            $json = json_decode($response, true);
            if (isset($json['candidates'][0]['content']['parts'][0]['text'])) {
                $text = $json['candidates'][0]['content']['parts'][0]['text'];
                $text = trim(str_replace(['```json', '```'], '', $text));
                $apiResponse = json_decode($text, true);
            } elseif (isset($json['error'])) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Erreur API: ' . ($json['error']['message'] ?? 'Inconnue')]);
                exit;
            }
        }

        if (!$apiResponse || !isset($apiResponse['rules'], $apiResponse['materiels'], $apiResponse['plan'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'L\'IA a renvoyé un format invalide. Veuillez réessayer.']);
            exit;
        }

        $rules = $apiResponse['rules'];
        $materiels = $apiResponse['materiels'];
        $plan = $apiResponse['plan'];

        // Insert directly into DB
        foreach ($rules as $r) {
            $this->queryCreateRessource([
                'evenement_id' => $evenementId,
                'type' => 'rule',
                'title' => $r['title'],
                'details' => $r['details'],
                'created_by' => $_SESSION['user_id']
            ]);
        }

        foreach ($materiels as $m) {
            $materielDetails = 'Quantité: ' . ($m['quantite'] ?? 1) . "\n" . ($m['details'] ?? '');
            $this->queryCreateRessource([
                'evenement_id' => $evenementId,
                'type' => 'materiel',
                'title' => $m['title'],
                'details' => trim($materielDetails),
                'created_by' => $_SESSION['user_id']
            ]);
        }

        foreach ($plan as $p) {
            $this->queryCreateRessource([
                'evenement_id' => $evenementId,
                'type' => 'plan',
                'title' => $p['title'],
                'details' => $p['details'],
                'created_by' => $_SESSION['user_id']
            ]);
        }

        $this->queryMarkEventPending($evenementId);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Ressources générées et ajoutées avec succès.'
        ]);
        exit;
    }
}
