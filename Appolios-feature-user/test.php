<?php
require_once __DIR__ . '/Service/ChatbotService.php';
$chatbot = new ChatbotService();
echo $chatbot->chat(null, 'test', 'Hello!');