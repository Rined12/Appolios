<?php
/**
 * APPOLIOS - Admin Discussion Live Chat
 */

$dc = $discussion_chat ?? ['back_url' => '#', 'upload_url' => '', 'summarize_url' => ''];
?>

<link rel="stylesheet" href="<?= APP_URL ?>/View/assets/css/discussion-chat.css">

<?php require __DIR__ . '/../../FrontOffice/student/partials/collab_hub_styles.php'; ?>

<div class="dashboard student-learning-page collab-hub collab-chat-root" data-chat-appearance="default">
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

            <form id="chatForm" class="collab-chat-composer chat-input-wrap" data-skip-validation="1" novalidate>
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
</div>

<script>
const DISCUSSION_ID = <?= (int) (($discussion['id_discussion'] ?? $discussion['id'] ?? 0)) ?>;
window.DiscussionChatConfig = {
    socketUrl: <?= json_encode((string) ($socketUrl ?? '')) ?>,
    chatRoom: <?= json_encode((string) ($chatRoom ?? '')) ?>,
    currentUserId: <?= json_encode((int) ($currentUserId ?? 0)) ?>,
    currentUserName: <?= json_encode((string) ($currentUserName ?? 'Admin')) ?>,
    uploadUrl: <?= json_encode((string) ($dc['upload_url'] ?? '')) ?>,
    summarizeUrl: <?= json_encode((string) ($dc['summarize_url'] ?? '')) ?>
};
</script>
<script src="<?= APP_URL ?>/View/assets/js/discussion-chat.js"></script>

<script>
(function () {
    var btnShare = document.getElementById('btn-share-location');
    if (!btnShare || !navigator.geolocation) { return; }

    btnShare.addEventListener('click', function () {
        btnShare.disabled = true;
        btnShare.textContent = 'Localisation...';
        navigator.geolocation.getCurrentPosition(function (pos) {
            btnShare.disabled = false;
            btnShare.textContent = '📍 Partager ma localisation';
            if (!pos || !pos.coords) { return; }
            var msg = '📍 Ma localisation: https://maps.google.com/?q=' + pos.coords.latitude + ',' + pos.coords.longitude;
            var input = document.getElementById('chatInput');
            if (input) {
                input.value = msg;
                input.focus();
            }
        }, function () {
            btnShare.disabled = false;
            btnShare.textContent = '📍 Partager ma localisation';
        }, { enableHighAccuracy: true, timeout: 8000, maximumAge: 0 });
    });
})();
</script>
