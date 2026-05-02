<?php
/**
 * Direct Chatbot API Endpoint
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../Service/ChatbotService.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$action = $_GET['action'] ?? '';

if ($action === 'chat') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['message']) || empty(trim($input['message']))) {
        echo json_encode(['error' => 'Message is required']);
        return;
    }
    
    $message = trim($input['message']);
    $sessionId = $input['session_id'] ?? bin2hex(random_bytes(16));
    $userId = $_SESSION['user_id'] ?? null;
    
    try {
        $chatbot = new ChatbotService();
        $response = $chatbot->chat($userId, $sessionId, $message);
        echo json_encode([
            'success' => true,
            'response' => $response,
            'session_id' => $sessionId
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => 'Failed: ' . $e->getMessage()
        ]);
    }
} elseif ($action === 'history') {
    $sessionId = $_GET['session_id'] ?? '';
    if (empty($sessionId)) {
        echo json_encode(['error' => 'Session ID required']);
        return;
    }
    
    $chatbot = new ChatbotService();
    $history = $chatbot->getConversations($sessionId);
    echo json_encode(['success' => true, 'history' => $history]);
} elseif ($action === 'clear') {
    $input = json_decode(file_get_contents('php://input'), true);
    $sessionId = $input['session_id'] ?? '';
    
    if (empty($sessionId)) {
        echo json_encode(['error' => 'Session ID required']);
        return;
    }
    
    $chatbot = new ChatbotService();
    $chatbot->clearConversation($sessionId);
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Invalid action']);
}