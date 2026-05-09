<?php
/**
 * APPOLIOS Home Controller
 * Handles public pages
 */

require_once __DIR__ . '/../Controller/BaseController.php';
require_once __DIR__ . '/../Model/Course.php';

class HomeController extends BaseController {

    /**
     * Home page (Landing page)
     * Redirects logged-in users to their respective dashboards
     */
    public function index() {
        // If user is logged in, redirect admin/teacher to their dashboards.
        // Students can access the public home page design.
        if ($this->isLoggedIn()) {
            if ($_SESSION['role'] === 'admin') {
                $this->redirect('admin/dashboard');
                return;
            } elseif ($_SESSION['role'] === 'teacher') {
                $this->redirect('teacher/dashboard');
                return;
            }
        }

        // Public landing page for non-logged users
        $data = [
            'title' => 'Welcome to APPOLIOS',
            'description' => 'APPOLIOS E-Learning Platform - Learn Anytime, Anywhere'
        ];

        $this->view('FrontOffice/home/index', $data);
    }

    /**
     * About page
     */
    public function about() {
        $data = [
            'title' => 'About APPOLIOS',
            'description' => 'Learn more about our e-learning platform'
        ];

        $this->view('FrontOffice/home/about', $data);
    }

    /**
     * Contact page
     */
    public function contact() {
        $data = [
            'title' => 'Contact Us',
            'description' => 'Get in touch with APPOLIOS',
            'flash' => $this->getFlash()
        ];

        $this->view('FrontOffice/home/contact', $data);
    }

