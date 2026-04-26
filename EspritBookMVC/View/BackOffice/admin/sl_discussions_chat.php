<?php $adminSidebarActive = 'sl-discussions'; ?>
<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main">
                <div class="dashboard-header" style="display:flex;justify-content:space-between;align-items:center;">
                    <div>
                        <h1>Admin Live Chat</h1>
                        <p>Discussion: <strong><?= htmlspecialchars((string) ($discussion['titre'] ?? $discussion['title'] ?? 'Discussion')) ?></strong></p>
                    </div>
                    <a class="btn btn-secondary" href="<?= APP_ENTRY ?>?url=admin/sl-discussions">Back</a>
                </div>

                <style>
                    .chat-shell { background:#fff; border:1px solid #e2e8f0; border-radius:14px; overflow:hidden; }
                    .chat-messages { height:420px; overflow-y:auto; padding:14px; background:#f8fafc; }
                    .chat-row { margin-bottom:10px; display:flex; }
                    .chat-row.self { justify-content:flex-end; }
                    .chat-bubble { max-width:70%; border-radius:12px; padding:10px 12px; background:#fff; border:1px solid #e2e8f0; }
                    .chat-row.self .chat-bubble { background:#dbeafe; border-color:#bfdbfe; }
                    .chat-author { font-size:12px; font-weight:700; color:#334155; margin-bottom:4px; }
                    .chat-text { color:#0f172a; white-space:pre-wrap; word-break:break-word; }
                    .chat-meta { font-size:11px; color:#64748b; margin-top:4px; }
                    .chat-input-wrap { border-top:1px solid #e2e8f0; padding:12px; display:flex; gap:10px; align-items:center; }
                    .chat-input-wrap input { flex:1; border:1px solid #cbd5e1; border-radius:10px; padding:10px 12px; }
                    .chat-attach-btn { border:1px solid #cbd5e1; border-radius:10px; background:#fff; width:38px; height:38px; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; font-size:16px; transition:all .2s ease; }
                    .chat-attach-btn:hover { border-color:#94a3b8; background:#f8fafc; transform:translateY(-1px); }
                    .chat-attach-btn.recording { background:#fee2e2; border-color:#ef4444; color:#991b1b; }
                    .chat-uploading { font-size:12px; color:#64748b; margin-left:6px; }
                </style>

                <div class="chat-shell">
                    <div id="chatMessages" class="chat-messages"></div>
                    <form id="chatForm" class="chat-input-wrap" data-skip-validation="1" novalidate>
                        <input id="chatFileInput" type="file" style="display:none;" />
                        <button id="pickImageBtn" class="chat-attach-btn" type="button" title="Send image" aria-label="Send image"><i class="bi bi-image"></i></button>
                        <button id="pickFileBtn" class="chat-attach-btn" type="button" title="Send attachment" aria-label="Send attachment"><i class="bi bi-paperclip"></i></button>
                        <button id="recordBtn" class="chat-attach-btn" type="button" title="Record voice note" aria-label="Record voice note"><i class="bi bi-mic-fill"></i></button>
                        <input id="chatInput" type="text" placeholder="Type your message..." autocomplete="off" />
                        <button class="btn btn-primary" type="submit">Send</button>
                        <span id="chatUploadState" class="chat-uploading"></span>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= htmlspecialchars((string) $socketUrl) ?>/socket.io/socket.io.js"></script>
<script>
(() => {
    const socketUrl = <?= json_encode((string) $socketUrl) ?>;
    const room = <?= json_encode((string) $chatRoom) ?>;
    const userId = <?= json_encode((int) $currentUserId) ?>;
    const userName = <?= json_encode((string) ('[ADMIN] ' . ($currentUserName ?? 'Admin'))) ?>;
    const messagesEl = document.getElementById('chatMessages');
    const form = document.getElementById('chatForm');
    const input = document.getElementById('chatInput');
    const fileInput = document.getElementById('chatFileInput');
    const pickImageBtn = document.getElementById('pickImageBtn');
    const pickFileBtn = document.getElementById('pickFileBtn');
    const recordBtn = document.getElementById('recordBtn');
    const uploadState = document.getElementById('chatUploadState');
    const uploadUrl = <?= json_encode((string) (APP_ENTRY . '?url=admin/sl-discussions/' . (int) ($discussion['id_discussion'] ?? $discussion['id'] ?? 0) . '/upload')) ?>;
    let mediaRecorder = null;
    let mediaChunks = [];

    const appendMessage = (payload) => {
        const row = document.createElement('div');
        row.className = 'chat-row' + ((Number(payload.userId) === Number(userId)) ? ' self' : '');
        const bubble = document.createElement('div');
        bubble.className = 'chat-bubble';
        const author = document.createElement('div');
        author.className = 'chat-author';
        author.textContent = payload.userName || 'User';
        const text = document.createElement('div');
        text.className = 'chat-text';
        const type = String(payload.messageType || 'text');
        if (type === 'image' && payload.fileUrl) {
            const img = document.createElement('img');
            img.src = payload.fileUrl;
            img.alt = payload.fileName || 'Image';
            img.style.maxWidth = '100%';
            img.style.borderRadius = '8px';
            img.style.border = '1px solid #e2e8f0';
            text.appendChild(img);
        } else if (type === 'audio' && payload.fileUrl) {
            const audio = document.createElement('audio');
            audio.controls = true;
            audio.src = payload.fileUrl;
            audio.style.width = '100%';
            text.appendChild(audio);
        } else if (type === 'file' && payload.fileUrl) {
            const link = document.createElement('a');
            link.href = payload.fileUrl;
            link.target = '_blank';
            link.rel = 'noopener noreferrer';
            link.textContent = payload.fileName || 'Download attachment';
            text.appendChild(link);
        } else {
            text.textContent = payload.message || '';
        }
        const meta = document.createElement('div');
        meta.className = 'chat-meta';
        const parts = [new Date(payload.ts || Date.now()).toLocaleTimeString()];
        if (payload.fileName && type !== 'text') parts.push(payload.fileName);
        meta.textContent = parts.join(' - ');
        bubble.appendChild(author);
        bubble.appendChild(text);
        bubble.appendChild(meta);
        row.appendChild(bubble);
        messagesEl.appendChild(row);
        messagesEl.scrollTop = messagesEl.scrollHeight;
    };

    if (typeof io !== 'function') {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            alert('Realtime chat server is unavailable. Please start the Socket.IO server.');
        });
        return;
    }

    const socket = io(socketUrl, { transports: ['websocket', 'polling'] });
    socket.on('connect', () => socket.emit('join-room', { room, userId, userName }));
    socket.on('room-history', (history) => {
        messagesEl.innerHTML = '';
        (history || []).forEach(appendMessage);
    });
    socket.on('chat-message', appendMessage);

    const sendAttachment = async (file) => {
        if (!file) return;
        uploadState.textContent = 'Uploading...';
        const fd = new FormData();
        fd.append('attachment', file);
        try {
            const response = await fetch(uploadUrl, { method: 'POST', body: fd, credentials: 'same-origin' });
            const json = await response.json();
            if (!response.ok || !json.ok) {
                uploadState.textContent = '';
                alert((json && json.error) ? json.error : 'Upload failed.');
                return;
            }
            const data = json.data || {};
            socket.emit('chat-message', {
                room,
                userId,
                userName,
                message: '',
                messageType: data.messageType || 'file',
                fileUrl: data.url || '',
                fileName: data.fileName || file.name || 'attachment'
            });
            uploadState.textContent = '';
        } catch (_e) {
            uploadState.textContent = '';
            alert('Upload failed.');
        }
    };

    pickImageBtn.addEventListener('click', () => {
        fileInput.value = '';
        fileInput.accept = 'image/*';
        fileInput.click();
    });
    pickFileBtn.addEventListener('click', () => {
        fileInput.value = '';
        fileInput.accept = '.pdf,.zip,.txt,.doc,.docx,image/*,audio/*';
        fileInput.click();
    });
    fileInput.addEventListener('change', () => {
        const file = fileInput.files && fileInput.files[0] ? fileInput.files[0] : null;
        if (file) sendAttachment(file);
    });

    recordBtn.addEventListener('click', async () => {
        if (mediaRecorder && mediaRecorder.state === 'recording') {
            mediaRecorder.stop();
            return;
        }
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            alert('Voice recording is not supported on this browser.');
            return;
        }
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            mediaChunks = [];
            mediaRecorder = new MediaRecorder(stream);
            mediaRecorder.ondataavailable = (ev) => {
                if (ev.data && ev.data.size > 0) mediaChunks.push(ev.data);
            };
            mediaRecorder.onstop = async () => {
                stream.getTracks().forEach((t) => t.stop());
                recordBtn.classList.remove('recording');
                recordBtn.innerHTML = '<i class="bi bi-mic-fill"></i>';
                if (mediaChunks.length === 0) return;
                const blob = new Blob(mediaChunks, { type: mediaRecorder.mimeType || 'audio/webm' });
                const ext = blob.type.includes('ogg') ? 'ogg' : (blob.type.includes('mpeg') ? 'mp3' : 'webm');
                const file = new File([blob], `voice-note.${ext}`, { type: blob.type || 'audio/webm' });
                await sendAttachment(file);
            };
            mediaRecorder.start();
            recordBtn.classList.add('recording');
            recordBtn.innerHTML = '<i class="bi bi-stop-fill"></i>';
        } catch (_e) {
            alert('Microphone permission denied.');
        }
    });

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        const message = (input.value || '').trim();
        if (!message) return;
        socket.emit('chat-message', { room, userId, userName, message, messageType: 'text' });
        input.value = '';
        input.focus();
    });
})();
</script>
