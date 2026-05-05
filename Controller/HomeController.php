<?php
/**
 * APPOLIOS Home Controller
 * Handles public pages
 */

require_once __DIR__ . '/../Controller/BaseController.php';
require_once __DIR__ . '/../Model/Course.php';

class HomeController extends BaseController {

    public function verifyCert() {
        $token = (string) ($_GET['token'] ?? '');
        $token = trim(rawurldecode($token));

        $data = [
            'title' => 'Vérification certificat - ' . APP_NAME,
            'ok' => false,
            'reason' => 'Certificat invalide.',
            'attempt' => null,
        ];

        if ($token === '' || strpos($token, '.') === false) {
            $this->view('FrontOffice/home/verify_cert', $data);
            return;
        }

        [$b64, $sigB64] = explode('.', $token, 2);
        $calc = hash_hmac('sha256', $b64, (string) APP_QR_SECRET, true);
        $calcB64 = rtrim(strtr(base64_encode($calc), '+/', '-_'), '=');

        if (!hash_equals($calcB64, $sigB64)) {
            $data['reason'] = 'Signature invalide.';
            $this->view('FrontOffice/home/verify_cert', $data);
            return;
        }

        $b64Std = strtr($b64, '-_', '+/');
        $padLen = (4 - (strlen($b64Std) % 4)) % 4;
        if ($padLen > 0) {
            $b64Std .= str_repeat('=', $padLen);
        }
        $json = base64_decode($b64Std, true);
        if (!is_string($json) || $json === '') {
            $data['reason'] = 'Token illisible.';
            $this->view('FrontOffice/home/verify_cert', $data);
            return;
        }

        $payload = json_decode($json, true);
        if (!is_array($payload)) {
            $data['reason'] = 'Token invalide.';
            $this->view('FrontOffice/home/verify_cert', $data);
            return;
        }

        $exp = (int) ($payload['exp'] ?? 0);
        if ($exp > 0 && time() > $exp) {
            $data['reason'] = 'Certificat expiré.';
            $this->view('FrontOffice/home/verify_cert', $data);
            return;
        }

        $aid = (int) ($payload['aid'] ?? 0);
        $uid = (int) ($payload['uid'] ?? 0);
        $qid = (int) ($payload['qid'] ?? 0);
        $pct = (int) ($payload['pct'] ?? 0);

        if ($aid <= 0 || $uid <= 0 || $qid <= 0) {
            $data['reason'] = 'Certificat incomplet.';
            $this->view('FrontOffice/home/verify_cert', $data);
            return;
        }

        $db = $this->db();
        $sql = "SELECT a.*, q.title AS quiz_title, u.name AS student_name
                FROM quiz_attempts a
                JOIN quizzes q ON q.id = a.quiz_id
                JOIN users u ON u.id = a.user_id
                WHERE a.id = ?
                LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([(int) $aid]);
        $attempt = $stmt->fetch();
        if (!$attempt) {
            $data['reason'] = 'Tentative introuvable.';
            $this->view('FrontOffice/home/verify_cert', $data);
            return;
        }

        if ((int) ($attempt['user_id'] ?? 0) !== $uid || (int) ($attempt['quiz_id'] ?? 0) !== $qid) {
            $data['reason'] = 'Certificat non-correspondant.';
            $this->view('FrontOffice/home/verify_cert', $data);
            return;
        }

        if ((int) ($attempt['percentage'] ?? 0) !== $pct) {
            $data['reason'] = 'Score non-correspondant.';
            $this->view('FrontOffice/home/verify_cert', $data);
            return;
        }

        $data['ok'] = true;
        $data['reason'] = 'Certificat authentique.';
        $data['attempt'] = $attempt;

        $this->view('FrontOffice/home/verify_cert', $data);
    }

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
            'description' => 'Get in touch with APPOLIOS'
        ];

        $this->view('FrontOffice/home/contact', $data);
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
        $courses = $courseModel->getAllWithCreator();

        $data = [
            'title' => 'All Courses',
            'description' => 'Explore our course catalog',
            'courses' => $courses
        ];

        $this->view('FrontOffice/home/courses', $data);
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