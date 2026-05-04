<?php
/**
 * AI Configuration
 * Free AI API settings - uses OpenRouter
 */

return [
    'provider' => 'openrouter',
    
    'openrouter' => [
        'api_key' => defined('OPENROUTER_API_KEY') ? OPENROUTER_API_KEY : getenv('OPENROUTER_API_KEY') ?: '',
        'model' => 'meta-llama/llama-3.1-8b-instruct',
        'endpoint' => 'https://openrouter.ai/api/v1/chat/completions',
        'referer' => 'appolios',
        'title' => 'Appolios E-Learning'
    ],
    
    'use_fallback' => true,
    
    'system_prompt' => 'You are a creative badge generator. Create short, unique, inspiring badge names for course completion. Always respond with valid JSON only.',
    'user_prompt_template' => 'Create a fun badge for completing a course.
Course: {course_title}
Type: {category}
Time: {time_taken}
Lessons: {lesson_count}

Return JSON: {"name": "Name", "icon": "emoji", "description": "desc"}'
];