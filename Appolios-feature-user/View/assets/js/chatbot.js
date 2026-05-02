// Appolios Chatbot Widget

(function() {
    'use strict';
    
    // For direct API calls - relative path works best
    var APP_API = 'api/chatbot.php';
    
    // Check if we're on login page - if so, this is a fresh visit after logout
    const isLoginPage = window.location.href.indexOf('login') > -1 || window.location.href.indexOf('logout') > -1;
    const wasLoggedIn = localStorage.getItem('chatbot_was_logged_in');
    
    // Clear conversation if logging in fresh or after logout
    if ((isLoginPage && wasLoggedIn === 'true') || (isLoginPage && !localStorage.getItem('chatbot_session'))) {
        localStorage.removeItem('chatbot_session');
        localStorage.setItem('chatbot_session', generateSessionId());
    }
    
    // Track logged in status
    const hasHeader = document.querySelector('.neo-header');
    if (hasHeader) {
        localStorage.setItem('chatbot_was_logged_in', 'true');
    }
    
    let sessionId = localStorage.getItem('chatbot_session') || generateSessionId();
    localStorage.setItem('chatbot_session', sessionId);
    
    const widgetHtml = `
        <div id="chatbot-widget" class="chatbot-widget">
            <button id="chatbot-toggle" class="chatbot-toggle">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
            </button>
            <div id="chatbot-container" class="chatbot-container">
                <div class="chatbot-header">
                    <div class="chatbot-header-info">
                        <h4>Appolios Assistant</h4>
                        <span class="chatbot-status">Online</span>
                    </div>
                    <button id="chatbot-clear" class="chatbot-clear" title="Clear conversation">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                    </button>
                    <button id="chatbot-close" class="chatbot-close">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <div id="chatbot-messages" class="chatbot-messages">
                    <div class="chatbot-message chatbot-message-assistant">
                        <div class="chatbot-message-content">
                            Hi! I'm your Appolios learning assistant. Ask me about courses, lessons, or anything about your learning journey!
                        </div>
                    </div>
                </div>
                <div class="chatbot-input-area">
                    <input type="text" id="chatbot-input" placeholder="Type your message..." autocomplete="off">
                    <button id="chatbot-send">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                    </button>
                </div>
            </div>
        </div>
    `;
    
    function init() {
        const container = document.createElement('div');
        container.innerHTML = widgetHtml;
        document.body.appendChild(container);
        
        addStyles();
        bindEvents();
        loadHistory();
    }
    
    function addStyles() {
        const style = document.createElement('style');
        style.textContent = `
            .chatbot-widget {
                position: fixed;
                bottom: 20px;
                right: 20px;
                z-index: 9999;
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            }
            .chatbot-toggle {
                width: 60px;
                height: 60px;
                border-radius: 50%;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border: none;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }
            .chatbot-toggle:hover {
                transform: scale(1.1);
                box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
            }
            .chatbot-container {
                position: absolute;
                bottom: 80px;
                right: 0;
                width: 380px;
                height: 500px;
                background: white;
                border-radius: 16px;
                box-shadow: 0 10px 40px rgba(0,0,0,0.15);
                display: none;
                flex-direction: column;
                overflow: hidden;
            }
            .chatbot-container.active {
                display: flex;
            }
            .chatbot-header {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                padding: 16px 20px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                color: white;
            }
            .chatbot-header-info h4 {
                margin: 0;
                font-size: 16px;
                font-weight: 600;
            }
            .chatbot-status {
                font-size: 12px;
                opacity: 0.9;
            }
            .chatbot-header button {
                background: rgba(255,255,255,0.2);
                border: none;
                color: white;
                width: 32px;
                height: 32px;
                border-radius: 8px;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: background 0.2s;
            }
            .chatbot-header button:hover {
                background: rgba(255,255,255,0.3);
            }
            .chatbot-messages {
                flex: 1;
                overflow-y: auto;
                padding: 20px;
                display: flex;
                flex-direction: column;
                gap: 12px;
                background: #f8f9fa;
            }
            .chatbot-message {
                display: flex;
                max-width: 85%;
            }
            .chatbot-message-user {
                align-self: flex-end;
            }
            .chatbot-message-assistant {
                align-self: flex-start;
            }
            .chatbot-message-content {
                padding: 12px 16px;
                border-radius: 16px;
                font-size: 14px;
                line-height: 1.5;
            }
            .chatbot-message-user .chatbot-message-content {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                border-bottom-right-radius: 4px;
            }
            .chatbot-message-assistant .chatbot-message-content {
                background: white;
                color: #333;
                border: 1px solid #e0e0e0;
                border-bottom-left-radius: 4px;
            }
            .chatbot-input-area {
                padding: 16px;
                background: white;
                border-top: 1px solid #e0e0e0;
                display: flex;
                gap: 10px;
            }
            .chatbot-input-area input {
                flex: 1;
                padding: 12px 16px;
                border: 1px solid #e0e0e0;
                border-radius: 24px;
                font-size: 14px;
                outline: none;
                transition: border-color 0.2s;
            }
            .chatbot-input-area input:focus {
                border-color: #667eea;
            }
            .chatbot-input-area button {
                width: 44px;
                height: 44px;
                border-radius: 50%;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border: none;
                color: white;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: transform 0.2s;
            }
            .chatbot-input-area button:hover {
                transform: scale(1.05);
            }
            .chatbot-input-area button:disabled {
                opacity: 0.6;
                cursor: not-allowed;
            }
            .chatbot-typing {
                display: flex;
                gap: 4px;
                padding: 12px 16px;
                background: white;
                border: 1px solid #e0e0e0;
                border-radius: 16px;
                border-bottom-left-radius: 4px;
                width: fit-content;
            }
            .chatbot-typing span {
                width: 8px;
                height: 8px;
                background: #667eea;
                border-radius: 50%;
                animation: chatbot-typing-bounce 1.4s infinite ease-in-out;
            }
            .chatbot-typing span:nth-child(1) { animation-delay: 0s; }
            .chatbot-typing span:nth-child(2) { animation-delay: 0.2s; }
            .chatbot-typing span:nth-child(3) { animation-delay: 0.4s; }
            @keyframes chatbot-typing-bounce {
                0%, 80%, 100% { transform: scale(0.6); opacity: 0.5; }
                40% { transform: scale(1); opacity: 1; }
            }
            @media (max-width: 480px) {
                .chatbot-container {
                    width: calc(100vw - 40px);
                    height: calc(100vh - 120px);
                    right: -20px;
                }
            }
        `;
        document.head.appendChild(style);
    }
    
    function bindEvents() {
        const toggle = document.getElementById('chatbot-toggle');
        const container = document.getElementById('chatbot-container');
        const close = document.getElementById('chatbot-close');
        const clear = document.getElementById('chatbot-clear');
        const input = document.getElementById('chatbot-input');
        const send = document.getElementById('chatbot-send');
        
        toggle.addEventListener('click', () => {
            container.classList.toggle('active');
            if (container.classList.contains('active')) {
                input.focus();
            }
        });
        
        close.addEventListener('click', () => {
            container.classList.remove('active');
        });
        
        clear.addEventListener('click', clearConversation);
        
        send.addEventListener('click', sendMessage);
        
        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });
    }
    
    function sendMessage() {
        const input = document.getElementById('chatbot-input');
        const message = input.value.trim();
        
        if (!message) return;
        
        addMessage(message, 'user');
        input.value = '';
        
        showTyping();
        
        // Try direct API endpoint
        const url = 'api/chatbot.php?action=chat';
        
        console.log('Sending to:', url);
        
        fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                message: message,
                session_id: sessionId
            })
        })
        .then(res => {
            console.log('Status:', res.status);
            return res.text().then(text => {
                console.log('Raw response:', text.substring(0, 200));
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.log('JSON parse error:', e.message);
                    throw new Error('Invalid JSON: ' + text.substring(0, 100));
                }
            });
        })
        .then(data => {
            console.log('Data:', data);
            hideTyping();
            if (data.success) {
                addMessage(data.response, 'assistant');
            } else {
                addMessage('Error: ' + (data.error || 'unknown'), 'assistant');
            }
        })
        .catch(err => {
            console.error('Error:', err);
            hideTyping();
            addMessage('Error: ' + err.message, 'assistant');
        });
    }
    
    function addMessage(content, role) {
        const messages = document.getElementById('chatbot-messages');
        const div = document.createElement('div');
        div.className = `chatbot-message chatbot-message-${role}`;
        div.innerHTML = `<div class="chatbot-message-content">${escapeHtml(content)}</div>`;
        messages.appendChild(div);
        messages.scrollTop = messages.scrollHeight;
    }
    
    function showTyping() {
        const messages = document.getElementById('chatbot-messages');
        const div = document.createElement('div');
        div.className = 'chatbot-message chatbot-message-assistant';
        div.id = 'chatbot-typing-msg';
        div.innerHTML = `
            <div class="chatbot-typing">
                <span></span>
                <span></span>
                <span></span>
            </div>
        `;
        messages.appendChild(div);
        messages.scrollTop = messages.scrollHeight;
    }
    
    function hideTyping() {
        const typing = document.getElementById('chatbot-typing-msg');
        if (typing) typing.remove();
    }
    
    function loadHistory() {
        fetch('api/chatbot.php?action=history&session_id=' + encodeURIComponent(sessionId))
        .then(res => res.json())
        .then(data => {
            if (data.success && data.history && data.history.length > 0) {
                const messages = document.getElementById('chatbot-messages');
                messages.innerHTML = '';
                data.history.forEach(msg => {
                    addMessage(msg.message, msg.role);
                });
            }
        })
        .catch(() => {});
    }
    
    function clearConversation() {
        fetch('api/chatbot.php?action=clear', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                session_id: sessionId
            })
        })
        .then(() => {
            const messages = document.getElementById('chatbot-messages');
            messages.innerHTML = `
                <div class="chatbot-message chatbot-message-assistant">
                    <div class="chatbot-message-content">
                        Conversation cleared. How can I help you?
                    </div>
                </div>
            `;
        });
    }
    
    function generateSessionId() {
        return 'chat_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();