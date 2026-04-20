<?php
$foPrefix = $foPrefix ?? 'student';
$studentSidebarActive = $studentSidebarActive ?? 'discussions';
$old = $old ?? [];
?>

<div class="dashboard student-events-page">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/../partials/group_discussion_sidebar.php'; ?>
            <div class="admin-main">
                <div class="dashboard-header">
                    <h1>Create Discussion</h1>
                    <p>Only groups you created are available here.</p>
                </div>

                <div class="form-container" style="max-width: 760px;">
                    <form method="POST" action="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/discussions/store" novalidate id="discussion-create-form">
                        <div class="form-group">
                            <label for="id_groupe">Group</label>
                            <select id="id_groupe" name="id_groupe">
                                <option value="">Select a group</option>
                                <?php foreach (($groups ?? []) as $group): ?>
                                    <option value="<?= (int) $group['id_groupe'] ?>" <?= ((int) ($old['id_groupe'] ?? 0) === (int) $group['id_groupe']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($group['nom_groupe']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="field-error" data-error-for="id_groupe" style="color:#ef4444;"><?= htmlspecialchars($errors['id_groupe'] ?? '') ?></div>
                        </div>
                        <div class="form-group">
                            <label for="titre">Title</label>
                            <input type="text" id="titre" name="titre" value="<?= htmlspecialchars($old['titre'] ?? '') ?>">
                            <div class="field-error" data-error-for="titre" style="color:#ef4444;"><?= htmlspecialchars($errors['titre'] ?? '') ?></div>
                        </div>
                        <div class="form-group">
                            <label for="contenu">Content</label>
                            <textarea id="contenu" name="contenu" rows="6"><?= htmlspecialchars($old['contenu'] ?? '') ?></textarea>
                            <div class="field-error" data-error-for="contenu" style="color:#ef4444;"><?= htmlspecialchars($errors['contenu'] ?? '') ?></div>
                        </div>
                        <button type="submit" class="btn btn-yellow">Save Discussion</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('discussion-create-form');
    if (!form) return;
    const setError = (name, message) => {
        const node = form.querySelector('[data-error-for="' + name + '"]');
        if (node) node.textContent = message;
    };
    form.addEventListener('submit', function (event) {
        const groupId = (form.querySelector('#id_groupe').value || '').trim();
        const title = (form.querySelector('#titre').value || '').trim();
        const content = (form.querySelector('#contenu').value || '').trim();
        let hasError = false;
        setError('id_groupe', '');
        setError('titre', '');
        setError('contenu', '');
        if (groupId === '') {
            setError('id_groupe', 'This field cannot be empty.');
            hasError = true;
        }
        if (title.length === 0) {
            setError('titre', 'This field cannot be empty.');
            hasError = true;
        } else if (title.length < 3) {
            setError('titre', 'Title must be between 3 and 200 characters.');
            hasError = true;
        } else if (title.length > 200) {
            setError('titre', 'Title must not exceed 200 characters.');
            hasError = true;
        }
        if (content.length === 0) {
            setError('contenu', 'This field cannot be empty.');
            hasError = true;
        } else if (content.length < 5) {
            setError('contenu', 'Content must be between 5 and 5000 characters.');
            hasError = true;
        } else if (content.length > 5000) {
            setError('contenu', 'Content must not exceed 5000 characters.');
            hasError = true;
        }
        if (hasError) event.preventDefault();
    });
});
</script>
