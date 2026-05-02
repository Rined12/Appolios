<?php
/**
 * CourseRecommendation Service
 * Course suggestions based on student's learning history
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/ai.php';

class CourseRecommendation {
    private $db;
    private $aiConfig;
    
    public function __construct() {
        $this->db = getConnection();
        $this->aiConfig = require __DIR__ . '/../config/ai.php';
    }
    
    public function getRecommendations($userId, $limit = 4) {
        $learnedCourses = $this->getUserCourses($userId);
        $availableCourses = $this->getAvailableCourses($userId);
        
        if (empty($learnedCourses)) {
            return $this->getPopularCourses($limit);
        }
        
        $recommendations = $this->getAIRecommendations($learnedCourses, $availableCourses, $limit);
        
        if (empty($recommendations)) {
            $recommendations = $this->getSimilarRecommendations($learnedCourses, $availableCourses, $limit);
        }
        
        return $recommendations;
    }
    
    private function getUserCourses($userId) {
        $sql = "SELECT c.id, c.title, c.description, c.price, c.image
                FROM courses c
                JOIN enrollments e ON c.id = e.course_id
                WHERE e.user_id = ? AND c.status = 'approved'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    private function getAvailableCourses($userId) {
        $sql = "SELECT c.id, c.title, c.description, c.price, c.image, u.name as creator_name
                FROM courses c
                JOIN users u ON c.created_by = u.id
                WHERE c.status = 'approved' 
                AND c.id NOT IN (SELECT course_id FROM enrollments WHERE user_id = ?)
                ORDER BY c.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    private function getAIRecommendations($learned, $available, $limit) {
        $apiKey = $this->aiConfig['openrouter']['api_key'] ?? '';
        
        if (empty($apiKey)) {
            return [];
        }
        
        $courseTitles = array_column($learned, 'title');
        $courseIds = array_column($learned, 'id');
        
        $availableJson = json_encode(array_map(function($c) {
            return ['id' => $c['id'], 'title' => $c['title']];
        }, $available));
        
        $prompt = "Based on the user's learning history:
- Completed courses: " . implode(', ', $courseTitles) . "
- Completed course IDs: " . implode(', ', $courseIds) . "

Available courses to recommend from:
$availableJson

Select the best $limit course IDs that would help this user learn more. Consider related topics and progressive learning.

Return ONLY a JSON array of course IDs (no other text):";

        $data = [
            'model' => $this->aiConfig['openrouter']['model'],
            'messages' => [
                ['role' => 'system', 'content' => 'You are a course recommendation expert.'],
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.3,
            'max_tokens' => 100
        ];
        
        $headers = [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json',
            'HTTP-Referer: http://localhost',
            'X-Title: Appolios'
        ];
        
        $ch = curl_init($this->aiConfig['openrouter']['endpoint']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            return [];
        }
        
        $result = json_decode($response, true);
        $content = $result['choices'][0]['message']['content'] ?? '';
        
        preg_match('/\[.*?\]/', $content, $matches);
        if (!empty($matches[0])) {
            $ids = json_decode($matches[0], true);
            if (is_array($ids)) {
                $recommended = array_filter($available, function($c) use ($ids) {
                    return in_array($c['id'], $ids);
                });
                return array_slice(array_values($recommended), 0, $limit);
            }
        }
        
        return [];
    }
    
    private function getSimilarRecommendations($learned, $available, $limit) {
        // Simple recommendation based on course titles/description similarity
        $learnedTitles = array_map('strtolower', array_column($learned, 'title'));
        
        $scored = [];
        foreach ($available as $course) {
            $score = 0;
            $titleLower = strtolower($course['title']);
            $descLower = strtolower($course['description'] ?? '');
            
            foreach ($learnedTitles as $learnedTitle) {
                if (strpos($titleLower, $learnedTitle) !== false || strpos($learnedTitle, $titleLower) !== false) {
                    $score += 10;
                }
                // Check for common words
                $learnedWords = explode(' ', $learnedTitle);
                foreach ($learnedWords as $word) {
                    if (strlen($word) > 3 && (strpos($titleLower, $word) !== false || strpos($descLower, $word) !== false)) {
                        $score += 1;
                    }
                }
            }
            $scored[] = ['course' => $course, 'score' => $score];
        }
        
        usort($scored, function($a, $b) {
            return $b['score'] - $a['score'];
        });
        
        return array_slice(array_column($scored, 'course'), 0, $limit);
    }
    
    private function getPopularCourses($limit) {
        $sql = "SELECT c.id, c.title, c.description, c.price, c.image, u.name as creator_name,
                (SELECT COUNT(*) FROM enrollments WHERE course_id = c.id) as enrollment_count
                FROM courses c
                JOIN users u ON c.created_by = u.id
                WHERE c.status = 'approved'
                ORDER BY enrollment_count DESC, c.created_at DESC
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
}