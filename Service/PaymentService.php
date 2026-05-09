<?php
/**
 * Payment Service - Stripe Integration
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/stripe.php';

class PaymentService {
    private $db;
    private $stripeSecret;
    private $stripePublishable;
    
    public function __construct() {
        $this->db = getConnection();
        $this->stripeSecret = STRIPE_SECRET_KEY;
        $this->stripePublishable = STRIPE_PUBLISHABLE_KEY;
    }
    
    public function getPublishableKey() {
        return $this->stripePublishable;
    }
    
    public function createCheckoutSession($courseId, $userId, $userEmail, $courseTitle, $price) {
        $priceInCents = (int)($price * 100);
        
        $sessionData = [
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => STRIPE_CURRENCY,
                    'product_data' => [
                        'name' => $courseTitle,
                        'description' => 'Course enrollment - ' . $courseTitle,
                        'images' => []
                    ],
                    'unit_amount' => $priceInCents
                ],
                'quantity' => 1
            ]],
            'mode' => 'payment',
            'success_url' => APP_URL . '/index.php?url=payment/success&session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => APP_URL . '/index.php?url=student/course/' . $courseId,
            'client_reference_id' => $userId . '|' . $courseId,
            'customer_email' => $userEmail,
            'metadata' => [
                'user_id' => $userId,
                'course_id' => $courseId
            ]
        ];
        
        $ch = curl_init('https://api.stripe.com/v1/checkout/sessions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($sessionData));
        curl_setopt($ch, CURLOPT_USERPWD, $this->stripeSecret);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
        
        $response = curl_exec($ch);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        error_log("Stripe cURL response: " . $response);
        error_log("Stripe cURL error: " . $curlError);
        
        $result = json_decode($response, true);
        
        if (isset($result['error'])) {
            error_log('Stripe Error: ' . $result['error']['message']);
            return ['success' => false, 'error' => $result['error']['message']];
        }
        
        if (isset($result['id'])) {
            if (empty($result['url'])) {
                return ['success' => false, 'error' => 'Stripe session created but no URL returned'];
            }
            $this->savePayment($userId, $courseId, $result['id'], $price);
            return ['success' => true, 'session_id' => $result['id'], 'url' => $result['url']];
        }
        
        return ['success' => false, 'error' => 'Failed to create checkout session'];
    }
    
    private function savePayment($userId, $courseId, $sessionId, $amount) {
        $sql = "INSERT INTO payments (user_id, course_id, stripe_session_id, amount, status) VALUES (?, ?, ?, ?, 'pending')";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $courseId, $sessionId, $amount]);
    }
    
    public function verifyPayment($sessionId) {
        $ch = curl_init('https://api.stripe.com/v1/checkout/sessions/' . $sessionId);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->stripeSecret);
        $response = curl_exec($ch);
        curl_close($ch);
        
        $result = json_decode($response, true);
        
        if (isset($result['payment_status']) && $result['payment_status'] === 'paid') {
            $this->updatePaymentStatus($sessionId, 'succeeded', $result['payment_intent'] ?? null);
            error_log("Updated payment to succeeded for session: $sessionId");
            return [
                'success' => true,
                'user_id' => $result['metadata']['user_id'] ?? null,
                'course_id' => $result['metadata']['course_id'] ?? null
            ];
        }
        
        return ['success' => false];
    }
    
    public function updatePaymentStatus($sessionId, $status, $paymentIntent = null) {
        $sql = "UPDATE payments SET status = ?";
        $params = [$status];
        
        if ($paymentIntent) {
            $sql .= ", stripe_payment_id = ?";
            $params[] = $paymentIntent;
        }
        
        $sql .= " WHERE stripe_session_id = ?";
        $params[] = $sessionId;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
    }
    
    public function hasCompletedPayment($userId, $courseId) {
        $sql = "SELECT id FROM payments WHERE user_id = ? AND course_id = ? AND status = 'succeeded' LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $courseId]);
        return $stmt->fetch() !== false;
    }
    
    public function getPaymentByCourse($userId, $courseId) {
        $sql = "SELECT * FROM payments WHERE user_id = ? AND course_id = ? ORDER BY id DESC LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $courseId]);
        return $stmt->fetch();
    }
    
    public function getPaymentsByUser($userId) {
        $sql = "SELECT p.*, c.title as course_title, c.image as course_image 
                FROM payments p 
                JOIN courses c ON p.course_id = c.id 
                WHERE p.user_id = ? 
                ORDER BY p.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}