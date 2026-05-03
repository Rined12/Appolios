<?php
/**
 * APPOLIOS Auth Controller
 * Handles user authentication and registration
 */

require_once __DIR__ . '/../Controller/BaseController.php';
require_once __DIR__ . '/../Model/Repositories.php';
require_once __DIR__ . '/../Model/PresentationHelpers.php';

class AuthController extends BaseController {

    /**
     * Show login page
     */
    public function login() {
        // Redirect if already logged in
        if ($this->isLoggedIn()) {
            if ($_SESSION['role'] === 'admin') {
                $this->redirect('admin/dashboard');
            } elseif ($_SESSION['role'] === 'teacher') {
                $this->redirect('teacher/dashboard');
            } else {
                $this->redirect('student/dashboard');
            }
            return;
        }

        $data = [
            'title' => 'Sign In - APPOLIOS',
            'description' => 'Login to your APPOLIOS account',
            'flash' => $this->sessionService()->flashConsumeForView()
        ];

        $this->view('FrontOffice/auth/login', $data);
    }

    /**
     * Process login
     */
    public function authenticate() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('login');
            return;
        }

        $email = $this->sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $isAdminLogin = isset($_POST['admin_login']) && $_POST['admin_login'] === '1';

        // Validation
        if (empty($email) || empty($password)) {
            $this->sessionService()->flashPersist('error', 'Please fill in all fields');
            $this->redirect('login');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->sessionService()->flashPersist('error', 'Please enter a valid email address');
            $this->redirect('login');
            return;
        }

        // Authenticate user
        $userRepository = $this->model('UserRepository');
        $user = $userRepository->authenticate($email, $password);

        if ($user) {
            // Check if user is blocked
            if ($user['is_blocked'] ?? 0) {
                $this->sessionService()->flashPersist('error', 'Your account has been blocked. Please contact an administrator.');
                $this->redirect('login');
                return;
            }

            if ($isAdminLogin && $user['role'] !== 'admin') {
                $this->sessionService()->flashPersist('error', 'Invalid admin email or password');
                $this->redirect('admin/login');
                return;
            }

            // Regenerate session ID for security
            session_regenerate_id(true);

            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['logged_in'] = true;
            $_SESSION['login_time'] = time();

            $this->sessionService()->flashPersist('success', 'Welcome back, ' . $user['name'] . '!');

            // Redirect based on role
            if ($user['role'] === 'admin') {
                $this->redirect('admin/dashboard');
            } elseif ($user['role'] === 'teacher') {
                $this->redirect('teacher/dashboard');
            } else {
                $this->redirect('student/dashboard');
            }
        } else {
            $this->sessionService()->flashPersist('error', 'Invalid email or password');
            if ($isAdminLogin) {
                $this->redirect('admin/login');
            } else {
                $this->redirect('login');
            }
        }
    }

    /**
     * Show registration page
     */
    public function register() {
        // Redirect if already logged in
        if ($this->isLoggedIn()) {
            if ($_SESSION['role'] === 'admin') {
                $this->redirect('admin/dashboard');
            } elseif ($_SESSION['role'] === 'teacher') {
                $this->redirect('teacher/dashboard');
            } else {
                $this->redirect('student/dashboard');
            }
            return;
        }

        $flashEntity = $this->sessionService()->takeFlash();
        $data = [
            'title' => 'Sign Up - APPOLIOS',
            'description' => 'Create your APPOLIOS account',
            'flash_banner' => FlashBannerPresenter::fromFlash($flashEntity),
            'register_old' => $this->sessionService()->consumeOld(),
            'register_inline_errors' => $this->sessionService()->pullInlineRegistrationErrors(),
        ];

        $this->view('FrontOffice/auth/register', $data);
    }

    /**
     * Process registration
     */
    public function signup() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('register');
            return;
        }

        $name = $this->sanitize($_POST['name'] ?? '');
        $email = $this->sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $role = $_POST['role'] ?? 'student';

        // Validation
        $errors = [];

        if (empty($name)) {
            $errors[] = 'Name is required';
        }

        if (empty($email)) {
            $errors[] = 'Email is required';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address';
        }

        if (empty($password)) {
            $errors[] = 'Password is required';
        } elseif (strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters';
        }

        if ($password !== $confirmPassword) {
            $errors[] = 'Passwords do not match';
        }

        $userRepository = $this->model('UserRepository');

        // Student signup should only rely on users table.
        if ($userRepository->emailExists($email)) {
            $errors[] = 'Email already registered';
        }

        // Handle teacher registration with CV
        if ($role === 'teacher') {
            $teacherAppRepository = $this->model('TeacherApplicationRepository');

            // Also block emails already pending as teacher applications.
            if ($teacherAppRepository->emailExists($email)) {
                $errors[] = 'Email already pending approval';
            }

            // Validate CV upload
            if (!isset($_FILES['cv_file']) || $_FILES['cv_file']['error'] !== UPLOAD_ERR_OK) {
                $errors[] = 'CV file is required for teacher registration';
            } else {
                $cvFile = $_FILES['cv_file'];
                
                // Validate file type
                $allowedTypes = ['application/pdf'];
                if (!in_array($cvFile['type'], $allowedTypes)) {
                    $errors[] = 'Only PDF files are allowed for CV';
                }
                
                // Validate file size (5MB max)
                if ($cvFile['size'] > 5 * 1024 * 1024) {
                    $errors[] = 'CV file size must be less than 5MB';
                }
            }

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old'] = ['name' => $name, 'email' => $email];
                $this->redirect('register');
                return;
            }

            // Upload CV file
            $uploadDir = PROJECT_ROOT . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'cvs' . DIRECTORY_SEPARATOR;
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $cvFileName = uniqid() . '_' . basename($_FILES['cv_file']['name']);
            $cvPath = $uploadDir . $cvFileName;

            if (!move_uploaded_file($_FILES['cv_file']['tmp_name'], $cvPath)) {
                $this->sessionService()->flashPersist('error', 'Failed to upload CV file. Please try again.');
                $_SESSION['old'] = ['name' => $name, 'email' => $email];
                $this->redirect('register');
                return;
            }

            // Create teacher application
            $appId = $teacherAppRepository->createApplication([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'cv_filename' => $_FILES['cv_file']['name'],
                'cv_path' => '../uploads/cvs/' . $cvFileName
            ]);

            if ($appId) {
                $this->sessionService()->flashPersist('success', 'Your teacher application has been submitted successfully! An administrator will review your CV and notify you via email once approved.');
                $this->redirect('login');
            } else {
                // Clean up uploaded file if DB insert failed
                unlink($cvPath);
                $this->sessionService()->flashPersist('error', 'Failed to submit application. Please try again.');
                $this->redirect('register');
            }
            return;
        }


        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = ['name' => $name, 'email' => $email];
            $this->redirect('register');
            return;
        }

        // Create user (always student from public registration)
        $userId = $userRepository->create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => 'student'
        ]);

        if ($userId) {
            // Auto-login the new user
            $user = $userRepository->findById($userId);

            if ($user) {
                // Regenerate session ID for security
                session_regenerate_id(true);

                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['logged_in'] = true;
                $_SESSION['login_time'] = time();

                $this->sessionService()->flashPersist('success', 'Welcome, ' . $user['name'] . '! Your account has been created.');
                $this->redirect('student/dashboard');
                return;
            }

            $this->sessionService()->flashPersist('error', 'Account created but auto-login failed. Please login manually.');
            $this->redirect('login');
        } else {
            $this->sessionService()->flashPersist('error', 'Registration failed. Please try again.');
            $this->redirect('register');
        }
    }

    /**
     * Show admin login page
     */
    public function adminLogin() {
        // Redirect if already logged in
        if ($this->isLoggedIn()) {
            if ($_SESSION['role'] === 'admin') {
                $this->redirect('admin/dashboard');
            } elseif ($_SESSION['role'] === 'teacher') {
                $this->redirect('teacher/dashboard');
            } else {
                $this->redirect('student/dashboard');
            }
            return;
        }

        $data = [
            'title' => 'Administrator Login - APPOLIOS',
            'description' => 'Admin login portal',
            'flash' => $this->sessionService()->flashConsumeForView()
        ];

        $this->view('FrontOffice/auth/admin_login', $data);
    }

    /**
     * Logout
     */
    public function logout() {
        // Destroy session
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }

        session_destroy();

        // Start new session for flash message
        session_start();
        $this->sessionService()->flashPersist('success', 'You have been logged out successfully');
        $this->redirect('login');
    }
}