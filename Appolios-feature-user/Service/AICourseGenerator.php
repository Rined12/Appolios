<?php
/**
 * AI Course Generator Service
 * Uses OpenRouter API to auto-generate course content
 */

require_once __DIR__ . '/../config/config.php';

class AICourseGenerator {
    private $apiKey;
    private $baseUrl;
    private $model;
    
    public function __construct() {
        $this->apiKey = defined('OPENROUTER_API_KEY') ? OPENROUTER_API_KEY : '';
        $this->baseUrl = defined('OPENROUTER_BASE_URL') ? OPENROUTER_BASE_URL : 'https://openrouter.ai/api/v1';
        $this->model = defined('OPENROUTER_MODEL') ? OPENROUTER_MODEL : 'meta-llama/llama-3.1-8b-instruct';
        
        if (empty($this->apiKey)) {
            throw new Exception('OpenRouter API key not configured');
        }
    }
    
    /**
     * Generate a complete course with chapters, lessons, and content
     */
    public function generateFullCourse($topic, $audience = 'beginners') {
        $prompt = $this->buildCoursePrompt($topic, $audience);
        $response = $this->callAI($prompt);
        
        if ($response['success']) {
            return $this->parseCourseResponse($response['content']);
        }
        
        return ['success' => false, 'error' => $response['error'] ?? 'Failed to generate course'];
    }
    
    /**
     * Generate just an outline (chapters and lesson titles)
     */
    public function generateOutline($topic, $audience = 'beginners') {
        $prompt = $this->buildOutlinePrompt($topic, $audience);
        $response = $this->callAI($prompt);
        
        if ($response['success']) {
            return $this->parseOutlineResponse($response['content']);
        }
        
        return ['success' => false, 'error' => $response['error'] ?? 'Failed to generate outline'];
    }
    
    /**
     * Generate content for a single lesson
     */
    public function generateLessonContent($lessonTitle, $chapterContext = '') {
        $prompt = $this->buildContentPrompt($lessonTitle, $chapterContext);
        $response = $this->callAI($prompt);
        
        if ($response['success']) {
            return ['success' => true, 'content' => $response['content']];
        }
        
        return ['success' => false, 'error' => $response['error'] ?? 'Failed to generate content'];
    }
    
    private function buildCoursePrompt($topic, $audience) {
        return <<<PROMPT
Generate a complete course about "$topic" for $audience.

Return ONLY a valid JSON object (not array). The course should have:
- "title": Course title
- "description": Course description (2-3 sentences)
- "chapters": Array of chapters, each with:
  - "title": Chapter title
  - "description": Chapter description
  - "lessons": Array of lessons, each with:
    - "title": Lesson title
    - "content": Detailed lesson content (300-500 words, educational but accessible)

Generate 4-5 chapters with 3-5 lessons each. Content should be comprehensive educational material.

Example format:
{
  "title": "Self Development Mastery",
  "description": "A comprehensive guide to personal growth and self-improvement for complete beginners.",
  "chapters": [
    {
      "title": "Chapter 1: Getting Started",
      "description": "Introduction to the fundamentals",
      "lessons": [
        {"title": "What is self development?", "content": "Full lesson text here..."}
      ]
    }
  ]
}
PROMPT;
    }
    
    private function buildOutlinePrompt($topic, $audience) {
        return <<<PROMPT
Generate a course outline about "$topic" for $audience.

Return ONLY a valid JSON array (no other text). Each chapter should have:
- "title": Chapter title
- "description": Brief chapter description
- "lessons": Array of lesson titles (strings only, no content)

Generate 4-5 chapters with 3-5 lessons each.
PROMPT;
    }
    
    private function buildContentPrompt($lessonTitle, $chapterContext) {
        return <<<PROMPT
Generate detailed educational content for a lesson titled "$lessonTitle".

Context: $chapterContext

Return ONLY the lesson content as plain text (no JSON, no markdown). 
Write 300-500 words of comprehensive educational content that is:
- Clear and accessible
- Well-structured with examples
- Practical and actionable

Content type: text-based lesson (no videos)
PROMPT;
    }
    
    private function callAI($prompt) {
        $ch = curl_init($this->baseUrl . '/chat/completions');
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'model' => $this->model,
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'max_tokens' => 4000,
            'temperature' => 0.7
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey,
            'HTTP-Referer: ' . (APP_URL ?? 'http://localhost'),
            'X-Title: APPOLIOS'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            return ['success' => false, 'error' => "API error: HTTP $httpCode. Response: $response. Curl error: $error"];
        }
        
        $data = json_decode($response, true);
        
        if (isset($data['choices'][0]['message']['content'])) {
            return ['success' => true, 'content' => $data['choices'][0]['message']['content']];
        }
        
        return ['success' => false, 'error' => 'Invalid API response. Data: ' . json_encode($data)];
    }
    
    private function parseCourseResponse($content) {
        $content = trim($content);
        
        // Try extracting JSON from markdown code blocks
        if (preg_match('/```(?:json)?\s*([\s\S]*?)```/', $content, $matches)) {
            $json = json_decode(trim($matches[1]), true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
                return ['success' => true, 'course' => $json];
            }
        }
        
        // Try direct parse as object
        $json = json_decode($content, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            // If it's an array with one course object, use that
            if (is_array($json) && isset($json[0]['title']) && isset($json[0]['chapters'])) {
                return ['success' => true, 'course' => $json[0]];
            }
            // If it has title and chapters, it's a course object
            if (is_array($json) && isset($json['title']) && isset($json['chapters'])) {
                return ['success' => true, 'course' => [$json]];
            }
        }
        
        // Debug: return raw content for inspection
        return ['success' => false, 'error' => 'Failed to parse course data. Raw response (first 500 chars): ' . substr($content, 0, 500)];
    }
    
    private function parseOutlineResponse($content) {
        $content = trim($content);
        
        if (preg_match('/\[[\s\S]*\]/', $content, $matches)) {
            $json = json_decode($matches[0], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
                return ['success' => true, 'outline' => $json];
            }
        }
        
        $json = json_decode($content, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
            return ['success' => true, 'outline' => $json];
        }
        
        return ['success' => false, 'error' => 'Failed to parse outline'];
    }
}