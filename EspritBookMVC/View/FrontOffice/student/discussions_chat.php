<?php
$studentSidebarActive = 'discussions';
$dc = $discussion_chat ?? ['discussion_title' => '', 'group_name' => '', 'back_url' => '#', 'upload_url' => ''];
?>
<div class="dashboard student-events-page collab-hub collab-chat-root">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main">
                <?php require __DIR__ . '/partials/collab_hub_styles.php'; ?>

                <div class="collab-chat-layout">
                    <header class="collab-chat-head">
                        <div>
                            <div class="collab-chat-live"><span class="collab-chat-dot" aria-hidden="true"></span> Realtime room</div>
                            <h2>Live discussion</h2>
                            <p class="collab-chat-sub">
                                <strong><?= htmlspecialchars((string) ($group['nom_groupe'] ?? 'Group')) ?></strong>
                                <span style="opacity:.65;"> · </span>
                                <?= htmlspecialchars((string) ($discussion['titre'] ?? 'Discussion')) ?>
                            </p>
                        </div>
                        <a class="collab-btn-ghost" href="<?= htmlspecialchars((string) ($dc['back_url'] ?? '#'), ENT_QUOTES, 'UTF-8') ?>">
                            <i class="bi bi-arrow-left" aria-hidden="true"></i> Back
                        </a>
                    </header>

                    <div id="chatMessages" class="collab-chat-stream chat-messages"></div>

                    <form id="chatForm" class="collab-chat-composer chat-input-wrap" data-skip-validation="1" novalidate>
                        <input id="chatFileInput" type="file" style="display:none;" />
                        <button id="pickImageBtn" class="chat-attach-btn" type="button" title="Send image" aria-label="Send image"><i class="bi bi-image"></i></button>
                        <button id="pickFileBtn" class="chat-attach-btn" type="button" title="Send attachment" aria-label="Send attachment"><i class="bi bi-paperclip"></i></button>
                        <button id="recordBtn" class="chat-attach-btn" type="button" title="Record voice note" aria-label="Record voice note"><i class="bi bi-mic-fill"></i></button>
                        <input id="chatInput" type="text" placeholder="Write something…" autocomplete="off" />
                        <button class="btn btn-primary" type="submit">Send</button>
                        <span id="chatUploadState" class="chat-uploading"></span>
                    </form>
                </div>
                <style>
                    .collab-chat-root .chat-attach-btn.recording { background:#fee2e2; border-color:#ef4444; color:#991b1b; }
                    .collab-chat-root .chat-uploading { font-size:12px; color:#64748b; margin-left:6px; flex:1 1 100%; }
                    @media (min-width:520px){ .collab-chat-root .chat-uploading { flex:0 0 auto; } }
                </style>
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
    const userName = <?= json_encode((string) $currentUserName) ?>;
    const messagesEl = document.getElementById('chatMessages');
    const form = document.getElementById('chatForm');
    const input = document.getElementById('chatInput');
    const fileInput = document.getElementById('chatFileInput');
    const pickImageBtn = document.getElementById('pickImageBtn');
    const pickFileBtn = document.getElementById('pickFileBtn');
    const recordBtn = document.getElementById('recordBtn');
    const uploadState = document.getElementById('chatUploadState');
    const uploadUrl = <?= json_encode((string) ($dc['upload_url'] ?? '')) ?>;
    let mediaRecorder = null;
    let mediaChunks = [];

    const appendMessage = (payload) => {
        const row = document.createElement('div');
        const isSelf = Number(payload.userId) === Number(userId);
        row.className = 'chat-row' + (isSelf ? ' self' : '');

        const wrap = document.createElement('div');
        wrap.className = 'collab-chat-msg-wrap';

        if (!isSelf) {
            const av = document.createElement('div');
            av.className = 'collab-chat-avatar';
            const n = String(payload.userName || 'User').trim();
            av.textContent = n ? n.charAt(0).toUpperCase() : '?';
            av.setAttribute('aria-hidden', 'true');
            wrap.appendChild(av);
        }

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
            img.style.borderRadius = '10px';
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
        meta.textContent = parts.join(' · ');
        bubble.appendChild(author);
        bubble.appendChild(text);
        bubble.appendChild(meta);
        wrap.appendChild(bubble);
        row.appendChild(wrap);
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
    socket.on('connect', () => {
        socket.emit('join-room', { room, userId, userName });
    });
    socket.on('room-history', (history) => {
        messagesEl.innerHTML = '';
        (history || []).forEach(appendMessage);
    });
    socket.on('chat-message', appendMessage);

    const sendAttachment = async (file) => {
        if (!file) return;
        uploadState.textContent = 'Uploading…';
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
