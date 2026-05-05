<?php
/**
 * APPOLIOS Auth Controller
 * Handles user authentication and registration - MVC Pattern
 * Controller contains: Business Logic (méthodes, logique métier)
 * Database operations are delegated to Models
 */

require_once __DIR__ . '/../Controller/BaseController.php';
require_once __DIR__ . '/../Controller/ActivityLogger.php';
require_once __DIR__ . '/../config/database.php';

class AuthController extends BaseController
{
    use ActivityLogger;
    /**
     * Show login page
     */
    public function login()
    {
        // Redirect if already logged in
        if ($this->isLoggedIn()) {
            $this->redirectByRole($_SESSION['role']);
            return;
        }

        $data = [
            'title' => 'Sign In - APPOLIOS',
            'description' => 'Login to your APPOLIOS account',
            'flash' => $this->getFlash()
        ];

        $this->view('FrontOffice/auth/login', $data);
    }

    /**
     * Process login - Business Logic
     */
    public function authenticate()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('login');
            return;
        }

        $email = $this->sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $isAdminLogin = isset($_POST['admin_login']) && $_POST['admin_login'] === '1';

        // Validate reCAPTCHA v2
        $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';
        if (empty($recaptchaResponse)) {
            $this->setFlash('error', 'Please complete the reCAPTCHA verification.');
            $this->redirect('login');
            return;
        }

        $recaptchaResult = $this->verifyRecaptchaV2($recaptchaResponse);
        if (!$recaptchaResult['valid']) {
            error_log('reCAPTCHA v2 login failed: ' . $recaptchaResult['message']);
            $this->setFlash('error', 'reCAPTCHA verification failed. Please try again.');
            $this->redirect('login');
            return;
        }


        // Validation - Business Logic
        if (empty($email) || empty($password)) {
            $this->setFlash('error', 'Please fill in all fields');
            $this->redirect('login');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->setFlash('error', 'Please enter a valid email address');
            $this->redirect('login');
            return;
        }

        // Use Controller database methods
        $user = $this->authenticateUser($email, $password);

        if ($user) {
            // Check if user is blocked
            if ($this->isUserBlocked($user['id'])) {
                $this->setFlash('error', 'Your account has been blocked. Please contact an administrator.');
                $this->redirect('login');
                return;
            }

            if ($isAdminLogin && $user['role'] !== 'admin') {
                $this->setFlash('error', 'Invalid admin email or password');
                $this->redirect('admin/login');
                return;
            }

            // Regenerate session ID for security - Business Logic
            session_regenerate_id(true);

            // Set session variables - Business Logic
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['logged_in'] = true;
            $_SESSION['login_time'] = time();

            // Log activity - Business Logic
            $this->logActivity('login', "User logged in: {$user['name']} ({$user['email']})");

            $this->setFlash('success', 'Welcome back, ' . $user['name'] . '!');
            $this->redirectByRole($user['role']);
        } else {
            $this->setFlash('error', 'Invalid email or password');
            if ($isAdminLogin) {
                $this->redirect('admin/login');
            } else {
                $this->redirect('login');
            }
        }
    }

    /**
     * Redirect to Google OAuth
     */
    public function googleLogin()
    {
        $params = [
            'client_id' => GOOGLE_CLIENT_ID,
            'redirect_uri' => GOOGLE_REDIRECT_URL,
            'response_type' => 'code',
            'scope' => 'email profile',
            'access_type' => 'online',
            'prompt' => 'select_account'
        ];

        $url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
        header('Location: ' . $url);
        exit();
    }

    /**
     * Handle Google OAuth Callback
     */
    public function googleCallback()
    {
        if (!isset($_GET['code'])) {
            $this->setFlash('error', 'Google authentication failed.');
            $this->redirect('login');
            return;
        }

        $code = $_GET['code'];

        // Exchange code for access token
        $params = [
            'client_id' => GOOGLE_CLIENT_ID,
            'client_secret' => GOOGLE_CLIENT_SECRET,
            'redirect_uri' => GOOGLE_REDIRECT_URL,
            'grant_type' => 'authorization_code',
            'code' => $code
        ];

        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Necessary for some local XAMPP setups
        $response = curl_exec($ch);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            $this->setFlash('error', 'CURL Error: ' . $curlError);
            $this->redirect('login');
            return;
        }

        $data = json_decode($response, true);

        if (!isset($data['access_token'])) {
            $errorDetail = $data['error_description'] ?? ($data['error'] ?? 'Unknown error');
            $this->setFlash('error', 'Google Token Error: ' . $errorDetail);
            $this->redirect('login');
            return;
        }

        $accessToken = $data['access_token'];

        // Get user info from Google
        $ch = curl_init('https://www.googleapis.com/oauth2/v3/userinfo');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Necessary for some local XAMPP setups
        $response = curl_exec($ch);
        curl_close($ch);

        $googleUser = json_decode($response, true);

        if (!isset($googleUser['email'])) {
            $this->setFlash('error', 'Failed to retrieve user info from Google.');
            $this->redirect('login');
            return;
        }

        $email = $googleUser['email'];
        $name = $googleUser['name'] ?? 'Google User';
        $googleId = $googleUser['sub'];

        // Check if user exists in database
        $user = $this->findUserByEmail($email);

        if ($user) {
            // Check if user is blocked
            if ($this->isUserBlocked($user['id'])) {
                $this->setFlash('error', 'Your account has been blocked.');
                $this->redirect('login');
                return;
            }
        } else {
            // Auto-register new user as student
            $userId = $this->createUser([
                'name' => $name,
                'email' => $email,
                'password' => bin2hex(random_bytes(16)), // Random password
                'role' => 'student'
            ]);
            $user = $this->findUserById($userId);
        }

        // Log the user in
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['logged_in'] = true;
        $_SESSION['login_time'] = time();

        $this->logActivity('login_google', "User logged in via Google: {$user['email']}");
        $this->setFlash('success', 'Welcome back, ' . $user['name'] . ' (via Google)!');
        
        $this->redirectByRole($user['role']);
    }

    /**
     * Redirect user based on role - Helper Business Logic
     */
    private function redirectByRole($role)
    {
        switch ($role) {
            case 'admin':
                $this->redirect('admin/dashboard');
                break;
            case 'teacher':
                $this->redirect('teacher/dashboard');
                break;
            default:
                $this->redirect('student/dashboard');
        }
    }

    /**
     * Show registration page
     */
    public function register()
    {
        // Redirect if already logged in
        if ($this->isLoggedIn()) {
            $this->redirectByRole($_SESSION['role']);
            return;
        }

        $data = [
            'title' => 'Sign Up - APPOLIOS',
            'description' => 'Create your APPOLIOS account',
            'flash' => $this->getFlash()
        ];

        $this->view('FrontOffice/auth/register', $data);
    }

    /**
     * Process registration - Business Logic
     */
    public function signup()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('register');
            return;
        }

        // Validate reCAPTCHA v2
        $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';
        if (empty($recaptchaResponse)) {
            $this->setFlash('error', 'Please complete the reCAPTCHA verification.');
            $_SESSION['old'] = $_POST;
            $this->redirect('register');
            return;
        }

        $recaptchaResult = $this->verifyRecaptchaV2($recaptchaResponse);
        if (!$recaptchaResult['valid']) {
            error_log('reCAPTCHA v2 register failed: ' . $recaptchaResult['message']);
            $this->setFlash('error', 'reCAPTCHA verification failed. Please try again.');
            $_SESSION['old'] = $_POST;
            $this->redirect('register');
            return;
        }

        $name = $this->sanitize($_POST['name'] ?? '');
        $email = $this->sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $role = $_POST['role'] ?? 'student';

        // Validation - Business Logic
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

        // Use Controller database methods

        // Check email exists
        if ($this->emailExists($email)) {
            $errors[] = 'Email already registered or pending approval';
        }

        // Handle teacher registration with CV - Business Logic
        if ($role === 'teacher') {
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

            // Also check if email is pending in teacher_applications
            if (!empty($email) && $this->teacherAppEmailExistsPending($email)) {
                $errors[] = 'A teacher application with this email is already pending review';
            }

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old'] = ['name' => $name, 'email' => $email];
                $this->redirect('register');
                return;
            }

            // Upload CV file
            $uploadDir = __DIR__ . '/../uploads/cvs/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $cvFileName = uniqid() . '_' . basename($_FILES['cv_file']['name']);
            $cvPath = $uploadDir . $cvFileName;

            if (!move_uploaded_file($_FILES['cv_file']['tmp_name'], $cvPath)) {
                $this->setFlash('error', 'Failed to upload CV file. Please try again.');
                $_SESSION['old'] = ['name' => $name, 'email' => $email];
                $this->redirect('register');
                return;
            }

            // Process face descriptor - Business Logic
            $faceDescriptor = $this->processFaceDescriptor($_POST['face_descriptor'] ?? '');

            // Insert teacher application using Controller method
            $appId = $this->createTeacherAppWithFace([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'cv_filename' => $cvFileName,
                'cv_path' => 'uploads/cvs/' . $cvFileName,
                'face_descriptor' => $faceDescriptor,
            ]);

            if ($appId) {
                $this->setFlash('success', 'Your teacher application has been submitted! An administrator will review your CV and notify you once approved.');
                $this->redirect('login');
            } else {
                // Clean up uploaded file if DB insert failed
                if (file_exists($cvPath))
                    unlink($cvPath);
                $this->setFlash('error', 'Failed to submit application. Please try again.');
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

        // Create user using Controller method (always student from public registration)
        $userId = $this->createUser([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => 'student'
        ]);

        if ($userId) {
            // Auto-login the new user
            $user = $this->findUserById($userId);

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

                // Save face descriptor if provided during registration
                $faceDescriptor = $this->processFaceDescriptor($_POST['face_descriptor'] ?? '');
                if ($faceDescriptor) {
                    $this->updateFaceDescriptor($userId, $faceDescriptor);
                }

                // Log registration activity
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                $this->logActivity('register', "New user registered: {$user['email']} ({$user['name']})");

                $this->setFlash('success', 'Welcome, ' . $user['name'] . '! Your account has been created.');
                $this->redirect('student/dashboard');
                return;
            }

            $this->setFlash('error', 'Account created but auto-login failed. Please login manually.');
            $this->redirect('login');
        } else {
            $this->setFlash('error', 'Registration failed. Please try again.');
            $this->redirect('register');
        }
    }

    /**
     * Process face descriptor for uniqueness - Business Logic
     * @param string $faceDescriptorRaw
     * @return string|null
     */
    private function processFaceDescriptor($faceDescriptorRaw)
    {
        if (empty($faceDescriptorRaw)) {
            return null;
        }

        $faceArr = json_decode($faceDescriptorRaw, true);
        if (!is_array($faceArr) || count($faceArr) !== 128) {
            return null;
        }

        // Uniqueness check
        $existingUsers = $this->getUsersWithFaceDescriptor();
        $isUnique = true;
        foreach ($existingUsers as $eu) {
            $stored = json_decode($eu['face_descriptor'], true);
            if (!$stored || count($stored) !== 128)
                continue;
            $dist = 0;
            for ($i = 0; $i < 128; $i++) {
                $diff = ($faceArr[$i] ?? 0) - ($stored[$i] ?? 0);
                $dist += $diff * $diff;
            }
            if (sqrt($dist) < 0.55) {
                $isUnique = false;
                break;
            }
        }

        return $isUnique ? json_encode($faceArr) : null;
    }

    /**
     * Show admin login page
     */
    public function adminLogin()
    {
        // Redirect if already logged in
        if ($this->isLoggedIn()) {
            $this->redirectByRole($_SESSION['role']);
            return;
        }

        $data = [
            'title' => 'Administrator Login - APPOLIOS',
            'description' => 'Admin login portal',
            'flash' => $this->getFlash()
        ];

        $this->view('FrontOffice/auth/admin_login', $data);
    }

    /**
     * Face ID Login — works for ALL roles (student, teacher, admin) - Business Logic
     * Accepts POST JSON {descriptor: [...]}, finds matching user, logs in, redirects by role
     */
    public function faceLoginAdmin()
    {
        $this->faceLogin();
    }

    public function faceLogin()
    {
        header('Content-Type: application/json');

        $body = json_decode(file_get_contents('php://input'), true);
        $descriptor = $body['descriptor'] ?? null;

        if (!$descriptor || !is_array($descriptor) || count($descriptor) !== 128) {
            echo json_encode(['success' => false, 'message' => 'Invalid face descriptor']);
            return;
        }

        // Use Controller database method
        $users = $this->getUsersWithFaceDescriptor();

        if (empty($users)) {
            echo json_encode(['success' => false, 'message' => 'No Face ID has been registered on this platform yet.']);
            return;
        }

        // Face matching algorithm - Business Logic
        $threshold = 0.55;
        $matchedUser = null;
        $minDistance = PHP_FLOAT_MAX;

        foreach ($users as $user) {
            $stored = json_decode($user['face_descriptor'], true);
            if (!$stored || count($stored) !== 128)
                continue;

            $dist = 0;
            for ($i = 0; $i < 128; $i++) {
                $diff = ($descriptor[$i] ?? 0) - ($stored[$i] ?? 0);
                $dist += $diff * $diff;
            }
            $dist = sqrt($dist);

            if ($dist < $minDistance) {
                $minDistance = $dist;
                if ($dist < $threshold) {
                    $matchedUser = $user;
                }
            }
        }

        if (!$matchedUser) {
            echo json_encode(['success' => false, 'message' => 'Face not recognized. Please try again or use email/password.']);
            return;
        }

        if ($matchedUser['is_blocked'] ?? 0) {
            echo json_encode(['success' => false, 'message' => 'This account has been blocked. Contact an administrator.']);
            return;
        }

        // Session management - Business Logic
        session_regenerate_id(true);
        $_SESSION['user_id'] = $matchedUser['id'];
        $_SESSION['user_name'] = $matchedUser['name'];
        $_SESSION['user_email'] = $matchedUser['email'];
        $_SESSION['role'] = $matchedUser['role'];
        $_SESSION['logged_in'] = true;
        $_SESSION['login_time'] = time();

        $redirect = match ($matchedUser['role']) {
            'admin' => APP_ENTRY . '?url=admin/dashboard',
            'teacher' => APP_ENTRY . '?url=teacher/dashboard',
            default => APP_ENTRY . '?url=student/dashboard',
        };

        echo json_encode([
            'success' => true,
            'message' => 'Welcome back, ' . $matchedUser['name'] . '!',
            'redirect' => $redirect
        ]);
    }

    /**
     * Check if a face descriptor is unique - Business Logic
     * Accepts POST JSON {descriptor: [...]}
     * Returns {unique: true/false, message}
     */
    public function checkFaceUnique()
    {
        header('Content-Type: application/json');

        $body = json_decode(file_get_contents('php://input'), true);
        $descriptor = $body['descriptor'] ?? null;

        if (!$descriptor || !is_array($descriptor) || count($descriptor) !== 128) {
            echo json_encode(['unique' => false, 'message' => 'Invalid descriptor']);
            return;
        }

        // Use Controller database method
        $users = $this->getUsersWithFaceDescriptor();
        $threshold = 0.55;

        foreach ($users as $user) {
            $stored = json_decode($user['face_descriptor'], true);
            if (!$stored || count($stored) !== 128)
                continue;

            $dist = 0;
            for ($i = 0; $i < 128; $i++) {
                $diff = ($descriptor[$i] ?? 0) - ($stored[$i] ?? 0);
                $dist += $diff * $diff;
            }
            if (sqrt($dist) < $threshold) {
                echo json_encode(['unique' => false, 'message' => 'This face is already registered with another account.']);
                return;
            }
        }

        echo json_encode(['unique' => true, 'message' => 'Face is unique.']);
    }

    /**
     * Verify Google reCAPTCHA v3 response using cURL
     * @param string $recaptchaResponse
     * @param string $action Expected action name (login, register)
     * @return array ['valid' => bool, 'score' => float, 'message' => string]
     */
    private function verifyRecaptcha($recaptchaResponse, $action = 'login')
    {
        $secretKey = RECAPTCHA_SECRET_KEY;
        $remoteIp = $_SERVER['REMOTE_ADDR'] ?? '';

        // Build POST data
        $postData = http_build_query([
            'secret' => $secretKey,
            'response' => $recaptchaResponse,
            'remoteip' => $remoteIp
        ]);

        // Initialize cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, RECAPTCHA_VERIFY_URL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        // Log cURL errors
        if ($result === false || $httpCode !== 200) {
            error_log('reCAPTCHA v3 cURL error: ' . $curlError . ' (HTTP: ' . $httpCode . ')');
            return [
                'valid' => false,
                'score' => 0,
                'message' => 'reCAPTCHA service unavailable'
            ];
        }

        $response = json_decode($result, true);

        // Log raw response for debugging
        error_log('reCAPTCHA v3 response: ' . print_r($response, true));

        if (!isset($response['success']) || !$response['success']) {
            $errorCodes = isset($response['error-codes']) ? implode(', ', $response['error-codes']) : 'unknown';
            error_log('reCAPTCHA v3 failed with errors: ' . $errorCodes);
            return [
                'valid' => false,
                'score' => $response['score'] ?? 0,
                'message' => 'reCAPTCHA verification failed: ' . $errorCodes
            ];
        }

        // Check action matches
        if (isset($response['action']) && $response['action'] !== $action) {
            return [
                'valid' => false,
                'score' => $response['score'] ?? 0,
                'message' => 'reCAPTCHA action mismatch (expected: ' . $action . ', got: ' . ($response['action'] ?? 'none') . ')'
            ];
        }

        // Check score threshold
        $minScore = defined('RECAPTCHA_MIN_SCORE') ? RECAPTCHA_MIN_SCORE : 0.5;
        $score = $response['score'] ?? 0;

        if ($score < $minScore) {
            return [
                'valid' => false,
                'score' => $score,
                'message' => 'reCAPTCHA score too low (' . round($score, 2) . ' < ' . $minScore . ')'
            ];
        }

        return [
            'valid' => true,
            'score' => $score,
            'message' => 'reCAPTCHA passed with score ' . round($score, 2)
        ];
    }

    /**
     * Verify Google reCAPTCHA v2 response
     * @param string $recaptchaResponse
     * @return array ['valid' => bool, 'message' => string]
     */
    private function verifyRecaptchaV2($recaptchaResponse)
    {
        $secretKey = RECAPTCHA_SECRET_KEY;
        $remoteIp = $_SERVER['REMOTE_ADDR'] ?? '';

        $postData = http_build_query([
            'secret' => $secretKey,
            'response' => $recaptchaResponse,
            'remoteip' => $remoteIp
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($result === false || $httpCode !== 200) {
            error_log('reCAPTCHA v2 cURL error: ' . $curlError . ' (HTTP: ' . $httpCode . ')');
            return [
                'valid' => false,
                'message' => 'reCAPTCHA service unavailable'
            ];
        }

        $response = json_decode($result, true);

        if (!isset($response['success']) || !$response['success']) {
            $errorCodes = isset($response['error-codes']) ? implode(', ', $response['error-codes']) : 'unknown';
            error_log('reCAPTCHA v2 failed with errors: ' . $errorCodes);
            return [
                'valid' => false,
                'message' => 'reCAPTCHA verification failed: ' . $errorCodes
            ];
        }

        return [
            'valid' => true,
            'message' => 'reCAPTCHA v2 passed'
        ];
    }

    /**
     * Save / update face descriptor for any logged-in user - Business Logic
     * Accepts POST JSON {descriptor: [...]}
     */
    public function saveFaceDescriptor()
    {
        header('Content-Type: application/json');

        if (!$this->isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        $body = json_decode(file_get_contents('php://input'), true);
        $descriptor = $body['descriptor'] ?? null;

        if (!$descriptor || !is_array($descriptor) || count($descriptor) !== 128) {
            echo json_encode(['success' => false, 'message' => 'Invalid face descriptor']);
            return;
        }

        // Use Controller database method
        $ok = $this->updateFaceDescriptor($_SESSION['user_id'], json_encode($descriptor));

        if ($ok) {
            echo json_encode(['success' => true, 'message' => 'Face ID saved successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save Face ID. Please try again.']);
        }
    }

    /**
     * Logout - Business Logic
     */
    public function logout()
    {
        // Store user info before destroying session
        $userId = $_SESSION['user_id'] ?? null;
        $userName = $_SESSION['user_name'] ?? null;
        $userEmail = $_SESSION['user_email'] ?? null;
        $userRole = $_SESSION['role'] ?? null;

        // Destroy session
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();

        // Log logout activity
        if ($userId) {
            $this->logActivity('logout', "User logged out: {$userName} ({$userEmail})", $userId, $userName, $userEmail, $userRole);
        }

        // Start new session for flash message
        session_start();
        $this->setFlash('success', 'You have been logged out successfully');
        $this->redirect('login');
    }

    // ==========================================
    // PASSWORD RESET FUNCTIONS - Business Logic
    // Uses User Model for database operations - MVC Pattern
    // ==========================================

    /**
     * Show forgot password page - redirects to login with modal
     */
    public function forgotPassword()
    {
        // Redirect to login page which has the forgot password modal
        $this->redirect('login');
    }

    /**
     * Process forgot password request - sends 4-digit verification code - Business Logic
     */
    public function requestPasswordReset()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('login');
            return;
        }

        $email = $this->sanitize($_POST['email'] ?? '');

        // Validation - Business Logic
        if (empty($email)) {
            $this->setFlash('error', 'Please enter your email address');
            $this->redirect('login');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->setFlash('error', 'Please enter a valid email address');
            $this->redirect('login');
            return;
        }

        // Use Controller database method
        $user = $this->findUserByEmail($email);
        if (!$user) {
            // Don't reveal if email exists or not (security)
            $this->setFlash('success', 'If an account exists with this email, you will receive a verification code.');
            $this->redirect('login');
            return;
        }

        // Generate 4-digit verification code - Business Logic
        $verificationCode = sprintf('%04d', random_int(1000, 9999));
        $codeExpiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        // Store code using Controller method
        $success = $this->setPasswordResetToken($user['id'], $verificationCode, $codeExpiry);

        if (!$success) {
            $this->setFlash('error', 'Failed to process request. Please try again.');
            $this->redirect('login');
            return;
        }

        // Send email with verification code - Business Logic
        require_once __DIR__ . '/MailService.php';
        $emailSent = MailService::sendVerificationCode(
            $user['email'],
            $user['name'],
            $verificationCode
        );

        if ($emailSent) {
            // Store email in session for next step
            $_SESSION['reset_email'] = $email;

            // Log password reset request
            $this->logActivity('reset_password', "Password reset requested for: {$user['email']}", $user['id'], $user['name'], $user['email'], $user['role']);

            $this->setFlash('success', 'Verification code sent to ' . htmlspecialchars($email) . '. Code expires in 10 minutes.');
            $this->redirect('login&verify=1');
        } else {
            $this->setFlash('error', 'Failed to send email. Please check sendmail configuration or contact support.');
            $this->redirect('login');
        }
    }

    /**
     * Verify the 4-digit code and show reset password form - Business Logic
     */
    public function verifyResetCode()
    {
        // Redirect if already logged in
        if ($this->isLoggedIn()) {
            $this->redirect('student/dashboard');
            return;
        }

        $code = $_POST['code'] ?? '';
        $email = $_SESSION['reset_email'] ?? '';

        if (empty($code)) {
            $this->setFlash('error', 'Please enter the verification code.');
            $this->redirect('login&verify=1');
            return;
        }

        if (empty($email)) {
            $this->setFlash('error', 'Session expired. Please request a new verification code.');
            $this->redirect('login');
            return;
        }

        // Use Controller database method
        $user = $this->findUserByEmail($email);
        if (!$user) {
            $this->setFlash('error', 'User not found.');
            unset($_SESSION['reset_email']);
            $this->redirect('login');
            return;
        }

        // Check if code matches and is not expired - Business Logic
        if ($user['reset_token'] !== $code) {
            error_log("Code mismatch: POST={$code}, DB=" . ($user['reset_token'] ?? 'NULL'));
            $this->setFlash('error', 'Invalid verification code.');
            unset($_SESSION['reset_email']);
            $this->redirect('login');
            return;
        }

        // Check expiry - Business Logic
        if (empty($user['reset_token_expiry']) || strtotime($user['reset_token_expiry']) < time()) {
            $this->setFlash('error', 'Verification code has expired.');
            unset($_SESSION['reset_email']);
            $this->redirect('login');
            return;
        }

        // Code is valid, store user ID for password reset - Business Logic
        $_SESSION['reset_user_id'] = $user['id'];
        $_SESSION['reset_verified'] = true;
        unset($_SESSION['reset_email']);

        // Redirect to reset password form with modal trigger
        $this->setFlash('success', 'Code verified! Now enter your new password.');
        $this->redirect('login&reset=1');
    }

    /**
     * Show reset password page (after code verification) - redirects to login with modal
     */
    public function resetPassword()
    {
        // Redirect if already logged in
        if ($this->isLoggedIn()) {
            $this->redirect('student/dashboard');
            return;
        }

        // Check if user has verified their code
        if (!isset($_SESSION['reset_verified']) || !isset($_SESSION['reset_user_id'])) {
            $this->setFlash('error', 'Please enter the verification code first.');
            $this->redirect('login');
            return;
        }

        // Redirect to login page with reset password modal
        $this->redirect('login?reset=1');
    }

    /**
     * Process reset password (after code verification) - Business Logic
     */
    public function processResetPassword()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('login');
            return;
        }

        // Check if user has verified their code
        if (!isset($_SESSION['reset_verified']) || !isset($_SESSION['reset_user_id'])) {
            $this->setFlash('error', 'Please enter the verification code first.');
            $this->redirect('login');
            return;
        }

        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $userId = $_SESSION['reset_user_id'];

        // Validation - Business Logic
        if (empty($password)) {
            $this->setFlash('error', 'Please enter a new password');
            $this->redirect('reset-password');
            return;
        }

        if (strlen($password) < 6) {
            $this->setFlash('error', 'Password must be at least 6 characters');
            $this->redirect('reset-password');
            return;
        }

        if ($password !== $confirmPassword) {
            $this->setFlash('error', 'Passwords do not match');
            $this->redirect('reset-password');
            return;
        }

        // Update password and clear reset code using Controller method
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);
        $success = $this->updatePassword($userId, $hashedPassword);

        if ($success) {
            // Log activity
            $user = $this->findUserById($userId);
            if ($user) {
                $this->logActivity('reset_password', "Password reset completed for: {$user['email']}", $user['id'], $user['name'], $user['email'], $user['role']);
            }

            // Clear session variables
            unset($_SESSION['reset_verified'], $_SESSION['reset_user_id'], $_SESSION['reset_email']);

            $this->setFlash('success', 'Password reset successful! Please login with your new password.');
            $this->redirect('login');
        } else {
            $this->setFlash('error', 'Failed to reset password. Please try again.');
            $this->redirect('reset-password');
        }
    }

    // ==========================================
    // DATABASE METHODS - Moved from User Model
    // ==========================================

    private $table = 'users';

    public function createUser($data)
    {
        $sql = "INSERT INTO {$this->table} (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())";
        try {
            $stmt = $this->getDb()->prepare($sql);
            $stmt->execute([
                $data['name'],
                $data['email'],
                password_hash($data['password'], PASSWORD_DEFAULT, ['cost' => HASH_COST]),
                $data['role'] ?? 'student'
            ]);
            return $this->getDb()->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function findUserByEmail($email)
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = ?";
        $stmt = $this->getDb()->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function authenticateUser($email, $password)
    {
        $user = $this->findUserByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    public function updateUser($id, $data)
    {
        $fields = [];
        $values = [];
        foreach ($data as $key => $value) {
            $fields[] = "{$key} = ?";
            $values[] = $value;
        }
        $values[] = $id;
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->getDb()->prepare($sql);
        return $stmt->execute($values);
    }

    public function getStudents()
    {
        $sql = "SELECT * FROM {$this->table} WHERE role = 'student'";
        $stmt = $this->getDb()->query($sql);
        return $stmt->fetchAll();
    }

    public function countStudents()
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE role = 'student'";
        $stmt = $this->getDb()->query($sql);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }

    public function emailExists($email)
    {
        return $this->findUserByEmail($email) !== false;
    }

    public function getTeachers()
    {
        $sql = "SELECT * FROM {$this->table} WHERE role = 'teacher' ORDER BY created_at DESC";
        $stmt = $this->getDb()->query($sql);
        return $stmt->fetchAll();
    }

    public function findUserById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->getDb()->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function blockUser($id)
    {
        $sql = "UPDATE {$this->table} SET is_blocked = 1 WHERE id = ?";
        $stmt = $this->getDb()->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function unblockUser($id)
    {
        $sql = "UPDATE {$this->table} SET is_blocked = 0 WHERE id = ?";
        $stmt = $this->getDb()->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function isUserBlocked($id)
    {
        try {
            $sql = "SELECT is_blocked, ban_until FROM {$this->table} WHERE id = ?";
            $stmt = $this->getDb()->prepare($sql);
            $stmt->execute([$id]);
            $result = $stmt->fetch();

            if (!$result || $result['is_blocked'] != 1) {
                return false;
            }

            // Check if temporary ban has expired
            if (!empty($result['ban_until'])) {
                $banUntil = strtotime($result['ban_until']);
                if ($banUntil < time()) {
                    // Ban expired - auto unblock
                    $this->unblockUser($id);
                    return false;
                }
            }

            return true;
        } catch (PDOException $e) {
            // Fallback if ban_until column doesn't exist - just check is_blocked
            $sql = "SELECT is_blocked FROM {$this->table} WHERE id = ?";
            $stmt = $this->getDb()->prepare($sql);
            $stmt->execute([$id]);
            $result = $stmt->fetch();
            return $result && $result['is_blocked'] == 1;
        }
    }

    public function getUsersWithFaceDescriptor()
    {
        $sql = "SELECT * FROM users WHERE face_descriptor IS NOT NULL AND face_descriptor != '' AND (is_blocked IS NULL OR is_blocked = 0)";
        $stmt = $this->getDb()->query($sql);
        return $stmt ? $stmt->fetchAll() : [];
    }

    public function updateFaceDescriptor($id, $faceDescriptor)
    {
        $sql = "UPDATE {$this->table} SET face_descriptor = ? WHERE id = ?";
        $stmt = $this->getDb()->prepare($sql);
        return $stmt->execute([$faceDescriptor, $id]);
    }

    public function setPasswordResetToken($id, $token, $expiry)
    {
        $sql = "UPDATE {$this->table} SET reset_token = ?, reset_token_expiry = ? WHERE id = ?";
        $stmt = $this->getDb()->prepare($sql);
        return $stmt->execute([$token, $expiry, $id]);
    }

    public function updatePassword($id, $hashedPassword)
    {
        $sql = "UPDATE {$this->table} SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE id = ?";
        $stmt = $this->getDb()->prepare($sql);
        return $stmt->execute([$hashedPassword, $id]);
    }

    public function createUserWithHashedPassword($data)
    {
        $sql = "INSERT INTO {$this->table} (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())";
        try {
            $stmt = $this->getDb()->prepare($sql);
            $stmt->execute([
                $data['name'],
                $data['email'],
                $data['password'],
                $data['role'] ?? 'teacher'
            ]);
            return $this->getDb()->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }

    // ==========================================
    // TEACHER APPLICATION METHODS - From TeacherApplication Model
    // ==========================================

    public function teacherAppEmailExistsPending($email)
    {
        $sql = "SELECT id FROM teacher_applications WHERE email = ? AND status = 'pending'";
        $stmt = $this->getDb()->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch() !== false;
    }

    public function createTeacherAppWithFace($data)
    {
        $sql = "INSERT INTO teacher_applications (name, email, password, cv_filename, cv_path, face_descriptor, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')";
        try {
            $stmt = $this->getDb()->prepare($sql);
            $stmt->execute([
                $data['name'],
                $data['email'],
                password_hash($data['password'], PASSWORD_DEFAULT, ['cost' => 12]),
                $data['cv_filename'],
                $data['cv_path'],
                $data['face_descriptor'] ?? null
            ]);
            return $this->getDb()->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }
}