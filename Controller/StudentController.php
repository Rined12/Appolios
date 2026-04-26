<?php
/**
 * APPOLIOS Student Controller
 * Handles student dashboard and course enrollment
 */

require_once __DIR__ . '/../Controller/BaseController.php';
require_once __DIR__ . '/../Model/Course.php';
require_once __DIR__ . '/../Model/Enrollment.php';

class StudentController extends BaseController {

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

    private function queryApprovedEvenements(): array {
        return $this->getDb()->query(
            "SELECT e.*, u.name as creator_name
             FROM evenements e
             JOIN users u ON e.created_by = u.id
             WHERE e.approval_status = 'approved'
             ORDER BY COALESCE(CONCAT(e.date_debut,' ',e.heure_debut), e.event_date) ASC"
        )->fetchAll();
    }

    private function queryEvenementWithCreator(int $id): array|false {
        $st = $this->getDb()->prepare(
            "SELECT e.*, u.name as creator_name, u.role as creator_role
             FROM evenements e
             JOIN users u ON u.id = e.created_by
             WHERE e.id = ? LIMIT 1"
        );
        $st->execute([$id]);
        return $st->fetch();
    }

    private function queryRessourcesByType(string $type, int $evenementId): array {
        $st = $this->getDb()->prepare(
            "SELECT r.*, u.name as creator_name
             FROM evenement_ressources r
             JOIN users u ON r.created_by = u.id
             WHERE r.type = ? AND r.evenement_id = ?
             ORDER BY r.created_at DESC"
        );
        $st->execute([$type, $evenementId]);
        return $st->fetchAll();
    }

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

        $evenements = $this->queryApprovedEvenements();