    /**
     * Handle contact form submission
     */
    public function submitContact() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('contact');
            return;
        }

        $name = $this->sanitize($_POST['name'] ?? '');
        $email = $this->sanitize($_POST['email'] ?? '');
        $subject = $this->sanitize($_POST['subject'] ?? '');
        $message = $this->sanitize($_POST['message'] ?? '');

        // Validation
        $errors = [];
        if (empty($name)) {
            $errors[] = 'Your name is required.';
        }
        if (empty($email)) {
            $errors[] = 'Email address is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        }
        if (empty($subject)) {
            $errors[] = 'Subject is required.';
        }
        if (empty($message)) {
            $errors[] = 'Message is required.';
        }

        if (!empty($errors)) {
            $this->setFlash('error', implode('<br>', $errors));
            $this->redirect('contact');
            return;
        }

        // Save to database
        require_once __DIR__ . '/../Model/ContactMessage.php';
        $contactModel = $this->model('ContactMessage');

        $result = $contactModel->createMessage([
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'message' => $message
        ]);

        if ($result) {
            $this->setFlash('success', 'Thank you! Your message has been sent successfully. We will get back to you soon.');
        } else {
            $this->setFlash('error', 'Sorry, there was an error sending your message. Please try again.');
        }

        $this->redirect('contact');
    }

    /**
     * Privacy Policy page
     */
    public function privacy() {
        $data = [
            'title' => 'Privacy Policy',
            'description' => 'APPOLIOS Privacy Policy'
        ];

        $this->view('FrontOffice/home/privacy', $data);
    }

    /**
     * Terms page
     */
    public function terms() {
        $data = [
            'title' => 'Terms of Service',
            'description' => 'APPOLIOS Terms of Service'
        ];

        $this->view('FrontOffice/home/terms', $data);
    }

    /**
     * Courses listing page (public - for students and visitors)
     * Admin and Teacher are redirected to their dashboards
     */
    public function courses() {
        // Redirect admin and teacher to their dashboards
        if ($this->isLoggedIn()) {
            if ($_SESSION['role'] === 'admin') {
                $this->redirect('admin/dashboard');
                return;
            } elseif ($_SESSION['role'] === 'teacher') {
                $this->redirect('teacher/dashboard');
                return;
            }
            // Students can view courses
        }

        $courseModel = $this->model('Course');
        $searchTerm = $_GET['search'] ?? '';
        $filter = $_GET['filter'] ?? '';

        if (!empty($searchTerm)) {
            $courses = $courseModel->searchCourses($searchTerm);
        } elseif ($filter === 'free') {
            $courses = $courseModel->getCoursesByPrice(true);
        } elseif ($filter === 'paid') {
            $courses = $courseModel->getCoursesByPrice(false);
        } else {
            $courses = $courseModel->getAllWithCreator();
            // Filter only approved courses
            $courses = array_filter($courses, function($course) {
                return ($course['status'] ?? '') === 'approved';
            });
            $courses = array_values($courses);
        }

        $data = [
            'title' => 'All Courses',
            'description' => 'Explore our course catalog',
            'courses' => $courses
        ];

        $this->view('FrontOffice/home/courses', $data);
    }

    /**
     * Verify a quiz certificate
     */
    public function verifyCert() {
        $token = (string) ($_GET['token'] ?? '');
        if ($token === '') {
            $this->view('FrontOffice/home/verify_cert', [
                'ok' => false,
                'reason' => 'Le lien de vérification est incomplet.'
            ]);
            return;
        }

        $parts = explode('.', $token);
        if (count($parts) !== 2) {
            $this->view('FrontOffice/home/verify_cert', [
                'ok' => false,
                'reason' => 'Le format du jeton est invalide.'
            ]);
            return;
        }

        $b64 = $parts[0];
        $sigB64 = $parts[1];

        $expectedSig = hash_hmac('sha256', $b64, (string) APP_QR_SECRET, true);
        $expectedSigB64 = rtrim(strtr(base64_encode($expectedSig), '+/', '-_'), '=');

        if (!hash_equals($expectedSigB64, $sigB64)) {
            $this->view('FrontOffice/home/verify_cert', [
                'ok' => false,
                'reason' => 'La signature du certificat est invalide ou corrompue.'
            ]);
            return;
        }

        $json = base64_decode(strtr($b64, '-_', '+/'));
        if ($json === false) {
            $this->view('FrontOffice/home/verify_cert', [
                'ok' => false,
                'reason' => 'Impossible de lire les données du certificat.'
            ]);
            return;
        }

        $payload = json_decode($json, true);
        if (!is_array($payload) || !isset($payload['uid'], $payload['qid'])) {
            $this->view('FrontOffice/home/verify_cert', [
                'ok' => false,
                'reason' => 'Les données du certificat sont incomplètes.'
            ]);
            return;
        }

        require_once __DIR__ . '/../config/database.php';
        $db = getConnection();

        $stmtU = $db->prepare("SELECT name FROM users WHERE id = ?");
        $stmtU->execute([(int) $payload['uid']]);
        $user = $stmtU->fetch();

        $stmtQ = $db->prepare("SELECT title FROM quizzes WHERE id = ?");
        $stmtQ->execute([(int) $payload['qid']]);
        $quiz = $stmtQ->fetch();

        // Get score and total from attempt if available in db, else use payload pct
        $score = $payload['pct'] ?? 0;
        $total = 100;
        if (isset($payload['aid'])) {
            try {
                $stmtA = $db->prepare("SELECT score, total_questions as total FROM quiz_attempts WHERE id = ?");
                $stmtA->execute([(int) $payload['aid']]);
                if ($attemptDb = $stmtA->fetch()) {
                    $score = $attemptDb['score'] ?? $score;
                    $total = $attemptDb['total'] ?? $total;
                }
            } catch (PDOException $e) {
                // Table might not exist or columns might differ, ignore gracefully
            }
        }

        $attemptView = [
            'student_name' => $user ? $user['name'] : 'Étudiant',
            'quiz_title' => $quiz ? $quiz['title'] : 'Quiz',
            'score' => $score,
            'total' => $total,
            'percentage' => $payload['pct'] ?? 0,
            'submitted_at' => date('Y-m-d H:i:s', $payload['iat'] ?? time())
        ];

        $this->view('FrontOffice/home/verify_cert', [
            'ok' => true,
            'reason' => 'Ce certificat est valide et authentique.',
            'attempt' => $attemptView
        ]);
    }

    /**
     * 404 Not Found
     */
    public function notFound() {
        http_response_code(404);
        $data = [
            'title' => '404 - Page Not Found',
            'description' => 'The page you are looking for does not exist'
        ];

        $this->view('FrontOffice/errors/404', $data);
    }
}