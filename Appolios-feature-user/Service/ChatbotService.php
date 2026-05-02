<?php
/**
 * ChatbotService
 * AI-powered student assistant chatbot
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/ai.php';

class ChatbotService {
    private $db;
    private $aiConfig;
    
    public function __construct() {
        $this->db = getConnection();
        $this->aiConfig = require __DIR__ . '/../config/ai.php';
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
            return $this->getGeneralContext();
        }
        
        $sql = "SELECT c.id, c.title, c.description, c.course_type, e.progress, e.completed_lessons
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
            
            $chapters = $this->getCourseChapters($course['id']);
            if (!empty($chapters)) {
                $currentChapter = $chapters[0];
                $lessons = $this->getChapterLessons($currentChapter['id']);
                if (!empty($lessons)) {
                    $courseList[] = "  Current: {$currentChapter['title']} - {$lessons[0]['title']}";
                }
            }
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
    
    private function getCourseChapters($courseId) {
        $sql = "SELECT id, title, chapter_order FROM chapters WHERE course_id = ? ORDER BY chapter_order";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$courseId]);
        return $stmt->fetchAll();
    }
    
    private function getChapterLessons($chapterId) {
        $sql = "SELECT id, title, lesson_order FROM lessons WHERE chapter_id = ? ORDER BY lesson_order";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$chapterId]);
        return $stmt->fetchAll();
    }
    
    private function getConversationHistory($sessionId, $limit = 10) {
        $sql = "SELECT role, message FROM chatbot_conversations 
                WHERE session_id = ? ORDER BY created_at DESC LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$sessionId, $limit]);
        return array_reverse($stmt->fetchAll());
    }
    
    private function getAIResponse($userMessage, $context, $history) {
        $provider = $this->aiConfig['provider'] ?? 'groq';
        $groqKey = $this->aiConfig['groq']['api_key'] ?? getenv('GROQ_API_KEY') ?: '';
        $openrouterKey = $this->aiConfig['openrouter']['api_key'] ?? getenv('OPENROUTER_API_KEY') ?: '';
        
        if (empty($groqKey) && empty($openrouterKey)) {
            return "I'm not configured yet. Please ask the administrator to set up an AI API key in the config/ai.php file.";
        }
        
        if ($provider === 'groq' && !empty($groqKey)) {
            return $this->callGroqAPI($userMessage, $context, $history);
        }
        
        if (!empty($openrouterKey)) {
            return $this->callOpenRouterAPI($userMessage, $context, $history);
        }
        
        if (!empty($groqKey)) {
            return $this->callGroqAPI($userMessage, $context, $history);
        }
        
        return "I'm having trouble connecting. Please contact the administrator.";
    }
    
    private function callOpenRouterAPI($userMessage, $context, $history) {
        $config = $this->aiConfig['openrouter'] ?? [];
        $apiKey = $config['api_key'] ?? getenv('OPENROUTER_API_KEY') ?: '';
        
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
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            error_log("Chatbot OpenRouter Error - HTTP: $httpCode, Response: $response, Error: $error");
            // Try Groq as fallback
            return $this->callGroqAPI($userMessage, $context, $history);
        }
        
        $result = json_decode($response, true);
        return $result['choices'][0]['message']['content'] ?? "I'm not sure how to respond to that.";
    }
    
    private function callGroqAPI($userMessage, $context, $history) {
        $config = $this->aiConfig['groq'] ?? [];
        $apiKey = $config['api_key'] ?? getenv('GROQ_API_KEY') ?: '';
        
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
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            error_log("Chatbot Groq Error - HTTP: $httpCode, Response: $response, Error: $error");
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
- Helping with homework or practice exercises

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