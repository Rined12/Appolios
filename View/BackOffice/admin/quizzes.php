<?php
$adminSidebarActive = 'quiz';
?>
<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main">
                <div class="dashboard-header" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
                    <h1>Quiz (admin)</h1>
                    <a href="<?= APP_ENTRY ?>?url=admin/add-quiz" class="btn btn-yellow">Nouveau quiz</a>
                </div>
                <?php if (!empty($flash)): ?>
                    <p class="flash flash-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></p>
                <?php endif; ?>
                <div class="table-container" style="margin-top:20px;">
                    <div style="padding:16px;overflow-x:auto;">
                        <table class="data-table" style="width:100%;">
                            <thead><tr><th>Titre</th><th>Cours</th><th>Chapitre</th><th>Difficulté</th><th></th></tr></thead>
                            <tbody>
                            <?php if (!empty($quizzes)): foreach ($quizzes as $q): ?>
                                <tr>
                                    <td><?= htmlspecialchars($q['title']) ?></td>
                                    <td><?= htmlspecialchars($q['course_title'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($q['chapter_title'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($q['difficulty'] ?? '') ?></td>
                                    <td>
                                        <a href="<?= APP_ENTRY ?>?url=admin/edit-quiz/<?= (int) $q['id'] ?>" class="btn btn-secondary" style="padding:6px 12px;">Modifier</a>
                                        <a href="<?= APP_ENTRY ?>?url=admin/delete-quiz/<?= (int) $q['id'] ?>" class="btn action-btn danger" style="padding:6px 12px;" onclick="return confirm('Supprimer ?');">Supprimer</a>
                                    </td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="5">Aucun quiz.</td></tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

