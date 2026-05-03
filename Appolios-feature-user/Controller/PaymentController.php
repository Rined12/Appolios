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
        
        $result = $this->paymentService->verifyPayment($sessionId);
        
        if ($result['success']) {
            $enrollmentModel = $this->model('Enrollment');
            
            $alreadyEnrolled = $enrollmentModel->isEnrolled($_SESSION['user_id'], $result['course_id']);
            
            if (!$alreadyEnrolled) {
                $enrollmentModel->enroll($_SESSION['user_id'], $result['course_id']);
            }
            
            $data = [
                'title' => 'Payment Successful',
                'course_id' => $result['course_id'],
                'flash' => $this->getFlash()
            ];
            
            $this->view('FrontOffice/student/payment_success', $data);
        } else {
            $this->setFlash('error', 'Payment verification failed.');
            $this->redirect('student/courses');
        }
    }
    
    public function cancel() {
        $this->setFlash('error', 'Payment was cancelled.');
        $this->redirect('student/courses');
    }
    
    public function webhook() {
        $payload = file_get_contents('php://input');
        $sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
        
        // For now, just log the webhook
        error_log('Stripe Webhook received: ' . $payload);
        
        // In production, verify signature and process event
        http_response_code(200);
    }
    
    public function getPublishableKey() {
        return $this->paymentService->getPublishableKey();
    }
}