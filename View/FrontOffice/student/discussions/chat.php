<?php
$dc = $discussion_chat ?? ['discussion_title' => '', 'group_name' => '', 'back_url' => '#', 'upload_url' => ''];
$foPrefixChat = (string) ($foPrefix ?? 'student');
$did = (int) (($discussion['id_discussion'] ?? $discussion['id'] ?? 0));
$chatFormAction = $did > 0 ? (APP_ENTRY . '?url=' . rawurlencode($foPrefixChat . '/discussions/' . $did . '/chat')) : '';
?>
<link rel="stylesheet" href="<?= APP_URL ?>/View/assets/css/discussion-chat.css">
<?php require __DIR__ . '/../partials/collab_layout_start.php'; ?>
                <?php require __DIR__ . '/../partials/collab_hub_styles.php'; ?>

                <div class="collab-chat-shell">
                <div class="collab-chat-layout">
                    <div class="header collab-chat-head">
                        <a class="collab-btn-ghost collab-chat-head__back" href="<?= htmlspecialchars((string) ($dc['back_url'] ?? '#'), ENT_QUOTES, 'UTF-8') ?>">
                            <i class="bi bi-arrow-left" aria-hidden="true"></i> Back
                        </a>
                        <div class="collab-chat-head__center">
                            <div class="collab-chat-live"><span class="collab-chat-dot" aria-hidden="true"></span> Realtime room</div>
                            <h2>Live discussion</h2>
                            <p class="collab-chat-sub">
                                <strong><?= htmlspecialchars((string) ($group['nom_groupe'] ?? 'Group')) ?></strong>
                                <span style="opacity:.65;"> · </span>
                                <?= htmlspecialchars((string) ($discussion['titre'] ?? 'Discussion')) ?>
                            </p>
                        </div>
                        <button id="detailsToggleBtn" class="collab-btn-ghost collab-chat-head__details" type="button" aria-expanded="false" aria-controls="chatDetailsPanel">
                            <i class="bi bi-layout-sidebar-inset" aria-hidden="true"></i> Details
                        </button>
                    </div>

                    <div id="chatMessages" class="collab-chat-stream chat-messages"></div>

                    <form id="chatForm" class="collab-chat-composer chat-input-wrap" data-skip-validation="1" novalidate<?= $chatFormAction !== '' ? ' action="' . htmlspecialchars($chatFormAction, ENT_QUOTES, 'UTF-8') . '" method="get"' : '' ?>>
                        <input id="chatFileInput" type="file" style="display:none;" />
                        <button id="pickImageBtn" class="chat-attach-btn" type="button" title="Send image" aria-label="Send image"><i class="bi bi-image"></i></button>
                        <button id="pickFileBtn" class="chat-attach-btn" type="button" title="Send attachment" aria-label="Send attachment"><i class="bi bi-paperclip"></i></button>
                        <button id="recordBtn" class="chat-attach-btn" type="button" title="Record voice note" aria-label="Record voice note"><i class="bi bi-mic-fill"></i></button>
                        <input id="chatInput" type="text" placeholder="Write something…" autocomplete="off" />
                        <button type="button" id="chatSendBtn" class="btn btn-primary">Send</button>
                        <span id="chatUploadState" class="chat-uploading"></span>
                    </form>

                    <div class="collab-location-share" style="padding: 0 1.15rem 1rem;">
                        <div class="location-share-controls" style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;margin-top:10px;">
                            <button id="btn-share-location" type="button" class="btn btn-primary" style="border-radius:14px;font-weight:700;">
                                📍 Partager ma localisation
                            </button>
                        </div>
                    </div>
                </div>
                <div id="chatDetailsBackdrop" class="chat-details-backdrop" aria-hidden="true"></div>
                <aside id="chatDetailsPanel" class="chat-details-panel chat-details-panel--light" aria-hidden="true">
                    <div class="chat-details-light-head">
                        <button id="chatDetailsDoneBtn" type="button" class="chat-details-done-btn chat-details-done-btn--light">Done</button>
                    </div>
                    <div class="chat-details-scroll chat-details-scroll--light">
                        <details class="chat-details-accordion chat-details-accordion--light">
                            <summary class="chat-details-accordion__summary">Discussion info</summary>
                            <div class="chat-details-accordion__body">
                                <p class="chat-details-info-line"><strong><?= htmlspecialchars((string) ($group['nom_groupe'] ?? ''), ENT_QUOTES, 'UTF-8') ?></strong></p>
                                <p class="chat-details-info-line"><?= htmlspecialchars((string) ($discussion['titre'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                            </div>
                        </details>
                        <section class="chat-customize-section chat-customize-section--light" aria-labelledby="chatCustomizeHeading">
                            <h4 id="chatCustomizeHeading" class="chat-customize-heading chat-customize-heading--prominent">Customize chat</h4>
                            <div class="chat-theme-grid-shell">
                                <div id="chatThemeGrid" class="chat-theme-grid" role="radiogroup" aria-labelledby="chatCustomizeHeading"></div>
                            </div>
                        </section>
                        <section class="chat-details-card chat-details-card--media">
                            <h4 class="chat-details-card__title"><i class="bi bi-folder2-open" aria-hidden="true"></i> Shared media &amp; files</h4>
                            <p class="chat-details-card__hint">Images, voice notes, and attachments from this chat.</p>
                            <div id="chatMediaList" class="chat-media-list">
                                <div class="chat-media-empty">No media shared yet.</div>
                            </div>
                        </section>
                    </div>
                </aside>
                </div>
<?php require __DIR__ . '/../partials/collab_layout_end.php'; ?>
<style>
.ds-swal-popup{
    border-radius:18px !important;
    border:1px solid rgba(148,163,184,.35) !important;
    box-shadow:0 22px 52px rgba(15,23,42,.30) !important;
}
.ds-swal-title{ font-weight:800 !important; letter-spacing:-.02em !important; }
.ds-swal-confirm{
    border-radius:10px !important;
    padding:.62rem 1.45rem !important;
    font-weight:700 !important;
    box-shadow:0 10px 18px rgba(37,99,235,.28) !important;
}
.ds-swal-cancel{
    border-radius:10px !important;
    padding:.62rem 1.45rem !important;
    font-weight:700 !important;
    box-shadow:0 10px 18px rgba(234,88,12,.25) !important;
}
</style>
<script>
const DISCUSSION_ID = <?= (int) (($discussion['id_discussion'] ?? $discussion['id'] ?? 0)) ?>;
window.DiscussionChatConfig = {
    socketUrl: <?= json_encode((string) ($socketUrl ?? '')) ?>,
    chatRoom: <?= json_encode((string) ($chatRoom ?? '')) ?>,
    currentUserId: <?= json_encode((int) ($currentUserId ?? 0)) ?>,
    currentUserName: <?= json_encode((string) ($currentUserName ?? 'User')) ?>,
    uploadUrl: <?= json_encode((string) ($dc['upload_url'] ?? '')) ?>,
    summarizeUrl: <?= json_encode((string) ($dc['summarize_url'] ?? '')) ?>
};
</script>
<script src="<?= APP_URL ?>/View/assets/js/discussion-chat.js"></script>
<script>
(function () {
    var btnShare = document.getElementById('btn-share-location');
    if (!btnShare) return;

    var watchId = null;
    var shareTimeoutId = null;
    var emitRetryTimerId = null;
    var lastLocationMessageSentAt = 0;
    var pendingLocation = null;
    var currentLocationSessionId = '';

    var shareLocationUrl = <?= json_encode(APP_ENTRY . '?controller=discussion&action=shareLocation') ?>;
    var stopLocationUrl = <?= json_encode(APP_ENTRY . '?controller=discussion&action=stopLocation') ?>;
    var getLiveLocationsUrlBase = <?= json_encode(APP_ENTRY . '?controller=discussion&action=getLiveLocations&discussion_id=') ?>;
    var localUserId = Number((window.DiscussionChatConfig || {}).currentUserId || 0);

    function applyLocationAvailability(activeUserIds) {
        var activeMap = {};
        (activeUserIds || []).forEach(function (id) { activeMap[String(id)] = true; });

        var rows = document.querySelectorAll('[data-location-user-id], [id^="chat-location-"]');
        var latestRowByUid = {};
        for (var j = 0; j < rows.length; j++) {
            var preRow = rows[j];
            var preUid = String(preRow.getAttribute('data-location-user-id') || '').trim();
            if (!preUid) {
                var preId = String(preRow.id || '');
                var preM = preId.match(/^chat-location-(\d+)/);
                preUid = preM ? String(preM[1]) : '';
            }
            if (!preUid) continue;
            var preTs = Number(preRow.getAttribute('data-location-created-ts') || 0);
            var known = latestRowByUid[preUid];
            var knownTs = known ? Number(known.getAttribute('data-location-created-ts') || 0) : -1;
            if (!known || preTs >= knownTs) {
                latestRowByUid[preUid] = preRow;
            }
        }

        for (var i = 0; i < rows.length; i++) {
            var row = rows[i];
            var uid = String(row.getAttribute('data-location-user-id') || '').trim();
            if (!uid) {
                var rowId = String(row.id || '');
                var m = rowId.match(/^chat-location-(\d+)/);
                uid = m ? String(m[1]) : '';
            }
            if (!uid) continue;
            var isActive = !!activeMap[uid];
            if (isActive) {
                isActive = latestRowByUid[uid] === row;
            }
            var isLocalRow = row.getAttribute('data-location-self') === '1';
            if (
                !isActive &&
                watchId !== null &&
                latestRowByUid[uid] === row &&
                (isLocalRow || (localUserId > 0 && String(localUserId) === uid))
            ) {
                isActive = true;
            }
            var viewBtn = row.querySelector('a[data-location-view="1"]');
            var sharingLine = row.querySelector('[data-location-sharing="1"]');
            if (!viewBtn) continue;
            var who = 'User';
            if (sharingLine) {
                who = String(sharingLine.getAttribute('data-location-user') || '').trim() || 'User';
            }

            if (isActive) {
                var savedHref = viewBtn.getAttribute('data-location-href') || '';
                if (savedHref) viewBtn.href = savedHref;
                viewBtn.style.pointerEvents = '';
                viewBtn.style.opacity = '';
                viewBtn.style.cursor = '';
                viewBtn.removeAttribute('aria-disabled');
                viewBtn.classList.remove('chat-location-card__cta--disabled');
                var ctaLabelOn = viewBtn.querySelector('.chat-location-card__cta-label');
                if (ctaLabelOn) ctaLabelOn.textContent = 'Open in maps';
                else viewBtn.textContent = 'Open in maps';
                if (sharingLine) {
                    if (sharingLine.textContent && sharingLine.textContent.toLowerCase().indexOf('stopped') !== -1) {
                        sharingLine.textContent = sharingLine.textContent.replace(/stopped sharing/i, 'is sharing');
                    }
                }
            } else {
                viewBtn.removeAttribute('href');
                viewBtn.style.pointerEvents = 'none';
                viewBtn.style.opacity = '0.55';
                viewBtn.style.cursor = 'not-allowed';
                viewBtn.setAttribute('aria-disabled', 'true');
                viewBtn.classList.add('chat-location-card__cta--disabled');
                var ctaLabelOff = viewBtn.querySelector('.chat-location-card__cta-label');
                if (ctaLabelOff) ctaLabelOff.textContent = 'Unavailable';
                else viewBtn.textContent = 'Unavailable';
                if (sharingLine) {
                    sharingLine.textContent = who + ' stopped sharing';
                }
            }
        }
    }

    function refreshLocationAvailability() {
        if (!getLiveLocationsUrlBase) return;
        fetch(getLiveLocationsUrlBase + encodeURIComponent(String(DISCUSSION_ID)))
            .then(function (res) { return res.json(); })
            .then(function (rows) {
                if (!Array.isArray(rows)) return;
                var activeIds = rows.map(function (r) { return Number(r.user_id || 0); }).filter(function (v) { return v > 0; });
                applyLocationAvailability(activeIds);
            })
            .catch(function () {});
    }

    function sendLocation(lat, lng, durationMinutes) {
        return fetch(shareLocationUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                discussion_id: DISCUSSION_ID,
                latitude: lat,
                longitude: lng,
                duration: durationMinutes
            })
        });
    }

    function emitLiveLocationChatMessage(lat, lng) {
        var cfg = window.DiscussionChatConfig || {};
        var socket = window.__DISCUSSION_CHAT_SOCKET;
        if (!socket || typeof socket.emit !== 'function') return false;

        var mapLink = 'https://www.openstreetmap.org/?mlat=' + encodeURIComponent(String(lat)) +
            '&mlon=' + encodeURIComponent(String(lng)) +
            '&sid=' + encodeURIComponent(String(currentLocationSessionId || '')) +
            '#map=16/' + encodeURIComponent(String(lat)) + '/' + encodeURIComponent(String(lng));

        socket.emit('chat-message', {
            room: String(cfg.chatRoom || ''),
            userId: Number(cfg.currentUserId || 0),
            userName: String(cfg.currentUserName || 'User'),
            message: '',
            messageType: 'location',
            fileUrl: mapLink,
            fileName: 'Live location',
            locationSessionId: String(currentLocationSessionId || '')
        });
        return true;
    }

    function tryEmitPendingLocation() {
        if (!pendingLocation) return false;
        var now = Date.now();
        if (now - lastLocationMessageSentAt < 5000) return false;
        var sent = false;
        try {
            sent = emitLiveLocationChatMessage(pendingLocation.lat, pendingLocation.lng);
        } catch (_e) {
            sent = false;
        }
        if (sent) {
            lastLocationMessageSentAt = now;
        }
        return sent;
    }

    function stopLocationShare() {
        if (watchId !== null) {
            try { navigator.geolocation.clearWatch(watchId); } catch (_e) {}
            watchId = null;
        }
        if (shareTimeoutId !== null) {
            clearTimeout(shareTimeoutId);
            shareTimeoutId = null;
        }
        if (emitRetryTimerId !== null) {
            clearInterval(emitRetryTimerId);
            emitRetryTimerId = null;
        }

        fetch(stopLocationUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ discussion_id: DISCUSSION_ID })
        }).catch(function () {});

        lastLocationMessageSentAt = 0;
        pendingLocation = null;
        currentLocationSessionId = '';
        applyLocationAvailability([]);
        refreshLocationAvailability();
        btnShare.innerText = '📍 Partager ma localisation';
    }

    function startLocationShare(durationMinutes) {
        if (!navigator.geolocation || !navigator.geolocation.watchPosition) {
            alert('Geolocation is not supported by your browser.');
            return;
        }

        if (watchId !== null) stopLocationShare();

        var safeDurationMinutes = parseInt(String(durationMinutes || '15'), 10);
        if (!safeDurationMinutes || safeDurationMinutes <= 0) safeDurationMinutes = 15;
        currentLocationSessionId = 'loc-' + String(Date.now()) + '-' + String(Math.random()).slice(2, 8);

        btnShare.innerText = '⏹ Stop sharing location';
        lastLocationMessageSentAt = 0;

        watchId = navigator.geolocation.watchPosition(
            function (position) {
                var lat = position.coords.latitude;
                var lng = position.coords.longitude;
                pendingLocation = { lat: lat, lng: lng };
                sendLocation(lat, lng, safeDurationMinutes).catch(function () {});
                tryEmitPendingLocation();
            },
            function (error) {
                alert('GPS error: ' + (error && error.message ? error.message : String(error)));
                stopLocationShare();
            },
            { enableHighAccuracy: true, maximumAge: 5000, timeout: 10000 }
        );

        shareTimeoutId = setTimeout(function () {
            stopLocationShare();
        }, safeDurationMinutes * 60 * 1000);

        emitRetryTimerId = setInterval(function () {
            tryEmitPendingLocation();
        }, 2000);

        refreshLocationAvailability();
    }

    btnShare.addEventListener('click', function () {
        if (watchId !== null) {
            stopLocationShare();
            return;
        }

        if (window.Swal && typeof window.Swal.fire === 'function') {
            window.Swal.fire({
                title: 'How long do you want to share?',
                input: 'select',
                inputOptions: {
                    15: '15 min',
                    60: '1 hour',
                    480: '8 hours'
                },
                inputValue: 60,
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No',
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
            }).then(function (result) {
                if (!result || !result.isConfirmed) return;
                var duration = parseInt(String(result.value || '60'), 10);
                startLocationShare(duration);
            });
        } else {
            startLocationShare(60);
        }
    });

    setInterval(refreshLocationAvailability, 5000);
    refreshLocationAvailability();
})();
</script>

