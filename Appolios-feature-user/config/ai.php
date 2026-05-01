<?php
/**
 * AI Configuration
 * Free AI API settings - supports Groq and OpenRouter
 * 
 * To use your own API key:
 * - Option 1: Set environment variable (recommended)
 *   - GROQ_API_KEY for Groq
 *   - OPENROUTER_API_KEY for OpenRouter
 * - Option 2: Replace the api_key value below with your key
 */

return [
    // Choose provider: 'groq' or 'openrouter'
    'provider' => 'openrouter',
    
    // Groq API - Free tier (sign up at https://groq.com)
    'groq' => [
        'api_key' => getenv('GROQ_API_KEY') ?: '',
        'model' => 'llama-3.1-70b-versatile',
        'endpoint' => 'https://api.groq.com/openai/v1/chat/completions'
    ],
    
    // OpenRouter API - Free tier (sign up at https://openrouter.ai)
    'openrouter' => [
        'api_key' => getenv('OPENROUTER_API_KEY') ?: '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',
        'model' => 'microsoft/phi-3.5-mini-instruct-free',
        'endpoint' => 'https://openrouter.ai/api/v1/chat/completions',
        'referer' => 'appolios',
        'title' => 'Appolios E-Learning'
    ],
    
    // Fallback settings
    'use_fallback' => true,
    
    // Badge prompts - optimized for short generation
    'system_prompt' => 'You are a creative badge generator. Create short, unique, inspiring badge names for course completion. Always respond with valid JSON only.',
    'user_prompt_template' => 'Create a fun badge for completing a course.
Course: {course_title}
Type: {category}
Time: {time_taken}
Lessons: {lesson_count}

Return JSON: {"name": "Name", "icon": "emoji", "description": "desc"}'
];