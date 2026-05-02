<?php
/**
 * ChatbotController
 * API endpoints for AI chatbot
 */

require_once __DIR__ . '/../Service/ChatbotService.php';

class ChatbotController {
    private $chatbotService;
    
    public function __construct() {
        $this->chatbotService = new ChatbotService();
    }
    
    public function handleRequest() {
        header('Content-Type: application/json');
        
        $action = $_GET['action'] ?? '';
        
        switch ($action) {
            case 'chat':
                $this->handleChat();
                break;
            case 'history':
                $this->handleHistory();
                break;
            case 'clear':
                $this->handleClear();
                break;
            default:
                echo json_encode(['error' => 'Invalid action']);
                break;
        }
    }
    
    private function handleChat() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['message']) || empty(trim($input['message']))) {
            echo json_encode(['error' => 'Message is required']);
            return;
        }
        
        $message = trim($input['message']);
        $sessionId = $input['session_id'] ?? $this->generateSessionId();
        $userId = $_SESSION['user_id'] ?? null;
        
        try {
            $response = $this->chatbotService->chat($userId, $sessionId, $message);
            echo json_encode([
                'success' => true,
                'response' => $response,
                'session_id' => $sessionId
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => 'Failed to get response'
            ]);
        }
    }
    
    private function handleHistory() {
        $sessionId = $_GET['session_id'] ?? '';
        
        if (empty($sessionId)) {
            echo json_encode(['error' => 'Session ID is required']);
            return;
        }
        
        $history = $this->chatbotService->getConversations($sessionId);
        echo json_encode([
            'success' => true,
            'history' => $history
        ]);
    }
    
    private function handleClear() {
        $input = json_decode(file_get_contents('php://input'), true);
        $sessionId = $input['session_id'] ?? '';
        
        if (empty($sessionId)) {
            echo json_encode(['error' => 'Session ID is required']);
            return;
        }
        
        $this->chatbotService->clearConversation($sessionId);
        echo json_encode(['success' => true]);
    }
    
    private function generateSessionId() {
        if (!isset($_SESSION['chatbot_session'])) {
            $_SESSION['chatbot_session'] = bin2hex(random_bytes(16));
        }
        return $_SESSION['chatbot_session'];
    }
}

$controller = new ChatbotController();
$controller->handleRequest();