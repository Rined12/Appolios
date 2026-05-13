<?php
/**
 * BadgeGenerator Service
 * AI-powered badge generation using Groq API with fallback
 */

require_once __DIR__ . '/../config/database.php';
$aiConfig = require __DIR__ . '/../config/ai.php';

class BadgeGenerator {
    private $config;
    
    public function __construct() {
        $this->config = $aiConfig;
    }
    
    /**
     * Generate a badge for course completion
     */
    public function generateBadge($courseTitle, $category, $timeTaken, $lessonCount, $progress = 100) {
        // Try AI first
        $badge = $this->generateWithAI($courseTitle, $category, $timeTaken, $lessonCount, $progress);
        
        // If AI fails, use fallback
        if (!$badge) {
            $badge = $this->generateFallback($courseTitle, $category, $timeTaken, $lessonCount, $progress);
        }
        
        return $badge;
    }
    
    /**
     * Generate badge using OpenRouter API
     */
    private function generateWithAI($courseTitle, $category, $timeTaken, $lessonCount, $progress) {
        $providerConfig = $this->config['openrouter'] ?? null;
        
        if (!$providerConfig || empty($providerConfig['api_key'])) {
            return null;
        }
        
        // Try multiple free models
        $models = [
            'microsoft/phi-3.5-mini-instruct-free',
            'qwen/qwen2.5-7b-instruct-free',
            'google/gemma-2-2b-it-free'
        ];
        
        foreach ($models as $model) {
            $result = $this->callOpenRouter($providerConfig, $model, $courseTitle, $category, $timeTaken, $lessonCount, $progress);
            if ($result) return $result;
        }
        
        return null;
    }
    
    private function callOpenRouter($config, $model, $courseTitle, $category, $timeTaken, $lessonCount, $progress) {
        $prompt = str_replace(
            ['{course_title}', '{category}', '{time_taken}', '{lesson_count}', '{progress}'],
            [$courseTitle, $category, $timeTaken, $lessonCount, $progress],
            $this->config['user_prompt_template']
        );
        
        $data = [
            'model' => $model,
            'messages' => [
                ['role' => 'system', 'content' => $this->config['system_prompt']],
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 1.0,
            'max_tokens' => 80
        ];
        
        $headers = [
            'Authorization: Bearer ' . $config['api_key'],
            'Content-Type: application/json',
            'HTTP-Referer: ' . ($config['referer'] ?? 'http://localhost'),
            'X-Title: ' . ($config['title'] ?? 'Appolios')
        ];
        
        $ch = curl_init($config['endpoint']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            return null;
        }
        
        $result = json_decode($response, true);
        $content = $result['choices'][0]['message']['content'] ?? '';
        
        // Parse JSON from response
        preg_match('/\{[^}]+\}/', $content, $matches);
        if (!empty($matches[0])) {
            $badge = json_decode($matches[0], true);
            if ($badge && isset($badge['name'])) {
                return [
                    'name' => trim($badge['name']),
                    'icon' => $this->getValidEmoji($badge['icon'] ?? '🎖️'),
                    'description' => trim($badge['description'] ?? 'Course completed!')
                ];
            }
        }
        
        return null;
    }
    
    /**
     * Fallback badge generator using algorithm
     */
    private function generateFallback($courseTitle, $category, $timeTaken, $lessonCount, $progress) {
        $prefixes = [
            'Programming' => ['Code', 'Syntax', 'Dev', 'Coder', 'Byte'],
            'Business' => ['Biz', 'Enterprise', 'Strategy', 'Leader'],
            'Design' => ['Art', 'Design', 'Creative', 'Visual'],
            'Marketing' => ['Brand', 'Reach', 'Growth', 'Market'],
            'Data Science' => ['Data', 'Analytics', 'Insight', 'Logic'],
            'Language' => ['Polyglot', 'Linguist', 'Word', 'Speech'],
            'Science' => ['Lab', 'Research', 'Science', 'Discover'],
            'Mathematics' => ['Math', 'Number', 'Calc', 'Quant'],
            'default' => ['Master', 'Pro', 'Expert', 'Champion']
        ];
        
        $suffixes = ['Master', 'Expert', 'Ninja', 'Wizard', 'Hero', 'Star', 'King', 'Queen', 'Ace', 'Boss'];
        
        $icons = ['🏆', '⭐', '🎯', '🚀', '💡', '🔥', '💪', '🎓', '🏅', '✨'];
        
        // Get category keywords
        $titleLower = strtolower($courseTitle);
        $categoryKey = $this->detectCategory($titleLower);
        
        // Generate name
        $prefixList = $prefixes[$categoryKey] ?? $prefixes['default'];
        $prefix = $prefixList[array_rand($prefixList)];
        $suffix = $suffixes[array_rand($suffixes)];
        
        // Time-based modifier
        $modifier = '';
        if (stripos($timeTaken, 'day') !== false) {
            preg_match('/(\d+)/', $timeTaken, $matches);
            $days = (int)($matches[0] ?? 0);
            if ($days <= 1) $modifier = 'Swift';
            elseif ($days <= 7) $modifier = 'Quick';
        } elseif (stripos($timeTaken, 'hour') !== false) {
            $modifier = 'Speed';
        }
        
        $name = ($modifier ? $modifier . ' ' : '') . $prefix . ' ' . $suffix;
        
        // Generate description
        $descriptions = [
            "Completed {$courseTitle} with determination!",
            "Successfully finished the {$category} course!",
            "Achieved mastery in {$courseTitle}!",
            "Earned through dedication and hard work!",
            "Proven skills in {$category}!"
        ];
        
        return [
            'name' => trim($name),
            'icon' => $icons[array_rand($icons)],
            'description' => $descriptions[array_rand($descriptions)]
        ];
    }
    
    /**
     * Detect category from course title
     */
    private function detectCategory($title) {
        $categories = [
            'Programming' => ['python', 'javascript', 'java', 'php', 'html', 'css', 'coding', 'programming', 'code', 'web develop', 'software'],
            'Business' => ['business', 'management', 'leadership', 'entrepreneur', 'startup', 'strategy'],
            'Design' => ['design', 'graphic', 'ui', 'ux', 'photo', 'illustrat', 'art', 'creative'],
            'Marketing' => ['marketing', 'seo', 'social media', 'advertising', 'brand', 'content'],
            'Data Science' => ['data', 'machine learning', 'ai', 'analytics', 'statistics', 'big data', 'python'],
            'Language' => ['english', 'spanish', 'french', 'german', 'language', 'learn', 'speak'],
            'Science' => ['physics', 'chemistry', 'biology', 'science', 'medical', 'health'],
            'Mathematics' => ['math', 'mathematic', 'algebra', 'calculus', 'statistic']
        ];
        
        foreach ($categories as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($title, $keyword) !== false) {
                    return $category;
                }
            }
        }
        
        return 'default';
    }
    
    /**
     * Ensure emoji is valid
     */
    private function getValidEmoji($emoji) {
        // Simple validation - ensure it's a single emoji or use default
        if (preg_match('/^[\p{Emoji_Presentation}\p{Extended_Pictographic}]/u', $emoji)) {
            return $emoji;
        }
        return '🎖️';
    }
}