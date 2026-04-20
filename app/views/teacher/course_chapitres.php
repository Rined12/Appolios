<?php
$teacherSidebarActive = 'chapitres';
?>
<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main">
                <div class="dashboard-header" style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:12px;">
                    <div>
                        <a href="<?= APP_URL ?>/index.php?url=teacher/chapitres" style="color:var(--secondary-color);">← Tous les chapitres</a>
                        <h1 style="margin-top:8px;"><?= htmlspecialchars($course['title']) ?></h1>
                    </div>
                    <a href="<?= APP_URL ?>/index.php?url=teacher/course/<?= (int) $course['id'] ?>/chapitres/add" class="btn btn-yellow">Ajouter un chapitre</a>
                </div>
                <?php if (!empty($flash)): ?>
                    <p class="flash flash-<?= htmlspecialchars($flash['type']) ?>" style="margin:12px 0;"><?= htmlspecialchars($flash['message']) ?></p>
                <?php endif; ?>
                <div class="table-container" style="margin-top:20px;">
                    <div class="table-header"><h3 style="margin:0;">Chapitres</h3></div>
                    <div style="padding:16px;">
                        <?php if (!empty($chapters)): ?>
                            <ul style="list-style:none;padding:0;">
                                <?php foreach ($chapters as $ch): ?>
                                    <li style="padding:12px 0;border-bottom:1px solid var(--gray);display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;">
                                        <div>
                                            <strong><?= htmlspecialchars($ch['title']) ?></strong>
                                            <span style="color:var(--gray-dark);font-size:0.85rem;"> (ordre <?= (int) $ch['sort_order'] ?>)</span>
                                        </div>
                                        <div style="display:flex;gap:8px;">
                                            <a href="<?= APP_URL ?>/index.php?url=teacher/chapitre/<?= (int) $ch['id'] ?>/edit" class="btn btn-secondary" style="padding:6px 12px;">Modifier</a>
                                            <a href="<?= APP_URL ?>/index.php?url=teacher/chapitre/<?= (int) $ch['id'] ?>/delete" class="btn action-btn danger" style="padding:6px 12px;" data-confirm="Supprimer ce chapitre ?">Supprimer</a>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p>Aucun chapitre. Ajoutez-en un pour structurer le cours.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
