(function () {
    function bootDiscussionChat() {
        var formGuard = document.getElementById('chatForm');
        if (formGuard) {
            formGuard.addEventListener(
                'submit',
                function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                },
                true
            );
        }

        var root = document.querySelector('.collab-chat-root');
        if (!root && formGuard) {
            root = formGuard.closest('.dashboard') || formGuard.closest('.admin-layout') || formGuard.closest('.container');
        }
        if (!root) {
            return;
        }

        var cfg = window.DiscussionChatConfig || {};
        var socketUrl = String(cfg.socketUrl || '');
        var room = String(cfg.chatRoom || '');
        var userId = Number(cfg.currentUserId || 0);
        var userName = String(cfg.currentUserName || 'User');
        var uploadUrl = String(cfg.uploadUrl || '');
        var summarizeUrl = String(cfg.summarizeUrl || '');
        var socketCandidates = [socketUrl, 'http://127.0.0.1:3001', 'http://localhost:3001'];
        var uniqueSocketCandidates = [];
        socketCandidates.forEach(function (candidate) {
            var value = String(candidate || '').trim();
            if (!value) return;
            if (uniqueSocketCandidates.indexOf(value) === -1) uniqueSocketCandidates.push(value);
        });

        var messagesEl = document.getElementById('chatMessages');
        var form = formGuard || document.getElementById('chatForm');
        var input = document.getElementById('chatInput');
        var fileInput = document.getElementById('chatFileInput');
        var pickImageBtn = document.getElementById('pickImageBtn');
        var pickFileBtn = document.getElementById('pickFileBtn');
        var recordBtn = document.getElementById('recordBtn');
        var uploadState = document.getElementById('chatUploadState');
        var detailsPanel = document.getElementById('chatDetailsPanel');
        function enforceDetailsPanelRightSide() {
            if (!detailsPanel) return;
            detailsPanel.style.left = 'auto';
            detailsPanel.style.right = '12px';
        }
        enforceDetailsPanelRightSide();

        var detailsBackdrop = document.getElementById('chatDetailsBackdrop');
        var detailsToggleBtn = document.getElementById('detailsToggleBtn');
        var chatDetailsDoneBtn = document.getElementById('chatDetailsDoneBtn');
        var chatThemeGrid = document.getElementById('chatThemeGrid');
        var mediaListEl = document.getElementById('chatMediaList');

        var CHAT_APPEARANCES = [
            { id: 'default', label: 'Default', swatchLight: '#f1f5f9', swatchDeep: '#64748b' },
            { id: 'paper', label: 'Paper', swatchLight: '#ffffff', swatchDeep: '#e2e8f0' },
            { id: 'ocean', label: 'Ocean', swatchLight: '#bae6fd', swatchDeep: '#0284c7' },
            { id: 'dusk', label: 'Dusk', swatchLight: '#e9d5ff', swatchDeep: '#7c3aed' },
            { id: 'mint', label: 'Mint', swatchLight: '#bbf7d0', swatchDeep: '#16a34a' },
            { id: 'rose', label: 'Rose', swatchLight: '#fecdd3', swatchDeep: '#e11d48' }
        ];
        var APPEARANCE_IDS = CHAT_APPEARANCES.map(function (a) { return a.id; });
        var appearanceStorageKey = 'appolios_chat_appearance_' + String(room || 'room');

        var mediaRecorder = null;
        var mediaChunks = [];
        var allTextMessages = [];
        var sharedMedia = [];
        var locationRowsByUserId = {};
        var activeSocket = null;

        function warnChatDisconnected() {
            var msg =
                'Chat is not connected to the server yet. Wait a moment, or start the realtime app (Node / Socket.IO on port 3001) and refresh the page.';
            if (window.Swal && typeof window.Swal.fire === 'function') {
                window.Swal.fire({
                    icon: 'warning',
                    title: 'Not connected',
                    text: msg,
                    confirmButtonColor: '#2563eb'
                });
            } else {
                alert(msg);
            }
        }

        function sendChatText() {
            if (!input) return;
            if (!userId) {
                alert('Your session is missing a user id. Please log out and sign in again, then reopen this chat.');
                return;
            }
            var message = String(input.value || '').trim();
            if (!message) return;
            if (!activeSocket || !activeSocket.connected) {
                warnChatDisconnected();
                return;
            }
            activeSocket.emit('chat-message', {
                room: room,
                userId: userId,
                userName: userName,
                message: message,
                messageType: 'text'
            });
            input.value = '';
            input.focus();
        }

        var sendBtn = document.getElementById('chatSendBtn');
        if (sendBtn) {
            sendBtn.addEventListener('click', function (e) {
                e.preventDefault();
                sendChatText();
            });
        }
        if (input) {
            input.addEventListener('keydown', function (e) {
                if (e.key !== 'Enter') return;
                e.preventDefault();
                sendChatText();
            });
        }
        if (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                sendChatText();
            });
        }

        function ensureIo() {
            if (typeof io === 'function') return Promise.resolve(true);
            return socketCandidates.reduce(function (p, base) {
                return p.then(function (ready) {
                    if (ready || !base) return ready;
                    return new Promise(function (resolve) {
                        var tag = document.createElement('script');
                        tag.src = String(base).replace(/\/+$/, '') + '/socket.io/socket.io.js';
                        tag.async = true;
                        tag.onload = function () { resolve(typeof io === 'function'); };
                        tag.onerror = function () { resolve(false); };
                        document.head.appendChild(tag);
                    });
                });
            }, Promise.resolve(false));
        }

        function fetchSummaryFromController(text, modeLabel) {
            if (!summarizeUrl) {
                return Promise.resolve({ ok: false, error: 'Summarizer endpoint is not configured.' });
            }
            return fetch(summarizeUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ text: text, mode: modeLabel })
            }).then(function (response) {
                return response.json().then(function (json) {
                    if (!response.ok || !json.ok) {
                        return { ok: false, error: (json && json.error) ? String(json.error) : 'Failed to summarize text.' };
                    }
                    return { ok: true, data: json.data || {} };
                });
            }).catch(function () {
                return { ok: false, error: 'Failed to summarize text.' };
            });
        }

        function fireDiscussionSwal(options) {
            if (!(window.Swal && typeof window.Swal.fire === 'function')) return null;
            var base = {
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#f97316',
                reverseButtons: true,
                backdrop: 'rgba(15, 23, 42, 0.55)',
                customClass: {
                    popup: 'ds-swal-popup',
                    title: 'ds-swal-title',
                    confirmButton: 'ds-swal-confirm',
                    cancelButton: 'ds-swal-cancel'
                }
            };
            var merged = Object.assign({}, base, options || {});
            if (options && options.customClass) {
                merged.customClass = Object.assign({}, base.customClass, options.customClass);
            }
            return window.Swal.fire(merged);
        }

        function getFiveConsecutiveBySameSender(ownerId, untilIndex) {
            var seq = [];
            for (var i = untilIndex; i >= 0; i -= 1) {
                var item = allTextMessages[i];
                if (!item || String(item.userId) !== String(ownerId)) break;
                seq.unshift(item.text);
                if (seq.length === 5) break;
            }
            return seq;
        }

        var CHAT_MEDIA_BROKEN_SRC = 'data:image/svg+xml,' + encodeURIComponent(
            '<svg xmlns="http://www.w3.org/2000/svg" width="160" height="100" viewBox="0 0 160 100"><rect fill="#f1f5f9" width="160" height="100" rx="10"/><path fill="#94a3b8" d="M48 72h64L88 40 72 58 60 44z"/><circle fill="#cbd5e1" cx="112" cy="36" r="8"/></svg>'
        );

        function encodePathSegments(path) {
            var raw = String(path || '');
            if (raw === '') return '';
            var segs = raw.split('/');
            var out = [];
            for (var i = 0; i < segs.length; i++) {
                if (segs[i] === '') {
                    out.push('');
                    continue;
                }
                try {
                    out.push(encodeURIComponent(decodeURIComponent(segs[i])));
                } catch (e2) {
                    out.push(encodeURIComponent(segs[i]));
                }
            }
            return out.join('/');
        }

        function encodeChatAssetUrl(u) {
            var s = String(u || '').trim();
            if (!s) return '';
            try {
                if (/^https?:\/\//i.test(s)) {
                    var abs = new URL(s);
                    if (typeof window !== 'undefined' && window.location && /\/uploads\/chat\//i.test(abs.pathname)) {
                        abs.protocol = window.location.protocol;
                        abs.host = window.location.host;
                    }
                    abs.pathname = encodePathSegments(abs.pathname);
                    return abs.href;
                }
                if (s.indexOf('//') === 0 && typeof window !== 'undefined' && window.location) {
                    return encodeChatAssetUrl(window.location.protocol + s);
                }
                if (s.charAt(0) === '/') {
                    var q = s.indexOf('?');
                    var h = s.indexOf('#');
                    var end = s.length;
                    if (q >= 0) end = Math.min(end, q);
                    if (h >= 0) end = Math.min(end, h);
                    var pathOnly = s.slice(0, end);
                    var tail = s.slice(end);
                    return encodePathSegments(pathOnly) + tail;
                }
                return s;
            } catch (e) {
                return s;
            }
        }

        function renderMediaList() {
            if (!mediaListEl) return;
            if (!sharedMedia.length) {
                mediaListEl.innerHTML = '<div class="chat-media-empty">No media shared yet.</div>';
                return;
            }
            mediaListEl.innerHTML = sharedMedia.slice().reverse().map(function (item) {
                var safeUser = String(item.userName || 'User').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                var safeTime = new Date(item.ts || Date.now()).toLocaleString();
                var href = encodeChatAssetUrl(item.url);
                if (item.type === 'image') {
                    var safeAlt = String(item.fileName || 'Shared image').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                    var ph = CHAT_MEDIA_BROKEN_SRC.replace(/'/g, "\\'");
                    return '<div class="chat-media-item"><a href="' + href + '" target="_blank" rel="noopener noreferrer"><img src="' + href + '" alt="' + safeAlt + '" loading="lazy" onerror="this.onerror=null;this.classList.add(\'chat-media-thumb--broken\');this.src=\'' + ph + '\';"></a><small>' + safeUser + ' · ' + safeTime + '</small></div>';
                }
                var label = item.type === 'audio' ? (item.fileName || 'Voice note') : (item.fileName || 'Attachment');
                var safeLabel = String(label).replace(/</g, '&lt;').replace(/>/g, '&gt;');
                return '<div class="chat-media-item"><a href="' + href + '" target="_blank" rel="noopener noreferrer">' + safeLabel + '</a><small>' + safeUser + ' · ' + safeTime + '</small></div>';
            }).join('');
        }

        function toggleDetails(open) {
            if (!detailsPanel || !detailsToggleBtn) return;
            enforceDetailsPanelRightSide();
            var shouldOpen = typeof open === 'boolean' ? open : !detailsPanel.classList.contains('is-open');
            detailsPanel.classList.toggle('is-open', shouldOpen);
            if (detailsBackdrop) detailsBackdrop.classList.toggle('is-open', shouldOpen);
            detailsPanel.setAttribute('aria-hidden', shouldOpen ? 'false' : 'true');
            if (detailsBackdrop) detailsBackdrop.setAttribute('aria-hidden', shouldOpen ? 'false' : 'true');
            detailsToggleBtn.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');
            if (shouldOpen) {
                var scrollArea = detailsPanel.querySelector('.chat-details-scroll--light');
                if (scrollArea) scrollArea.scrollTop = 0;
            }
        }

        function buildThemeGrid() {
            if (!chatThemeGrid) return;
            chatThemeGrid.innerHTML = '';
            CHAT_APPEARANCES.forEach(function (t) {
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'chat-theme-swatch-btn';
                btn.setAttribute('data-chat-appearance', t.id);
                btn.setAttribute('role', 'radio');
                btn.setAttribute('aria-checked', 'false');
                btn.style.setProperty('--swatch-light', t.swatchLight);
                btn.style.setProperty('--swatch-deep', t.swatchDeep);
                var tile = document.createElement('span');
                tile.className = 'chat-theme-swatch-btn__tile';
                var lab = document.createElement('span');
                lab.className = 'chat-theme-swatch-btn__label';
                lab.textContent = t.label;
                btn.appendChild(tile);
                btn.appendChild(lab);
                btn.addEventListener('click', function () {
                    applyChatAppearance(t.id, true);
                });
                chatThemeGrid.appendChild(btn);
            });
        }

        function syncThemeButtons(activeId) {
            if (!chatThemeGrid) return;
            var nodes = chatThemeGrid.querySelectorAll('.chat-theme-swatch-btn');
            for (var i = 0; i < nodes.length; i++) {
                var id = nodes[i].getAttribute('data-chat-appearance');
                var on = id === activeId;
                nodes[i].classList.toggle('is-selected', on);
                nodes[i].setAttribute('aria-checked', on ? 'true' : 'false');
            }
        }

        function applyChatAppearance(id, persist) {
            var theme = APPEARANCE_IDS.indexOf(id) !== -1 ? id : 'default';
            root.setAttribute('data-chat-appearance', theme);
            syncThemeButtons(theme);
            if (persist) {
                try {
                    localStorage.setItem(appearanceStorageKey, theme);
                } catch (e) {
                }
            }
        }

        function initialAppearance() {
            try {
                var saved = localStorage.getItem(appearanceStorageKey);
                if (saved && APPEARANCE_IDS.indexOf(saved) !== -1) return saved;
            } catch (e) {
            }
            return 'default';
        }

        function isLikelyAudioAttachment(payload) {
            var t = String(payload.messageType || '').toLowerCase();
            if (t === 'audio') return true;
            var name = String(payload.fileName || '').toLowerCase();
            var url = String(payload.fileUrl || '').toLowerCase();
            return /\.(webm|ogg|oga|opus|mp3|wav|m4a|aac|flac)(\?|#|$)/i.test(name) || /\.(webm|ogg|oga|opus|mp3|wav|m4a|aac|flac)(\?|#|$)/i.test(url);
        }

        function messageTimestampMs(payload) {
            if (payload.ts != null && payload.ts !== '') {
                var n = Number(payload.ts);
                if (!isNaN(n) && n > 0) return n;
            }
            if (payload.createdAt) {
                var d = new Date(payload.createdAt);
                var x = d.getTime();
                if (!isNaN(x)) return x;
            }
            return Date.now();
        }

        function appendMessage(payload) {
            if (!messagesEl) return;
            var msgIndex = allTextMessages.length;
            var type = String(payload.messageType || 'text');
            var textValue = type === 'text' ? String(payload.message || '') : '';
            if (type === 'text') {
                allTextMessages.push({ userId: payload.userId, text: textValue, type: type });
            }

            if ((type === 'image' || type === 'audio' || type === 'file') && payload.fileUrl) {
                sharedMedia.push({
                    type: type,
                    url: String(payload.fileUrl || ''),
                    fileName: String(payload.fileName || ''),
                    userName: String(payload.userName || 'User'),
                    ts: messageTimestampMs(payload)
                });
                renderMediaList();
            }

            var row = document.createElement('div');
            var isSelf = Number(payload.userId) === Number(userId);
            row.className = 'chat-row' + (isSelf ? ' self' : '');
            var wrap = document.createElement('div');
            wrap.className = 'collab-chat-msg-wrap';

            if (!isSelf) {
                var av = document.createElement('div');
                av.className = 'collab-chat-avatar';
                var n = String(payload.userName || 'User').trim();
                av.textContent = n ? n.charAt(0).toUpperCase() : '?';
                av.setAttribute('aria-hidden', 'true');
                wrap.appendChild(av);
            }

            if (type === 'location') {
                var uidKey = String(payload.userId || '0');
                var sessionKey = String(payload.locationSessionId || '').trim();
                if (!sessionKey && payload.fileUrl) {
                    var rawLocationUrl = String(payload.fileUrl || '');
                    var sidMatch = rawLocationUrl.match(/[?&]sid=([^&#]+)/i);
                    if (sidMatch && sidMatch[1]) {
                        try {
                            sessionKey = decodeURIComponent(String(sidMatch[1]));
                        } catch (_e) {
                            sessionKey = String(sidMatch[1]);
                        }
                    }
                }
                var rowKey = uidKey + ':' + (sessionKey || 'legacy');
                var existingRow = locationRowsByUserId[rowKey] || null;
                var viewHref = payload.fileUrl ? encodeChatAssetUrl(payload.fileUrl) : '';
                var sharingName = String(payload.userName || 'User');

                if (existingRow) {
                    var link = existingRow.querySelector('a[data-location-view="1"]');
                    if (link && viewHref) {
                        link.href = viewHref;
                        link.setAttribute('data-location-href', viewHref);
                    }
                    var sharingLine = existingRow.querySelector('[data-location-sharing="1"]');
                    if (sharingLine) {
                        sharingLine.setAttribute('data-location-user', sharingName);
                        sharingLine.textContent = sharingName + ' is sharing';
                    }
                    var meta = existingRow.querySelector('[data-location-meta="1"]');
                    if (meta) meta.textContent = new Date(messageTimestampMs(payload)).toLocaleTimeString();
                    messagesEl.scrollTop = messagesEl.scrollHeight;
                    return;
                }

                locationRowsByUserId[rowKey] = row;
                var safeSession = sessionKey.replace(/[^a-zA-Z0-9_-]/g, '');
                row.setAttribute('id', 'chat-location-' + uidKey + (safeSession ? ('-' + safeSession) : ''));
                row.setAttribute('data-location-user-id', uidKey);
                row.setAttribute('data-location-self', isSelf ? '1' : '0');
                if (safeSession) row.setAttribute('data-location-session-id', safeSession);
                row.setAttribute('data-location-created-ts', String(Number(payload.ts || Date.now()) || Date.now()));

                var bubble = document.createElement('div');
                bubble.className = 'chat-bubble';
                var author = document.createElement('div');
                author.className = 'chat-author';
                author.textContent = sharingName || 'User';
                var text = document.createElement('div');
                text.className = 'chat-text';

                var card = document.createElement('div');
                card.className = 'chat-location-card';

                var head = document.createElement('div');
                head.className = 'chat-location-card__head';
                var icon = document.createElement('span');
                icon.className = 'chat-location-card__icon';
                icon.setAttribute('aria-hidden', 'true');
                icon.textContent = '📍';
                var titles = document.createElement('div');
                titles.className = 'chat-location-card__titles';
                var eyebrow = document.createElement('div');
                eyebrow.className = 'chat-location-card__eyebrow';
                eyebrow.textContent = 'Live share';
                var liveTitle = document.createElement('div');
                liveTitle.className = 'chat-location-card__title';
                liveTitle.textContent = 'Location on map';
                titles.appendChild(eyebrow);
                titles.appendChild(liveTitle);
                head.appendChild(icon);
                head.appendChild(titles);
                card.appendChild(head);

                var sharingLineEl = document.createElement('p');
                sharingLineEl.setAttribute('data-location-sharing', '1');
                sharingLineEl.setAttribute('data-location-user', sharingName);
                sharingLineEl.className = 'chat-location-card__status';
                sharingLineEl.textContent = (sharingName || 'User') + ' is sharing';

                card.appendChild(sharingLineEl);

                if (viewHref) {
                    var viewBtn = document.createElement('a');
                    viewBtn.setAttribute('data-location-view', '1');
                    viewBtn.href = viewHref;
                    viewBtn.setAttribute('data-location-href', viewHref);
                    viewBtn.target = '_blank';
                    viewBtn.rel = 'noopener noreferrer';
                    viewBtn.className = 'chat-location-card__cta';
                    viewBtn.innerHTML = '<span class="chat-location-card__cta-icon" aria-hidden="true">🗺️</span><span class="chat-location-card__cta-label">Open in maps</span>';
                    card.appendChild(viewBtn);
                }

                text.appendChild(card);

                var meta = document.createElement('div');
                meta.className = 'chat-meta';
                meta.setAttribute('data-location-meta', '1');
                meta.textContent = new Date(messageTimestampMs(payload)).toLocaleTimeString();

                bubble.appendChild(author);
                bubble.appendChild(text);
                bubble.appendChild(meta);
                wrap.appendChild(bubble);
                row.appendChild(wrap);
                messagesEl.appendChild(row);
                messagesEl.scrollTop = messagesEl.scrollHeight;
                return;
            }

            var bubble = document.createElement('div');
            bubble.className = 'chat-bubble';
            var author = document.createElement('div');
            author.className = 'chat-author';
            author.textContent = payload.userName || 'User';
            var text = document.createElement('div');
            text.className = 'chat-text';
            var tsMs = messageTimestampMs(payload);

            if (type === 'image' && payload.fileUrl) {
                var img = document.createElement('img');
                var imgHref = encodeChatAssetUrl(payload.fileUrl);
                img.src = imgHref;
                img.alt = payload.fileName || 'Image';
                img.className = 'chat-bubble-img';
                img.style.maxWidth = '100%';
                img.style.borderRadius = '10px';
                img.addEventListener('error', function onImgErr() {
                    img.removeEventListener('error', onImgErr);
                    img.classList.add('chat-bubble-img--broken');
                    img.src = CHAT_MEDIA_BROKEN_SRC;
                });
                text.appendChild(img);
            } else if (payload.fileUrl && isLikelyAudioAttachment(payload)) {
                var voiceCard = document.createElement('div');
                voiceCard.className = 'chat-voice-card';
                var ribbon = document.createElement('div');
                ribbon.className = 'chat-voice-card__ribbon';
                ribbon.innerHTML =
                    '<span class="chat-voice-card__pulse" aria-hidden="true"></span>' +
                    '<span class="chat-voice-card__ribbon-label">Voice message</span>';
                voiceCard.appendChild(ribbon);
                var audio = document.createElement('audio');
                audio.className = 'chat-voice-player';
                audio.controls = true;
                audio.preload = 'metadata';
                audio.setAttribute('playsinline', '');
                audio.src = encodeChatAssetUrl(payload.fileUrl);
                voiceCard.appendChild(audio);
                var dl = document.createElement('a');
                dl.className = 'chat-voice-card__download';
                dl.href = encodeChatAssetUrl(payload.fileUrl);
                dl.target = '_blank';
                dl.rel = 'noopener noreferrer';
                dl.innerHTML = '<span class="chat-voice-card__dl-icon" aria-hidden="true">⬇</span> Save audio file';
                voiceCard.appendChild(dl);
                text.appendChild(voiceCard);
            } else if (type === 'file' && payload.fileUrl) {
                var link = document.createElement('a');
                link.href = encodeChatAssetUrl(payload.fileUrl);
                link.target = '_blank';
                link.rel = 'noopener noreferrer';
                link.textContent = payload.fileName || 'Download attachment';
                text.appendChild(link);
            } else {
                text.textContent = payload.message || '';
            }

            var meta = document.createElement('div');
            meta.className = 'chat-meta';
            var parts = [new Date(tsMs).toLocaleTimeString()];
            var hideFileInMeta = !!(payload.fileUrl && isLikelyAudioAttachment(payload));
            if (payload.fileName && type !== 'text' && !hideFileInMeta) parts.push(payload.fileName);
            meta.textContent = parts.join(' · ');

            bubble.appendChild(author);
            bubble.appendChild(text);
            bubble.appendChild(meta);

            if (type === 'text' && textValue.trim().length >= 120) {
                var actions = document.createElement('div');
                actions.className = 'chat-summary-actions';
                var summaryBtn = document.createElement('button');
                summaryBtn.type = 'button';
                summaryBtn.className = 'chat-summary-btn';
                summaryBtn.textContent = 'AI summarize';
                summaryBtn.addEventListener('click', function () {
                    var sequence = getFiveConsecutiveBySameSender(payload.userId, msgIndex);
                    var targetText = textValue;
                    var label = 'Long message';
                    if (sequence.length === 5) {
                        targetText = sequence.join(' ');
                        label = '5 consecutive texts';
                    }
                    summaryBtn.disabled = true;
                    fetchSummaryFromController(targetText, label).then(function (result) {
                        summaryBtn.disabled = false;
                        if (!result.ok) {
                            if (window.Swal && typeof window.Swal.fire === 'function') {
                                fireDiscussionSwal({
                                    icon: 'error',
                                    title: 'Summary failed',
                                    text: result.error || 'Could not generate summary.',
                                    confirmButtonText: 'Yes'
                                });
                            } else {
                                alert(result.error || 'Could not generate summary.');
                            }
                            return;
                        }
                        var summary = String((result.data && result.data.summary) || 'Could not generate a summary.');
                        if (window.Swal && typeof window.Swal.fire === 'function') {
                            fireDiscussionSwal({
                                icon: 'info',
                                title: 'AI Summary',
                                width: 'min(820px, 94vw)',
                                html: '<div class="chat-summary-modal-body" style="text-align:left;white-space:pre-wrap;max-height:62vh;overflow:auto;line-height:1.72;padding:14px 16px;border:1px solid rgba(148,163,184,.30);border-radius:14px;background:linear-gradient(180deg,#ffffff 0%,#f8fafc 100%);box-shadow:inset 0 1px 0 rgba(255,255,255,.7);">'
                                    + summary.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
                                    + '</div>',
                                confirmButtonText: 'Yes'
                            });
                        } else {
                            alert(summary);
                        }
                    });
                });
                actions.appendChild(summaryBtn);
                bubble.appendChild(actions);
            }

            wrap.appendChild(bubble);
            row.appendChild(wrap);
            messagesEl.appendChild(row);
            messagesEl.scrollTop = messagesEl.scrollHeight;
        }

        ensureIo().then(function (ioReady) {
            if (!ioReady || typeof io !== 'function') {
                return;
            }

            function connectSocket() {
                return new Promise(function (resolve) {
                    var index = 0;
                    function tryNext() {
                        if (index >= uniqueSocketCandidates.length) {
                            resolve(null);
                            return;
                        }
                        var target = uniqueSocketCandidates[index];
                        index += 1;
                        var socket = io(target, { transports: ['websocket', 'polling'], timeout: 15000 });
                        var settled = false;
                        socket.once('connect', function () {
                            if (settled) return;
                            settled = true;
                            resolve(socket);
                        });
                        socket.once('connect_error', function () {
                            if (settled) return;
                            settled = true;
                            socket.close();
                            tryNext();
                        });
                    }
                    tryNext();
                });
            }

            connectSocket().then(function (socket) {
                if (!socket) {
                    return;
                }
                activeSocket = socket;
                socket.emit('join-room', { room: room, userId: userId, userName: userName });
                window.__DISCUSSION_CHAT_SOCKET = socket;
                socket.on('room-history', function (history) {
                    if (!messagesEl) return;
                    messagesEl.innerHTML = '';
                    sharedMedia.length = 0;
                    renderMediaList();
                    locationRowsByUserId = {};
                    (history || []).forEach(appendMessage);
                });
                socket.on('chat-message', appendMessage);

                function sendAttachment(file) {
                    if (!file) return Promise.resolve();
                    if (uploadState) uploadState.textContent = 'Uploading...';
                    var fd = new FormData();
                    fd.append('attachment', file);
                    return fetch(uploadUrl, { method: 'POST', body: fd, credentials: 'same-origin' })
                        .then(function (response) { return response.json().then(function (json) { return { response: response, json: json }; }); })
                        .then(function (res) {
                            if (!res.response.ok || !res.json.ok) {
                                if (uploadState) uploadState.textContent = '';
                                alert((res.json && res.json.error) ? res.json.error : 'Upload failed.');
                                return;
                            }
                            var data = res.json.data || {};
                            socket.emit('chat-message', {
                                room: room,
                                userId: userId,
                                userName: userName,
                                message: '',
                                messageType: data.messageType || 'file',
                                fileUrl: data.url || '',
                                fileName: data.fileName || file.name || 'attachment'
                            });
                            if (uploadState) uploadState.textContent = '';
                        })
                        .catch(function () {
                            if (uploadState) uploadState.textContent = '';
                            alert('Upload failed.');
                        });
                }

                if (pickImageBtn && fileInput) {
                    pickImageBtn.addEventListener('click', function () {
                        fileInput.value = '';
                        fileInput.accept = 'image/*';
                        fileInput.click();
                    });
                }
                if (pickFileBtn && fileInput) {
                    pickFileBtn.addEventListener('click', function () {
                        fileInput.value = '';
                        fileInput.accept = '.pdf,.zip,.txt,.doc,.docx,image/*,audio/*';
                        fileInput.click();
                    });
                }
                if (fileInput) {
                    fileInput.addEventListener('change', function () {
                        var file = fileInput.files && fileInput.files[0] ? fileInput.files[0] : null;
                        if (file) sendAttachment(file);
                    });
                }

                if (recordBtn) {
                    recordBtn.addEventListener('click', function () {
                        if (mediaRecorder && mediaRecorder.state === 'recording') {
                            mediaRecorder.stop();
                            return;
                        }
                        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                            alert('Voice recording is not supported on this browser.');
                            return;
                        }
                        navigator.mediaDevices.getUserMedia({ audio: true }).then(function (stream) {
                            mediaChunks = [];
                            mediaRecorder = new MediaRecorder(stream);
                            mediaRecorder.ondataavailable = function (ev) { if (ev.data && ev.data.size > 0) mediaChunks.push(ev.data); };
                            mediaRecorder.onstop = function () {
                                stream.getTracks().forEach(function (t) { t.stop(); });
                                recordBtn.classList.remove('recording');
                                recordBtn.innerHTML = '<i class="bi bi-mic-fill"></i>';
                                if (mediaChunks.length === 0) return;
                                var blob = new Blob(mediaChunks, { type: mediaRecorder.mimeType || 'audio/webm' });
                                var ext = blob.type.indexOf('ogg') !== -1 ? 'ogg' : (blob.type.indexOf('mpeg') !== -1 ? 'mp3' : 'webm');
                                var voice = new File([blob], 'voice-note.' + ext, { type: blob.type || 'audio/webm' });
                                sendAttachment(voice);
                            };
                            mediaRecorder.start();
                            recordBtn.classList.add('recording');
                            recordBtn.innerHTML = '<i class="bi bi-stop-fill"></i>';
                        }).catch(function () {
                            alert('Microphone permission denied.');
                        });
                    });
                }
            });
        });

        if (detailsToggleBtn) detailsToggleBtn.addEventListener('click', function () { toggleDetails(); });
        if (chatDetailsDoneBtn) chatDetailsDoneBtn.addEventListener('click', function () { toggleDetails(false); });
        if (detailsBackdrop) detailsBackdrop.addEventListener('click', function () { toggleDetails(false); });
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && detailsPanel && detailsPanel.classList.contains('is-open')) toggleDetails(false);
        });
        ['default', 'midnight', 'mint', 'sunset', 'violet'].forEach(function (id) {
            root.classList.remove('chat-theme-' + id);
        });
        root.removeAttribute('data-chat-palette');
        buildThemeGrid();
        applyChatAppearance(initialAppearance(), false);
        renderMediaList();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bootDiscussionChat);
    } else {
        bootDiscussionChat();
    }
})();
