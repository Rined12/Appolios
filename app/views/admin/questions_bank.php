<?php
$adminSidebarActive = 'questions';
?>
<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main">
                <div class="dashboard-header" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
                    <h1>Banque de questions (admin)</h1>
                    <a href="<?= APP_URL ?>/index.php?url=admin/questions/add" class="btn btn-yellow">Nouvelle question</a>
                </div>
                <?php if (!empty($flash)): ?>
                    <p class="flash flash-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></p>
                <?php endif; ?>
                <div class="table-container" style="margin-top:20px;">
                    <div style="padding:16px;">
                        <?php if (!empty($questions)): foreach ($questions as $q): ?>
                            <div style="padding:16px;border-bottom:1px solid var(--gray);">
                                <strong><?= htmlspecialchars($q['title'] ?: 'Sans titre') ?></strong>
                                <span style="font-size:0.85rem;color:var(--gray-dark);"> — <?= htmlspecialchars($q['author_name'] ?? '') ?></span>
                                <p><?= htmlspecialchars($q['question_text']) ?></p>
                                <a href="<?= APP_URL ?>/index.php?url=admin/questions/edit/<?= (int) $q['id'] ?>" class="btn btn-secondary" style="padding:6px 12px;">Modifier</a>
                                <a href="<?= APP_URL ?>/index.php?url=admin/questions/delete/<?= (int) $q['id'] ?>" class="btn action-btn danger" style="padding:6px 12px;" data-confirm="Supprimer ?">Supprimer</a>
                            </div>
                        <?php endforeach; else: ?>
                            <p>Aucune question.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
