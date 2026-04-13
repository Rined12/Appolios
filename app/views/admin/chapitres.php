<?php
$adminSidebarActive = 'chapitres';
?>
<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main">
                <div class="dashboard-header" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
                    <h1>Chapitres (admin)</h1>
                    <div style="display:flex;gap:10px;flex-wrap:wrap;">
                        <a href="<?= APP_URL ?>/index.php?url=admin/chapitres/add" class="btn btn-yellow">Nouveau chapitre</a>
                        <a href="<?= APP_URL ?>/index.php?url=admin/courses" class="btn btn-outline">Retour aux cours</a>
                    </div>
                </div>
                <?php if (!empty($flash)): ?>
                    <p class="flash flash-<?= htmlspecialchars($flash['type']) ?>" style="margin:12px 0;"><?= htmlspecialchars($flash['message']) ?></p>
                <?php endif; ?>
                <div class="table-container" style="margin-top:20px;">
                    <div class="table-header"><h3 style="margin:0;">Tous les chapitres (CRUD)</h3></div>
                    <div style="padding:16px;overflow-x:auto;">
                        <table class="data-table" style="width:100%;">
                            <thead><tr><th>Cours</th><th>Auteur cours</th><th>Titre</th><th>Ordre</th><th style="min-width:200px;">Actions</th></tr></thead>
                            <tbody>
                            <?php if (!empty($chapters)): foreach ($chapters as $ch): ?>
                                <tr>
                                    <td><?= htmlspecialchars($ch['course_title'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($ch['author_name'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($ch['title']) ?></td>
                                    <td><?= (int) ($ch['sort_order'] ?? 0) ?></td>
                                    <td>
                                        <a href="<?= APP_URL ?>/index.php?url=admin/course/<?= (int) ($ch['course_id'] ?? 0) ?>/chapitres" class="btn btn-outline" style="padding:6px 10px;font-size:0.8rem;">Cours</a>
                                        <a href="<?= APP_URL ?>/index.php?url=admin/chapitre/<?= (int) $ch['id'] ?>/edit" class="btn btn-secondary" style="padding:6px 10px;font-size:0.8rem;">Modifier</a>
                                        <a href="<?= APP_URL ?>/index.php?url=admin/chapitre/<?= (int) $ch['id'] ?>/delete" class="btn action-btn danger" style="padding:6px 10px;font-size:0.8rem;" data-confirm="Supprimer ce chapitre ?">Supprimer</a>
                                    </td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="5">Aucun chapitre. Utilisez « Nouveau chapitre » ou ajoutez-en depuis un cours.</td></tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
