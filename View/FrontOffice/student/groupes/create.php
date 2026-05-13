<?php
$old = $old ?? [];
$errors = $errors ?? [];
$flash = $flash ?? null;
$foPrefix = (string) ($foPrefix ?? 'student');
$storeUrl = APP_ENTRY . '?url=' . rawurlencode($foPrefix . '/groupes/store');
$listUrl = APP_ENTRY . '?url=' . rawurlencode($foPrefix . '/groupes');
?>
<?php require __DIR__ . '/../partials/collab_layout_start.php'; ?>
                <?php require __DIR__ . '/../partials/collab_hub_styles.php'; ?>

                <div class="header collab-hero group-create-hero">
                    <div class="collab-hero__inner">
                        <div>
                            <div class="collab-eyebrow"><i class="bi bi-people-fill" aria-hidden="true"></i> New community</div>
                            <h1>Create a group</h1>
                            <p>Give your space a clear name and a short pitch. Add a cover photo so members recognize it instantly—approval may take a moment.</p>
                        </div>
                        <div class="collab-hero-actions">
                            <a href="<?= htmlspecialchars($listUrl, ENT_QUOTES, 'UTF-8') ?>" class="collab-btn-ghost">
                                <i class="bi bi-arrow-left" aria-hidden="true"></i> Back to groups
                            </a>
                        </div>
                    </div>
                </div>

                <?php if (is_array($flash) && trim((string) ($flash['message'] ?? '')) !== ''): ?>
                    <?php
                    $flashType = strtolower((string) ($flash['type'] ?? 'success'));
                    $flashClass = $flashType === 'error' ? 'group-create-flash--error' : 'group-create-flash--success';
                    ?>
                    <div class="collab-alert-soft <?= htmlspecialchars($flashClass, ENT_QUOTES, 'UTF-8') ?>" role="status"><?= htmlspecialchars((string) $flash['message'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>

                <div class="group-create-layout">
                    <div class="section collab-form-shell group-create-form">
                        <form method="POST" action="<?= htmlspecialchars($storeUrl, ENT_QUOTES, 'UTF-8') ?>" enctype="multipart/form-data" class="group-create-form__inner">
                            <div class="form-group">
                                <label for="group_nom">Group name</label>
                                <input id="group_nom" type="text" name="nom_groupe" value="<?= htmlspecialchars((string) ($old['nom_groupe'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="e.g. Web dev study circle" autocomplete="off">
                                <?php if (!empty($errors['nom_groupe'])): ?>
                                    <div class="group-create-field-error"><?= htmlspecialchars((string) $errors['nom_groupe'], ENT_QUOTES, 'UTF-8') ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="form-group">
                                <label for="group_desc">Description</label>
                                <textarea id="group_desc" name="description" rows="5" placeholder="Who is it for? What will you share or discuss?">
<?= htmlspecialchars((string) ($old['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                                <?php if (!empty($errors['description'])): ?>
                                    <div class="group-create-field-error"><?= htmlspecialchars((string) $errors['description'], ENT_QUOTES, 'UTF-8') ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="form-group">
                                <label for="group_photo_input">Cover photo <span class="group-create-optional">optional</span></label>
                                <p class="group-create-hint">JPEG, PNG, GIF or WebP · max 2&nbsp;MB · shown on cards after approval</p>
                                <div class="upload-field group-create-upload">
                                    <input id="group_photo_input" class="upload-field__input" type="file" name="group_photo" accept="image/jpeg,image/png,image/gif,image/webp">
                                    <label for="group_photo_input" class="upload-field__button">
                                        <i class="bi bi-image" aria-hidden="true"></i>
                                        <span>Choose image</span>
                                    </label>
                                    <span id="group_photo_name" class="upload-field__name">No file chosen</span>
                                </div>
                                <div id="group_photo_preview" class="group-photo-preview" hidden>
                                    <img src="" alt="Preview of selected group cover">
                                </div>
                                <?php if (!empty($errors['group_photo'])): ?>
                                    <div class="group-create-field-error"><?= htmlspecialchars((string) $errors['group_photo'], ENT_QUOTES, 'UTF-8') ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="group-create-actions">
                                <button class="collab-btn-primary" type="submit">
                                    <i class="bi bi-check2-circle" aria-hidden="true"></i> Save group
                                </button>
                            </div>
                        </form>
                    </div>

                    <aside class="group-create-aside">
                        <div class="section collab-detail-sidecard group-create-tip">
                            <h3><i class="bi bi-lightbulb" aria-hidden="true"></i> Tips</h3>
                            <ul class="group-create-tip-list">
                                <li><strong>Clear name</strong> — helps others find you in search and lists.</li>
                                <li><strong>Honest description</strong> — sets expectations before anyone joins.</li>
                                <li><strong>Strong cover</strong> — readable at small sizes; avoid busy screenshots.</li>
                            </ul>
                        </div>
                        <div class="section collab-detail-sidecard group-create-tip group-create-tip--muted">
                            <h3><i class="bi bi-hourglass-split" aria-hidden="true"></i> Approval</h3>
                            <p class="group-create-tip-p">New groups start as <em>awaiting approval</em>. You’ll see yours in the top section until an administrator approves it.</p>
                        </div>
                    </aside>
                </div>
<?php require __DIR__ . '/../partials/collab_layout_end.php'; ?>
<style>
.group-create-hero {
    margin-bottom: 1.35rem;
}
.collab-hub .collab-alert-soft.group-create-flash--error {
    background: linear-gradient(135deg, #fef2f2 0%, #fff1f2 100%);
    border-color: rgba(248, 113, 113, 0.55);
    color: #991b1b;
}
.collab-hub .collab-alert-soft.group-create-flash--success {
    background: linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 100%);
    border-color: rgba(52, 211, 153, 0.5);
    color: #065f46;
}
.group-create-layout {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(260px, 320px);
    gap: 1.25rem;
    align-items: start;
}
.group-create-aside {
    display: flex;
    flex-direction: column;
    gap: 1.65rem;
}
@media (max-width: 1024px) {
    .group-create-layout {
        grid-template-columns: 1fr;
    }
    .group-create-aside {
        margin-top: 0.35rem;
        padding-top: 0.75rem;
        border-top: 1px solid rgba(148, 163, 184, 0.28);
    }
}
.group-create-form__inner .form-group {
    margin-bottom: 1.25rem;
}
.group-create-form__inner .form-group:last-of-type {
    margin-bottom: 1.5rem;
}
.group-create-optional {
    font-weight: 600;
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: var(--ch-muted);
    margin-left: 0.25rem;
}
.group-create-hint {
    margin: 0 0 0.55rem;
    font-size: 0.82rem;
    color: var(--ch-muted);
    line-height: 1.45;
}
.group-create-field-error {
    color: #dc2626;
    font-size: 0.85rem;
    font-weight: 600;
    margin-top: 0.4rem;
}
.group-create-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    align-items: center;
    padding-top: 0.25rem;
    border-top: 1px solid rgba(148, 163, 184, 0.25);
    margin: 0 -0.15rem;
    padding-left: 0.15rem;
    padding-right: 0.15rem;
}
.collab-hub .group-create-form.collab-form-shell {
    max-width: none;
}
.collab-hub .collab-detail-sidecard.group-create-tip {
    padding: 1.15rem 1.2rem 1.25rem;
    border-radius: 18px;
}
.collab-hub .collab-detail-sidecard.group-create-tip h3 {
    margin: 0 0 0.75rem;
    font-size: 1rem;
    font-weight: 800;
    color: var(--ch-teal);
    display: flex;
    align-items: center;
    gap: 0.45rem;
}
.collab-hub .collab-detail-sidecard.group-create-tip--muted h3 {
    color: var(--ch-slate);
}
.group-create-tip-list {
    margin: 0;
    padding-left: 1.1rem;
    font-size: 0.88rem;
    color: var(--ch-muted);
    line-height: 1.55;
}
.group-create-tip-list li {
    margin-bottom: 0.5rem;
}
.group-create-tip-list li:last-child {
    margin-bottom: 0;
}
.group-create-tip-p {
    margin: 0;
    font-size: 0.88rem;
    color: var(--ch-muted);
    line-height: 1.55;
}
.group-create-tip-p em {
    font-style: normal;
    font-weight: 700;
    color: var(--ch-teal-mid);
}
.collab-hub .upload-field.group-create-upload {
    position: relative;
    display: flex;
    align-items: center;
    gap: 0.65rem;
    flex-wrap: wrap;
    padding: 0.65rem 0.75rem;
    border: 1px solid rgba(84, 140, 168, 0.35);
    border-radius: 14px;
    background: linear-gradient(145deg, rgba(248, 251, 255, 0.95) 0%, #fff 55%);
    box-shadow: 0 6px 20px rgba(43, 72, 101, 0.06);
}
.collab-hub .upload-field__input {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    border: 0;
}
.collab-hub .upload-field__button {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    padding: 0.58rem 1rem;
    border-radius: 11px;
    border: 1px solid rgba(43, 72, 101, 0.2);
    background: #fff;
    color: var(--ch-teal);
    font-weight: 700;
    font-size: 0.86rem;
    cursor: pointer;
    transition: transform 0.18s ease, box-shadow 0.2s ease, border-color 0.2s ease;
}
.collab-hub .upload-field__button:hover {
    transform: translateY(-1px);
    border-color: var(--ch-teal-soft);
    box-shadow: 0 8px 20px rgba(43, 72, 101, 0.12);
}
.collab-hub .upload-field__name {
    font-size: 0.84rem;
    color: var(--ch-muted);
    max-width: min(320px, 100%);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.group-photo-preview {
    margin-top: 0.85rem;
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid var(--ch-line);
    background: #f1f5f9;
    aspect-ratio: 16 / 9;
    max-height: 220px;
    box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.6);
}
.group-photo-preview[hidden] {
    display: none !important;
}
.group-photo-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
</style>
<script>
(function () {
    var input = document.getElementById('group_photo_input');
    var nameEl = document.getElementById('group_photo_name');
    var preview = document.getElementById('group_photo_preview');
    var previewImg = preview ? preview.querySelector('img') : null;
    var lastObjectUrl = null;

    function revokeLast() {
        if (lastObjectUrl) {
            URL.revokeObjectURL(lastObjectUrl);
            lastObjectUrl = null;
        }
    }

    if (input && nameEl) {
        input.addEventListener('change', function () {
            revokeLast();
            if (input.files && input.files.length > 0) {
                var f = input.files[0];
                nameEl.textContent = f.name;
                if (preview && previewImg && f.type.indexOf('image/') === 0) {
                    lastObjectUrl = URL.createObjectURL(f);
                    previewImg.src = lastObjectUrl;
                    preview.hidden = false;
                } else if (preview) {
                    preview.hidden = true;
                }
                return;
            }
            nameEl.textContent = 'No file chosen';
            if (preview) {
                preview.hidden = true;
            }
            if (previewImg) {
                previewImg.removeAttribute('src');
            }
        });
    }
})();
</script>

