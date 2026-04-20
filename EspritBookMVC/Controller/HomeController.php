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