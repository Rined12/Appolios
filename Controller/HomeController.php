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
        // Public landing page for everyone

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
        $result = $this->createContactMessage($name, $email, $subject, $message);

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
        // View courses

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

    /**
     * Create a new contact message - Database operation moved from Model
     */
    public function createContactMessage(string $name, string $email, string $subject, string $message): bool {
        $sql = "INSERT INTO contact_messages (name, email, subject, message, is_read, created_at)
                VALUES (?, ?, ?, ?, 0, NOW())";
        $stmt = $this->getDb()->prepare($sql);
        return $stmt->execute([$name, $email, $subject, $message]);
    }
}