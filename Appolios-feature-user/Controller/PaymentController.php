<?php
/**
 * Payment Controller - Handles Stripe payments
 */

require_once __DIR__ . '/../Controller/BaseController.php';
require_once __DIR__ . '/../Service/PaymentService.php';

class PaymentController extends BaseController {
    private $paymentService;
    
    public function __construct() {
        parent::__construct();
        $this->paymentService = new PaymentService();
    }
    
    public function checkout($courseId) {
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Please login to purchase courses.');
            $this->redirect('login');
            return;
        }
        
        if ($_SESSION['role'] !== 'student') {
            $this->setFlash('error', 'Only students can purchase courses.');
            $this->redirect('student/dashboard');
            return;
        }
        
        $courseModel = $this->model('Course');
        $course = $courseModel->findById($courseId);
        
        if (!$course) {
            $this->setFlash('error', 'Course not found.');
            $this->redirect('student/courses');
            return;
        }
        
        $price = floatval($course['price'] ?? 0);
        
        if ($price <= 0) {
            $this->setFlash('error', 'This course is free. Use regular enrollment.');
            $this->redirect('student/course/' . $courseId);
            return;
        }
        
        $enrollmentModel = $this->model('Enrollment');
        if ($enrollmentModel->isEnrolled($_SESSION['user_id'], $courseId)) {
            $this->setFlash('error', 'You are already enrolled in this course.');
            $this->redirect('student/course/' . $courseId);
            return;
        }
        
        $userModel = $this->model('User');
        $user = $userModel->findById($_SESSION['user_id']);
        $userEmail = $user['email'] ?? '';
        
        $result = $this->paymentService->createCheckoutSession(
            $courseId,
            $_SESSION['user_id'],
            $userEmail,
            $course['title'],
            $price
        );
        
        if ($result['success'] && isset($result['url'])) {
            header('Location: ' . $result['url']);
            exit;
        } else {
            $this->setFlash('error', 'Payment error: ' . ($result['error'] ?? 'Unknown error'));
            $this->redirect('student/course/' . $courseId);
        }
    }
    
    public function success() {
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Please login.');
            $this->redirect('login');
            return;
        }
        
        $sessionId = $_GET['session_id'] ?? null;
        
        if (!$sessionId) {
            $this->setFlash('error', 'Invalid payment session.');
            $this->redirect('student/courses');
            return;
        }
        
        $verification = $this->paymentService->verifyPayment($sessionId);
        
        if (!$verification['success']) {
            $this->setFlash('error', 'Payment verification failed.');
            $this->redirect('student/courses');
            return;
        }
        
        $userId = $verification['user_id'];
        $courseId = $verification['course_id'];
        
        $this->paymentService->updatePaymentStatus($sessionId, 'succeeded');
        
        $enrollmentModel = $this->model('Enrollment');
        $enrollmentModel->enroll($userId, $courseId);
        
        $courseModel = $this->model('Course');
        $course = $courseModel->findById($courseId);
        
        $this->setFlash('success', 'Payment successful! You are now enrolled in the course.');
        $this->redirect('student/course/' . $courseId);
    }
    
    public function cancel() {
        $courseId = $_GET['course_id'] ?? null;
        
        $this->setFlash('error', 'Payment was cancelled.');
        
        if ($courseId) {
            $this->redirect('student/course/' . $courseId);
        } else {
            $this->redirect('student/courses');
        }
    }
    
    public function webhook() {
        $payload = file_get_contents('php://input');
        $sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
        
        $result = $this->paymentService->handleWebhook($payload, $sigHeader);
        
        http_response_code($result['success'] ? 200 : 400);
        echo json_encode($result);
    }
    
    public function getPublishableKey() {
        header('Content-Type: application/json');
        echo json_encode(['key' => $this->paymentService->getPublishableKey()]);
    }
}