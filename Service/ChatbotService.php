<?php
/**
 * ChatbotService
 * AI-powered student assistant chatbot
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';

class ChatbotService {
    private $db;
    private $aiConfig;
    
    public function __construct() {
        $this->db = getConnection();
        
        $configPath = __DIR__ . '/../config/ai.php';
        if (file_exists($configPath)) {
            $this->aiConfig = require $configPath;
        } else {
            $this->aiConfig = [];
        }
        
        $this->ensureTableExists();
    }
    
    private function ensureTableExists() {
        $tables = array_column($this->db->query("SHOW TABLES")->fetchAll(), 0);
        if (!in_array('chatbot_conversations', $tables)) {
            $this->db->exec("CREATE TABLE IF NOT EXISTS chatbot_conversations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                session_id VARCHAR(100) NOT NULL,
                user_id INT DEFAULT NULL,
                role ENUM('user', 'assistant') NOT NULL,
                message TEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_session (session_id),
                INDEX idx_user (user_id)
            )");
        }
    }
    
    public function chat($userId, $sessionId, $message) {
        $this->saveMessage($sessionId, $userId, 'user', $message);
        
        $context = $this->buildContext($userId);
        $history = $this->getConversationHistory($sessionId);
        
        $response = $this->getAIResponse($message, $context, $history);
        
        $this->saveMessage($sessionId, $userId, 'assistant', $response);
        
        return $response;
    }
    
    private function buildContext($userId) {
        if (!$userId) {
            return [
                'type' => 'general',
                'message' => 'Welcome to Appolios! Browse our courses to start learning.'
            ];
        }
        
        $sql = "SELECT c.id, c.title, c.description, e.progress
                FROM courses c
                JOIN enrollments e ON c.id = e.course_id
                WHERE e.user_id = ? AND c.status = 'approved'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $enrolledCourses = $stmt->fetchAll();
        
        if (empty($enrolledCourses)) {
            return $this->getGeneralContext();
        }
        
        $courseList = [];
        foreach ($enrolledCourses as $course) {
            $progress = $course['progress'] ?? 0;
            $courseList[] = "- {$course['title']} ({$progress}% complete)";
        }
        
        return [
            'type' => 'student',
            'enrolled_courses' => $courseList,
            'course_count' => count($enrolledCourses)
        ];
    }
    
    private function getGeneralContext() {
        return [
            'type' => 'general',
            'message' => 'Welcome to Appolios! Browse our courses to start learning.'
        ];
    }
    
    private function getConversationHistory($sessionId, $limit = 10) {
        $sql = "SELECT role, message FROM chatbot_conversations 
                WHERE session_id = ? ORDER BY created_at DESC LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$sessionId, $limit]);
        return array_reverse($stmt->fetchAll());
    }
    
    private function getAIResponse($userMessage, $context, $history) {
        $openrouterKey = $this->aiConfig['openrouter']['api_key'] ?? '';
        
        if (!empty($openrouterKey)) {
            return $this->callOpenRouterAPI($userMessage, $context, $history);
        }
        
        $groqKey = $this->aiConfig['groq']['api_key'] ?? '';
        if (!empty($groqKey)) {
            return $this->callGroqAPI($userMessage, $context, $history);
        }
        
        return "I'm not configured yet. Please ask the administrator to set up an AI API key.";
    }
    
    private function callOpenRouterAPI($userMessage, $context, $history) {
        $config = $this->aiConfig['openrouter'] ?? [];
        $apiKey = $config['api_key'] ?? '';
        
        if (empty($apiKey)) {
            return "Sorry, I'm having trouble connecting right now. Please try again later.";
        }
        
        $systemPrompt = $this->buildSystemPrompt($context);
        
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt]
        ];
        
        foreach ($history as $msg) {
            $messages[] = ['role' => $msg['role'], 'content' => $msg['message']];
        }
        
        $messages[] = ['role' => 'user', 'content' => $userMessage];
        
        $data = [
            'model' => $config['model'] ?? 'meta-llama/llama-3.1-8b-instruct',
            'messages' => $messages,
            'temperature' => 0.7,
            'max_tokens' => 500
        ];
        
        $headers = [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json',
            'HTTP-Referer: http://localhost',
            'X-Title: Appolios'
        ];
        
        $ch = curl_init($config['endpoint'] ?? 'https://openrouter.ai/api/v1/chat/completions');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            return $this->callGroqAPI($userMessage, $context, $history);
        }
        
        $result = json_decode($response, true);
        return $result['choices'][0]['message']['content'] ?? "I'm not sure how to respond to that.";
    }
    
    private function callGroqAPI($userMessage, $context, $history) {
        $config = $this->aiConfig['groq'] ?? [];
        $apiKey = $config['api_key'] ?? '';
        
        if (empty($apiKey)) {
            return "Sorry, I'm having trouble connecting right now. Please try again later.";
        }
        
        $systemPrompt = $this->buildSystemPrompt($context);
        
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt]
        ];
        
        foreach ($history as $msg) {
            $messages[] = ['role' => $msg['role'], 'content' => $msg['message']];
        }
        
        $messages[] = ['role' => 'user', 'content' => $userMessage];
        
        $data = [
            'model' => $config['model'] ?? 'llama-3.1-70b-versatile',
            'messages' => $messages,
            'temperature' => 0.7,
            'max_tokens' => 500
        ];
        
        $headers = [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json'
        ];
        
        $ch = curl_init($config['endpoint'] ?? 'https://api.groq.com/openai/v1/chat/completions');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            return "Sorry, I'm experiencing some issues. Please try again in a moment.";
        }
        
        $result = json_decode($response, true);
        return $result['choices'][0]['message']['content'] ?? "I'm not sure how to respond to that.";
    }
    
    private function buildSystemPrompt($context) {
        $basePrompt = "You are Appolios Learning Assistant, a helpful AI tutor for students on an e-learning platform. ";
        
        if ($context['type'] === 'student' && !empty($context['enrolled_courses'])) {
            $courses = implode("\n", $context['enrolled_courses']);
            $basePrompt .= "You have access to the student's enrolled courses:\n{$courses}\n\n";
            $basePrompt .= "You can help with:
- Explaining course concepts
- Answering questions about lessons and chapters
- Providing study tips and guidance
- Recommending next steps in their learning journey

Always be encouraging and educational. Keep responses concise but helpful.";
        } else {
            $basePrompt .= "This is a new visitor. Help them:
- Understand what Appolios offers
- Navigate the course catalog
- Find courses that interest them
- Sign up for an account

Be friendly and welcoming. Keep responses concise.";
        }
        
        return $basePrompt;
    }
    
    private function saveMessage($sessionId, $userId, $role, $message) {
        $sql = "INSERT INTO chatbot_conversations (session_id, user_id, role, message) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$sessionId, $userId, $role, $message]);
    }
    
    public function clearConversation($sessionId) {
        $sql = "DELETE FROM chatbot_conversations WHERE session_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$sessionId]);
    }
    
    public function getConversations($sessionId, $limit = 50) {
        $sql = "SELECT role, message, created_at FROM chatbot_conversations 
                WHERE session_id = ? ORDER BY created_at ASC LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$sessionId, $limit]);
        return $stmt->fetchAll();
    }
}