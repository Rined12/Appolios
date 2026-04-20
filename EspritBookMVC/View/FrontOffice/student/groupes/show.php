<?php
$foPrefix = $foPrefix ?? 'student';
$studentSidebarActive = $studentSidebarActive ?? 'groupes';
$groupCover = trim((string) ($groupe['image_url'] ?? $groupe['photo'] ?? $groupe['image'] ?? ''));
$groupeApproval = (string) ($groupe['approval_statut'] ?? '');
$groupeApprouve = $groupeApproval === 'approuve';
$viewerId = (int) ($_SESSION['user_id'] ?? 0);
$isGroupCreatorViewer = (int) ($groupe['id_createur'] ?? $groupe['created_by'] ?? 0) === $viewerId;
?>

<div class="dashboard student-events-page">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/../partials/group_discussion_sidebar.php'; ?>
            <div class="admin-main">
                <?php if (!$groupeApprouve): ?>
                <div style="margin-bottom:16px;padding:14px 16px;background:#fff7ed;border:1px solid #fed7aa;border-radius:12px;color:#7c2d12;">
                    <strong>En cours d&apos;approbation</strong>
                    <p style="margin:6px 0 0;font-size:14px;">
                        <?php if ($groupeApproval === 'rejete'): ?>
                            Ce groupe a ete rejete par l administrateur. Vous pouvez le modifier pour le soumettre a nouveau.
                        <?php else: ?>
                            Ce groupe est en attente de validation par un administrateur (approuve ou rejete). Il n apparait pas dans la liste publique tant qu il n est pas approuve.
                        <?php endif; ?>
                    </p>
                </div>
                <?php endif; ?>
                <?php if ($groupCover !== ''): ?>
                <div style="margin-bottom:18px;">
                    <img src="<?= htmlspecialchars($groupCover) ?>" alt="" style="width:100%;max-height:240px;object-fit:cover;border-radius:14px;border:1px solid #e2e8f0;">
                </div>
                <?php endif; ?>
                <div class="dashboard-header" style="display:flex;justify-content:space-between;align-items:center;">
                    <div>
                        <h1><?= htmlspecialchars($groupe['nom_groupe'] ?? 'Group') ?></h1>
                        <p><?= htmlspecialchars($groupe['description'] ?? '') ?></p>
                    </div>
                    <div style="display:flex;gap:10px;">
                        <?php if ($isGroupCreatorViewer): ?>
                            <a class="btn btn-primary" href="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/groupes/<?= (int) $groupe['id_groupe'] ?>/edit">Edit</a>
                            <a class="btn action-btn danger" href="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/groupes/<?= (int) $groupe['id_groupe'] ?>/delete" onclick="return confirm('Supprimer ce groupe et toutes ses discussions ?');">Delete group</a>
                        <?php endif; ?>
                        <?php if ($groupeApprouve && !$isMembre): ?>
                            <a class="btn btn-yellow" href="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/groupes/<?= (int) $groupe['id_groupe'] ?>/join">Join</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="table-container">
                    <div class="table-header"><h3 style="margin:0;">Members</h3></div>
                    <div class="table-responsive">
                        <table>
                            <thead><tr><th>Name</th><th>Email</th><th>Role</th></tr></thead>
                            <tbody>
                                <?php if (!empty($membres)): foreach ($membres as $m): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($m['name']) ?></td>
                                        <td><?= htmlspecialchars($m['email']) ?></td>
                                        <td><?= htmlspecialchars($m['role']) ?></td>
                                    </tr>
                                <?php endforeach; else: ?>
                                    <tr><td colspan="3" style="text-align:center;padding:30px;">No members yet.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="table-container" style="margin-top: 18px; padding: 16px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; gap:10px; margin-bottom:12px;">
                        <h3 style="margin:0;">Discussions</h3>
                        <?php if ($isGroupCreatorViewer): ?>
                            <span style="font-size:12px; color:#64748b;">You are the group creator</span>
                        <?php endif; ?>
                    </div>

                    <?php if ($isGroupCreatorViewer): ?>
                        <form method="POST" action="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/groupes/<?= (int) $groupe['id_groupe'] ?>/discussions/store" novalidate id="group-discussion-form" style="margin-bottom:16px;">
                            <div class="form-group">
                                <label for="discussion_title">Title</label>
                                <input type="text" id="discussion_title" name="titre" value="<?= htmlspecialchars($discussionOld['titre'] ?? '') ?>">
                                <div class="field-error" data-error-for="titre" style="color:#ef4444;"><?= htmlspecialchars($discussionErrors['titre'] ?? '') ?></div>
                            </div>
                            <div class="form-group">
                                <label for="discussion_content">Content</label>
                                <textarea id="discussion_content" name="contenu" rows="4"><?= htmlspecialchars($discussionOld['contenu'] ?? '') ?></textarea>
                                <div class="field-error" data-error-for="contenu" style="color:#ef4444;"><?= htmlspecialchars($discussionErrors['contenu'] ?? '') ?></div>
                            </div>
                            <button class="btn btn-yellow" type="submit">Create Discussion</button>
                        </form>
                    <?php endif; ?>

                    <?php if (!empty($discussions)): ?>
                        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(280px,1fr)); gap:12px;">
                            <?php foreach ($discussions as $d): ?>
                                <?php
                                    $dap = (string) ($d['approval_statut'] ?? $d['approval_status'] ?? 'approuve');
                                    $dapLabel = $dap === 'approuve' ? 'Approved' : ($dap === 'rejete' ? 'Rejected' : 'In progress (admin)');
                                    $dapBg = $dap === 'approuve' ? '#dcfce7' : ($dap === 'rejete' ? '#fee2e2' : '#ffedd5');
                                    $dapColor = $dap === 'approuve' ? '#166534' : ($dap === 'rejete' ? '#991b1b' : '#9a3412');
                                ?>
                                <article style="background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:12px;">
                                    <div style="display:flex;justify-content:space-between;align-items:center;gap:8px;margin-bottom:6px;flex-wrap:wrap;">
                                        <h4 style="margin:0; color:#1e293b;"><?= htmlspecialchars($d['titre'] ?? 'Discussion') ?></h4>
                                        <span style="font-size:11px;padding:3px 8px;border-radius:999px;background:<?= $dapBg ?>;color:<?= $dapColor ?>;"><?= htmlspecialchars($dapLabel) ?></span>
                                    </div>
                                    <p style="margin:0 0 10px 0; color:#64748b;"><?= htmlspecialchars(substr((string) ($d['contenu'] ?? ''), 0, 180)) ?></p>
                                    <div style="display:flex;justify-content:space-between;align-items:center;gap:8px;flex-wrap:wrap;margin-top:8px;">
                                        <span style="font-size:12px; color:#94a3b8;">By <?= htmlspecialchars($d['auteur_name'] ?? 'Unknown') ?></span>
                                        <?php
                                            $discId = (int) ($d['id_discussion'] ?? $d['id'] ?? 0);
                                            $discAuthorId = (int) ($d['id_auteur'] ?? $d['created_by'] ?? 0);
                                            $canDelDisc = $discId > 0 && ($discAuthorId === $viewerId || $isGroupCreatorViewer);
                                        ?>
                                        <?php if ($canDelDisc): ?>
                                            <a class="btn action-btn danger" style="padding:4px 10px;font-size:12px;" href="<?= APP_ENTRY ?>?url=<?= htmlspecialchars($foPrefix, ENT_QUOTES, 'UTF-8') ?>/groupes/<?= (int) $groupe['id_groupe'] ?>/discussions/<?= $discId ?>/delete" onclick="return confirm('Supprimer cette discussion ?');">Delete</a>
                                        <?php endif; ?>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p style="margin:0; color:#64748b;">No discussions yet in this group.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('group-discussion-form');
    if (!form) return;

    const setError = (name, message) => {
        const node = form.querySelector('[data-error-for="' + name + '"]');
        if (node) node.textContent = message;
    };

    form.addEventListener('submit', function (event) {
        const title = (form.querySelector('#discussion_title').value || '').trim();
        const content = (form.querySelector('#discussion_content').value || '').trim();
        let hasError = false;

        setError('titre', '');
        setError('contenu', '');

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