        $data = [
            'title'            => 'My Dashboard - APPOLIOS',
            'description'      => 'Student evenement dashboard',
            'userName'         => $_SESSION['user_name'],
            'evenements'       => $evenements,
            'participationMap' => $this->queryParticipationMap((int)$_SESSION['user_id']),
            'participations'   => $this->queryMyParticipations((int)$_SESSION['user_id']),
            'flash'            => $this->getFlash()
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

        $data = [
            'title'            => 'Evenements - APPOLIOS',
            'description'      => 'Browse upcoming evenements',
            'userName'         => $_SESSION['user_name'],
            'evenements'       => $this->queryApprovedEvenements(),
            'participationMap' => $this->queryParticipationMap((int)$_SESSION['user_id']),
            'participations'   => $this->queryMyParticipations((int)$_SESSION['user_id']),
            'flash'            => $this->getFlash()
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

        $evenement = $this->queryEvenementWithCreator((int)$id);
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

        $grouped = [
            'rules'     => $this->queryRessourcesByType('rule',     (int)$id),
            'materiels' => $this->queryRessourcesByType('materiel', (int)$id),
            'plans'     => $this->queryRessourcesByType('plan',     (int)$id),
        ];

        $data = [
            'title' => (($evenement['titre'] ?? '') ?: ($evenement['title'] ?? 'Evenement')) . ' - APPOLIOS',
            'description' => $evenement['description'] ?? 'Evenement details',
            'evenement' => $evenement,
            'rules' => $grouped['rules'],
            'materiels' => $grouped['materiels'],
            'plans' => $grouped['plans'],
            'participation' => $this->queryFindParticipation((int)$id, (int)$_SESSION['user_id']),
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
     * My events page (Events student is participating in)
     */
    public function myEvents() {
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Please login to view your events.');
            $this->redirect('login');
            return;
        }

        $studentId = (int) $_SESSION['user_id'];
        $participations = $this->queryMyParticipations($studentId);

        $data = [
            'title'          => 'My Events - APPOLIOS',
            'description'    => 'Events you are participating in',
            'userName'       => $_SESSION['user_name'],
            'participations' => $participations,
            'flash'          => $this->getFlash()
        ];

        $this->view('FrontOffice/student/my_events', $data);
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

    // =========================================================================
    // PUBLIC PARTICIPATION ACTIONS
    // =========================================================================

    /**
     * Student requests to participate in an event.
     */
    public function participate($id) {
        if (!$this->isLoggedIn()) {
            $this->redirect('login');
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('student/evenements');
            return;
        }

        $eventId   = (int) $id;
        $studentId = (int) $_SESSION['user_id'];

        $event = $this->queryApprovedEventById($eventId);
        if (!$event) {
            $this->setFlash('error', 'Event not found or not available.');
            $this->redirect('student/evenements');
            return;
        }

        $existing = $this->queryFindParticipation($eventId, $studentId);
        if ($existing) {
            $this->setFlash('info', 'You already requested participation for this event.');
            $this->redirect('student/evenements');
            return;
        }

        if ($this->queryCreateParticipation($eventId, $studentId)) {
            $this->setFlash('success', 'Participation request sent! Waiting for teacher approval.');
        } else {
            $this->setFlash('error', 'Failed to send participation request.');
        }

        $this->redirect('student/evenements');
    }

    /**
     * Student cancels a pending participation.
     */
    public function cancelParticipation($id) {
        if (!$this->isLoggedIn()) {
            $this->redirect('login');
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('student/evenements');
            return;
        }

        $eventId   = (int) $id;
        $studentId = (int) $_SESSION['user_id'];

        $existing = $this->queryFindParticipation($eventId, $studentId);
        if (!$existing || $existing['details'] !== 'pending') {
            $this->setFlash('error', 'Only pending participation requests can be cancelled.');
            $this->redirect('student/evenements');
            return;
        }

        if ($this->queryCancelParticipation($eventId, $studentId)) {
            $this->setFlash('success', 'Participation request cancelled.');
        } else {
            $this->setFlash('error', 'Failed to cancel participation.');
        }

        $this->redirect('student/evenements');
    }

    // =========================================================================
    // PRIVATE DB QUERY METHODS — Participation (via evenement_ressources)
    // =========================================================================

    /**
     * Returns [evenement_id => status] map for the student.
     * Uses type='participation', details=status in evenement_ressources.
     */
    private function queryParticipationMap(int $studentId): array {
        $st = $this->getDb()->prepare(
            "SELECT evenement_id, details as status
             FROM evenement_ressources
             WHERE type = 'participation' AND created_by = ?"
        );
        $st->execute([$studentId]);
        $map = [];
        foreach ($st->fetchAll() as $row) {
            $map[(int)$row['evenement_id']] = $row['status'];
        }
        return $map;
    }

    private function queryApprovedEventById(int $id): array|false {
        $st = $this->getDb()->prepare(
            "SELECT * FROM evenements WHERE id = ? AND approval_status = 'approved' LIMIT 1"
        );
        $st->execute([$id]);
        return $st->fetch();
    }

    private function queryFindParticipation(int $eventId, int $studentId): array|false {
        $st = $this->getDb()->prepare(
            "SELECT * FROM evenement_ressources
             WHERE evenement_id = ? AND created_by = ? AND type = 'participation' LIMIT 1"
        );
        $st->execute([$eventId, $studentId]);
        return $st->fetch();
    }

    private function queryCreateParticipation(int $eventId, int $studentId): bool {
        try {
            $stUser = $this->getDb()->prepare("SELECT name FROM users WHERE id = ? LIMIT 1");
            $stUser->execute([$studentId]);
            $user = $stUser->fetch();
            $studentName = $user['name'] ?? 'Student';

            $st = $this->getDb()->prepare(
                "INSERT INTO evenement_ressources (evenement_id, type, title, details, created_by, created_at)
                 VALUES (?, 'participation', ?, 'pending', ?, NOW())"
            );
            return $st->execute([$eventId, $studentName, $studentId]);
        } catch (PDOException $e) { return false; }
    }

    private function queryCancelParticipation(int $eventId, int $studentId): bool {
        $st = $this->getDb()->prepare(
            "DELETE FROM evenement_ressources
             WHERE evenement_id = ? AND created_by = ? AND type = 'participation' AND details = 'pending'"
        );
        return $st->execute([$eventId, $studentId]);
    }

    /**
     * Fetch events where the student has a participation record.
     */
    private function queryMyParticipations(int $studentId): array {
        $st = $this->getDb()->prepare(
            "SELECT e.*, er.id as p_id, er.details as p_status, er.rejection_reason, er.created_at as p_date, er.updated_at as p_update_date, u.name as creator_name
             FROM evenements e
             JOIN evenement_ressources er ON e.id = er.evenement_id
             JOIN users u ON e.created_by = u.id
             WHERE er.type = 'participation' AND er.created_by = ?
             ORDER BY er.created_at DESC"
        );
        $st->execute([$studentId]);
        return $st->fetchAll();
    }

    public function downloadTicket($pId) {
        if (!$this->isLoggedIn()) { $this->redirect('auth/login'); return; }
        $pId = (int)$pId;
        $studentId = $_SESSION['user_id'];
        $st = $this->getDb()->prepare(
            "SELECT er.*, e.title as event_title, e.location as event_location, 
                    COALESCE(CONCAT(e.date_debut, ' ', e.heure_debut), e.event_date) as event_full_date,
                    u.name as student_name, u.email as student_email
             FROM evenement_ressources er
             JOIN evenements e ON er.evenement_id = e.id
             JOIN users u ON er.created_by = u.id
             WHERE er.id = ? AND er.created_by = ? AND er.type = 'participation' AND er.details = 'approved'
             LIMIT 1"
        );
        $st->execute([$pId, $studentId]);
        $ticket = $st->fetch();
        if (!$ticket) {
            $this->setFlash('error', 'Ticket not found or not approved yet.');
            $this->redirect('student/my-participations');
            return;
        }

        // Create QR Data
        $qrData = "Ticket ID: " . str_pad($ticket['id'], 6, '0', STR_PAD_LEFT) . "\n"
                . "Event: " . $ticket['event_title'] . "\n"
                . "Attendee: " . $ticket['student_name'] . "\n"
                . "Status: Approved by Appolios";
        $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($qrData);

        header('Content-Type: text/html; charset=utf-8');
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Event Ticket - <?= htmlspecialchars($ticket['event_title']) ?></title>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
            <style>
                @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
                * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
                body { background: #f1f5f9; padding: 40px; display: flex; justify-content: center; min-height: 100vh; align-items: center; }
                .ticket-container { background: white; width: 700px; border-radius: 24px; overflow: hidden; box-shadow: 0 20px 50px rgba(0,0,0,0.1); display: flex; position: relative; }
                .ticket-left { flex: 1; padding: 40px; border-right: 2px dashed #e2e8f0; }
                .ticket-right { width: 220px; background: #2B4865; color: white; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 20px; text-align: center; }
                .brand { color: #548CA8; font-weight: 800; font-size: 1.2rem; margin-bottom: 30px; display: block; }
                .event-badge { background: #e0f2fe; color: #0369a1; padding: 6px 14px; border-radius: 100px; font-size: 0.75rem; font-weight: 700; margin-bottom: 15px; display: inline-block; }
                h1 { font-size: 2rem; color: #1e293b; line-height: 1.2; margin-bottom: 25px; }
                .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 30px; }
                .info-item label { display: block; font-size: 0.7rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; margin-bottom: 5px; }
                .info-item span { display: block; font-size: 1rem; color: #334155; font-weight: 600; }
                .qr-box { width: 140px; height: 140px; background: white; border-radius: 12px; margin-bottom: 20px; display: flex; align-items: center; justify-content: center; overflow: hidden; border: 6px solid #355C7D; }
                .qr-box img { width: 100%; height: 100%; object-fit: contain; }
                .status-approved { color: #10b981; font-weight: 800; font-size: 1.2rem; transform: rotate(-15deg); border: 3px solid #10b981; padding: 5px 15px; border-radius: 8px; margin-top: 20px; text-transform: uppercase; }
                .ticket-id { font-size: 0.6rem; color: rgba(255,255,255,0.5); margin-top: auto; font-family: monospace; }
                .ticket-container::before, .ticket-container::after { content: ''; position: absolute; width: 30px; height: 30px; background: #f1f5f9; border-radius: 50%; left: 465px; }
                .ticket-container::before { top: -15px; }
                .ticket-container::after { bottom: -15px; }
                .loading-msg { position: fixed; top: 20px; left: 50%; transform: translateX(-50%); background: #2B4865; color: white; padding: 12px 24px; border-radius: 12px; font-weight: 700; box-shadow: 0 10px 20px rgba(0,0,0,0.1); z-index: 100; transition: opacity 0.3s; }
            </style>
        </head>
        <body>
            <div id="loadingMsg" class="loading-msg">Téléchargement du PDF en cours...</div>
            <div id="ticket-content" class="ticket-container">
                <div class="ticket-left">
                    <span class="brand">APPOLIOS</span>
                    <div class="event-badge">Official Event Pass</div>
                    <h1><?= htmlspecialchars($ticket['event_title']) ?></h1>
                    <div class="info-grid">
                        <div class="info-item" style="grid-column: 1 / -1;"><label>Event</label><span><?= htmlspecialchars($ticket['event_title']) ?></span></div>
                        <div class="info-item"><label>Attendee</label><span><?= htmlspecialchars($ticket['student_name']) ?></span></div>
                        <div class="info-item"><label>Date & Time</label><span><?= date('M d, Y - H:i', strtotime($ticket['event_full_date'])) ?></span></div>
                        <div class="info-item"><label>Location</label><span><?= htmlspecialchars($ticket['event_location'] ?: 'To be announced') ?></span></div>
                        <div class="info-item"><label>Ticket Type</label><span>Student Pass</span></div>
                    </div>
                </div>
                <div class="ticket-right">
                    <div class="qr-box">
                        <img src="<?= $qrUrl ?>" alt="Ticket QR Code">
                    </div>
                    <div style="font-size: 0.65rem; font-weight: 700; letter-spacing: 1px; color: #94a3b8; margin-top: -10px;">SCAN TO VALIDATE</div>
                    <div class="status-approved">Approved</div>
                    <div class="ticket-id">#ID-<?= str_pad($ticket['id'], 6, '0', STR_PAD_LEFT) ?></div>
                </div>
            </div>
            <script>
                window.onload = function() {
                    const element = document.getElementById('ticket-content');
                    const opt = {
                        margin:       0.5,
                        filename:     'ticket_event_<?= (int)$ticket['evenement_id'] ?>.pdf',
                        image:        { type: 'jpeg', quality: 0.98 },
                        html2canvas:  { scale: 2, useCORS: true },
                        jsPDF:        { unit: 'in', format: 'letter', orientation: 'landscape' }
                    };
                    
                    // Generate PDF
                    html2pdf().set(opt).from(element).save().then(function() {
                        document.getElementById('loadingMsg').innerText = "Téléchargement terminé. Vous pouvez fermer cet onglet.";
                        setTimeout(() => window.close(), 3000); // Attempt to close window after 3s
                    });
                };
            </script>
        </body>
        </html>
        <?php
        exit;
    }
}